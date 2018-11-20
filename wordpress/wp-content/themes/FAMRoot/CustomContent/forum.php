<?php
/* custom google maps location sugestion*/



function forum_register() {
 
	$labels = array(
		'name' => _x('Forum', 'post type general name'),
		'menu_name'=>_x('Fórum', 'post display name'),
		'singular_name' => _x('Tópico de forum', 'post type singular name'),
		'add_new' => _x('Adicionar tópico de fórum', 'tópico item'),
		'add_new_item' => __('Adicionar novo tópico de fórum'),
		'edit_item' => __('Editar tópico de fórum'),
		'new_item' => __('Novo tópico de fórum'),
		'view_item' => __('Visualizar tópico de fórum'),
		'search_items' => __('Buscar tópico de fórum'),
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
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-forum.png',
		'rewrite' => array( 'slug' => 'forum' ),
		'capability_type' => 'forum',
		'capabilities' => array(
				'publish_posts' => 'publish_forum',
				'edit_posts' => 'edit_forum',
				'edit_others_posts' => 'edit_others_forum',
				'delete_posts' => 'delete_forum',
				'delete_others_posts' => 'delete_others_forum',
				'read_private_posts' => 'read_private_forum',
				'edit_post' => 'edit_forum',
				'delete_post' => 'delete_forum',
				'read_post' => 'read_forum',
			),
		'taxonomies' => array('category'),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','excerpt', 'comments'),
		'has_archive' => true,
		); 
 
		register_post_type( 'forum' , $args );
}

add_action('init', 'forum_register');

function tune_admin_area() {
    echo '<style>#commentstatusdiv{ display:none; }</style>';
}
add_action('admin_head', 'tune_admin_area');


add_action('init', 'forum_rewrite');
function forum_rewrite() {
	global $wp_rewrite;
	$queryarg = 'post_type=forum&p=';	
	$wp_rewrite->add_rewrite_tag('%cpt_forum_id%', '([^/]+)', $queryarg);	
	$wp_rewrite->add_permastruct('forum', '/forum/%postname%/%cpt_forum_id%/', false);
}

add_filter('gettext', 'custom_rewrites', 10, 4);
function custom_rewrites($translation, $text, $domain) {
    
    $translations = &get_translations_for_domain($domain);
    $translation_array = array();

    switch (get_current_post_type()) {
        case 'forum':
            $translation_array = array(
                'Enter title here' => 'Digite o título do tópico aqui',
                'Excerpt' => "Descrição do tópico",
				'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about manual excerpts.</a>' => "",
				'Comments' => 'Mensagens'
            );
            break;
    }
	
    if (array_key_exists($text, $translation_array)) {
        return $translations->translate($translation_array[$text]);
    }
    return $translation;
}




//function forum_comments_on( $data ) {
//    if( $data['post_type'] == 'forum' ) {
//        $data['comment_status'] = 1;
//    }
//    return $data;
//}
//add_filter( 'wp_insert_post_data', 'forum_comments_on' );


function assign_forum_categories(){
	global $wp_taxonomies;
	if(get_current_post_type() == "forum")
	{
	  $wp_taxonomies['category']->cap->assign_terms = 'edit_forum';
	}
}
add_action('init', 'assign_forum_categories');





