<?php

function dica_register() {
 
	$labels = array(
		'name' => _x('Dicas', 'post type general name'),
		'menu_name'=>_x('Dicas de viagem', 'post display name'),
		'singular_name' => _x('Dica', 'post type singular name'),
		'add_new' => _x('Nova dica', 'item'),
		'add_new_item' => __('Nova dica'),
		'edit_item' => __('Editar dica'),
		'new_item' => __('Nova dica'),
		'view_item' => __('Visualizar dica'),
		'search_items' => __('Buscar dica'),
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
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-status.png',
		'rewrite' => array( 'slug' => 'dicas-de-viagem' ),
		'capability_type' => 'dica',
		'capabilities' => array(
				'publish_posts' => 'publish_dica',
				'edit_posts' => 'edit_dica',
				'edit_others_posts' => 'edit_others_dica',
				'delete_posts' => 'delete_dica',
				'delete_others_posts' => 'delete_others_dica',
				'read_private_posts' => 'read_private_dica',
				'edit_post' => 'edit_dica',
				'delete_post' => 'delete_dica',
				'read_post' => 'read_dica',
			),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor'),
		'has_archive' => true,
		); 
 
	register_post_type( 'dica' , $args );
}

add_action('init', 'dica_register');

function dica_autosave_scripts() {
if ('dica' == get_current_post_type() )
	{
		//wp_dequeue_script( 'autosave' );
	}
}
add_action( 'admin_enqueue_scripts', 'dica_autosave_scripts' );




function dica_dados()
{	
	global $post;
	
	?>	
	<table class="form-table">		
		<tr>
			<th><label for="fam_upload">Imagem/Vídeo</label></th>
			<td>
				<div class="fam_upload">
					<input type="hidden" class="upload_single" value="false"   />
					<input type="hidden" class="upload_mandatory" value="false"   />										
					<input type="hidden"  class="postId" value="<?if ($post->ID != null){echo $post->ID;}?>" />	
					<? PrintImages($post->ID ); ?>			
				</div>
				
			</td>
		</tr>
		<tr>
			<th><label for="local">Local</label></th>
			<td>
				<input type="text" style="width:100%;"  name="local" id="local" value="<?php echo get_post_meta($post->ID, "local", true); ?>" class="regular-text" /><br />
				<span class="description">Informe o local</span>
			</td>
		</tr>
		
		<tr style="display:none;">
			<th><label for="latitude">Latitude</label></th>
			<td>
				<input type="text" style="width:100%;"  name="latitude" id="latitude" value="<?php echo get_post_meta($post->ID, "latitude", true); ?>" /><br />
				<span class="description">Informe a latitude</span>
			</td>
		</tr>
		<tr style="display:none;">
			<th><label for="longitude">Longitude</label></th>
			<td>
				<input type="text" style="width:100%;"  name="longitude" id="longitude" value="<?php echo get_post_meta($post->ID, "longitude", true); ?>" /> <br />
				<span class="description">Informe a longitude</span>
			</td>
		</tr>
		<tr>
			<th><label for="locationmap">Mapa</label></th>
			<td>
				<div id="locationmap" style="height:140px;border:1px solid #ccc;">
				</div>
			</td>
		</tr>
		
				
	</table>	
	
	<?
}

function add_custom_dica_dados_form() 
{	
	add_meta_box('custom-metabox-dados', __('Dica'), 'dica_dados', 'dica', 'normal', 'high');	
}

add_action('admin_init', 'add_custom_dica_dados_form');

function save_dica($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}
	if(get_current_post_type() == 'dica')
	{		
		SaveImages($post_ID, $_POST);
		update_post_meta($post_ID, 'local', $_POST['local']);
		update_post_meta($post_ID, 'latitude', $_POST['latitude']);
		update_post_meta($post_ID, 'longitude', $_POST['longitude']);	
			
	}
}

function delete_dica($post_id) {
	if(get_current_post_type() == 'dica')
	{		
		delete_post_meta($post_id, '_fam_upload_id_');	
		delete_post_meta($post_id, 'local');
		delete_post_meta($post_id, 'latitude');
		delete_post_meta($post_id, 'longitude');		
	}    
}

add_action( 'save_post', 'save_dica' );
add_action('delete_post', 'delete_dica');

add_action('init', 'dica_rewrite');
function dica_rewrite() {
	global $wp_rewrite;
	$queryarg = 'post_type=dica&p=';
	$wp_rewrite->add_rewrite_tag('%cpt_dica_id%', '([^/]+)', $queryarg);	
	$wp_rewrite->add_permastruct('dica', '/status/%postname%/%cpt_dica_id%/', false);
}
