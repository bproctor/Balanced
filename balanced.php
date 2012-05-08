<?php

require_once __DIR__ . '/config.php';

/**
 * 
 */
class Balanced {
	
	private $marketplace_uri;
	
	/**
	 * 
	 */
	private function __construct() {
		$this->marketplace_uri = '/v1/marketplaces/'.BALANCED_MARKETPLACE;
	}
	
	/**
	 *
	 * @staticvar null $instance
	 * @return \Balanced 
	 */
	public static function instance() {
		static $instance = null;
		if ($instance === null) {
			$instance = new Balanced();
		}
		return $instance;
	}
	
	/**
	 *
	 * @param type $method
	 * @param type $uri
	 * @param type $data
	 * @return type 
	 */
	private function send_request($method, $uri, $data = null) {
		$ch = curl_init('https://api.balancedpayments.com'.$uri);
		curl_setopt($ch, CURLOPT_USERPWD, BALANCED_KEY);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($method == 'POST') {
			$json = json_encode($data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',
				'Content-Length: '.strlen($json)
			));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		}
		$out = curl_exec($ch);
		curl_close($ch);
		return json_decode($out, true);
	}
	
	/**
	 *
	 * @param type $str
	 * @return type 
	 */
	private function parse_id($str) {
		return substr($str, strrpos($str, '/')+1);
	}
	
	/**
	 *
	 * @return type 
	 */
	public function create_api_key() {
		return $this->send_request('POST', '/v1/api_keys');
	}
	
	/**
	 *
	 * @return type 
	 */
	public function create_marketplace() {
		return $this->send_request('POST', '/v1/marketplaces');
	}
	
	/**
	 *
	 * @return type 
	 */
	public function get_marketplace() {
		return $this->send_request('GET', $this->marketplace_uri);
	}
	
	/**
	 * Creates a new buyer account
	 * 
	 * @param array $data
	 *		The data to create the buyer with
	 * 
	 * @return string
	 *		Returns the buyers account number
	 */
	public function create_buyer_account(array $data) {
		$out = $this->send_request('POST', $this->marketplace_uri.'/accounts', $data);
		return $this->parse_id($out['uri']);
	}

	/**
	 * Create a hold on an account
	 * 
	 * @param type $account
	 * @param type $amount
	 * @return type 
	 */
	public function create_hold($account, $amount) {
		
		// Amount must be at least $50
		if ($amount < 50) {
			return false;
		}
		$out = $this->send_request('POST', $this->marketplace_uri.'/accounts/'.$account.'/holds', array('amount' => $amount));
		return $this->parse_id($out['uri']);
	}
	
	/**
	 * 
	 */
	public function capture_hold() {
		
	}
	
	/**
	 * 
	 */
	public function void_hold() {
		
	}
	
	/**
	 * 
	 */
	public function refund_debit() {
		
	}
	
	/**
	 *
	 * @param array $data 
	 */
	public function create_merchant_account(array $data) {
		
	}
	
	/**
	 * 
	 */
	public function create_credit() {
		
	}
	
}
