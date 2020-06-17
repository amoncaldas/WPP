<?php

/**
* Apply admin tag filter selected
*
* @param Object $query
* @return void
*/
function wpp_apply_admin_tag_filter( $query ) {
 global $pagenow;
 if ( is_admin() && ($pagenow === 'edit.php' || $pagenow === 'upload.php') && !empty($_GET['wpp_tag'])) {
  $valid_post_types = get_post_types(array("public"=>true));

  if (in_array($query->query_vars['post_type'], $valid_post_types)) {
   $tax_query = [];
   $term_slug = $_GET['wpp_tag'];
   $tag_query = array (
    array(
     'taxonomy' => 'post_tag',
     'field' => 'slug',
     'terms' => $term_slug
    )
   );
   $tax_query[] = $tag_query;
   $query->set( 'tax_query', $tax_query );
  }
 }
}
 
 
/**
 * Create admin tag filter
 *
 * @return void
 */
function wpp_create_admin_tag_filter() {
 global $wpdb;
 $valid_post_types = get_post_types(array("public"=>true));

 $current_post_type = wpp_get_current_post_type();

 if (isset($current_post_type) && in_array($current_post_type, $valid_post_types)) {	

  $available_tag_terms = get_terms( array( 'taxonomy' => 'post_tag', 'hide_empty' => false) );
	
  if (is_array($available_tag_terms)) {
   $select = '<select name="wpp_tag"><option value="">Ignore tags</option>';

   $current = isset($_GET['wpp_tag']) ? $_GET['wpp_tag'] : '';
   foreach ($available_tag_terms as $tag_term) {
    $select .= sprintf('<option value="%s"%s>%s</option>', $tag_term->slug, $tag_term->slug == $current ? ' selected="selected"' : '', $tag_term->name);
   }
   $select .= '</select>';
  }
  
  echo $select;
 } else {
  return;
 }
}

add_filter( 'parse_query', 'wpp_apply_admin_tag_filter' );
add_action( 'restrict_manage_posts', 'wpp_create_admin_tag_filter' );
