<?php require_once("header.php");

 global $section;
 $has_content = get_post_meta($section->ID, "has_content", true);
 if ($has_content) {
   $content = get_post_meta( $section->ID, "content", true);
   echo "<main>$content</main><br/>";
 }

 echo "<div>";
  // Output compact listing posts archive link
  $compact_list_posts = get_post_meta($section->ID, "compact_list_posts", true);
  if ($compact_list_posts) {  
    $compact_list_post_endpoints = get_post_meta($section->ID, "compact_list_post_endpoints", true);

    foreach ($compact_list_post_endpoints as $compact_list_post_endpoint) {
      $post_type  = get_post_type_by_endpoint($compact_list_post_endpoint);
      $title = get_post_type_title_translation($post_type, get_request_locale());
      $endpoint_translated = network_site_url(get_post_path_translation($post_type, get_request_locale()))."?l=".get_request_locale();
      echo "<a href='$endpoint_translated'>$title</a><br/>";
    }
  }

  // output listing posts archive link
  $list_posts = get_post_meta($section->ID, "list_posts", true);
  if ($list_posts) {  
    $list_post_endpoints = get_post_meta($section->ID, "list_post_endpoints", true);

    foreach ($list_post_endpoints as $list_post_endpoint) {
      $post_type  = get_post_type_by_endpoint($list_post_endpoint);    
      $title = get_post_type_title_translation($post_type, get_request_locale());
      $endpoint_translated = network_site_url(get_post_path_translation($post_type, get_request_locale()))."?l=".get_request_locale();
      echo "<a href='$endpoint_translated'>$title</a><br/>";    
    }
  }

  echo "<br/><div>";    
  $sections = get_posts(array("post_type"=> SECTION_POST_TYPE));
  foreach ($sections as $section) {    
    $section_permalink = get_the_permalink($section->ID);
    if ($section_permalink !== "/") {
      $section_permalink .= "?l=".get_request_locale();
      echo "<a href='$section_permalink'>$section->post_title</a><br/>";
    }
  }
  echo "</div>";    

echo "</div>";
?>
<?php echo get_the_date(); ?>
<?php require_once("footer.php"); ?>