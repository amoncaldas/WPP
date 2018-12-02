<?php
/**
 * Theme: FAM WebApp *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 */

 class FamWebApp {

	function __construct () {
		$this->set_output();
		$this->register_hooks();		
  }
  
  /**
   * Set the ouput of the theme
   *
   * @return void
   */
  public function set_output () {
    if (!is_admin() && strrpos($_SERVER["REQUEST_URI"], "wp-json/") === false ) {
			$webapp = file_get_contents("/var/www/webapp/index.html");
			$webapp = str_replace("=static/", "=/static/", $webapp);
			$webapp = str_replace("{{title}}", "Fazendo as Malas", $webapp);
      echo $webapp;
      exit;
		}
  }
	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_insert_post', array($this,'after_insert_content'), 10, 2 );
		add_filter('post_type_link', array($this, 'set_permalink'), 10, 2);
		add_filter('preview_post_link', array($this, 'set_preview_permalink'), 10, 2);
	}

	public function after_insert_content( $content_id, $content) {
		if ($content->post_type === "page") {
			return $this->after_insert_page($content_id, $content);
		} else {
			return $this->after_insert_post($content_id, $content);
		}
	}

	/**
	 * run custom adjustments after a new post is created
	 *
	 * @param Integer $post_id
	 * @param WP_Post $post
	 * @return void
	 */
	public function after_insert_post( $post_id, $post ) {
		$this->set_default_taxonomy($post, "lang");
		$this->set_default_section($post);	
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
	public function set_permalink ($permalink, $post) {
		// Get the pos types that supports `no_post_type_in_permalink`
		$no_post_type_in_permalink_types = get_post_types_by_support("no_post_type_in_permalink");

		// Get the post types that supports `parent_section` and `section_in_permalink`
		$section_in_permalink_types = get_post_types_by_support(array("parent_section","section_in_permalink"));				
		
		// This covers the post types with no post type in permalink
		if (in_array($post->post_type, $no_post_type_in_permalink_types)) {
			$permalink = $this->get_permalink_with_no_post_type_in_it($post, $permalink);
		} 
		// This covers all custom post types with support for `post_type_after_section_in_permalink`
		elseif (in_array($post->post_type, $section_in_permalink_types)) {

			$parent = get_post($post->post_parent);

			// We only set the custom permalink when the the parent is already defined
			// In the normal wordpress post save flow this hook is fired twice, and in the second time
			// have the parent already defined, after running the `set_default_section` as a trigger for `wp_insert_post`
			if($parent) {
				// Some post type may support translations in url: /stories/meu-conteudo/123 => /relatos/meu-conteudo/123
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
		return $permalink;	
	}

	/**
	 * Mke sure that the page has a valid post url/slug that does not conflicts with other custom post types having similar permalink structure 
	 *
	 * @param WP_Post $post
	 * @param string $permalink
	 * @return string
	 */
	public function after_insert_page( $page_id, $page) {		
		$get_valid_name = function($name, $post_types) use (&$get_valid_name)  {
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
		$post_url_slug = $post_type_obj->rewrite["slug"];
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
		$post_slug = $post_type_obj->rewrite["slug"];

		// Get the post `lang` list
		$lang_list = wp_get_post_terms($post->ID, "lang", array("fields" => "all"));	
				
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
	 * Set the permalink for post preview
	 *
	 * @param string $preview_link
	 * @param WP_Post $post
	 * @return string
	 */
	public function set_preview_permalink($preview_link, $post) {
		$permalink = $this->set_permalink($preview_link, $post);
		
		// Add the preview query
		$preview_link = add_query_arg( array("preview" => 'true'), $preview_link );
		return $preview_link;
	}

	/**
	 * Set post default section, if supports section
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function set_default_section($post) {
		$post_types_with_section = get_post_types_by_support("parent_section");

		if (in_array($post->post_type, $post_types_with_section)) {

			if ($post->post_parent === 0) {								
				$sections = get_posts( array( 'post_type' => 'section', 'orderby'  => 'id', 'order' => 'ASC'));
		
				if (count($sections) > 0) {
					wp_update_post( array('ID' => $post->ID, 'post_parent' => $sections[0]->ID));
					$post ->post_parent = $sections[0]->ID;
				}
			}
		}
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
 }

 // Start the plugin
 new FamWebApp();








  
