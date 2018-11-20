<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global $categoria_blog;
$cat = get_category_by_slug($categoria_blog);
if($categoria_blog != null)
{		
	Conteudo::SetMetas("archive-blog-category",$categoria_blog,$cat->name);
}
else
{		
	Conteudo::SetMetas("archive-blog");
}


include('header.php');?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">				
				<div class="relatosInterna list_blog_posts blog">					
					<?php widget::Get("blog_posts", array('show_meta_data_label'=>'yes','title'=>'Blog Fazendo as Malas','itens'=> 8,'show_share'=>'yes','content_lenght'=> 600,'width'=>'95%','show_more'=> 'yes','width'=>'95%','show_large_image'=>'yes','page'=>$_GET["page"])); ?>									
				</div>
				<!-- end relatos -->
				
				<aside id="coluna-lateral-direita" class="lateral_list_blogs lateral_without_title_spacer">
					<? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"0px",'label'=>'archive_blog','margin_right'=>'-5px'));  ?>
					<?php //widget::Get("blog_sidebar", array('itens'=> 2,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'100%','float'=>'right'));?>					
					<?php widget::Get("ultimos-relatos", array('itens'=> 2,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>													
				</aside>	
				<?php widget::Get("share", array("comment_box_width" => '900')); ?>
				<? widget::Get('footer_adds', array('label'=>'archive-blog_post')); ?>	
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container" >
				<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes')); ?>	
				<?php widget::Get("facebook-box");?>				
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo");?>				
			</div>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<?php include('footer-wp.php') ?>



