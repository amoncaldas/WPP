<?php

function atualizacao_register() {
 
	$labels = array(
		'name' => _x('Status', 'post type general name'),
		'menu_name'=>_x('Status', 'post display name'),
		'singular_name' => _x('Status', 'post type singular name'),
		'add_new' => _x('Novo status', 'item'),
		'add_new_item' => __('Novo status'),
		'edit_item' => __('Editar status'),
		'new_item' => __('Novo status'),
		'view_item' => __('Visualizar status'),
		'search_items' => __('Buscar status'),
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
		'rewrite' => array( 'slug' => 'status' ),
		'capability_type' => 'atualizacao',
		'capabilities' => array(
				'publish_posts' => 'publish_atualizacao',
				'edit_posts' => 'edit_atualizacao',
				'edit_others_posts' => 'edit_others_atualizacao',
				'delete_posts' => 'delete_atualizacao',
				'delete_others_posts' => 'delete_others_atualizacao',
				'read_private_posts' => 'read_private_atualizacao',
				'edit_post' => 'edit_atualizacao',
				'delete_post' => 'delete_atualizacao',
				'read_post' => 'read_atualizacao',
			),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title'),
		'has_archive' => true,
		); 
 
	register_post_type( 'atualizacao' , $args );
}

add_action('init', 'atualizacao_register');

function atualizacao_autosave_scripts() {
	if ('atualizacao' == get_current_post_type() )
	{
		//wp_dequeue_script( 'autosave' );
	}
}
add_action( 'admin_enqueue_scripts', 'atualizacao_autosave_scripts' );




function atualizacao_dados()
{	
	global $post;
	$conteudo = get_post_meta($post->ID, "conteudo", true);
	if($conteudo == null || strlen($conteudo) == 0)
	{
		$conteudo = "Digite o texto do status aqui";
	}
	?>	
	<table class="form-table">
		<tr>
			<th><label for="conteudo">Texto do status</label></th>
			<td>
				<textarea type="text" style="width:100%; height:150px" class="atualizacaoContent" maxlength="1000"  name="conteudo" id="conteudo"><?php  echo $conteudo; ?> </textarea><br />
				<span class="description">Conteudo da postagem</span>
			</td>
		</tr>	
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

function add_custom_atualizacao_dados_form() 
{	
	add_meta_box('custom-metabox-dados', __('Status'), 'atualizacao_dados', 'atualizacao', 'normal', 'high');	
}

add_action('admin_init', 'add_custom_atualizacao_dados_form');

function save_atualizacao($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}
	if(get_current_post_type() == 'atualizacao')
	{		
		SaveImages($post_ID, $_POST);
		update_post_meta($post_ID, 'local', $_POST['local']);
		update_post_meta($post_ID, 'latitude', $_POST['latitude']);
		update_post_meta($post_ID, 'longitude', $_POST['longitude']);
		
		$conteudo = $_POST["conteudo"];		
		if ($conteudo == "Digite o texto do status aqui")
		{
			$conteudo = "";			
		}		
		update_post_meta($post_ID, 'conteudo', $conteudo);	
		//GetFBPlace();	
		
			
	}
}

function delete_atualizacao($post_id) {
	if(get_current_post_type() == 'atualizacao')
	{		
		delete_post_meta($post_id, '_fam_upload_id_');	
		delete_post_meta($post_id, 'local');
		delete_post_meta($post_id, 'latitude');
		delete_post_meta($post_id, 'longitude');
		delete_post_meta($post_id, 'conteudo');	
	}    
}

add_action( 'save_post', 'save_atualizacao' );
add_action('delete_post', 'delete_atualizacao');

add_action('init', 'atualizacao_rewrite');
function atualizacao_rewrite() {
	global $wp_rewrite;
	$queryarg = 'post_type=atualizacao&p=';
	$wp_rewrite->add_rewrite_tag('%cpt_status_id%', '([^/]+)', $queryarg);	
	$wp_rewrite->add_permastruct('atualizacao', '/status/%postname%/%cpt_status_id%/', false);
}
