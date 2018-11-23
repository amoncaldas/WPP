<?php

/**
 * @package FAM Mail From
 * @author Amon Caldas
 * @version 0.1
 */
 
/*
Plugin Name: FAM MU Related posts
Plugin URI: http://fazendoasmalas.com/
Description: Add multisite related post feature.
Version: 0.0.1
Author: Amon Caldas
Author URI: http://fazendoasmalas.com/
Last Change: 13.12.2014 08:41:06
*/	

class fam_mu_related_post {
	function __construct() {		
		add_action('admin_init', array( $this, 'add_related_post_form' )); 
		add_action('save_post', array( $this, 'save_related_posts') );
		add_action('delete_post', array( $this, 'delete_related_posts'));
		add_filter( 'wp_link_query_args',  array( $this,'fam_mu_wp_link_query_args'));
		add_action('admin_enqueue_scripts',  array( $this,'fam_mu_related_add_js'), 0); 
		add_action( 'get_related_posts', array( $this, 'fam_mu_get_related_posts'),1,1);
		
		//add_filter('posts_fields',array( $this,'fam_mu_posts_fields'));
		//add_filter('posts_join',array( $this,'fam_mu_posts_join'));
		//add_filter( 'posts_request', array( $this,'dump_request' ));
		//add_filter('posts_where','fam_mu_posts_where');
		
	}

	function add_related_post_form() 
	{	
		foreach($this->get_fam_mu_active_posttypes() as $type)
		{
			add_meta_box('custom-metabox-related_posts', __('Posts relacionados'),array( $this, 'fam_mu_related_post_form'), $type, 'normal', 'low');			 
		}		
	}

	function save_related_posts($post_ID)
	{
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		{
			return $post_ID;
		}
		if($this->fam_mu_active_in_current_post_type())
		{			
			$relatedNamePrefix = "fam_mu_related_post_";
			if(is_array($_POST) && count($_POST) > 0)
			{
				$ids= "";		
				foreach($_POST as $key=>$value)
				{
					if(strpos($key,$relatedNamePrefix) > -1)
					{
						$siteAndPostId = $value;	
						if($ids != "")
						{
							$ids.= ";".$value;
						}
						else
						{
							$ids.= $value;
						}	
					}
				}
			}
			update_post_meta($post_ID, 'related_posts_ids', $ids);				
		}		
	}

	function delete_related_posts($post_id) {
		if($this->fam_mu_active_in_current_post_type())
		{				
			delete_post_meta($post_id, 'related_posts_ids');			   
		}
	}	

	function get_sites() {
		global $wpdb;				
		$excluded_id = get_current_blog_id();	
		$sites = $wpdb->get_results( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' AND blog_id <> $excluded_id ORDER BY registered DESC"), ARRAY_A );
		return $sites;
	}
	
	function fam_mu_related_post_form()
	{		
		?>		
		<table class="form-table">
			<tr>
				<th><label for="related_posts_site_select">Site</label></th>
				<td>
					<select id="related_posts_site_select">
						<?
							if(get_current_blog_id() > 1)
							{
								echo "<option value='1|/wp-admin/admin-ajax.php'>Fazendo as malas</option>";
							}
							$subsiteurl = str_replace(network_home_url(),"/",get_bloginfo('url'))."/";
							echo "<option selected value='".get_current_blog_id()."|".$subsiteurl."/wp-admin/admin-ajax.php'>".get_bloginfo('name')."</option>";							
							
							$sites = $this->get_sites();
							foreach ( $sites as $site ) {
								switch_to_blog( $site->blog_id );
								$subsiteurl = str_replace(network_home_url(),"/",get_bloginfo('url'))."/";								
								echo "<option value='".get_current_blog_id()."|".$subsiteurl."/wp-admin/admin-ajax.php'>".get_bloginfo('name')."</option>";
								restore_current_blog();
							}							
						?>
					</select>
					<span class="description">Selecione os site</span>
					<textarea style="display:none" class="dummy_text_area" id="dummy_text_area"></textarea>
				</td>
			</tr>
			<tr>
				<th><label for="post_search">Selecionar Posts</label></th>
				<td>
					<a class="button" id="post_search_open_btn" onclick="" >selecionar</a>					
					<ul class="selected_related_posts">	
						<?
							global $post;							
							$related_posts_ids = get_post_meta($post->ID, "related_posts_ids", true);							
							if($related_posts_ids != null && strlen($related_posts_ids) > 0)
							{
								foreach(explode(";", $related_posts_ids) as $related_post_id)
								{
									$post_id_data = explode('-',$related_post_id);	
									switch_to_blog( $post_id_data[0] );								
									$postRelated = get_post($post_id_data[1]);	
									restore_current_blog();
									echo								
									'<li class="alternate">
										<input type="hidden" name="fam_mu_related_post_'.$related_post_id.'" class="item-permalink" value="'.$related_post_id.'">
										<span class="item-title">'.$postRelated->post_title.' ('.$postRelated->post_type.')'.'</span>
										<span title="remover" onclick="javascript:$(this).parent().remove();" style="color: red;font-size: 18px;float:right;cursor:pointer" class="remove_related_post">x</span>
									</li>';
								}
							}	
						?>						
					</ul>					
				</td>				
			</tr>
			<tr style="display:none;">				
				<td>
					<? 
						if(!post_type_supports( get_current_post_type(), 'editor' ))
						{
							wp_editor( $content, 'fam_mu-second-editor', array( 'media_buttons' => false ));
						}
					?>				
				</td>				
			</tr>				
		</table>	
		<?
			
		
	}
	
	function fam_mu_wp_link_query_args( $query ) {
		$query['post_type'] = $this->get_fam_mu_active_posttypes();
		return $query;
	}	

	function fam_mu_related_add_js() {	
		if($this->fam_mu_active_in_current_post_type())
		{					
			$location = plugins_url('js/fam_mu_related_js.js', __FILE__ );		
			wp_register_script('fam_mu_related_js',$location, array('jquery', 'jquery-ui-core'), '0.0.1', true);
			wp_enqueue_script('fam_mu_related_js');
		}
	}
	
	function fam_mu_active_in_current_post_type()
	{
		return in_array(get_current_post_type(), $this->get_fam_mu_active_posttypes());			
	}
	
	function get_fam_mu_active_posttypes()
	{
		return array("albuns","relatos","blog_post");
	}	
	
	function fam_mu_posts_fields ($fields) {
		if($this->fam_mu_active_in_current_post_type())
		{			
			$fam_mu_fields = ', fam_related_meta.meta_value fam_related_value';			
			$fields .= $fam_mu_fields;
			return $fields;
		}
	}	
	function fam_mu_posts_join ($join) {
		if($this->fam_mu_active_in_current_post_type())
		{		
			global	$wpdb;
			$fam_mu_join = " left JOIN $wpdb->postmeta fam_related_meta ON ($wpdb->posts.ID = fam_related_meta.post_id AND fam_related_meta.meta_key = 'related_posts_ids')";
			$join .= $fam_mu_join;
			return $join;
		}
	}
	function fam_mu_posts_where ($where) {
		if($this->fam_mu_active_in_current_post_type())
		{			
			$where .= "my_custom_where";
			return $where;
		}
	}	
	function dump_request( $input ) {
		if($this->fam_mu_active_in_current_post_type())
		{
			var_dump($input);

			return $input;
		}
	}	
}
	
new fam_mu_related_post();
