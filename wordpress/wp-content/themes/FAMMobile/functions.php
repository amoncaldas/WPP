<?php
ob_start();
//define('CFCT_DEBUG', true);

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

if(get_current_blog_id() ==1)
{
	require_once( ABSPATH . '/wp-content/themes/FAMRoot/functions.php' );
	
}
else
{
	require_once( ABSPATH . '/wp-content/themes/FAM/functions.php' );
}

load_theme_textdomain('carrington-mobile');

define('CFCT_DEBUG', false);
define('CFCT_PATH', trailingslashit(TEMPLATEPATH));
define('CFCT_HOME_LIST_LENGTH', 5);
define('CFCT_HOME_LATEST_LENGTH', 250);

$cfct_options = array(
	'cfct_about_text'
	, 'cfct_credit'
	, 'cfct_posts_per_archive_page'
	, 'cfct_wp_footer'
);

function cfct_blog_init() {
	if (cfct_get_option('cfct_ajax_load') == 'yes') {
		cfct_ajax_load();
	}
}
add_action('init', 'cfct_blog_init');

function cfct_archive_title() {
	if(is_author()) {
		$output = __('Posts by:');
	} elseif(is_category()) {
		$output = __('Category Archives:');
	} elseif(is_tag()) {
		$output = __('Tag Archives:');
	} elseif(is_archive()) {
		$output = __('Archives:');
	}
	$output .= ' ';
	echo $output;
}

function cfct_mobile_post_gallery_columns($columns) {
	return 1;
}
add_filter('cfct_post_gallery_columns', 'cfct_mobile_post_gallery_columns');

add_filter('show_admin_bar', '__return_false');  

include_once(CFCT_PATH.'carrington-core/carrington.php');

//var_dump($wp_filter);
ob_end_clean();
?>