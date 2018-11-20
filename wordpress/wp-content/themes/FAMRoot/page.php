<?php	
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global $wp_query;
$page = $wp_query->post;
	Conteudo::SetMetas("page",$page->ID, $page->post_title,$page->post_content);
	get_header(); ?>
		<div id="content-container">
			<div id="content">
				<div id="bloco-conteudo-central">	
					<div class="page_template">				
						<h1><? echo $page->post_title; ?></h1>
						<?	echo $page->post_content;?>
					</div>
					<? widget::Get('footer_adds', array('label'=>'page')); ?>						
				</div>						
			</div><!-- #content -->
			<div id="contentBottom">
					<div id="bottom-boxes-container">
						<?php widget::Get("ultimos-relatos") ?>
						<?php widget::Get("blog_posts", array('itens'=> 2,'content_lenght'=> 75,'width'=>'350px','show_more'=> 'no', 'float'=>'right','margin_right'=>'20px;')); ?>
					</div>
					<div class="clear"></div>
					<? widget::Get("socialmedia");?>
					<?php widget::Get("codigocriativo")?>
				</div><!-- end content-bottom -->
		</div>
	</div><!-- end geral -->

<?php include('footer-wp.php') ?>

