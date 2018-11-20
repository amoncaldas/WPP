<?php
global $is_mobile;

require_once( ABSPATH . '/wp-content/fam/CustomContent/media/media.php');

require_once( ABSPATH . '/wp-content/fam/admin/fam_options.php');

if (current_user_can('add_videos'))
{
	require_once( ABSPATH . '/wp-content/fam/CustomContent/media/youtube_video.php');
}	

if(get_current_blog_id() == 1)
{
	add_action( 'wp_enqueue_scripts', 'add_thickbox' );
}

	
if(strpos($_SERVER['REQUEST_URI'], "/?attachment_id=" ) > -1 && !is_admin())
{	
	header('Location: /');
}
if(strpos($_SERVER['REQUEST_URI'], "/?author=" ) > -1 && !is_admin())
{	
	header('Location: /');
}


remove_action ('wp_head', 'wp_generator');
remove_action ('wp_head', 'rsd_link');
remove_action( 'wp_head', 'wlwmanifest_link');

function fam_remove_wp_ver_meta_rss() {
    return '';
}

add_filter( 'the_generator', 'fam_remove_wp_ver_meta_rss' );

function fam_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'fam_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'fam_remove_wp_ver_css_js', 9999 );


function fam_remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
	if(get_current_blog_id() != 1)
	{
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	}
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	
	wp_add_dashboard_widget('custom_help_widget', 'Assinantes', 'custom_dashboard_help');	

}

function custom_dashboard_help() {
	global $wpdb;
    $news_users = $wpdb->get_results("SELECT count(*) as 'news_users' FROM wp_fam_news_subscribers");	
	echo "Há <strong>".$news_users[0]->news_users."</strong> assinantes de atualizações de viagens";
}

add_action('wp_dashboard_setup', 'fam_remove_dashboard_widgets' );


add_action("login_head", "my_login_head");
	

function my_login_head() {
	
	echo "
	<style>	
	body.login #login h1 a {
		background: url('/wp-content/themes/images/logo.png') no-repeat scroll center top transparent;
		height: 187px;
		width: 311px;
		background-size:200px;
	}
		
	#login {
		padding-top:30px !important;
	}
	</style>	
	";
	if(is_fam_mobile())
	{
		?>
		<style>
			#nav, #backtoblog
			{
				padding-left:5px !important;
			}
			.login #nav a, .login #backtoblog a
			{	
				color: #21759b!important;
				display: block;
				background: #fff;
				height: 20px;
				border: 1px solid;
				padding: 5px;
				text-align: center;
				padding-top: 12px;
				border:1px dashed #ccc;
				font-size: 16px;
			}
		</style>
		<?
	}
}



add_filter('comment_form_default_fields', 'remove_url');
function remove_url($val) {
    $val['url'] = '';
    return $val;    
}


function get_user_role() {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	return $user_role;
}

function is_site_home()
{
	if($_SERVER["REQUEST_URI"] == "/" || $_SERVER["REQUEST_URI"] == "/".str_replace(network_home_url(),"", get_bloginfo('url') )."/" )
	{
		return true;
	}
	return false;
}

function is_content_archive($post_type)
{
	$replace = str_replace(network_home_url(),"", get_bloginfo('url'));
	$url = str_replace("/".$replace,"", $_SERVER["REQUEST_URI"]);
	
	$queryInit = strpos($url,"?");
	if($queryInit !== false)
	{
		$uri = trim(substr($url,0,$queryInit),"/");
	}	
	
	if($url == "/".$post_type."/" )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_paged_archive_type($post_type)
{
	$paged_type = null;
	$replace = str_replace(network_home_url(),"", get_bloginfo('url'));
	$url = str_replace("/".$replace,"", $_SERVER["REQUEST_URI"]);
	
	$queryInit = strpos($url,"?");
	if($queryInit !== false)
	{
		$post_type = trim(substr($url,0,$queryInit),"/");
	}	
	else
	{
		$post_type = trim($url,"/");
	}
	
	if( in_array($post_type, array('blog','status','relatos')))
	{
		$paged_type = $post_type;
		if($post_type == 'blog')
		{
			$paged_type = "blog_post";
		}
		if($post_type == 'status')
		{
			$paged_type = "atualizacao";
		}		
	}
	return $paged_type;
}


function CropImage($imageVO, $maxDimension, $heightProportion = null)
{	
	require_once( $_SERVER["DOCUMENT_ROOT"]. '/wp-content/themes/incs/imagemanipulation.php');
	if($imageVO->ImageCroppedSrc != null)
	{
		$imgSrc = $imageVO->ImageCroppedSrc;			
	}
	else
	{
		$src = $imageVO->ImageFullSrc;	//todo change do medinum src
		if(strpos($src,"wp-content/blogs.dir/") === false)	
		{			
			$cleanUrl = str_replace("http://", "", $src);				
			$srcParts = explode("/",$cleanUrl);				
			$blog_id = get_blog_id_from_url( $srcParts[0], "/".$srcParts[1]."/" );
				
			$phisicalUrlPart = "wp-content/blogs.dir/".$blog_id;
			switch_to_blog($blog_id);
			$imgSrc = str_replace(get_bloginfo('url'),$phisicalUrlPart, $src);
			restore_current_blog();			
		}
	}	
	
	$path = "/wp-content/blogs.dir/".get_current_blog_id(). "/files/share_media/";
	$absSaveNameAndPath = $_SERVER["DOCUMENT_ROOT"].$path;
	if (!file_exists($absSaveNameAndPath)) {
		mkdir($absSaveNameAndPath, 0777, true);	
	}	

	if(strpos($imgSrc, $_SERVER['HTTP_HOST']) > -1)
	{
		$imgSrc = split($_SERVER['HTTP_HOST']."/", $imgSrc);
		$imgSrc = $imgSrc[1];	
	}	

	$imgSrc = $_SERVER["DOCUMENT_ROOT"]."/".$imgSrc;
	$absSaveNameAndPath = $absSaveNameAndPath."_mediaid_".$imageVO->MediaId."_".basename($imgSrc);	
	$imgSrc = str_replace("wp-content/blogs.dir/0/wp-content/","wp-content/",$imgSrc);
			
	$imgMaxHeight = ($maxDimension * $heightProportion);	
	$newBaseName = "_".$maxDimension."x".$imgMaxHeight.basename($absSaveNameAndPath);	
	$absSaveNameAndPath = str_replace(basename($absSaveNameAndPath),$newBaseName,$absSaveNameAndPath);
	
	if (!file_exists($absSaveNameAndPath)) {
		
		if(file_exists($imgSrc))
		{	
			$imgSize = getimagesize($imgSrc);					
			$x_start = 0;
			$y_start = 0;
			
			$imgMaxheight = ($maxDimension * $imgMaxHeight);
			
			if($imgSize[0] > $maxDimension || $imgSize[1] > $imgMaxHeight)
			{	
				if($imgSize[0] > $maxDimension)
				{
					$x_start = ($imgSize[0] - $maxDimension) / 2;
				}
				if($imgSize[1] > $maxDimension)
				{
					$y_start = ($imgSize[1] - $imgMaxHeight) / 2;
				}

				$objImage = new ImageManipulation($imgSrc);
				
				
				if ($objImage->imageok) {
					
					$maxSize = $imgSize[0];
					if($imgSize[1] > $imgSize[1] )
					{
						$maxSize = $imgSize[1];
					}				
						
					if($maxSize / $maxDimension > 1 || $maxSize / $maxDimension == 1 && $maxSize % $maxDimension > 0)
					{										
						$objImage->resize($maxDimension * $maxSize / $maxDimension);						
						$objImage->save($absSaveNameAndPath);
					}

					$objImage = new ImageManipulation($absSaveNameAndPath);					
					$objImage->setCrop($x_start, $y_start, $maxDimension, $imgMaxHeight);
						
					if (file_exists($absSaveNameAndPath)) {
						
						$objImage->save($absSaveNameAndPath);	
						$savedFile = $path.basename($absSaveNameAndPath);						
						return ConvertPhysicalPathInUrl($savedFile);
					}

					else
					{
						$savedFile = $path.basename($absSaveNameAndPath);
						return ConvertPhysicalPathInUrl($savedFile);
					}
				} 
			}
			
			$location = ConvertPhysicalPathInUrl($imgSrc);						
			return $location;
		}
		
		return $imageVO->ImageFullSrc;
		
		
	}

	return ConvertPhysicalPathInUrl($absSaveNameAndPath);
}

function ResizeImage($imgSource, $maxDimension)
{	
	require_once( $_SERVER["DOCUMENT_ROOT"]. '/wp-content/themes/incs/imagemanipulation.php');
	if(strpos($src,"wp-content/blogs.dir/") === false)
	{
		$cleanUrl = str_replace("http://", "", $imgSource);				
		$srcParts = explode("/",$cleanUrl);				
		$blog_id = get_blog_id_from_url( $srcParts[0], "/".$srcParts[1]."/" );				
		$phisicalUrlPart = "wp-content/blogs.dir/".$blog_id;
		switch_to_blog($blog_id);
		$imgSrc = str_replace(get_bloginfo('url'),$phisicalUrlPart, $imgSource);
		restore_current_blog();				
	}
	else
	{		
		$cleanUrl = str_replace("http://", "", $imgSource);				
		$srcParts = explode("/",$cleanUrl);				
		$blog_id = get_blog_id_from_url( $srcParts[0], "/".$srcParts[1]."/" );				
		$phisicalUrlPart = "wp-content/blogs.dir/".$blog_id;
		switch_to_blog($blog_id);
		$imgSrc = str_replace(get_bloginfo('url'),$phisicalUrlPart, $src);
		restore_current_blog();			
	}	
	
	$path = "/wp-content/blogs.dir/".get_current_blog_id(). "/files/cropped_destaque/";
	$absSaveNameAndPath = $_SERVER["DOCUMENT_ROOT"].$path;
	if (!file_exists($absSaveNameAndPath)) {
		mkdir($absSaveNameAndPath, 0777, true);	
	}	

	if(strpos($imgSrc, $_SERVER['HTTP_HOST']) > -1)
	{
		$imgSrc = split($_SERVER['HTTP_HOST']."/", $imgSrc);
		$imgSrc = $imgSrc[1];	
	}	

	$imgSrc = $_SERVER["DOCUMENT_ROOT"]."/".$imgSrc;
	$absSaveNameAndPath = $absSaveNameAndPath."_destaque-viagem_".basename($imgSrc);	
			
	if (!file_exists($absSaveNameAndPath)) {		
		
		if(file_exists($imgSrc))
		{
			$imgSize = getimagesize($imgSrc);
			$x_start = 0;
			$y_start = 0;
			if($imgSize[0] > $maxDimension || $imgSize[1] > $maxDimension)
			{	
				if($imgSize[0] > $maxDimension)
				{
					$x_start = ($imgSize[0] - $maxDimension) / 2;
				}
				if($imgSize[1] > $maxDimension)
				{
					$y_start = ($imgSize[1] - $maxDimension) / 2;
				}

				$objImage = new ImageManipulation($imgSrc);
				if ($objImage->imageok) {
					$maxSize = $imgSize[0];
					if($imgSize[1] > $imgSize[1] )
					{
						$maxSize = $imgSize[1];
					}				

					if($maxSize > $maxDimension)
					{
						$objImage->resize($maxDimension);
						$objImage->save($absSaveNameAndPath);
					}
					
					$savedFile = $path.basename($absSaveNameAndPath);
					return ConvertPhysicalPathInUrl($savedFile);					
				} 
			}
			
			return ConvertPhysicalPathInUrl($absSaveNameAndPath);
		}
		
		return $imgSource;	
	}

	return ConvertPhysicalPathInUrl($absSaveNameAndPath);
}

function ConvertPhysicalPathInUrl($phisicalPath)
{
		
	if( strrpos($phisicalPath, "/wp-content/uploads/") === false)
	{
		$fileParts = split("wp-content/blogs.dir/",$phisicalPath);
		$blogId = split("/",$fileParts[1]);
		$blogId = $blogId[0];	
		switch_to_blog($blogId);
		$returnFileName = get_bloginfo('url')."/".$fileParts[1];
		restore_current_blog();
		return str_replace($blogId."/","",$returnFileName);
	}
	else
	{
		$fileParts = split(network_home_url(),$phisicalPath);
		if(get_current_blog_id() != 1)
		{
			switch_to_blog(1);
			$returnFileName = get_bloginfo('url')."/".$fileParts[1];
			restore_current_blog();
		}		
		$returnFileName = get_bloginfo('url')."/".$fileParts[1];		
	}
	
}


/**
 * gets the current post type in the WordPress Admin
 */
function get_current_post_type() {

  global $post, $typenow, $current_screen;
  //we have a post so we can just get the post type from that

  if ($post && $post->post_type )
  {
    return $post->post_type;
  }   

  //check the global $typenow - set in admin.php

  elseif( $typenow )
  {
    return $typenow;
  }    

  //check the global $current_screen object - set in sceen.php

  elseif( $current_screen && $current_screen->post_type )
  {
	return $current_screen->post_type; 
  }

  //lastly check the post_type querystring

  elseif( isset( $_REQUEST['post_type'] ) )
  {
    return sanitize_key( $_REQUEST['post_type'] );
  }

  elseif (isset($_REQUEST['post_id'])) {
	return get_post_type($_REQUEST['post_id']);
  }

  else if(isset($_GET["post"]) && $_GET["post"] != null && isset($_GET["action"]) && $_GET["action"] == "edit")
  {	
	return get_post_type($_GET["post"]);
  }

  //we do not know the post type!

  return null;
}


function PrintImages($post_ID, $prefix = null, $itens = null,$size = null)
{
	$prefix = ($prefix == null)? "" : $prefix;
	$upid = $prefix."_fam_upload_id_";
	$upname = $prefix."_fam_upload_name_";
	
	$counter = 0;
	$imgsId = get_post_meta($post_ID, $upid, true);
	
	if(strpos($imgsId,";") > -1)
	{
		$ids = explode(";",$imgsId);
	}

	elseif($imgsId != null && $imgsId != "" )
	{
		$ids[0] = $imgsId;
	}
	else
	{
		$ids = array();
	}
	
	if(is_array($ids) && count($ids) > 0)
	{
		$mover =  "<span class='mover'></span>";
		foreach($ids as $imgId)
		{
			if($itens == null || $counter < $itens)
			{
				$postAttach = get_post($imgId, 'ARRAY_A' );
				
				$imgName =	$postAttach['post_title'];	
				if ($counter == 0) {
					echo "<ul>";
				}
				if($postAttach['post_mime_type'] == "video/x-flv")
				{					
					$videoUrlParts = explode("embed", $postAttach['guid']);						
					$videoCode = trim($videoUrlParts[1],"/");												
					$src = "http://img.youtube.com/vi/".$videoCode."/hqdefault.jpg";
					$sizeStyle = ($size == "gallery")? " style='width:190px;height:140px' " : (($size == "thumb")? " style='width:120px;height:69px' ": "");
					$src = "<a class='fancybox fancybox.iframe' href='http://www.youtube.com/embed/".$videoCode."' rel='permalink' title='".$imgName."'><img ".$sizeStyle." src='".$src. "' /></a>";
				}
				else
				{		
					require_once( ABSPATH. '/FAMCore/VO/ImagemVO.php' );
					$imagemVO = new ImagemVO($imgId);					
					if($size != null)	
					{
						switch($size)
						{
							case "thumb":
								$src = $imagemVO->ImageThumbSrc;
								break;	
							case "gallery":
								$src = $imagemVO->ImageGaleryThumbSrc;
								break;
							default:
								$src = $imagemVO->ImageGaleryThumbSrc;	
								break;													
						}
					}
					else
					{							
						$src = $imagemVO->ImageGaleryThumbSrc;	
					}		
					
					if(get_current_blog_id() != 1)
					{
						$src = str_replace("/wp-content/uploads/", "/files/", $src);
					}
					$src = "<a class='fancybox' href='".$imagemVO->ImageLargeSrc."' rel='permalink' title='".$imagemVO->Descricao."'><img src='".$src. "' /></a>";				
				}	
				
				$is_cover = get_post_meta($imgId, '_fam_upload_is_cover_', true);		
				$checked = $is_cover == "cover"? " checked='checked' ": "";				
				
					echo "<li>
							".$mover.$src;
					if(is_admin())
					{
						echo "							
							<input type='hidden' value='".$imgId."' id='".$upid.$imgId."' name='".$upid.$imgId."' />
							<input type='text' name='".$upname.$imgId."' id='".$upname.$imgId."' value='".$imgName."' />
							<span title='Remover item' class='fam_delete_img'></span>								
							<span class='cover_label'>É capa</span>
							<input value='cover' ".$checked." name='_fam_upload_is_cover_".$imgId."' class='fam_is_cover' id='_fam_upload_is_cover_".$imgId."' type='checkbox'/>
						</li>";  
					}      

				$counter++;	
			}
		}

		if($counter > 0)
		{
			echo "</ul>";
		}

	}

	

}

/* Save custom field value */

function fam_image_attachment_save_custom_fields($post, $attachment) {

    if(isset($attachment['attachment_location'])) {

        update_post_meta($post['ID'], 'attachment_location', $attachment['attachment_location']);

    } else {

        delete_post_meta($post['ID'], 'attachment_location');
    }

	if(isset($attachment['attachment_latitude'])) {

        update_post_meta($post['ID'], 'attachment_latitude', $attachment['attachment_latitude']);

    } else {
        delete_post_meta($post['ID'], 'attachment_latitude');
    }

	if(isset($attachment['attachment_longitude'])) {

        update_post_meta($post['ID'], 'attachment_longitude', $attachment['attachment_longitude']);

    } else {

        delete_post_meta($post['ID'], 'attachment_longitude');
    }

    return $post;
}

add_filter("attachment_fields_to_save", "fam_image_attachment_save_custom_fields", null , 2);

function SaveImages($parentId, $postArray, $prefix = null)
{
	$prefix = ($prefix == null)? "" : $prefix;	
	$upid = $prefix."_fam_upload_id_";
	$upname = $prefix."_fam_upload_name_";	
	$cover_field_name = "_fam_upload_is_cover_";
	$coverIdentified = false;
	if(is_array($postArray) && count($postArray) > 0)
	{
		$ids= "";	
			
		foreach($postArray as $key=>$value)
		{
			if(strpos($key,$upid) > -1)
			{
				$attachId = $value;	
				if($ids != "")
				{
					$ids.= ";".$value;
				}
				else
				{
					$ids.= $value;
				}	
			}

			if(strpos($key,$upname)> -1)
			{
				$current_post = get_post($attachId, 'ARRAY_A' );
				$current_post['post_title'] = $value;
				wp_update_post($current_post);
			}
			
			delete_post_meta($attachId, $cover_field_name);	
			
			if(strpos($key,$cover_field_name)> -1 )
			{			
				if($value == "cover" && $coverIdentified == false)
				{
					update_post_meta($attachId, $cover_field_name,"cover");
					$coverIdentified = true;
					//$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>' . "\r\n";
					//$body .= "updated".$attachId."\r\n";	
					//mail("amoncaldas@yahoo.com.br",'debug fazendoasmalas',$body,$headers);
				}
				
				
			}
		}
		update_post_meta($parentId, $upid,$ids);
	}	

}

add_action('wp_insert_comment','remove_comment_from_default_table');
function remove_comment_from_default_table( $id, $comment_obj){	
	if($comment_obj->comment_approved == 1)	
	{
		$template = file_get_contents(ABSPATH.'/Templates/Mail/news.html');
		$template = str_replace("{site-url}", get_bloginfo('url'), $template);
		$template = str_replace("{content-url}", get_comment_link($comment_obj), $template);
		$template = str_replace("{content-image-src}", "", $template);
		$template = str_replace("{main-img-height}", "0", $template);
		$template = str_replace("{content-excerpt}", $comment_obj->comment_content, $template);
		$template = str_replace("{content-title}", "Houve um novo comentário numa participação sua no fórum FazendoasMalas", $template);	
		$template = str_replace("teste.faz", "faz", $template);	
        $template = str_replace("dev.faz", "faz", $template);	
		$template = str_replace("{other-itens}", "", $template);	
		$template = str_replace("{news-type}", 'Novo comentário de fórum', $template);		
		$message = $template;	
			
		global $wpdb;		
		$sql_news_pending = "INSERT INTO wp_fam_pending_mail( subject, content, content_type, mail_list_type, site_id ) VALUES ('Alguém te respondeu no fórum Fazendo as Malas','".$message."','html','comment_post_".$comment_obj->comment_post_ID."', ".get_current_blog_id().")";	
		$wpdb->query($sql_news_pending);
	}
}

add_action('transition_comment_status', 'fam_approve_comment_callback', 10, 3);
function fam_approve_comment_callback($new_status, $old_status, $comment_obj) {
    if($old_status != $new_status) {
		if($new_status == 'approved' || $new_status==1) {           
			$template = file_get_contents(ABSPATH.'/Templates/Mail/news.html');
			$template = str_replace("{site-url}", get_bloginfo('url'), $template);
			$template = str_replace("{content-url}", get_comment_link($comment_obj), $template);
			$template = str_replace("{content-image-src}", "", $template);
			$template = str_replace("{main-img-height}", "0", $template);
			$template = str_replace("{content-excerpt}", $comment_obj->comment_content, $template);
			$template = str_replace("{content-title}", "Houve um novo comentário numa participação sua no fórum FazendoasMalas", $template);			
			$template = str_replace("{other-itens}", "", $template);		
			$template = str_replace("teste.faz", "faz", $template);	
            $template = str_replace("dev.faz", "faz", $template);	
			$template = str_replace("{news-type}", 'Novo comentário de fórum', $template);				
			$message = $template;	
			
			global $wpdb;		
			$sql_news_pending = "INSERT INTO wp_fam_pending_mail( subject, content, content_type, mail_list_type, site_id ) VALUES ('Alguém te respondeu no fórum Fazendo as Malas','".$message."','html','comment_post_".$comment_obj->comment_post_ID."', ".get_current_blog_id().")";	
			$wpdb->query($sql_news_pending);
			
        }
    }
}

function custom_author_base() {
	global $wp_rewrite;   
	$wp_rewrite->author_base = 'viajantes';
	$wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
}

add_action( 'init', 'custom_author_base' );

//SET LARGE IMAGEM AS DEFAULT IMAGE AND DELETE FULL IMAGE UPLOADED

function replace_uploaded_image($image_data) {

    // if there is no large image : return

    if (!isset($image_data['sizes']['large'])) return $image_data;   

    // paths to the uploaded image and the large image

    $upload_dir = wp_upload_dir();
    $uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];
    $large_image_location = $upload_dir['basedir'] . '/' . substr($image_data['file'], 0, 8).$image_data['sizes']['large']['file'];     
	
	
	$imgSize = getimagesize($large_image_location);			
			
	if($imgSize[0] > 350 && $imgSize[1] > 350)
	{	
		// delete the uploaded image
		unlink($uploaded_image_location);
		
		// rename the large image
		rename($large_image_location,$uploaded_image_location);
		
		// update image metadata and return them
		$image_data['width'] = $image_data['sizes']['large']['width'];
		$image_data['height'] = $image_data['sizes']['large']['height'];
		unset($image_data['sizes']['large']);
		return $image_data;
	}
	else
	{
		unlink($large_image_location);				
		unset($image_data['sizes']['large']);
		return $image_data;
	}
		
    

}

add_filter('wp_generate_attachment_metadata','replace_uploaded_image');


function include_wordpress_template($t) { 
    if ($wp_query->is_404) {
        $wp_query->is_404 = false;
        $wp_query->is_archive = true;
		header("HTTP/1.1 200 OK");
    }	
	include($t);    
}

add_action('admin_enqueue_scripts', 'fzm_custom_js', 0);

function fzm_custom_js() {		
	wp_enqueue_style('uploadField', '/wp-content/themes/css/uploadField.css');
	wp_enqueue_style('fancybox_css', '/wp-content/themes/js/fancybox/jquery.fancybox.css?v=2.1.2');	
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	if(is_admin())
	{
		wp_enqueue_style('thickbox');
	}
	
	wp_enqueue_script('fam_action', '/wp-content/fam/js/action.js', array('jquery', 'jquery-ui-core','jquery-ui-sortable','media-upload'), '0.0.1', true);	
	wp_enqueue_script('uploadField', '/wp-content/fam/js/uploadField.js', array('jquery', 'jquery-ui-core','jquery-ui-sortable','media-upload', 'fam_action'), '0.0.1', true);	
	wp_enqueue_script('datepick-pt-BR', '/wp-content/fam/js/jquery.datepick-pt-BR.js', array('jquery', 'jquery-ui-core','jquery-ui-datepicker', 'jquery-ui-sortable'), '0.0.1', true);
	wp_enqueue_script('jquery.maskedinput', '/wp-content/fam/js/jquery.maskedinput-1.3.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '0.0.1', true);		
	wp_enqueue_script('google_maps', 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places,weather&language=pt-BR', array('jquery'), '3.0', true);
	wp_enqueue_script('media-location', '/wp-content/fam/CustomContent/media/media-location.js', array('jquery', 'google_maps','jquery-ui-core', 'jquery-ui-sortable'), '0.0.1', true);
	wp_enqueue_script('fancy_box', '/wp-content/fam/js/fancybox/jquery.fancybox.js?v=2.1.3R', array('jquery'), '2.1', true);	
	wp_enqueue_script('famAdminFunctions', '/wp-content/fam/js/famAdminFunctions.js', array('fancy_box'), '1.0', true);
	
	
	$uri = $_SERVER['REQUEST_URI'];
	if (strpos($uri,'user-edit') > -1 || strpos($uri,'profile.php') > -1)
	{ 
		wp_enqueue_script('user-location', '/wp-content/fam/js/user-location.js', array(), '0.0.1', true);			
	}
}


include_once('CustomContent/viajante.php');
include_once('CustomContent/album.php');


//add_action( 'comment_form_after', 'tinyMCE_comment_form' );
function tinyMCE_comment_form() {
	?> <script type="text/javascript" src="<?php echo includes_url( 'js/tinymce/tiny_mce.js' ); ?>"></script>;
    <script type="text/javascript">
        tinyMCE.init({
            theme : "advanced",
            mode: "specific_textareas",
            language: "pt_BR",
            skin: "default",
            theme_advanced_buttons1: 'bold, italic, underline, blockquote, strikethrough, bullist,numlist,undo,redo,outdent,indent,anchor,cleanup,insertdate,inserttime,preview,,forecolor,backcolor,fullscreen',
            theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			charLimit : 3000,
            elements: 'comment',
            theme_advanced_toolbar_location : "top",
			theme_advanced_path : false,
			theme_advanced_resizing : true,
			plugins: "autoresize",
			plugins: "fullscreen",
			toolbar: "fullscreen",
			content_css : "/wp-content/themes/css/comment_content_tinymce.css",			
        });
    </script>
<?php
}


function posts_for_current_author($query) {
	global $pagenow;

	if( 'edit.php' != $pagenow || !$query->is_admin )
		return $query;

	if( !current_user_can( 'manage_options' ) ) {
		global $user_ID;
		$query->set('author', $user_ID );
	}
	return $query;
}
//add_filter('pre_get_posts', 'posts_for_current_author');

add_action( 'init', 'register_category_again', 1 );

function register_category_again() {   
	global $wp_taxonomies;
	unset( $wp_taxonomies['category'] );
	global $wp_rewrite;
	$rewrite = array(
		'hierarchical' => true,
		'slug' => get_option('category_base') ? get_option('category_base') : 'category',
		'with_front' => ! get_option('category_base') || $wp_rewrite->using_index_permalinks(),
		'ep_mask' => EP_CATEGORIES,
		);
	register_taxonomy( 'category', array('blog_post','forum'), array(
		'hierarchical' => true,
		'query_var' => 'category_name',
		'rewrite' => $rewrite,
		'public' => true,
		'capabilities' => array(
					'manage_terms'=> 'manage_categories',
					'edit_terms'=> 'manage_categories',
					'delete_terms'=> 'manage_categories',
					'assign_terms' => 'edit_posts'
					),
				'show_ui' => true,
				'show_admin_column' => true,
				'_builtin' => true,
				) );
}


function fam_admin_users_caps( $caps, $cap, $user_id, $args ){
	
	foreach( $caps as $key => $capability ){
		
		if( $capability != 'do_not_allow' )
			continue;
		
		switch( $cap ) {
			case 'edit_user':
			case 'edit_users':
				$caps[$key] = 'edit_users';
				break;
			case 'delete_user':
			case 'delete_users':
				$caps[$key] = 'delete_users';
				break;
			case 'create_users':
				$caps[$key] = $cap;
				break;
		}
	}
	
	return $caps;
}
add_filter( 'map_meta_cap', 'fam_admin_users_caps', 1, 4 );
remove_all_filters( 'enable_edit_any_user_configuration' );
add_filter( 'enable_edit_any_user_configuration', '__return_true');

/**
 * Checks that both the editing user and the user being edited are
 * members of the blog and prevents the super admin being edited.
 */
function fam_edit_permission_check() {
	global $current_user, $profileuser; 
	$screen = get_current_screen(); 
	get_currentuserinfo();
	
	if( ! is_super_admin( $current_user->ID ) && in_array( $screen->base, array( 'user-edit', 'user-edit-network' ) ) ) { // editing a user profile
		if ( is_super_admin( $profileuser->ID ) ) { // trying to edit a superadmin while less than a superadmin
			wp_die( __( 'You do not have permission to edit this user.' ) );
		} elseif ( ! ( is_user_member_of_blog( $profileuser->ID, get_current_blog_id() ) && is_user_member_of_blog( $current_user->ID, get_current_blog_id() ) )) { // editing user and edited user aren't members of the same blog
			wp_die( __( 'You do not have permission to edit this user.' ) );
		}
	}
	
}
add_filter( 'admin_head', 'fam_edit_permission_check', 1, 4 );


function fam_get_editable_roles_for_new_user( $editable_roles ) {
	global $pagenow;
	if ( 'user-new.php' == $pagenow || 'user-edit.php' == $pagenow ) {
		
		if(!is_super_admin())
		{			
			unset( $editable_roles['administrator'] );	
			//unset( $editable_roles['blog_writer'] );	
			unset( $editable_roles['adm_fam_root']);	
		}	
		if(get_current_blog_id() != 1)
		{			
			unset( $editable_roles['adm_fam_root'] );
			unset( $editable_roles['blog_writer'] );			
			unset( $editable_roles['usuario_forum'] );
			$editable_roles['administrator']['name'] = "Administrador de site de viagem";	
		}	
		else
		{
			unset( $editable_roles['administrator']);	
			unset( $editable_roles['viajante'] );
		}	
		
		unset( $editable_roles['editor'] );
		unset( $editable_roles['author'] );
		unset( $editable_roles['contributor'] );
		unset( $editable_roles['subscriber'] );		
	}
	return $editable_roles;

}
add_filter( 'editable_roles', 'fam_get_editable_roles_for_new_user' );

add_action('pre_user_query','fam_pre_user_query');
function fam_pre_user_query($user_search) {
	$user = wp_get_current_user();
	if ($user->ID!=1) { // Is not administrator, remove administrator
		global $wpdb;
		$user_search->query_where = str_replace('WHERE 1=1',
			"WHERE 1=1 AND {$wpdb->users}.ID<>1",$user_search->query_where);
	}
}


add_filter('login_redirect', 'fam_after_login_redirect', 10, 3);
function fam_after_login_redirect( $redirect_to, $request, $user ) {
	// is there a user ?
	
	if (get_the_author_meta('status_viajante',$user->ID) == 'disabled')
	{		
		$action = " entre em contato com o administrador do site de viagem. Voltar ao <a href='".get_site_url()."'>site</a>.";
		if(get_current_blog_id() == 1)
		{
			$action = " entre em contato pelo  <a href='/contato'>formulário de contato</a> ou volte para o <a href='".get_site_url()."'>site</a>.";
		}
		wp_logout();
		wp_die( __('<div style="color:red;font-size:20px">Acesso bloqueado</div>
		<div style="font-weight:bold">Este usuário está com o acesso bloqueado. Em caso de dúvidas '.$action.' </div>'));
	}
	if(is_array($user->roles)) {
		
		if(get_current_blog_id() == 1)
		{
			if(in_array('usuario_forum', $user->roles)) {					
				return admin_url('edit.php?post_type=forum');
			}			
		}
		
	}
	return $redirect_to;
}

add_filter('user_contactmethods','fam_remove_contactmethod',10,1);
function fam_remove_contactmethod( $contactmethods ) {
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	return $contactmethods;
}


add_filter('manage_posts_columns', 'fam_post_add_post_author_column', 5);
function fam_post_add_post_author_column($columns){
	$columns['fam_post_author'] = __('Author');
	return $columns;
}

add_action('manage_users_columns','remove_user_posts_column');
function remove_user_posts_column($column_headers) {
	unset($column_headers['posts']);
	return $column_headers;
}

add_filter('manage_posts_custom_column', 'fam_display_post_author_column', 5, 2);
function fam_display_post_author_column($column, $post_id ){
	global $post;
	switch( $column ) {		
		case 'fam_post_author' :
			$authorId = $post->post_author;
			if($authorId == 1)
			{
				$authorId = 2;
			}		
			$userData = get_userdata($authorId);					
			if ( $userData != null )
			{
				echo "<span>".$userData->display_name."</span>";
			}
			break;
		default :
			break;
	}
}


/* make author column sortable */
//add_filter( 'manage_post_sortable_columns', 'fam_posts_author_sortable_columns' );

function fam_posts_author_sortable_columns( $columns ) {
	$columns['fam_post_author'] = 'fam_post_author';
	return $columns;
}

if($_GET["s"] != null && get_current_blog_id() == 1)//todo bug pre_get_posts $query->set('post_type') vs admin bar
{
	add_filter('show_admin_bar', '__return_false');
}


add_filter('parse_query', 'fam_filter_query' );
function fam_filter_query($query, $error = true ) {
	if ( is_search() && !is_admin() ) {
		$query->is_search = false;
		/*$query->query_vars["s"] = false;
		$query->query["s"] = false;*/
		
		// to error
		if ( $error == true )
		{
			$query->is_404 = true;
		}
	}
}

function search_template() {	
	if ($_GET['s'] != NULL)		{	
		include_wordpress_template(TEMPLATEPATH . '/search.php');			
		exit();				
	}
}
add_action('template_redirect', 'search_template');



function sitemap_template() {
	global $wp_query;	
	if(!empty($wp_query->query_vars["xml_sitemap"])) {
		
		$wp_query->is_404 = false;				
	}	
}
add_action('template_redirect', 'sitemap_template');


add_action('wp_trash_post','fam_pre_trash',1,1);
function fam_pre_trash( $postid ){
	do_action('untrash_post', $postid);
	$post_data = get_post($postid, ARRAY_A);
	$slug = $post_data['post_name']; 
	$post_type = $post_data['post_type'];	
	if (($post_type == 'page' && $slug == 'viajantes') || $post_type == 'viagem')
	{		
		wp_die( __('<div style="color:red;font-size:20px">Item não pode ser removido</div>
		<div style="font-weight:bold">Não é possível remover este item Volte para o <a href="index.php">painel</a></div>'));			
	}    
}

add_filter('gettext', 'remove_admin_stuff', 20, 3);

function remove_admin_stuff( $translated_text, $untranslated_text, $domain ) {

	if ( !current_user_can('update_core') ) {
		
		$custom_field_text = 'You are using <span class="b">WordPress %s</span>.';
		if ( is_admin() && $untranslated_text === $custom_field_text ) {
			return '';
		}
		if($untranslated_text == 'Theme <span class="b">%1$s</span>')
		{
			return '';
		}		
	}	
	return $translated_text;	
}

add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

function remove_admin_bar_links() {
	global $wp_admin_bar;	
	//var_dump($wp_admin_bar->nodes["user-actions"]["blog-1-n"]);
	$wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
	$wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
	$wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
	$wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
	$wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
	$wp_admin_bar->remove_menu('feedback');  
	$wp_admin_bar->remove_menu('blog-1-n');       // Remove the feedback link
	//$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
	//$wp_admin_bar->remove_menu('view-site');        // Remove the view site link
	$wp_admin_bar->remove_menu('updates');          // Remove the updates link
	//$wp_admin_bar->remove_menu('comments');         // Remove the comments link
	//$wp_admin_bar->remove_menu('new-content');      // Remove the content link
	$wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
	//$wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
	$wp_admin_bar->remove_menu('new-viagem');
	global $current_user;
	$blogs = get_blogs_of_user( $current_user->id );
	{
		if($blogs) {
			foreach ( $blogs as $blog ) {				
				$wp_admin_bar->remove_menu('blog-'.$blog->userblog_id.'-n');
				$wp_admin_bar->remove_menu('blog-'.$blog->userblog_id.'-c');
			}
		}
		if(get_current_blog_id() != 1)
		{
			$wp_admin_bar->remove_menu('comments');
		}
		//$wp_admin_bar->remove_menu('new-content');
		$wp_admin_bar->remove_menu('new-post');
		$wp_admin_bar->remove_menu('new-page');
	}
	
	global $facebook_loader;

	$wp_admin_bar->add_menu( array(
		'id' => 'comments',
		'title' => '<span class="ab-icon"></span><span id="ab-awaiting-mod" class="ab-label awaiting-mod"></span>',
		'href' => 'https://developers.facebook.com/tools/comments?' . http_build_query( array( 'id' => $facebook_loader->credentials['app_id'] ) ),
		'meta' => array( 'target' => '_blank', 'title' => __( 'Moderar comentários do facebook', 'facebook' ) )
		) );
}

add_action("user_register", "set_user_admin_bar_true_by_default", 10, 1);
function set_user_admin_bar_true_by_default($user_id) {
	update_user_meta( $user_id, 'show_admin_bar_front', 'true' );
	update_user_meta( $user_id, 'show_admin_bar_admin', 'true' );
}

function deny_create_new_native_content()
{
	global $pagenow;	
	$invalidpagenow = ( $pagenow =='post-new.php' || $pagenow =='edit.php');
	$invalidType = (!isset($_GET['post_type']) || ( $_GET['post_type'] == "page" && get_current_blog_id() != 1 ) );	
	if(($invalidpagenow === true && $invalidType === true) || ($pagenow == 'edit-comments.php' && get_current_blog_id() != 1))
	{		
		wp_die( __('<div style="color:red;font-size:20px">Sem permissão</div>
		<div style="font-weight:bold">Você não ter permissão para esta ação. Volte para o <a href="index.php">painel</a></div>') );		
		
	}
	
}

add_action('init', 'deny_create_new_native_content');

add_filter('admin_footer_text', 'fam_version', 9999);
function fam_version () {
    global $wp_version;
    return 'FAM Versão 0.9.5 | WP '.$wp_version;;
}


add_filter( 'update_footer', 'change_footer_version', 9999);


function change_footer_version() {
	$footer = 'Obrigado por usar o <strong>FazendoAsMalas.com</strong><span id="site_id" style="display:none">'.get_current_blog_id().'</span>';
	if(in_array(get_user_role(), array('viajante','administrator')) )
	{
		$footer .= '<a style="margin-left:20px;" href="'.trailingslashit(get_bloginfo('home')).'?cf_action=show_mobile">Versão móvel</a>';
	}
	return $footer;
	
}

function GetSubContent($content, $lenght, $forcePrecision = false)
{
	$lenght = $lenght-3;
	$content = strip_tags($content, '');
	if(strlen($content) > $lenght)
	{		
		if($forcePrecision == true)	
		{
			
			$content = trim($content);					
			if(strpos($content, " ") > 0 )
			{		
				$content = substr($content, 0,$lenght + 200);						
				$offset = strlen($content);	
				$spacePosition = 	strlen($content);	
				while( ($pos = strrpos(($content)," ",$offset)) != false) {
					$offset = $pos;
					if($pos < $lenght)
					{
						
						$spacePosition = $pos;							
						break;
					}
				}		
													
				$content = substr($content, 0, $spacePosition)."...";
				
				if(strlen($content) > $lenght + 3)
				{
					$content = substr($content, 0, $lenght)."...";
				}
				
			}
			else
			{																		
				$content = substr($content, 0,$lenght)."...";
			}	
				
				
		}
		else
		{
			if(strlen($content) > $lenght)
			{			
				if(strpos($content, " ", $lenght) > $lenght)
				{
					$content =  substr($content, 0, strpos($content, " ", $lenght))."...";
				}
				else
				{
					$lenght = ($lenght > 20)? ($lenght -20): 0;								
					$content =  substr($content, 0, strpos($content, " ", $lenght))."...";
				}				
			}
		}	
		//$content = rtrim($content, '.');
		$content = rtrim($content, ',');
		$content = rtrim($content, '-');		
		return $content;
	}
	else
	{
		return $content;
	}			
}

function GetMediaSafeUrl($media_url)
{
	$media_url = str_replace("/","__",$media_url);
	$media_url = str_replace(":",";",$media_url);
	return $media_url;
}

add_filter( 'fb_meta_tags', 'custom_og', 10,2 ); 
function custom_og( $meta_array, $post )
{	
	//var_dump("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
	$meta_array["http://ogp.me/ns#url"] = "http://".$_SERVER["SERVER_NAME"].str_replace(":",";",$_SERVER["REQUEST_URI"]);	
	if(strpos($meta_array["http://ogp.me/ns#site_name"],'Fazendo as Malas') === false)
	{
		$meta_array["http://ogp.me/ns#site_name"] .=" | Fazendo as Malas";
	}		
	
	unset($meta_array["http://ogp.me/ns#description"]);
	unset($meta_array["http://ogp.me/ns#title"]);
	unset($meta_array["http://ogp.me/ns#type"]);	
	//$meta_array['http://ogp.me/ns#locale']	
	
	return $meta_array; 	 
}

add_action('admin_head', 'fam_header');
function fam_header() {
	
	$type = get_current_post_type();
	if($type == 'blog_post')
	{
		$type = 'blog';
	}
	elseif($type == 'atualizacao')
	{
		$type = 'status';
	}	
	if ($type != null) 
	{ 
		?>
			<style>
				#icon-edit { background:transparent url('/wp-content/themes/images/icons/icon-<?echo $type;?>.png') no-repeat; width: 40px;height: 40px;}
				.wrap>h2{line-height:30px;}
				#icon-users { background:transparent url('/wp-content/themes/images/icons/icon-viajantes.png') no-repeat !important; width: 40px;height: 40px;}				
				<? if($type == "forum") {?>	#excerpt { height:150px;}<?}?>
			</style>	
				
		<?  
		  
	}; 
	?>
		<style>			
			.places_suggestion {border: 1px solid #ccc;padding: 5px;background: #fff;color: #6d6d6d;margin: 0px !important;}
			.places_suggestion li {border-bottom: 1px solid #dfdfdf;cursor: pointer;background: url(/wp-content/themes/images/icons/16px/icon-pino.png) no-repeat top right;min-height: 20px;padding-right: 17px;}
			.places_suggestion li.no_result {cursor:default;}
		</style>
		<!--- facebook sdk --->	
		<script type='text/javascript'>
		/* <![CDATA[ */
		var FB_WP=FB_WP||{};FB_WP.queue={_methods:[],flushed:false,add:function(fn){FB_WP.queue.flushed?fn():FB_WP.queue._methods.push(fn)},flush:function(){for(var fn;fn=FB_WP.queue._methods.shift();){fn()}FB_WP.queue.flushed=true}};window.fbAsyncInit=function(){FB.init({"channelUrl":"http:\/\/fazendoasmalas.com\/channel.php","xfbml":true,"appId":"585138868164342"});if(FB_WP && FB_WP.queue && FB_WP.queue.flush){FB_WP.queue.flush()}}
		/* ]]> */
		</script>
		<div id="fb-root"></div><script type="text/javascript">(function(d){var id="facebook-jssdk";if(!d.getElementById(id)){var js=d.createElement("script"),ref=d.getElementsByTagName("script")[0];js.id=id,js.async=true,js.src="http:\/\/connect.facebook.net\/pt_BR\/all.js",ref.parentNode.insertBefore(js,ref)}})(document)</script>	
	<!--- facebook sdk --->	
	<?
	}

function wph_right_now_content_table_end() {
	$args = array( 'public' => true , '_builtin' => false );
	$output = 'object';
	$operator = 'and';
	$post_types = get_post_types( $args , $output , $operator );
	foreach( $post_types as $post_type ) {
		if(strtolower($post_type->name) != "viagem")
		{
			$num_posts = wp_count_posts( $post_type->name );
			$num = number_format_i18n( $num_posts->publish );
			$text = _n( $post_type->labels->singular_name, $post_type->labels->name , intval( $num_posts->publish ) );
			if ( current_user_can( 'edit_posts' ) ) {
				$num = "<a href='edit.php?post_type=$post_type->name'>$num</a>";
				$text = "<a href='edit.php?post_type=$post_type->name'>$text</a>";
			}
			echo '<tr><td class="first b b-' . $post_type->name . '">' . $num . '</td>';
			echo '<td class="t ' . $post_type->name . '">' . $text . '</td></tr>';
		}
	} 
}
add_action( 'right_now_content_table_end' , 'wph_right_now_content_table_end' );

add_action( 'admin_footer-index.php', 'fam_admin_footer' );

function fam_admin_footer()
{   
?><script>
	jQuery( function( $ ) {
		var itens = $("#dashboard_right_now a:hidden");
		$(itens).each(function(){
			$(this).parents('tr').first().remove();
		});
		var itens = $("#dashboard_right_now td:hidden");
		$(itens).each(function(){
			$(this).parents('tr').first().remove();
		});
	});
	</script>		
<?
}

add_action( 'admin_head', 'fam_admin_bar_icon' );
function fam_admin_bar_icon()
{    
    ?><style>#wpadminbar .quicklinks li div.blavatar{background:url(/wp-content/themes/images/icons/16px/icon-viagem.png) no-repeat !important;}</style><?
}

function CheckUploadedFileExists($fileHttpLocation)
{	
	//var_dump($fileHttpLocation);
	if(strpos($fileHttpLocation,"/uploads/") > -1)
	{
		$file_location_parts = explode("/uploads/",$fileHttpLocation);		
	}
	else
	{
		$file_location_parts = explode("/files/",$fileHttpLocation);	
	}			
    $cleanUrl = str_replace("http://", "", $fileHttpLocation);				
    $srcParts = explode("/",$cleanUrl);		
    if($srcParts[6] == "cropped_destaque")
    {
    	$blog_id = $srcParts[4];
    }	
    else
    {	
    	$blog_id = get_blog_id_from_url( $srcParts[0], "/".$srcParts[1]."/" );
    }
	
	if($blog_id == 0)
	{
		$file_location =  $_SERVER["DOCUMENT_ROOT"]."/wp-content/uploads/".$file_location_parts[1];	
	}
	else
	{
		$file_location =  $_SERVER["DOCUMENT_ROOT"]."/wp-content/blogs.dir/".$blog_id."/files/".$file_location_parts[1];	
	}
	if(strpos($file_location,"?randon=")> -1)
	{
		$file_parts = explode("?randon=",$file_location);
		$file_location = $file_parts[0];
	}
	//var_dump($file_location);
	
    return file_exists($file_location);
}

remove_filter('the_content','wpautop');

function fam_theme_add_editor_styles() {
    add_editor_style( 'style.css' );
}
add_action( 'init', 'fam_theme_add_editor_styles' );

add_filter( 'plupload_init', 'fam_plupload_init', 0, 1 ); 
function fam_plupload_init( $plupload_init ) { 
    $plupload_init['resize'] = array('width' =>  1600, 'height' =>  1024, 'quality'=> 75); 
    //max_file_size: 10485760
    //multi_selection: true
    return $plupload_init; 
} 

function fam_feed_request($qv) {	
    	
    if (isset($qv['feed']))
    {		
    	$qv['post_type'] = get_post_types();					
    	unset($qv['post_type']['post']);
    	unset($qv['post_type']['destaque']);
    	unset($qv['post_type']['page']);
    	unset($qv['post_type']['attachment']);
    	unset($qv['post_type']['revision']);
    	unset($qv['post_type']['nav_menu_item']);
    		
    }
    return $qv;
}
add_filter('request', 'fam_feed_request');

//add custom feed content
function add_feed_content($content) {
    	
    if(is_feed()) {
    	global $post;
    	switch($post->post_type)
    	{
    		case "albuns":			
    			$content .= substr(get_post_meta($post->ID, "descricao_album", true), 0, 300); ; 
    			break;
    		case "atualizacao":			
    			$content .= substr(get_post_meta($post->ID, "conteudo", true),0,300); 
    			break;	
    		case "forum":			
    			$content .= substr($post->post_excerpt,0,300); 
    			break;				
    	}
    }
    return $content;
}
add_filter('the_excerpt_rss', 'add_feed_content');
add_filter('the_content', 'add_feed_content');

//add custom feed content
function fam_set_feed_title($title) {
    	
    if(is_feed()) {
    	global $post;
    	switch($post->post_type)
    	{
    		case "albuns":			
    			$title .= " - Album de viagem"; 
    			break;
    		case "atualizacao":			
    			$title .= " - Status"; 
    			break;	
    		case "relatos":			
    			$title .= " - Relato de viagem";; 
    			break;
    		case "viagem":			
    			$title .= " - Viagem"; 
    			break;
    		case "blog_post":			
    			$title .= " - Blog"; 
    			break;
    		case "forum":			
    			$title .= " - Fórum"; 
    			break;		
    	}
    }
    return $title;
}
add_filter('the_title_rss', 'fam_set_feed_title');

add_action( 'post_submitbox_misc_actions', 'fam_social_media_publish' );
function fam_social_media_publish()
{   
    $post_status = get_post_status();
    echo GetSocialPublishHtml($post_status,get_current_post_type());
}

function GetSocialPublishHtml($post_status, $post_type)
{	
	$wall_checked =  ($post_status != "publish" && !Is_test_enviroment())? 'checked="checked"':"";
    $html = "";
	
	if($post_type != "informativo" && $post_type != "destaque")
	{
		$html .=  
			'<div class="misc-pub-section misc-pub-section-last">							
			<input id="facebook_wall" type="checkbox" '.$wall_checked.' />
			<label for="facebook_wall">Publicar no meu mural do Facebook</label>						
		</div>';
		if(get_user_role() == "administrator")
		{
			$html .=
				'<div class="misc-pub-section misc-pub-section-last">
					<input id="fam_page_status" type="checkbox" '.$wall_checked.' />
					<label for="fam_page_status">Publicar no facebook FazendoAsMalas</label>
				</div>';
		}
	}
	
	if($post_type != "destaque" && ( ($post_type != "atualizacao" && $post_type != "forum") || get_user_role() == "administrator" ))
	{	
		$news_checked =  ($post_status != "publish")? 'checked="checked"':"";
		$html .= 
				'<div class="misc-pub-section misc-pub-section-last">							
				<input id="news_send" name="news_send" value="yes" type="checkbox" '.$news_checked.' />
				<label for="news_send">Notificar assinantes</label>						
				</div>';
	}    
    	
	if($post_type == "atualizacao"  && !Is_test_enviroment())
    {
		if($post_status != "publish")
		{
			$checked = ' checked="checked"';
		}
		$html .=
			'<div class="misc-pub-section misc-pub-section-last">														
			<input id="facebook_checkin" type="checkbox" '.$checked.' />
			<label for="facebook_checkin">Fazer checkin no Facebook</label>						
			<span id ="place_id" style="display:none;"></span>				
			<input type="text" style="width:100%;" autocomplete="off"  name="place_name" id="place_name" placeholder="Digite para buscar o local do checkin"  value="" />							
			</div>';
    }    
	
	if($post_type == "informativo")
	{
		$html .=  
			'<div class="misc-pub-section misc-pub-section-last">							
			<a class="teste_send_informativo button" href="javascript:void(0);"  id="teste_send_informativo">Testar envio</a>
			</div>
			';
	}
	
    return $html;
}

function save_post($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}	
	update_post_meta($post_ID, 'news_send', $_POST['news_send']);	
}


add_action( 'save_post', 'save_post' );

function CheckCanPreview($post)
{
    $can_preview = ($post->post_status == "publish" || get_current_user_id() == $post->post_author || is_super_admin() || get_user_role() == "administrator" || get_user_role() == "adm_fam_root")? true: false;	
    return $can_preview;	
}

function Is_test_enviroment()
{
	if(strpos($_SERVER["SERVER_NAME"],"teste.") === 0 || strpos($_SERVER["SERVER_NAME"],"dev.") === 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function fam_update_slug( $data, $postarr ) {
    if ( !in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) ) {
    	$title = sanitize_title( $data['post_title']);
    	if($title != "temp")
    	{			
    		$data['post_name'] = $title;
    	}
    }
    return $data;
}
add_filter( 'wp_insert_post_data', 'fam_update_slug', 99, 2 );


add_filter('post_type_link', 'fam_post_permalink', 1, 3);
function fam_post_permalink($post_link, $post = 0, $leavename) {	
    if(is_numeric($post))
    {
    	$post = &get_post($post);
    }
    	
    if (is_wp_error($post))
    {
    	return $post;
    }
    if(in_array($post->post_type, array("albuns","relatos","atualizacao",'forum', 'blog_post')))
    {
    	global $wp_rewrite;				
    	$post_type_obj = get_post_type_object($post->post_type);		
    	$newlink = $wp_rewrite->get_extra_permastruct($post->post_type);
    	$cpt_slug = $post_type_obj->rewrite["slug"];		
    	$newlink = str_replace("%cpt_".$cpt_slug."_id%", $post->ID, $newlink);		
    	$postName = $post->post_name;
    	if($postName == null || $postName == "")
    	{
    		$postName = sanitize_title($post->post_title);
    	}
    	if($postName == null || $postName == "")
    	{
    		$postName = sanitize_title($_POST['new_title']);
    	}
    	$newlink = str_replace("%postname%", $postName, $newlink);
    	$newlink = str_replace("//","/",$newlink);
    		
    	$newlink = home_url(user_trailingslashit($newlink));
    	return $newlink;
    		
    }
    else
    {
    	return $post_link;
    }		
}

function fam_flush_rewrite_rules()
{
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
//add_action( 'init', 'fam_flush_rewrite_rules');

function fam_change_mime_icon($icon, $mime = null, $post_id = null){
    //var_dump($icon);	
    //return $icon;
    //return "http://img.youtube.com/vi/BVhLc-wP_64/1.jpg";
    //return "http://teste.fazendoasmalas.com/passeiopelooriente/files/2013/09/DSC01674-120x70.jpg";
    //return "http://teste.fazendoasmalas.com/passeiopelooriente/wp-includes/images/crystal/video2.png";
    if($post_id != null)
    {
    	$post = get_post($post_id);
    		
    	if(strpos($post->post_mime_type,"video") === 0)//if is video
    	{		
    			
    		$videoUrlParts = explode("embed", $post->guid);				
    		$icon = "http://img.youtube.com/vi/".trim($videoUrlParts[1],"/")."/1.jpg";						
    	}
    		
    }
    echo "<img src='".$icon."' />";	
    return $icon;
}
//add_filter('wp_mime_type_icon', 'fam_change_mime_icon',10,3);

function fam_enable_more_buttons($buttons) {
    $buttons[] = 'hr';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    /*$buttons[] = 'styleselect';*/
    return $buttons;
}
add_filter("mce_buttons_3", "fam_enable_more_buttons");

function fam_customize_tiny_mce($initArray){
	$initArray['theme_advanced_font_sizes'] = "10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,32px,48px";
	//$initArray['extended_valid_elements'] = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
	$ext = 'pre[id|name|class|style],iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src|noscroll]';
	if ( isset( $initArray['extended_valid_elements'] ) ) {
		$initArray['extended_valid_elements'] .= ',' . $ext;
	} else {
		$initArray['extended_valid_elements'] = $ext;
	}
	$initArray['wordpress_adv_hidden'] = FALSE;
	return $initArray;
}

// Assigns customize_text_sizes() to "tiny_mce_before_init" filter
add_filter('tiny_mce_before_init', 'fam_customize_tiny_mce');

add_filter( 'wp_kses_allowed_html', 'fam_editor_cap_filter',1,1 );

function fam_editor_cap_filter( $allowedposttags ) {
	$allowedposttags['iframe']=array(
		'align' => true,
		'width' => true,
		'height' => true,
		'frameborder' => true,
		'name' => true,
		'src' => true,
		'id' => true,
		'class' => true,
		'style' => true,
		'scrolling' => true,
		'marginwidth' => true,
		'marginheight' => true,
		'noscroll'=> true

		);
	return $allowedposttags;
}





/*mobile*/
if($is_mobile && is_admin() && is_user_logged_in() && (!defined( 'DOING_AJAX' ) || ( defined( 'DOING_AJAX' ) && !DOING_AJAX ) ) && get_current_blog_id() != 1)
{
    wp_redirect( home_url()."/m-admin/" );exit;
}

if(($is_mobile != true || ($is_mobile && get_current_blog_id() == 1)) &&  strpos($_SERVER['REQUEST_URI'],"/m-admin/") > -1)
{
    wp_redirect( home_url()."/wp-admin/" ); exit;;
}

function is_mobile_admin()
{
    global $is_mobile_admin;
    if(strpos($_SERVER['REQUEST_URI'],"/m-admin/") > -1 && get_current_blog_id() > 1)
    {		
    	$is_mobile_admin = true;
    }
    else
    {		
    	$is_mobile_admin = false;
    }
    return $is_mobile_admin;

}

function is_fam_mobile()
{
    global $is_mobile;
	if($is_mobile == null)
	{
		$is_mobile = false;
	}
    return $is_mobile;
}
/*end mobile*/


function email_subscribers($post_ID, $post) 
{
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		return;
	// AJAX? Not used here
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
		return;	
	// Return if it's a post revision
	if ( false !== wp_is_post_revision( $post_id ) )
		return;
		
	try
	{
		$notified = get_post_meta($post->ID, "notified", true);
		$news_send = get_post_meta($post->ID, "news_send", true);		
		$post_type = $post->post_type;		
		
		/*$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>' . "\r\n";			
		$body = "notified:".var_export($notified, true)." - news_send:".var_export($news_send, true). " - posttype:".var_export($post_type, true)." - post status:".var_export($post->post_status, true);
		mail("amoncaldas@yahoo.com.br",'DEBUG fazendoasmalas',$body,$headers);*/
	
		if(in_array($post_type,array('informativo','atualizacao','albuns','relatos','blog_post','forum')) && $post->post_status == "publish" && $notified != "yes" && $news_send == "yes")
		{	
			update_post_meta($post->ID, 'notified', "yes");						
			
			switch($post_type)
			{
				case "atualizacao":
					require_once(ABSPATH. "/FAMCore/VO/AtualizacaoVO.php");
					$AtualizacaoVO = new AtualizacaoVO($post->ID);
					$news_content_type = 'Status de viagem';
					$email_title = $AtualizacaoVO->Titulo;
					$content_title = $post->post_title;
					$post_type = "status";
					$excerpt = GetSubContent($AtualizacaoVO->Conteudo,600);				
					$content_image = $AtualizacaoVO->MidiaPrincipal->ImageLargeSrc;
					$url = $AtualizacaoVO->AtualizacaoUrl;
					break;
				case "albuns":
					require_once(ABSPATH. "/FAMCore/VO/AlbumVO.php");
					$albumVO = new AlbumVO($post->ID, true);
					$email_title = $albumVO->Titulo;
					$news_content_type = 'Álbum de viagem';
					$content_title = $post->post_title;
					$excerpt = $albumVO->Resumo;
					$content_image = $albumVO->MidiaPrincipal->ImageLargeSrc;
					$url = $albumVO->AlbumUrl;
					break;
				case "relatos":
					require_once(ABSPATH. "/FAMCore/VO/RelatoVO.php");
					$relatoVO = new RelatoVO($post->ID);
					$email_title = $relatoVO->Titulo;
					$news_content_type = 'Relato de viagem';
					$content_title = $post->post_title;
					$excerpt = $relatoVO->SEODesc;
					$content_image = $relatoVO->MidiaPrincipal->ImageLargeSrc;	
					$url = $relatoVO->RelatoUrl;
					break;
				case "blog_post":
					require_once(ABSPATH. "/FAMCore/VO/PostVO.php");
					$blogPostVO = new PostVO("blog_post",$post->ID);
					$email_title = $blogPostVO->Titulo;	
					$news_content_type = 'Post no blog';			
					$content_title = $post->post_title;
					$post_type = "blog";
					$excerpt = $blogPostVO->SEODesc;	
					$content_image = $blogPostVO->MidiaPrincipal->ImageLargeSrc;
					$url = $blogPostVO->PostUrl;				
					break;
				case "forum":
					require_once(ABSPATH. "/FAMCore/VO/PostVO.php");
					$forumVO = new PostVO("forum",$post->ID);
					$email_title = $forumVO->Titulo;
					$news_content_type = 'Tópico de fórum';	
					$content_title = $post->post_title;
					$excerpt = GetSubContent($forumVO->Conteudo,600);
					$content_image = $forumVO->MidiaPrincipal->ImageLargeSrc;
					$url = $forumVO->PostUrl;					
					break;
				case "informativo":				
					$email_title = $post->post_title;			
					$content_title = $post->post_title;
					$excerpt = $post->post_content;
					$news_content_type = 'Informativo';
					$content_image = "";	
					$url = get_post_meta($post->ID, "url_informativo", true);				
					break;
			}
		
			
			$template = file_get_contents(ABSPATH.'/Templates/Mail/news.html');
			
			$template = str_replace("{site-url}", network_home_url(), $template);
			$template = str_replace("{content-url}", $url, $template);
			if($post_type == "informativo")
			{
				$template = str_replace("{content-image-src}", "", $template);
				$template = str_replace("{main-img-height}", "0", $template);
			}
			else
			{
				$template = str_replace("{content-image-src}", $content_image, $template);
			}
			
			if($content_image = ""){ $template = str_replace("{main-img-height}", "0", $template); } else {$template = str_replace("{main-img-height}", "290", $template);}
			$template = str_replace("{content-excerpt}", $excerpt, $template);
			$template = str_replace("{content-title}", $content_title, $template);
			$template = str_replace("{current_year}", date('Y'), $template);	
			$template = str_replace("teste.faz", "faz", $template);
            $template = str_replace("dev.faz", "faz", $template);
			$template = str_replace("{news-type}", $news_content_type, $template);			
			
		
			require_once(ABSPATH. "/FAMCore/BO/Conteudo.php");
			$relacionados = Conteudo::GetPostsRelacionados(array('post_id'=>$post->ID));			
			
			if(is_array($relacionados) && count($relacionados) > 2)
			{
				$template_other_itens = file_get_contents(ABSPATH.'/Templates/Mail/news_other_itens.html');					
				$counter = 1;
				foreach($relacionados as $relacionado)
				{
					$template_other_itens = str_replace("{related-content-url-".$counter."}", $relacionado->PostUrl, $template_other_itens);
					$template_other_itens = str_replace("{related-content-image-src-".$counter."}", $relacionado->MidiaPrincipal->ImageGaleryThumbSrc, $template_other_itens);
					$template_other_itens = str_replace("{related-content-title-".$counter."}", $relacionado->Titulo, $template_other_itens);
					
					if($counter == 3)
					{
						break;
					}
					$counter++;
				}				
				
				$template = str_replace("{other-itens}", $template_other_itens, $template);
			}	
			$template = str_replace("{other-itens}", "", $template);	
		
			$message = str_replace("'","''", $template);
		
			global $wpdb;		
			$sql_news_pending	= "INSERT INTO wp_fam_pending_mail( subject, content, content_type, mail_list_type, site_id ) VALUES ('".$email_title."','".$message."','html','news', 1)";						
			$wpdb->query($sql_news_pending);		
				
			update_post_meta($post_ID, 'notified','no');		
		}
	}
	catch(Exception $e)
	{
		$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>' . "\r\n";	
		$body .= "dump error on news send:". var_export($e,true)."\r\n";		
		mail("amoncaldas@yahoo.com.br",'News error on fazendoasmalas',$body,$headers);
		update_post_meta($post_ID, 'notified','no');		
	}
	
    return $post_ID;
}

add_action( 'save_post', 'email_subscribers' ,100,2);

function set_html_content_type() {
	return 'text/html';
}

//add_action( 'publish_albuns', 'email_subscribers' ,1,2);
//add_action( 'publish_relatos', 'email_subscribers' ,1,2);

//add_action('shutdown', 'sql_logger');
function sql_logger() {
    global $wpdb;
    $log_file = fopen(ABSPATH.'/sql_log.txt', 'a');
    fwrite($log_file, "//////////////////////////////////////////\n\n" . date("F j, Y, g:i:s a")."\n");
    foreach($wpdb->queries as $q) {
        echo $q[0];
		fwrite($log_file, $q[0] . " - ($q[1] s)" . "\n\n");
    }
    fclose($log_file);
}


/* apply this filter only on relevant to you pages */
function fam_disable_main_wp_query( $sql, WP_Query &$wpQuery ) {	
	if ( $wpQuery->is_main_query() ) {
		/* prevent SELECT FOUND_ROWS() query*/
		$wpQuery->query_vars['no_found_rows'] = true;

		/* prevent post term and meta cache update queries */
		$wpQuery->query_vars['cache_results'] = false;		
		
		return false;
	}
	return $sql;
}
remove_action('wp_head', 'feed_links_extra', 3 );
remove_action('wp_head', 'feed_links', 2 );

function CheckValidBrowser()
{
	preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
	
	//$body = var_export( $_SERVER['HTTP_USER_AGENT'], true). "\r\n";						
	//mail("amoncaldas@yahoo.com.br",'debug db fazendoasmalas',$body,$headers);

	if (count($matches)>1 && !is_fam_mobile()){
		
		//Then we're using IE			
		
		if($matches[1]<=8)
		{		
			define( 'DONOTCACHEPAGE', 1 );
			if(!isset($_GET["t"]) && $_SESSION["request_ip_notified"] == null || $_SESSION["request_ip_notified"] != $_SERVER["REMOTE_ADDR"])
			{
				$_SESSION["request_ip_notified"] = $_SERVER["REMOTE_ADDR"];
				$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>' . "\r\n";	
				
				/*$body = "Tentativa de acesso com navegador antigo". "\r\n";
				$body .= "Ip de requisição: http://www.ip2location.com/".$_SERVER["REMOTE_ADDR"]."\r\n";	
				$body .= "User Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n";	
				$body .= "Requested with:" . $_SERVER['HTTP_X_REQUESTED_WITH']."\r\n";
				$body .= "Url:". "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\r\n";
				$body .= "Url anterior:". "http://".$_POST["referer_url"]."\r\n";				
				mail("amoncaldas@yahoo.com.br",'Old browser on fazendoasmalas',$body,$headers);*/
			}
			
			$analytics = "/*  analytics */			
			(function (i, s, o, g, r, a, m) {
				i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
					(i[r].q = i[r].q || []).push(arguments)
				}, i[r].l = 1 * new Date(); a = s.createElement(o),
				m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
			})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');


			if (location.host == 'fazendoasmalas.com') {
				ga('create', 'UA-20810650-5', 'auto');
				ga('send', 'pageview');
			}
			/*  fim analytics */";
			
				
			$die_html =  '
		<html lang="pt-BR">	
				<head>
					<title>Oops!</title>		
					<meta charset="UTF-8" />
				</head>
				<script type="text/javascript">
				'.$analytics.'
				</script>
				<style>                 
					.error_content{                     
						background: ghostwhite;
						vertical-align: middle;
						margin:0 auto;
						padding:10px;
						width:80%;    
						border:1px solid #ccc; 
						margin-top:40px;  
						border-radius:10px;                       
					} 
					.error_content label{color: red;font-family: Georgia;font-size: 16pt;font-style: italic;display:block;}
					.error_content ul li{ 
						background: none repeat scroll 0 0 FloralWhite;                   
						background: none repeat scroll 0 0 #fff;
						border: 1px solid #ccc;
						display: block;
						font-family: monospace;
						padding: 2%;
						text-align: left;
						line-height: 30px;
						margin-bottom:10px;
						word-wrap:break-word;
						list-style-type:none;
					}
					.error_content ul
					{
						padding-left: 0px;
						padding: 20px;
					}
					.error_content a
					{
						color:#276BB1;
					}
					.error_content img
					{
						width:130px;
					} 
				
				</style>
				<body> 
					<div  style="text-align: center;width:100%"> 
						<div class="error_content">
							<img src="/fazendo_as_malas.png" />
							<label>Navegador não suportado | Browser not supported</label>				
							<ul>
								<li>O navegador Internet Explorer '.$matches[1].' que você está utilizando está desatualizado e não é suportado pois não seria possível exibir os recursos de nosso site.</li>
								<li>Você deve <a target="_blank" href="http://windows.microsoft.com/pt-pt/internet-explorer/download-ie">atualizar</a> para a versão 9.0 ou superior ou baixar outro navegador como  <a target="_blank" href="http://google.com/chrome/">Google Chrome</a> ou <a target="_blank" href="http://mozilla.org/pt-BR/firefox/">Firefox</a>.</li>									                           
								<li style="display:none">'.$_SERVER['HTTP_USER_AGENT'].'</li>
							</ul>	
						                        
						</div>
					</div>
				</body>
			</html>';
			die($die_html);	
		}
	}
}

CheckValidBrowser();	
require_once( ABSPATH . '/FAMCore/boot.php' );
?>