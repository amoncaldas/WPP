<?php
require_once(ABSPATH."/FAMCore/BO/Galeria.php");
$galeria = new Galeria();
$id = $wp_query->post->ID;
$album = $galeria->GetAlbumById($id, 21);

	
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");

Conteudo::SetMetas("single-albuns",$album);


get_header(); ?>
		<div id="content-container">		
			<div id="content">		
				<section id="bloco-conteudo-central">				
					<div class="label_single_content albumHeader">Álbum de viagem</div>
										
					<div class="album-details">
						<h1 class="single_content_title"><? echo $album->Titulo; ?></h1>						
						<? if($album->Resumo != null) 
							{
								?><p><span class="album-description"></span><? echo $album->Resumo; ?></p><?
							}
							widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true,'send'=>'true')); 
						if ($album->Location->Local != null){?>
		<div class="album-location">
								<? widget::Get("content-location", array("location" =>  $album->Location, "locationImage" => null,'enable_controls'=>'false','image_map'=>'yes'));?>
							</div>
						<?}						
												
							$data = strtotime(str_replace('/', '-', $album->DataPublicacao));
							$data = utf8_encode(strftime("%d %b %Y", $data));
						?>
						<? if ($album->Location->Local != null){?>
						<div class="meta_container place">
							<div class="content_meta">
								<span class="meta_place">
									<span class="meta_icon">i</span>
									<span class="meta_text">
										<a href="javascript:void(0);"><? echo $album->Location->Local; ?></a>
									</span>
								</span>								
								<span class="meta_date">
									<span class="meta_separator">s</span>
									<span class="meta_text">
										<time datetime="<? echo $album->DataPublicacao;?>"><? echo $data;?></time>
									</span>
								</span>								
							</div>
						</div>
						
						<?}?>			
						
						<ul class="galeriafoto medias-album">
						<?php widget::Get("fotos", array('foto_size' => "gallery", 'show_more' => 'yes','parentId'=>$album->AlbumId, 'itens'=>20, 'return' => 'onlyitens','medias'=>$album->MediasAlbum)) ?>					
						</ul>
						<?php widget::Get("share", array("comment_box_width" => '590','send'=>'true')); ?>	
						<? widget::Get("posts_relacionados", array("post_id" => $album->AlbumId, "title"=>'Posts relacionados')); ?>													
					</div>					
					
					<aside id="coluna-lateral-direita" class="album-clumn">	
						<? widget::Get("add_box_top_right", array('width'=>'300','margin_right'=>'-5px','float'=>'right','margin_top'=>"-10px",'label'=>'single_albuns','place'=>$album->Location->GetLocalSubString(25)));  ?>						
						<?php widget::Get("ultimos-relatos", array('float' => "left", 'margin_right' => "25px", 'width'=>'300px','show_more' => 'yes','content_lenght'=> 100)) ?>												
						<?php widget::Get("galeria", array('title'=>'Outros albuns','show_more' => 'yes','itens'=> 3, "excluded_ids"=>(get_current_blog_id() == 1)? $album->AlbumId."1":$album->AlbumId)) ?>							
					</aside>
					<? widget::Get('footer_adds', array('label'=>'single-albuns')); ?>	
				</div>
				<div class="clear"></div>
				
					
				</div><!-- end page -->
			</div><!-- end content -->
			<footer id="contentBottom">
				<div id="bottom-boxes-container">
					<?php widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id()))?>
					<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes','show_more'=>'no','width'=>'400px','float'=>'right','title'=>'Outras viagens', 'current_viagemId'=>get_current_blog_id()));?>	
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</footer><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>

