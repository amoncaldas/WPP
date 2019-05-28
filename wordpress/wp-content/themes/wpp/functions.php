<?php
/**
 * Theme: WPP
 * Author: Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

if ( ! defined('LOCALE_TAXONOMY_SLUG') ) {
	define('LOCALE_TAXONOMY_SLUG', "locale");
}

define('SECTION_POST_TYPE', "section");
define('SECTION_TYPE_FIELD_SLUG', "section_type");
define('SECTION_POST_HOME_FIELD_VALUE', "home");


/**
 * Check if the request is to a front-end page
 *
 * @return boolean
 */
function is_front_end() {
	$uri = $_SERVER["REQUEST_URI"];
	return $uri !== "" && !is_admin() && strrpos($uri, "wp-json") === false && strrpos($uri, "/feed") === false && strrpos($uri, "wp-login.php") === false && strrpos($uri, "/admin") === false;
}

	/**
 * WordPress store in db absolute urls and this is the way to update this 
 * and other post metas that also stores absolute urls to images
 * @see https://codex.wordpress.org/Changing_The_Site_URL
 * WP_HOME is defined in wp-config.php
 */
function update_site_url() {
	global $wpdb;
	$current_url = $wpdb->get_col( "SELECT option_value from ".$wpdb->prefix."options where option_name = 'siteurl'" )[0];
	if(WP_HOME != null && $current_url != WP_HOME){
		// For some reason (maybe cache ?) update_option function is not working in this case, so we direct update the db
		$wpdb->update($wpdb->prefix."options", array('option_value'=>WP_HOME), array('option_name' => 'siteurl'));
		$wpdb->update($wpdb->prefix."options", array('option_value'=>WP_HOME), array('option_name' => 'home'));
	}
}

/**
 * Get request locale, considering query string, header, browser locale or default
 * If the locale in the request is not valid, it will return the default one
 *
 * @return string|null
 */
function get_request_locale() {
	$locale = get_theme_default_locale();
	$post_id = get_request_post_id();
	if ($post_id) {
		$content_lang_taxonomies = wp_get_post_terms($post_id, LOCALE_TAXONOMY_SLUG);
		if( is_array($content_lang_taxonomies) && count($content_lang_taxonomies) > 0) {
			$locale = $content_lang_taxonomies[0]->slug;
		}
	} else {
		if (isset($_GET["locale"])) {
			$locale = $_GET["locale"];
		} elseif (isset($_GET["l"])) {
			$locale = $_GET["l"];
		} elseif (isset($_SERVER["HTTP_LOCALE"])) {
			$locale = $_SERVER["HTTP_LOCALE"];
		} else {
			$browser_locale = get_browser_locale();
			if (isset($browser_locale)) {
				$locale = $browser_locale;
			}
		}
	}
	
	$locale_options = get_wpp_locales();
	if (!in_array($locale, $locale_options)){
		$locale = get_theme_default_locale();
	}
	return $locale;
}

/**
 * Get cotent related posts
 *
 * @param Integer $content_id
 * @return Array
 */
function get_related ($content_id) {
  $posts = [];
  
  $public_post_types = get_post_types(array("public"=>true));
  unset($public_post_types["attachment"]);
  unset($public_post_types[SECTION_POST_TYPE]);
  $args = ["post_type" => $public_post_types];

	$include = get_post_meta($content_id, "related", true);
	if (is_array($include)) {
    $args["post__in"] = $include;
    $posts = get_posts($args); 
	}

	return $posts;
}

/**
 * Get all wpp supported locales
 *
 * @return void
 */
function get_wpp_locales() {
	$wpp_locales_terms = get_terms( array('taxonomy' => LOCALE_TAXONOMY_SLUG, 'hide_empty' => false, 'orderby' => 'id', 'order' => 'ASC'));
	$locale_options = [];
	foreach ($wpp_locales_terms as $term) {
		if ($term->slug !== "neutral") {
			$locale_options[] = $term->slug;
		}
	}
	return $locale_options;
}

/**
 * Get request locale sent by the browser
 *
 * @return string|null
 */
function get_browser_locale () {
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$locale_strings = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		
		foreach ($locale_strings as $locale_string) {
			$locale_string = strtolower($locale_string);
			if(strpos($locale_string, ";") > -1)
			{
				$locale_string = explode(";", $locale_string)[0];
			}
			return $locale_string;
		}           
	}
}


/**
 * Add menu support to the theme
 *
 * @return void
 */	
	
function register_wpp_menus() {
	foreach (get_wpp_locales() as $locale) {
		register_nav_menus(
			array(
				"primary-menu-$locale" => __( "Primary Menu $locale" ),
				"secondary-menu-$locale" => __( "Secondary Menu $locale" )
			)
		);
	}
}

/**
 * Add a admin menu entry to manage the languages
 *
 * @return void
 */
function add_language_admin_menu(){
	global $submenu;
	$submenu['options-general.php'][] = array( 'Locales', 'manage_options', "/wp-admin/edit-tags.php?taxonomy=".LOCALE_TAXONOMY_SLUG);
}

/**
 * Register custom types section and lang
 *
 * @return void
 */
function register_custom_types () {
	$section_args = array (
		'name' => SECTION_POST_TYPE,
		'label' => ucfirst(SECTION_POST_TYPE)."s",
		'singular_label' => ucfirst(SECTION_POST_TYPE),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'show_in_rest' => true,
		'rest_base' => strtolower(SECTION_POST_TYPE)."s",
		'map_meta_cap' => true,
		'has_archive' => false,
		'exclude_from_search' => false,
		'capability_type' => array(SECTION_POST_TYPE, SECTION_POST_TYPE."s"),
		'hierarchical' => false,
		'rewrite' => true,
		'rewrite_withfront' => true,	
		'show_in_menu' => true,
		'supports' => 
		array (
			0 => 'title',
			2 => 'thumbnail',
			3 => 'revisions',
		),
	);

	register_post_type( SECTION_POST_TYPE , $section_args );

	$lang_tax_args = array (
		'name' => LOCALE_TAXONOMY_SLUG."s",
		'label' => ucfirst(LOCALE_TAXONOMY_SLUG),
		'singular_label' => ucfirst(LOCALE_TAXONOMY_SLUG),
		'public' => true,
		'publicly_queryable' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'query_var' => true,
		'rewrite' => true,
		'rewrite_withfront' => '1',
		'rewrite_hierarchical' => '0',
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rest_base' => 'langs',
	);
	register_taxonomy( LOCALE_TAXONOMY_SLUG, null, $lang_tax_args );
}


/**
 * Determine if the request is being made by a crawler
 *
 * @return boolean
 */
function is_crawler_request () {
  $is_crawler_request = false;
  $crawlers_user_agents = get_option("wpp_crawlers_user_agents", "fake-crawler-agent");
  $crawlers_user_agents = strpos($crawlers_user_agents, ",") > -1 ? explode(",", $crawlers_user_agents) : [$crawlers_user_agents];
  $crawlers_user_agents = array_map('trim', $crawlers_user_agents);

  $is_crawler_user_agent = false;
  foreach ($crawlers_user_agents as $crawler) {
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($crawler)) !== false) {
      $is_crawler_user_agent = true;
      break;
    }
  }
  if (isset($_GET["_escaped_fragment_"]) && get_option("wpp_treat_escaped_fragment_as_crawler") === "yes" || $is_crawler_user_agent) {
    $is_crawler_request = true;
  }
  return $is_crawler_request;
}


/**
 * Add the locale query string to each menu item href
 *
 * @param Array $atts
 * @param WP_Post $item
 * @param stdClass $args
 * @return void
 */
function add_crawler_menu_iems_locale ($atts, $item, $args) {
  $atts["href"] .= "?l=".get_request_locale();
  return $atts;
}

/**
 * Get the amount of pages for a given pot type and posts per page
 *
 * @param String $post_type
 * @param Integer $posts_per_page
 * @return Integer
 */
function wpp_get_post_type_pages($post_type, $posts_per_page) {
  // Prepare pagination
  $posts_to_count = new WP_Query(array( "post_type"=> $post_type,  "post_status"=> "publish"));
	$total = $posts_to_count->post_count;
	$pages = 1;
	if ($total > $posts_per_page) {
		$pages = $total / $posts_per_page;
		$rest = $total % $posts_per_page;
		if ($rest > 0) {
			$pages++;
		}
  }
  return $pages;
}

/**
 * Set the output of the theme
 *
 * @return void
 */
function set_output () {
	if ($_SERVER["REQUEST_URI"] === "/manifest.json") {
		define('RENDER_AUDIENCE', 'MANIFEST');
		require_once("app-renderer.php");
	} elseif (is_front_end()) {
		if (is_crawler_request()) {
      define('RENDER_AUDIENCE', 'CRAWLER_BROWSER');
      add_filter('nav_menu_link_attributes','add_crawler_menu_iems_locale', 10, 3);
		} else {
			define('RENDER_AUDIENCE', 'USER_BROWSER');
		}
		require_once("app-renderer.php");
	} 
}

/**
 * Unsubscribe a follower to the notification
 *
 * @return void
 */
function get_wpp_metas() {
	$all_options = wp_load_alloptions();

	$wpp_metas = [];
	foreach ($all_options as $key => $value) {
		if ( strpos($key, "wpp_meta_") === 0) {
			$clean_key =  $meta_property_name = str_replace("wpp_meta_", "", $key);
			$wpp_metas[$clean_key] = $value;
		}
	}
	return $wpp_metas;
}

/**
 * Allow cors for content type, authorization and locale
 *
 * @return WP_REST_Response
 */
function allow_cors() {
	$allow_cors = get_option("wpp_allow_cors", false);
	if ($allow_cors === "yes") {
		add_filter('rest_post_dispatch', function (\WP_REST_Response $result) {
			if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
				$result->header('Access-Control-Allow-Headers', 'Authorization, Content-Type, locale', true);
			}
			return $result;
		});
	}
}

/**
 * Set custom logo in login screen from wpp sitel logo option
 *
 * @return void
 */
function wpp_login_logo() { 
	$og_image_url = network_site_url(trim(get_option("wpp_site_relative_logo_url")));
	$login_style =  "
		<style type='text/css'>
			#login h1 a, .login h1 a {
				background-image: url($og_image_url);
				width: 220px;
				background-size: 250px;
				background-repeat: no-repeat;
				padding-bottom: 30px;
				min-height: 220px;
			}
		</style>";
		echo $login_style;
}

/**
 * Costumize the wp login logo url
 *
 * @param String $url
 * @return String
 */
function wpp_loginlogo_url($url) {
  return network_site_url();
}

/**
 * Get the default locale
 *
 * @return String
 */
function get_theme_default_locale () {
	$default_locale = get_option("wpp_default_locale");

	$locale_found = get_term_by('slug', $default_locale, LOCALE_TAXONOMY_SLUG);	
	if (!$locale_found) {
		$default_locale = "en-us";
	}
	return $default_locale;
}

/**
 * Get ACF POST request field value
 *
 * @param [type] $field_name
 * @param boolean $first
 * @return void
 */
function get_acf_post_request_field_value ($field_name, $first = true) {
	$field_value = null;
	$acf = $_POST['acf'];

	if (isset($acf) && is_array($acf)) {
		// Find the import id from the ACF fields extra data
		foreach( $acf as $key => $value ) {			
			// Get field.
			$field = acf_get_field( $key );				
			
			// Get the import id value
			if( $field && is_array($field) & $field["name"] === $field_name) {				
				if ($first && is_array($value) && count($value) > 0 ) {
					$field_value = $value[0];
				} else {
					$field_value = $value;
				}
				break;
			}
		}
	}
	return $field_value;
}

/**
	* 
	* get the translated endpoint of a post type endpoint
	*
	* @param string $post_url_slug
	* @param string $lang
	* @return String|false
	*/
function get_post_type_by_endpoint($endpoint) {
	$public_post_types = get_post_types(array("public"=>true));
	unset($public_post_types["attachment"]);

	if(in_array($endpoint, $public_post_types)){
			return $endpoint;
	} else {
			$registered_post_types = get_post_types([ "public"=>true], "object");

			foreach ($registered_post_types as $registered_post_type) {
					$rest_base_translation = get_post_path_translation($registered_post_type->name, get_request_locale());
					if ($rest_base_translation === $endpoint || $registered_post_type->rest_base === $endpoint) {
							return $registered_post_type->name;
					}
			}
	}
	return false;
}

/**
 * Translate a post type slug
 *
 * @param string $post_type
 * @param string $lang
 * @return void
 */
function get_post_path_translation($post_type, $lang) {
	$dictionary = get_option("wpp_post_type_translations", "{}");
	$dictionary = str_replace("\\", "", $dictionary);
	$dictionary = json_decode($dictionary, true);

	if (!isset($dictionary[$post_type])) {
		return $post_type;
	} elseif (!isset($dictionary[$post_type][$lang])) {
		return $post_type;
	} elseif (!isset($dictionary[$post_type][$lang]["path"])) {
		return $post_type;
	}
	else {
		return $dictionary[$post_type][$lang]["path"];
	}
}

/**
 * Translate a post type slug
 *
 * @param string $post_type
 * @param string $lang
 * @return void
 */
function get_post_type_title_translation($post_type, $lang) {
	$dictionary = get_option("wpp_post_type_translations", "{}");
	$dictionary = str_replace("\\", "", $dictionary);
	$dictionary = json_decode($dictionary, true);

	if (!isset($dictionary[$post_type])) {
		return $post_type;
	} elseif (!isset($dictionary[$post_type][$lang])) {
		return $post_type;
	} elseif (!isset($dictionary[$post_type][$lang]["title"])) {
		return $post_type;
	}
	else {
		return $dictionary[$post_type][$lang]["title"];
	}
}

/**
 * Get home sections with a given language term id
 *
 * @param Integer $lang_term_id
 * @return Array
 */
function get_home_section($locale = null) {
	if (!$locale) {
		$locale = get_request_locale();
	}

	$wpp_locales_terms = get_terms( array('taxonomy' => LOCALE_TAXONOMY_SLUG, 'hide_empty' => false, 'slug' => $locale));
	$wpp_locales_term = $wpp_locales_terms[0];

	// Set the get posts args to retrieve the home section
	$home_section_args = array(
		"post_type"=> SECTION_POST_TYPE, 
		"post_status"=> "publish", 
		'meta_query' => array(
			array(
				'key'=> SECTION_TYPE_FIELD_SLUG,
				'value'=> SECTION_POST_HOME_FIELD_VALUE
			)
		),
		'tax_query' => array (
			array(
				'taxonomy' => LOCALE_TAXONOMY_SLUG,
				'field' => 'term_id',
				'terms' => $wpp_locales_term->term_id
			)
		)
	);
	$home_sections = get_posts($home_section_args);	
	if (is_array($home_sections) && count($home_sections) > 0) {
		return $home_sections[0];
	} 
	return $home_sections;
}


/**
 * Get the post id of the request
 *
 * @return Integer|null
 */
function get_request_post_id() {
	$REQUEST_URI = trim(strtok($_SERVER["REQUEST_URI"],'?'), "/");
	if ($REQUEST_URI !== "/") {
			$uri_parts = explode("/", $REQUEST_URI);
			$last_uri_segment = $uri_parts[count($uri_parts) -1];
			if(is_numeric($last_uri_segment)) {
					return $last_uri_segment;
			} else {
					global $wpdb;
					$sql = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' && post_type = '".SECTION_POST_TYPE."' && post_name = '".$last_uri_segment."'";
					$section_id = $wpdb->get_var($sql);	
					if ($section_id > 0) {
							return $section_id;
					} else {
							$page = get_page_by_path( $last_uri_segment);		
							if ($page !== null) {
									return $page->ID;
							}
					}
			}
	}
}


/**
 * After init run custom functions
 *
 * @return void
 */
function after_init() {
	allow_cors();
	register_custom_types();
	set_output();
	register_wpp_menus();
	add_language_admin_menu();
	add_theme_support( 'menus' );
	add_theme_support( 'post-thumbnails');
	add_action( 'login_head', 'wpp_login_logo' );	
	add_filter( 'login_headerurl', 'wpp_loginlogo_url' );
}

update_site_url();

add_action('init', 'after_init', 10);

// Start theme functions class
require_once("wpp-theme.php");
new WpWebAppTheme();



 











  
