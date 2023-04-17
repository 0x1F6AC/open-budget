<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crone extends CI_Controller {

	public function __construct(){
		parent::__construct();
		 if (!$this->input->is_cli_request()){
		 	die('denied!');
		 }
	}

	public function index(){
		
	}

	public function stats(){
		$message = "📊 ". date('Y-m-d H:i') ." holati uchun statistika:\n\n🔸Ovozlar:\n--------------------\n🗣 Umumiy: {all_voices} dona\n🗓  Bugungi: {this_day_voices} dona\n🕐 Oxirgi soatda: {last_hour_voices} dona\n\n🔸Foydalanuvchilar:\n--------------------\n👨‍👩‍👦‍👦 Umumiy: {all_users} dona\n🗓  Bugungi: {this_day_users} dona\n🕐 Oxirgi soatda: {last_hour_users} dona\n\n🔸Referallar:\n--------------------\n🔗 Umumiy: {all_referrers} dona\n🗓  Bugungi: {this_day_referrers} dona\n🕐 Oxirgi soatda: {last_hour_referrers} dona";

		$all_voices = $this->db
			->where('status', 1)
			->from("voices")
		->count_all_results();

		$this_day_voices = $this->db
			->where('status', 1)
			->where('time>=', strtotime( date( 'Y-m-d 00:00:00' ) ))
			->where('time<=', strtotime( date( 'Y-m-d 23:59:59' ) ))
			->from("voices")
		->count_all_results();

		$last_hour_voices = $this->db
			->where('status', 1)
			->where('time>=', ( strtotime('now') - 3600 ) )
			->from("voices")
		->count_all_results();

		$all_users = $this->db
			->from("users")
		->count_all_results();

		$this_day_users = $this->db
			->where('registered>=', strtotime( date( 'Y-m-d 00:00:00' ) ))
			->where('registered<=', strtotime( date( 'Y-m-d 23:59:59' ) ))
			->from("users")
		->count_all_results();

		$last_hour_users = $this->db
			->where('registered>=', ( strtotime('now') - 3600 ) )
			->from("users")
		->count_all_results();

		$all_referrers = $this->db
			->where('status', 1)
			->from("referals")
		->count_all_results();

		$this_day_referrers = $this->db
			->where('status', 1)
			->where('time>=', strtotime( date( 'Y-m-d 00:00:00' ) ))
			->where('time<=', strtotime( date( 'Y-m-d 23:59:59' ) ))
			->from("referals")
		->count_all_results();

		$last_hour_referrers = $this->db
			->where('status', 1)
			->where('time>=', ( strtotime('now') - 3600 ) )
			->from("referals")
		->count_all_results();

		$message = str_replace([
			'{all_voices}',
			'{this_day_voices}',
			'{last_hour_voices}',
			'{all_users}',
			'{this_day_users}',
			'{last_hour_users}',
			'{all_referrers}',
			'{this_day_referrers}',
			'{last_hour_referrers}',
		], [
			number_format($all_voices, 0, ',', ' '),
			number_format($this_day_voices, 0, ',', ' '),
			number_format($last_hour_voices, 0, ',', ' '),
			number_format($all_users, 0, ',', ' '),
			number_format($this_day_users, 0, ',', ' '),
			number_format($last_hour_users, 0, ',', ' '),
			number_format($all_referrers, 0, ',', ' '),
			number_format($this_day_referrers, 0, ',', ' '),
			number_format($last_hour_referrers, 0, ',', ' ')
		], $message);

		send_notification( setting_item('bot_token'), setting_item('channel_id'), $message );
	}

	public function notifications(){
		while (1){
			$this->send_notifications();
			sleep(1);
		}
	}

	public function send_notifications(){
		$files = glob(FCPATH . 'tmp/notifications/*.json');
		
		if ( empty( $files ) ) return;

		usort( $files, function( $a, $b ) { return filemtime($a) - filemtime($b); } );

		$files = array_filter($files, function( $x){
			if ( filemtime($x) < time() ) {
				return $x;
			}
		});

		if( !empty( $files ) ){
			foreach ($files  as $file) {
				$item = file_get_contents( $file );
				$item = json_decode( $item, TRUE );

				$chat_id = $item['chat_id'];
				$text = $item['text'];
				$token = $item['token'];
				$status = send_notification( $token, $chat_id, $text );
				echo $chat_id. ': ' . ( $status ? 'ok' : 'error' ) . PHP_EOL;
				@unlink($file);
				usleep(50000);
			}
		}
	}

}

/* End of file Crone.php */
/* Location: ./application/controllers/Crone.php */