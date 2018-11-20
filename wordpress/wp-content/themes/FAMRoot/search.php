<?php
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("search", null, $_GET["s"], $_GET["s"]);
if($_POST["is_ajax"] == null)
{
	include('header.php'); 
}
else
{
	require_once(ABSPATH."/wp-content/FAMComponents/widget.php");
}
	
 ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central" class="search-result">				
					<div class="search single_type_label search_icon topic_label_icon topic_font_color hand_font">Resultado de busca</div>						
					<div class="searchDiv">			
						<h1 class="search_scope">Itens para a busca por <strong>'<? echo $_GET["s"];?>'</strong></h1>
									
						<?php widget::Get("resultados_busca", array('itens'=> 30,'show_more'=> 'yes', 'term'=>$_GET["s"], 'multisite'=>"yes")); ?>
						
						<? 
							global $noSearchResult;							
							if($noSearchResult == "yes") 
							{
								?><div class="relatosInterna"><?
								widget::Get("blog_posts", array('title'=>'Últimos posts do blog','show_meta_data_label'=>'yes','itens'=> 8,'show_share'=>'yes','content_lenght'=> 600,'width'=>'96%', 'show_more'=> 'yes','show_location'=> true,'show_large_image'=>'yes'));
								?></div><?
							}
						?>
						
					</div>
					<aside id="coluna-lateral-direita" class="search_lateral" >
						<? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"20px",'label'=>'search_root','margin_right'=>'-5px'));  ?>
						<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'100%','float'=>'right'));?>						
						<?php widget::Get("ultimos-relatos", array('itens'=> 3,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>
						<?php widget::Get("galeria", array('title'=>'Álbuns de viagem','show_more' => 'yes','itens'=> 2)) ?>
					</aside>						
					<div class="clear"></div>
					<?php widget::Get("share", array("comment_box_width" => '930','show_share_bar' => 'yes')); ?>
					<? widget::Get('footer_adds', array('label'=>'search')); ?>	
					
				</div><!-- end bloco-conteudo-central -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<? widget::Get("twitter-box"); ?>							
					<? widget::Get("facebook-box"); ?>	
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php 
	if($_POST["is_ajax"] == null)
	{
		include('footer-wp.php'); 
	}
?>

