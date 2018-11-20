<?php

require_once( FAM_PLUGIN_PATH . '/includes/FAMCore/VO/AlbumVO.php' );

function albuns_register() {
 
	$labels = array(
		'name' => _x('Albuns', 'post type general name'),
		'menu_name'=>_x('Albuns', 'post display name'),
		'singular_name' => _x('Album', 'post type singular name'),
		'add_new' => _x('Adicionar novo', 'portfolio item'),
		'add_new_item' => __('Adicionar novo'),
		'edit_item' => __('Editar album'),
		'new_item' => __('Novo album'),
		'view_item' => __('Visualizar album'),
		'search_items' => __('Buscar album'),
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
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-camera.png',
		'rewrite' => true,
		'capability_type' => 'albuns',
		'capabilities' => array(
				'publish_posts' => 'publish_albuns',
				'edit_posts' => 'edit_albuns',
				'edit_others_posts' => 'edit_others_albuns',
				'delete_posts' => 'delete_albuns',
				'delete_others_posts' => 'delete_others_albuns',
				'read_private_posts' => 'read_private_albuns',
				'edit_post' => 'edit_albuns',
				'delete_post' => 'delete_albuns',
				'read_post' => 'read_albuns',
			),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title'),
		'has_archive' => true,
		); 
 
	register_post_type( 'albuns' , $args );
}

add_action('init', 'albuns_register');

function albuns_autosave_scripts() {
	if ('albuns' == get_current_post_type() )
	{
		//wp_dequeue_script( 'autosave' );
	}
}
add_action( 'admin_enqueue_scripts', 'albuns_autosave_scripts' );

function album_location_map()
{	
	?><div id="locationmap" style="height:300px"></div><?
}

function album_dados()
{	
	global $post;
	$descricao = get_post_meta($post->ID, "descricao_album", true);
	if($descricao == null || strlen($descricao) == 0)
	{
		$descricao = "Digite a descrição do álbum aqui";
	}
	?>	
	<table class="form-table">
		<tr>
			<th><label for="local">local</label></th>
			<td>
				<input type="text" style="width:100%;"  name="local" id="local" value="<?php echo get_post_meta($post->ID, "local", true); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe o local do album</span>
			</td>
		</tr>
		<tr>
			<th><label for="descricao_album">Descrição do album</label></th>
			<td>
				<textarea type="text" style="width:100%; height:150px"  name="descricao_album" id="descricao_album"><?php echo $descricao; ?> </textarea><br />
				<span class="description">Por favor informe descrição do album</span>
			</td>
		</tr>
		<tr>
			<th><label for="latitude">Latitude</label></th>
			<td>
				<input type="text" style="width:100%;"  name="latitude" id="latitude" value="<?php echo get_post_meta($post->ID, "latitude", true); ?>" /><br />
				<span class="description">Por favor informe a latitude do album</span>
			</td>
		</tr>
		<tr>
			<th><label for="longitude">Longitude</label></th>
			<td>
				<input type="text" style="width:100%;"  name="longitude" id="longitude" value="<?php echo get_post_meta($post->ID, "longitude", true); ?>" /> <br />
				<span class="description">Por favor informe a longitude do album</span>
			</td>
		</tr>	
		<tr>
			<th><label for="fam_upload">Imagens</label></th>
			<td>
				<div class="fam_upload">
					<input type="hidden" class="upload_single" value="false"   />
					<input type="hidden" class="upload_mandatory" value="true"   />					
					<input type="hidden"  class="postId" value="<?if ($post->ID != null){echo $post->ID;}?>" />	
					<? PrintImages($post->ID ); ?>			
				</div>
				
			</td>
		</tr>
		
			
	</table>
	
	<?
}

function add_custom_album_form() 
{
	add_meta_box('custom-metabox-map', __('Local'), 'album_location_map', 'albuns', 'normal', 'high');
	add_meta_box('custom-metabox-dados', __('Album'), 'album_dados', 'albuns', 'normal', 'high');	
}

add_action('admin_init', 'add_custom_album_form');

function save_album($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}
	if(get_current_post_type() == 'albuns')
	{		
		SaveImages($post_ID, $_POST);
		update_post_meta($post_ID, 'local', $_POST['local']);
		update_post_meta($post_ID, 'latitude', $_POST['latitude']);
		update_post_meta($post_ID, 'longitude', $_POST['longitude']);
		$descricao = $_POST["descricao_album"];
		if ($descricao == "Digite a descrição do álbum aqui")
		{
			$descricao = "";			
		}
		update_post_meta($post_ID, 'descricao_album', $descricao);			
	}
}

function delete_album($post_id) {
	if(get_current_post_type() == 'albuns')
	{		
		delete_post_meta($post_id, '_fam_upload_id_');	
		delete_post_meta($post_id, 'local');
		delete_post_meta($post_id, 'latitude');
		delete_post_meta($post_id, 'longitude');
		delete_post_meta($post_id, 'descricao_album');	
	}    
}

add_action( 'save_post', 'save_album' );
add_action('delete_post', 'delete_album');

// Add the posts and pages columns filter. They can both use the same function.
add_filter('manage_albuns_posts_columns', 'albuns_add_post_thumbnail_column', 5);

// Add the column thumb image
function albuns_add_post_thumbnail_column($columns){
	$columns['albuns_post_thumb'] = __('Capa');
	return $columns;
}


add_filter('manage_albuns_posts_custom_column', 'albuns_display_post_thumbnail_column', 5, 2);

// Grab featured-thumbnail size post thumbnail and display it.
function albuns_display_post_thumbnail_column($column, $post_id ){
	switch( $column ) {		
		case 'albuns_post_thumb' :
			$albumVO = new AlbumVO($post_id, true);			
			if ( $albumVO != null )
			{				
				echo "<img style='width:120px;' src='".$albumVO->MidiaPrincipal->ImageThumbSrc."' />";
			}
			break;
		default :
			break;
	}
}

add_action('init', 'albuns_rewrite');
function albuns_rewrite() {
	global $wp_rewrite;
	$queryarg = 'post_type=albuns&p=';
	$wp_rewrite->add_rewrite_tag('%cpt_albuns_id%', '([^/]+)', $queryarg);
	$wp_rewrite->add_permastruct('albuns', '/albuns/%postname%/%cpt_albuns_id%/', false);
}



