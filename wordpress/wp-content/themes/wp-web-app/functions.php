<?php
/**
 * Theme: FAM WebApp *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

define('DEFAULT_LOCALE', "pt-br");
define('LOCALE_TAXONOMY_SLUG', "locale");
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
	$locale = DEFAULT_LOCALE;
	if (isset($_GET["locale"])) {
		$locale = $_GET["locale"];
	} elseif (isset($_SERVER["HTTP_LOCALE"])) {
		$locale = $_SERVER["HTTP_LOCALE"];
	} else {
		$browser_locale = get_browser_locale();
		if (isset($browser_locale)) {
			$locale = $browser_locale;
		}
	}
	
	$locale_options = get_wpp_locales();
	if (!in_array($locale, $locale_options)){
		$locale = DEFAULT_LOCALE;
	}
	return $locale;
}

/**
 * Get all wpp supported locales
 *
 * @return void
 */
function get_wpp_locales() {
	$locale_options = get_option("wpp_locales", DEFAULT_LOCALE);
	$locale_options = strpos($locale_options, ",") > -1 ? explode(",", $locale_options) : [$locale_options];
	$locale_options = array_map('trim', $locale_options);
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
		'rest_base' => 'sections',
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
 * Set the output of the theme
 *
 * @return void
 */
function set_output () {
	if (is_front_end()) {
		$crawlers_user_agents = get_option("wpp_crawlers_user_agents", "fake-crawler-agent");
		$crawlers_user_agents = strpos($crawlers_user_agents, ",") > -1 ? explode(",", $crawlers_user_agents) : [$crawlers_user_agents];
		$crawlers_user_agents = array_map('trim', $crawlers_user_agents);

		$is_crawler_request = false;
		foreach ($crawlers_user_agents as $crawler) {
			if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($crawler)) !== false) {
				$is_crawler_request = true;
				break;
			}
		}

		if (isset($_GET["_escaped_fragment_"]) && get_option("wpp_treat_escaped_fragment_as_crawler") === "yes" || $is_crawler_request) {
			define('RENDER_AUDIENCE', 'CRAWLER_BROWSER');
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
}

update_site_url();

add_action('init', 'after_init', 10);

// Start theme functions class
require_once("wp-web-app-theme.php");
new WpWebAppTheme();



 











  
