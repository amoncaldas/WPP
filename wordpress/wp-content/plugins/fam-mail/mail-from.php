<?php

	class wp_mail_from {
		
		function __construct() {
			add_filter( 'wp_mail_from', array(&$this, 'fb_mail_from') );
			add_filter( 'wp_mail_from_name', array(&$this, 'fb_mail_from_name') );
		}
		
		// new name
		function fb_mail_from_name($name) {
			if( strpos($name, "Fazendo as Malas") === false){
				$name = 'Fazendo as Malas';		
				$name = esc_attr($name);
			}
			return $name;
		}
		
		// new email-adress
		function fb_mail_from($email) {
			if( strpos($email, "@fazendoasmalas.com") === false){
				$email = 'contato@fazendoasmalas.com';
			}
			return $email;
		}
	}
?>