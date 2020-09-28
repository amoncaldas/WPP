<?php
/**
 * Plugin Name: WPP Gep
 * Description: Adds geo features to posts
 * Version:     0.0.1
 *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 * Text Domain: wpp
 *
 * @package wpp_geo
 */

/**
 * This plugin assumes that the jwt-authentication-for-wp-rest-api plugin is already installed ac active
 */


// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
class WppGeo {
	public function __construct () {
		$this->register_hooks();
	}	

	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action('rest_api_init', array($this, 'on_rest_api_init'));
	}

	/**
	 * Register rest api hooks to treat locale and resolve custom data
	 *
	 * @return void
	 */
	public function on_rest_api_init() {
		$public_post_types = get_post_types(array("public"=>true));
		unset($public_post_types["attachment"]);

		foreach ($public_post_types as $post_type) {
			if (post_type_exists("place") === true) {
				register_rest_field($post_type, 'places',
					array(
						'get_callback'  => function ($post, $field_name, $request) {
							return $this->resolve_places($post);
						},
						'schema' => null,
					)
				);
			}
			if (post_type_exists("map_route") === true) {
				register_rest_field($post_type, 'routes',
					array(
						'get_callback'  => function ($post, $field_name, $request) {
							$routes = $this->resolve_routes($post);
							return $routes;
						},
						'schema' => null,
					)
				);
			}	
		}
	}

	/**
	 * Resolve the the post places
	 *
	 * @param Array $post_arr
	 * @return array of places
	 */
	public function resolve_places($post_arr) {
		$place_ids = get_post_meta($post_arr["id"], "places", true);
		$places = [];
		if (is_array($place_ids) && count($place_ids) > 0) {			
			foreach ($place_ids as $place_id) {				
				$location = get_post_meta($place_id, "location", true);	
				if($location) {
					$places[$place_id] = $location;
					$places[$place_id]["title"] = get_the_title($place_id);
					$locales = wp_get_post_terms($place_id, LOCALE_TAXONOMY_SLUG);
					$places[$place_id][LOCALE_TAXONOMY_SLUG] = count($locales) > 0 ? $locales[0]->slug : null;
					$places[$place_id]["link"] = get_permalink($place_id);
				}
			}
		} else {			
			$location = get_post_meta($post_arr["id"], "location", true);			
			if($location) {
				$title = is_array($post_arr["title"]) ? $post_arr["title"]["rendered"] : $post_arr["title"];
				$places[$post_arr["id"]] = $location;
				$places[$post_arr["id"]]["title"] = $title;
				$locales = wp_get_post_terms($post_arr["id"], LOCALE_TAXONOMY_SLUG);
				$places[$post_arr["id"]][LOCALE_TAXONOMY_SLUG] = count($locales) > 0 ? $locales[0]->slug : null;
				$places[$post_arr["id"]]["link"] = $post_arr["link"];
			}
		}
		return $places;
	}	

	/**
	 * Resolve the the post routes
	 *
	 * @param Array $post_arr
	 * @return array of routes
	 */
	public function resolve_routes($post_arr) {
		$route_ids = get_post_meta($post_arr["id"], "routes", true);
		$routes = [];
		if (is_array($route_ids) && count($route_ids) > 0) {			
			foreach ($route_ids as $route_id) {				
				$route_content = get_post_meta($route_id, "route_content", true);
				
				if($route_content) {
					$route = [
						"means_of_transportation" => get_post_meta($route_id, "means_of_transportation", true),
            			"route_content_type" => get_post_meta($route_id, "route_content_type", true),
            			"coordinates_order" => get_post_meta($route_id, "coordinates_order", true),
						"route_content" => $route_content,						
						"title" => get_the_title($route_id),
						"id" => $route_id
					];
					$routes[$route_id] = $route;
				}
			}
		}
		return $routes;
	}
 }

 // Start the plugin
 $wppGeo = new WppGeo();










  
