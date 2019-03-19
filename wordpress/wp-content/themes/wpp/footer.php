<?php
/**
 * The template for crawler requests
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Wpp
 */
?>
	</div><!-- .site-content -->
		<footer id="colophon" class="site-footer" role="contentinfo">
      <?php 
      	$locale = get_request_locale();
        if ( has_nav_menu( "primary-menu-$locale")) : ?>
				<nav class="main-navigation" role="navigation">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'secondary-menu',
							'menu_class'     => 'secondary-menu',
						 ) );
					?>
				</nav><!-- .main-navigation -->
			<?php endif; ?>

			<div class="site-info">
				<span class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>
			</div><!-- .site-info -->
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->
</body>
</html>
