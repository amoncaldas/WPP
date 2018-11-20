<?php	
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("archive-videos",null,"Vídeos de viagens e aventuras pelo mundo - Fazendo as Malas","Vídeos de viagens e aventuras");

include('header.php') ?>	
<div id="content">									
	<div class="label_single_content albumHeader">Vídeos</div>
	<h2 class="single_content_title single_album_title">Vídeos de viagens e aventuras ao pelo mundo de todas as viagens Fazendo as Malas</h2>					
	
	<div class="share_album">
		<? widget::Get("share",array('hideCommentBox'=>true,'send'=>true));?>
	</div>
			
	<ul class="galeriafoto medias-album">
		<?php widget::Get("fotos", array('foto_size' => "gallery", 'orderby'=> "rand",'show_more' => 'yes','itens'=>20, 'return' => 'onlyitens', 'destaques_video'=> "yes")) ?>
	</ul>
	<div>	
		<? widget::Get("share",array('hideCommentBox'=>false,'send'=>true));?>	
	</div>		
	<div class="clear"></div>		
</div><!-- end content -->
<?php include('footer/footer-default.php') ?>	

