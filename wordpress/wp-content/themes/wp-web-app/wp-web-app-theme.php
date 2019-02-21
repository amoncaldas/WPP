<?php
/**
 * Theme: FAM WebApp *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

 class WpWebAppTheme {
	public $section_type_field_slug = "section_type";
	public $section_type_home_field_value = "home";
	public $WPP_SKIP_AFTER_SAVE = false;
	

	public function __construct () {
		$this->register_hooks();
	}

	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action('wp_insert_post', array($this,'after_save_content'), 10, 2 );
		add_filter('request', array($this, 'set_feed_types'));
		add_action('init', array($this, 'make_sure_locale_exists'), 11);
		add_action('init', array($this, 'make_sure_home_section_exists'), 12);
		add_action('rest_api_init', array($this, 'on_rest_api_init'));
		add_filter('image_send_to_editor', array($this, 'set_image_before_send_to_editor'), 10, 6 );		

		// Customize permalink
		add_filter('post_type_link', array($this, 'set_post_permalink'), 10, 2);
		add_filter('preview_post_link', array($this, 'set_post_preview_permalink'), 10, 2);
		add_filter('page_link', array($this, 'set_page_permalink'), 10, 2);
		add_filter('preview_page_link', array($this, 'set_page_preview_permalink'), 10, 2);
	}

	/**
	 * Check if home section exists. If does not exist, create it
	 *
	 * @return void
	 */
	public function make_sure_locale_exists() {
		if (is_admin()) {
			$en_us_locale = get_term_by('slug', 'en-us', LOCALE_TAXONOMY_SLUG);	
			if (!$en_us_locale) {
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
	
					// Get the home for a given language. If does not exist, create it
					$home_sections = $this->get_home_section($lang_term->term_id);
	
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
	 * Register user meta data to get and update callbacks user wp rest api
	 *
	 * @return void
	 */
	public function on_rest_api_init() {
		// Apply the locale taxonomy filter to when running rest api requests
		add_action( 'pre_get_posts', array($this, 'apply_locale_filter_get_posts') );

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
			register_rest_field($post_type, 'places',
				array(
					'get_callback'  => function ($post, $field_name, $request) {
						return $this->resolve_places($post, $field_name, $request);
					},
					'schema' => null,
				)
			);
		}
	
	}

	public function attach_post_locale ($post) {
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
	public function apply_locale_filter_get_posts($query) {		
		$public_post_types = get_post_types(array("public"=>true));
		unset($public_post_types["attachment"]);
		$post_type = $query->query["post_type"];
		if ($post_type !== SECTION_POST_TYPE && in_array($post_type, $public_post_types)) {
			$request_locale = get_request_locale();
			$tax_query = array (
				array(
					'taxonomy' => LOCALE_TAXONOMY_SLUG,
					'field' => 'slug',
					'terms' => [$request_locale, "neutral"]
				)
			);
			$query->set( 'tax_query', $tax_query );
		}
	}

	/**
	 * Resolve the slide images gallery data based on the ids postmeta
	 *
	 * @param Array $post_arr
	 * @param string $field_name
	 * @param Object $request
	 * @return array of data
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
				}
			}
		}
		return $places;
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
	public function get_home_section($lang_term_id) {
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
				$home_sections = $this->get_home_section($post_langs_terms[0]->term_id);
				
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
		
		// This covers the section  that is home
		if (SECTION_POST_TYPE === $post->post_type) {
			$section_type = get_post_meta($post->ID, "section_type", true);	
			if ($section_type === SECTION_POST_HOME_FIELD_VALUE ) {
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
			$no_post_type_in_permalink_types[] = SECTION_POST_TYPE;
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
		$lang_list = wp_get_post_terms($post->ID, LOCALE_TAXONOMY_SLUG, array("fields" => "all"));	
				
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
		$dictionary = get_option("wpp_post_type_translations", "{}");
		$dictionary = str_replace("\\", "", $dictionary);
		$dictionary = json_decode($dictionary, true);

		if (!isset($dictionary[$post_url_slug])) {
			return $post_url_slug;
		} elseif (!isset($dictionary[$post_url_slug][$lang])) {
			return $post_url_slug;
		} elseif (!isset($dictionary[$post_url_slug][$lang]["path"])) {
			return $post_url_slug;
		}
		else {
			return $dictionary[$post_url_slug][$lang]["path"];
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
				$sections = get_posts( array( 'post_type' => SECTION_POST_TYPE, 'orderby'  => 'id', 'order' => 'ASC'));		
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
		$parent_id = get_post_meta($post_id, SECTION_POST_TYPE, true);	
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
 











  
