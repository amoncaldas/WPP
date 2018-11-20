<?php
/*
Plugin Name: FAM new blog settings plugin
Version: 0.1
Plugin URI: http://fazendoasmalas.com
Author: Amon Caldas
Author URI: http://fazendoasmalas.com
Description: FAM new blog settings plugin
*/
function new_blogs_setting($blog_id)  {
	//set your options here:	
	create_viagem_post($blog_id);
	create_viajantes_page($blog_id);
	setMediaSizes($blog_id);
	UpdateOptions($blog_id);
	RemoveOldDynamicScripts();
	//register_activation_hook();
	update_option('gmt_offset', 2);
	update_fam_site($blog_id);
	// stop editing here
    return;
}
add_action('wpmu_new_blog', 'new_blogs_setting');


add_action('wpmu_blog_updated', 'update_fam_site');
function update_fam_site($blog_id)  {
	$blog_details = get_blog_details($blog_id);		
	$sql = "update wp_fam_blogs set blog_name = '".$blog_details->blogname."' where blog_id = '".$blog_id."'";
	global $wpdb;
	$wpdb->query($sql);	
    return;
}


function create_viagem_post($blog_id) {   
    $the_page_title = 'A Viagem';
    $the_page = get_page_by_title( $the_page_title );    

    // Create post object
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_content'] = "Descreva aqui a viagem";
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'viagem';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
		
	switch_to_blog($blog_id); 			
    // Insert the post into the database
    $the_page_id = wp_insert_post( $_p );
	restore_current_blog();
      
}

function create_viajantes_page($blog_id) {    
    $the_page_title = 'Viajantes';	
    $the_page = get_page_by_title( $the_page_title);  

    // Create post object
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_content'] = "vazio";
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
		
	switch_to_blog($blog_id); 			
    // Insert the post into the database
    $the_page_id = wp_insert_post( $_p );
	restore_current_blog();    
}


function setMediaSizes($blog_id)
{
	switch_to_blog($blog_id);
	//Check and Set the Default Thumbnail Sizes
	if(get_option('thumbnail_size_w')!=120)update_option('thumbnail_size_w',120);
	if(get_option('thumbnail_size_h')!=70)update_option('thumbnail_size_h',70);
	if(get_option('medium_size_w')!=300)update_option('medium_size_w',300);
	if(get_option('medium_size_h')!=300)update_option('medium_size_h',300);
	if(get_option('large_size_w')!=1024)update_option('large_size_w',1024);
	if(get_option('large_size_h')!=1024)update_option('large_size_h',1024);
	restore_current_blog();
}

/*this will force the javascript regeneration in the first request*/
function RemoveOldDynamicScripts(){
	$file = "../themes/FAMRoot/js/viagens.js";	
	try{
		unlink($file);
	}
	catch(Exception $ex){}	
}

function UpdateOptions($blog_id)
{
	switch_to_blog($blog_id);
	update_option("mailserver_url", "mail.fazendoasmalas.com");
	update_option("mailserver_login", "contato@fazendoasmalas.com");
	update_option("mailserver_pass", "APQ292-FAM");
	update_option("mailserver_port", "26");
	update_option("facebook_og_action", "1");
	
	$facebook_options = array('app_id'=>'585138868164342', 'app_secret'=>'01df854847ff9f6cca9ea076b1fe0ade','app_namespace'=>'sigafazendoasmalas','access_token'=>'585138868164342|9Luxc3zO1RXMJR20BqjGB2W022o');
	update_option("facebook_application", $facebook_options);
	
	/*$facebook_publish_options = array('id'=>'604453679565803','name'=> 'Testes Códigocriativo', 'access_token'=>'CAAIULkJhlvYBAC71tUKZAByaTLlCEbzk9ZBIZB1lNYv0G3S4EYPCwQnS9FmuC77gJMpLEa3tZAOt0ZBIymenfHT2WGEbaJmGx3km1uAO3OZAfIjebxu36WERGkfdWzvZBOAe56EHBCdE9EZCF2btZBICkjx1W5VPulg81M8Dgk7bhfAIt01Du8DLv');	
	if(strpos($_SERVER["SERVER_NAME"],"teste.") === false)
	{
		$facebook_publish_options = array('id'=>'222705747761628','name'=> 'Fazendo as Malas - Descobrindo o mundo', 'access_token'=>'CAAIULkJhlvYBABZBXqNbKWgsZBFj4lbRxQIR9AfSIlongBvGNSf412jNroCc7HmYenQPaBz9xkDK8yMlkbAtfdg1b5JMDWTywS932K4zA6ZCmmRKKYnADNPCKyUtSGUjAcsWsmDZCQ4tZAiot0VJgHXnHDbGs00ebovaqlZC6XdwZDZD');
	}	
	update_option("facebook_publish_page", $facebook_publish_options);*/
	restore_current_blog();	
}

?>