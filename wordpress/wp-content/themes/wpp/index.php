<?php require_once("header.php"); ?>

 <?php 
 global $section;
 $has_content = get_post_meta($section->ID, "has_content", true);
 if ($has_content) {
   $content = get_post_meta( $section->ID, "content", true);
   echo $content;
 }
 
 ?>
 <?php echo get_the_date(); ?>

<?php require_once("footer.php"); ?>