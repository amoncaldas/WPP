<?php
/**
 * Theme: FAM WebApp *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

 class WpWebAppTheme {

	public $locale_taxonomy_slug = "locale";
	public $section_custom_post_type_slug = "section";
	public $section_type_field_slug = "section_type";
	public $section_type_home_field_value = "home";
	public $default_locale = "en-us";
	public $WPP_SKIP_AFTER_SAVE = false;
	

	function __construct () {
		$this->update_site_url();
		$this->add_supports();
		$this->set_output();
		$this->register_hooks();
	}	
	
	/**
	 * WordPress store in db absolute urls and this is the way to update this 
	 * and other post metas that also stores absolute urls to images
	 * @see https://codex.wordpress.org/Changing_The_Site_URL
	 * WP_HOME is defined in wp-config.php
	 */
	public function update_site_url() {
		global $wpdb;
		$current_url = $wpdb->get_col( "SELECT option_value from ".$wpdb->prefix."options where option_name = 'siteurl'" )[0];
		if(WP_HOME != null && $current_url != WP_HOME){
			// for some reason update_option function is not working in this case, so we direct update the db
			$wpdb->update($wpdb->prefix."options", array('option_value'=>WP_HOME), array('option_name' => 'siteurl'));
			$wpdb->update($wpdb->prefix."options", array('option_value'=>WP_HOME), array('option_name' => 'home'));
		}
	}

	/**
	 * Check if the request is to a front-end page
	 *
	 * @return boolean
	 */
	public function is_front_end() {
		$uri = $_SERVER["REQUEST_URI"];
		return $uri !== "" && !is_admin() && strrpos($uri, "wp-json") === false && strrpos($uri, "/feed") === false && strrpos($uri, "wp-login.php") === false;
	}
  
	/**
	 * Set the output of the theme
	 *
	 * @return void
	 */
	public function set_output () {
		if ($this->is_front_end()) {
			$crawlers_user_agents = ["googlebot","bingbot","msnbot","yahoo",
				"Baidu","aolbuild","facebookexternalhit","iaskspider","DuckDuckBot",
				"Applebot","Almaden","iarchive","archive.org_bot"];

			$is_crawler_request = false;
			foreach ($crawlers_user_agents as $crawler) {
				if (strpos($_SERVER['HTTP_USER_AGENT'], $crawler) !== false) {
					$is_crawler_request = true;
					break;
				}
			}

			if (isset($_GET["_escaped_fragment_"]) || $is_crawler_request) {
				define('RENDER_AUDIENCE', 'CRAWLER_BROWSER');
			} else {
				define('RENDER_AUDIENCE', 'USER_BROWSER');
			}
			require_once("app-renderer.php");
		}
	}

	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action('wp_insert_post', array($this,'after_save_content'), 10, 2 );
		add_filter('request', array($this, 'set_feed_types'));
		add_action('init', array($this, 'register_custom_types'), 10);
		add_action('init', array($this, 'make_sure_locale_exists'), 11);
		add_action('init', array($this, 'make_sure_home_section_exists'), 12);
		add_action('admin_menu', array($this, 'add_language_admin_menu'));
		add_action('rest_api_init', array($this, 'on_rest_api_init'));

		// Customize permalink
		add_filter('post_type_link', array($this, 'set_post_permalink'), 10, 2);
		add_filter('preview_post_link', array($this, 'set_post_preview_permalink'), 10, 2);
		add_filter('page_link', array($this, 'set_page_permalink'), 10, 2);
		add_filter('preview_page_link', array($this, 'set_page_preview_permalink'), 10, 2);
		add_filter('image_send_to_editor', array($this, 'set_image_before_send_to_editor'), 10, 6 );		
	}

	/**
	 * Check if home section exists. If does not exist, create it
	 *
	 * @return void
	 */
	public function make_sure_locale_exists() {
		if (is_admin()) {
			$en_us_locale = get_term_by('slug', 'en-us', $this->locale_taxonomy_slug);	
			if (!$en_us_locale) {
				wp_insert_term("en-us", $this->locale_taxonomy_slug, array("description"=> "English", "slug"=> "en-us"));
			}
			$neutral_locale = get_term_by('slug', 'neutral', $this->locale_taxonomy_slug);	
			if (!$neutral_locale) {
				wp_insert_term("neutral", $this->locale_taxonomy_slug, array("description"=> "Neutral", "slug"=> "neutral"));
			}
		}
	}

	/**
	 * Check if home section exists. If does not exist, create it
	 *
	 * @return void
	 */
	public function make_sure_home_section_exists() {
		if (is_admin()) {
			// Get all available terms, and select the first one as default
			$available_locale_terms = get_terms( array('taxonomy' => $this->locale_taxonomy_slug, 'hide_empty' => false, 'orderby' => 'id', 'order' => 'ASC'));
	
			if (is_array($available_locale_terms)) {
				foreach ($available_locale_terms as $lang_term) {
					if ($lang_term->slug === "neutral") {
						continue;
					}
	
					// Get the home for a given language. If does not exist, create it
					$home_sections = $this->get_home_section($lang_term->term_id);
	
					if (count($home_sections) === 0) {
						$section_title = "Home | ". $lang_term->name;
						$home_section_id = wp_insert_post(
							array(
								"post_type"=> $this->section_custom_post_type_slug, 
								"post_status"=> "publish",
								"post_author"=> 1, // 1 is always the admin, the first user created
								"post_title"=> $section_title,
								"meta_input"=> array(
									$this->section_type_field_slug => $this->section_type_home_field_value
								)
							)
						);
						// Assign the default (the first one available)
						wp_set_post_terms($home_section_id, [$lang_term->term_id], $this->locale_taxonomy_slug);
					}
				}				
			}
		}
	}

	/**
	 * Register user meta data to get and update callbacks user wp rest api
	 *
	 * @return void
	 */
	public function on_rest_api_init() {
		// Apply the locale taxonomy filter to when running rest api requests
		add_action( 'pre_get_posts', array($this, 'apply_locale_filter_get_posts') );

		// Add a custom field for section
		register_rest_field( $this->section_custom_post_type_slug, 'places',
			array(
				'get_callback'  => function ($post, $field_name, $request) {
					return $this->resolve_places($post, $field_name, $request);
				},
				'schema' => null,
			)
		);

		$public_post_types = get_post_types(array("public"=>true));
		unset($public_post_types["attachment"]);

		foreach ($public_post_types as $post_type) {
			// Add a custom field for section
			register_rest_field($post_type, 'locale',
				array(
					'get_callback'  => function ($post, $field_name, $request) {
						return $this->attach_post_locale( $post);
					},
					'schema' => null,
				)
			);
		}
	
	}

	public function attach_post_locale ($post) {
		$terms = get_the_terms( $post->ID, $this->locale_taxonomy_slug );
		if ( !empty( $terms ) ){
			// get the first term
			$term = array_shift( $terms );
			return $term->slug;
		}
	}

	/**
	 * Apply the locale taxonomy filter to when running rest api requests
	 *
	 * @param [type] $query
	 * @return void
	 */
	public function apply_locale_filter_get_posts($query) {		
		$request_locale = $this->get_request_locale();
		$tax_query = array (
			array(
				'taxonomy' => $this->locale_taxonomy_slug,
				'field' => 'slug',
				'terms' => [$request_locale, "neutral"]
			)
		);
		$query->set( 'tax_query', $tax_query );		
	}

	/**
	 * Get request locale, considering query string, header, browser locale or default
	 * If the locale in the request is not valid, it will return the default one
	 *
	 * @return string|null
	 */
	public function get_request_locale() {
		$locale = $this->default_locale;
		if (isset($_GET["locale"])) {
			$locale = $_GET["locale"];
		} elseif (isset($_SERVER["locale"])) {
			$locale = $_SERVER["locale"];
		} else {
			$browser_locale = $this->get_browser_locale();
			if (isset($browser_locale)) {
				$locale = $browser_locale;
			}
		}
		$locale_options = get_option("wpp_locales", $this->default_locale);
		$locale_options = strpos($locale_options, ",") > -1 ? explode(",", $locale_options) : [$locale_options];
		$locale_options = array_map('trim', $locale_options);

		if (!in_array($locale, $locale_options)){
			$locale = $this->default_locale;
		}
		return $locale;
	}

	/**
     * Get request locale sent by the browser
     *
     * @return string|null
     */
    protected function get_browser_locale () {
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
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
	 * Add custom data to the wp/v2/users/<user-id> endpoint
	 *
	 * @param Wp_User $user
	 * @param string $field_name
	 * @param Object $request
	 * @return array of metas to be added in the response
	 */
	public function resolve_places($post_arr, $field_name, $request) {
		$place_ids = get_post_meta($post_arr["id"], "places", true);
		$places = [];
		if (is_array($place_ids) && count($place_ids) > 0) {			
			foreach ($place_ids as $place_id) {				
				$location = get_post_meta($place_id, "location", true);	
				if($location) {
					$places[$place_id] = $location;
					$places[$place_id]["title"] = get_the_title($place_id);
					$locales = wp_get_post_terms($place_id, $this->locale_taxonomy_slug);
					$places[$place_id][$this->locale_taxonomy_slug] = count($locales) > 0 ? $locales[0]->slug : null;
				}
			}
		}
		return $places;
	}	


	/**
	 * Register custom types section and lang
	 *
	 * @return void
	 */
	public function register_custom_types () {
		$section_args = array (
			'name' => $this->section_custom_post_type_slug,
			'label' => 'Sections',
			'singular_label' => 'Section',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_rest' => true,
			'rest_base' => 'sections',
			'map_meta_cap' => true,
			'has_archive' => false,
			'exclude_from_search' => false,
			'capability_type' => array($this->section_custom_post_type_slug, $this->section_custom_post_type_slug."s"),
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

		register_post_type( $this->section_custom_post_type_slug , $section_args );

		$lang_tax_args = array (
			'name' => $this->locale_taxonomy_slug."s",
			'label' => ucfirst($this->locale_taxonomy_slug),
			'singular_label' => ucfirst($this->locale_taxonomy_slug),
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
		register_taxonomy( $this->locale_taxonomy_slug, null, $lang_tax_args );
	}

	/**
	 * Add a admin menu entry to manage the languages
	 *
	 * @return void
	 */
	public function add_language_admin_menu(){
		global $submenu;
		$submenu['options-general.php'][] = array( 'Locales', 'manage_options', "/wp-admin/edit-tags.php?taxonomy=".$this->locale_taxonomy_slug);
	}

	/**
	 * Set post types added to the feed output
	 *
	 * @param Array $qv
	 * @return Array
	 */
	public function set_feed_types($qv) {
		if (isset($qv['feed'])) {			
			$builtin_post_types = get_post_types(array("public"=>true, "_builtin"=>true));
			unset($builtin_post_types["attachment"]);
			$custom_post_types = get_post_types_by_support("feed");		
			$custom_post_types[] = $this->section_custom_post_type_slug;	
			$post_types = array_merge_recursive($builtin_post_types, $custom_post_types);
			$qv['post_type'] = $post_types;
		}
		return $qv;
	}

	/**
	 * Run custom action after a content is inserted
	 *
	 * @param Integer $content_id
	 * @param String $content
	 * @return WP_Post
	 */
	public function after_save_content( $content_id, $content) {
		if ($content->post_type === "page") {
			return $this->after_save_page($content_id, $content);
		} else {
			return $this->after_save_post($content_id, $content);
		}
	}

	/**
	 * run custom adjustments after a new post is created
	 *
	 * @param Integer $post_id
	 * @param WP_Post $post
	 * @return WP_Post
	 */
	public function after_save_post( $post_id, $post ) {
		if ($this->WPP_SKIP_AFTER_SAVE === false) {
			$this->set_default_taxonomy($post, $this->locale_taxonomy_slug);
			$this->set_section($post);	
			$this->set_valid_post_name($post);
			$this->set_unique_section_home($post);
			return $post;
		}
	}

	/**
	 * Get home sections with a given language term id
	 *
	 * @param Integer $lang_term_id
	 * @return Array
	 */
	public function get_home_section($lang_term_id) {
		// Set the get posts args to retrieve the home section
		$home_section_args = array(
			"post_type"=> $this->section_custom_post_type_slug, 
			"post_status"=> "publish", 
			'meta_query' => array(
				array(
					'key'=> $this->section_type_field_slug,
					'value'=> $this->section_type_home_field_value
				)
			),
			'tax_query' => array (
				array(
					'taxonomy' => $this->locale_taxonomy_slug,
					'field' => 'term_id',
					'terms' => $lang_term_id
				)
			)
		);
		$home_sections = get_posts($home_section_args);	
		return $home_sections;
	}

	/**
	 * Guarantee that only one section is defined as home
	 *
	 * @param object $post
	 * @return void
	 */
	public function set_unique_section_home ($post) {
		if ($this->section_custom_post_type_slug === $post->post_type) {
			$section_type = get_post_meta($post->ID, $this->section_type_field_slug, true);	

			$post_langs_terms = wp_get_post_terms($post->ID, $this->locale_taxonomy_slug);

			if (is_array($post_langs_terms)) {
				$home_sections = $this->get_home_section($post_langs_terms[0]->term_id);
				
				if ($section_type === $this->section_type_home_field_value && count($home_sections) > 1) {
					$this->WPP_SKIP_AFTER_SAVE = true;
					foreach ($home_sections as $home_section) {
						wp_update_post(array('ID' => $home_section->ID, 'meta_query' => array(array('key'=> $this->section_type_field_slug,'value'=> $this->section_type_home_field_value))));
					}
					wp_update_post( array('ID' => $post->ID, 'meta_query' => array(array('key'=> $this->section_type_field_slug,'value'=> $this->section_type_home_field_value))));
					$this->WPP_SKIP_AFTER_SAVE = false;
				}
			}
		}
	}

	/**
	 * Make sure that the post has not a name/slug conflicting with an existing page
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function set_valid_post_name ($post) {
		$get_valid_name = function($name) use (&$get_valid_name) {
			$page = get_page_by_path($name);
			if ($page) {
				$integer_append = 1;
				$segments = explode("-", $name);
				$last_segment = $segments[count($segments)-1];
				if (is_int($last_segment)) {
					$integer_append = $last_segment + 1;
					$name = str_replace("-$last_segment", "", $name);
				}
				
				$name .= "-$integer_append";
				return $get_valid_name($name);
			}
			return $name;
		};

		if(isset($post->post_name) && $post->post_name !== "") {
			$valid_post_name = $get_valid_name($post->post_name);
			if($valid_post_name !== $post->post_name) {
				$post->post_name = $valid_post_name;
				wp_update_post( array('ID' => $post->ID, 'post_name' => $valid_post_name));
			}
		}
	}

	/**
	 * Set the custom post type permalink for published posts
	 *
	 * @param string $permalink
	 * @param WP_Post $post
	 * @return string
	 */
	public function set_post_permalink ($permalink, $post) {
		// Get the pos types that supports `no_post_type_in_permalink`
		$no_post_type_in_permalink_types = get_post_types_by_support("no_post_type_in_permalink");

		// built in `post` also supports `no_post_type_in_permalink`
		$no_post_type_in_permalink_types[] = "post";
		$no_post_type_in_permalink_types[] = $this->section_custom_post_type_slug;	

		// Get the post types that supports `parent_section` and `section_in_permalink`
		$section_in_permalink_types = get_post_types_by_support(array("parent_section","section_in_permalink"));

		// built in `post` also supports `parent_section` and `section_in_permalink`
		$section_in_permalink_types[] = "post";		
		
		// This covers the section  that is home
		if ($this->section_custom_post_type_slug === $post->post_type) {
			$section_type = get_post_meta($post->ID, "section_type", true);	
			if ($section_type === $this->section_type_home_field_value ) {
				return "/";
			} 
		} 
		
		// This covers all custom post types with support for `post_type_after_section_in_permalink`
		if (in_array($post->post_type, $section_in_permalink_types)) {

			if ($post->post_parent) {
				$parent = get_post($post->post_parent);
			}

			// We only set the custom permalink when the the parent is already defined
			// In the normal wordpress post save flow this hook is fired twice, and in the second time
			// have the parent already defined, after running the `set_section` as a trigger for `wp_insert_post`
			if($parent) {
				// Some post type may support translations in url: /stories/my-conteudo/123 => /relatos/meu-conteudo/123
				$post_slug_translation = $this->get_post_url_slug($post);

				$section_type = get_post_meta($parent->ID, "section_type", true);	

				$parent_post_url_segment = $section_type === $this->section_type_home_field_value ? "/" : $parent->post_name;

				if (isset($_POST['new_title']) && $_POST['new_title'] !== "") {
					$post_name = sanitize_title($_POST['new_title']);
					$permalink = network_site_url("/$parent_post_url_segment/$post_slug_translation/$post_name/$post->ID");
				}
				elseif (isset($post->post_title)  && $post->post_title !== "") {
					$post_name = sanitize_title($post->post_title);
					$permalink = network_site_url("/$parent_post_url_segment/$post_slug_translation/$post_name/$post->ID");
				} 
				elseif (isset($post->post_name) && $post->post_name !== "") {
					$permalink = network_site_url("/$parent_post_url_segment/$post_slug_translation/$post->post_name/$post->ID");
				}  else {
					$permalink = network_site_url("/$parent_post_url_segment/$post_slug_translation/$post->ID");
				}
			}
		}
		// This covers the post types with no post type in permalink
		if (in_array($post->post_type, $no_post_type_in_permalink_types)) {
			$permalink = $this->get_permalink_with_no_post_type_in_it($post, $permalink);
		} 
		return $permalink;	
	}

	/**
	 * Set the permalink for post preview
	 *
	 * @param string $preview_link
	 * @param WP_Post $post
	 * @return string
	 */
	public function set_post_preview_permalink($preview_link, $post) {
		$permalink = $this->set_post_permalink($preview_link, $post);
		
		// Add the preview query
		$preview_link = add_query_arg( array("preview" => 'true'), $preview_link );
		return $preview_link;
	}


	/**
	 * Set the permalink for post preview
	 *
	 * @param string $preview_link
	 * @param WP_Post $post
	 * @return string
	 */
	public function set_page_permalink($permalink, $page_id ) {
		$page = get_page($page_id);
		$parent_section_id = $this->get_parent_section_id($page_id);

		// Get the url segment of the page section
		$section_url_segment = "";
		if($parent_section_id) {
			$parent_section = get_post($parent_section_id);
			$section_url_segment = "/$parent_section->post_name";
		} 

		// Get the url segment of the page ancestors
		$ancestors_url_segment = "";
		$ancestors = get_post_ancestors($page_id);
		if ($ancestors) {
			foreach ($ancestors as $ancestor_id) {
				$ancestor = get_page($ancestor_id);
				$ancestors_url_segment .= "/$ancestor->post_name";
			}
		}

		// Set the url segment of the page parents/ancestors
		$parents_url_segment = $section_url_segment.$ancestors_url_segment;

		// We only set the custom permalink when the the parent is already defined
		// In the normal wordpress post save flow this hook is fired twice, and in the second time
		// have the parent already defined, after running the `set_section` as a trigger for `wp_insert_post`
		
		if (isset($_POST['new_title']) && $_POST['new_title'] !== "") {
			$page_name = sanitize_title($_POST['new_title']);
			$permalink = network_site_url("$parents_url_segment/$page_name");
		}
		elseif (isset($page->post_title)  && $page->post_title !== "") {
			$page_name = sanitize_title($page->post_title);
			$permalink = network_site_url("$parents_url_segment/$page_name");
		} 
		elseif (isset($page->post_name) && $page->post_name !== "") {
			$permalink = network_site_url("$parents_url_segment/$page->post_name");
		}  else {
			$permalink = network_site_url("$parents_url_segment/$page->ID");
		}
		
		return $permalink;
	}

	/**
	 * Set the permalink for post preview
	 *
	 * @param string $preview_link
	 * @param WP_Post $post
	 * @return string
	 */
	public function set_page_preview_permalink($permalink, $page) {		
		$permalink = $this->set_page_permalink($permalink, $page);
		$preview_link = add_query_arg( array("preview" => 'true'), $preview_link );		
		return $preview_link;
	}

	/**
	 * Mke sure that the page has a valid post url/slug that does not conflicts with other custom post types having similar permalink structure 
	 *
	 * @param WP_Post $page
	 * @param string $permalink
	 * @return WP_Post
	 */
	public function after_save_page( $page_id, $page) {		
		$get_valid_name = function($name, $post_types) use (&$get_valid_name)  {
			if (!$post_types || count($post_types) === 0) {
				return $name;
			}
			foreach ($post_types as $key => $post_type) {
				$post = get_page_by_path($name, OBJECT, $post_type);
				if ($post) {
					$integer_append = 1;
					$segments = explode("-", $name);
					$last_segment = $segments[count($segments)-1];
					if (is_int($last_segment)) {
						$integer_append = $last_segment + 1;
						$name = str_replace("-$last_segment", "", $name);
					}
					$name .= "-$integer_append";
					return $get_valid_name($name, $post_types);
				}
				return $name;
			}
		};

		if(isset($page->post_name) && $page->post_name !== "") {
			$no_post_type_in_permalink_types = get_post_types_by_support("no_post_type_in_permalink");
			$no_post_type_in_permalink_types[] = $this->section_custom_post_type_slug;
			$valid_page_name = $get_valid_name($page->post_name, $no_post_type_in_permalink_types);
			if($valid_page_name !== $page->post_name) {
				$page->post_name = $valid_page_name;
				wp_update_post( array('ID' => $page->ID, 'post_name' => $valid_page_name));
			}
		}		

		return $page;
	}
	

	/**
	 * Get permalink with no post_type in it 
	 *
	 * @param WP_Post $post
	 * @param string $permalink
	 * @return string
	 */
	public function get_permalink_with_no_post_type_in_it($post, $permalink) {
		$post_type_obj = get_post_type_object($post->post_type);

		$post_url_slug = $post->post_type;
		if ($post_type_obj->rewrite !== false) {
			$post_url_slug = $post_type_obj->rewrite["slug"];
		} 
		
		$permalink = str_replace("/$post_url_slug/", "/", $permalink);
		return $permalink;
	}

	/**
	 * Get the post type rewrite slug translation
	 *
	 * @param string $post_type
	 * @return string
	 */
	public function get_post_url_slug($post) {
		$post_type_obj = get_post_type_object($post->post_type);

		$post_slug = $post->post_type;
		if ($post_type_obj->rewrite !== false) {
			$post_slug = $post_type_obj->rewrite["slug"];
		} 

		// Get the post `lang` list
		$lang_list = wp_get_post_terms($post->ID, $this->locale_taxonomy_slug, array("fields" => "all"));	
				
		// If the post has not such taxonomy assigned
		if ( !empty( $lang_list ) ) {
			// Should contains always one
			$lang = $lang_list[0]->slug;
			$post_slug_translation = $this->get_post_slug_url_translation($post_slug, $lang);
			return $post_slug_translation;
		}

		return $post_slug;
	}

	/**
	 * Translate a post type slug
	 *
	 * @param string $post_url_slug
	 * @param string $lang
	 * @return void
	 */
	public function get_post_slug_url_translation($post_url_slug, $lang) {
		$dictionary = get_option("wpp_post_url_translations", "{}");
		$dictionary = str_replace("\\", "", $dictionary);
		$dictionary = json_decode($dictionary, true);

		if (!isset($dictionary[$post_url_slug])) {
			return $post_url_slug;
		} elseif (!isset($dictionary[$post_url_slug][$lang])) {
			return $post_url_slug;
		} else {
			return $dictionary[$post_url_slug][$lang];
		}
	}

	/**
	 * Set post section, if it supports section
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function set_section($post) {
		$post_types_with_section = get_post_types_by_support("parent_section");

		// Built in `post` also supports parent_section
		$post_types_with_section[] = "post";

		if (in_array($post->post_type, $post_types_with_section)) {
			$parent_id = $this->get_parent_section_id($post->ID);

			if ($post->post_parent === 0 && !$parent_id) {				
				$sections = get_posts( array( 'post_type' => $this->section_custom_post_type_slug, 'orderby'  => 'id', 'order' => 'ASC'));		
				if (count($sections) > 0) {
					$parent_id = $sections[0]->ID;
				}							
			} 

			// If we could find an available parent and this is not already the post parent
			// set this parent as parent of the post
			if($parent_id && $post->post_parent !== $parent_id) {
				wp_update_post( array('ID' => $post->ID, 'post_parent' => $parent_id));
				$post->post_parent = $parent_id;		
			}
		}
	}

	/**
	 * Get parent section id
	 *
	 * @param Integer $post_id
	 * @return Integer|null
	 */
	public function get_parent_section_id($post_id) {
		$parent_id = get_post_meta($post_id, $this->section_custom_post_type_slug, true);	
		$parent_id = is_array($parent_id) && count($parent_id)> 0 ? $parent_id[0] : $parent_id;
		if (isset($parent_id)) {
			$parent_id = intval($parent_id);
		}
		return $parent_id;
	}

	/**
	 * Set default taxonomy for a fresh created post
	 *
	 * @param WP_Post|Integer $post_or_id
	 * @param string $taxonomy_slug
	 * @return void
	 */
	public function set_default_taxonomy($post_or_id, $taxonomy_slug) {
		$post_id = is_integer($post_or_id) ? $post_or_id : $post_or_id->ID;

		$post = get_post($post_id);

		// Get the possible taxonomies to this kind of post
		$taxonomies = get_object_taxonomies($post);
		
		// If the post supports the passed taxonomy slug
		// we will make sure it has one assigned
		if (!in_array($taxonomy_slug, $taxonomies)) {
			$term_list = wp_get_post_terms($post_id, $taxonomy_slug, array("fields" => "all"));	
			
			// If the post has not such taxonomy assigned
			if ( empty( $term_list)) {				
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
	 * Add menu support to the theme
	 *
	 * @return void
	 */
	public function add_supports() {
		add_theme_support( 'menus' );
		add_action( 'init', 'register_wpp_menus' );
		function register_wpp_menus() {
			register_nav_menus(
				array(
					'primary-menu' => __( 'Primary Menu' ),
					'secondary-menu' => __( 'Secondary Menu' )
				)
			);
		}

		add_theme_support( 'post-thumbnails');
	}

	/**
	 * Convert an image url about to be inserted in post html to a base 64 image representation
	 *
	 * @param string $html
	 * @param integer $id
	 * @param string $caption
	 * @param string $title
	 * @param string $align
	 * @param string $url
	 * @return string
	 */
	public function set_image_before_send_to_editor ($html, $id, $caption, $title, $align, $url) {
		$src_data  = wp_get_attachment_image_src( $id, $size, false );
		if(is_array($src_data)) {
			$src_url =  $src_data[0];
			$relative_src = str_replace(network_home_url(), "", $src_url);
			$local_path = $_SERVER["DOCUMENT_ROOT"].$relative_src;			
			$data = file_get_contents($local_path);
			if ($data) {
				$type = pathinfo($relative_src, PATHINFO_EXTENSION);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);	
				$title = $title === "" ? $caption :  $title;

				// Set the new html out put for the image with base64 src			
				$html = "<div id='post-$id media-$id' class='align$align'><img src='$base64' alt='$title' width='$src_data[1]' height='$src_data[2]' /></div>";
				return $html;
			}
			return $html;
		}

		return $html;
	}
 }

 // Start the plugin
 new WpWebAppTheme();








  
