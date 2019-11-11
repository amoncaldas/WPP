<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

?><!DOCTYPE html>
<html lang="<?php echo get_request_locale(); ?>" class="no-js">
<head>
	<title><?php echo WPP_TITLE ?></title>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
 <meta property="og:title" content="<?php echo WPP_TITLE; ?>"" />
 <meta property="og:image" content="<?php echo WPP_OG_URL; ?>" />
 <meta property="og:locale" content="<?php echo get_request_locale(); ?>" />
 <meta property="og:description" content="<?php echo WPP_OG_DESCRIPTION; ?>" />
	<link rel="image_src" type="image/<?php echo WPP_OG_IMAGE_EXT ?>" href="<?php echo WPP_OG_URL; ?>" />

	<?php 
	foreach (get_wpp_metas() as $key => $value) {
		echo "<meta property='$key' content='$value'>";
	} 
	foreach (get_wpp_metas("name_") as $key => $value) {
		echo "<meta name='$key' content='$value'>";
	} 
	?>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
</head>

<body>
<div >
	<div>
		<header>
			<div>
				<div>
					<?php 
					foreach (get_wpp_locales() as $locale) {
						echo "<a href='/?l=$locale'>$locale</a><br/>";
					}
					?>
				</div>
				<div>
					<?php if ( defined("IS_SECTION") || defined("IS_SEARCH") || defined("RENDER_ARCHIVE_POST_TYPE")) : ?>
						<h1 ><?php echo WPP_TITLE?></h1>
					<?php else : ?>
						<p ><a href="/?l=<?php echo get_request_locale() ?>" rel="home"><?php echo WPP_TITLE ?></a></p>
					<?php endif;
					?><p><?php echo WPP_OG_DESCRIPTION ?></p>
				</div><!-- .site-branding -->			
					<div>						
						<nav role="navigation">
							<?php
								$locale = get_request_locale();
								$menus = get_terms( 'nav_menu' );
								$menus = array_combine( wp_list_pluck( $menus, 'name' ), wp_list_pluck( $menus, 'term_id' ) );								
								$primary_menu_id = $menus["primary_menu_$locale"];

								$menu_items = wp_get_nav_menu_items($primary_menu_id);

								if ($menu_items !== false) {
									wp_nav_menu( 
										array(
										'menu' => $primary_menu_id,
										'menu_class'     => "primary-menu-$locale",
										"depth"=> 3,
										'fallback_cb' => false
										)
									);
								}
							?>
						</nav><!-- .main-navigation -->					
					</div><!-- .site-header-menu -->
				
			</div><!-- .site-header-main -->
		</header><!-- .site-header -->

		<div>
