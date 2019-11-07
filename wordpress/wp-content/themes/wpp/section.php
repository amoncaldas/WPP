<?php require_once("header.php");

global $section;

// Get the highlighted ids from the content highlighted_top saved in post meta
$highlighted_top = get_highlighted($section->ID, "top");

// Check if there is highlighted posts
if (isset($highlighted_top) && is_array($highlighted_top)) {
  echo "<section><h1>".$highlighted_top["title"]."</h1>";
  foreach ($highlighted_top["posts"] as $highlighted_post) {
    ?>  
    <article>
      <?php 
        $permalink = get_post_meta($highlighted_post->ID, "custom_link", true);
        if (!$permalink) {
          $permalink = get_the_permalink($highlighted_post->ID);
        }
      ?>
      <a href="<?php echo $permalink?>"><?php echo "<h1>".$highlighted_post->post_title."</h1>";?></a>
      <time datetime="<?php echo $highlighted_post->post_date;?>"><?php echo get_the_date("", $highlighted_post);?></time><br/>
      <br/>
      <div> <?php echo get_the_post_thumbnail($highlighted_post->ID); ?></div>
      <div>
        <?php 
          $excerpt = strip_tags(apply_filters('the_content', $highlighted_post->post_content));
          if (strlen($excerpt) > 300) {
            $excerpt = substr($excerpt,0, 300);
            $excerpt .= " [...]";
          }
          echo $excerpt;
        ?>
      </div>
    </article>
    <?php
  }     
  echo "</section>";
}
 // Slides
 $has_slider = get_post_meta($section->ID, "has_image_slides", true);
 if ($has_slider) {
  echo "<section>";
    $slider_images = get_post_meta( $section->ID, "slide_images", true); 
    foreach ($slider_images as $image_id) {
      echo wp_get_attachment_image($image_id, "full");
    }
  echo "</section>";
 }

// Get the highlighted ids from the content highlighted_top saved in post meta
$highlighted_middle = get_highlighted($section->ID, "middle");

// Check if there is highlighted posts
if (isset($highlighted_middle) && is_array($highlighted_middle)) {
  echo "<section><h1>".$highlighted_middle["title"]."</h1>";
  foreach ($highlighted_middle["posts"] as $highlighted_post) {
    ?>  
    <article>
      <?php 
        $permalink = get_post_meta($highlighted_post->ID, "custom_link", true);
        if (!$permalink) {
          $permalink = get_the_permalink($highlighted_post->ID);
        }
      ?>
      <a href="<?php echo $permalink ?>"><?php echo "<h1>".$highlighted_post->post_title."</h1>";?></a>
      <time datetime="<?php echo $highlighted_post->post_date;?>"><?php echo get_the_date("", $highlighted_post);?></time><br/>
      <br/>
      <div> <?php echo get_the_post_thumbnail($highlighted_post->ID); ?></div>
      <div>
        <?php 
          $excerpt = strip_tags(apply_filters('the_content', $highlighted_post->post_content));
          if (strlen($excerpt) > 300) {
            $excerpt = substr($excerpt,0, 300);
            $excerpt .= " [...]";
          }
          echo $excerpt;
        ?>
      </div>
    </article>
    <?php
  }     
  echo "</section>";
}

 $has_content = get_post_meta($section->ID, "has_content", true);
 if ($has_content) {
  $content = apply_filters('the_content', get_post_meta( $section->ID, "content", true)); 
  echo "<main>$content</main><br/>";
 }

 echo "<div>";
  // Output compact listing posts archive link
  $compact_list_posts = get_post_meta($section->ID, "compact_list_posts", true);
  if ($compact_list_posts) {  
    $compact_list_post_endpoints = get_post_meta($section->ID, "compact_list_post_endpoints", true);

    foreach ($compact_list_post_endpoints as $compact_list_post_endpoint) {
      $post_type  = get_post_type_by_endpoint($compact_list_post_endpoint);
      $title = ucfirst(get_post_type_title_translation($post_type, get_request_locale()));
      $path = get_post_path_translation($post_type, get_request_locale())."?l=".get_request_locale();
      if ($section->guid !== "/") {
        $path = "/$section->post_name/".$path;
      } else {
        $path = "/$path";
      }
      $link = network_site_url($path);
      echo "<a href='$link'>$title</a><br/>";
    }
  }

  // Output listing posts archive link
  $list_posts = get_post_meta($section->ID, "list_posts", true);
  if ($list_posts) {  
    $list_post_endpoints = get_post_meta($section->ID, "list_post_endpoints", true);

    foreach ($list_post_endpoints as $list_post_endpoint) {
      $post_type  = get_post_type_by_endpoint($list_post_endpoint);    
      $title = ucfirst(get_post_type_title_translation($post_type, get_request_locale()));
      $path = get_post_path_translation($post_type, get_request_locale())."?l=".get_request_locale();
      if ($section->guid !== "/") {
        $path = "/$section->post_name/".$path;
      } else {
        $path = "/$path";
      }
      $link = network_site_url($path);
      echo "<a href='$link'>$title</a><br/>";    
    }
  }

  echo "<br/>";
  echo "</div>";
  
  echo "<div>";
    // Get the request locale
    $locale = get_request_locale();

    $args = [
      "post_type"=> SECTION_POST_TYPE, 
      "numberposts" => -1,
      'tax_query'=> [
        [
          'taxonomy' => LOCALE_TAXONOMY_SLUG,
          'field' => 'slug',
          'terms' => [$locale, "neutral"] // bring the content that has the same locale or are neutral
        ]
      ]
    ];
    $sections = get_posts($args);
    
    foreach ($sections as $section) {    
      $section_permalink = get_the_permalink($section->ID);
      $section_path = parse_url($section_permalink, PHP_URL_PATH);
      if ($section_path !== "/") {
        $section_permalink .= "?l=".$locale;
        echo "<a href='$section_permalink'>$section->post_title</a><br/>";
      }
    }
  echo "</div>";    

echo "</div>";

// Get the highlighted ids from the content highlighted_top saved in post meta
$highlighted_bottom = get_highlighted($section->ID, "bottom");

// Check if there is highlighted posts
if (isset($highlighted_bottom) && is_array($highlighted_bottom)) {
  echo "<section><h1>".$highlighted_bottom["title"]."</h1>";
  foreach ($highlighted_bottom["posts"] as $highlighted_post) {
    ?>  
    <article>
      <?php 
        $permalink = get_post_meta($highlighted_post->ID, "custom_link", true);
        if (!$permalink) {
          $permalink = get_the_permalink($highlighted_post->ID);
        }
      ?>
      <a href="<?php echo $permalink ?>"><?php echo "<h1>".$highlighted_post->post_title."</h1>";?></a>
      <time datetime="<?php echo $highlighted_post->post_date;?>"><?php echo get_the_date("", $highlighted_post);?></time><br/>
      <br/>
      <div> <?php echo get_the_post_thumbnail($highlighted_post->ID); ?></div>
      <div>
        <?php 
          $excerpt = strip_tags(apply_filters('the_content', $highlighted_post->post_content));
          if (strlen($excerpt) > 300) {
            $excerpt = substr($excerpt,0, 300);
            $excerpt .= " [...]";
          }
          echo $excerpt;
        ?>
      </div>
    </article>
    <?php
  }     
  echo "</section>";
}
?>
<?php echo get_the_date(); ?>
<?php require_once("footer.php"); ?>

