<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global 	$wp_query;
$archive_posts = $wp_query->posts;
Conteudo::SetMetas("archive-atualizacao");

include('header.php');
?>					
	<div id="content">					
		<?widget::Get("atualizacoes", array('itens'=> 8,'title'=>'Status','show_comment'=>'yes','show_read_full'=>'yes','show_map'=>'no','width'=>'100%','foto_width'=>'70px', 'show_more'=> 'yes','show_location'=> true,'default_archive'=>$archive_posts,'page'=>$_GET["page"]));?>								
		<div class="clear"></div>		
	</div><!-- end content-container -->

<?php include('footer/footer-default.php') ?>



