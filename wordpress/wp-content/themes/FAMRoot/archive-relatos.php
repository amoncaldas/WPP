<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global 	$wp_query;
$archive_posts = $wp_query->posts;
Conteudo::SetMetas("archive-relatos");


get_header();?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">				
				<div class="relatosInterna">
					<h1 class="single_type_label relato_icon topic_label_icon topic_font_color hand_font">Relatos de viagem</h1>
					<ul>
						<? widget::Get("ultimos-relatos", array('show_meta_data_label'=>'yes','itens'=> 8,'show_map'=>'yes','show_share'=>'yes','default_archive'=>$archive_posts,'return'=>'onlyitens','content_lenght'=> 600,'width'=>'100%', 'show_more'=> 'yes','show_location'=> true,'show_large_image'=>'yes','page'=>$_GET["page"]));?>
					</ul>					
				</div>
				<!-- end relatos -->
				
				<aside id="coluna-lateral-direita">
					<?php //widget::Get("a-viagem", array('idViagem'=>get_current_blog_id())); ?>	
					<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"20px",'label'=>'archive_relatos','width'=>'280px','margin_right'=>'-5px'));  ?>	
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'current_viagemId'=>get_current_blog_id(),'show_more'=>'yes','width'=>'93%','float'=>'right','title'=>'Outras viagens'));?>			
					<?php widget::Get("blog_posts", array('itens'=> 3,'show_share'=>'no','content_lenght'=> 100,'width'=>'95%','show_more'=> 'yes','width'=>'95%','float'=>'right','margin_right'=>'-5px')); ?>									
					<?php //widget::Get("fotos", array('show_more' => 'yes','itens'=>12,'orderby'=>'rand')) ?>
					<?php widget::Get("galeria", array('title'=>'Álbuns','show_more' => 'yes','itens'=> 3,'float'=>'right','width'=>'93%')) ?>					
				</aside>	
				<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true)); ?>
				<?php widget::Get("share", array("comment_box_width" => '900')); ?>
					
				<? widget::Get('footer_adds', array('label'=>'archive-relatos')); ?>	
				
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<? widget::Get("twitter-box"); ?>
				<? widget::Get("viagens", array('current_viagemId'=>get_current_blog_id(),'show_view_all'=>'yes', 'itens'=>2,'float'=>'right', 'margin_right'=>'30px')); ?>				
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo");?>				
			</div>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<?php include('footer-wp.php') ?>



