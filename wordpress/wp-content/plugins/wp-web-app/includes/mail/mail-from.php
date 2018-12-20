<?php

	class WppMailFrom {
		
		function __construct() {
			add_filter( 'wp_mail_from', array(&$this, 'fb_mail_from') );
			add_filter( 'wp_mail_from_name', array(&$this, 'fb_mail_from_name') );
		}
		
		// new name
		function fb_mail_from_name($name) {
			$fromName = get_option("email_sender_name");
			if($fromName && strpos($name, $fromName) === false){
				$name = esc_attr($fromName);
			}
			return $name;
		}
		
		// new email-adress
		function fb_mail_from($email) {
			$senderEmail = get_option("email_sender_email");
			if($senderEmail && strpos($email, $senderEmail) === false){
				$email = $senderEmail;
			}
			return $email;
		}
	}
?>