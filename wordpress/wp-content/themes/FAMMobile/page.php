<?php	
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global $wp_query;
$page = $wp_query->post;
Conteudo::SetMetas("page",$page->ID, $page->post_title,$page->post_content);
get_header(); ?>		
	<div id="content">					
		<div class="page_template">				
			<h2><? echo $page->post_title; ?></h2>
			<?	echo $page->post_content;?>
		</div>									
	</div><!-- #content -->
<?php include('footer/footer-default.php') ?>

