<?php
	
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");

	Conteudo::SetMetas("archive-videos",null,"Vídeos de viagens e aventuras pelo mundo - Fazendo as Malas","Vídeos de viagens e aventuras");

get_header(); ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">				
					<div class="label_single_content albumHeader">Vídeos</div>										
					<div class="album-details">
						<h2 class="single_content_title">Vídeos de viagens e aventuras ao pelo mundo de todas as viagens Fazendo as Malas</h2>						
						<? widget::Get("share", array('hideShareBtns' => false,'hideCommentBox'=>true,'send'=>'true'));?>						
						<ul class="galeriafoto medias-album">
						<?php widget::Get("fotos", array('foto_size' => "gallery", 'orderby'=> "rand",'show_more' => 'yes','itens'=>50, 'return' => 'onlyitens', 'destaques_video'=> "yes")) ?>					
						</ul>
						<?php widget::Get("share", array("comment_box_width" => '590','send'=>'true')); ?>														
					</div>					
					
					<aside id="coluna-lateral-direita" class="album-clumn">	
						<? widget::Get("add_box_top_right", array('width'=>'300','margin_right'=>'-5px','float'=>'right','margin_top'=>"-10px",'label'=>'single_albuns'));  ?>						
						<? widget::Get("viagens", array('list_type'=>'box','itens'=>3,'show_more'=>'yes','width'=>'100%','float'=>'right'));?>	
						<?php widget::Get("ultimos-relatos", array('float' => "left", 'margin_right' => "25px", 'width'=>'300px','show_more' => 'yes','content_lenght'=> 100)) ?>						
						<?php widget::Get("galeria", array('title'=>'Albuns de viagens','show_more' => 'yes','itens'=> 3)) ?>							
					</aside>
					<? widget::Get('footer_adds', array('label'=>'videos')); ?>	
				</div>
				<div class="clear"></div>
				
					
				</div><!-- end page -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<? widget::Get("twitter-box"); ?>							
					<? widget::Get("facebook-box"); ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>

