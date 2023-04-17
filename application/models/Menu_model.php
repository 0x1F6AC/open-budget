<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model {

	public function start( $message = NULL ){
		$this->bot->check_chat_member();
		$this->voice->clear_process();
		$this->user->set_data('lastcommand', '');

		if ( is_null( $message ) ) {
			$message = bot_message('start_message', $this->bot->chatid);
		}

		$this->telegram->send_chatAction('typing')->set_replyKeyboard([
			[bot_message('voting_button', $this->bot->chatid)],
			[bot_message('balance_button', $this->bot->chatid), bot_message('referrer_button', $this->bot->chatid)],
			[bot_message('help_button', $this->bot->chatid), bot_message('about_button', $this->bot->chatid)]
		])->send_message( $message );
	}

	public function voice(){
		$this->telegram->send_chatAction('typing')->send_message( bot_message('voting_button_message', $this->bot->chatid) );
	}

	public function balance(){
		$balance = number_format($this->user->get_data('balance'), 0, ',', ' ');
		$all_voices = $this->db
			->where('bot_id', $this->bot->chatid)
			->where('chat_id', $this->user->chat_id)
			->where('status', 1)
			->from("voices")
		->count_all_results();
		
		$this->telegram->send_chatAction('typing')->set_inlineKeyboard([
        	[
            	[
                	"text" => bot_message('get_cash', $this->bot->chatid),
                	"callback_data" => "cash=payment"
            	]
        	]
    	])->send_message( bot_message('balance_button_message', $this->bot->chatid, [
    		'all_voices' => $all_voices,
    		'balance' => $balance
    	]));
	}

	public function referrer(){
		$ref_payment = number_format($this->bot->ref_price, 0, ',', ' ');
		$ref = $this->ref->get_referals_count();

		$referrer_link = "https://t.me/".ltrim($this->bot->username, '@')."?start=" . $this->telegram->get_chatId();
		
		$this->telegram->send_chatAction('typing')->set_inlineKeyboard([
        	[
            	[
                	"text" => bot_message('share_referrer_link', $this->bot->chatid),
                	"switch_inline_query" => bot_message('share_referrer_link_message', $this->bot->chatid, [
                		'referrer_link' => $referrer_link
                	])
            	]
        	]
    	])->send_message( bot_message('referrer_button_message', $this->bot->chatid, [
            'referrer_link' => $referrer_link,
            'ref_payment' => $ref_payment,
            'ref' => $ref
        ]));
	}

	public function help(){
		$this->telegram->send_chatAction('typing')->send_message( bot_message('help_button_message', $this->bot->chatid) );
	}

	public function about(){
		$this->telegram->send_chatAction('typing')->send_message( bot_message('about_button_message', $this->bot->chatid) );
	}

	public function humans(){
		$this->telegram->send_chatAction('typing')->send_message( bot_message('humans_message', $this->bot->chatid) );
	}

	public function write_captcha( $phone ){
		$phone_status = $this->voice->check_phone( $phone );
		if ( $phone_status ) {
			$this->start( bot_message('number_previously_used_message', $this->bot->chatid) );
		}else{
			$this->budget->removeUserConfig();
			$captcha = $this->budget->getCaptcha();
			if ( $captcha ) {
				$this->voice->step_1( $captcha, $phone );

				$this->telegram->send_chatAction('typing')->set_replyKeyboard([
					[bot_message('cancel_button', $this->bot->chatid)]
				])->send_photo( base_url('tmp/' . $captcha['image']), bot_message('enter_captcha_message', $this->bot->chatid) );
			}else{
				$this->telegram->send_chatAction('typing')->send_message( bot_message('internal_error_message', $this->bot->chatid) );		
			}
		}
	}

	public function write_captcha_answer( $text ){
		if ( is_numeric( $text ) ) {
			$otp = $this->voice->send_otp( $text );
			if ( !empty( $otp['otpKey'] ) ) {
				$this->voice->step_2( $text, $otp );
				$this->telegram->send_chatAction('typing')->set_inlineKeyboard([
	        		[
	            		[
	                		"text" => bot_message('resend_sms', $this->bot->chatid),
	                		"callback_data" => "resend_sms=".$otp['otpKey']
	            		]
	        		]
	    		])->send_message( bot_message('enter_otp_code', $this->bot->chatid) );
			}else{
				if ( !empty( $otp['code'] ) ) {
					if ( $otp['code'] ==  112 ) {
						$this->start( bot_message('number_previously_used_message', $this->bot->chatid) );
					}else{
						$this->start( bot_message('wrong_captcha_message', $this->bot->chatid) );
					}
				}else{
					$this->start( bot_message('internal_error_message', $this->bot->chatid) );
				}
			}
		}else{
			$this->telegram->send_chatAction('typing')->send_message( bot_message('captcha_number_required_message', $this->bot->chatid) );
		}
	}

	public function verify_otp( $otp ){
		if ( is_numeric( $otp ) ) {
			$status = $this->voice->verify_otp( $otp );

			if ( $status == "wrong" ) {
				$this->telegram->send_chatAction('typing')->send_message( bot_message('wrong_otp_entered_message', $this->bot->chatid) );
			}else if ( $status == "expired" ) {
				$this->start( bot_message('expired_otp_message', $this->bot->chatid) );
			}else if ( $status == "undefined" ) {
				$this->start( bot_message('otp_undefined_error_message', $this->bot->chatid) );
			}else if ( $status == "success" ) {
				$this->start( bot_message('vote_success_message', $this->bot->chatid) );
			}else if ( $status == "not_accepted" ) {
				$this->start( bot_message('vote_not_accepted_message', $this->bot->chatid) );
			}else{
				$this->start( bot_message('internal_error_message', $this->bot->chatid) );
			}
		}else{
			$this->telegram->send_chatAction('typing')->send_message( bot_message('otp_code_number_format_required', $this->bot->chatid) );
		}
	}

	public function understand() {
		$this->telegram->send_chatAction('typing')->send_message( bot_message('understand_message', $this->bot->chatid) );
	}
}