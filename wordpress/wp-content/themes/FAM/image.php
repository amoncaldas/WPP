<?php
get_header(); ?>

		<div id="content-container">
			<div id="content">
				<div id="bloco-conteudo-central">
					<div class="media-attach-info">
						<?php while ( have_posts() ) {					
							the_post();
					
							$metadata = wp_get_attachment_metadata();
							printf( __( '<span class="meta-prep meta-prep-entry-date">Publicado </span> <span class="entry-date"><abbr class="published" title="%1$s">%2$s</abbr></span> em <a href="%3$s" title="Return to %4$s" rel="gallery">%5$s</a>', 'twentyeleven' ),
								esc_attr( get_the_time() ),
								get_the_date(),
								esc_url( get_permalink( $post->post_parent ) ),
								esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
								get_the_title( $post->post_parent )
								);
					
					
					} ?>
					</div>
				<div class="attachment">
						<?php
						/**
						 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
						 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
						 */
						$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
						foreach ( $attachments as $k => $attachment ) {
							if ( $attachment->ID == $post->ID )
								break;
						}
						$k++;
						// If there is more than 1 attachment in a gallery
						if ( count( $attachments ) > 1 ) {
							if ( isset( $attachments[ $k ] ) )
								// get the URL of the next image attachment
								$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
							else
								// or get the URL of the first image attachment
								$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
						} else {
							// or, if there's only 1 image, get the URL of the image
							$next_attachment_url = wp_get_attachment_url();
						}
						?>
						<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
						$attachment_size = apply_filters( 'twentyeleven_attachment_size', 848 );
						echo wp_get_attachment_image( $post->ID, array( $attachment_size, 1024 ) ); // filterable image width with 1024px limit for image height.
						?></a>

						<?php if ( ! empty( $post->post_excerpt ) ) { ?>
							<div class="entry-caption">
								<?php the_excerpt(); ?>
						</div>
						<?php }; ?>
					</div><!-- .attachment -->
					<div class="clear"></div>
					<?php widget::Get("share", array("comment_box_width" => '900')); ?>
					
				</div>
				
			</div><!-- #content -->
			<div id="contentBottom">
					<div id="bottom-boxes-container">
						<?php widget::Get("ultimos-relatos") ?>
						<?php widget::Get("fotos", array("width"=>'390px', 'float'=>'right', 'margin_right'=>'25px', 'itens'=>9, 'orderby'=>'rand')); ?>
					</div>
					<div class="clear"></div>
					<?php widget::Get("codigocriativo")?>
				</div><!-- end content-bottom -->
		</div>
	</div><!-- end geral -->

<?php include('footer-wp.php') ?>