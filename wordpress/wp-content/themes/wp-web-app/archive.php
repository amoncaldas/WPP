<?php require_once("header.php");

// the query
$the_query = new WP_Query( array( "post_type"=> RENDER_ARCHIVE_POST_TYPE,  "post_status"=> "publish" )) ?>

<?php if ( $the_query->have_posts() ) : ?>

	<!-- pagination here -->

	<!-- the loop -->
	<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<h2><?php the_title(); ?></h2>
        <h2><?php the_content(); ?></h2>
	<?php endwhile; ?>
	<!-- end of the loop -->
    <?php  
        the_posts_pagination( array() );
    ?>
	<!-- pagination here -->

	<?php wp_reset_postdata(); 
	endif;

require_once("footer.php");