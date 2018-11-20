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

define('ORS_PLUGIN_PATH', dirname( __FILE__ ));

// Import custom api endpoint classes
require_once ORS_PLUGIN_PATH . '/includes/user/ors-user-data-wp-api.php';
require_once ORS_PLUGIN_PATH . '/includes/global/update-site.php';

// Import custom listeners starters
require_once ORS_PLUGIN_PATH . '/includes/user/user-events-listener.php';

// Load ors api routes/endpoints
if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {

	// Base api namespace that represents also the url
	$baseNamespace = 'ors-api/v1';

	$orsUserData = new OrsUserData($baseNamespace);
	add_action('rest_api_init', array(&$orsUserData, 'register_routes'));
} 

// Start the user hooks listeners
$userEventsListener = new UserEventsListener();




  
