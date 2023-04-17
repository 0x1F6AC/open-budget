<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Render_model extends CI_Model {
	public function supersection_owners( $data = [] ){
		if ( !empty( $data['aaData'] ) ) {
			$aaData = [];
			foreach ($data['aaData'] as $row) {
				$actions = [];
				$row['lastlogged'] = date("Y-m-d | H:i:s", $row['lastlogged']);
				
				$row['status'] = ( $row['status'] == 1 ) ? '<span class="badge text-bg-success">Aktiv</span>' : '<span class="badge text-bg-danger">O\'chirilgan</span>';

				if ( $row['level'] ==  '1' ) {
					$row['level'] = '<span class="badge text-bg-primary">Superadmin</span>';
				}else if ( $row['level'] ==  '2' ) {
					$row['level'] = '<span class="badge text-bg-warning">Kuzatuvchi</span>';
				}else if ( $row['level'] ==  '0' ) {
					$row['level'] = '<span class="badge text-bg-secondary">Tashabbuskor</span>';
				}

				$actions[] = [
					'action' => 'data-location',
					'actionData' => base_url('supersection/owners/edit/' . $row['id']),
					'btn' => 'secondary',
					'icon' => 'far fa-edit'
				];
				$actions[] = [
					'action' => 'data-location',
					'actionData' => base_url('supersection/owners/delete/' . $row['id']),
					'btn' => 'danger',
					'icon' => 'fa fa-trash',
					'confirm' => true
				];
				$row['action'] = $this->actionButtons($actions);
				
				$aaData[] = $row;
			}
			$data['aaData'] = $aaData;
		}

		return $data;
	}

	public function bots_list( $data = [] ){
		if ( !empty( $data['aaData'] ) ) {
			$aaData = [];
			foreach ($data['aaData'] as $row) {
				$actions = [];
				
				$row['status'] = ( $row['status'] == 1 ) ? '<span class="badge text-bg-success">Aktiv</span>' : '<span class="badge text-bg-danger">O\'chirilgan</span>';

				$row['mandatory_subscription'] = ( $row['mandatory_subscription'] == 1 ) ? '<span class="badge text-bg-primary">Yoqilgan</span>' : '<span class="badge text-bg-dark">O\'chirilgan</span>';

				$row['ref_mode'] = ( $row['ref_mode'] == 1 ) ? '<span class="badge text-bg-warning">Aktiv</span>' : '<span class="badge text-bg-secondary">Passiv</span>';

				$actions[] = [
					'action' => 'data-stats',
					'actionData' => $row['id'],
					'btn' => 'warning',
					'icon' => 'fas fa-terminal'
				];

				$actions[] = [
					'action' => 'data-location',
					'actionData' => base_url('bots/messages/' . $row['id']),
					'btn' => 'primary',
					'icon' => 'far fa-comment-dots'
				];

				$actions[] = [
					'action' => 'data-location',
					'actionData' => base_url('bots/edit/' . $row['id']),
					'btn' => 'secondary',
					'icon' => 'far fa-edit'
				];

				if( $this->session->userdata('user_level') == '1' ){
					$actions[] = [
						'action' => 'data-location',
						'actionData' => base_url('bots/delete/' . $row['id']),
						'btn' => 'danger',
						'icon' => 'fa fa-trash',
						'confirm' => true
					];
				}
				

				$row['action'] = $this->actionButtons($actions);
				
				$aaData[] = $row;
			}
			$data['aaData'] = $aaData;
		}

		return $data;
	}

	public function users_list( $data = [] ){
		if ( !empty( $data['aaData'] ) ) {
			$aaData = [];
			foreach ($data['aaData'] as $row) {
				$actions = [];
				
				$row['registered'] = date('Y-m-d | H:i:s', $row['registered']);
				if ( $row['lastaction'] == '0' ) {
					$row['lastaction'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}else{
					$row['lastaction'] = date('Y-m-d | H:i:s', $row['lastaction']);
				}

				$actions[] = [
					'action' => 'data-user-stats',
					'actionData' => $row['id'].'-'.$row['bot_id'],
					'btn' => 'warning',
					'icon' => 'fas fa-terminal'
				];

				$actions[] = [
					'action' => 'data-user-referrers',
					'actionData' => $row['chat_id'].'-'.$row['bot_id'],
					'btn' => 'info',
					'icon' => 'fas fa-link'
				];

				$actions[] = [
					'action' => 'data-user-votes',
					'actionData' => $row['chat_id'].'-'.$row['bot_id'],
					'btn' => 'secondary',
					'icon' => 'fas fa-sticky-note'
				];

				$row['action'] = $this->actionButtons($actions);

				if ( empty( $row['username'] ) ) {
					$row['username'] = '<span class="badge text-bg-danger">Kiritilmagan</span>';
				}else{
					$row['username'] = '@'.$row['username'];
				}

				$row['chat_id'] = $row['firstname'] .' '.$row['lastname'];

				if ( empty( $row['chat_id'] ) ) {
					$row['chat_id'] = '<span class="badge text-bg-danger">Kiritilmagan</span>';
				}

				$bot = $this->db->get_where('bots', ['bot_id' => $row['bot_id']]);
				if ( $bot->num_rows() > 0 ) {
					$row['bot_id'] = $bot->row()->username;
				}else{
					$row['bot_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}
				
				$aaData[] = $row;
			}
			$data['aaData'] = $aaData;
		}

		return $data;
	}

	public function referrers_list( $data = [] ){
		if ( !empty( $data['aaData'] ) ) {
			$aaData = [];
			foreach ($data['aaData'] as $row) {
				$actions = [];
				
				$row['time'] = date('Y-m-d | H:i:s', $row['time']);
				
				$user = $this->db->get_where('users', [
					'bot_id' => $row['bot_id'],
					'chat_id' => $row['chat_id']
				]);
				if ( $user->num_rows() > 0 ) {
					$user = $user->row_array();
					$name = $user['firstname'] . ' '. $user['lastname'];
					if ( !empty( $user['username'] ) ) {
						$name .= " ( @{$user['username']} )";
					}
					$row['chat_id'] = $name;
				}else{
					$row['chat_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}


				$referrer = $this->db->get_where('users', [
					'bot_id' => $row['bot_id'],
					'chat_id' => $row['owner_id']
				]);
				if ( $referrer->num_rows() > 0 ) {
					$referrer = $referrer->row_array();
					$name = $referrer['firstname'] . ' '. $user['lastname'];
					if ( !empty( $referrer['username'] ) ) {
						$name .= " ( @{$referrer['username']} )";
					}
					$row['owner_id'] = $name;
				}else{
					$row['owner_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}

				$bot = $this->db->get_where('bots', ['bot_id' => $row['bot_id']]);
				if ( $bot->num_rows() > 0 ) {
					$row['bot_id'] = $bot->row()->username;
				}else{
					$row['bot_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}
				
				$aaData[] = $row;
			}
			$data['aaData'] = $aaData;
		}

		return $data;
	}

	public function payments_list( $data = [] ){
		if ( !empty( $data['aaData'] ) ) {
			$aaData = [];
			foreach ($data['aaData'] as $row) {
				$actions = [];

				$actions[] = [
					'action' => 'data-payment',
					'actionData' => $row['chat_id'].'-'.$row['bot_id'],
					'btn' => 'warning',
					'icon' => 'fas fa-terminal'
				];

				$actions[] = [
					'action' => 'data-reload-payments',
					'actionData' => '',
					'btn' => 'info',
					'icon' => 'fas fa-sync'
				];

				if ( $row['status'] == 0 ) {
					$actions[] = [
						'action' => 'data-succes-payment',
						'actionData' => $row['chat_id'].'-'.$row['bot_id'],
						'btn' => 'danger',
						'icon' => 'fas fa-check'
					];	
				}

				$row['action'] = $this->actionButtons($actions);

				$row['time'] = date('Y-m-d | H:i:s', $row['time']);
				$row['data'] = "<code class=\"copy-me cursor-pointer\">{$row['data']}</code>";

				$user = $this->db->get_where('users', [
					'bot_id' => $row['bot_id'],
					'chat_id' => $row['chat_id']
				]);

				if ( $user->num_rows() > 0 ) {
					$user = $user->row_array();
					$name = $user['firstname'] . ' '. $user['lastname'];
					if ( !empty( $user['username'] ) ) {
						$name .= " ( @{$user['username']} )";
					}
					$row['chat_id'] = $name;
					$row['balance'] = number_format($user['balance'], 0, ',', ' ');
					if ( $row['status'] == 1 ) {
						$row['balance'] = '<i class="fas fa-check text-success"></i> '.number_format($row['amount'], 0, ',', ' ');
					}
				}else{
					$row['balance'] = '0';
					$row['chat_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}

				$row['status'] = ( $row['status'] == 1 ) ? '<span class="badge text-bg-success">To\'lov qilingan</span>' : '<span class="badge text-bg-danger">To\'lov qilinmagan</span>';

				$bot = $this->db->get_where('bots', ['bot_id' => $row['bot_id']]);
				if ( $bot->num_rows() > 0 ) {
					$row['bot_id'] = $bot->row()->username;
				}else{
					$row['bot_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}

				$aaData[] = $row;
			}
			$data['aaData'] = $aaData;
		}
		return $data;
	}

	public function votes_list( $data = [] ){
		if ( !empty( $data['aaData'] ) ) {
			$aaData = [];
			foreach ($data['aaData'] as $row) {
				$actions = [];
				
				$row['time'] = date('Y-m-d | H:i:s', $row['time']);
				$row['phone'] = format_phone('998'.$row['phone'], ( $this->session->userdata('user_level') == '1' ));
				$row['board'] = "<a href=\"{$row['board']}\" target=\"_blank\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$row['board']}\"><span class=\"badge text-bg-secondary\">Ko'rish</span></a>";
				
				$user = $this->db->get_where('users', [
					'bot_id' => $row['bot_id'],
					'chat_id' => $row['chat_id']
				]);
				if ( $user->num_rows() > 0 ) {
					$user = $user->row_array();
					$name = $user['firstname'] . ' '. $user['lastname'];
					if ( !empty( $user['username'] ) ) {
						$name .= " ( @{$user['username']} )";
					}
					$row['chat_id'] = $name;
				}else{
					$row['chat_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}

				$bot = $this->db->get_where('bots', ['bot_id' => $row['bot_id']]);
				if ( $bot->num_rows() > 0 ) {
					$row['bot_id'] = $bot->row()->username;
				}else{
					$row['bot_id'] = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
				}
				
				$aaData[] = $row;
			}
			$data['aaData'] = $aaData;
		}

		return $data;
	}

	public function notifications_list($data=[]){
		if ( !empty( $data ) ) {
			$tmp_data = [];

			foreach ($data as $file) {
				if ( file_exists( $file ) ) {
					$json = file_get_contents( $file );
					$json = json_decode( $json, TRUE );
					
					if ( $json ) {
						// code...
					}

					$name = $json['firstname'] . ' '. $json['lastname'];
					if ( !empty( $json['username'] ) ) {
						$name .= " ( @{$json['username']} )";
					}

					if ( empty( $name ) ) {
						$name = '<span class="badge text-bg-danger">Aniqlanmagan</span>';
					}

					$tmp_data[] = [
						'user' => $name,
						'message' => "<span data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"".htmlentities( $json['text'] )."\"><span class=\"badge text-bg-secondary cursor-pointer\">Ko'rish</span></span>",
						'time' => date('Y-m-d | H:i:s', $json['time']),
						'bot' => $json['bot_username'],
					];
				}
			}

			return $tmp_data;
		}

		return [];
	}

	private function actionButtons($data='') {
		$html = '<div class="d-flex justify-content-center">';
		foreach ($data as $row) {
			$icon = (array_key_exists('icon', $row)) ? '<i class="'.$row['icon'].'"></i>' : '' ;
			$text = (array_key_exists('text', $row)) ? $row['text'] : '' ;
			$btn = (array_key_exists('btn', $row)) ? $row['btn'] : 'primary' ;
			$action = '';
			if(array_key_exists('action', $row)){
				if (array_key_exists('actionData', $row)) {
					$action = $row['action'].'="'.$row['actionData'].'"';
				}else{
					$action = $row['action'].'=""';
				}
			}

			$confirm = "";

			if(array_key_exists('confirm', $row)){
				if ( $row[ 'confirm' ] ) {
					$confirm = "data-confirm=\"\"";		
				}
			}
			$html .= '<button type="button" class="me-1 btn btn-'.$btn.' shadow btn-xs sharp" '.$action.' '.$confirm.'>'.$icon.' '.$text.'</button>';
		}
		$html .= '</div>';
		return $html;
	}
}