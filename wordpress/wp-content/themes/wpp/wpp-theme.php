<?php
/**
 * Theme: WPP
 * Author: Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

// Require WP_Rest_Cache_Plugin caching class
use \WP_Rest_Cache_Plugin\Includes\Caching\Caching;

class WpWebAppTheme {
	public $section_type_field_slug = "section_type";
	public $section_type_home_field_value = "home";
	public $WPP_SKIP_AFTER_SAVE = false;
	public $WPP_SKIP_BEFORE_UPDATE = false;

	public function __construct () {
		if (!defined('WPP_API_NAMESPACE')) {
			define('WPP_API_NAMESPACE', "wpp/v1");
		}
		$this->register_hooks();
	}

	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action('wp_insert_post', array($this,'after_save_content'), 10, 2 );
		add_action('save_post', array($this,'wpp_clear_listing_wp_rest_cache'), 10, 2 );
		add_filter('request', array($this, 'set_feed_types'));
		add_action('init', array($this, 'make_sure_locale_exists'), 11);
		add_action('init', array($this, 'make_sure_home_section_exists'), 12);
		add_action('rest_api_init', array($this, 'on_rest_api_init'));
		add_filter('image_send_to_editor', array($this, 'set_image_before_send_to_editor'), 10, 6 );		

		// Customize permalinks
		add_filter('post_type_link', array($this, 'set_post_permalink'), 10, 2);
		add_filter('post_link', array($this, 'set_post_permalink'), 10, 2);
		add_filter('preview_post_link', array($this, 'set_post_preview_permalink'), 10, 2);
		add_filter('page_link', array($this, 'set_page_permalink'), 10, 2);
		add_filter('preview_page_link', array($this, 'set_page_preview_permalink'), 10, 2);
		add_action("pre_post_update", array($this, 'before_update_draft_post'), 9, 2);
		add_action('admin_enqueue_scripts', array($this, 'may_disable_autosave'));
		add_filter('rest_prepare_comment', array($this, 'filter_rest_prepare_comment'), 10, 3 );		
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

	/**
	 * Check if the auto save should be skipped
	 *
	 * @param Object $post
	 * @return null
	 */
	public function may_disable_autosave() {
		$skip_auto_save = get_option("wpp_disable_auto_save", "no");

		if ($skip_auto_save === "yes") {
			wp_dequeue_script( 'autosave' );
		}	
	}

	/**
	 * If it is a draft post and it has an import_id meta
	 * create a new post with the defined import id and
	 * remove the auto generated one
	 *
	 * @param Array $post_id
	 * @param Array $data
	 * @return Array $data - new post created or the existing one
	 */
	public function before_update_draft_post($post_id, $data) {
		$skip_auto_save = get_option("wpp_disable_auto_save", "no");

		// We only process this imported post id stuff if auto save is disabled
		if ($skip_auto_save === "yes" && $this->WPP_SKIP_BEFORE_UPDATE === false) {			
			$import_id = 0;

			// If the post is a saved in the db as draft
			$post_in_db = get_post($post_id);

			if ($data["post_status"] === "draft") {
				$import_id = intval(get_acf_post_request_field_value("import_id", true));
			}
	
			// If the import id was found and it is different from the current post id
			// If the import post already exists (wordpress fires this callback multiple times)			
			if ($import_id > 0 && $post_id !== $import_id) {
				$post_exists = get_post($import_id);
	
				// return the already created post from database
				if ($post_exists && $post_exists->post_name === $data["post_name"] && $post_exists->post_type === $data["post_type"]) {
					$data = $post_exists->to_array();
					return $data;
				}
				else { // if not, create a new post using the current post data
					$data["import_id"] = $import_id;
				
			    // Remove the id to trigger a creation of a new post
					unset($data["ID"]);
					
					// Avoid infinity loop
					remove_action("pre_post_update", array($this, 'before_update_draft_post'), 9, 2);
					$this->WPP_SKIP_AFTER_SAVE = true;

					$data["post_name"] = sanitize_title($data["post_title"]);

					// Create new clone post with the specified id
					
					$inserted_id = wp_insert_post($data);
			
			    // Redefine the id from the inserted post
				  $data["ID"] = $import_id;

					// This is not straight forward but we have
					// to change the post id in the request
					// to the one we have created
					// so that all the following wordpress
					// actions use the new post ID
					$_REQUEST["post_ID"] = $import_id;
					$_POST["post_ID"] = $import_id;
					$_POST["_acf_post_id"] = $import_id;		
					$_REQUEST["_acf_post_id"] = $import_id;			

					// Delete the draft created automatically
					// by wordpress, we don need it any more
					$this->WPP_SKIP_BEFORE_UPDATE = true;
					wp_delete_post($post_id, true);
				} 
			}
		}

		return $data;
	}

	/**
	 * Make sure the mandatory locales exists exists. If any one required does not exist, create it
	 *
	 * @return void
	 */
	public function make_sure_locale_exists() {
		if (is_admin()) {
			$locale_terms = get_terms( array('taxonomy' => LOCALE_TAXONOMY_SLUG, 'hide_empty' => false, 'orderby' => 'id', 'order' => 'ASC'));
			if (!$locale_terms || count($locale_terms) === 0) {
				wp_insert_term("en-us", LOCALE_TAXONOMY_SLUG, array("description"=> "English", "slug"=> "en-us"));
			}
			$neutral_locale = get_term_by('slug', 'neutral', LOCALE_TAXONOMY_SLUG);	
			if (!$neutral_locale) {
				wp_insert_term("neutral", LOCALE_TAXONOMY_SLUG, array("description"=> "Neutral", "slug"=> "neutral"));
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
			$available_locale_terms = get_terms( array('taxonomy' => LOCALE_TAXONOMY_SLUG, 'hide_empty' => false, 'orderby' => 'id', 'order' => 'ASC'));
	
			if (is_array($available_locale_terms)) {
				foreach ($available_locale_terms as $lang_term) {
					if ($lang_term->slug === "neutral") {
						continue;
					}
	
					// Get the home for a given language. If it does not exist, create it
					$home_sections = $this->get_home_sections($lang_term->term_id);
	
					if (count($home_sections) === 0) {
						$section_title = "Home | ". $lang_term->name;
						$home_section_id = wp_insert_post(
							array(
								"post_type"=> SECTION_POST_TYPE, 
								"post_status"=> "publish",
								"post_author"=> 1, // 1 is always the admin, the first user created
								"post_title"=> $section_title,
								"meta_input"=> array(
									SECTION_TYPE_FIELD_SLUG => SECTION_POST_HOME_FIELD_VALUE
								)
							)
						);
						// Assign the default (the first one available)
						wp_set_post_terms($home_section_id, [$lang_term->term_id], LOCALE_TAXONOMY_SLUG);
					}
				}				
			}
		}
	}

	/**
	 * Register rest api hooks to treat locale and resolve custom data
	 *
	 * @return void
	 */
	public function on_rest_api_init() {
		// Apply the locale taxonomy filter to when running rest api requests
		add_action( 'pre_get_posts', array($this, 'apply_wpp_filters_pre_get_posts') );

		$public_post_types = get_post_types(array("public"=>true));
		unset($public_post_types["attachment"]);

		foreach ($public_post_types as $post_type) {
			// Add a custom field for section
			register_rest_field($post_type, 'locale',
				array(
					'get_callback'  => function ($post, $field_name, $request) {
						return $this->get_post_locale( $post);
					},
					'schema' => null,
				)
			);
			register_rest_field($post_type, 'places',
				array(
					'get_callback'  => function ($post, $field_name, $request) {
						return $this->resolve_places($post, $field_name, $request);
					},
					'schema' => null,
				)
			);

			register_rest_field($post_type, 'author_member',
				array(
					'get_callback'  => function ($post, $field_name, $request) {
						return $this->resolve_author_member($post, $field_name, $request);
					},
					'schema' => null,
				)
			);		
		}
	}

	// define the rest_prepare_comment callback 
	public function filter_rest_prepare_comment( $response, $comment, $request ) { 
		if (!isset($response->data["author_member"])) {
			$response->data["author_member"] = $this->get_author_member($comment->user_id);
		}
		return $response; 
	}


	/**
	 * Attach post locale slug to post object
	 *
	 * @param Array $post
	 * @return String - locale slug
	 */
	public function get_post_locale ($post) {
		$terms = get_the_terms( $post["id"], LOCALE_TAXONOMY_SLUG );
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
	public function apply_wpp_filters_pre_get_posts($query) {		

		//TODO: Change to: select post type by locale taxonomy support!
		$public_post_types = get_post_types(array("public"=>true));
		unset($public_post_types["attachment"]);

		$post_type = $query->query["post_type"];

		if ($post_type !== SECTION_POST_TYPE) {
			// Pages are found without id, quering by slug. In this case, we 
			if (in_array($post_type, $public_post_types)) {
				$request_locale = get_request_locale();
				$tax_query = [];
				if ($post_type !== "page" || !isset($_GET["slug"])) {
					$locale_query = array (
						array(
							'taxonomy' => LOCALE_TAXONOMY_SLUG,
							'field' => 'slug',
							'terms' => [$request_locale, "neutral"]
						)
					);
					$tax_query[] = $locale_query;
				}
				// Build the categories query, if passed
				if (isset($_GET["cats"])) {
					$categories = strpos($_GET["cats"], ",") > 1 ? explode(",", $_GET["cats"]) : [$_GET["cats"]];					
					$cat_query  = array(
						'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => $categories
					);
					$tax_query[] = $cat_query;
				}

				// Build the tags query, if passed
				if (isset($_GET["p_tags"])) {
					$tags = strpos($_GET["p_tags"], ",") > 1 ? explode(",", $_GET["p_tags"]) : [$_GET["p_tags"]];					
					$tag_query  = array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => $tags
					);
					$tax_query[] = $tag_query;
				}

				// Assign the tax query
				$query->set( 'tax_query', $tax_query );
			}
			
	
			$post_types_with_section = get_post_types_by_support("parent_section");
			if (in_array($post_type, $post_types_with_section) && $_GET["parent_id"]) {
				$query->set('post_parent', $_GET["parent_id"]);				
			}
		}
	}

	/**
	 * Resolve the the post places
	 *
	 * @param Array $post_arr
	 * @param string $field_name
	 * @param Object $request
	 * @return array of palces
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
	 * Resolve the author linked member
	 *
	 * @param Array $post_arr
	 * @param string $field_name
	 * @param Object $request
	 * @return Array
	 */
	public function resolve_author_member($post_arr, $field_name, $request) {		
		$author_id = get_post_field( 'post_author', $post_arr["id"]);
		$data = $this->get_author_member($author_id);
		return $data;
	}	

	/**
	 * Resolve the author linked member
	 *
	 * @param Array $post_id
	 * @return Array
	 */
	public function get_author_member($author_id) {
		$linked_member_id = get_user_meta($author_id, "linked_member", true);

		if (is_array($linked_member_id) && count($linked_member_id) === 0) {
			return;
		}
		$linked_member_id = is_array($linked_member_id) ? $linked_member_id[0] : $linked_member_id;
		
		if (isset($linked_member_id) && $linked_member_id > 0) {			
			$author_member = get_post($linked_member_id);
			if ($author_member) {
				$data = [
					"title" => $author_member->post_title,
					"content" => strip_tags(apply_filters('the_content', $author_member->post_content)),
					"featured_thumb_url" => get_the_post_thumbnail_url($author_member->ID, "thumbnail"),
					"link" => get_the_permalink($author_member->ID)
				];
				return $data;
			}
		} 
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
			$custom_post_types[] = SECTION_POST_TYPE;	
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
			$this->set_default_taxonomy($post, LOCALE_TAXONOMY_SLUG);
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
	public function get_home_sections($lang_term_id) {
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
		if (SECTION_POST_TYPE === $post->post_type) {
			$section_type = get_post_meta($post->ID, SECTION_TYPE_FIELD_SLUG, true);	

			$post_langs_terms = wp_get_post_terms($post->ID, LOCALE_TAXONOMY_SLUG);

			if (is_array($post_langs_terms)) {
				$home_sections = $this->get_home_sections($post_langs_terms[0]->term_id);
				
				if ($section_type === SECTION_POST_HOME_FIELD_VALUE && count($home_sections) > 1) {
					$this->WPP_SKIP_AFTER_SAVE = true;
					foreach ($home_sections as $home_section) {
						wp_update_post(array('ID' => $home_section->ID, 'meta_query' => array(array('key'=> SECTION_TYPE_FIELD_SLUG,'value'=> SECTION_POST_HOME_FIELD_VALUE))));
					}
					wp_update_post( array('ID' => $post->ID, 'meta_query' => array(array('key'=> SECTION_TYPE_FIELD_SLUG,'value'=> SECTION_POST_HOME_FIELD_VALUE))));
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
		$no_post_type_in_permalink_types[] = SECTION_POST_TYPE;	

		// Get the post types that supports `parent_section` and `section_in_permalink`
		$section_in_permalink_types = get_post_types_by_support(array("parent_section","section_in_permalink"));

		// built in `post` also supports `parent_section` and `section_in_permalink`
		$section_in_permalink_types[] = "post";		

		$match = false;
		
		// This covers the section  that is home
		if (SECTION_POST_TYPE === $post->post_type) {
			$section_type = get_post_meta($post->ID, "section_type", true);	
			if ($section_type === SECTION_POST_HOME_FIELD_VALUE ) {
				$match = true;
				$locale = $this->get_post_locale( $post->to_array());
				return network_site_url("/?l=$locale");
			} 
		} 

		// Translate the post type url
		$post_type_obj = get_post_type_object($post->post_type);
		$post_slug = $post->post_type;
		if ($post_type_obj->rewrite !== false) {
			$post_slug = $post_type_obj->rewrite["slug"];
		} 
		$post_slug_translation = $this->get_post_url_slug($post);		
		$permalink = str_replace("/$post_slug/", "/$post_slug_translation/", $permalink);
		
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

				$parent_post_url_segment = $section_type === SECTION_POST_HOME_FIELD_VALUE ? "/" : $parent->post_name;

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
				$match = true;
			}
		}
		// This covers the post types with no post type in permalink
		if (in_array($post->post_type, $no_post_type_in_permalink_types)) {
			$permalink = $this->get_permalink_with_no_post_type_in_it($post, $permalink);
			$match = true;
		}  
		// Content types that are not section or page can not have permalink without id 
		if (!$match && $post->post_type !== "page") {
			$permalink = trim($permalink, "/");
			$link_parts = explode("/", $permalink);
			$last_part = ($link_parts[count($link_parts) - 1]);
			$permalink = is_numeric($last_part)? $permalink : $permalink."/".$post->ID;
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
	 * Set the permalink for a page
	 *
	 * @param string $permalink
	 * @param Integer $page_id
	 * @return string
	 */
	public function set_page_permalink($permalink, $page_id ) {
		$page = get_page($page_id);
		$parent_section_id = $this->get_parent_section_id($page_id);

		// Get the url segment of the page section
		$section_url_segment = "";
		if($parent_section_id) {
			$parent_section = get_post($parent_section_id);
			$section_type = get_post_meta($parent_section_id, "section_type", true);	
			$section_url_segment = $section_type === SECTION_POST_HOME_FIELD_VALUE ? "/" : "/$parent_section->post_name";
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
	 * @param Integer $page_id
	 * @param WP_Post $page
	 * @return WP_Post page
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
		$parent_id = $this->get_parent_section_id($page_id);
		if ($parent_id > 0) {
			update_post_meta($page_id, SECTION_POST_TYPE, $parent_id);
		}

		if(isset($page->post_name) && $page->post_name !== "") {
			$no_post_type_in_permalink_types = get_post_types_by_support("no_post_type_in_permalink");
			$no_post_type_in_permalink_types[] = SECTION_POST_TYPE;
			$valid_page_name = $get_valid_name($page->post_name, $no_post_type_in_permalink_types);
			if($valid_page_name !== $page->post_name) {
				$page->post_name = $valid_page_name;
				$this->WPP_SKIP_BEFORE_UPDATE = true;
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
		$lang_list = wp_get_post_terms($post->ID, LOCALE_TAXONOMY_SLUG, array("fields" => "all"));	
				
		// If the post has not such taxonomy assigned
		if ( !empty( $lang_list ) ) {
			// Should contains always one
			$lang = $lang_list[0]->slug;
			$post_slug_translation = get_post_path_translation($post_slug, $lang);
			return $post_slug_translation;
		}

		return $post_slug;
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
				$sections = get_posts( array( 'post_type' => SECTION_POST_TYPE, 'orderby'  => 'id', 'order' => 'ASC'));		
				if (count($sections) > 0) {
					$parent_id = $sections[0]->ID;
				}							
			} 

			// If we could find an available parent and this is not already the post parent
			// set this parent as parent of the post
			if($parent_id && $post->post_parent !== $parent_id) {
				$this->WPP_SKIP_AFTER_SAVE = true;
				$this->WPP_SKIP_BEFORE_UPDATE = true;
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
		$parent_id = get_acf_post_request_field_value(SECTION_POST_TYPE, true);
		if (!$parent_id) {
			$parent_id = get_post_meta($post_id, SECTION_POST_TYPE, true);	
			$parent_id = is_array($parent_id) && count($parent_id)> 0 ? $parent_id[0] : $parent_id;
		}
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
		$src_data  = wp_get_attachment_image_src( $id, null, false );
		
		$insert_image_as_base64_in_editor = get_option("wpp_insert_image_as_base64_in_editor", "no");

		if(is_array($src_data) && $insert_image_as_base64_in_editor === "yes") {
			$src_url =  $src_data[0];
			$relative_src = str_replace(network_home_url(), "", $src_url);
			$local_path = $_SERVER["DOCUMENT_ROOT"].$relative_src;			
			$data = file_get_contents($local_path);
			if ($data) {
				$type = pathinfo($relative_src, PATHINFO_EXTENSION);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);	
				$title = $title === "" ? $caption :  $title;

				preg_match_all( '@src="([^"]+)"@' , $html, $match );
				if (isset($match[0]) && isset($match[0][0])) {
					$src = $match[0][0];
					$src = str_replace("src=", "", $src);
					$src = trim($src, '"');
					$html = str_replace($src, $base64, $html);
				}

				// Set the new html out put for the image with base64 src			
				// $html = "<div id='post-$id media-$id' class='align$align'><img src='$base64' alt='$title' width='$src_data[1]' height='$src_data[2]' /></div>";
				return $html;
			}
			return $html;
		}

		return $html;
	}
 }

// Start theme functions class
 $wpWebAppTheme = new WpWebAppTheme();
 











  
