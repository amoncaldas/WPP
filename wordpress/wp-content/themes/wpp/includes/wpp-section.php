<?php

class WpWebAppSection {

	public $WPP_SKIP_AFTER_SECTION_SAVE = false;

	public function __construct () {
		define('SECTION_POST_TYPE', "section");
		define('SECTION_TYPE_FIELD_SLUG', "section_type");
		define('SECTION_POST_HOME_FIELD_VALUE', "home");
		$this->register_section();
		$this->register_hooks();
	}

	public function register_hooks() {
		add_filter( 'parse_query', array($this,'wpp_apply_admin_section_filter'));
		add_action( 'restrict_manage_posts', array($this,'wpp_create_admin_sections_filter') );
		add_filter('manage_posts_columns', array($this,'wpp_section_admin_columns_head'), 10);
		add_action('manage_posts_custom_column', array($this,'wpp_admin_section_column_content'), 11, 2);
		add_action('init', array($this,'make_sure_home_section_exists'), 12);	
		add_action('wp_insert_post', array($this,'after_save_section'), 10, 2 );
	}
	
	/**
		* Register custom types section and lang
		*
		* @return void
		*/
	public function register_section () {
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
	}
	
	/**
		* Apply admin section filter selected
		*
		* @param Object $query
		* @return void
		*/
	public function wpp_apply_admin_section_filter( $query ) {
		global $pagenow;
		if ( is_admin() && $pagenow == 'edit.php' && !empty($_GET['wpp_parent_section'])) {
			$post_types_with_section = get_post_types_by_support("parent_section");
			$post_types_with_section[] = "post";
			if (in_array($query->query_vars['post_type'], $post_types_with_section)) {
				$query->query_vars['post_parent'] = $_GET['wpp_parent_section'];
			}
		}
	}
	
	
	/**
		* Create admin sections filter
		*
		* @return void
		*/
	public function wpp_create_admin_sections_filter() {
		global $wpdb;
		$post_types_with_section = get_post_types_by_support("parent_section");
		$post_types_with_section[] = "post";
		$current_post_type = wpp_get_current_post_type();
	
		if (isset($current_post_type) && in_array($current_post_type, $post_types_with_section)) {		
			$sql = "SELECT ID, post_title FROM ".$wpdb->posts." WHERE post_type = '".SECTION_POST_TYPE."' AND post_parent = 0 AND post_status = 'publish' ORDER BY post_title";
			$parent_sections = $wpdb->get_results($sql, OBJECT_K);
			$select = '<select name="wpp_parent_section"><option value="">All '.SECTION_POST_TYPE.'s</option>';
			$current = isset($_GET['wpp_parent_section']) ? $_GET['wpp_parent_section'] : '';
			foreach ($parent_sections as $section) {
				$select .= sprintf('<option value="%s"%s>%s</option>', $section->ID, $section->ID == $current ? ' selected="selected"' : '', $section->post_title);
			}
			$select .= '</select>';
			echo $select;
		} else {
			return;
		}
	}
		
	/**
		* Create section column head
		*
		* @param Array $defaults
		* @return Array $defaults
		*/
	public function wpp_section_admin_columns_head($columns) {
		$post_types_with_section = get_post_types_by_support("parent_section");
		$post_types_with_section[] = "post";
	
		$current_post_type = wpp_get_current_post_type();
		if (in_array($current_post_type, $post_types_with_section)) {
			$columns['parent_section'] = ucfirst(SECTION_POST_TYPE);
		}
		return $columns;
	}
	/**
		* Add section column content
		*
		* @param String $column_name
		* @param Integer $post_ID
		* @return void
		*/
	public function wpp_admin_section_column_content($column_name, $post_ID) {
			if ($column_name == "parent_section") {
				$post = get_post($post_ID);	
				if ($post) {
					$parent_section = get_post($post->post_parent);
					if ($parent_section) {
						echo $parent_section->post_title;
					}
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
					$home_sections = $this->wpp_get_home_sections ($lang_term->term_id);
	
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
		* Get home sections with a given language term id
		*
		* @param Integer $lang_term_id
		* @return Array
		*/
	public function wpp_get_home_sections ($lang_term_id) {
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
					'taxonomy' => LOCALE_TAXONOMY_SLUG, // defined in wpp-locale.php
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
				$home_sections = $this->wpp_get_home_sections ($post_langs_terms[0]->term_id);
				
				if ($section_type === SECTION_POST_HOME_FIELD_VALUE && count($home_sections) > 1) {
					$this->WPP_SKIP_AFTER_SECTION_SAVE = true;
					foreach ($home_sections as $home_section) {
						wp_update_post(array('ID' => $home_section->ID, 'meta_query' => array(array('key'=> SECTION_TYPE_FIELD_SLUG,'value'=> SECTION_POST_HOME_FIELD_VALUE))));
					}
					wp_update_post( array('ID' => $post->ID, 'meta_query' => array(array('key'=> SECTION_TYPE_FIELD_SLUG,'value'=> SECTION_POST_HOME_FIELD_VALUE))));
					$this->WPP_SKIP_AFTER_SECTION_SAVE = false;
				}
			}
		}
	}
		
	/**
		* Run custom action after a content is inserted
		*
		* @param Integer $section_id
		* @param Object $section
		*/
	public function after_save_section( $section_id, $section) {
		if ($this->WPP_SKIP_AFTER_SECTION_SAVE === false && $section->post_type === "section") {
			$this->set_unique_section_home($section);	
		}
	}
}

// Start class
$WpWebAppSection = new WpWebAppSection();

