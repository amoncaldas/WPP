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
<html lang="<?php get_request_locale() ?>" class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php foreach (get_wpp_metas() as $key => $value) {
		echo "<meta name='$key' content='$value'>";
	} ?>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
</head>

<body>
<div id="page" class="site">
	<div class="site-inner">
		<header id="masthead" class="site-header" role="banner">
			<div class="site-header-main">
				<div class="site-branding">
					<?php if ( defined("IS_HOME_SECTION") ) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<?php else : ?>
						<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<?php endif;
					?><p class="site-description"><?php bloginfo( 'description', 'display' ); ?></p>
				</div><!-- .site-branding -->			
					<div id="site-header-menu" class="site-header-menu">						
						<nav id="site-navigation" class="main-navigation" role="navigation">
							<?php
								$locale = get_request_locale();
								wp_nav_menu( array(
									'theme_location' => "primary-menu-$locale",
									'menu_class'     => "primary-menu-$locale",
									) );
							?>
						</nav><!-- .main-navigation -->					
					</div><!-- .site-header-menu -->
				
			</div><!-- .site-header-main -->
		</header><!-- .site-header -->

		<div id="content" class="site-content">
