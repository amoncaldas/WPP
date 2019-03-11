<?php

/**
	 * Get the request ip address
	 *
	 * @return String
	 */
	function get_request_ip () {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	 * Check if th e script is running on localhost
	 *
	 * @return boolean
	 */
	function is_localhost() {
		$localhosts = array('127.0.0.1', "::1", "localhost");
		$url_parts = explode("//", network_home_url());
		$current_host = $url_parts[1];

		$is_localhost = false;

		foreach ($localhosts as $localhost) {
			if (strpos($current_host, $localhost) > -1) {
				$is_localhost = true;
				break;
			}
		}
		return $is_localhost;
	}

		/**
	 * Set the email content type to be html
	 *
	 * @return string
	 */
	function set_email_html_content_type() {
		return "text/html";
	}

	/**
	 * Get sub content of a string
	 *
	 * @param String $content
	 * @param Integer $length
	 * @param boolean $forcePrecision
	 * @return String
	 */
	function get_sub_content($content, $length, $forcePrecision = false) {
		$length = $length-3;
		$content = strip_tags($content, '');
		if(strlen($content) > $length) {		
			if($forcePrecision == true)	{			
				$content = trim($content);					
				if(strpos($content, " ") > 0 ) {		
					$content = substr($content, 0,$length + 200);						
					$offset = strlen($content);	
					$spacePosition = 	strlen($content);	
					while( ($pos = strrpos(($content)," ",$offset)) != false) {
						$offset = $pos;
						if($pos < $length) {						
							$spacePosition = $pos;							
							break;
						}
					}		
														
					$content = substr($content, 0, $spacePosition)."...";
					
					if(strlen($content) > $length + 3) {
						$content = substr($content, 0, $length)."...";
					}				
				}
				else {																		
					$content = substr($content, 0,$length)."...";
				}
			}
			else {
				if(strlen($content) > $length) {			
					if(strpos($content, " ", $length) > $length) {
						$content =  substr($content, 0, strpos($content, " ", $length))."...";
					}
					else {
						$length = ($length > 20)? ($length -20): 0;								
						$content =  substr($content, 0, strpos($content, " ", $length))."...";
					}				
				}
			}	
			$content = rtrim($content, ',');
			$content = rtrim($content, '-');		
			return $content;
		}
		else {
			return $content;
		}			
	}

	/**
   * Get the google recaptcha secret
   *
   * @var string
   */
  function get_recaptcha_secret (){
		$secret = get_option("recaptcha_secret", "6LcOa2MUAAAAAMjC-Nqnxcs1u4mX62PSrXeixDeI");
		return $secret;
	}

	/**
   * Validates the temp captcha code
   *
   * @param string $recaptchaToken
   * @return boolean
   */
  function validate_captcha($recaptchaToken) {
		$recaptchaValidationUrl = "https://www.google.com/recaptcha/api/siteverify";

		$secret = get_recaptcha_secret();
		$post_data = http_build_query(
				array(
						'secret' => $secret,
						'response' => $recaptchaToken,
						'remoteip' => $_SERVER['REMOTE_ADDR']
				)
		);
		$opts = array('http' =>
				array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $post_data
				)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents($recaptchaValidationUrl, false, $context);
		$result = json_decode($response);
		return $result->success;		
	}