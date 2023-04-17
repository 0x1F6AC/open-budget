<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budget_model extends CI_Model {

	private $proxy = "10.8.0.2:8118";

    	public function __construct(){
		parent::__construct();
		$this->load->library('Randomagent');
	}

    	private function math_random() {
        	return (float)rand() / (float)getrandmax();
    	}

    	private function access_captcha_s($t = 10, $n = 5){
        	return intval($this->math_random() * ($t - $n) + $n);
    	}

    	private function access_captcha_r($t, $n, $e, $i, $a){
        	return base64_encode("s" . $t . "e" . $n . "k" . $e . "r" . $i . "e" . $a . "t");
    	}

    	private function access_captcha_get($t = 12) {
        	return $this->access_captcha_r($this->access_captcha_s(-3) * $t, $this->access_captcha_s(2, 19) * $t, $this->access_captcha_s(10, 5) * $t, $this->access_captcha_s(10, 4) * $t, $this->access_captcha_s(10, 220));
    	}

    	private function random_ip(){
        	$ips = [
	                '46.227.123.',
	                '37.110.212.',
	                '46.255.69.',
	                '62.209.128.',
	                '37.110.214.',
	                '31.135.209.',
	                '37.110.213.',
	                '95.47.108.',
	                '95.46.146.',
	                '93.170.168.',
	                '93.170.10.',
	                '92.253.192.'
	        ];
        	$prefix = $ips[array_rand($ips)];
        	return $prefix.rand(1, 255);
    	}

    	private function generateRandomString($length = 10) {
        	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        	$charactersLength = strlen($characters);
        	$randomString = '';
        	for ($i = 0; $i < $length; $i++) {
            		$randomString .= $characters[random_int(0, $charactersLength - 1)];
        	}
        	return $randomString;
    	}

    	private function save_captcha( $base64 ){
        	$filename = md5( $this->generateRandomString() ).'.png';
        	$this->setUserConfig('captcha', $filename);
        	file_put_contents( FCPATH.'tmp/'.$filename, base64_decode( $base64 ) );
        	return $filename;
    	}

    	private function setUserConfig($key='', $value='') {
        	$file = APPPATH.'cache/cookie_'.$this->user->chat_id.'_'.$this->bot->chatid.'.json';
        	if (file_exists( $file )) {
            		$user_data = file_get_contents( $file );
            		$user_data = json_decode( $user_data, TRUE );
        	}else{
            		$user_data = [];
        	}
        	$user_data[$key] = $value; 
        	file_put_contents( $file, json_encode( $user_data ) );
        	return TRUE;
    	}

    	private function getUserConfig($key='') {
        	$file = APPPATH.'cache/cookie_'.$this->user->chat_id.'_'.$this->bot->chatid.'.json';
        	if (file_exists( $file )) {
	            $user_data = file_get_contents( $file );
	            $user_data = json_decode( $user_data, TRUE );
	        }else{
	            $user_data = [];
	        }

	        if (array_key_exists($key, $user_data)) {
	            return $user_data[$key];
	        }

	        return FALSE;
	}

	public function removeUserConfig(){
		@unlink( FCPATH.'tmp/'.$this->getUserConfig('captcha') );
	        @unlink( APPPATH.'cache/cookie_'.$this->user->chat_id.'_'.$this->bot->chatid.'.json' );
	}

    	private function saveCookies($headers=''){
        	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
	        $stored_cookies = $this->getUserConfig('cookies');
	        if ( !is_array($stored_cookies) ) $stored_cookies = [];
	        
	        foreach($matches[1] as $item) {
	            $cookie = explode('=', $item);
	            $stored_cookies[ $cookie[0] ] = $cookie[1];
	        }
	        $this->setUserConfig('cookies', $stored_cookies);
	}

	public function getCookies(){
	        $cookies = $this->getUserConfig('cookies');
	        
	        $cookie_string="";

	        if (!empty( $cookies )) {
	            foreach ($cookies as $key => $value) {
	                $cookie_string .= "$key=$value;";
	            }
	        }

	        return $cookie_string;
	}

	public function getXXsrfToken(){
		$cookies = $this->getUserConfig('cookies');
		if (!empty( $cookies )) {
			foreach ($cookies as $key => $value) {
				if ( $key == 'XSRF-TOKEN' ) {
					return $value;
				}
			}
		}

		return FALSE;
	}

    	public function vote_format_phone( $number ){
        	return preg_replace( '/(90|91|93|94|95|97|98|99|50|88|77|33)([0-9]{3})([0-9]{2})([0-9]{2})/', "$1-$2-$3-**", preg_replace('/\D/', "", $number));
    	}

    	public function checkVote( $phoneNumber ){
        	$voices = $this->getVoices();
        	$phone = $this->vote_format_phone( $phoneNumber );
        	if ( !empty( $voices['content'] ) ) {
            		foreach ($voices['content'] as $row) {
                		if ( $row['phoneNumber'] ==  $phone) {
                    			return TRUE;
                		}
            		}
        	}

        	return FALSE;
    	}

    	public function get_cookie($count = FALSE){
	        if ( $count ) {
	            $ip  = $this->getUserConfig('ip');
	            $agent  = $this->getUserConfig('agent');
	        }else{
	            $ip  = $this->random_ip();
	            $agent  = $this->randomagent->generate();
	        
	            $this->setUserConfig('ip', $ip);
	            $this->setUserConfig('agent', $agent);
	        }

	        $ch = curl_init();
	        $url = 'https://openbudget.uz/api/v2/info/initiative/' . ($count ? 'count/' : '');
	        curl_setopt($ch, CURLOPT_URL, $url.get_board_data($this->bot->board, 'hash'));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	        curl_setopt($ch, CURLOPT_HEADER, 1);
	        
	        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	        $headers = array();
	        $headers[] = 'Authority: openbudget.uz';
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,uz;q=0.7,tg;q=0.6,tr;q=0.5';
	        $headers[] = 'Cache-Control: no-cache';
	        $headers[] = 'Hl: en';
	        $headers[] = 'Pragma: no-cache';
	        $headers[] = 'Referer: '.$this->bot->board;
	        
	        $headers[] = 'Sec-Fetch-Dest: empty';
	        $headers[] = 'Sec-Fetch-Mode: cors';
	        $headers[] = 'Sec-Fetch-Site: same-origin';
	        $headers[] = 'Cookie: '.$this->getCookies();
	        $headers[] = 'User-Agent: ' . $agent;
	        $headers[] = "X-Real-Ip: " . $ip;
	        $headers[] = "REMOTE_ADDR: " . $ip;
	        $headers[] = "HTTP_X_FORWARDED_FOR: " . $ip;
	        $headers[] = "HTTP_X_REAL_IP: " . $ip;
	        $headers[] = "X-Forwarded-For: " . $ip;

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $response = curl_exec($ch);
	        
	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        $header = substr($response, 0, $header_size);
	        $body = substr($response, $header_size);
	        $this->saveCookies( $header );

	        if (curl_errno($ch)) {
    			return FALSE;
		}
	        curl_close($ch);

	        if ( !$count ) {
	        	usleep(50000);
	            $this->get_cookie(TRUE);
	        }
	        
	        return json_decode( $body, TRUE );
    	}

    	public function getCaptcha(){

    		$cookie = $this->get_cookie();

    		/*if ( !$cookie ) {
    			return FALSE;
    		}*/

    		$ip  = $this->getUserConfig('ip');
	        $agent  = $this->getUserConfig('agent');

	        $ch = curl_init();

	        curl_setopt($ch, CURLOPT_URL, 'https://openbudget.uz/api/v2/vote/captcha-2');
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	        curl_setopt($ch, CURLOPT_HEADER, 1);

	        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	        $headers = array();
	        $headers[] = 'Authority: openbudget.uz';
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,uz;q=0.7,tg;q=0.6,tr;q=0.5';
	        $headers[] = 'Access-Captcha: ' . $this->access_captcha_get();
	        $headers[] = 'Cache-Control: no-cache';
	        $headers[] = 'Hl: uz_cyr';
	        $headers[] = 'Pragma: no-cache';
	        $headers[] = 'Referer: '.$this->bot->board;
	        
	        $headers[] = 'Sec-Fetch-Dest: empty';
	        $headers[] = 'Sec-Fetch-Mode: cors';
	        $headers[] = 'Sec-Fetch-Site: same-origin';

	        $headers[] = 'Cookie: '.$this->getCookies();
	        $headers[] = 'User-Agent: ' . $agent;
	        $headers[] = "REMOTE_ADDR: " . $ip;
	        $headers[] = "HTTP_X_FORWARDED_FOR: " . $ip;
	        $headers[] = "HTTP_X_REAL_IP: " . $ip;
	        $headers[] = "X-Forwarded-For: " . $ip;
	        
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $response = curl_exec($ch);
	        if (curl_errno($ch)) {
    			return FALSE;
		}
	        curl_close($ch);

	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        $header = substr($response, 0, $header_size);
	        $body = substr($response, $header_size);
	        $this->saveCookies( $header );

	        $res = json_decode($body, TRUE);
	        
	        if ( empty( $res['captchaKey'] ) ) {
	            return FALSE;
	        }

	        $r = base64_encode( strrev( $res['captchaKey'] ) );

	        $res['image'] = str_replace($r, "", $res['image']);
	        $image = $this->save_captcha( $res['image'] );
	        return [
	            'image' => $image,
	            'key' => $res['captchaKey'],
	        ];
    	}

    	public function vote_check($captchaKey, $captchaResult, $phoneNumber){
	        $ip  = $this->getUserConfig('ip');
	        $agent  = $this->getUserConfig('agent');

	        $ch = curl_init();

	        curl_setopt($ch, CURLOPT_URL, 'https://openbudget.uz/api/v2/vote/check');
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HEADER, 1);

	        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
	            'boardId' => get_board_data( $this->bot->board, 'id' ),
	            'captchaKey' => $captchaKey,
	            'captchaResult' => $captchaResult,
	            'phoneNumber' => '998'.$phoneNumber,
	        ]));
	        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	        $headers = array();
	        $headers[] = 'Authority: openbudget.uz';
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,uz;q=0.7';
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'Hl: en';
	        $headers[] = 'Origin: https://openbudget.uz';
	        $headers[] = 'Referer: '.$this->bot->board;
	        $headers[] = 'Sec-Fetch-Dest: empty';
	        $headers[] = 'Sec-Fetch-Mode: cors';
	        $headers[] = 'Sec-Fetch-Site: same-origin';
	        
	        $headers[] = 'Cookie: '.$this->getCookies();
	        $headers[] = 'User-Agent: ' . $agent;
	        $headers[] = "REMOTE_ADDR: " . $ip;
	        $headers[] = "HTTP_X_FORWARDED_FOR: " . $ip;
	        $headers[] = "HTTP_X_REAL_IP: " . $ip;
	        $headers[] = "X-Forwarded-For: " . $ip;

	        $xxsrftoken = $this->getXXsrfToken();
	        if ( $xxsrftoken ) {
	        	$headers[] = "x-xsrf-token: " . $xxsrftoken;
	        }

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $response = curl_exec($ch);
	        if (curl_errno($ch)) {
	            return FALSE;
	        }
	        curl_close($ch);

	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        $header = substr($response, 0, $header_size);
	        $body = substr($response, $header_size);
	        $this->saveCookies( $header );

	        $this->setUserConfig('phone', $phoneNumber);
    		
	        return json_decode($body, TRUE);
    	}

    	public function resend_sms($otpKey){

	        $ip  = $this->getUserConfig('ip');
	        $agent  = $this->getUserConfig('agent');

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, 'https://openbudget.uz/api/v2/vote/resend-sms');
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HEADER, 1);
	        
	        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        curl_setopt($ch, CURLOPT_POST, 1);
	        
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
	            'otpKey' => $otpKey
	        ]));

	        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	        $headers = array();

	        $headers[] = 'Authority: openbudget.uz';
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,uz;q=0.7';
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'Hl: en';
	        $headers[] = 'Origin: https://openbudget.uz';
	        $headers[] = 'Referer: '.$this->bot->board;
	        $headers[] = 'Sec-Fetch-Dest: empty';
	        $headers[] = 'Sec-Fetch-Mode: cors';
	        $headers[] = 'Sec-Fetch-Site: same-origin';
	        
	        $headers[] = 'Cookie: '.$this->getCookies();
	        $headers[] = 'User-Agent: ' . $agent;
	        $headers[] = "REMOTE_ADDR: " . $ip;
	        $headers[] = "HTTP_X_FORWARDED_FOR: " . $ip;
	        $headers[] = "HTTP_X_REAL_IP: " . $ip;
	        $headers[] = "X-Forwarded-For: " . $ip;

	        $xxsrftoken = $this->getXXsrfToken();
	        if ( $xxsrftoken ) {
	        	$headers[] = "x-xsrf-token: " . $xxsrftoken;
	        }

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
	        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        $response = curl_exec($ch);
	        
	        if (curl_errno($ch)) {
	            return FALSE;
	        }

	        curl_close($ch);

	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        $header = substr($response, 0, $header_size);
	        $body = substr($response, $header_size);

	        $this->saveCookies( $header );

	        return json_decode($body, TRUE);
    	}

    	public function getVoices(){

	        $ip  = $this->getUserConfig('ip');
	        $agent  = $this->getUserConfig('agent');

	        $ch = curl_init();

	        curl_setopt($ch, CURLOPT_URL, 'https://openbudget.uz/api/v2/info/votes/'.get_board_data($this->bot->board, 'hash').'/?size=10&page=0');
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HEADER, 1);

	        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	        $headers = array();
	        $headers[] = 'Authority: openbudget.uz';
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,uz;q=0.7';
	        $headers[] = 'Hl: en';
	        $headers[] = 'Referer: '.$this->bot->board;
	        $headers[] = 'Sec-Fetch-Dest: empty';
	        $headers[] = 'Sec-Fetch-Mode: cors';
	        $headers[] = 'Sec-Fetch-Site: same-origin';
	        
	        $headers[] = 'Cookie: '.$this->getCookies();
	        $headers[] = 'User-Agent: ' . $agent;
	        $headers[] = "REMOTE_ADDR: " . $ip;
	        $headers[] = "HTTP_X_FORWARDED_FOR: " . $ip;
	        $headers[] = "HTTP_X_REAL_IP: " . $ip;
	        $headers[] = "X-Forwarded-For: " . $ip;

	        $xxsrftoken = $this->getXXsrfToken();
	        if ( $xxsrftoken ) {
	        	$headers[] = "x-xsrf-token: " . $xxsrftoken;
	        }

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $response = curl_exec($ch);
	        if (curl_errno($ch)) {
	            return FALSE;
	        }
	        curl_close($ch);

	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        $header = substr($response, 0, $header_size);
	        $body = substr($response, $header_size);
	        $this->saveCookies( $header );

	        return json_decode($body, TRUE);
    	}

    	public function getRecaptcha(){
    		$ctx = stream_context_create(array('http'=>
			    array(
			        'timeout' => 3,  //1200 Seconds is 20 Minutes
			    )
			));

    		return @file_get_contents('http://127.0.0.1:123', false, $ctx);
    	}

    	public function vote_verify($otpCode, $otpKey){
	        $ip  = $this->getUserConfig('ip');
	        $agent  = $this->getUserConfig('agent');
	        $reCaptchaResponse = $this->getRecaptcha();
	        log_message('ERROR', $reCaptchaResponse);
	        if ( empty( $reCaptchaResponse ) ) {
	        	$this->removeUserConfig();
	            return "not_accepted";
	        }

	        $reCaptchaResponse  =str_replace('new', '', $reCaptchaResponse);
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, 'https://openbudget.uz/api/v2/vote/verify-2');
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HEADER, 1);
	        
	        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	        curl_setopt($ch, CURLOPT_POST, 1);
	        
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
	            'otpKey' => $otpKey,
	            'otpCode' => $otpCode,
	            'reCaptchaResponse' => $reCaptchaResponse,
	            'initiativeId' => get_board_data( $this->bot->board, 'hash' ),
	            'subinitiativesId' => [],
	        ]));

	        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	        $headers = array();
	        $headers[] = 'Authority: openbudget.uz';
		$headers[] = 'Accept: application/json';
		$headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,uz;q=0.7';
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Cookie: '.$this->getCookies();
		$headers[] = 'Hl: uz_cyr';
		$headers[] = 'Origin: https://openbudget.uz';
		$headers[] = 'Referer: '.$this->bot->board;
		$headers[] = 'User-Agent: ' . $agent;
	        $headers[] = "REMOTE_ADDR: " . $ip;
	        $headers[] = "HTTP_X_FORWARDED_FOR: " . $ip;
	        $headers[] = "HTTP_X_REAL_IP: " . $ip;
	        $headers[] = "X-Forwarded-For: " . $ip;

	        $xxsrftoken = $this->getXXsrfToken();
	        if ( $xxsrftoken ) {
	        	$headers[] = "x-xsrf-token: " . $xxsrftoken;
	        }


	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $response = curl_exec($ch);
	        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        if (curl_errno($ch)) {
	            return FALSE;
	        }
	        curl_close($ch);

	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        $header = substr($response, 0, $header_size);
	        $body = substr($response, $header_size);
	        $this->saveCookies( $header );
	        log_message('ERROR', $response);
	        if ( $http_status == "200" ) {
	            usleep(50000);
	            if ( $this->checkVote( $this->getUserConfig('phone') ) ) {
	                $this->removeUserConfig();
	                return "success";
	            }else{
	            	$this->removeUserConfig();
	            	return "not_accepted";
	            }
	        }

	        return json_decode($body, TRUE);
    	}
}