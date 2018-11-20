<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global 	$wp_query;
$archive_posts = $wp_query->posts;
Conteudo::SetMetas("archive-relatos");

include('header.php');?>				
		<div id="content" class="relatosInterna">					
			<? widget::Get("ultimos-relatos", array('show_meta_data_label'=>'yes','show_large_image'=>'yes','itens'=> 8,'show_share'=>'yes','default_archive'=>$archive_posts,'content_lenght'=> 350,'width'=>'100%', 'show_more'=> 'yes','show_location'=> true,'page'=>$_GET["page"]));?>					
			<div class="clear"></div>				
		</div><!-- end content -->
<?php include('footer/footer-default.php') ?>



