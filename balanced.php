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
	 * Get an instance
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
	 * Send the request
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
	 * Get a particular account
	 *
	 * @param string $id
	 * @return type
	 */
	public function get_account($id) {
		return $this->send_request('GET', $this->marketplace_uri.'/accounts/'.$id);
	}

	/**
	 * Get a set of accounts
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return type
	 */
	public function get_accounts($limit = 10, $offset = 0) {
		return $this->send_request('GET', $this->marketplace_uri.'/accounts?limit='.$limit.'&offset='.$offset);
	}

	/**
	 * Create a hold on an account
	 *
	 * @param string $id
	 * @param type $amount
	 * @return type
	 */
	public function create_hold($id, $amount) {

		// Amount must be at least $50
		if ($amount < 50) {
			return false;
		}
		$out = $this->send_request('POST', $this->marketplace_uri.'/accounts/'.$id.'/holds', array('amount' => $amount));
		return $this->parse_id($out['uri']);
	}

	/**
	 *
	 */
	public function get_holds($id) { return $this->send_request('GET', $this->marketplace_uri.'/accounts/'.$id.'/holds'); }
	public function get_debits($id) { return $this->send_request('GET', $this->marketplace_uri.'/accounts/'.$id.'/debits'); }
	public function get_refunds($id) { return $this->send_request('GET', $this->marketplace_uri.'/accounts/'.$id.'/refunds'); }
	public function get_credits($id) { return $this->send_request('GET', $this->marketplace_uri.'/accounts/'.$id.'/credits'); }
	public function get_transactions($id) { return $this->send_request('GET', $this->marketplace_uri.'/accounts/'.$id.'/transactions'); }

	/**
	 *
	 * @param type $id
	 * @param type $hold_id
	 * @return type
	 */
	public function void_hold($id, $hold_id) {
		return $this->send_request('PUT', $this->marketplace_uri.'/accounts/'.$id.'/holds/'.$hold_id, array('is_void' => true));
	}

	/**
	 *
	 */
	public function refund_debit($id, $debit_id) {
		return $this->send_request('POST', $this->marketplace_uri.'/accounts/'.$id.'/debits/'.$debit_id.'/refunds');
	}

	/**
	 *
	 * @param array $data
	 */
	public function create_merchant_account(array $data) {

	}

	/**
	 *
	 * @param type $id
	 * @param type $amount
	 * @return type
	 */
	public function create_credit($id, $amount) {
		return $this->send_request('POST', $this->marketplace_uri.'/accounts/'.$id.'/credits', array('amount' => $amount));
	}

}
