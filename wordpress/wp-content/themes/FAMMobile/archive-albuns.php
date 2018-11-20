<?php
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global 	$wp_query;
$archive_posts = $wp_query->posts;
Conteudo::SetMetas("archive-albuns");
	
include('header.php') ?>
	<div id="content">						
		<?php widget::Get("galeria", array('show_more' => 'yes','itens'=> 16, 'default_archive'=>$archive_posts)) ?>				
		<div class="clear"></div>				
	</div><!-- end content -->	
<?php include('footer/footer-default.php') ?>