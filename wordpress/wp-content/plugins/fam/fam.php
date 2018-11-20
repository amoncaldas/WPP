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
 * This plugin assumes that the jwt-authentication-for-wp-rest-api, 
 * profilepress and pp-mailchimp plugins are already installed ac active
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define('FAM_PLUGIN_PATH', dirname( __FILE__ ));

// Import custom api endpoint classes
require_once(FAM_PLUGIN_PATH . '/includes/user/ors-user-data-wp-api.php');
require_once(FAM_PLUGIN_PATH . '/includes/global/update-url.php');

if(get_current_blog_id() === 1) {
	require_once(FAM_PLUGIN_PATH . '/includes/main-site/main-site.php');
}
else {
	require_once(FAM_PLUGIN_PATH . '/includes/sub-site/sub-site.php');
}

// Import custom listeners starters
require_once(FAM_PLUGIN_PATH . '/includes/user/user-events-listener.php');

//Define the environment
$serverName = strpos($_SERVER["SERVER_NAME"]);
define('FAM_ENV_PRODUCTION', strpos($serverName,"teste.") === false && strpos($serverName,"staging.") === false && strpos($serverName,"dev.") === false);

// Load ors api routes/endpoints
if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {

	// Base api namespace that represents also the url
	$baseNamespace = 'fam-api/v1';

	$orsUserData = new OrsUserData($baseNamespace);
	add_action('rest_api_init', array(&$orsUserData, 'register_routes'));
} 

// Start the user hooks listeners
$userEventsListener = new UserEventsListener();


function callback($buffer) {
	// modify buffer here, and then return the updated code
	$index = file_get_contents("/var/www/webapp/index.html");
	return $index;
  }
  
  function buffer_start() { ob_start("callback"); }
  
  function buffer_end() { ob_end_flush(); }
  
  add_action('wp_loaded', 'buffer_start');
  add_action('shutdown', 'buffer_end');




  
