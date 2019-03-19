<?php require_once("header.php"); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<main>
 <h1><?php the_title(); ?></h1>
 <div> <?php the_content(); ?></div>
 <div> <?php get_the_post_thumbnail(get_the_ID()) ?></div>
 <?php echo get_the_date(); ?>
 </main>

<?php endwhile; ?>
<?php endif; ?>
<?php require_once("footer.php"); ?>