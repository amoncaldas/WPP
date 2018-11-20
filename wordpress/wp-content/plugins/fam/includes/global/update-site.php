<?php
/**
 * WordPress store in db absolute urls and this is the way to update this 
 * and other post metas that also stores absolute urls to images
 * @see https://codex.wordpress.org/Changing_The_Site_URL
 * WP_HOME is defined in wp-config.php
 */
global $wpdb;
$current_url = $wpdb->get_col( "SELECT option_value from ".$wpdb->prefix."options where option_name = 'siteurl'" )[0];
if(WP_HOME != null && $current_url != WP_HOME){
  // for some reason update_option function is not working in this case, so we direct update the db
  $wpdb->update($wpdb->prefix."options", array('option_value'=>WP_HOME), array('option_name' => 'siteurl'));
  $wpdb->update($wpdb->prefix."options", array('option_value'=>WP_HOME), array('option_name' => 'home'));
  // updateThemePostMetaImageUrls();   
}

/**
 * Update the pursuit theme post metas image urls
 * including slider image urls and team image urls
 *
 * @return void
 */
function updateThemePostMetaImageUrls(){
  // the custom meta image are linked to pages, so we get the pages post and inspect them
  $args = array('posts_per_page' => '-1','post_type'=> 'page','post_status'=> 'publish');
  $the_query = new WP_Query($args);  
  if ( $the_query->have_posts() ){
    while ( $the_query->have_posts() ) : $the_query->the_post(); 
      updateSlideImages(get_the_ID());      
      updateTeamImages(get_the_ID());
    endwhile;
  } 
}

/**
 * Update pursuit theme team image urls
 *
 * @param integer $post_id
 * @return void
 */
function updateTeamImages($post_id){
  $themo_team = get_post_meta($post_id,'themo_team_1',true);
  if(isset($themo_team) && is_array($themo_team)){
    $changed = false;     
    foreach($themo_team as $key => $value) {
      // the theme stores the image url in the sub-key background-image  
      $image_url = $themo_team[$key]["themo_team_1_photo"];
      if($image_url != ""){         
        $themo_team[$key]["themo_team_1_photo"] = replaceImageSiteUrl($image_url);
        $changed = true;      
      }           
    } 
    if($changed === true) {
      update_post_meta($post_id, 'themo_team_1', $themo_team);        
    } 
  }
}

/**
 * Update pursuit theme home slider image urls
 *
 * @param integer $post_id
 * @return void
 */
function updateSlideImages($post_id){
  $slider_meta = get_post_meta($post_id,'themo_slider_flex',true);
  if(isset($slider_meta) && is_array($slider_meta)){
    $changed = false;     
    foreach($slider_meta as $key => $value) {
      // the theme stores the image url in the sub-key background-image  
      $image_url = $slider_meta[$key]["themo_slider__background"]["background-image"];
      if($image_url != ""){         
        $slider_meta[$key]["themo_slider__background"]["background-image"] = replaceImageSiteUrl($image_url);
        $changed = true;
              
      //or in the key themo_slider_image image key
      } elseif($slider_meta[$key]["themo_slider_image"] != ""){
        $slider_meta[$key]["themo_slider_image"] = replaceImageSiteUrl($slider_meta[$key]["themo_slider_image"]);
        $changed = true;
      } 
    }
    if($changed === true) {
      update_post_meta($post_id, 'themo_slider_flex', $slider_meta);        
    }        
  } 
}

/**
 * Replace the base site url in an image url for pursuit theme
 *
 * @param string $image_url
 * @return string new image url
 */
function replaceImageSiteUrl($image_url){
  $url_pattern = "/(https?:\/\/)([^\/]*)(:\\d*|[a-z0-9A-Z])?(.*)?/";				
  if(preg_match_all($url_pattern,$image_url, $output_segments))
  {
    $protocol = $output_segments[1][0];
    $address_and_port = $output_segments[2][0];
    $site = $protocol.$address_and_port;    
    return str_replace($site, WP_HOME, $image_url);                  
  }
  return $image_url;
}