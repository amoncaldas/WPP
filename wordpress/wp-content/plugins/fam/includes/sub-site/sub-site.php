<?php
ob_start();
require_once( FAM_PLUGIN_PATH . '/includes/global/fam-customization.php' );

define('FAM_SUB_SITE_PLUGIN_PATH', dirname( __FILE__ ));

include_once(FAM_SUB_SITE_PLUGIN_PATH.'/content/viagem.php');
include_once(FAM_SUB_SITE_PLUGIN_PATH.'/content/relato.php');
include_once(FAM_SUB_SITE_PLUGIN_PATH.'/content/destaque.php');
include_once(FAM_SUB_SITE_PLUGIN_PATH.'/content/atualizacao.php');
include_once(FAM_PLUGIN_PATH.'/includes/global/content/album.php');

add_action('admin_enqueue_scripts', 'fam_custom_add_js', 0);
function fam_custom_add_js() {
	$type = get_current_post_type();
	$uri = $_SERVER['REQUEST_URI'];
	$scriptLocation = FAM_SUB_SITE_PLUGIN_PATH. "/resources/js/";		
	if($type == 'relatos' || $type == 'atualizacao' || $type == 'attachment' || $type == 'viagem' || $type == 'albuns' || strpos($uri,'trajeto') > -1 || strpos($uri,'media') > -1 || strpos($uri,'user-edit') > -1 || strpos($uri,'profile.php') > -1 )
	{		
		if($type == 'viagem' && (strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{ 
			wp_enqueue_script('roteiro', $scriptLocation.'roteiro.js', array('jquery','google_maps', 'jquery-ui-core', 'jquery-ui-sortable'), '0.0.1', true);			
		}
	
		if($type == 'albuns' && (strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{			
			wp_enqueue_script('album', $scriptLocation.'album.js', array('jquery', 'jquery-ui-core', 'google_maps','jquery-ui-sortable'), '0.0.1', true);			
		}

		if($type == 'relatos' && (strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{
			wp_enqueue_script('relato', $scriptLocation.'relato.js', array('jquery', 'jquery-ui-core','google_maps', 'jquery-ui-sortable'), '0.0.1', true);			
		}
		if($type == 'atualizacao' && (strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{
			wp_enqueue_script('atualizacao', $scriptLocation.'atualizacao.js', array('jquery', 'jquery-ui-core','google_maps', 'jquery-ui-sortable'), '0.0.1', true);			
		}	
	
		if (strpos($uri,'trajeto') > -1)
		{					
			wp_enqueue_script('trajeto', $scriptLocation.'trajeto.js', array('jquery','google_maps', 'jquery-ui-core', 'jquery-ui-sortable'), '0.0.1', true);			
		}
	}
}


add_action('admin_head', 'edit_admin_menu'); 
function edit_admin_menu(){
	global $menu;
	remove_menu_page("edit-comments.php");
	remove_menu_page("edit-tags.php?taxonomy=category");
	remove_menu_page("link-manager.php");
	remove_menu_page("edit.php?post_type=page");
	remove_menu_page("edit.php");
	remove_menu_page("admin.php?page=pods");	
	$menu[70][0] = "Viajantes";
	$postsViagens = get_posts(array('post_type'=>'viagem'));
	$menu[27][2] = "post.php?post=".$postsViagens[0]->ID."&action=edit";
	$menu[3] = $menu[21]; // Copy Destaques from position 21 to position 3
	unset($menu[20]); // Unset Pages (from original position)
	unset($menu[21]); // Unset Pages (from original position)
}


$facebook_publish_options = array('id'=>'604453679565803','name'=> 'Testes Códigocriativo', 'access_token'=>'CAAIULkJhlvYBAC71tUKZAByaTLlCEbzk9ZBIZB1lNYv0G3S4EYPCwQnS9FmuC77gJMpLEa3tZAOt0ZBIymenfHT2WGEbaJmGx3km1uAO3OZAfIjebxu36WERGkfdWzvZBOAe56EHBCdE9EZCF2btZBICkjx1W5VPulg81M8Dgk7bhfAIt01Du8DLv');	
if(FAM_ENV_PRODUCTION) {
	$facebook_publish_options = array('id'=>'222705747761628','name'=> 'Fazendo as Malas - Descobrindo o mundo', 'access_token'=>'CAAIULkJhlvYBABZBXqNbKWgsZBFj4lbRxQIR9AfSIlongBvGNSf412jNroCc7HmYenQPaBz9xkDK8yMlkbAtfdg1b5JMDWTywS932K4zA6ZCmmRKKYnADNPCKyUtSGUjAcsWsmDZCQ4tZAiot0VJgHXnHDbGs00ebovaqlZC6XdwZDZD');
}	
//update_option("facebook_publish_page", $facebook_publish_options);


add_action( 'admin_head', 'fam_hide_widget_elements' );
function fam_hide_widget_elements() {    
    ?>
		<style>
			#dashboard-widgets-wrap .table_discussion {display:none;}
			#dashboard-widgets-wrap .musubtable {display:block;}
			#dashboard-widgets-wrap .table_content a[href$="edit.php"] {display:none;}
			#dashboard-widgets-wrap .table_content td.b_pages {display:none;}
			#dashboard-widgets-wrap .table_content td.t_pages {display:none;}
			#dashboard-widgets-wrap .table_content td.b-cats {display:none;}
			#dashboard-widgets-wrap .table_content td.cats {display:none;}
			#dashboard-widgets-wrap .table_content td.b-tags {display:none;}
			#dashboard-widgets-wrap .table_content td.tags {display:none;}		
			#dashboard-widgets-wrap .table_content a[href$="edit.php?post_type=page"]{display:none;}
			#dashboard-widgets-wrap .table_content a[href$="edit-tags.php"]{display:none;}	
			#dashboard-widgets-wrap .table_content a[href$="edit-tags.php?taxonomy=category"]{display:none;}
		</style>
    <?php
}


//even before any taxonmy/terms are initialized, we reset the tables
add_action( 'init', 'fam_change_tax_terms_table', 0 );

//on blog switching, we need to reset it again, so it does not use current blog's tax/terms only
//it works both on switch/restore blog
add_action( 'switch_blog', 'fam_change_tax_terms_table', 0 );
 
function fam_change_tax_terms_table() {
    global $wpdb;
    //change terms table to use main site's
    $wpdb->terms = $wpdb->base_prefix . 'terms';    
    //change taxonomy table to use main site's taxonomy table
    $wpdb->term_taxonomy = $wpdb->base_prefix . 'term_taxonomy';
    //if you want to use a different sub sites table for sharing, you can replca e$wpdb->vbase_prefix with $wpdb->get_blog_prefix( $blog_id )
}

ob_end_clean();
?>