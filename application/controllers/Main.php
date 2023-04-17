<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Owner_model', 'owner');
		$this->load->model('Dtable_model', 'dtable');

		if ($this->owner->checkLogged() == FALSE) {
        	redirect( base_url('login') );
		}

	}

	public function index() {
		redirect( base_url('votes') );
		/*$this->load->view('main/index', [
			'title' => 'Statistika',
			'content' => 'dashboard'
		]);*/
	}

	public function supersection($section='', $subsection='', $id=''){
		if( $this->session->userdata('user_level') != '1' ){
			redirect( base_url() );
			exit(1);
		}

		switch ( $section ) {
			case 'owners':
				$errors = "";
				
				$fields = [
					'name' => '',
					'chat_id' => '',
					'status' => '',
					'level' => '',
					'lastlogged' => '',
					'bots' => '',
				];

				if ( $subsection == 'add' ) {
					if ($this->input->server('REQUEST_METHOD') === 'POST') {
						$data = [
							'name' => $this->input->post('name'),
							'chat_id' => $this->input->post('chat_id'),
							'status' => $this->input->post('status'),
							'level' => $this->input->post('level'),
							'lastlogged' => time(),
							'bots' => implode( ',', (array)$this->input->post('bots') ),
						];

						foreach ($data as $key => $value) {
							$fields[$key] = $value;
						}

						$requirements = [
							'name' => 'ism',
							'chat_id' => 'telegram id'
						];

						$not_filled = [];

						foreach ($requirements as $k => $v) {
							if ( empty( $data[$k] ) ) {
								$not_filled[] = $v;								
							}
						}

						if ( empty( $not_filled ) ) {
							$this->db->insert('owners', $data);
							redirect( base_url('supersection/owners') );
							exit(1);
						}else{
							$errors = ucfirst( implode( ', ', $not_filled ) ) . ' maydonlari to\'ldirilmadi';
						}
					}

					$this->load->view('main/index', [
						'title' => 'Mijoz qo\'shish',
						'content' => 'supersection/owners_add',
						'content_data' => [
							'errors' => $errors,
							'fields' => $fields
						]
					]);
				}else if ( $subsection == 'edit' ) {
					$owner = $this->db->get_where('owners', [
						'id' => $id
					]);

					if ( $owner->num_rows() > 0 ) {
						if ($this->input->server('REQUEST_METHOD') === 'POST') {
							$data = [
								'name' => $this->input->post('name'),
								'chat_id' => $this->input->post('chat_id'),
								'status' => $this->input->post('status'),
								'level' => $this->input->post('level'),
								'lastlogged' => time(),
								'bots' => implode( ',', (array)$this->input->post('bots') ),
							];

							foreach ($data as $key => $value) {
								$fields[$key] = $value;
							}

							$requirements = [
								'name' => 'ism',
								'chat_id' => 'telegram id'
							];

							$not_filled = [];

							foreach ($requirements as $k => $v) {
								if ( empty( $data[$k] ) ) {
									$not_filled[] = $v;								
								}
							}

							if ( empty( $not_filled ) ) {
								$this->db->update('owners', $data, ['id' => $id]);
								redirect( base_url('supersection/owners') );
								exit(1);
							}else{
								$errors = ucfirst( implode( ', ', $not_filled ) ) . ' maydonlari to\'ldirilmadi';
							}
						}

						$this->load->view('main/index', [
							'title' => 'Mijozlar',
							'content' => 'supersection/owners_edit',
							'errors' => $errors,
							'content_data' => $owner->row_array()
						]);
					}else{
						redirect( base_url('eror') );
					}
				}else if ( $subsection == 'delete' ) {
					$owner = $this->db->get_where('owners', [
							'id' => $id
					]);
					if ( $owner->num_rows() > 0 ) {
						$this->db->delete('owners', ['id' => $id]);
						redirect( base_url('supersection/owners') );
					}else{
						redirect( base_url('eror') );
					}
				}else{
					$this->load->view('main/index', [
						'title' => 'Mijozlar',
						'content' => 'supersection/owners'
					]);
				}
			break;

			case 'messages':
				$errors = "";
				if ($this->input->server('REQUEST_METHOD') === 'POST') {
					if ( empty( $errors ) ) {
						$data = $this->input->post();
						$message_keys = array_keys( config_item('message_keys') );
						foreach ($data as $k => $v) {
							if ( in_array($k, $message_keys) ) {
								$this->db->update('settings', [
									'key' => $k,
									'value' => $v,
								], [
									'key' => $k
								]);

								@unlink(APPPATH.'cache/settings_'.$k);
							}
						}
					}
				}

				$this->load->view('main/index', [
					'title' => 'Bot xabarlarini boshqarish',
					'content' => 'supersection/messages',
					'errors' => $errors,
				]);
			break;

			case 'settings':
				$this->load->view('main/index', [
					'title' => 'Mijozlar',
					'content' => 'dashboard'
				]);
			break;
			
			default:
				redirect( base_url() );
			break;
		}
	}

	public function bots($section='', $id='') {
		switch ($section) {
			case 'add':
				if( $this->session->userdata('user_level') != '1' ){
					redirect( base_url() );
					exit(1);
				}
				$errors = "";
				
				$fields = [
					'name' => '',
					'token' => '',
					'board' => '',
					'voice_limit' => '',
					'voice_price' => '',
					'ref_price' => '',
					'min_payment' => '',
					'ref_mode' => '',
					'mandatory_subscription' => '',
					'mandatory_chatid' => '',
					'mandatory_link' => '',
				];

				if ($this->input->server('REQUEST_METHOD') === 'POST') {
					$data = [
						'name' => $this->input->post('name'),
						'token' => $this->input->post('token'),
						'board' => $this->input->post('board'),
						'voice_limit' => $this->input->post('voice_limit'),
						'voice_price' => $this->input->post('voice_price'),
						'ref_price' => $this->input->post('ref_price'),
						'min_payment' => $this->input->post('min_payment'),
						'ref_mode' => $this->input->post('ref_mode'),
						'mandatory_subscription' => $this->input->post('mandatory_subscription'),
						'mandatory_chatid' => $this->input->post('mandatory_chatid'),
						'mandatory_link' => $this->input->post('mandatory_link')
					];

					foreach ($data as $key => $value) {
						$fields[$key] = $value;
					}

					$requirements = [
						'name' => 'nom',
						'token' => 'token',
						'board' => 'tashabbus'
					];

					$not_filled = [];

					foreach ($requirements as $k => $v) {
						if ( empty( $data[$k] ) ) {
							$not_filled[] = $v;								
						}
					}

					if ( empty( $not_filled ) ) {
						$data['board'] = remake_board_url( $data['board'] );

						$bot_data = get_bot_data( $data['token'] );
						if ( $bot_data ) {
							$data['bot_id'] = $bot_data['id'];
							$data['username'] = '@'.$bot_data['username'];
							$data['status'] = '1';

							$this->db->insert('bots', $data);
							redirect( base_url('bots') );
						}else{
							$errors = 'Kiritilgan token orqali telegram serveriga ulanib bo\'lmadi!';
						}
					}else{
						$errors = ucfirst( implode( ', ', $not_filled ) ) . ' maydonlari to\'ldirilmadi';
					}
				}

				$this->load->view('main/index', [
					'title' => 'Telegram bot qo\'shish',
					'content' => 'bots/add',
					'content_data' => [
						'errors' => $errors,
						'fields' => $fields
					]
				]);
			break;

			case 'edit':
				$bot = $this->db->get_where('bots', [
					'id' => $id
				]);
				if ( $bot->num_rows() > 0 ) {
					$bot = $bot->row_array();
					$errors = "";						
					if( $this->session->userdata('user_level') == '1' ){
						if ($this->input->server('REQUEST_METHOD') === 'POST') {
							$data = [
								'name' => $this->input->post('name'),
								'token' => $this->input->post('token'),
								'board' => $this->input->post('board'),
								'voice_limit' => $this->input->post('voice_limit'),
								'voice_price' => $this->input->post('voice_price'),
								'ref_price' => $this->input->post('ref_price'),
								'min_payment' => $this->input->post('min_payment'),
								'ref_mode' => $this->input->post('ref_mode'),
								'mandatory_subscription' => $this->input->post('mandatory_subscription'),
								'mandatory_chatid' => $this->input->post('mandatory_chatid'),
								'mandatory_link' => $this->input->post('mandatory_link')
							];

							$requirements = [
								'name' => 'nom',
								'token' => 'token',
								'board' => 'tashabbus'
							];

							$not_filled = [];

							foreach ($requirements as $k => $v) {
								if ( empty( $data[$k] ) ) {
									$not_filled[] = $v;								
								}
							}

							if ( empty( $not_filled ) ) {
								if ( $data['board'] != $bot['board'] ) {
									$data['board'] = remake_board_url( $data['board'] );
								}

								if ( $data['token'] != $bot['token'] ) {
									$bot_data = get_bot_data( $data['token'] );
									if ( $bot_data ) {
										remove_bot_hook( $bot['token'] );
										$data['bot_id'] = $bot_data['id'];
										$data['username'] = '@'.$bot_data['username'];
									}else{
										$errors = 'Kiritilgan token orqali telegram serveriga ulanib bo\'lmadi!';
									}
								}

								if ( empty( $errors ) ) {
									$this->db->update('bots', $data, [ 'id' => $id ]);
									redirect( base_url('bots') );
								}
							}else{
								$errors = ucfirst( implode( ', ', $not_filled ) ) . ' maydonlari to\'ldirilmadi';
							}
						}
						$this->load->view('main/index', [
							'title' => 'Telegram botni tahrirlash',
							'content' => 'bots/super_edit',
							'errors' => $errors,
							'content_data' => $bot
						]);
					}else{
						if ($this->input->server('REQUEST_METHOD') === 'POST') {
							$data = [
								'board' => $this->input->post('board'),
								'voice_limit' => $this->input->post('voice_limit'),
								'voice_price' => $this->input->post('voice_price'),
								'ref_price' => $this->input->post('ref_price'),
								'min_payment' => $this->input->post('min_payment'),
								'ref_mode' => $this->input->post('ref_mode'),
								'mandatory_subscription' => $this->input->post('mandatory_subscription'),
								'mandatory_chatid' => $this->input->post('mandatory_chatid'),
								'mandatory_link' => $this->input->post('mandatory_link')
							];

							$requirements = [
								'board' => 'tashabbus'
							];

							$not_filled = [];

							foreach ($requirements as $k => $v) {
								if ( empty( $data[$k] ) ) {
									$not_filled[] = $v;								
								}
							}

							if ( empty( $not_filled ) ) {
								if ( $data['board'] != $bot['board'] ) {
									$data['board'] = remake_board_url( $data['board'] );
								}

								$this->db->update('bots', $data, [ 'id' => $id ]);
								redirect( base_url('bots') );
							}else{
								$errors = ucfirst( implode( ', ', $not_filled ) ) . ' maydonlari to\'ldirilmadi';
							}
						}
						$this->load->view('main/index', [
							'title' => 'Telegram botni tahrirlash',
							'content' => 'bots/edit',
							'errors' => $errors,
							'content_data' => $bot
						]);
					}
				}else{
					show_404();
				}
			break;

			case 'messages':
				$bot = $this->db->get_where('bots', [
					'id' => $id
				]);
				if ( $bot->num_rows() > 0 ) {
					$bot = $bot->row_array();
					$errors = "";
					if ($this->input->server('REQUEST_METHOD') === 'POST') {
						if( $this->session->userdata('user_level') != '1' ){
							if ( bot_message('about_button_message', $bot['bot_id']) != $this->input->post('about_button_message') ) {
								$errors = 'Bot haqida ma\'lumotini tahrirlashga ruxsat etilmadi';
							}	
						}

						if ( empty( $errors ) ) {
							$data = $this->input->post();
							$message_keys = array_keys( config_item('message_keys') );
							foreach ($data as $k => $v) {
								if ( in_array($k, $message_keys) ) {
									$check_message = $this->db->get_where('bot_messages', [
										'bot_id' => $bot['bot_id'],
										'key' => $k,
									]);
									if ( $check_message->num_rows() > 0 ) {
										$this->db->update('bot_messages', [
											'bot_id' => $bot['bot_id'],
											'key' => $k,
											'message' => $v,
										], [
											'bot_id' => $bot['bot_id'],
											'key' => $k
										]);
									}else{
										$this->db->insert('bot_messages', [
											'bot_id' => $bot['bot_id'],
											'key' => $k,
											'message' => $v,
										]);
									}
									unlink(APPPATH.'cache/bot_message_'.$bot['bot_id'].'_'.$k);
								}
							}
							redirect( base_url('bots') );
						}
					}
					$this->load->view('main/index', [
						'title' => 'Telegram bot xabarlarini tahrirlash',
						'content' => 'bots/messages',
						'errors' => $errors,
						'content_data' => $bot
					]);
				}else{
					show_404();	
				}
			break;
			
			default:
				$this->load->view('main/index', [
					'title' => 'Telegram botlar',
					'content' => 'bots/list'
				]);
			break;
		}
	}

	public function users($bot_id=''){
		$this->load->view('main/index', [
			'title' => 'Foydalanuvchilar',
			'content' => 'users/list',
			'content_data' => [
				'bot_id' => $bot_id
			]
		]);
	}

	public function referrers($bot_id=''){
		$this->load->view('main/index', [
			'title' => 'Referallar',
			'content' => 'referrers/list',
			'content_data' => [
				'bot_id' => $bot_id
			]
		]);
	}

	public function payments($bot_id=''){
		$this->load->view('main/index', [
			'title' => 'To\'lovlar',
			'content' => 'payments/list',
			'content_data' => [
				'bot_id' => $bot_id
			]
		]);
	}

	public function votes($bot_id=''){
		$this->load->view('main/index', [
			'title' => 'Ovozlar',
			'content' => 'votes/list',
			'content_data' => [
				'bot_id' => $bot_id
			]
		]);
	}

	public function notifications($bot_id=''){
		$this->load->view('main/index', [
			'title' => 'Bildirishnomalar',
			'content' => 'notifications/list',
			'content_data' => [
				'bot_id' => $bot_id
			]
		]);
	}
}