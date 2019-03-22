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
		<footer>
			<nav>
				<?php
					$locale = get_request_locale();
					wp_nav_menu( array(
						'theme_location' => "secondary-menu-$locale",
						'menu_class'     => "secondary-menu-$locale",
						"depth"=> 3
					));
				?>
			</nav>
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->
</body>
</html>
