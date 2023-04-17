<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Owner_model', 'owner');
	}

	public function index(){
		if ($this->owner->checkLogged() == TRUE) {
        	redirect( base_url() );
		}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$auth_data = $this->input->post();
			$login_status = $this->owner->auth( $auth_data );

			if ( $login_status ) {
				$query = $this->db->get_where('owners', [
					'chat_id' => $login_status['id']
				]);

				if ( $query->num_rows() > 0 ) {
					$query = $query->row_array();
					if ( $query['status'] == '1') {
						if (array_key_exists('first_name', $login_status)) {
							$this->session->set_userdata('telegram_first_name', $login_status['first_name']);
						}
						if (array_key_exists('last_name', $login_status)) {
							$this->session->set_userdata('telegram_last_name', $login_status['last_name']);
						}
						if (array_key_exists('username', $login_status)) {
							$this->session->set_userdata('telegram_username', $login_status['username']);
						}
						if (array_key_exists('photo_url', $login_status)) {
							$this->session->set_userdata('telegram_photo_url', $login_status['photo_url']);
						}
						if (array_key_exists('auth_date', $login_status)) {
							$this->session->set_userdata('telegram_auth_date', $login_status['auth_date']);
						}
						$this->session->set_userdata('telegram_id', $login_status['id']);
						$this->session->set_userdata('logged', TRUE);

						$this->session->set_userdata('user_level', $query['level']);
						$this->session->set_userdata('user_bots', $query['bots']);
						$this->session->set_userdata('user_name', $query['name']);
						$this->session->set_userdata('user_id', $query['id']);
						
						$this->db->update('owners', ['lastlogged' => time()], ['chat_id' => $login_status['id']]);

						die( json_encode(['status' => 'ok']) );

					}else{
						die( json_encode(['status' => 'error', 'message' => 'Kechirasiz, sizning akkountizgiz o\'chirilgan']) );
					}
				}else{
					die( json_encode(['status' => 'error', 'message' => 'Kechirasiz, bunday foydalanuvchi mavjud emas']) );
				}
			}else{
				die( json_encode(['status' => 'error', 'message' => 'Tizimga kirishda xatolik']) );
			}
		}

		$this->load->view('main/login');
	}

	public function logout() {
		$this->session->sess_destroy();
		redirect( base_url('login') );
	}
}