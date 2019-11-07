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
					$menus = get_terms( 'nav_menu' );
					$menus = array_combine( wp_list_pluck( $menus, 'name' ), wp_list_pluck( $menus, 'term_id' ) );								
					$secondary_menu_id = $menus["secondary_menu_$locale"];

					$menu_items = wp_get_nav_menu_items($secondary_menu_id);

					if ($menu_items !== false) {
						wp_nav_menu( 
							array(
								'menu' => $secondary_menu_id,
								'menu_class'     => "secondary-menu-$locale",
								'fallback_cb' => false,
								"depth"=> 3
							)
						);
					}
				?>
			</nav>
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->
</body>
</html>
