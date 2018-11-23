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

 class Fam {

	function __construct () {
		$this->defineConstants();
		$this->requiredDependencies();
		$this->runClasses();
		$this->registerEndpoints();
		$this->register_hooks();		
	}

	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_insert_post', array($this,'after_insert_post'), 10, 2 );
	}

	/**
	 * run custom adjustments after a new post is created
	 *
	 * @param Integer $post_id
	 * @param WP_Post $post
	 * @return void
	 */
	public function after_insert_post( $post_id, $post ) {
		$this->set_default_taxonomy($post, "trip");
		$this->set_default_taxonomy($post, "lang");
		return $post;
	}

	/**
	 * Set default taxonomy for a fresh created post
	 *
	 * @param WP_Post $post
	 * @param string $taxonomy_slug
	 * @return void
	 */
	public function set_default_taxonomy($post, $taxonomy_slug) {
		$post_id = $post->ID;

		// Get the possible taxonomies to this kind of post
		$taxonomies = get_object_taxonomies($post);
		
		// If the post supports the passed taxonomy slug
		// we will make sure it has one assigned
		if (in_array($taxonomy_slug, $taxonomies)) {
			$term_list = wp_get_post_terms($post_id, $taxonomy_slug, array("fields" => "all"));	
			
			// If the post has not such taxonomy assigned
			if ( empty( $term_list ) ) {				
				// Get all available terms, and select the first one as default
				$available_tax_terms = get_terms( array('taxonomy' => $taxonomy_slug, 'hide_empty' => false, 'orderby' => 'id', 'order' => 'ASC'));
				if (isset($available_tax_terms[0])) {
					// Assign the default (the first one available)
					$term_arr = [$available_tax_terms[0]->term_id];
					wp_set_post_terms($post_id, $term_arr, $taxonomy_slug);
				}				
			}
		}
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
	 * Register custom wp api plugin endpoints
	 *
	 * @return void
	 */
	public function registerEndpoints () {
		// Load ors api routes/endpoints
		if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {

			// Base api namespace that represents also the url
			$baseNamespace = 'fam-api/v1';

			$orsUserData = new FamUserData($baseNamespace);
			add_action('rest_api_init', array(&$orsUserData, 'register_routes'));
		} 
	}
 }

 // Start the plugin
 new Fam();








  
