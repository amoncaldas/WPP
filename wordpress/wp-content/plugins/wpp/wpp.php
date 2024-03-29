<?php
/**
 * Plugin Name: WPP
 * Description: Add custom behaviors and functions to change WordPress and third party plugins regarding JWT authentication and wp rest api returning data. It assumes that the jwt-authentication-for-wp-rest-api
 * plugin is installed
 * Version:     0.0.1
 *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 * Text Domain: wpp
 *
 * @package wpp
 */

/**
 * This plugin assumes that the jwt-authentication-for-wp-rest-api plugin is already installed ac active
 */


// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

 class WpWebApp {
	function __construct () {
		$this->defineConstants();
		$this->requiredDependencies();
		$this->runClasses();
		// add_action( 'init', array($this, 'runClasses'));
	}	

	/**
	 * Define plugin constants
	 *
	 * @return void
	 */
	public function defineConstants () {
		define('WPP_PLUGIN_PATH', dirname( __FILE__ ));

		// Define the environment
		$serverName = $_SERVER["SERVER_NAME"];
		define('WPP_ENV_PRODUCTION', strpos($serverName,"teste.") === false && strpos($serverName,"staging.") === false && strpos($serverName,"dev.") === false);

		// Base api namespace that represents also the url
		if ( ! defined('WPP_API_NAMESPACE') ) {
			define('WPP_API_NAMESPACE', "wpp/v1");
		}

		if ( ! defined('LOCALE_TAXONOMY_SLUG') ) {
			define('LOCALE_TAXONOMY_SLUG', "locale");
		}
	}

	/**
	 * Import plugins dependencies from includes
	 *
	 * @return void
	 */
	public function requiredDependencies () {		
		require_once(WPP_PLUGIN_PATH . '/includes/user/wpp-user.php');
		require_once(WPP_PLUGIN_PATH . '/includes/user/wpp-user-api.php');
		require_once(WPP_PLUGIN_PATH . '/includes/user/wpp-follower.php');
		require_once(WPP_PLUGIN_PATH . '/includes/mail/wpp-mailer.php');
		require_once(WPP_PLUGIN_PATH . '/includes/mail/wpp-notifier.php');
		require_once(WPP_PLUGIN_PATH . '/includes/mail/mail-from.php');
		require_once(WPP_PLUGIN_PATH . '/includes/oauth/wpp-oauth-wp-api.php');
		require_once(WPP_PLUGIN_PATH . '/includes/global/wp-web-app-services-api.php');
		require_once(WPP_PLUGIN_PATH . '/includes/global/functions.php');		
	}

	/**
	 * Instantiate the classes part of the plugin that will add listeners
	 *
	 * @return void
	 */
	public function runClasses () {
		// Start the user hooks listeners
		new WppUser();
		new WppUserApi();
		new WppMailFrom();
		new WppFollower();
		new WppNotifier();
		new WppOauthApi();
		new WppServicesApi();
	}
 }

 // Start the plugin
 $wpWebApp = new WpWebApp();










  
