<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner_model extends CI_Model {
	public function checkLogged(){
		if ($this->session->has_userdata('logged') == TRUE) {
			$id = $this->session->userdata('user_id');
        	if ( !empty( $id ) ) {
        		$query = $this->db->get_where( 'owners', ['id' => $id, 'status' => '1'] );
        		if ( $query->num_rows() > 0 ) {
        			$query = $query->row();
        			$this->session->set_userdata('user_bots', $query->bots);
        			$this->session->set_userdata('user_level', $query->level);
        			return TRUE;	
        		}	
        	}
		}

		return FALSE;
	}

	public function auth($auth_data=[]) {
		
		if( !array_key_exists('id', $auth_data) ) return FALSE;
		if( !array_key_exists('hash', $auth_data) ) return FALSE;
		return $this->checkTelegramAuthorization($auth_data);
	}

	public function checkTelegramAuthorization($auth_data) {
  		$check_hash = $auth_data['hash'];
  		unset($auth_data['hash']);
  		$data_check_arr = [];
  		foreach ($auth_data as $key => $value) {
    		$data_check_arr[] = $key . '=' . $value;
  		}
  		sort($data_check_arr);
  		$data_check_string = implode("\n", $data_check_arr);
  		$secret_key = hash('sha256', setting_item('bot_token'), true);
  		$hash = hash_hmac('sha256', $data_check_string, $secret_key);
  		if (strcmp($hash, $check_hash) !== 0) {
    		return FALSE;
  		}
  		if ((time() - $auth_data['auth_date']) > 86400) {
    		return FALSE;
  		}
  		return $auth_data;
	}
}