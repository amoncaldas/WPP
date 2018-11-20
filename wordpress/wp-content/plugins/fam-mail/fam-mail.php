<?php
/**
 * @package FAM Mail From
 * @author Amon Caldas
 * @version 0.1
 */
 
/*
Plugin Name: FAM Mail From
Plugin URI: http://fazendoasmalas.com/
Description: Change the default address that WordPress sends it’s email from.
Version: 0.9.3
Author: Amon Caldas
Author URI: http://fazendoasmalas.com/
Last Change: 19.09.2013 08:41:06
*/

if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if ( !class_exists('wp_mail_from') ) {
	class wp_mail_from {
		
		function wp_mail_from() {
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
	
	$wp_mail_from = new wp_mail_from();
}
?>