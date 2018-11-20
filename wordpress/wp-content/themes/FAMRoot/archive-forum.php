<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global $categoria;
if($categoria->term_id != null)
{
	Conteudo::SetMetas("archive-topics",null, $categoria->name,$categoria->category_description);
}
else
{
	Conteudo::SetMetas("archive-forum");
}

include('header.php');?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">
				<?php 
					if($categoria->term_id != null)
					{
						widget::Get("forum_topics", array('itens'=> 10,'show_register_link'=>'yes','width'=>'615px','show_more'=> 'yes','margin_right'=>'12px','parentId'=> $categoria->term_id)); 
					}
					else
					{
						widget::Get("forum", array('itens'=> 10,'title'=>'Categorias do fórum','width'=>'615px','show_register_link'=>'yes','show_more'=> 'yes','margin_right'=>'12px'));
					}	
				?>
				
				<!-- end categorias forum -->			
				<aside id="coluna-lateral-direita" class="lateral_without_title_spacer">
					<? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"4px",'label'=>'archive_forum'));  ?>
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'100%','float'=>'right'));?>
					<?php widget::Get("ultimos-relatos", array('itens'=> 2,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>									
				</aside>
				
				<?php widget::Get("share", array("comment_box_width" => '900')); ?>
				<? widget::Get('footer_adds', array('label'=>'archive-forum')); ?>	
				
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
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



