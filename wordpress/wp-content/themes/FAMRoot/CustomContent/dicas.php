<?php
/* custom google maps location sugestion*/



function dicas_register() {
 
	$labels = array(
		'name' => _x('dicas', 'post type general name'),
		'menu_name'=>_x('Dicas', 'post display name'),
		'singular_name' => _x('Item dica', 'post type singular name'),
		'add_new' => _x('Adicionar dica', 'portfolio item'),
		'add_new_item' => __('Adicionar novo'),
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
		/*'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',*/
		'rewrite' => array( 'slug' => 'dicas-de-viagem' ),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','revisions', 'thumbnail'),
		'has_archive' => true,
		); 
 
		register_post_type( 'dicas' , $args );
}

add_action('init', 'dicas_register');






