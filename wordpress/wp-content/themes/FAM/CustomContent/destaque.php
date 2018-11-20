<?php
	
define('ABSPATH', $_SERVER["DOCUMENT_ROOT"]."/");
require_once( ABSPATH. '/FAMCore/VO/DestaqueVO.php' );	


function destaques_register() {
 
	$labels = array(
		'name' => _x('Destaque', 'post type general name'),
		'menu_name'=>_x('Destaques', 'post display name'),
		'singular_name' => _x('Destaque', 'post type singular name'),
		'add_new' => _x('Adicionar novo', 'portfolio item'),
		'add_new_item' => __('Adicionar novo'),
		'edit_item' => __('Editar destaque'),
		'new_item' => __('Novo destaque'),
		'view_item' => __('Visualizar destaque'),
		'search_items' => __('Buscar destaque'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado na lixeira'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-destaque.png',
		'rewrite' => true,
		'capability_type' => 'destaque',
		'capabilities' => array(
				'publish_posts' => 'publish_destaque',
				'edit_posts' => 'edit_destaque',
				'edit_others_posts' => 'edit_others_destaque',
				'delete_posts' => 'delete_destaque',
				'delete_others_posts' => 'delete_others_destaque',
				'read_private_posts' => 'read_private_destaque',
				'edit_post' => 'edit_destaque',
				'delete_post' => 'delete_destaque',
				'read_post' => 'read_destaque',
			),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','page-attributes')
		); 
 
	register_post_type( 'Destaque' , $args );
}

function destaque_permalink_btns($return, $id, $new_title, $new_slug){
    global $post;
    if($post->post_type == 'destaque')
    {
        $return = preg_replace('/<span id="edit-slug-buttons">.*<\/span>/i', '', $return);
    }
    return $return;
}

add_filter('post_type_link', 'destaque_permalink', 1, 3);
function destaque_permalink($post_link, $id = 0, $leavename) {
	global $wp_rewrite;
	$post = &get_post($id);
	if (is_wp_error($post))
	{
		return $post;
	}	
	if($post->post_type == "destaque")
	{			
		$post_link = get_bloginfo("url");
	}
	
	return $post_link;		
}

function destaque_autosave_scripts() {
	if ('destaque' == get_current_post_type() )
	{
		wp_dequeue_script( 'autosave' );
	}
}

function add_custom_destaque_form() 
{
	add_meta_box('custom-metabox', __('Imagem destaque'), 'destaque_form', 'destaque', 'normal', 'high');	
}

function save_destaque($post_ID)
{
	if(get_current_post_type() == 'destaque')
	{		
		update_post_meta($post_ID, 'cropped_img_destaque', $_POST['upload_image_src']);
		update_post_meta($post_ID, 'originalSrc', $_POST['originalSrc']);		
	}
}

function delete_destaque($post_id) {
	if(get_current_post_type() == 'destaque')
	{
		$imgCroppedSrc = get_post_meta($post->ID, "cropped_img_destaque", true);	
		if($imgCroppedSrc != null)
		{
			$rootfolder = '/wp-content/blogs.dir/'.get_current_blog_id().'/files/';
			$destaqueAbsoluteFolder = $rootfolder."cropped_destaque/";
			$imgFile = $_SERVER["DOCUMENT_ROOT"].$destaqueAbsoluteFolder.basename($imgCroppedSrc);					
			if (file_exists($imgFile)) { unlink ($imgFile); }			
			delete_post_meta($post_id, 'cropped_img_destaque');
			delete_post_meta($post_id, 'originalSrc');			
		}
	}    
}

function destaque_form()
{
	global $post;
	$imgCroppedSrc = get_post_meta($post->ID, "cropped_img_destaque", true);
	$originalSrc = get_post_meta($post->ID, "originalSrc", true);			
	?>		
	<div id="imageToCrop">
		<style type="text/css">
			#custom-metabox{min-width:828px;}
			#submitdiv{min-width:200px;margin-left:33px;}
		</style>
		<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?if ($post->ID != null){echo $post->ID;}?>" />
		<input type="hidden" name="upload_image_src" id="upload_image_src" value="<?if ($imgCroppedSrc != null){echo "files/cropped_destaque/".$imgCroppedSrc."?randon=".rand(10,999999);}?>" />
		<input type="hidden" name="blogId" id="blogId" value="<? echo get_current_blog_id(); ?>" />
		<input type="hidden" name="originalSrc" id="originalSrc" value="<?if ($originalSrc != null){echo $originalSrc;}?>" />
		<script type="text/javascript"> var baseUrl = '<?echo get_home_url();?>';</script>
		<p>
			<a href="#" class="thickbox"  id="set-image">Escolher imagem</a>
			<a href="#" id="remove-image" style="display:none;">Remover imagem</a>
		</p>				
		<input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />
		<a href="#" id="sendCrop" style="display:none">Recortar</a>			 
		<div style="display:none;" id="dimensionsToCrop" >A área selecionada deve ser maior ou igual a:</div>
		<div id="handw" class="toggle" >Dimensões selecionadas <span id="picw"></span> x <span id="pich"></span> (arraste o cursor para selecionar a área que deseja)</div>	
		<script type="text/javascript">	
			var postId = <?php echo $post->ID;?>
		</script>		
	</div>		
	<?php
}

function post_limit_destaque()
{
	global $pagenow;
	$limit = 5;
	if($pagenow =='post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'destaque') //substr($_SERVER['PHP_SELF'],-12) !== 'post-new.php'
	{	
		$posts = get_posts(array('numberposts' => $limit, 'post_status' => 'publish','post_type'=>'destaque'));
		$nrPosts = count($posts);

		if(($nrPosts>= $limit))
		{
			 wp_die( __('<div style="color:red;font-size:20px">Aviso de limite de Destaques</div>
				<div style="font-weight:bold">Só é permitido o cadastro de '.$limit.' destaque(s) e você já atigiu esse limite. Retorne para o 
				<a href="index.php">painel</a></div>') );
		 }		
	}
}


add_filter( 'post_row_actions', 'destaque_remove_row_actions', 10, 2 );
function destaque_remove_row_actions( $actions, $post )
{
	global $current_screen;
	if( $current_screen->post_type == 'destaque' ) 
	{
		//unset( $actions['edit'] );
		unset( $actions['view'] );
		unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );		
	}

	return $actions;
}


function fam_destaque_remove_meta_boxes() { 
	remove_meta_box('pageparentdiv','destaque', 'normal'); 
}
add_action( 'admin_menu', 'fam_destaque_remove_meta_boxes' );


function fam_destaque_media_tab($tab)
{	
	return 'library';	   
}

function remove_medialibrary_tab($tabs){  
    unset($tabs['youtube_video']);
	//unset($tabs['gallery']);
	//unset($tabs['type']);//upload from computer without resize
    return $tabs;  
}


// Add the posts and pages columns filter. They can both use the same function.
add_filter('manage_destaque_posts_columns', 'destaque_add_post_thumbnail_column', 5);

// Add the column thumb image
function destaque_add_post_thumbnail_column($columns){
	$columns['destaque_post_thumb'] = __('Imagem');
	return $columns;
}


add_filter('manage_destaque_posts_custom_column', 'destaque_display_post_thumbnail_column', 5, 2);

// Grab featured-thumbnail size post thumbnail and display it.
function destaque_display_post_thumbnail_column($column, $post_id ){
	switch( $column ) {		
		case 'destaque_post_thumb' :	
					
			$destaqueVO = new DestaqueVO($post_id);		
			//var_dump($destaqueVO);	
			if ( $destaqueVO != null && CheckUploadedFileExists($destaqueVO->ImageCroppedSrc) )
			{
				echo '
				<a class="fancybox" href="'. $destaqueVO->ImageCroppedSrc.'" rel="permalink" title="'.$destaqueVO->Descricao.'" >
					<img style="width:120px;" src="'.$destaqueVO->OriginalImageVO->ImageThumbSrc.'" />
				</a>';
			}
			else
			{
				echo "notexists";
			}
			break;
		default :
			break;
	}
}

function CropDestaque($img_src, $img_id, $blog_id, $x, $y, $w, $h)
{
	switch_to_blog($blog_id);
	if (is_user_logged_in() === true && current_user_can('edit_destaque'))
	{	
		require_once($_SERVER["DOCUMENT_ROOT"] . '/wp-load.php' );
		define('DESTAQUE_FOLDER', 'cropped_destaque/');
		define('ROOT_FILES_FOLDER','/wp-content/blogs.dir/'.get_current_blog_id().'/files/');
	
		
		$destaqueAbsoluteFolder = ROOT_FILES_FOLDER.DESTAQUE_FOLDER;	
		require('inc/imagemanipulation.php');
		$blogId = $blog_id;		
				
		$imgFile = basename($img_src);		
		$imgId = $img_id;		
		$blogDetails = get_blog_details($blogId, true);	
		if(strpos($img_src, $blogDetails->siteurl) > -1)
		{
			$src_location = str_replace($blogDetails->siteurl,"", $img_src);		
			$src_location = str_replace('files',ROOT_FILES_FOLDER, $src_location);
		}
		else
		{
			$src = $img_src;
			if(strpos($img_src, DESTAQUE_FOLDER) > -1)
			{			
				$src = str_replace(DESTAQUE_FOLDER,"", $src);
			}
			$src_location = str_replace('files', $destaqueAbsoluteFolder, $src);
		}
	
		if(strpos($imgFile, 'destaque_') > -1)
		{
			$returnImgFileName = $imgFile;
		}
		else
		{
			$returnImgFileName = 'destaque_'.$imgId.'_'.$imgFile;
		}
	
		$sourceNameAndPath = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"].$src_location);
		$dir = str_replace("//", "/",$_SERVER["DOCUMENT_ROOT"].$destaqueAbsoluteFolder);
		$saveNameAndPath =  $dir.$returnImgFileName;
		if (!file_exists($dir)) {
			mkdir($dir,0777);
		}	
	
		$ext = ".".pathinfo($saveNameAndPath, PATHINFO_EXTENSION);
		$fullFileName = str_replace($ext,"_full".$ext,$saveNameAndPath);
	
		if(file_exists($fullFileName))
		{
			$saveNameAndPath = $fullFileName;
		}
	
		$objImage = new ImageManipulation($sourceNameAndPath);
		$objImage->setJpegQuality(100);
		if ( $objImage->imageok ) {
			$objImage->setCrop($x, $y, $w, $h);		
			if (file_exists($saveNameAndPath)) { unlink ($saveNameAndPath); }
			$objImage->save($saveNameAndPath);
			$objImage = new ImageManipulation($saveNameAndPath);
			$objImage->setJpegQuality(100);
			$objImage->resize(985);
			$objImage->save($saveNameAndPath);		
			echo basename($returnImgFileName);
		} else {
			echo 'error';
		}
		if(file_exists($fullFileName))
		{
			unlink($fullFileName);
		}
	}
	else
	{
		echo 'error';
	}
	restore_current_blog();	

}

function DeleteCropDestaque($blog_id, $delete_image_src)
{
	switch_to_blog($blog_id);
	if (is_user_logged_in() === true && current_user_can('delete_destaque'))
	{		
		require_once( $_SERVER["DOCUMENT_ROOT"] . '/wp-load.php' );		
		$blogId = $blog_id;		
			
		$rootfolder = '/wp-content/blogs.dir/'.get_current_blog_id().'/files/';
		$destaqueAbsoluteFolder = $rootfolder."cropped_destaque/";
		$imgFile = $_SERVER["DOCUMENT_ROOT"].$destaqueAbsoluteFolder.basename($delete_image_src);	
		$destaque_home = str_replace("destaque_","_destaque-viagem_destaque_",$imgFile);
		if (file_exists($destaque_home)) { 
			if(unlink($destaque_home))
			{	
				echo "success";	
			}
			else
			{
				echo "error";
			}
		}			
		if (file_exists($imgFile)) { 
			unlink($imgFile);			
		}			
		
	}
	restore_current_blog();	
}

if($_POST["action_crop"] == 'true')
{
	CropDestaque($_POST['upload_image_src'],$_POST['upload_image_id'], $_POST["blogId"],$_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
}


if($_POST["action_delete_crop"] == 'true')
{
	DeleteCropDestaque( $_POST["blogId"],$_POST['delete_image_src']);
}

add_action('init', 'destaques_register');		
add_action( 'admin_enqueue_scripts', 'destaque_autosave_scripts' );	
add_action( 'admin_init', 'add_custom_destaque_form');
add_action( 'save_post', 'save_destaque' );
add_action('delete_post', 'delete_destaque');
add_action('init', 'post_limit_destaque');
	
if (get_current_post_type() == 'destaque')
{    
	add_filter('media_upload_tabs', 'remove_media_library_tab');
	add_filter('media_send_to_editor', 'my_plugin_image_selected', 10, 3);
	add_filter( 'flash_uploader', 'force_html_uploader' );	
	add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
	wp_enqueue_script('media-upload');
	wp_enqueue_style('destaque', get_bloginfo('template_url').'/CustomContent/css/destaque.css');
	wp_enqueue_style('jcrop', get_bloginfo('template_url').'/CustomContent/js/jcrop/jquery.Jcrop.css');
	add_filter('get_sample_permalink_html', 'destaque_permalink_btns', '', 4);	
	wp_enqueue_style('thickbox'); 
	$scriptLocation = get_bloginfo('template_url')."/CustomContent/js/";
	wp_enqueue_script('upload_media_destaque', $scriptLocation.'upload_media_destaque.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '0.0.1', true);
	wp_enqueue_script('upload_media_crop', $scriptLocation.'jcrop/jquery.Jcrop.min.js', array('jquery', 'jquery-ui-core', 'upload_media_destaque'), '0.0.1', true);			
	add_filter('media_upload_default_tab', 'fam_destaque_media_tab');
	add_filter('media_upload_tabs','remove_medialibrary_tab');
}	

	




