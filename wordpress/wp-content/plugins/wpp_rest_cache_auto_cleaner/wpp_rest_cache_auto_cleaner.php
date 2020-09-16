<?php
/**
 * Plugin Name: WPP rest API cache cleaner
 * Description: Adds auto clean rest api cache for wp_rest_cache. When a post is updated, remove the list of posts cache.
 * This plugin assumes that the wp_rest_cache plugin is installed
 * Version:     0.0.1
 *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 * Text Domain: wpp
 *
 * @package wpp_rest_cache_auto_cleaner
 */

/**
 * This plugin assumes that the jwt-authentication-for-wp-rest-api plugin is already installed ac active
 */


// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
if ( ! defined('LOCALE_TAXONOMY_SLUG') ) {
	define('LOCALE_TAXONOMY_SLUG', "locale");
}
// Require WP_Rest_Cache_Plugin caching class
use \WP_Rest_Cache_Plugin\Includes\Caching\Caching;

class WppRestCacheAutoCleaner {
	public function __construct () {
		$this->register_hooks();
	}	

	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action('save_post', array($this,'wpp_clear_listing_wp_rest_cache'), 10, 2 );
	}

	/**
	 * Attach post locale slug to post object
	 *
	 * @param Array $post
	 * @return String - locale slug
	 */
	public function get_post_locale ($post) {
		$terms = get_the_terms( $post["ID"], LOCALE_TAXONOMY_SLUG );
		if ( !empty( $terms ) ){
			// get the first term
			$term = array_shift( $terms );
			return $term->slug;
		}
	}

	/**
	 * Try to remove listing post type endpoint cache on save post
	 *
	 * @param [type] $post_id
	 * @param [type] $post
	 * @return void
	 */
	public function wpp_clear_listing_wp_rest_cache ($post_id, $post) {
		if (is_plugin_active("wp-rest-cache/wp-rest-cache.php")) {			
			$post_type_object = get_post_type_object($post->post_type);
			$rest_base = $post_type_object->rest_base;
			$locale = $this->get_post_locale($post->to_array());
			$endpoint = "/wp-json/wp/v2/$rest_base?_embed=&l=$locale";

			$cachingPlugin = Caching::get_instance();
			$cachingPlugin->delete_cache_by_endpoint($endpoint, $cachingPlugin::FLUSH_PARAMS, false); // FLUSH_PARAMS or FLUSH_LOOSE
		}
	}
 }

 // Start the plugin
 $wppRestCacheAutoCleaner = new WppRestCacheAutoCleaner();










  
