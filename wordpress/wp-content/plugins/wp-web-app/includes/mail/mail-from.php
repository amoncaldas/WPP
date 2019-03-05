<?php

	class WppMailFrom {
		
		function __construct() {
			add_filter( 'wp_mail_from', array(&$this, 'fb_mail_from') );
			add_filter( 'wp_mail_from_name', array(&$this, 'fb_mail_from_name') );
		}
		
		// new name
		function fb_mail_from_name($name) {			
			$from_name = get_option("wpp_email_sender_name");
			if($from_name && strpos($name, $from_name) === false){
				$name = esc_attr($from_name);
			}
			return $name;			
		}
		
		// new email-adress
		function fb_mail_from($email) {
			if (!is_localhost()) {
				$sender_email = get_option("wpp_email_sender_email");
				if($sender_email && strpos($email, $sender_email) === false){
					$email = $sender_email;
				}
			}
			return $email;			
		}
	}
?>