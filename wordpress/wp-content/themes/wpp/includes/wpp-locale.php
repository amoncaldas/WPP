<?php

if ( ! defined('LOCALE_TAXONOMY_SLUG') ) {
	define('LOCALE_TAXONOMY_SLUG', "locale");
}

/**
 * Register custom types section and lang
 *
 * @return void
 */
function register_locale_type () {
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
* Apply admin section filter selected
*
* @param Object $query
* @return void
*/
function wpp_apply_admin_locale_filter( $query ) {
 global $pagenow;
 if ( is_admin() && $pagenow == 'edit.php' && !empty($_GET['wpp_locale'])) {
  $post_types_with_locale = get_post_types(array("public"=>true));
  unset($post_types_with_locale["attachment"]);

  if (in_array($query->query_vars['post_type'], $post_types_with_locale)) {
   $tax_query = [];
   $term_id = $_GET['wpp_locale'];
   $locale_query = array (
    array(
     'taxonomy' => LOCALE_TAXONOMY_SLUG,
     'field' => 'term_id',
     'terms' => $term_id
    )
   );
   $tax_query[] = $locale_query;
   $query->set( 'tax_query', $tax_query );
  }
 }
}
 
 
/**
 * Create admin sections filter
 *
 * @return void
 */
function wpp_create_admin_locale_filter() {
 global $wpdb;
 $post_types_with_locale = get_post_types(array("public"=>true));
 unset($post_types_with_locale["attachment"]);

 $current_post_type = wpp_get_current_post_type();

 if (isset($current_post_type) && in_array($current_post_type, $post_types_with_locale)) {	

  $available_locale_terms = get_terms( array('taxonomy' => LOCALE_TAXONOMY_SLUG, 'hide_empty' => false, 'orderby' => 'id', 'order' => 'ASC'));
	
  if (is_array($available_locale_terms)) {
   $select = '<select name="wpp_locale"><option value="">All '.LOCALE_TAXONOMY_SLUG.'s</option>';

   $current = isset($_GET['wpp_locale']) ? $_GET['wpp_locale'] : '';
   foreach ($available_locale_terms as $locale_term) {
    if ($locale_term->slug === "neutral") {
     continue;
    }
    $select .= sprintf('<option value="%s"%s>%s</option>', $locale_term->term_id, $locale_term->term_id == $current ? ' selected="selected"' : '', $locale_term->name);
   }
   $select .= '</select>';
  }
  
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
function wpp_locale_admin_columns_head($columns) {
  $post_types_with_locale = get_post_types(array("public"=>true));
  unset($post_types_with_locale["attachment"]);
 
  $current_post_type = wpp_get_current_post_type();
  if (in_array($current_post_type, $post_types_with_locale)) {
   $columns['wpp_locale'] = ucfirst(LOCALE_TAXONOMY_SLUG);
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
function wpp_admin_locale_column_content($column_name, $post_ID) {
  if ($column_name == "wpp_locale") {
    $post_locales = wp_get_post_terms( $post_ID, LOCALE_TAXONOMY_SLUG, [] );
    if ($post_locales && is_array($post_locales) && count($post_locales) > 0) {
      echo $post_locales[0]->name;
    }
  }
}

/**
 * Make sure the mandatory locales exists exists. If any one required does not exist, create it
 *
 * @return void
 */
function make_sure_locale_exists() {
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
 

register_locale_type();
add_filter( 'parse_query', 'wpp_apply_admin_locale_filter' );
add_action( 'restrict_manage_posts', 'wpp_create_admin_locale_filter' );
add_filter('manage_posts_columns', 'wpp_locale_admin_columns_head', 10);
add_action('manage_posts_custom_column', 'wpp_admin_locale_column_content', 11, 2);
add_action('init', 'make_sure_locale_exists', 11);
