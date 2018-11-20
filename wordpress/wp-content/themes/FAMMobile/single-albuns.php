<?php
require_once(ABSPATH."/FAMCore/BO/Galeria.php");
$galeria = new Galeria();
$id = $wp_query->post->ID;
$album = $galeria->GetAlbumById($id, 1);
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");

Conteudo::SetMetas("single-albuns",$album);

include('header.php') ?>
				
		<div id="content">									
			<div class="label_single_content albumHeader">Album de viagem</div>
			<h1 class="single_content_title single_album_title"><? echo $album->Titulo; ?></h1>					
			<div class="album-details">
				<h4><span class="date"><?php echo date("d M Y", strtotime($album->DataPublicacao))  ?></span></h4>
				<h4><span class="location_ico_small"></span><span><?php if($album->Location->Local != null) {echo $album->Location->Local;}else {echo "não especificado";} ?></span></h4>
				<? if($album->Resumo != null) 
				{
					?><p><span class="album-description"></span><? echo $album->Resumo; ?></p><?
				}
				?>												
			</div>	
			<div class="share_album">
				<? widget::Get("share",array('hideCommentBox'=>true,'send'=>true));?>
			</div>				
			<div class="album-location">
				<? widget::Get("content-location", array("location" =>  $album->Location, "locationImage" => null,'enable_controls'=>'false','image_map'=>'yes'));?>
			</div>	
			
			<ul class="galeriafoto medias-album">
				<?php widget::Get("fotos", array('foto_size' => "gallery", 'show_more' => 'yes','parentId'=>$album->AlbumId, 'itens'=>20, 'return' => 'onlyitens')) ?>					
			</ul>
			<div>	
				<? widget::Get("share",array('hideCommentBox'=>false,'send'=>true));?>	
				<? widget::Get("posts_relacionados", array("post_id" => $album->AlbumId, "title"=>'Posts relacionados')); ?>
			</div>		
			<div class="clear"></div>		
		</div><!-- end content -->	
<?php include('footer/footer-default.php') ?>

