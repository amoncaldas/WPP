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

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define('FAM_MAIL_PLUGIN_PATH', dirname( __FILE__ ));

// Imports
require_once(FAM_MAIL_PLUGIN_PATH . '/fam-mass-mailer.php');
require_once(FAM_MAIL_PLUGIN_PATH . '/mail-from.php');


// change from
$wp_mail_from = new wp_mail_from();

// process mass mails
$massMailer = new FamMassMailer();

?>