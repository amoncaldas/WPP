<?php require_once("header.php");
  global $post;
?>
<main>
  <h1><?php echo $post->post_title; ?></h1>
  <time :datetime="<?php echo $post->post_date;?>"><?php echo get_the_date("", $post);?></time><br/>
  <br/>
  <div> <?php echo get_the_post_thumbnail($post->ID); ?></div>
  <br/>
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
      $content = apply_filters('the_content', $post->post_content); 
      $content = !$content || $content === "" ? $post->post_excerpt : $content;
      echo $content;
    ?>  
  </div>
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
      <time :datetime="<?php echo $related_post->post_date;?>"><?php echo get_the_date("", $related_post);?></time><br/><br/>
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