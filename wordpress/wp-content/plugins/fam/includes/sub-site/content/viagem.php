<?php

require_once( FAM_PLUGIN_PATH . '/includes/FAMCore/BO/Trajeto.php' );

function viagem_register() {
 
	$labels = array(
		'name' => _x('Viagem', 'post type general name'),
		'menu_name'=>_x('A viagem', 'post display name'),
		'singular_name' => _x('viagem', 'post type singular name'),
		'add_new' => _x('Adicionar nova', 'portfolio item'),
		'add_new_item' => __('Adicionar nova'),
		'edit_item' => __('Editar viagem'),
		'new_item' => __('Nova viagem'),
		'view_item' => __('Visualizar viagem'),
		'search_items' => __('Buscar viagem'),
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
		'menu_icon' =>  '/wp-content/themes/images/icons/16px/icon-viagem.png',
		'rewrite' => true,
		'capability_type' => 'viagem',
		'capabilities' => array(
				'publish_posts' => 'publish_viagem',
				'edit_posts' => 'edit_viagem',
				'edit_others_posts' => 'edit_others_viagem',
				'delete_posts' => 'delete_viagem',
				'delete_others_posts' => 'delete_others_viagem',
				'read_private_posts' => 'read_private_viagem',
				'edit_post' => 'edit_viagem',
				'delete_post' => 'delete_viagem',
				'read_post' => 'read_viagem',
			),
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'editor'),
		'has_archive' => true,
		); 
 
	register_post_type( 'viagem' , $args );
}

add_action('init', 'viagem_register');

function viagem_form()
{
	?>	
		<link rel="stylesheet" href="<?=FAM_SUB_SITE_PLUGIN_PATH?>/resources/css/roteiro.css" />  	
		<div id="locationmap">
		</div>	
	<?	
		
	$trajetosBO= new Trajeto();
	$trajetosVO =  $trajetosBO->GetTrajetos(get_current_blog_id());	
	$temTrajetos = false;	
	?>
	
	<div id="travelpoints">
		<ul>
			<?
			if(is_array($trajetosVO) &&count($trajetosVO) > 0)
			{
				foreach($trajetosVO as $trajetoVO)
				{
					$temTrajetos = true;
					$value = $trajetoVO->LocationPartida->Latitude.",".$trajetoVO->LocationPartida->Longitude."|".$trajetoVO->LocationChegada->Latitude.",".$trajetoVO->LocationChegada->Longitude;
					$valueDisplay = $trajetoVO->LocationPartida->LocalComPais." | ".$trajetoVO->data_de_partida." ->".$trajetoVO->LocationChegada->LocalComPais." | ".$trajetoVO->data_de_chegada." | ".$trajetoVO->transporte."";
					echo  '<li><div class="trajetoDisplay">'.$valueDisplay.'</div><div class="trajetoValue">'.$value.'</div><div onclick="deleteTrajeto(this)" class="deleteTrajeto">x</div></li>';								  
				}
			}	
		?>
		</ul>
		<? if(!$temTrajetos)
		{
			echo "<h2>Nenhum trajeto incluído ainda</h2>";
		}		
		?>
		
	</div>
	<select name="pods_meta_trajeto[]" data-name-clean="pods-meta-trajeto" id="pods-form-ui-pods-meta-trajeto" class="pods-form-ui-field-type-pick pods-form-ui-field-name-pods-meta-trajeto destinosCoordinates pods-validate pods-validate-required" tabindex="2" multiple="multiple">
		<?
			foreach($trajetosVO as $trajetoVO)
			{
				$value = $trajetoVO->LocationPartida->Latitude.",".$trajetoVO->LocationPartida->Longitude."|".$trajetoVO->LocationChegada->Latitude.",".$trajetoVO->LocationChegada->Longitude."|".$trajetoVO->LocationPartida->LocalComPais."|".$trajetoVO->data_de_partida."|".$trajetoVO->LocationChegada->LocalComPais."|".$trajetoVO->data_de_chegada."|".$trajetoVO->transporte;
				echo '<option selected="selected" value="'.$value.'">'.$value.'</option>';				  
			}	
		?>
	</select>
	<div id="addTrajetoDiv">	
		<a title="Adicionar Trajeto"  id="adicionarTrajeto">Adicionar trajeto</a>
	</div>
	<span style="display:none;" id="vid"><? echo get_current_blog_id();?></span>
	<?php	
}

function add_custom_viagem_form() 
{
	add_meta_box('custom-metabox', __('Roteiro'), 'viagem_form', 'viagem', 'normal', 'high');	
}

add_action('admin_init', 'add_custom_viagem_form');


function save_viagem_trajetos()
{	
	if ('viagem' == $_POST['post_type'] ) 
    {		
		if(is_array($_POST['pods_meta_trajeto']) && count($_POST['pods_meta_trajeto']) > 0)
		{			
			$trajetoBO = new Trajeto();			
			$trajetoBO->SaveTrajetos($_POST['pods_meta_trajeto'], get_current_blog_id());			
		}
	}
}

add_action( 'save_post', 'save_viagem_trajetos' );



function post_limit_viagem()
{
	global $pagenow;
	$limit = 1;
	if($pagenow =='post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'viagem') //substr($_SERVER['PHP_SELF'],-12) !== 'post-new.php'
	{	
		$posts = get_posts(array('numberposts' => $limit, 'post_status' => 'publish','post_type'=>'viagem'));
		$nrPosts = count($posts);

		if(($nrPosts>= $limit))
		{
			 wp_die( __('<div style="color:red;font-size:20px">Aviso de limite de cadastro de viagem</div>
				<div style="font-weight:bold">Só é permitido o cadastro de '.$limit.' detalhamento da viagem e você já atigiu esse limite. Retorne para o 
				<a href="index.php">painel</a></div>') );
		 }		
	}
}

add_action('init', 'post_limit_viagem');


add_filter('get_sample_permalink_html', 'viagem_permalink_btns', '', 4);
function viagem_permalink_btns($return, $id, $new_title, $new_slug){
    global $post;
    if($post->post_type == 'viagem')
    {
        $return = preg_replace('/<span id="edit-slug-buttons">.*<\/span>/i', '', $return);
    }
    return $return;
}

add_filter('post_type_link', 'viagem_permalink', 1, 3);
function viagem_permalink($post_link, $id = 0, $leavename) {
	global $post;
	if (is_wp_error($post))
	{
		return $post;
	}	
	
	if($post->post_type == "viagem")
	{			
		$post_link = get_bloginfo("url")."/viagem";
	}	
	return $post_link;		
}