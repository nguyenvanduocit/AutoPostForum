<?php


class curlClass {
	 private $ch;
	 private $url;
	 private $timeout;	 
	 private $debug;
	 private static $instance;
	 private $agents = array(
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
			'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.9) Gecko/20100508 SeaMonkey/2.0.4',
			'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
			'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1'
		 
		);
	 protected function __construct($debug = FALSE)
	 {
		 // Initialize cURL handle
		 $this->ch = curl_init();
		 // Default mode
		 $this->debug = $debug;
		 // Set error in case http return code bigger than 400
		 curl_setopt($this->ch, CURLOPT_FAILONERROR, TRUE);
		 // Allow redirects
		 curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);
		 // Return into a variable rather than displaying it
		 curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		 // Default connection timeout
		 curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);
		 //IP
		 //curl_setopt( $this->ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));

	 }
	 
	 public function __destruct() 
	 {
	 	curl_close($this->ch);
	 }

	 public static function getInstance($debug = FALSE) 
	 {
		 if (self::$instance === NULL) 
		 {
		 self::$instance = new curlClass($debug);
		 }

		 return self::$instance;
	 }

	 
	 public function setCookie($cookie) 
	 {
	 	curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie);
	 }

	 
	 public function setURL($url) 
	 {
	 	curl_setopt($this->ch, CURLOPT_URL, $url);
	 	$this->url = $url;
	 }

	 
	 public function setTimeout($timeout) 
	 {
	 	curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
	 	$this->timeout = $timeout;
	 }
	 public function sendGet($url = NULL, $timeout = 10)
	 {
	 	return $this->sendPost(null, $url, $timeout);
	 }
	 public function sendPost(array $postData=null, $url = NULL, $timeout = 10) 
	 {
		 // Validate URL
		 if ($url === NULL) {
			 if ($this->url === NULL) {
				 // Debug mode
				 if ($this->debug) {
				 	echo 'Error: URL is null.';
				 }
				 return FALSE;
			 }
			 $url = $this->url;
		 }

		 // Set url to post to
		 curl_setopt($this->ch, CURLOPT_URL, $url);

		 // Set connection timeout
		 curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);

		 // Set post array
		 if($postData!=null)
		 {
		 			 // Set method to post
		 	curl_setopt($this->ch, CURLOPT_POST, TRUE);
		 	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
		 }

		 // Execute request
		 $result["content"] = curl_exec($this->ch);
		if (curl_errno($this->ch)) {
			 // Debug mode
			 if ($this->debug) {
				 echo 'Error number: ' . curl_errno($this->ch);
				 echo 'Error message: ' . curl_error($this->ch);
			 }
			 return FALSE;
		 }
		 $result["redirectUrl"] = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
		 return $result;
	 }

	 
	 public function fetchURL($url = NULL, $timeout = 10) {
		 // Validate URL
		 if ($url === NULL) {
			 if ($this->url === NULL) {
				 // Debug mode
				 if ($this->debug) {
					 echo 'Error: URL is null.';
				 }
				 return FALSE;
			 }
			 $url = $this->url;
		 }

		 // Set url to fetch data from
		 curl_setopt($this->ch, CURLOPT_URL, $url);

		 // Set connection timeout
		 curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);

		 // Set method to get
		 curl_setopt($this->ch, CURLOPT_HTTPGET, TRUE);

		 // Execute request
		 $result = curl_exec($this->ch);

		 if (curl_errno($this->ch)) {
			 // Debug mode
			 if ($this->debug) {
				 echo 'Error number: ' . curl_errno($this->ch);
				 echo 'Error message: ' . curl_error($this->ch);
			 }
			 return FALSE;
		 }

		 return $result;
	}

}

?>