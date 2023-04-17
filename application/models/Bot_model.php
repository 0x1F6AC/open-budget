<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bot_model extends CI_Model {
	public $id;
	public $chatid;
	public $name;
	public $username;
	public $token;
	public $board;
	public $voice_limit;
	public $voice_price;
	public $ref_price;
	public $min_payment;
	public $ref_mode;

	public function getBot( $id ){
		$query = $this->db->get_where('bots', ['id' => $id]);
		
		if ( $query->num_rows() > 0 ) {
			$query = $query->row_array();
			if ($query['status'] == 1) {
				return $query;
			}
		}

		return FALSE;
	}

	public function check_chat_member(){
		if ( $this->bot->mandatory_subscription == 0 ) return;
		
		$chatmem = $this->telegram->get_chatMember($this->bot->mandatory_chatid, $this->user->chat_id);
		if ( !empty( $chatmem['result']['status'] ) ) {
			if(!in_array($chatmem['result']['status'], ['creator', 'creator', 'administrator', 'member'])){
			
				$this->telegram->send_chatAction('typing')->set_inlineKeyboard([
	            	[
	                	[
	                    	"text" => bot_message('subscribed_message', $this->bot->chatid),
	                    	"callback_data" => "channel=subscribed"
	                	]
	            	],
	            	[
	                	[
	                    	"text" => bot_message('go_to_channel_message', $this->bot->chatid),
	                    	"url" => $this->bot->mandatory_link
	                	]
	            	]
	        	])->send_message( bot_message('subscribe_message', $this->bot->chatid) );

	        	exit(1);
			}
		}
	}
}