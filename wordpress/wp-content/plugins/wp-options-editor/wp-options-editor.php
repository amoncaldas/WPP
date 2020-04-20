<?php
/*
Plugin Name: WPP Options
Description: Manage WPP theme options
Author: Amon Santana
Author URI:  https://github.com/amoncaldas
Version: 1.2
*/

// include the settings page
require_once( 'admin/manager-page.php' );

// Load the settings page if we're in the admin section
if ( is_admin() ){
	$settings = new OptionsManagerSettingsPage( __FILE__ );
}

// define the plugin url
if ( ! defined( 'WPOE_URL' ) ){
	define( 'WPOE_URL', plugins_url( '' ,  __FILE__ ) );
}
