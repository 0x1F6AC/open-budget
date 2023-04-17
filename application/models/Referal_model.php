<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Referal_model extends CI_Model {
	public function check_ref(){
		$query = $this->db->get_where('referals', [
			'bot_id' => $this->bot->chatid,
			'chat_id' => $this->user->chat_id,
		]);

		if ( $query->num_rows() > 0 ) {
			return TRUE;
		}

		return FALSE;
	}

	public function add_passive( $chat_id ){
		$balance = $this->user->get_data('balance', $chat_id);
		$balance = $balance + $this->bot->ref_price;
		$this->user->set_data('balance', $balance, $chat_id);

		$this->db->insert('referals', [
			'bot_id' => $this->bot->chatid,
			'chat_id' => $this->user->chat_id,
			'owner_id' => $chat_id,
			'time' => time(),
			'status' => 1,
		]);

		$this->telegram->send_message( bot_message('new_referrer_message', $this->bot->chatid), $chat_id );
	}

	public function add_active( $chat_id ){
		$this->db->insert('referals', [
			'bot_id' => $this->bot->chatid,
			'chat_id' => $this->user->chat_id,
			'owner_id' => $chat_id,
			'time' => time(),
			'status' => 0,
		]);
	}

	public function update_active(){
		$query = $this->db->get_where('referals', [
			'bot_id' => $this->bot->chatid,
			'chat_id' => $this->user->chat_id,
			'status' => 0,
		]);

		if ( $query->num_rows() > 0 ) {
			$query = $query->row_array();

			$balance = $this->user->get_data('balance', $query['owner_id']);
			$balance = $balance + $this->bot->ref_price;
			$this->user->set_data('balance', $balance, $query['owner_id']);

			$this->db->update('referals', [
				'status' => '1'
			],[
				'bot_id' => $this->bot->chatid,
				'chat_id' => $this->user->chat_id
			]);

			$this->telegram->send_message( bot_message('new_referrer_message', $this->bot->chatid), $query['owner_id'] );
		}
	}

	public function get_referals_count(){
		return $this->db
				->where('bot_id', $this->bot->chatid)
				->where('owner_id', $this->user->chat_id)
				->where('status', 1)
				->from("referals")
		->count_all_results();
	}
}