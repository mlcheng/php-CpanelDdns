<?php
/***********************************************

  "ddns.php"

  Created by Michael Cheng on 04/16/2014 14:40
            http://michaelcheng.us/
            michael@michaelcheng.us
            --All Rights Reserved--

***********************************************/

/**
 * An HTTP Request object.
 *
 * GET or POST requests can be sent using ->get() or ->post()
 */
class HttpRequest {
	private $_url;
	private $_params;
	private $_headers;

	function get() {
		$url = $this->getUrl();
		$params = $this->getParams();
		$headers = $this->getHeaders();

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		
		$header[0] = $headers;

		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_URL, $url . $params);

		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

	function post() {
		$url = $this->getUrl();
		$params = $this->getParams();

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

	function getUrl() {
		return $this->_url;
	}
	function setUrl($url) {
		$this->_url = $url;
		return $this;
	}

	function getParams() {
		return $this->_params;
	}
	function setParams($params) {
		$this->_params = $params;
		return $this;
	}

	function getHeaders() {
		return $this->_headers;
	}
	function setHeaders($headers) {
		$this->_headers = $headers;
		return $this;
	}
}


/**
 * Cpanel object. For now, the only public facing method is to update the DDNS
 */
class Cpanel {
	private $_url;
	private $_user;
	private $_pass;
	private $_token;

	/**
	 * Update DDNS on your domain. This method requires a login, therefore be sure to provide the URL, username, and password before calling
	 * @param  [String] $domain    Your website
	 * @param  [String] $subdomain Your website's subdomain, used to connect to your VNC or whatever
	 * @return                     Returns nothing
	 */
	public function updateDdns($domain, $subdomain) {
		$this->login();

		$params = "address=" . $_SERVER['REMOTE_ADDR'];
		$params .= "&class=IN";
		$params .= "&cpanel_jsonapi_func=edit_zone_record";
		$params .= "&cpanel_jsonapi_module=ZoneEdit";
		$params .= "&cpanel_jsonapi_version=2";
		$params .= "&domain=" . $domain;
		$params .= "&line=28";
		$params .= "&name=" . $subdomain . ".";
		$params .= "&ttl=1200";
		$params .= "&type=A";


		$httpRequest = new HttpRequest();
		$httpRequest
			->setUrl($this->getUrl() . $this->getToken() . "/json-api/cpanel?")
			->setParams($params)
			->setHeaders("Authorization: Basic " . base64_encode($this->getUser() . ":" . $this->getPass()) . "\n\r");
		$httpRequest->get();
	}


	/**
	 * Login to your cpanel.
	 * @return [Boolean] Currently returns the $result of the login.
	 * TODO: return true if login was successful, otherwise false :D
	 */
	private function login() {
		$url = $this->getUrl();
		$user = $this->getUser();
		$pass = $this->getPass();


		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, "user=" . $user . "&pass=" . $pass);
		curl_setopt($curl, CURLOPT_TIMEOUT, 100020);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		$inf = curl_getinfo($curl);
		curl_close($curl);


		//get the session
		if($result == true && strpos($inf['url'], "cpsess")) {
			$pattern = "/.*?(\/cpsess.*?)\/.*?/is";
			$preg_res = preg_match($pattern, $inf['url'], $cpsess);

			$this->setToken($cpsess[1]);
		}

		return $result;
	}

	function getUrl() {
		return $this->_url;
	}
	function setUrl($url) {
		$this->_url = $url;
		return $this;
	}

	function getUser() {
		return $this->_user;
	}
	function setUser($user) {
		$this->_user = $user;
		return $this;
	}

	function getPass() {
		return $this->_pass;
	}
	function setPass($pass) {
		$this->_pass = $pass;
		return $this;
	}

	function getToken() {
		return $this->_token;
	}
	function setToken($token) {
		$this->_token = $token;
		return $this;
	}

}

?>