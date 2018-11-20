<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global 	$wp_query;
$archive_posts = $wp_query->posts;
Conteudo::SetMetas("archive-atualizacao");

include('header.php');
?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">				
				<div class="atualizacao">				
					<?widget::Get("atualizacoes", array('title'=>'Status de viagem','itens'=> 8,'show_map'=>'yes','width'=>'100%','foto_width'=>'70px', 'show_more'=> 'yes','show_location'=> true,'default_archive'=>$archive_posts,'page'=>$_GET["page"]));?>								
				</div><!-- end atualizacoes -->
				<aside id="coluna-lateral-direita">
					<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"26px",'label'=>'archive_albuns','width'=>'280px','margin_right'=>'-5px'));  ?>	
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'93%','float'=>'right','title'=>'Outras viagens'));?>
					<?php widget::Get("ultimos-relatos", array('itens'=> 2,'content_lenght'=> 120,'width'=>'93%','float'=>'right','excluded_ids'=> array($relato->DadosRelato->RelatoId),'show_more'=> 'yes')); ?>														
					<?php widget::Get("fotos", array('show_more' => 'yes','itens'=>6, 'orderby'=>'rand')) ?>
				</aside>	
				<? widget::Get('footer_adds', array('label'=>'archive-atualizacao')); ?>			
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<?php widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id()));?>
				<? widget::Get("viagens", array('current_viagemId'=>get_current_blog_id(),'show_view_all'=>'yes', 'itens'=>2,'float'=>'right', 'margin_right'=>'30px')); ?>				
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo");?>				
			</div>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<?php include('footer-wp.php') ?>



