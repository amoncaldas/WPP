<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("archive-blog");


include('header.php');?>
	<div id="content">						
		<?php widget::Get("blog_posts", array('show_large_image'=>'yes','show_meta_data_label'=>'yes','itens'=> 8,'show_share'=>'yes','content_lenght'=> 600,'width'=>'100%','show_more'=> 'yes','page'=>$_GET["page"])); ?>	
		<?php //widget::Get("blog_sidebar", array('itens'=> 2,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>					
		<div class="clear"></div>		
	</div><!-- end content-container -->

<?php include('footer/footer-default.php') ?>



