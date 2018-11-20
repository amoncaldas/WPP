<?php

add_image_size('galerythumb', 190, 140, true);
add_image_size('tinythumb', 120, 70, true);

add_filter('upload_mimes','restrict_mime');
function restrict_mime($mimes) {
	$mimes = array(
	'jpg|jpeg|jpe' => 'image/jpeg',
	'jpg|pjpeg|jpe' => 'image/pjpeg',
	'gif' => 'image/gif',
	'png' => 'image/png',	
	);
	return $mimes;
}

function remove_media_library_tab($tabs) {    
	//unset($tabs['library']);
	unset($tabs['type_url']);	  
	return $tabs;
}

add_filter('plupload_default_settings', 'plupload_fam_settings', 20);
function plupload_fam_settings($defaults) {
	//throw new Exception(var_export($defaults));
	$defaults['max_file_size'] = "200097152b";	
	$defaults['resize'] = array("width" => 1024, "height" => 1024, "quality" => 75);	
	return $defaults;    
}


/* Add custom field to attachment */
function fam_image_attachment_add_custom_fields($form_fields, $post) {	
    $form_fields["attachment_location"] = array(
        "label" => __("Localização"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "attachment_location", true),
        "helps" => __("Informe a localização geográfica da mídia."),
    );
	$form_fields["attachment_latitude"] = array(
        "label" => __("Latitude"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "attachment_latitude", true),
        "helps" => __("Latitude (digite o local para preencher automaticamente)"),
    );
	$form_fields["attachment_longitude"] = array(
        "label" => __("Longitude"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "attachment_longitude", true),
        "helps" => __("Longitude (digite o local para preencher automaticamente)"),
    );
	$form_fields["attachment_script"] = array(
			"label" => __(" "),
        "input" => "html",
			'html'      => "<div id='media_location_script_container'><script type='text/javascript'>jQuery(document).ready(function () { CheckMediaLoaded();});</script></div>",
    );
	if('video' == substr( $post->post_mime_type,0,5))
	{
		$form_fields["video_iframe"] = array(
			"label" => __("Vídeo"),
			"input" => "html",
			'html'      => '<iframe  src="'.$post->guid.'?modestbranding=1&rel=0" class="fancybox" type="text/html" width="190" height="140" frameborder="0"></iframe>',
			);
	}
    return $form_fields;
}
add_filter("attachment_fields_to_edit", "fam_image_attachment_add_custom_fields", null, 2);


function fam_media_column( $cols ) {	
	unset($cols['parent']); 
	unset($cols['comments']); 	      
    return $cols;
}

add_filter( 'manage_media_columns', 'fam_media_column' );

function remove_media_link( $form_fields, $post ) {
    unset( $form_fields['url'] );
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'remove_media_link', 10, 2 );

add_filter( 'media_row_actions', 'remove_atachment_row_actions', 10, 3 );

function remove_atachment_row_actions($actions, $post, $detached)
{   
	if($post->post_mime_type == 'video/x-flv')
	{
		$actions['view'] = '<a class="fancybox fancybox.iframe" href="'. $post->guid.'" rel="permalink" title="'.$post->post_title.'" >Ver</a>';
		unset( $actions['edit']);		
	}	
	else
	{
		$actions['view'] = '<a class="fancybox" href="'. $post->guid.'" rel="permalink" title="'.$post->post_title.'" >Ver</a>';
	}
    return $actions;
}

add_filter('attachment_link', 'fam_attachment_permalink', 1, 2);
function fam_attachment_permalink($post_link, $id = 0) {	
	$post = &get_post($id);	
	if (is_wp_error($post))
	{
		return $post;
	}	
	//example: http://fazendoasmalas.com/?media=chapadadosveadeiros__files__2013__06__ASTK0001-2-3.jpg
	
	$media_url_parts = explode(get_site_url(1), $post->guid);
    $media_url = get_bloginfo('url')."?media=". str_replace("/","__",trim($media_url_parts[1],'/'));    
	return $media_url;		
}

add_filter('get_sample_permalink_html', 'attachment_permalink_btn', '', 4);
function attachment_permalink_btn($return, $id, $new_title, $new_slug){
    global $post;
    if($post->post_type == 'attachment')
    {
        $return = str_replace("Ver página de Anexos","Ver media no site",$return);
			$return = str_replace("<a","<a target='_blank'",$return);
    }
    return $return;
}


function fam_send_to_editor( $html, $id, $attachment ) {
	$post = get_post($id);
	
	if ('video' == substr( $post->post_mime_type,0,5)) { 
		
		$urlParts = explode("/embed/",$post->guid);
		$video_id = $urlParts[1];		
        $title = $post->post_title;
        $link = $post->guid;
		$src = 'http://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
		
		$newhtml = '[caption id="attachment_'.$post->ID.'" align="alignnone" width="300"]"						
					<a href="'.$link.'">
						<img  alt="'.$title.'" src="'.$src.'" width="300" height="168" class="size-medium wp-image-'.$post->ID.'" >
					</a>'.$title.'(vídeo)					
				 [/caption]					
				';	
		$html = $newhtml;		
	}
 
	return $html;
}
add_filter( 'media_send_to_editor', 'fam_send_to_editor', 10, 3 );

/** set default settings of attachment media box */
function attachment_default_settings() {
	update_option('image_default_align', 'none' );
	update_option('image_default_link_type', 'file' );
	update_option('image_default_size', 'medium' );
}
add_action('after_setup_theme', 'attachment_default_settings');

function GetFamUploaderHtml($multiple = true, $parentId, $user_image = false)
{	
	global $post;
	// adjust values here
	$id = "img1"; // this will be the name of form field. Image url(s) will be submitted in $_POST using this key. So if $id == “img1” then $_POST[“img1”] will have all the image urls
 
	$svalue = ""; // this will be initial value of the above form field. Image urls.
 
	$width = null; // If you want to automatically resize all uploaded images then provide width here (in pixels)
 
	$height = null; // If you want to automatically resize all uploaded images then provide height here (in pixels)
	
	if($multiple === true)
	{
		$buttom_select_label = "Selecionar imagens";
	}
	else
	{
		$buttom_select_label = "Selecionar imagem";
	}
	
	if($user_image)
	{
		echo "<label>Foto de viajante</label>";
	}
	else
	{		
		echo "<label>".$buttom_select_label."</label>";		
	}
	
	?>	
	<input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" />
	<div class="plupload-upload-uic hide-if-no-js <?php if ($multiple): ?>plupload-upload-uic-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-upload-ui">
		<input id="<?php echo $id; ?>plupload-browse-button" type="button" value="<? echo $buttom_select_label;?>" class="button" />
		<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
		
		<?php if ($width && $height): ?>
				<span class="plupload-resize"></span><span class="plupload-width" id="plupload-width<?php echo $width; ?>"></span>
				<span class="plupload-height" id="plupload-height<?php echo $height; ?>"></span>
		<?php endif; ?>
		<div class="filelist"></div>
		<div class="fam_upload">
			<input type="hidden" class="upload_single" value="<? echo ($multiple)? "false":"true"; ?>"   />
			<input type="hidden" class="upload_mandatory" value="false"   />										
			<input type="hidden"  class="postId" value="<?if ($post->ID != null){echo $post->ID;}?>" />	
			<? 
				if($user_image)
				{
					PrintUserImage($parentId);
				}
				else
				{
					PrintImages($parentId); 
				}
			?>			
		</div>
		<? if(is_mobile_admin()){ ?>
		<div class="use_existing_media">
			<a id="btn_use_existing_img"  class="button" > Usar media existente </a>
			<ul class="galeriafoto medias-album">
				<?php widget::Get("fotos", array('foto_size' => "gallery", 'show_more' => 'yes','itens'=>10, 'return' => 'onlyitens','media_mime_type'=>'image/jpeg')) ?>
			</ul>
		</div> 
		<?}?>
	</div>
	<div class="plupload-thumbs <?php if ($multiple): ?>plupload-thumbs-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-thumbs">		
	</div>	
	<div class="clear"></div>
	<?
}

function fam_plupload_action() {
 
    // check ajax noonce
	$output = array();
    $imgid = $_POST["imgid"];
    check_ajax_referer($imgid . 'pluploadan');
 
    // handle file upload
    $file = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'plupload_action'));
	
	if ( isset( $file['error'] ) )
		return $file;

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$filename = basename( $file );
	$name = $filename;
	$name_parts = pathinfo($name);
	$name = trim( substr( $name, 0, -(1 + strlen($name_parts['extension'])) ) );
	
	// use image exif/iptc data for title and caption defaults if possible
	if ( $image_meta = @wp_read_image_metadata($file) ) {
		if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) )
			$title = $image_meta['title'];
		if ( trim( $image_meta['caption'] ) )
			$content = $image_meta['caption'];
	}

	// Construct the object array
	$object = array( 'post_title' => $name,
		'post_content' => $url,
		'post_mime_type' => $type,
		'guid' => $url,
		'post_status' => 'inherit',
		'post_content' => $content,
	);

	// Save the data
	$id = wp_insert_attachment( $object, $file );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file));
	
	$imageThumb = wp_get_attachment_image_src($id, "tinythumb", false);
	$src = $imageThumb[0];
	
	$output["id"] = $id;
	$output["url"] = $src;
	$output["name"] = $name;
    // send the uploaded file url in response
	//var_dump($status);
    //echo $status['url'];
	
	echo json_encode($output);
    exit;
}

add_action('wp_ajax_plupload_action', "fam_plupload_action");

function fam_plupload_loader($multiselection = false) {
		
		$scripts_upload = array(
		'/wp-includes/js/plupload/plupload.js',
		'/wp-includes/js/plupload/plupload.html5.js',
		'/wp-includes/js/plupload/plupload.flash.js',
		'/wp-includes/js/plupload/plupload.silverlight.js',
		'/wp-includes/js/plupload/plupload.html4.js',
		'/wp-includes/js/plupload/handlers.js',
		'/wp-includes/js/plupload/wp-plupload.js',
		);
	
		foreach($scripts_upload as $script)
		{
			echo '<script type="text/javascript" src="'.$script.'" ></script>'; 
		}
		
		// place js config array for plupload
		$plupload_init = array(
			'runtimes' => 'html5,silverlight,flash,html4',
			'browse_button' => 'plupload-browse-button', // will be adjusted per uploader
			'container' => 'plupload-upload-ui', // will be adjusted per uploader
			'drop_element' => 'drag-drop-area', // will be adjusted per uploader
			'file_data_name' => 'async-upload', // will be adjusted per uploader
			'multiple_queues' => true,
			'max_file_size' => wp_max_upload_size() . 'b',
			'url' => admin_url('admin-ajax.php'),
			'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
			'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
			'filters' => array(array('title' => __('Allowed Files'), 'extensions' => 'jpg,png')),
			'multipart' => true,
			'urlstream_upload' => true,
			'multi_selection' => $multiselection, // will be added per uploader
			 // additional post data to send to our ajax hook
			'multipart_params' => array(
				'_ajax_nonce' => "", // will be added per uploader
				'action' => 'plupload_action', // the ajax action name
				'imgid' => 0 // will be added per uploader
			),
			'resize' => array('width' =>  1600, 'height' =>  1024, 'quality'=> 75)
		);
		?>
		<script type="text/javascript">
			var base_plupload_config=<?php echo json_encode($plupload_init); ?>;
		</script>
		<?php
		}
