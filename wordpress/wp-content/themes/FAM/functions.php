<?php
ob_start();
require_once( ABSPATH . '/wp-content/fam/functions.php' );

function fzm_custom_add_js() {
	$type = get_current_post_type();
	$uri = $_SERVER['REQUEST_URI'];
	$scriptLocation = get_bloginfo('template_url')."/CustomContent/js/";			
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

function edit_admin_menu(){
	global $menu;
	//var_dump($menu;)
	// Change the order of the standard WP menu items
	//var_dump($menu);	
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


include_once('CustomContent/viagem.php');
include_once('CustomContent/relato.php');
include_once('CustomContent/destaque.php');
include_once('CustomContent/atualizacao.php');



add_action('admin_head', 'edit_admin_menu');
 
add_action('admin_enqueue_scripts', 'fzm_custom_add_js', 0);

add_action('delete_attachment','fam_pre_delete_attachment',1,1);
function fam_pre_delete_attachment( $attachment_id ){
	//remove attachment $attachment_id from albuns  and user images 
}

$facebook_publish_options = array('id'=>'604453679565803','name'=> 'Testes Códigocriativo', 'access_token'=>'CAAIULkJhlvYBAC71tUKZAByaTLlCEbzk9ZBIZB1lNYv0G3S4EYPCwQnS9FmuC77gJMpLEa3tZAOt0ZBIymenfHT2WGEbaJmGx3km1uAO3OZAfIjebxu36WERGkfdWzvZBOAe56EHBCdE9EZCF2btZBICkjx1W5VPulg81M8Dgk7bhfAIt01Du8DLv');	
if(strpos($_SERVER["SERVER_NAME"],"teste.") === false)
{
	$facebook_publish_options = array('id'=>'222705747761628','name'=> 'Fazendo as Malas - Descobrindo o mundo', 'access_token'=>'CAAIULkJhlvYBABZBXqNbKWgsZBFj4lbRxQIR9AfSIlongBvGNSf412jNroCc7HmYenQPaBz9xkDK8yMlkbAtfdg1b5JMDWTywS932K4zA6ZCmmRKKYnADNPCKyUtSGUjAcsWsmDZCQ4tZAiot0VJgHXnHDbGs00ebovaqlZC6XdwZDZD');
}	
//update_option("facebook_publish_page", $facebook_publish_options);


add_action( 'admin_head', 'fam_hide_widget_elements' );
function fam_hide_widget_elements()
{    
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
    <?
}

function famviagem_request($request) {
	global $is_mobile;		
	if(is_array($request) && $request["name"] == "m-admin" && $is_mobile)
	{
		$request = array();
	}
	$subsite_url = str_replace(network_home_url(),"/",get_bloginfo('url'))."/";
	$uri = 	trim(str_replace($subsite_url,"",$_SERVER['REQUEST_URI']),"/");	
	$queryInit = strpos($uri,"?");
	if($queryInit !== false)
	{
		$uri = trim(substr($uri,0,$queryInit),"/");
	}
	
	$uri_values = explode("/",$uri);	
	
	if($uri_values[0] == "viajantes" &&  count($uri_values) == 2)
	{		
    	$author = get_user_by('slug',$uri_values[1]);
    	require_once(ABSPATH. "/FAMCore/BO/Viajante.php");
    		
    	if($author->ID > 0 && Viajante::CheckIsValidViajante($author->ID))
    	{
    		$request = array("author_name"=>$uri_values[1]); 
    	}
    	else
    	{    		
    		
    		status_header( 404 );
    		nocache_headers();
    		include( get_query_template( '404' ) );
    		die();	 
    	}		
	}
    elseif(in_array($uri, array("albuns","relatos","status",'viagem','roteiro')))
	{		
		if($uri == "roteiro")
		{
    		wp_redirect( home_url()."/viagem/" ); exit;;
		}
		if($uri == "status")
		{
			$uri = "atualizacao";
		}
		add_filter( 'posts_request', 'fam_disable_main_wp_query', 10, 2 );	
		$request = array("post_type"=>$uri);		
	}
	elseif(count($uri_values) == 2)
	{
		if(in_array($uri_values[0], array("albuns","relatos","status")) && count($uri_values) == 2 && $uri_values[1] != "" && $uri_values[1] != null)
		{		
			
			if(is_numeric($uri_values[1]))
			{				
				$post = get_post($uri_values[1]);
				$can_preview = CheckCanPreview($post);
				if($post != null && $post->ID > 0 && $can_preview && $post->post_type == $uri_values[0])
				{					
					$request = array("p"=>$post->ID,"post_type"=>$uri_values[0]);																								
				}							
			}
			else
			{	
				add_filter( 'posts_request', 'fam_disable_main_wp_query', 10, 2 );	
				if($uri_values[0] == "status")
				{
					$uri_values[0] = "atualizacao";				
				}	
				$args = array('name' => $uri_values[1],'post_type' => $uri_values[0],'post_status' => 'publish','posts_per_page' => 1);
				$posts = get_posts($args);				
				if( is_array($posts) && count($posts)> 0)
				{					
					$request = array("p"=>$posts[0]->ID,"post_type"=>$uri_values[0]);							
				}
			}					
		}		
	}
	elseif(count($uri_values) == 3)	
	{		
		if($uri_values[0] == "status")
		{
			$uri_values[0] = "atualizacao";				
		}									
		if(is_numeric($uri_values[2]))
		{
			$post = get_post($uri_values[2]);
			$can_preview = CheckCanPreview($post);
			if($post != null && $post->ID > 0 && $can_preview && $post->post_type == $uri_values[0])
			{	
							
				$request = array("p"=>$uri_values[2],"post_type"=>$uri_values[0]);													
			}
						
		}		
	}	
		
	return $request;
}
add_filter('request', 'famviagem_request');

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