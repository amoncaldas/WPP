<?php

class WppParentPlaceFilter {

	public function __construct () {
		$this->register_hooks();
	}

	public function register_hooks() {
		add_filter( 'parse_query', array($this,'wpp_apply_admin_place_parent_filter'));
		add_action( 'restrict_manage_posts', array($this,'wpp_create_admin_parent_place_filter') );
		
		// For some reason wpp_admin_parent_place_column_content is not working
		// so we disabled the head and the content
		// add_filter('manage_posts_columns', array($this,'wpp_parent_place_admin_columns_head'), 10);
		// add_action('manage_posts_custom_column', array($this,'wpp_admin_parent_place_column_content'), 12, 2);
	}
	
	/**
		* Apply admin place parent filter selected
		*
		* @param Object $query
		* @return void
		*/
	public function wpp_apply_admin_place_parent_filter( $query ) {
		global $pagenow;
		if ( is_admin() && $pagenow == 'edit.php' && !empty($_GET['wpp_parent_place']) && $query->query_vars['post_type'] === "place") {
			$query->query_vars['post_parent'] = $_GET['wpp_parent_place'];
		}
	}
	
	
	/**
		* Create admin parent place filter
		*
		* @return void
		*/
	public function wpp_create_admin_parent_place_filter() {
		global $wpdb;
		$current_post_type = wpp_get_current_post_type();
	
		if (isset($current_post_type) && $current_post_type === "place") {		
			$sql = "SELECT ID, post_title FROM ".$wpdb->posts." WHERE post_type = 'place' AND post_parent = 0 AND post_status = 'publish' ORDER BY post_title";
			$parent_places = $wpdb->get_results($sql, OBJECT_K);
			$select = '<select name="wpp_parent_place"><option value="">Ignore parents</option>';
			$current = isset($_GET['wpp_parent_place']) ? $_GET['wpp_parent_place'] : '';
			foreach ($parent_places as $parent_place) {
				$select .= sprintf('<option value="%s"%s>%s</option>', $parent_place->ID, $parent_place->ID == $current ? ' selected="selected"' : '', $parent_place->post_title);
			}
			$select .= '</select>';
			echo $select;
		} else {
			return;
		}
	}
		
	/**
		* Create parent place column head
		*
		* @param Array $defaults
		* @return Array $defaults
		*/
	public function wpp_parent_place_admin_columns_head($columns) {	
		$current_post_type = wpp_get_current_post_type();
		if ($current_post_type === "place") {
			$columns['wpp_parent_place'] = "Parent place";
		}
		return $columns;
	}
	/**
		* Add parent place column content
		*
		* @param String $column_name
		* @param Integer $post_ID
		* @return void
		*/
	public function wpp_admin_parent_place_column_content($column_name, $post_ID) {
		if ($column_name == "wpp_parent_place") {
			$post = get_post($post_ID);	
			if ($post) {
				$parent_place = get_post($post->post_parent);
				if ($parent_place) {
					echo $parent_place->post_title;
				}
			}
		}
	}
}

// Start class
$wppParentPlaceFilter = new WppParentPlaceFilter();

