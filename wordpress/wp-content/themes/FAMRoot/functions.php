<?php
ob_start();
require_once( ABSPATH . '/wp-content/fam/functions.php' );

//CUSTOM FROM HERE
global $GetMultiSiteData;
$GetMultiSiteData = true;

if(strpos($_SERVER['REQUEST_URI'], "/?attachment_id=" ) > -1 && !is_admin())
{	
	header('Location: /');	
}


include_once('CustomContent/blog_post.php');
include_once('CustomContent/forum.php');
//include_once('CustomContent/dica.php');
include_once('CustomContent/informativo.php');

function fzmroot_custom_add_js() {
	$type = get_current_post_type();
	$uri = $_SERVER['REQUEST_URI'];
	$scriptLocation = "/wp-content/themes/FAM/CustomContent/js/";			
	if($type == 'albuns')
	{	
		if((strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{			
			wp_enqueue_script('album', $scriptLocation.'album.js', array('jquery', 'jquery-ui-core', 'google_maps','jquery-ui-sortable'), '0.0.1', true);			
		}	
	}
	if($type == 'blog_post')
	{	
		$scriptLocation = "/wp-content/themes/FAMRoot/CustomContent/js/";	
		if((strpos($uri,'post-new.php') > -1 || $_GET['action'] == 'edit') )
		{			
			wp_enqueue_script('blog_post', $scriptLocation.'blog_post.js', array('jquery', 'jquery-ui-core', 'google_maps','jquery-ui-sortable'), '0.0.1', true);			
		}	
	}
}
add_action('admin_enqueue_scripts', 'fzmroot_custom_add_js', 0);

function viajante_template() 
{
	global $wp_query;
	if ( strpos( $_SERVER['REQUEST_URI'], "/viajantes/") === 0 && $wp_query->is_404)
	{
		$uri = trim($_SERVER['REQUEST_URI'],"/");		
		$parans = explode("/",$uri);			
		$author = get_user_by('slug', $parans[1]);												
		if($author->ID != null && $author->ID > 1)
		{		
			global $viajante_id;
			$viajante_id = 	$author->ID;
			include_wordpress_template(TEMPLATEPATH . '/author.php');
			exit();			
		}		
	}
}
 
add_action('template_redirect', 'viajante_template');

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

function famroot_request($request) {     
    if (isset($request['feed']))
    {
		add_filter( 'posts_request', 'fam_disable_main_wp_query', 10, 2 );
		return $request;		
    }
	
	$uri = 	trim($_SERVER['REQUEST_URI'],"/");	
	$queryInit = strpos($uri,"?");
	if($queryInit !== false)
	{
		$uri = trim(substr($uri,0,$queryInit),"/");
	}	
	$uri_values = explode("/",$uri);
    	
		
	if($uri_values[0] == "viajantes" &&  count($uri_values) == 2)
	{ 		
    	$author = get_user_by('slug', $uri_values[1]);    			
					
    	require_once(ABSPATH. "/FAMCore/BO/Viajante.php");
    	//$hassites = Viajante::CheckIsValidViajante($author->ID);
    	
    	if($author->ID > 0 /*&& $hassites == true*/)
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
    if(in_array($uri, array("blog","forum",'dicas-de-viagem','albuns')))
	{		
		add_filter( 'posts_request', 'fam_disable_main_wp_query', 10, 2 );	
		if($uri == "blog")
		{
			$uri = "blog_post";
		}
		if($uri == "dicas-de-viagem")
		{
    		global $categoria_blog, $is_dica;
    		$is_dica = true;								
    		$categoria_blog = 'dicas-de-viagem';
			$uri = "blog_post";
			/*$uri = "dica";*/
		}
    	$request = array("post_type"=>$uri);//here i could set an invalid post-type so default wordpress query does not run		
	}
	else
	{
		$uri_values = explode("/",$uri);
    	if(in_array($uri_values[0], array("blog","forum",'dicas-de-viagem','albuns')) && count($uri_values) > 1 && $uri_values[1] != "" && $uri_values[1] != null)
		{		
			if($uri_values[0] == "blog")
			{
				$uri_values[0] = "blog_post";							
			}	
			if($uri == "dicas-de-viagem")
			{    			
				//$uri = "blog_post";
				$uri = "dicas";
			}
				
			if(count($uri_values) == 2)
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
					$cat = get_category_by_slug( $uri_values[1]);    					
					if($cat->term_id != null)
					{
						switch($uri_values[0])
						{
							case "blog_post":
								global $categoria_blog, $is_dica;
								$is_dica = in_array($uri_values[1],array('dicas-de-viagem'));								
								$categoria_blog = $uri_values[1];
								break;
							case "forum":
								global $categoria;
								$categoria = $cat;    							
								break;						
						}								
						$request = array("post_type"=>$uri_values[0]);		
					}
				}				
			}
			elseif(count($uri_values) == 3)	
			{		
				if($uri_values[0] == "blog_post")
				{		
					if(is_numeric($uri_values[1]) && is_numeric($uri_values[2]) && strlen($uri_values[1]) == 4 && strlen($uri_values[2]) == 2)
					{	
						add_filter( 'posts_request', 'fam_disable_main_wp_query', 10, 2 );		
						global $ano_blog;
						global $mes_blog;	
						$ano_blog = $uri_values[1];
						$mes_blog = $uri_values[2];						
						$request = array("post_type"=>$uri_values[0]);	
					}
					else
					{
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
				}
				else
				{									
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
			}				
		}		
	}	
	
	return $request;
}
add_filter('request', 'famroot_request');

remove_all_actions( 'do_feed_rss2' );
add_action( 'do_feed_rss2', 'famroot_feed_rss2', 10, 1 );
function famroot_feed_rss2( $for_comments ) {
    $rss_template = ABSPATH . '/wp-content/themes/FAMRoot/feeds/feed-rss2.php';	
    load_template( $rss_template );    
}

remove_all_actions( 'do_feed_rss' );
add_action( 'do_feed_rss', 'famroot_feed_rss', 10, 1 );

function famroot_feed_rss( $for_comments ) {
    $rss_template = ABSPATH . '/wp-content/themes/FAMRoot/feeds/feed-rss.php'; 	   
    load_template( $rss_template );
}

remove_all_actions( 'do_feed_atom' );
add_action( 'do_feed_atom', 'famroot_feed_rss', 10, 1 );

function famroot_feed_atom( $for_comments ) {
    $rss_template = ABSPATH . '/wp-content/themes/FAMRoot/feeds/feed-atom.php'; 	   
    load_template( $rss_template ); 	  
}
ob_end_clean();
?>