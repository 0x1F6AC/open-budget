<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
	public $chat_id;
	public $bot_id;

	public function set_chatid( $id ){
		$this->chat_id = $id;
		return $this;
	}

	public function set_botid( $id ){
		$this->bot_id = $id;
		return $this;
	}

	public function get_data( $key, $chat_id = NULL, $bot_id = NULL ){
		
		if ( is_null( $chat_id ) ) $chat_id = $this->chat_id;
		if ( is_null( $bot_id ) ) $bot_id = $this->bot_id;

		if ( ! $user = $this->cache->file->get( 'user_' . $chat_id . '_'. $bot_id ) ) {
			$user = $this->db->get_where('users', [
				'chat_id' => $chat_id,
				'bot_id' => $bot_id
			]);

			if ( $user->num_rows() > 0 ) {
				$user = $user->row_array();
			}else{
				$user = [];
			}

			$user = json_encode( $user );

			$this->cache->file->save('user_' . $chat_id . '_'. $bot_id, $user, 300);
		}

		$data = json_decode( $user, TRUE );
	
		return array_key_exists( $key , $data) ? $data[ $key ] : FALSE;
	}

	public function set_data( $key, $value, $chat_id = NULL, $bot_id = NULL ){
		if ( is_null( $chat_id ) ) $chat_id = $this->chat_id;
		if ( is_null( $bot_id ) ) $bot_id = $this->bot_id;

		$this->db->update('users', [
			$key => $value
		],[
			'chat_id' => $chat_id,
			'bot_id' => $bot_id
		]);

		$this->update_data($chat_id);
	}

	public function update_data( $chat_id = NULL, $bot_id = NULL ) {
		if ( is_null( $chat_id ) ) $chat_id = $this->chat_id;
		if ( is_null( $bot_id ) ) $bot_id = $this->bot_id;

		$user = $this->db->get_where('users', [
			'chat_id' => $chat_id,
			'bot_id' => $bot_id
		]);

		if ( $user->num_rows() > 0 ) {
			
			$this->cache->file->save('user_' . $chat_id . '_'. $bot_id, json_encode( $user->row_array() ), 300);

			return TRUE;
		}

		return FALSE;
	}
}