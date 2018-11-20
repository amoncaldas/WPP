<?php

//add role viajante
 add_role('viajante', 'Viajante', array( 
	
	//pages  
	'delete_others_pages'=>false,
	'delete_private_pages'=>false,
	'delete_published_pages'=>false,
	'edit_others_pages'=>false,
	'edit_pages'=>false,
	'edit_private_pages'=>false,
	'delete_pages'=>false,
	'edit_published_pages'=>false,
	'publish_pages'=>false,
	'read_private_pages'=>false,
	
	//posts
	'delete_others_posts'=>false,	
	'delete_posts'=>false,	
	'delete_private_posts'=>false,	
	'delete_published_posts'=>false,	
	'edit_others_posts'=>false,	
	'edit_posts'=>true,	
	'edit_private_posts'=>false,	
	'edit_published_posts'=>false,
	'publish_posts'=>false,
	'read_private_posts'=>false,
	'read_posts'=>false,
	
	//themes
	'update_themes'=>false,
	'install_themes'=>false,
	'delete_themes'=>false,
	'edit_themes'=>false,
	'edit_theme_options'=>false,
	'switch_themes'=>false,
	
	//plugins
	'delete_plugins'=>false,
	'update_plugins'=>false,
	'install_plugins'=>false,
	'activate_plugins'=>false,
	
	//misc		
	'update_core'=>false,	
	'edit_dashboard'=>false,	
	'export'=>false,
	'import'=>false,		
	'manage_links'=>false,
	'moderate_comments'=>false,	
	'unfiltered_html'=>false,	
	'manage_options'=>false,
	'read'=>true,	
	
	//videos
	'add_videos'=>true,
	
	/* //terms
	'manage_terms' => false,
    'edit_terms' => false,
    'delete_terms' => false,
    'assign_terms' => false,	*/
	'manage_categories'=> false,  
	
	
	//users
	'delete_users'=>false,
	'remove_users'=>false,
	'promote_users'=>false,
	'list_users'=>false,
	'create_users'=>false,
	'edit_users'=>false,
	'manage_network_users'=>false,	
	
	//files
	'edit_files'=>true,
	'upload_files'=>true,
	
	//forum
	'publish_forum' => true,
    'edit_forum' => true,
    'edit_others_forum' => false,
    'delete_forum' => true,
    'delete_others_forum' => false,
    'read_private_forum' => true,   
    'read_forum' => true,
	
	//albuns
	'publish_albuns' => true,
    'edit_albuns' => true,
    'edit_others_albuns' => true,
    'delete_albuns' => true,
    'delete_others_albuns' => false,
    'read_private_albuns' => true,
    'delete_albuns' => true,
    'read_albuns' => true,
	
	//atualizacao
	'publish_atualizacao' => true,
    'edit_atualizacao' => true,
    'edit_others_atualizacao' => false,
    'delete_atualizacao' => true,
    'delete_others_atualizacao' => false,
    'read_private_atualizacao' => true,
    'delete_atualizacao' => true,
    'read_atualizacao' => true,
	
	//destaque
	'publish_destaque' => true,
    'edit_destaque' => true,
    'edit_others_destaque' => true,
    'delete_destaque' => true,
    'delete_others_destaque' => true,
    'read_private_destaque' => true,
    'delete_destaque' => true,
    'read_destaque' => true,
	
	//relatos
	'publish_relatos' => false,
    'edit_relatos' => true,
    'edit_others_relatos' => false,
    'delete_relatos' => true,
    'delete_others_relatos' => false,
    'read_private_relatos' => true,
    'delete_relatos' => true,
    'read_relatos' => true,
	
	//viagem
	'publish_viagem' => false,
    'edit_viagem' => false,
    'edit_others_viagem' => false,
    'delete_viagem' => false,
    'delete_others_viagem' => false,
    'read_private_viagem' => false,
    'delete_viagem' => false,
    'read_viagem' => true,
	
	//blog
	'publish_blog_post' => false,
    'edit_blog_post' => false,
    'edit_others_blog_post' => false,
    'delete_blog_post' => false,
    'delete_others_blog_post' => false,
    'read_private_blog_post' => false,
    'delete_blog_post' => false,
    'read_blog_post' => false,
));

////add role viajante adm
//remove_role('adm_fam_root');
add_role('adm_fam_root', 'Administrador site principal', array( 
	
	//pages  
	'delete_others_pages'=>true,
	'delete_private_pages'=>true,
	'delete_published_pages'=>true,
	'edit_others_pages'=>true,
	'edit_pages'=>true,
	'edit_private_pages'=>true,
	'delete_pages'=>true,
	'edit_published_pages'=>true,
	'publish_pages'=>true,
	'read_private_pages'=>true,
	
	
	//posts
	'delete_others_posts'=>false,	
	'delete_posts'=>false,	
	'delete_private_posts'=>false,	
	'delete_published_posts'=>false,	
	'edit_others_posts'=>false,	
	'edit_posts'=>true,	/*  allow moderate comments*/
	'edit_private_posts'=>false,	
	'edit_published_posts'=>false,
	'publish_posts'=>false,
	'read_private_posts'=>false,
	'read_posts'=>true,
	
	//themes
	'update_themes'=>false,
	'install_themes'=>false,
	'delete_themes'=>false,
	'edit_themes'=>false,
	'edit_theme_options'=>false,
	'switch_themes'=>false,
	
	//plugins
	'delete_plugins'=>false,
	'update_plugins'=>false,
	'install_plugins'=>false,
	'activate_plugins'=>false,
	
	//misc		
	'update_core'=>false,	
	'edit_dashboard'=>false,	
	'export'=>false,
	'import'=>false,		
	'manage_links'=>false,
	'moderate_comments'=>true,
	'edit_comment' => true,	
	'unfiltered_html'=>false,	
	'manage_options'=>false,
	'read'=>true,	
	'fam_settings'=>true,
	
	
	//videos
	'add_videos'=>true,
	
	 //terms
	/*'manage_terms' => true,
    'edit_terms' => false,
    'delete_terms' => false,
    'assign_terms' => false,	*/
	'manage_categories'=> true,
	
	//users
	'delete_users'=>false,
	'remove_users'=>true,
	'promote_users'=>false,
	'list_users'=>true,
	'create_users'=>true,
	'edit_users'=>false,	
	'manage_network_users'=>true,
	
	//files
	'edit_files'=>true,
	'upload_files'=>true,
	
	//forum
	'publish_forum' => true,
    'edit_forum' => true,
    'edit_others_forum' => true,
    'delete_forum' => true,
    'delete_others_forum' => false,
    'read_private_forum' => true,    
    'read_forum' => true,
	
	//albuns
	'publish_albuns' => true,
    'edit_albuns' => true,
    'edit_others_albuns' => true,
    'delete_albuns' => true,
    'delete_others_albuns' => true,
    'read_private_albuns' => true,
    'delete_albuns' => true,
    'read_albuns' => true,
	
	//atualizacao
	'publish_atualizacao' => false,
    'edit_atualizacao' => false,
    'edit_others_atualizacao' => false,
    'delete_atualizacao' => false,
    'delete_others_atualizacao' => false,
    'read_private_atualizacao' => false,
    'delete_atualizacao' => false,
    'read_atualizacao' => false,
	
	//destaque
	'publish_destaque' => false,
    'edit_destaque' => false,
    'edit_others_destaque' => false,
    'delete_destaque' => false,
    'delete_others_destaque' => false,
    'read_private_destaque' => false,
    'delete_destaque' => false,
    'read_destaque' => false,
	
	//relatos
	'publish_relatos' => false,
    'edit_relatos' => false,
    'edit_others_relatos' => false,
    'delete_relatos' => false,
    'delete_others_relatos' => false,
    'read_private_relatos' => false,
    'delete_relatos' => false,
    'read_relatos' => false,
	
	//viagem
	'publish_viagem' => false,
    'edit_viagem' => false,
    'edit_others_viagem' => false,
    'delete_viagem' => false,
    'delete_others_viagem' => false,
    'read_private_viagem' => false,    
    'delete_viagem' => false,
    'read_viagem' => false,
	
	//blog
	'publish_blog_post' => true,
    'edit_blog_post' => true,
    'edit_others_blog_post' => true,
    'delete_blog_post' => true,
    'delete_others_blog_post' => true,
    'read_private_blog_post' => true,
    'delete_blog_post' => true,
    'read_blog_post' => true,
	
	//informativo
	'publish_informativo' => true,
    'edit_informativo' => true,
    'edit_others_informativo' => true,
    'delete_informativo' => true,
    'delete_others_informativo' => true,
    'read_private_informativo' => true,    
    'delete_informativo' => true,
    'read_informativo' => true,	
));


add_role('usuario_forum', 'Usuário Fórum', array( 
	
	//pages  
	'delete_others_pages'=>false,
	'delete_private_pages'=>false,
	'delete_published_pages'=>false,
	'edit_others_pages'=>false,
	'edit_pages'=>false,
	'edit_private_pages'=>false,
	'delete_pages'=>false,
	'edit_published_pages'=>false,
	'publish_pages'=>false,
	'read_private_pages'=>false,
	
	//posts
	'delete_others_posts'=>false,	
	'delete_posts'=>false,	
	'delete_private_posts'=>false,	
	'delete_published_posts'=>false,	
	'edit_others_posts'=>false,	
	'edit_posts'=>false,	
	'edit_private_posts'=>false,	
	'edit_published_posts'=>false,
	'publish_posts'=>false,
	'read_private_posts'=>false,
	'read_posts'=>true,
	
	//themes
	'update_themes'=>false,//*
	'install_themes'=>false,//*
	'delete_themes'=>false,//*
	'edit_themes'=>false,
	'edit_theme_options'=>false,
	'switch_themes'=>false,
	
	//plugins
	'delete_plugins'=>false,
	'update_plugins'=>false,//*
	'install_plugins'=>false,//*
	'activate_plugins'=>false,
	
	//misc		
	'update_core'=>false,	
	'edit_dashboard'=>false,	
	'export'=>false,
	'import'=>false,		
	'manage_links'=>false,
	'moderate_comments'=>false,	
	'edit_comment' => false,
	'unfiltered_html'=>false,	
	'manage_options'=>false,
	'read'=>true,	
	
	//videos
	'add_videos'=>false,//*
	
	 //terms
	/*'manage_terms' => false,
    'edit_terms' => false,
    'delete_terms' => false,
    'assign_terms' => true,	*/
	'manage_categories'=> false,   
	
	//users
	'delete_users'=>false,
	'remove_users'=>false,
	'promote_users'=>false,
	'list_users'=>false,
	'create_users'=>false,
	'edit_users'=>false,
	'manage_network_users'=>false,
	
	//files
	'edit_files'=>false,
	'upload_files'=>false,
	'unfiltered_upload'=>false,//*
	
	//forum
	'publish_forum' => true,
    'edit_forum' => true,
    'edit_others_forum' => false,
    'delete_forum' => true,
    'delete_others_forum' => false,
    'read_private_forum' => false,    
    'read_forum' => true,
	
	//albuns
	'publish_albuns' => false,
    'edit_albuns' => false,
    'edit_others_albuns' => false,
    'delete_albuns' => false,
    'delete_others_albuns' => false,
    'read_private_albuns' => false,
    'delete_albuns' => false,
    'read_albuns' => false,
	
	//atualizacao
	'publish_atualizacao' => false,
    'edit_atualizacao' => false,
    'edit_others_atualizacao' => false,
    'delete_atualizacao' => false,
    'delete_others_atualizacao' => false,
    'read_private_atualizacao' => false,
    'delete_atualizacao' => false,
    'read_atualizacao' => false,
	
	//destaque
	'publish_destaque' => false,
    'edit_destaque' => false,
    'edit_others_destaque' => false,
    'delete_destaque' => false,
    'delete_others_destaque' => false,
    'read_private_destaque' => false,
    'delete_destaque' => false,
    'read_destaque' => false,
	
	//relatos
	'publish_relatos' => false,
    'edit_relatos' => false,
    'edit_others_relatos' => false,
    'delete_relatos' => false,
    'delete_others_relatos' => false,
    'read_private_relatos' => false,
    'delete_relatos' => false,
    'read_relatos' => false,
	
	//viagem
	'publish_viagem' => false,
    'edit_viagem' => false,
    'edit_others_viagem' => false,
    'delete_viagem' => false,
    'delete_others_viagem' => false,
    'read_private_viagem' => false,    
    'delete_viagem' => false,
    'read_viagem' => false,
	
	//blog
	'publish_blog_post' => false,
    'edit_blog_post' => false,
    'edit_others_blog_post' => false,
    'delete_blog_post' => false,
    'delete_others_blog_post' => false,
    'read_private_blog_post' => false,
    'delete_blog_post' => false,
    'read_blog_post' => false,
));

//remove_role('blog_writer');
add_role('blog_writer', 'Escritor do Blog', array( 
	
	//pages  
	'delete_others_pages'=>false,
	'delete_private_pages'=>false,
	'delete_published_pages'=>false,
	'edit_others_pages'=>false,
	'edit_pages'=>false,
	'edit_private_pages'=>false,
	'delete_pages'=>false,
	'edit_published_pages'=>false,
	'publish_pages'=>false,
	'read_private_pages'=>false,
	
	//posts
	'delete_others_posts'=>false,	
	'delete_posts'=>false,	
	'delete_private_posts'=>false,	
	'delete_published_posts'=>false,	
	'edit_others_posts'=>false,	
	'edit_posts'=>false,	
	'edit_private_posts'=>false,	
	'edit_published_posts'=>false,
	'publish_posts'=>false,
	'read_private_posts'=>false,
	'read_posts'=>true,
	
	//themes
	'update_themes'=>false,//*
	'install_themes'=>false,//*
	'delete_themes'=>false,//*
	'edit_themes'=>false,
	'edit_theme_options'=>false,
	'switch_themes'=>false,
	
	//plugins
	'delete_plugins'=>false,
	'update_plugins'=>false,//*
	'install_plugins'=>false,//*
	'activate_plugins'=>false,
	
	//misc		
	'update_core'=>false,	
	'edit_dashboard'=>false,	
	'export'=>false,
	'import'=>false,		
	'manage_links'=>false,
	'moderate_comments'=>false,	
	'edit_comment' => false,
	'unfiltered_html'=>false,	
	'manage_options'=>false,
	'read'=>true,	
	
	//videos
	'add_videos'=>true,//*
	
	 //terms
	/*'manage_terms' => false,
    'edit_terms' => false,
    'delete_terms' => false,*/
    'assign_terms' => true,
	'manage_categories'=> false,   
	
	//users
	'delete_users'=>false,
	'remove_users'=>false,
	'promote_users'=>false,
	'list_users'=>false,
	'create_users'=>false,
	'edit_users'=>false,
	'manage_network_users'=>false,
	
	//files
	'edit_files'=>true,
	'upload_files'=>true,
	'unfiltered_upload'=>false,//*
	
	//forum
	'publish_forum' => true,
    'edit_forum' => true,
    'edit_others_forum' => false,
    'delete_forum' => true,
    'delete_others_forum' => false,
    'read_private_forum' => false,    
    'read_forum' => true,
	
	//albuns
	'publish_albuns' => true,
    'edit_albuns' => true,
    'edit_others_albuns' => true,
    'delete_albuns' => true,
    'delete_others_albuns' => true,
    'read_private_albuns' => true,
    'delete_albuns' => true,
    'read_albuns' => true,
	
	//atualizacao
	'publish_atualizacao' => false,
    'edit_atualizacao' => false,
    'edit_others_atualizacao' => false,
    'delete_atualizacao' => false,
    'delete_others_atualizacao' => false,
    'read_private_atualizacao' => false,
    'delete_atualizacao' => false,
    'read_atualizacao' => false,
	
	//destaque
	'publish_destaque' => false,
    'edit_destaque' => false,
    'edit_others_destaque' => false,
    'delete_destaque' => false,
    'delete_others_destaque' => false,
    'read_private_destaque' => false,
    'delete_destaque' => false,
    'read_destaque' => false,
	
	//relatos
	'publish_relatos' => false,
    'edit_relatos' => false,
    'edit_others_relatos' => false,
    'delete_relatos' => false,
    'delete_others_relatos' => false,
    'read_private_relatos' => false,
    'delete_relatos' => false,
    'read_relatos' => false,
	
	//viagem
	'publish_viagem' => false,
    'edit_viagem' => false,
    'edit_others_viagem' => false,
    'delete_viagem' => false,
    'delete_others_viagem' => false,
    'read_private_viagem' => false,    
    'delete_viagem' => false,
    'read_viagem' => false,
	
	//blog
	'publish_blog_post' => true,
    'edit_blog_post' => true,
    'edit_others_blog_post' => false,
    'delete_blog_post' => true,
    'delete_others_blog_post' => false,
    'read_private_blog_post' => false,    
    'read_blog_post' => true,
));


/******************** Expand adminsitrator capabilities ********************/
 
add_action('init', 'site_adm_expand_capabilities');
 
function site_adm_expand_capabilities() {
	
	//run only once
	/*$supers = get_super_admins();
	foreach ( $supers as $admin ) {
		$user = new WP_User( 0, $admin );
		$user->add_cap( 'fam_settings' );
		$user->add_cap('add_videos')	;
		$user->add_cap('edit_viagem')	;
		
		$user->add_cap('publish_informativo')	;
		$user->add_cap('edit_informativo')	;
		$user->add_cap('edit_others_informativo')	;
		$user->add_cap('delete_informativo')	;
		$user->add_cap('delete_others_informativo')	;
		$user->add_cap('read_private_informativo')	;
		$user->add_cap('delete_informativo')	;
		$user->add_cap('read_informativo')	;	
		
	}*/
	
    //There has to be a better way to do this?
    global $wp_roles;
 
    //Check if $wp_roles has been initialized
    if ( isset($wp_roles) ) { 
			
		$role = get_role( 'administrator' );
		
		$role->add_cap( 'publish_albuns' );
		$role->add_cap( 'edit_albuns' );
		$role->add_cap( 'delete_albuns' );
		$role->add_cap( 'edit_others_albuns');
		$role->add_cap( 'delete_others_albuns' );
		$role->add_cap( 'read_private_albuns' );
		$role->add_cap( 'read_albuns' );
		
		
		$role->add_cap( 'publish_atualizacao' );
		$role->add_cap( 'edit_atualizacao' );
		$role->add_cap( 'delete_atualizacao' );			
		$role->add_cap( 'read_atualizacao' );
		$role->add_cap( 'edit_others_atualizacao');
		$role->add_cap( 'delete_others_atualizacao');		
		$role->add_cap( 'read_private_atualizacao' );
		
		$role->add_cap( 'publish_destaque' );
		$role->add_cap( 'edit_destaque' );
		$role->add_cap( 'delete_destaque' );		
		$role->add_cap( 'edit_others_destaque');
		$role->add_cap( 'delete_others_destaque');		
		$role->add_cap( 'read_private_destaque' );
		$role->add_cap( 'read_destaque' );
		$role->add_cap( 'add_videos' );//youtube videos
				
		$role->add_cap( 'publish_relatos' );
		$role->add_cap( 'edit_relatos' );
		$role->add_cap( 'delete_relatos' );		
		$role->add_cap( 'edit_others_relatos');
		$role->add_cap( 'delete_others_relatos');		
		$role->add_cap( 'read_private_relatos' );
		$role->add_cap( 'read_relatos' );		
	
		$role->add_cap( 'publish_viagem' );
		$role->add_cap( 'edit_viagem' );	
		$role->add_cap( 'read_viagem' );
		$role->add_cap( 'edit_others_viagem');		
		$role->add_cap('fam_settings');	
		
		$role->add_cap('edit_posts');
		$role->add_cap('delete_posts');
		$role->add_cap('read_posts');
		$role->add_cap('delete_private_posts');
		$role->add_cap('delete_others_posts');
		$role->add_cap('delete_published_posts');
		$role->add_cap('edit_private_posts');
		$role->add_cap('read_private_posts');
		
		$role->add_cap('publish_informativo')	;
		$role->add_cap('edit_informativo')	;
		$role->add_cap('edit_others_informativo')	;
		$role->add_cap('delete_informativo')	;
		$role->add_cap('delete_others_informativo')	;
		$role->add_cap('read_private_informativo')	;
		$role->add_cap('delete_informativo')	;
		$role->add_cap('read_informativo')	;
		
		
		/* remove */
		$role->remove_cap('manage_network_users');
		
		$role->remove_cap('unfiltered_upload');
		$role->remove_cap('update_core');
		$role->remove_cap('export');
		$role->remove_cap('import');
		$role->remove_cap('manage_links');
		$role->remove_cap('unfiltered_html');
		$role->remove_cap('manage_options');
		
		
		$role->remove_cap('update_themes');
		$role->remove_cap('install_themes');
		$role->remove_cap('delete_themes');
		$role->remove_cap('edit_themes');
		$role->remove_cap('edit_theme_options');
		$role->remove_cap('switch_themes');
		
		$role->remove_cap('delete_plugins');
		$role->remove_cap('update_plugins');
		$role->remove_cap('install_plugins');
		$role->remove_cap('manage_options');
		$role->remove_cap('activate_plugins');		
		
		$role->remove_cap('publish_posts');	
		
		//wp_update_user( array ('ID' => $user_id, 'role' => 'administrator' ) ) ;	
		
	} 
}
/******************** end EXPAND CAPS ********************/
	

require_once( ABSPATH . '/FAMCore/BO/Imagem.php' );

function SaveUserImage($userId, $postArray)
{	
	if(is_array($postArray) && count($postArray) > 0)
	{		
		foreach($postArray as $key=>$value)
		{			 		
			if(strpos($key,"_fam_upload_id_") > -1)
			{
				$attachId = $value;							
				update_user_meta( $userId, "_fam_upload_id_", $value);										
			}
			if(strpos($key,"_fam_upload_site_id") > -1)
			{				
				update_user_meta( $userId, "_fam_upload_site_id", $value);						
			}
			if(strpos($key,"_fam_upload_name_")> -1)
			{
				$current_post = get_post($attachId, 'ARRAY_A' );
				$current_post['post_title'] = $value;
				wp_update_post($current_post);										
			}
		}
	}	
}

function fam_delete_user($user_id) {	
	delete_user_meta($user_id, '_fam_upload_id_');
	delete_user_meta($user_id, '_fam_upload_site_id');
	delete_user_meta($user_id, 'url_perfil_facebook');
	delete_user_meta($user_id, 'local_de_nascimento');
	delete_user_meta($user_id, 'data_de_nascimento');
	delete_user_meta($user_id, 'latitude_de_nascimento');
	delete_user_meta($user_id, 'longitude_de_nascimento');
	delete_user_meta($user_id, 'local_de_residencia');
	delete_user_meta($user_id, 'latitude_de_residencia');
	delete_user_meta($user_id, 'longitude_de_residencia');
	delete_user_meta($user_id, 'status_viajante');
	
}

add_action( 'personal_options_update', 'fam_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'fam_save_extra_profile_fields' );
add_action( 'delete_user', 'fam_delete_user');

function fam_save_extra_profile_fields( $user_id ) {

	/*if ( !current_user_can( 'edit_user', $user_id ) )
	{
		return false;
	}	*/
	SaveUserImage($user_id, $_POST);	
	update_usermeta( $user_id, 'local_de_nascimento', $_POST['local_de_nascimento'] );
	update_usermeta( $user_id, 'data_de_nascimento', $_POST['data_de_nascimento'] );
	update_usermeta( $user_id, 'latitude_de_nascimento', $_POST['latitude_de_nascimento'] );
	update_usermeta( $user_id, 'longitude_de_nascimento', $_POST['longitude_de_nascimento'] );
	update_usermeta( $user_id, 'local_de_residencia', $_POST['local_de_residencia'] );	
	update_usermeta( $user_id, 'latitude_de_residencia', $_POST['latitude_de_residencia'] );
	update_usermeta( $user_id, 'longitude_de_residencia', $_POST['longitude_de_residencia'] );
	update_usermeta( $user_id, 'status_viajante', $_POST['status_viajante']);
	update_usermeta( $user_id, 'url_perfil_facebook', $_POST['url_perfil_facebook']);
	
	
}

function PrintUserImage($userId)
{
	$imgId = get_the_author_meta("_fam_upload_id_", $userId);	
	$site_id = get_the_author_meta("_fam_upload_site_id", $userId);
	if($site_id > 0 && switch_to_blog($site_id))
	{		
		if($imgId != null && $imgId > 0)
		{	
			try
			{		
				$img = Imagem::GetImage($imgId);				
				echo "<ul>
						<li>							
							<a class='fancybox' href='".$img->ImageLargeSrc."' rel='permalink' title='".$img->Titulo."'><img src='".$img->ImageGaleryThumbSrc. "' /></a>;				
							<input type='hidden' value='".$imgId."' id='_fam_upload_id_".$imgId."' name='_fam_upload_id_".$imgId."' />
							<input type='hidden' value='".$site_id."' id='_fam_upload_site_id_".$imgId."' name='_fam_upload_site_id_".$imgId."' />
							<input type='text' disabled='disabled' name='_fam_upload_name_".$imgId."' id='_fam_upload_name_".$imgId."' value='".$img->Descricao."' />
							<span class='fam_delete_img'></span>
						</li>
					</ul>";  
			}
			catch(exception $ex){
				delete_user_meta($userId, '_fam_upload_id_');
				delete_user_meta($userId, '_fam_upload_site_id');				
			}
		}		
	}	
	restore_current_blog();
}


function fam_show_extra_profile_fields( $user ) 
{ 
	add_filter('media_upload_tabs', 'remove_media_library_tab');
	
	global $post;
	?>
	<h3>Dados adicionais do viajante</h3>
	<table class="form-table">
		<?if((get_user_role() == "adm_fam_root" || get_user_role() == "administrator" || is_super_admin()) && get_current_user_id() != $user->ID) {?>
		<tr>
			<th><label for="status_viajante">Status viajante</label></th>
			<td>
				<input type="radio" name="status_viajante" value="enable"  <?php if (get_the_author_meta('status_viajante',$user->ID) == 'enabled' || get_the_author_meta('status_viajante',$user->ID) == null){echo "checked='checked'";}?>  />Ativo<br/>
				<input type="radio"  name="status_viajante" value="disable"  <?php if (get_the_author_meta('status_viajante',$user->ID) == 'disabled'){echo "checked='checked'";}?> />Bloqueado<br/>
				<span class="description">Por favor informe o status do viajante</span>
			</td>
		</tr>
		<?}?>
		<tr>
			<th><label for="data_de_nascimento">Data de nascimento</label></th>
			<td>
				<input type="text" class="fam_date_picker"   name="data_de_nascimento" id="data_de_nascimento" value="<?php echo esc_attr( get_the_author_meta( 'data_de_nascimento', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe a data de nascimento</span>
			</td>
		</tr>
		<tr>
			<th><label for="url_perfil_facebook">Url do seu perfil no facebook</label></th>
			<td>
				<input type="text" class="url_perfil_facebook" style="width:100%" name="url_perfil_facebook" id="url_perfil_facebook" value="<?php echo esc_attr( get_the_author_meta( 'url_perfil_facebook', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe a url do seu perfil no facebook. Exemplo: facebook.com/seunome</span>
			</td>
		</tr>
		<tr>
			<th><label for="local_de_nascimento">Local de nascimento</label></th>
			<td>
				<input type="text" class="localNascimento" style="width:100%" name="local_de_nascimento" id="local_de_nascimento" value="<?php echo esc_attr( get_the_author_meta( 'local_de_nascimento', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe o local de nascimento</span>
			</td>
		</tr>
		<tr>
			<th><label for="local_de_residencia">Local de residência</label></th>
			<td>
				<input type="text" class="localResidencia" style="width:100%" name="local_de_residencia" id="local_de_residencia" value="<?php echo esc_attr( get_the_author_meta( 'local_de_residencia', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe o local de nascimento</span>
			</td>
		</tr>
		<tr style="display:none">
			<th><label for="latitude_de_nascimento">Latitude de nascimento</label></th>
			<td>
				<input type="text" class="latitudeNascimento" name="latitude_de_nascimento" id="latitude_de_nascimento" value="<?php echo esc_attr( get_the_author_meta( 'latitude_de_nascimento', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe a latitude de nascimento</span>
			</td>
		</tr>
		<tr style="display:none">
			<th><label for="longitude_de_nascimento">Longitude de nascimento</label></th>
			<td>
				<input type="text" class="longitudeNascimento" name="longitude_de_nascimento" id="longitude_de_nascimento" value="<?php echo esc_attr( get_the_author_meta( 'longitude_de_nascimento', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe a latitude de nascimento</span>
			</td>
		</tr>
		<tr style="display:none">
			<th><label for="latitude_de_residencia">Latitude de residência</label></th>
			<td>
				<input type="text" class="latitudeResidencia" name="latitude_de_residencia" id="latitude_de_residencia" value="<?php echo esc_attr( get_the_author_meta( 'latitude_de_residencia', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe a latitude de nascimento</span>
			</td>
		</tr>
		<tr style="display:none">
			<th><label for="longitude_de_residencia">Longitude de residência</label></th>
			<td>
				<input type="text" class="longitudeResidencia"  name="longitude_de_residencia" id="longitude_de_residencia" value="<?php echo esc_attr( get_the_author_meta( 'longitude_de_residencia', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Por favor informe a latitude de nascimento</span>
			</td>
		</tr>
		<? if(current_user_can('upload_files')) 
		{?>
		<tr>
			<th><label for="fam_upload">Foto</label></th>
			<td>
				<div class="fam_upload">
					<input type="hidden" class="upload_single" value="true"   />
					<input type="hidden" class="upload_mandatory" value="false"   />
					<input type="hidden" class="disable_youtube_video" value="true"   />					
					<input type="hidden"  class="postId" value="<?if ($post->ID != null){echo $post->ID;}?>" />	
					<? PrintUserImage($user->ID ); ?>			
				</div>
				
			</td>
		</tr>
		<?}
		else
		{	
			?><tr>
				<th><label for="fam_upload">Foto</label></th>
				<td>
				<?
					fam_plupload_loader(false);
					GetFamUploaderHtml(false,$user->ID, true);
				?>
				</td>
			</tr>
			<?
		}
			
		?>
	</table>
	<?php
 }

add_action( 'edit_user_profile', 'fam_show_extra_profile_fields' );
add_action( 'show_user_profile', 'fam_show_extra_profile_fields' );
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );


add_filter('manage_users_columns', 'viajantes_add_thumbnail_column'); 
// Add the column thumb image
function viajantes_add_thumbnail_column($columns){
	$columns['viajante_thumb'] = __('Imagem de viajante');
	return $columns;
}

 
add_action ('manage_users_custom_column', 'viajantes_display_thumbnail_column', 10, 3); 
function viajantes_display_thumbnail_column($c = '',$column_name , $user_id) { 	
    if ($column_name == 'viajante_thumb') { 		
		$imgId = get_the_author_meta("_fam_upload_id_", $user_id);		
		$site_id = get_the_author_meta("_fam_upload_site_id", $user_id);
		if($site_id > 0 && switch_to_blog($site_id))
		{		
			if($imgId != null && $imgId > 0)
			{	
				try
				{		
					require_once( ABSPATH . '/FAMCore/BO/Imagem.php' );					
					$img = Imagem::GetImage($imgId);				
					$output .= "<img width='120px' src='".$img->ImageThumbSrc. "' />";								
				}
				catch(exception $ex){
					delete_user_meta($userId, '_fam_upload_id_');
					delete_user_meta($userId, '_fam_upload_site_id');				
				}
			}		
		}	
		restore_current_blog();  
    }    
    return $output;  
}