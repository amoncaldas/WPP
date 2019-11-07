<?php require_once("header.php");
  global $post;
?>
<main>
  <h1><?php echo $post->post_title; ?></h1>
  <time datetime="<?php echo $post->post_date;?>"><?php echo get_the_date("", $post);?></time><br/>
  <br/>
  <div> <?php echo get_the_post_thumbnail($post->ID); ?></div>
  <br/>  
  <?php
    // Get the highlighted ids from the content highlighted_top saved in post meta
    $highlighted_top = get_highlighted($post->ID, "top");

    // Check if there is highlighted posts
    if (isset($highlighted_top) is_array($highlighted_top)) {
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
          <time datetime="<?php echo $pohighlighted_postst->post_date;?>"><?php echo get_the_date("", $highlighted_post);?></time><br/>
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
  <div> 
    <?php 
      $places = get_post_meta($post->ID, "places", true);
      if (is_array($places) && $places > 0) {
        $last_place = $places[count($places)-1];
        $place_title = get_the_title($last_place);
        $place_link = get_the_permalink($last_place);
        echo "<a href='$place_link'>$place_title</a>";
      }
    ?>  
  </div>
  <div> 
    <?php 
      // print the prepend 
      $prepend_id = get_post_meta($post->ID, "prepend", true);
      $prepend_id = is_array($prepend_id)? $prepend_id[0] : $prepend_id;
      if ($prepend_id) {
        $prepend_post = get_post($prepend_id)
        echo apply_filters('the_content', $prepend_post->post_content); 
      }
      // print the post content
      $content = apply_filters('the_content', $post->post_content); 
      $content = !$content || $content === "" ? $post->post_excerpt : $content;
      echo $content;

      // print the append
      $append_id = get_post_meta($post->ID, "append", true);
      $append_id = is_array($append_id)? $append_id[0] : $append_id;
      if ($append_id) {
        $append_post = get_post($append_id)
        echo apply_filters('the_content', $append_post->post_content); 
      }
    ?>  
  </div>
  <?php
    // Get the highlighted ids from the content highlighted_bottom saved in post meta
    $highlighted_top = get_highlighted($post->ID, "bottom");

    // Check if there is highlighted posts
    if (isset($highlighted_top) is_array($highlighted_top)) {
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
          <time datetime="<?php echo $pohighlighted_postst->post_date;?>"><?php echo get_the_date("", $highlighted_post);?></time><br/>
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
  <div>
    <?php  
      $medias = get_post_meta($post->ID, "medias", true);
      foreach ($medias as $media_id) {
        $media_html = wp_get_attachment_image($media_id, "medium");
        echo $media_html;
      }
    ?>
  </div>
  <div>
    <?php wp_list_comments(array(), get_approved_comments($post->ID)); ?>
  </div>
</main>
<section>
<?php  
  $relate = get_related($post->ID);
  foreach ($relate  as $related_post) {
  ?>
    <article>
      <h2><a href="<?php echo get_the_permalink($related_post->ID); ?>"><?php echo get_the_title($related_post->ID);?></a></h2>
      <time datetime="<?php echo $related_post->post_date;?>"><?php echo get_the_date("", $related_post);?></time><br/><br/>
      <div> <?php echo get_the_post_thumbnail($related_post->ID); ?></div><br/>
      <?php 
        $content =  get_the_content($related_post->ID);
        $content = strip_tags(apply_filters('the_content', $content));
        $append = "";
        if (strlen($content) > 300) {
          $append = " [...]";
        }
      ?>
      <div><?php echo $content.$append; ?></div><br/>
      <a href="<?php echo get_the_permalink($related_post->ID)."?l=".get_request_locale(); ?>"><?php echo get_the_title($related_post->ID); ?></a>
    </article>    
  <?php
  }
 ?>
</section>

<?php require_once("footer.php"); ?>
