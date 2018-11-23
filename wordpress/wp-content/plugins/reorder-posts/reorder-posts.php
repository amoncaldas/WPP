<?
/**
 * @package FAM Reorder posts
 * @author Amon Caldas
 * @version 0.1
 */
 
/*
Plugin Name: FAM Reorder posts
Plugin URI: http://fazendoasmalas.com/
Description: Add multisite related post feature.
Version: 0.0.1
Author: Amon Caldas
Author URI: http://fazendoasmalas.com/
Last Change: 12.05.2015 08:41:06
*/
add_action('admin_menu', 'create_reorder_pages');
function create_reorder_pages() {  
	if(!$screen->is_network)
	{
		add_submenu_page( 'edit.php?post_type=destaque', 'Reordenar destaques', 'Reordenar', 'edit_destaque', 'reorder_destaque', 'destaque_reorder_page' );
		add_submenu_page( 'edit.php?post_type=relatos', 'Reordenar relatos', 'Reordenar', 'edit_relatos', 'reorder_relatos', 'relatos_reorder_page' );
	}
}

function destaque_reorder_page()
{
	require_once(ABSPATH."/FAMCore/BO/Destaque.php");
	$destaque = new Destaque();
	$destaques = $destaque->GetDestaques(5);
	?>
	<div id="theme-options-wrap">
		<div class="icon32" id="icon-destaque" style="background:url(/wp-content/themes/images/icons/icon-destaque.png)"> 
			<br /> 
		</div>
		<h2>Reordene os destaques do site</h2>
		<p>Os destaques são exibidos na página principal e o destaque na posição 1 é usado como imagem principal do site</p>
		<div class="reorderposts">
			<span id="status_process"></span>
			<ul id="itens_destaque" >		
				<?php			
				if(is_array($destaques) && count($destaques) > 0)	
				{
					foreach($destaques as $destaque)			
					{ 		
						if(CheckUploadedFileExists($destaque->ImageCroppedSrc))
						{						
							?><li><a class="fancybox" href="<? echo $destaque->OriginalImageVO->ImageLargeSrc; ?>"><img src="<? echo $destaque->OriginalImageVO->ImageThumbSrc; ?>" alt="<? echo $destaque->DestaqueId; ?>" /></a><span><? echo $destaque->Descricao; ?></span><input type="hidden" class="itenId" value="<? echo $destaque->DestaqueId; ?>"/></li><?						
						}
					}
				}			
				?> 	      
			</ul>
		</div>
	</div>
	<style>
		.reorderposts
		{
			margin-top: 20px;
			float: left;
			width: 100%;
		}
		.reorderposts ul li
		{
			border-bottom: 1px solid #dfdfdf;
			background-color: #fcfcfc;
			border-top: 1px solid #dfdfdf;
			margin-bottom:10px;
			height:70px;
			cursor:move;
		}
		.reorderposts ul li a
		{
			margin-right:10px;
		}
		.reorderposts ul li span
		{
			bottom: 35px;
			position: relative;	
			font-size:18px;		
		}
	</style>
	
	<script>
	 jQuery(document).ready(function () {
		ReorderDestaques(<? echo get_current_blog_id();?>);
	});
	</script>
	<?
}

function relatos_reorder_page()
{
	require_once(ABSPATH."/FAMCore/BO/Relato.php");
	$relatoBO = new Relato();
	$relatos = $relatoBO->GetRelatos(array('itens'=>100));
	?>
	<div id="theme-options-wrap">
		<div class="icon32" id="icon-destaque" style="background:url(/wp-content/themes/images/icons/icon-relatos.png);height:42px"> 
			<br /> 
		</div>
		<h2>Reordene os relatos do site</h2>
		<p>Arraste e solte para reordenar. Os maiS recentes são exibidos no topo</p>		
		<div class="reorderposts">
			<span id="status_process"></span>
			<ul id="itens_relato" >		
				<?php			
				if(is_array($relatos) && count($relatos) > 0)	
				{
					foreach($relatos as $relato)			
					{ 		
												
						?><li><a class="fancybox" href="<? echo $relato->MidiaPrincipal->ImageLargeSrc; ?>"><img src="<? echo $relato->MidiaPrincipal->ImageThumbSrc; ?>" alt="<? echo $relato->RelatoId; ?>" /></a><span><? echo $relato->Titulo; ?></span><input type="hidden" class="itenId" value="<? echo $relato->RelatoId; ?>"/></li><?						
						
					}
				}			
				?> 	      
			</ul>
		</div>
	</div>
	<style>
		.reorderposts
		{
			margin-top: 20px;
			float: left;
			width: 100%;
		}
		.reorderposts ul li
		{
			border-bottom: 1px solid #dfdfdf;
			background-color: #fcfcfc;
			border-top: 1px solid #dfdfdf;
			margin-bottom:10px;
			height:70px;
			cursor:move;
		}
		.reorderposts ul li a
		{
			margin-right:10px;
		}
		.reorderposts ul li span
		{
			bottom: 35px;
			position: relative;	
			font-size:18px;		
		}
	</style>
	
	<script>
	 jQuery(document).ready(function () {
		ReorderDestaques(<? echo get_current_blog_id();?>);
	});
	</script>
	<?
}

add_filter('pre_get_posts', 'posts_reorded_filter');
function posts_reorded_filter($query) { 
	
  if($query->get('post_type') == 'destaque') {       
    $query->set('orderby', 'menu_order');
    $query->set('order', 'ASC');        
  }
  if($query->get('post_type') == 'relatos') {       
    //$query->set('orderby', 'menu_order');
    //$query->set('order', 'ASC');        
  }
  return $query;
}