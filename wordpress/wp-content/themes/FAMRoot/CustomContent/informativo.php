<?php

function Informativo_register() {
 
	$labels = array(
		'name' => _x('Informativos', 'post type general name'),
		'menu_name'=>_x('Informativos', 'post display name'),
		'singular_name' => _x('Post de informativo', 'post type singular name'),
		'add_new' => _x('Adicionar post de informativo', 'informativo item'),
		'add_new_item' => __('Adicionar novo'),
		'edit_item' => __('Editar post de informativo'),
		'new_item' => __('Novo post de informativo'),
		'view_item' => __('Visualizar post de informativo'),
		'search_items' => __('Buscar post de informativo'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado na lixeira'),
		'parent_item_colon' => ''
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => false,
		'exclude_from_search'=> true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-informativo.png',
		'rewrite' => array( 'slug' => 'informativo'),
		'capability_type' => 'Informativo',
		'capabilities' => array(
				'publish_posts' => 'publish_informativo',
				'edit_posts' => 'edit_informativo',
				'edit_others_posts' => 'edit_others_informativo',
				'delete_posts' => 'delete_informativo',
				'delete_others_posts' => 'delete_others_informativo',
				'read_private_posts' => 'read_private_informativo',
				'edit_post' => 'edit_informativo',
				'delete_post' => 'delete_informativo',
				'read_post' => 'read_informativo',
			),
		'taxonomies' => array(),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','revisions'),
		'has_archive' => false,
		); 
 
	register_post_type( 'informativo' , $args );
}

add_action('init', 'informativo_register');


function informativo_dados()
{	
	global $post;
	$url_informativo = get_post_meta($post->ID, "url_informativo", true);
	if($url_informativo == null || strlen($url_informativo) == 0)
	{
		$url_informativo = "";
	}
	?>	
	<table class="form-table">		
		<tr>
			<th><label for="url_informativo">Url destino do informativo</label></th>
			<td>
				<input type="text" style="width:100%;"  name="url_informativo" id="url_informativo" value="<? echo $url_informativo?>" /><br />
				<span class="url_informativo">Por favor informe a url</span>
			</td>
		</tr>	
			
	</table>	
	<?
}

function add_custom_informativo_form() 
{	
	add_meta_box('custom-metabox-dados', __('Url destino'), 'informativo_dados', 'informativo', 'normal', 'high');	
}

add_action('admin_init', 'add_custom_informativo_form');

function save_informativo($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}
	if(get_current_post_type() == 'informativo')
	{		
		$url = $_POST["url_informativo"];		
		update_post_meta($post_ID, 'url_informativo', $url);			
	}
}

function delete_informativo($post_id) {
	if(get_current_post_type() == 'informativo')
	{		
		delete_post_meta($post_id, 'url_informativo');	
	}    
}

add_action( 'save_post', 'save_informativo' );
add_action('delete_post', 'delete_informativo');

function informativo_admin_css() {
	global $post_type;
	if($post_type == 'informativo') {
		echo '<style type="text/css">#edit-slug-box,#view-post-btn,#post-preview,.updated p a{display: none;}</style>';
	}
}
add_action('admin_head', 'informativo_admin_css'); 