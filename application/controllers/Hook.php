<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hook extends CI_Controller {

	private $updates = [];
	private $chat_id;

	public function __construct() {
		parent::__construct();
		$this->load->model('Bot_model', 'bot');
		$this->load->model('Menu_model', 'menu');
		$this->load->model('Budget_model', 'budget');
		$this->load->model('User_model', 'user');
		$this->load->model('Voice_model', 'voice');
		$this->load->model('Referal_model', 'ref');

		$this->bot_id = $this->input->get('id');
		
		$bot = $this->bot->getBot( $this->bot_id );
		if ( $bot == FALSE ) {
			die("Hacking attempt!");
		}

		$this->bot->id = $bot['id'];
		$this->bot->chatid = $bot['bot_id'];
		$this->bot->token = $bot['token'];
		$this->bot->name = $bot['name'];
		$this->bot->username = $bot['username'];
		$this->bot->board = $bot['board'];
		$this->bot->voice_limit = $bot['voice_limit'];
		$this->bot->voice_price = $bot['voice_price'];
		$this->bot->ref_price = $bot['ref_price'];
		$this->bot->min_payment = $bot['min_payment'];
		$this->bot->ref_mode = $bot['ref_mode'];

		$this->bot->mandatory_subscription = $bot['mandatory_subscription'];
		$this->bot->mandatory_chatid = $bot['mandatory_chatid'];
		$this->bot->mandatory_link = $bot['mandatory_link'];

		$this->user->bot_id = $this->bot->chatid;

		$this->load->library('Telegram', [
        	'token' => $this->bot->token
        ]);
	}

	public function index() {
		$this->updates = $this->telegram->get_webhookUpdates();

		if ( !empty( $this->updates ) ) {
			if( ! empty( $this->updates['message']['text'] ) ) {
				$this->messageText();
				$this->stats();
			}

			if( ! empty( $this->updates['callback_query']['data'] ) ){
				$this->callbackQuery();
				$this->stats();
			}
		}
	}

	private function messageText(){
		$this->telegram->set_chatId( $this->updates['message']['chat']['id'] );
		$this->chat_id = $this->telegram->get_chatId();
		$this->user->set_chatid( $this->chat_id );
		$text = $this->updates['message']['text'];

		if (preg_match('/\/start (\d+)/', $text, $refmatches)) {
			if ( $refmatches[1] != $this->chat_id && !$this->ref->check_ref() ) {
				if ( $this->bot->ref_mode == 1 ) {
					$this->ref->add_active( $refmatches[1] );
				}else{
					$this->ref->add_passive( $refmatches[1] );
				}
			}
			$this->menu->start();
		}else if ( $text == '/start' || $text == bot_message('cancel_button', $this->bot->chatid) || $text == "/bekor" ) {
			$this->menu->start();
		}else if ( $text == bot_message('voting_button', $this->bot->chatid) || $text == '/ovoz' ) {
			$this->menu->voice();
		}else if ( $text == bot_message('balance_button', $this->bot->chatid) || $text == '/hisobim' ) {
			$this->menu->balance();
		}else if ( $text == bot_message('referrer_button', $this->bot->chatid) || $text == '/referal' ) {
			$this->menu->referrer();
		}else if ( $text == bot_message('help_button', $this->bot->chatid) || $text == '/yordam' ) {
			$this->menu->help();
		}else if ( $text == bot_message('about_button', $this->bot->chatid) || $text == '/malumot'  ) {
			$this->menu->about();
		}else if ( $this->voice->get_step() == '1'  ) {
			$this->menu->write_captcha_answer( $text );
		}else if ( $this->voice->get_step() == '2'  ) {
			$this->menu->verify_otp( $text );
		}else if( $this->user->get_data('lastcommand') == 'exchange' ){
			if ( in_array(strlen( preg_replace("/\D/", "", $text) ), [16, 12, 9]) ) {
				$this->db->insert('payment_requests', [
					'data' => preg_replace("/\D/", "", $text),
					'bot_id' => $this->bot->chatid,
					'chat_id' => $this->user->chat_id,
					'time' => time(),
					'status' => '0',
				]);
				$this->menu->start( bot_message('payment_request_success_message', $this->bot->chatid) );	
			}else{
				$this->telegram->send_message( bot_message('payment_request_length_message', $this->bot->chatid) );
			}
		}else if( preg_match("/^(?:998)?(90|91|93|94|95|97|98|99|50|88|77|33)([0-9]{7})$/", preg_replace("/\D/", "", $text), $phone_matches) ){
			//$this->menu->start( "⚠️ Ovoz berishning bu mavsumi yakuniga yetdi. Siz kelgusi mavsumlarda ovoz berishingiz va daromad olishingiz mumkun. Mavsum muddati haqida keyinroq telegram bot orqali xabar beramiz!" );
			if ( intval( $this->bot->voice_limit ) > 0 ) {
				if ( $phone_matches[1] == '33' ) {
					$this->menu->humans();
				}else{
					$this->menu->write_captcha( $phone_matches[1].$phone_matches[2] );
				}
			}else{
				$this->menu->start( bot_message('over_limits_message', $this->bot->chatid) );
			}
		}else{
			$this->menu->understand();
		}
	}

	private function callbackQuery(){
		$this->telegram->set_chatId( $this->updates['callback_query']['message']['chat']['id'] );
		$this->chat_id = $this->telegram->get_chatId();
		$this->user->set_chatid( $this->chat_id );
		parse_str($this->updates['callback_query']['data'], $query);
		if ( !empty( $query['resend_sms'] ) ) {
			$status = $this->voice->resend_sms( $query['resend_sms'] );
			if ( is_array( $status ) ) {
				$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id'], 'text' => bot_message('try_again_otp', $this->bot->chatid, ['seconds' => $status['wait']]), 'show_alert' => true]);
			}else if( is_string( $status ) ){
				$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id'], 'text' => bot_message('otp_has_ben_resent', $this->bot->chatid), 'show_alert' => true]);
				$req = $this->telegram->request('editMessageText', [
                    'chat_id' => $this->updates['callback_query']['message']['chat']['id'],
                    'message_id' => $this->updates['callback_query']['message']['message_id'],
					'reply_markup' => [
                    	'inline_keyboard' => [
	        				[
	            				[
	                				"text" => bot_message('resend_sms', $this->bot->chatid),
	                				"callback_data" => "resend_sms=".$status
	            				]
	        				]
	    				]
                    ],
                    'text' => bot_message('enter_otp_code', $this->bot->chatid),
                    'parse_mode' => 'html',
                	'disable_web_page_preview' => true
				]);
			}else{
				$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id'], 'text' => bot_message('error_resending_otp', $this->bot->chatid), 'show_alert' => true]);
			}
		}else if ( !empty( $query['cash'] ) ) {
			$balance = $this->user->get_data('balance');
			if ( $balance >= $this->bot->min_payment ) {
				$payment_request = $this->db->get_where('payment_requests', [
					'bot_id' => $this->bot->chatid,
					'chat_id' => $this->user->chat_id,
					'status' => '0',
				]);

				if ( $payment_request->num_rows() > 0 ) {
					$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id'], 'text' => bot_message('payment_waiting_message', $this->bot->chatid), 'show_alert' => true]);
				}else{
					$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id']]);
					$this->user->set_data('lastcommand', 'exchange');
					$this->telegram->send_chatAction('typing')->set_replyKeyboard([
						[bot_message('cancel_button', $this->bot->chatid)]
					])->send_message( bot_message('enter_payment_details_message', $this->bot->chatid) );
				}
			}else{
				$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id'], 'text' => bot_message('lack_balance_message', $this->bot->chatid, ['min_payment' => number_format($this->bot->min_payment, 0, ',', ' ')]), 'show_alert' => true]);
			}
			
		}else if ( !empty( $query['channel'] ) ) {
			if ( $this->bot->mandatory_subscription == 1 ){
				$chatmem = $this->telegram->get_chatMember($this->bot->mandatory_chatid, $this->user->chat_id);
				if ( !empty( $chatmem['result']['status'] ) ) {
					if(in_array($chatmem['result']['status'], ['creator', 'creator', 'administrator', 'member'])){
						$this->telegram->request('deleteMessage', ['chat_id' => $this->updates['callback_query']['message']['chat']['id'], 'message_id' => $this->updates['callback_query']['message']['message_id']]);
						$this->menu->start();
					}else{
						$this->telegram->request('answerCallbackQuery', ['callback_query_id' => $this->updates['callback_query']['id'], 'text' => bot_message('not_subscribed_alert_message', $this->bot->chatid), 'show_alert' => true]);
					}
				}else{
					$this->menu->start();
				}
			}else{
				$this->menu->start();
			}
		}
	}

	public function stats(){

		$user = $this->db->get_where('users', [
			'chat_id' => $this->telegram->get_chatId(),
			'bot_id' => $this->bot->chatid
		]);

		$user_data = [
			'chat_id' => $this->telegram->get_chatId(),
			'bot_id' => $this->bot->chatid
		];

		if (!empty( $this->updates['message']['chat']['username'] )){
			$user_data['username'] = $this->updates['message']['chat']['username'];
		}

		if (!empty( $this->updates['message']['chat']['first_name'] )){
			$user_data['firstname'] = $this->updates['message']['chat']['first_name'];
		}

		if (!empty( $this->updates['message']['chat']['last_name'] )){
			$user_data['lastname'] = $this->updates['message']['chat']['last_name'];
		}

		$user_data['lastaction'] = time();

		if ( $user->num_rows() > 0 ) {
			$user = $user->row_array();

			$this->db->update('users', $user_data, [
				'chat_id' => $this->telegram->get_chatId(),
				'bot_id' => $this->bot->chatid
			]);
		}else{
			$user_data['registered'] = time();
			$this->db->insert('users', $user_data);
		}
	}
}