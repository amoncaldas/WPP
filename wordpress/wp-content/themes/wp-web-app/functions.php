<?php
/**
 * Theme: FAM WebApp *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

 class WpWebAppTheme {

	public $lang_tax_slug = "lang";
	public $section_custom_post_type_slug = "section";

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
		add_action('init', array($this, 'register_custom_types'));
		add_action('admin_menu', array($this, 'add_language_admin_menu'));
		add_action( 'rest_api_init', array($this, 'register_wp_api_custom_fields'));
		add_filter('acf/fields/google_map/api', array($this,'acf_google_map_api'));

		// Customize permalink
		add_filter('post_type_link', array($this, 'set_post_permalink'), 10, 2);
		add_filter('preview_post_link', array($this, 'set_post_preview_permalink'), 10, 2);
		add_filter('page_link', array($this, 'set_page_permalink'), 10, 2);
		add_filter('preview_page_link', array($this, 'set_page_preview_permalink'), 10, 2);
		add_filter('image_send_to_editor', array($this, 'set_image_before_send_to_editor'), 10, 6 );		
	}

		/**
	 * Register user meta data to get and update callbacks user wp rest api
	 *
	 * @return void
	 */
	public function register_wp_api_custom_fields() {
		register_rest_field( $this->section_custom_post_type_slug, 'places',
			array(
				'get_callback'  => function ($post, $field_name, $request) {
					return $this->resolve_places($post, $field_name, $request);
				},
				'schema' => null,
			)
		);
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
					$langs = wp_get_post_terms($place_id, $this->lang_tax_slug);
					$places[$place_id]["lang"] = count($langs) > 0 ? $langs[0]->slug : null;
				}
			}
		}
		return $places;
	}	

	/**
	 * Set the ACF custom fields google map api key from `wpp_google_maps_api_key` option value
	 *
	 * @param Array $api
	 * @return Array
	 */
	public function acf_google_map_api( $api ){	
		$api['key'] = get_option("wpp_google_maps_api_key");
		return $api;		
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
				1 => 'editor',
				2 => 'thumbnail',
				3 => 'revisions',
			),
		);

		register_post_type( $this->section_custom_post_type_slug , $section_args );

		$lang_tax_args = array (
			'name' => 'languages',
			'label' => 'Language',
			'singular_label' => 'Language',
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
		register_taxonomy( $this->lang_tax_slug, null, $lang_tax_args );
	}

	/**
	 * Add a admin menu entry to manage the languages
	 *
	 * @return void
	 */
	public function add_language_admin_menu(){
		global $submenu;
		$submenu['options-general.php'][] = array( 'Language', 'manage_options', "/wp-admin/edit-tags.php?taxonomy=".$this->lang_tax_slug);
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
		$this->set_default_taxonomy($post, $this->lang_tax_slug);
		$this->set_section($post);	
		$this->set_valid_post_name($post);
		return $post;
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

				if (isset($_POST['new_title']) && $_POST['new_title'] !== "") {
					$post_name = sanitize_title($_POST['new_title']);
					$permalink = network_site_url("/$parent->post_name/$post_slug_translation/$post_name/$post->ID");
				}
				elseif (isset($post->post_title)  && $post->post_title !== "") {
					$post_name = sanitize_title($post->post_title);
					$permalink = network_site_url("/$parent->post_name/$post_slug_translation/$post_name/$post->ID");
				} 
				elseif (isset($post->post_name) && $post->post_name !== "") {
					$permalink = network_site_url("/$parent->post_name/$post_slug_translation/$post->post_name/$post->ID");
				}  else {
					$permalink = network_site_url("/$parent->post_name/$post_slug_translation/$post->ID");
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
		$lang_list = wp_get_post_terms($post->ID, $this->lang_tax_slug, array("fields" => "all"));	
				
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
		$dictionary = [
			"story" => [ 
				"pt-br" => "relatos",
				"en-us" => "stories"
			]			
		];

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








  