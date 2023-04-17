<?php

function get_board_data( $link, $key = "" ){
	preg_match('/https:\/\/openbudget\.uz\/(.*?)\/boards-list\/(.*?)\/(.*)/', $link, $matches);

	if ( !empty( $matches ) ) {
		switch ( $key ) {
			case 'lang':
				return $matches[1];
			break;

			case 'id':
				return $matches[2];
			break;

			case 'hash':
				return $matches[3];
			break;
			
			default:
				return $matches[1];
			break;
		}
	}

	return FALSE;
}

function setting_item($key=''){
        $CI =& get_instance();
        if ( ! $item = $CI->cache->file->get( 'settings_'.$key ) ){
                $item = $CI->db->get_where('settings', array('key' => $key));
                
                if ($item->num_rows() > 0) {
					$item = $item->row()->value;
                }else{
                    $item = "";
                }

                $CI->cache->file->save('settings_'.$key, $item, 3600);
        }

        return $item;
}

function bot_message($key, $bot_id, $replacement=[]){
	$CI	=&	get_instance();

	if ( ! $item = $CI->cache->file->get( 'bot_message_' . $bot_id . '_' .$key ) ){
		$item =	$CI->db->get_where('bot_messages', [
			'key' => $key,
			'bot_id' => $bot_id
		]);
		
		if ($item->num_rows() > 0) {
			$item = $item->row()->message;
		}else{
			$item = setting_item( $key ); 
		}

		$CI->cache->file->save('bot_message_' . $bot_id . '_' .$key, $item, 3600);
	}
	

	if ( ! empty( $replacement ) ) foreach ($replacement as $k => $v) $item = str_replace( '{'. $k .'}', $v, $item);

	return $item;
}

function remake_board_url( $url ){
	if ( preg_match('/https:\/\/openbudget\.uz\/boards-list\/(.*?)\/(.*)/', $url, $matches) ) {
		$url = "https://openbudget.uz/en/boards-list/{$matches['1']}/{$matches['2']}";
	}

	return $url;
}

function get_bot_data( $token='' ){
	$url = 'https://api.telegram.org/bot'.$token.'/getme';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	$res = json_decode($data, TRUE);

	if ( $res['ok'] ) {
		return [
			'id' => $res['result']['id'],
			'username' => $res['result']['username'],
		];
	}

	return FALSE;
}

function remove_bot_hook( $token='' ){
	$url = 'https://api.telegram.org/bot'.$token.'/deleteWebhook?drop_pending_updates=true';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	$res = json_decode($data, TRUE);

	if ( $res['ok'] ) {
		return TRUE;
	}

	return FALSE;
}

function reload_status($token, $id){
	$hook = remove_bot_hook( $token );
	if ( !$hook ) return FALSE;

	$url = 'https://api.telegram.org/bot'.$token.'/setWebhook?url='.base_url('hook?id='.$id).'&max_connections=100&drop_pending_updates=true';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	$res = json_decode($data, TRUE);

	if ( $res['ok'] ) {
		return TRUE;
	}

	return FALSE;
}

function send_payment_message($token, $chat_id){
	$url = 'https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$chat_id.'&text=✅ Siz yuborgan so\'rovga asosan to\'lov amalga oshirildi!';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	$res = json_decode($data, TRUE);

	if ( $res['ok'] ) {
		return TRUE;
	}

	return FALSE;
}

function format_phone($number, $superadmin = TRUE){
	if ($superadmin) {
		$format = '+$1 ($2) $3-$4-$5';
	}else{
		$format = '+$1 ($2) $3-$4-**';
	}
	return preg_replace( '/^(998)(90|91|93|94|95|97|98|99|50|88|77|33)([0-9]{3})([0-9]{2})([0-9]{2})$/', $format, $number);
}

function folder_exist( $folder ) {
    $path = realpath($folder);

    if($path !== false AND is_dir($path)){
        return $path;
    }

    return false;
}

function send_notification($token='', $chat_id='', $message=''){
	$url = 'https://api.telegram.org/bot'.$token.'/sendMessage';
	$params=[
      'chat_id' => $chat_id, 
      'text'=> $message,
	'parse_mode' => 'Markdown',
      'disable_web_page_preview' => 'true'
  	];
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_POST, 1);
  	curl_setopt($curl, CURLOPT_POSTFIELDS, ($params));
	$data = curl_exec($curl);
	curl_close($curl);

	$res = json_decode($data, TRUE);

	if ( $res['ok'] ) {
		return TRUE;
	}

	return FALSE;
}

function create_notification($message,  $user, $bot_data, $filetime) {
	$data = [
		'text' => $message,
		'chat_id' => $user['chat_id'],
		'bot_id' => $bot_data['bot_id'],
		'token' => $bot_data['token'],
		'bot_username' => $bot_data['username'],
		'username' => $user['username'],
		'firstname' => $user['firstname'],
		'lastname' => $user['lastname'],
		'time' => time()
	];
	$filename = FCPATH . 'tmp/notifications/m_' . $bot_data['bot_id'] . '_' . $user['chat_id'] . '.json';
	file_put_contents( $filename , json_encode( $data ));
	touch($filename, $filetime);
}

function check_notifications( $bot_id ) {
	if ( !empty( glob( FCPATH . "tmp/notifications/m_{$bot_id}_*.json") ) ) {
		return TRUE;
	}

	return FALSE;
}

function get_notifications( $bot_id = '' ){
	$CI =& get_instance();
	
	if ( folder_exist( FCPATH . 'tmp/notifications/' ) ) {
		if ( is_numeric( $bot_id )  ) {
			if( $CI->session->userdata('user_level') == '1' ){
				return glob( FCPATH . 'tmp/notifications/m_'.$bot_id.'_*.json');
			}

			$user_bots = explode(',', $CI->session->userdata('user_bots'));
			if ( in_array( $bot_id , $user_bots) ) {
				return glob( FCPATH . "tmp/notifications/m_{$bot_id}_*.json");
			}else{
				return [];
			}
			
		}

		if( $CI->session->userdata('user_level') == '1' ){
			return glob( FCPATH . 'tmp/notifications/m_*.json');
		}else{
			if ( !empty( $CI->session->userdata('user_bots') ) ) {
				return glob( FCPATH . 'tmp/notifications/m_{'.$CI->session->userdata('user_bots').'}_*.json', GLOB_BRACE);
			}
		}
	}

	return [];
}

function clear_notifications($bot_id=''){
	$notifications = glob( FCPATH . 'tmp/notifications/m_'.$bot_id.'_*.json');
	if (!empty( $notifications )) {
		foreach ($notifications as $file) {
			@unlink( $file );
		}
	}
}

function search_from_array( $arr=[], $query='' ){
	$results = [];

    foreach( $arr as $item ){
        if( is_array( $item ) ){
			if( array_filter($item, function($var) use ($query) { return ( !is_array( $var ) )? stristr( $var, $query ): false; } ) ){
                $results[] = $item;
                continue;
            }
        }
    }

    return $results;
}
