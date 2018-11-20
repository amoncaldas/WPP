<?php
ob_start();

require_once( FAM_PLUGIN_PATH . '/includes/global/fam-customization.php' );

define('FAM_MAIN_SITE_PLUGIN_PATH', dirname( __FILE__ ));

include_once(FAM_PLUGIN_PATH.'/content/blog-post.php');
include_once(FAM_PLUGIN_PATH.'/content/informativo.php');
include_once(FAM_PLUGIN_PATH.'/includes/global/content/album.php');

global $GetMultiSiteData;
$GetMultiSiteData = true;

if(strpos($_SERVER['REQUEST_URI'], "/?attachment_id=" ) > -1 && !is_admin())
{	
	header('Location: /');	
}

function famroot_custom_add_js() {
	$type = get_current_post_type();
	$uri = $_SERVER['REQUEST_URI'];
	
	if($type == 'albuns')
	{	
		$scriptLocation = FAM_PLUGIN_PATH. "/global/content/js/";			
		if((strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{			
			wp_enqueue_script('album', $scriptLocation.'album.js', array('jquery', 'jquery-ui-core', 'google_maps','jquery-ui-sortable'), '0.0.1', true);			
		}	
	}
	if($type == 'blog_post')
	{	
		$scriptLocation = FAM_MAIN_SITE_PLUGIN_PATH. "/resources/js/";		
		if((strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{			
			wp_enqueue_script('blog_post', $scriptLocation.'blog_post.js', array('jquery', 'jquery-ui-core', 'google_maps','jquery-ui-sortable'), '0.0.1', true);			
		}	
	}
}
add_action('admin_enqueue_scripts', 'famroot_custom_add_js', 0);


add_action('admin_head', 'edit_root_admin_menu');
function edit_root_admin_menu(){
	$screen = get_current_screen();
	if(!$screen->is_network)
	{
		global $menu;	
		$comentariosLabel = $menu[25][0];
		$comentatiosCount = str_replace("Comentários", "", $comentariosLabel);			
		$menu[25][0] = "Mensagens no fórum ".$comentatiosCount;
		remove_menu_page("edit-tags.php?taxonomy=category");		
		$comentariosLabel = $menu[28][0] = "Tópicos de fórum";
		unset($menu[5]);		
	}		
}

add_action( 'admin_head', 'fam_root_hide_widget_elements' );
function fam_root_hide_widget_elements()
{ 	
    ?>
	<style>
		<?php
			if(!current_user_can( 'edit_posts' ) || !current_user_can( 'moderate_comments' ) ) {
			   ?>#dashboard-widgets-wrap .table_discussion {display:none;} #dashboard-widgets-wrap .musubtable {display:block;}<?php
			}
		?>
		#dashboard-widgets-wrap .table_content a[href$="edit.php"] {display:none;}
		#dashboard-widgets-wrap .table_content a[href$="edit-tags.php"]{display:none;}	
		#dashboard-widgets-wrap .table_content a[href$="edit-tags.php?taxonomy=category"]{display:none;}
		<?php
			if(!current_user_can( 'edit_pages' )) {
			   ?>
				#dashboard-widgets-wrap .table_content td.b_pages {display:none;}
				#dashboard-widgets-wrap .table_content td.t_pages {display:none;}
				<?php
			}
		?>
		<?php
			if(!current_user_can( 'manage_categories' )) {
			   ?>
				#dashboard-widgets-wrap .table_content td.b-cats {display:none;}
				#dashboard-widgets-wrap .table_content td.cats {display:none;}
				<?php
			}
		?>		
		
		#dashboard-widgets-wrap .table_content td.b-tags {display:none;}
		#dashboard-widgets-wrap .table_content td.tags {display:none;}
	</style>
	
    <?php
}


add_action('admin_head', 'edit_admin_menu_root');
function edit_admin_menu_root(){
	global $menu;	
	$menu[70][0] = "Viajantes";	
}

remove_all_actions( 'do_feed_rss2' );
add_action( 'do_feed_rss2', 'famroot_feed_rss2', 10, 1 );
function famroot_feed_rss2( $for_comments ) {
    $rss_template = FAM_PLUGIN_PATH . '/includes/feeds/feed-rss2.php';	
    load_template( $rss_template );    
}

remove_all_actions( 'do_feed_rss' );
add_action( 'do_feed_rss', 'famroot_feed_rss', 10, 1 );

function famroot_feed_rss( $for_comments ) {
    $rss_template = FAM_PLUGIN_PATH . '/includes/feeds/feed-rss.php'; 	   
    load_template( $rss_template );
}

remove_all_actions( 'do_feed_atom' );
add_action( 'do_feed_atom', 'famroot_feed_rss', 10, 1 );

function famroot_feed_atom( $for_comments ) {
    $rss_template = FAM_PLUGIN_PATH . '/includes/feeds/feed-atom.php'; 	   
    load_template( $rss_template ); 	  
}
ob_end_clean();
?>