<?php require_once("header.php"); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

 <?php the_title(); ?>
 <?php the_content(); ?>
 <?php echo get_the_date(); ?>

<?php endwhile; ?>
<?php endif; ?>
<?php require_once("footer.php"); ?>