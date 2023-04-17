<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Owner_model', 'owner');
		$this->load->model('Dtable_model', 'dtable');
		$this->load->model('Render_model', 'render');
		
		if ($this->owner->checkLogged() == FALSE) {
        	die( json_encode([
        		'status' => 'error',
        		'message' => 'unauthorized'
        	]));
		}
	}

	public function supersection($section=''){
		if( $this->session->userdata('user_level') != '1' ){
			die( json_encode([
        		'status' => 'error',
        		'message' => 'denied'
        	]));
		}

		switch ($section) {
			case 'owners':
				$postData = $this->input->post();
			    $query['like'] = ['id', 'name', 'chat_id', 'bots'];
			    $data = $this->dtable->getData( 'owners', $query, $postData );
			    $data = $this->render->supersection_owners( $data );
			    echo json_encode($data);
			break;
			
			default:
				die( json_encode([
        			'status' => 'error',
        			'message' => 'no route'
        		]));
			break;
		}
	}

	public function bots($section='', $id=''){
		switch ($section) {
			case 'stats':
				$bot = $this->db->get_where('bots', [
					'id' => $id
				]);
				if ( $bot->num_rows() > 0 ) {
					$bot = $bot->row_array();
					die( json_encode([
        				'status' => 'ok',
        				'bot_status' => $bot['status'],
        				'data' => $this->load->view('main/bots/stats', $bot, TRUE)
        			]));
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Ushbu idenfikator asosida ma\'lumot topilmadi!'
        			]));
				}
			break;

			case 'reload':
				if( $this->session->userdata('user_level') != '1' ){
					die( json_encode([
        				'status' => 'error',
        				'message' => 'denied'
        			]));
				}

				$bot = $this->db->get_where('bots', [
					'id' => $id
				]);

				if ( $bot->num_rows() > 0 ) {
					$bot = $bot->row_array();
					$reload_status = reload_status( $bot['token'], $bot['id'] );
					if ( $reload_status ) {
						die( json_encode([
        					'status' => 'ok',
        					'message' => 'Bot qayta tiklandi!'
        				]));
					}else{
						die( json_encode([
        					'status' => 'error',
        					'message' => 'Botni qayta tiklashda xatolik!'
        				]));
					}
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Ushbu idenfikator asosida ma\'lumot topilmadi!'
        			]));
				}
			break;

			case 'pause':
				if( $this->session->userdata('user_level') != '1' ){
					die( json_encode([
        				'status' => 'error',
        				'message' => 'denied'
        			]));
				}

				$bot = $this->db->get_where('bots', [
					'id' => $id
				]);

				if ( $bot->num_rows() > 0 ) {
					$bot = $bot->row_array();
					$bot['status'] = ( $bot['status'] == '1' ) ? '0' : '1';

					$this->db->update('bots', ['status' => $bot['status']], ['id' => $id]);
					die( json_encode([
        				'status' => 'ok',
        				'bot_status' => $bot['status'],
        				'message' => ( $bot['status'] == '1' ) ? 'Bot faoliyati tiklandi!' : 'Bot faoliyati to\'xtatildi!'
        			]));
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Ushbu idenfikator asosida ma\'lumot topilmadi!'
        			]));
				}
			break;
			
			default:
				$postData = $this->input->post();
			    $query['like'] = ['bot_id', 'token', 'name', 'username', 'board', 'mandatory_link'];
			    $conditions = [];
			    if( $this->session->userdata('user_level') != '1' ){
			    	$conditions[] = [
						'con' => 'where_in',
						'col' => 'bot_id',
						'val' => explode(',', $this->session->userdata('user_bots'))
					];
					$conditions[] = [
						'con' => 'where',
						'col' => 'status',
						'val' => '1'
					];
			    }
			    $data = $this->dtable->getData( 'bots', $query, $postData, $conditions );
			    $data = $this->render->bots_list( $data );
			    echo json_encode($data);
			break;
		}
	}

	public function users($section='', $id=''){
		switch( $section ){
			case 'stats':
				$data = explode('-', $id);
				if (!empty( $data ) && count( $data ) > 1) {
					$user_bots = explode(',', $this->session->userdata('user_bots'));
					if( $this->session->userdata('user_level') != '1' ){
						if ( !in_array($data[1], $user_bots) ) {
							die( json_encode([
		        				'status' => 'error',
		        				'message' => 'Huquqlar mavjud emas!'
		        			]));
						}
					}
					$user = $this->db->get_where('users', [
						'id' => $data[0],
						'bot_id' => $data[1],
					]);
					if ( $user->num_rows() > 0 ) {
						die( json_encode([
	        				'status' => 'ok',
	        				'data' => $this->load->view('main/users/stats', $user->row_array(), TRUE)
	        			]));
					}else{
						die( json_encode([
	        				'status' => 'error',
	        				'message' => 'Ushbu idenfikator asosida ma\'lumot topilmadi!'
	        			]));
					}
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;

			case 'referrers':
				$data = explode('-', $id);
				if (!empty( $data ) && count( $data ) > 1) {
					$user_bots = explode(',', $this->session->userdata('user_bots'));
					if( $this->session->userdata('user_level') != '1' ){
						if ( !in_array($data[1], $user_bots) ) {
							die( json_encode([
		        				'status' => 'error',
		        				'message' => 'Huquqlar mavjud emas!'
		        			]));
						}
					}
					$referals = $this->db->get_where('referals', [
						'owner_id' => $data[0],
						'bot_id' => $data[1],
						'status' => '1'
					]);
					if ( $referals->num_rows() > 0 ) {
						die( json_encode([
	        				'status' => 'ok',
	        				'data' => $this->load->view('main/users/referals', ['referals' => $referals->result_array()], TRUE)
	        			]));
					}else{
						die( json_encode([
	        				'status' => 'error',
	        				'message' => 'Ma\'lumotlar topilmadi!'
	        			]));
					}
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;

			case 'votes':
				$data = explode('-', $id);
				if (!empty( $data ) && count( $data ) > 1) {
					$user_bots = explode(',', $this->session->userdata('user_bots'));
					if( $this->session->userdata('user_level') != '1' ){
						if ( !in_array($data[1], $user_bots) ) {
							die( json_encode([
		        				'status' => 'error',
		        				'message' => 'Huquqlar mavjud emas!'
		        			]));
						}
					}
					$voices = $this->db->get_where('voices', [
						'chat_id' => $data[0],
						'bot_id' => $data[1],
						'status' => '1'
					]);
					if ( $voices->num_rows() > 0 ) {
						die( json_encode([
	        				'status' => 'ok',
	        				'data' => $this->load->view('main/users/voices', ['voices' => $voices->result_array()], TRUE)
	        			]));
					}else{
						die( json_encode([
	        				'status' => 'error',
	        				'message' => 'Ma\'lumotlar topilmadi!'
	        			]));
					}
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;

			default:
				$postData = $this->input->post();
			    $query['like'] = ['bot_id', 'chat_id', 'username', 'firstname', 'lastname'];
			    $conditions = [];
			    if( $this->session->userdata('user_level') != '1' ){
			    	$conditions[] = [
						'con' => 'where_in',
						'col' => 'bot_id',
						'val' => explode(',', $this->session->userdata('user_bots'))
					];
			    }

			    if ( is_numeric( $id ) ) {
			    	$conditions[] = [
						'con' => 'where',
						'col' => 'bot_id',
						'val' => $id
					];
			    }
			    $data = $this->dtable->getData( 'users', $query, $postData, $conditions );
			    $data = $this->render->users_list( $data );
			    echo json_encode($data);
			break;
		}
	}

	public function referrers($id=''){
		$postData = $this->input->post();
	    $query['like'] = ['bot_id', 'chat_id', 'owner_id'];
	    $conditions = [];
	    if( $this->session->userdata('user_level') != '1' ){
	    	$conditions[] = [
				'con' => 'where_in',
				'col' => 'bot_id',
				'val' => explode(',', $this->session->userdata('user_bots'))
			];
	    }

	    if ( is_numeric( $id ) ) {
	    	$conditions[] = [
				'con' => 'where',
				'col' => 'bot_id',
				'val' => $id
			];
	    }

	    $conditions[] = [
			'con' => 'where',
			'col' => 'status',
			'val' => '1'
		];

	    $data = $this->dtable->getData( 'referals', $query, $postData, $conditions );
	    $data = $this->render->referrers_list( $data );
	    echo json_encode($data);
	}

	public function payments($section='', $id=''){
		switch( $section ){
			case 'status':
				$data = explode('-', $id);
				if (!empty( $data ) && count( $data ) > 1) {
					$user_bots = explode(',', $this->session->userdata('user_bots'));
					if( $this->session->userdata('user_level') != '1' ){
						if ( !in_array($data[1], $user_bots) ) {
							die( json_encode([
		        				'status' => 'error',
		        				'message' => 'Huquqlar mavjud emas!'
		        			]));
						}
					}
					$payment = $this->db->get_where('payment_requests', [
						'chat_id' => $data[0],
						'bot_id' => $data[1],
					]);

					if ( $payment->num_rows() > 0 ) {
						$payment = $payment->row_array();
						$payment['status'] = ( $payment['status'] == '1' ) ? '1' : '1';

						if ( $payment['status'] == '1' ) {
							$bot = $this->db->get_where('bots', [
								'bot_id' => $data[1]
							]);

							if ( $bot->num_rows() > 0 ) {
								$bot = $bot->row_array();
								send_payment_message($bot['token'], $data[0] );
							}

							@unlink(APPPATH.'cache/user_'.$data[0].'_'.$data[1]);
							$user = $this->db->get_where('users', [
								'chat_id' => $data[0],
								'bot_id' => $data[1]
							]);

							$amount = ( $user->num_rows() > 0 ) ? $user->row()->balance : 0 ;

							$this->db->update('users', ['balance' => '0'], [
								'chat_id' => $data[0],
								'bot_id' => $data[1]
							]);
						}
						
						$this->db->update('payment_requests', ['status' => $payment['status'], 'amount' => $amount], [
							'chat_id' => $data[0],
							'bot_id' => $data[1],
						]);
						die( json_encode([
	        				'status' => 'ok',
	        				'payment_status' => $payment['status'],
	        				'message' => ( $payment['status'] == '1' ) ? 'To\'lov amalga oshirildi!' : 'To\'lov bekor qilindi!'
	        			]));
					}else{
						die( json_encode([
	        				'status' => 'error',
	        				'message' => 'Ma\'lumotlar topilmadi!'
	        			]));
					}
				}
			break;
			case 'message':
				$data = explode('-', $id);
				if (!empty( $data ) && count( $data ) > 1) {
					$user_bots = explode(',', $this->session->userdata('user_bots'));
					if( $this->session->userdata('user_level') != '1' ){
						if ( !in_array($data[1], $user_bots) ) {
							die( json_encode([
		        				'status' => 'error',
		        				'message' => 'Huquqlar mavjud emas!'
		        			]));
						}
					}
					$payment = $this->db->get_where('payment_requests', [
						'chat_id' => $data[0],
						'bot_id' => $data[1],
					]);

					if ( $payment->num_rows() > 0 ) {
						$bot = $this->db->get_where('bots', [
							'bot_id' => $data[1]
						]);

						if ( $bot->num_rows() > 0 ) {
							$bot = $bot->row_array();
							if ( send_payment_message($bot['token'], $data[0] ) ) {
								die( json_encode([
	        						'status' => 'ok',
	        						'message' => 'Xabarnoma yuborildi!'
	        					]));	
							}else{
								die( json_encode([
	        						'status' => 'error',
	        						'message' => 'Xabarnoma yuborishda xatolik!'
	        					]));
							}
						}else{
							die( json_encode([
	        					'status' => 'error',
	        					'message' => 'Xabarnoma uchun bot topilmadi!'
	        				]));
						}
					}else{
						die( json_encode([
	        				'status' => 'error',
	        				'message' => 'Ma\'lumotlar topilmadi!'
	        			]));
					}
				}
			break;
			case 'data':
				$data = explode('-', $id);
				if (!empty( $data ) && count( $data ) > 1) {
					$user_bots = explode(',', $this->session->userdata('user_bots'));
					if( $this->session->userdata('user_level') != '1' ){
						if ( !in_array($data[1], $user_bots) ) {
							die( json_encode([
		        				'status' => 'error',
		        				'message' => 'Huquqlar mavjud emas!'
		        			]));
						}
					}
					$user = $this->db->get_where('users', [
						'chat_id' => $data[0],
						'bot_id' => $data[1],
					]);
					if ( $user->num_rows() > 0 ) {
						die( json_encode([
	        				'status' => 'ok',
	        				'data' => $this->load->view('main/users/stats', $user->row_array(), TRUE)
	        			]));
					}else{
						die( json_encode([
	        				'status' => 'error',
	        				'message' => 'Ushbu idenfikator asosida ma\'lumot topilmadi!'
	        			]));
					}
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;
			default:
				$postData = $this->input->post();
			    $query['like'] = ['bot_id', 'chat_id', 'data'];
			    $conditions = [];
			    if( $this->session->userdata('user_level') != '1' ){
			    	$conditions[] = [
						'con' => 'where_in',
						'col' => 'bot_id',
						'val' => explode(',', $this->session->userdata('user_bots'))
					];
			    }

			    if ( is_numeric( $id ) ) {
			    	$conditions[] = [
						'con' => 'where',
						'col' => 'bot_id',
						'val' => $id
					];
			    }

			    $data = $this->dtable->getData( 'payment_requests', $query, $postData, $conditions );
			    $data = $this->render->payments_list( $data );
			    echo json_encode($data);
			break;
		}
	}

	public function votes($id=''){
		$postData = $this->input->post();
	    $query['like'] = ['bot_id', 'chat_id', 'phone', 'board'];
	    $conditions = [];
	    if( $this->session->userdata('user_level') != '1' ){
	    	$conditions[] = [
				'con' => 'where_in',
				'col' => 'bot_id',
				'val' => explode(',', $this->session->userdata('user_bots'))
			];
	    }

	    if ( is_numeric( $id ) ) {
	    	$conditions[] = [
				'con' => 'where',
				'col' => 'bot_id',
				'val' => $id
			];
	    }

	    $conditions[] = [
			'con' => 'where',
			'col' => 'status',
			'val' => '1'
		];

	    $data = $this->dtable->getData( 'voices', $query, $postData, $conditions );
	    $data = $this->render->votes_list( $data );
	    echo json_encode($data);
	}

	public function notifications($segment='', $id=''){
		switch ($segment) {
			case 'send':
				$bots = $this->input->post( 'bots' );
				$message = $this->input->post( 'message' );
				if ( !empty( $bots ) && !empty( $message ) ) {

					if ( strlen( $message ) < 50 ) {
						die( json_encode([
				        	'status' => 'error',
				        	'message' => 'Xabar uzunligi 50 dona belgidan kam bo\'lmasligi lozim!'
				        ]));
					}

					if( $this->session->userdata('user_level') != '1' ){
						$user_bots = explode(',', $this->session->userdata('user_bots'));
                        if ( !empty( $user_bots ) ) {
                        	foreach ($bots as $bot) {
                        		if ( !in_array($bot, $user_bots) ) {
                        			die( json_encode([
				        				'status' => 'error',
				        				'message' => 'Ruxsat etilmagan botlar kiritilgan!'
				        			]));
                        		}
                        	}
                        }
					}

					foreach ($bots as $bot) {
						if ( check_notifications( $bot ) ) {
							die( json_encode([
				        		'status' => 'error',
				        		'message' => 'Yuborish jarayonida xabarlar mavjud, iltimos, kuting!'
				        	]));
						}
					}

					foreach ($bots as $bot) {
						$bot_data = $this->db->get_where('bots', ['bot_id' => $bot, 'status' => '1']);
						if ( $bot_data->num_rows() > 0 ) {
							$bot_data = $bot_data->row_array();
							if ( !folder_exist( FCPATH . 'tmp/notifications/' ) ) {
								mkdir( FCPATH . 'tmp/notifications/' );	
							}
							$users = $this->db->get_where('users', ['bot_id' => $bot]);
							if ( $users->num_rows() > 0 ) {
								$filetime = time();
								foreach ($users->result_array() as $user) {
									$filetime += rand(1, 2);
									create_notification( $message,  $user, $bot_data, $filetime );	
								}
							}
						}
					}

					die( json_encode([
        				'status' => 'ok',
        				'message' => 'Xabar muvaffaqiyatli yuborildi!'
        			]));

				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;

			case 'trash':
				$bots = $this->input->post( 'bots' );
				if ( !empty( $bots ) ) {
					if( $this->session->userdata('user_level') != '1' ){
						$user_bots = explode(',', $this->session->userdata('user_bots'));
                        if ( !empty( $user_bots ) ) {
                        	foreach ($bots as $bot) {
                        		if ( !in_array($bot, $user_bots) ) {
                        			die( json_encode([
				        				'status' => 'error',
				        				'message' => 'Ruxsat etilmagan botlar kiritilgan!'
				        			]));
                        		}
                        	}
                        }
					}

					foreach ($bots as $bot) {
						clear_notifications( $bot );
					}

					die( json_encode([
        				'status' => 'ok',
        				'message' => 'Xabarlar muvaffaqiyatli tozalandi!'
        			]));
				}else{
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;
			
			default:
				$postData = $this->input->post();

				if ( !empty( $postData ) ) {
					$notifications = get_notifications( $id );
					$notifications_count = count( $notifications );

					usort( $notifications, function( $a, $b ) { return filemtime($a) - filemtime($b); } );
					$notifications = array_reverse( $notifications );

					$notifications = array_slice( $notifications, $postData['start'], $postData['length'] ); 

					$result = [
                		'draw' => 0,
                		'iTotalRecords' => $notifications_count,
                		'iTotalDisplayRecords' => $notifications_count,
                		'aaData' => $this->render->notifications_list( $notifications ),
            		];
				}else{
					$result = [
                		'draw' => 0,
                		'iTotalRecords' => 0,
                		'iTotalDisplayRecords' => 0,
                		'aaData' => [],
            		];
				}

				echo json_encode($result);
			break;
		}
	}

	public function export($section=''){
		switch ($section) {
			case 'votes':
				$bots = $this->input->post( 'bots' );
				$start = $this->input->post( 'start' );
				$end = $this->input->post( 'end' );
				if ( !empty( $bots ) && !empty( $start ) && !empty( $end ) ) {
					if( $this->session->userdata('user_level') != '1' ){
						$user_bots = explode(',', $this->session->userdata('user_bots'));
                        if ( !empty( $user_bots ) ) {
                        	foreach ($bots as $bot) {
                        		if ( !in_array($bot, $user_bots) ) {
                        			header('Content-Type: application/json; charset=utf-8');
                        			die( json_encode([
				        				'status' => 'error',
				        				'message' => 'Ruxsat etilmagan botlar kiritilgan!'
				        			]));
                        		}
                        	}
                        }
					}

					$start = strtotime( $start . ' 00:00:00' );
					$end = strtotime( $end . ' 23:59:59' );

					$votes = $this->db
							->where('status', '1')
							->where_in('bot_id', $bots)
							->where('time >=', $start)
							->where('time <=', $end)
							->get('voices');

					if ( $votes->num_rows() > 0 ) {
						header("Content-Type: text/html");
						echo '<table>';
						echo '<thead><tr><th data-f-bold="true">Foydalanuvchi</th><th data-f-bold="true">Telefon raqam</th><th data-f-bold="true">Vaqt</th><th data-f-bold="true">Bot</th><th data-f-bold="true">Tashabbus</th></tr></thead>';
						echo '<tbody>';
						foreach ($votes->result_array() as $vote) {
							$user = $this->db->get_where('users', [
								'bot_id' => $vote['bot_id'],
								'chat_id' => $vote['chat_id']
							]);
							if ( $user->num_rows() > 0 ) {
								$user = $user->row_array();
								$name = $user['firstname'] . ' '. $user['lastname'];
								if ( !empty( $user['username'] ) ) {
									$name .= " ( @{$user['username']} )";
								}
								$user = $name;
							}else{
								$user = 'Aniqlanmagan';
							}

							$bot = $this->db->get_where('bots', ['bot_id' => $vote['bot_id']]);
							if ( $bot->num_rows() > 0 ) {
								$bot = $bot->row()->username;
							}else{
								$bot = 'Aniqlanmagan';
							}

							echo "<tr><td>{$user}</td><td>".format_phone('998'.$vote['phone'], FALSE)."</td><td>".date('Y-m-d | H:i:s', $vote['time'])."</td><td>{$bot}</td><td>{$vote['board']}</td></tr>";
						}
						echo '</tbody>';
						echo '</table>';
					}else{
						header('Content-Type: application/json; charset=utf-8');
						die( json_encode([
        					'status' => 'error',
        					'message' => 'Eksport qilish uchun ma\'lumotlar topiladi!'
        				]));
					}
					
				}else{
					header('Content-Type: application/json; charset=utf-8');
					die( json_encode([
        				'status' => 'error',
        				'message' => 'Kerakli maydonlar to\'ldirilmagan!'
        			]));
				}
			break;
			
			default:
				
			break;
		}
	}
}