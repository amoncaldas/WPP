<?php
/* custom google maps location sugestion*/



function blog_register() {
 
	$labels = array(
		'name' => _x('Blog', 'post type general name'),
		'menu_name'=>_x('Blog', 'post display name'),
		'singular_name' => _x('Post de blog', 'post type singular name'),
		'add_new' => _x('Adicionar post de blog', 'blog item'),
		'add_new_item' => __('Adicionar novo'),
		'edit_item' => __('Editar post de blog'),
		'new_item' => __('Novo post de blog'),
		'view_item' => __('Visualizar post de blog'),
		'search_items' => __('Buscar post de blog'),
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
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-blog.png',
		'rewrite' => array( 'slug' => 'blog' ),
		'capability_type' => 'blog_post',
		'capabilities' => array(
				'publish_posts' => 'publish_blog_post',
				'edit_posts' => 'edit_blog_post',
				'edit_others_posts' => 'edit_others_blog_post',
				'delete_posts' => 'delete_blog_post',
				'delete_others_posts' => 'delete_others_blog_post',
				'read_private_posts' => 'read_private_blog_post',
				'edit_post' => 'edit_blog_post',
				'delete_post' => 'delete_blog_post',
				'read_post' => 'read_blog_post',
			),
		'taxonomies' => array('category'),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','revisions', 'thumbnail','comments'),
		'has_archive' => true,
		); 
 
	register_post_type( 'blog_post' , $args );
}

add_action('init', 'blog_register');


add_action('init', 'blog_post_rewrite');
function blog_post_rewrite() {
	global $wp_rewrite;
	$queryarg = 'post_type=blog_post&p=';
	$wp_rewrite->add_rewrite_tag('%cpt_blog_id%', '([^/]+)', $queryarg);	
	$wp_rewrite->add_permastruct('blog_post', '/blog/%postname%/%cpt_blog_id%/', false);
}



/* dicas*/


function assign_blog_categories(){
	global $wp_taxonomies;
	if(get_current_post_type() == "blog_post")
	{
	  $wp_taxonomies['category']->cap->assign_terms = 'edit_blog_post';
	}
}
add_action('init', 'assign_blog_categories');


function add_custom_blog_form() 
{
	add_meta_box('custom-metabox-dados', __('Dados do post'), 'blog_dados', 'blog_post', 'normal', 'high');	
	add_meta_box('custom-metabox-mapa', __('Mapa do local'), 'location_sugestion', 'blog_post', 'normal', 'high');
	add_meta_box('custom-metabox-seo', __('SEO'), 'seo_box', 'blog_post', 'normal', 'high');
}


add_action('admin_init', 'add_custom_blog_form');

function location_sugestion()
{	
	?>	
	<div id="locationmap" style="height:300px">
	</div>	
	<?
}

function blog_dados()
{	
	global $post;
	?>	
	<table class="form-table">		
		<tr>
			<th><label for="fam_upload">Imagem</label></th>
			<td>
				<div class="fam_upload">
					<input type="hidden" class="upload_single" value="true"   />
					<input type="hidden" class="upload_mandatory" value="false"   />
					<input type="hidden" class="upName" value="Imagem para mídias sociais"   />					
					<input type="hidden"  class="postId" value="<?if ($post->ID != null){echo $post->ID;}?>" />	
					<? PrintImages($post->ID ); ?>			
				</div>
				
			</td>
		</tr>
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
				<input type="hidden" name="my_hidden_blog_post_flag" value="true" />				
			</td>
		</tr>			
	</table>			
	<?
}


function save_blog($post_ID)
{
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	{
		return $post_ID;
	}
	if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
	{
      return $post_ID;
	}
	else if(get_current_post_type() == 'blog_post' && isset($_POST["my_hidden_blog_post_flag"]))
	{		
		SaveImages($post_ID, $_POST);
		update_post_meta($post_ID, 'local', $_POST['local']);
		update_post_meta($post_ID, 'latitude', $_POST['latitude']);
		update_post_meta($post_ID, 'longitude', $_POST['longitude']);	
		
			
		update_post_meta($post_ID, 'seo_desc', $_POST['seo_desc']);
		
		
			
	}
}

function delete_blog($post_id) {
	if(get_current_post_type() == 'blog_post')
	{		
		delete_post_meta($post_id, '_fam_upload_id_');	
		delete_post_meta($post_id, 'local');
		delete_post_meta($post_id, 'latitude');
		delete_post_meta($post_id, 'longitude');
		delete_post_meta($post_id, 'seo_desc');	
	}    
}

add_action( 'save_post', 'save_blog' );
add_action('delete_post', 'delete_blog');
