<?php
/**
 * @package FAM Mail From
 * @author Amon Caldas
 * @version 0.1
 */
 
/*
Plugin Name: FAM Mail
Plugin URI: http://fazendoasmalas.com/
Description: Change the default address that WordPress sends it’s email from and process mass mails
Version: 0.9.4
Author: Amon Caldas
Author URI: http://fazendoasmalas.com/
Last Change: 20.12.2018 08:41:06
*/

if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

define('FAM_MAIL_PLUGIN_PATH', dirname( __FILE__ ));

// Imports
require_once(FAM_MAIL_PLUGIN_PATH . '/fam-mass-mailer.php');
require_once(FAM_MAIL_PLUGIN_PATH . '/mail-from.php');


// change from
$wp_mail_from = new wp_mail_from();

// process mass mails
$massMailer = new FamMassMailer();

register_rest_route("fam-mailer", '/send', array(
	array(
		'methods'  => "GET",
		'callback' => array(&$massMailer, 'run' ),
	)
));


?>