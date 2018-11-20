<?php
/* custom google maps location sugestion*/
function location_sugestion()
{	
	?>	
	<div id="locationmap" style="height:300px">
	</div>	
	<?
}



function add_custom_relato_form() 
{
	add_meta_box('custom-metabox-dados', __('Dados do relato'), 'relato_dados', 'relatos', 'normal', 'high');
	add_meta_box('custom-metabox-mapa', __('Mapa do local'), 'location_sugestion', 'relatos', 'normal', 'high');
	add_meta_box('custom-metabox-seo', __('SEO'), 'seo_box', 'relatos', 'normal', 'high');	
}


add_action('admin_init', 'add_custom_relato_form');

function relato_register() {
 
	$labels = array(
		'name' => _x('Relatos', 'post type general name'),
		'menu_name'=>_x('Relatos', 'post display name'),
		'singular_name' => _x('Relato', 'post type singular name'),
		'add_new' => _x('Adicionar relato', 'portfolio item'),
		'add_new_item' => __('Adicionar novo'),
		'edit_item' => __('Editar relato'),
		'new_item' => __('Novo relato'),
		'view_item' => __('Visualizar relato'),
		'search_items' => __('Buscar relato'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado na lixeira'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
        'taxonomies'  => array( 'post_tag','category' ),
		'query_var' => true,
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-relato.png',
		'rewrite' => true,
		'capability_type' => 'relatos',
		'capabilities' => array(
				'publish_posts' => 'publish_relatos',
				'edit_posts' => 'edit_relatos',
				'edit_others_posts' => 'edit_others_relatos',
				'delete_posts' => 'delete_relatos',
				'delete_others_posts' => 'delete_others_relatos',
				'read_private_posts' => 'read_private_relatos',
				'edit_post' => 'edit_relatos',
				'delete_post' => 'delete_relatos',
				'read_post' => 'read_relatos',
			),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' =>(get_user_role() == "administrator")? array('title','editor','revisions') : array('title','editor','revisions'),
		'has_archive' => true,
		); 
 
	register_post_type( 'relatos' , $args );
}

add_action('init', 'relato_register');

function relato_autosave_scripts() {
	if ('relatos' == get_current_post_type() )
	{
		wp_dequeue_script( 'autosave' );
	}
}
//add_action( 'admin_enqueue_scripts', 'atualizacao_autosave_scripts' );


function relato_dados()
{	
	
	global $post;
	?>	
	<table class="form-table">		
		<tr>
			<th><label for="local">local</label></th>
			<td>
				<input type="text" style="width:100%;"  name="local" id="local" value="<?php echo get_post_meta($post->ID, "local", true); ?>" class="regular-text" /><br />
				<span class="description">Informe o local</span>				
			</td>
		</tr>
		
		<tr style="display:none">
			<th><label for="latitude">Latitude</label></th>
			<td>
				<input type="text" style="width:100%;"  name="latitude" id="latitude" value="<?php echo get_post_meta($post->ID, "latitude", true); ?>" /><br />
				<span class="description">Informe a latitude</span>
			</td>
		</tr>
		<tr style="display:none">
			<th ><label for="longitude">Longitude</label></th>
			<td>
				<input type="text" style="width:100%;"  name="longitude" id="longitude" value="<?php echo get_post_meta($post->ID, "longitude", true); ?>" /> <br />
				<span class="description">Informe a longitude</span>
			</td>
		</tr>
		<tr>
			<th><label for="cidade">Data do relato</label></th>
			<td>
				<input type="text" style="width:100%;" class="fam_date_picker"  name="data_de_visita" id="data_de_visita" value="<?php echo get_post_meta($post->ID, "data_de_visita", true); ?>" /> <br />
				<span class="description">Informe a data do relato</span>
			</td>
		</tr>
		<tr>
			<th><label for="cidade">Temperatura</label></th>
			<td>
				<input type="text" style="width:100%;"  name="temperatura" id="temperatura" value="<?php echo get_post_meta($post->ID, "temperatura", true); ?>" /> <br />
				<span class="description">Informe a temperatura (sem o ' º C ')</span>
			</td>
		</tr>
		<tr>
			<th><label for="fam_upload">Imagem</label></th>
			<td>
				<div class="fam_upload">
					<input type="hidden" class="upload_single" value="true"   />
					<input type="hidden" class="upload_mandatory" value="false"   />
					<input type="hidden" class="upName" value="Imagem destaque"   />					
					<input type="hidden"  class="postId" value="<?if ($post->ID != null){echo $post->ID;}?>" />	
					<? PrintImages($post->ID ); ?>			
				</div>				
			</td>
		</tr>		
	</table>			
	<?
}

function seo_box()
{	
	global $post;
	?>	
	<table class="form-table">		
		<tr>
			<th><label for="local">Descrição SEO</label></th>
			<td>
				<textarea type="text" style="width:100%;" maxlength="160"  name="seo_desc" id="seo_desc" class="regular-text" ><?php echo get_post_meta($post->ID, "seo_desc", true); ?></textarea><br />
				<span class="description">Informe  a descrição SEO (max 160 caracteres)</span>				
			</td>
		</tr>			
	</table>			
	<?
}



function save_relatos($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}
	if(get_current_post_type() == 'relatos')
	{		
		SaveImages($post_ID, $_POST);
		update_post_meta($post_ID, 'local', $_POST['local']);
		update_post_meta($post_ID, 'latitude', $_POST['latitude']);
		update_post_meta($post_ID, 'longitude', $_POST['longitude']);
		update_post_meta($post_ID, 'temperatura', $_POST['temperatura']);
		update_post_meta($post_ID, 'data_de_visita', $_POST['data_de_visita']);
		update_post_meta($post_ID, 'seo_desc', $_POST['seo_desc']);	
		
					
	}
}

function delete_relatos($post_id) {
	if(get_current_post_type() == 'relatos')
	{		
		delete_post_meta($post_id, '_fam_upload_id_');	
		delete_post_meta($post_id, 'local');
		delete_post_meta($post_id, 'latitude');
		delete_post_meta($post_id, 'longitude');
		delete_post_meta($post_id, 'temperatura');	
		delete_post_meta($post_id, 'data_de_visita');
		delete_post_meta($post_id, 'seo_desc');
	}    
}

add_action( 'save_post', 'save_relatos' );
add_action('delete_post', 'delete_relatos');

add_action('init', 'relatos_rewrite');
function relatos_rewrite() {
	global $wp_rewrite;
	$queryarg = 'post_type=relatos&p=';
	$wp_rewrite->add_rewrite_tag('%cpt_relatos_id%', '([^/]+)', $queryarg);	
	$wp_rewrite->add_permastruct('relatos', '/relatos/%postname%/%cpt_relatos_id%/', false);
}








