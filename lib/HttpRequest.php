<?php
/***********************************************

  "HttpRequest.php"

  Created by Michael Cheng on 06/14/2015 16:43
            http://michaelcheng.us/
            michael@michaelcheng.us
            --All Rights Reserved--

***********************************************/

class HttpRequest {
	/**
	 * An HTTP Request object.
	 *
	 * GET or POST requests can be sent using ->get() or ->post()
	 */
	private $_url;
	private $_params;
	private $_headers;
	private $_curlInfo;
	private $_cookieFile;

	function __construct($url, $cookieFile = '') {
		$this->_url = $url;

		if(empty($cookieFile)) {
			$cookieFile = tempnam(sys_get_temp_dir(), 'HttpRequestPostCookie');
		}
		$this->_cookieFile = $cookieFile;
	}

	function get($params = []) {
		$url = $this->getUrl();
		$params = $this->processParams($params);
		$headers = $this->getHeaders();
		$header[0] = $headers;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_URL, $url . $params);

		$result = curl_exec($curl);
		$this->setCurlInfo(curl_getinfo($curl));

		curl_close($curl);
		return $result;
	}

	function post($params = []) {
		$url = $this->getUrl();
		$params = $this->processParams($params);
		$cookieFile = $this->_cookieFile;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, substr($params, 1)); // Remove the '?'
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 100020);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		// Both COOKIEFILE and COOKIEJAR are needed... Lots of debugging here to figure that out :/
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);

		$result = curl_exec($curl);
		$this->setCurlInfo(curl_getinfo($curl));

		curl_close($curl);
		return $result;
	}

	private function processParams($params) {
		$out = "?";
		foreach($params as $key => $value) {
			$out .= urlencode($key) . "=" . urlencode($value) . "&";
		}

		// Remove the last "&"
		$out = substr($out, 0, strlen($out)-1);

		return $out;
	}

	private function getUrl() {
		return $this->_url;
	}

	private function getHeaders() {
		return $this->_headers;
	}

	public function setHeaders($headers) {
		$this->_headers = $headers;
		return $this;
	}

	public function getCurlInfo() {
		return $this->_curlInfo;
	}

	private function setCurlInfo($curlInfo) {
		$this->_curlInfo = $curlInfo;
		return $this;
	}

	public function getCookieFile() {
		return $this->_cookieFile;
	}
}
?>