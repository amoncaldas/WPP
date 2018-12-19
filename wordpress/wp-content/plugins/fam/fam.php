<?php
/**
 * Plugin Name: FAM
 * Description: Add custom behaviors and functions to change WordPress and third party plugins regarding JWT authentication and wp rest api returning data. It assumes that the jwt-authentication-for-wp-rest-api, 
 * profilepress and pp-mailchimp plugins are installed adn active.
 * Version:     0.0.1
 *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 * Text Domain: fam
 *
 * @package FAM
 */

/**
 * This plugin assumes that the jwt-authentication-for-wp-rest-api plugin is already installed ac active
 */


// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

 class Fam {

	function __construct () {
		$this->defineConstants();
		$this->requiredDependencies();
		$this->runClasses();
		$this->registerEndpoints();	
	}	

	/**
	 * Define plugin constants
	 *
	 * @return void
	 */
	public function defineConstants () {
		define('FAM_PLUGIN_PATH', dirname( __FILE__ ));

		//Define the environment
		$serverName = $_SERVER["SERVER_NAME"];
		define('FAM_ENV_PRODUCTION', strpos($serverName,"teste.") === false && strpos($serverName,"staging.") === false && strpos($serverName,"dev.") === false);
	}

	/**
	 * Import plugins dependencies from includes
	 *
	 * @return void
	 */
	public function requiredDependencies () {		
		require_once(FAM_PLUGIN_PATH . '/includes/user/fam-user-data-wp-api.php');
		require_once(FAM_PLUGIN_PATH . '/includes/global/update-url.php');
		require_once(FAM_PLUGIN_PATH . '/includes/global/getters.php');
		require_once(FAM_PLUGIN_PATH . '/includes/feed/feed-loader.php');

		// Import custom listeners starters
		require_once(FAM_PLUGIN_PATH . '/includes/user/user-events-listener.php');
	}

	/**
	 * Instantiate the classes part of the plugin that will add listeners
	 *
	 * @return void
	 */
	public function runClasses () {
		// Start the user hooks listeners
		new UserEventsListener();
	}

	

	/**
	 * Register custom wp api plugin endpoints
	 *
	 * @return void
	 */
	public function registerEndpoints () {
		// Load ors api routes/endpoints
		if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {

			// Base api namespace that represents also the url
			$baseNamespace = 'fam-api/v1';

			$famUserAPI = new FamUserAPI($baseNamespace);
			add_action('rest_api_init', array(&$famUserAPI, 'register_routes'));
		} 
	}
 }

 // Start the plugin
 new Fam();








  
