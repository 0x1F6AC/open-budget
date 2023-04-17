<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voice_model extends CI_Model {
	public function step_1( $captcha, $phone ){
		$this->db->insert('voice_process', [
			'step' => '1',
			'captcha_file' => $captcha['image'],
			'captcha_key' => $captcha['key'],
			'phone' => $phone,
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid,
		]);
	}

	public function step_2( $answer, $otp ){
		$this->db->update('voice_process', [
			'captcha_result' => $answer,
			'step' => '2',
			'otpKey' => $otp['otpKey'],
			'retryAfter' => $otp['retryAfter'],
			'retry_begin' => time(),
		], [
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid,
		]);
	}


	public function send_otp( $captcha_result ){
		$query = $this->db->get_where('voice_process', [
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid
		]);

		if ( $query->num_rows() > 0 ) {
			$query = $query->row_array();
			@unlink( FCPATH.'tmp/'.$query['captcha_file'] );
			return $this->budget->vote_check($query['captcha_key'], $captcha_result, $query['phone']);
		}

		return FALSE;
	}

	public function resend_sms( $otpKey ){
		$query = $this->db->get_where('voice_process', [
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid,
			'otpKey' => $otpKey
		]);

		if ( $query->num_rows() > 0 ) {
			$query = $query->row_array();
			if ( ( intval($query['retry_begin']) + intval($query['retryAfter']) ) > time() ) {
				$wait = ( ( $query['retry_begin'] + $query['retryAfter'] ) - time()  );
				return [
					'begin' => $query['retry_begin'],
					'after' => $query['retryAfter'],
					'wait' => ( $wait < 0 ) ? 0 : $wait
				];
			}
			$new_otp = $this->budget->resend_sms( $otpKey );
			if ( !empty( $new_otp['otpKey'] ) ) {
				$this->db->update('voice_process', [
					'otpKey' => $new_otp['otpKey'],
					'retryAfter' => $new_otp['retryAfter'],
				], [
					'chat_id' => $this->user->chat_id,
					'bot_id' => $this->bot->chatid,
				]);

				return $new_otp['otpKey'];
			}
		}

		return FALSE;
	}

	public function verify_otp( $otp ){
		$query = $this->db->get_where('voice_process', [
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid
		]);

		if ( $query->num_rows() > 0 ) {
			$query = $query->row_array();
			$verify_status = $this->budget->vote_verify( $otp, $query['otpKey'] );
			if ( is_array($verify_status) && array_key_exists('code', $verify_status) ) {
				if ( in_array( $verify_status['code'] , [108]) ) {
					return "wrong";
				}else if ( in_array( $verify_status['code'] , [107, 116, 109]) ) {
					return "expired";
				}else{
					return "undefined";
				}
			}else if(is_string($verify_status) && $verify_status == "success"){
				$this->db->insert('voices', [
					'phone' => $query['phone'],
					'bot_id' => $this->bot->chatid,
					'chat_id' => $this->user->chat_id,
					'board' => $this->bot->board,
					'time' => time(),
					'status' => 1,
				]);
				$blance = $this->user->get_data('balance');
				$blance = $blance + $this->bot->voice_price;
				$this->user->set_data('balance', $blance);
				$this->db->where('bot_id', $this->bot->chatid)->set('voice_limit', 'voice_limit-1', FALSE)->update('bots');

				if ( $this->bot->ref_mode == 1 ) {
					$this->ref->update_active();
				}

				return "success";
			}else if(is_string($verify_status) && $verify_status == "not_accepted"){
				return "not_accepted";
			}
		}

		return FALSE;
	}

	public function get_step(){
		$query = $this->db->get_where('voice_process', [
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid
		]);

		if ( $query->num_rows() > 0 ) {
			return strval( $query->row()->step );
		}

		return FALSE;
	}

	public function clear_process(){
		$this->db->delete('voice_process', [
			'chat_id' => $this->user->chat_id,
			'bot_id' => $this->bot->chatid
		]);
	}

	public function check_phone( $phone ){
		$query = $this->db->get_where('voices', [
			'phone' => $phone
		]);

		if ( $query-> num_rows() > 0 ) {
			return $query->row_array();
		}

		return FALSE;
	}
}