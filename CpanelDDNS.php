<?php
/***********************************************

  "CpanelDDNS.php"

  Created by Michael Cheng on 04/16/2014 14:40
            http://michaelcheng.us/
            michael@michaelcheng.us
            --All Rights Reserved--

***********************************************/

require('lib/HttpRequest.php');

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
	 * @param  [String] $subdomain Your website's subdomain, used to connect to your VNC or whatever, e.g. "vnc"
	 * @param  [String] $domain    Your website, e.g. "example.com"
	 * @return                     Returns nothing
	 */
	public function updateDdns($subdomain, $domain) {
		if(!$this->login()) return false;

		$params = "address=" . $_SERVER['REMOTE_ADDR'];
		$params .= "&class=IN";
		$params .= "&cpanel_jsonapi_func=edit_zone_record";
		$params .= "&cpanel_jsonapi_module=ZoneEdit";
		$params .= "&cpanel_jsonapi_version=2";
		$params .= "&domain=" . $domain;
		$params .= "&line=28";
		$params .= "&name=" . $subdomain . "." . $domain . ".";
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
	 * @return [Boolean] True if the login succeeded
	 */
	private function login() {
		$url = $this->getUrl();
		$user = $this->getUser();
		$pass = $this->getPass();


		$params = "user=" . $user . "&pass=" . $pass;

		$httpRequest = new HttpRequest();
		$httpRequest
			->setUrl($url . "/login")
			->setParams($params);
		$result = $httpRequest->post();
		$inf = $httpRequest->getCurlInfo();
		

		//get the session
		if(strpos($inf['url'], "cpsess")) {
			$pattern = "/.*?(\/cpsess.*?)\/.*?/is";
			$preg_res = preg_match($pattern, $inf['url'], $cpsess);

			$this->setToken($cpsess[1]);
			return true;
		}

		return false;
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