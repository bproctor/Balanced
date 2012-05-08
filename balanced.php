<?php

class Balanced {
	
	private function __construct() {
		Config::load('balanced', true);
	}
	
	public static function instance() {
		static $instance = null;
		if ($instance === null) {
			$instance = new Balanced();
		}
		return $instance;
	}
	
	private function send_request($method, $url, $data = null) {
		$ch = curl_init($url);
		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "");
		}
		curl_setopt($ch, CURLOPT_USERPWD, Config::get('balanced.key'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		return json_decode($data, true);
	}
	
	public function create_api_key() {
		return $this->send_request('POST', 'https://api.balancedpayments.com/v1/api_keys');
	}
	
	public function create_marketplace() {
		return $this->send_request('POST', 'https://api.balancedpayments.com/v1/marketplaces');
		//return $this->send_request('https://api.balancedpayments.com'.Config::get('balanced.api_uri'));
	}
	
	public function get_marketplace() {
		return $this->send_request('GET', 'https://api.balancedpayments.com/v1/marketplaces/'.Config::get('balanced.marketplace'));
	}
	
	public function create_buyer_account() {
		
	}
	
}
