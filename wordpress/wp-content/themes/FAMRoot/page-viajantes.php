<?php 
require_once(ABSPATH."/FAMCore/BO/Viajante.php");

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("page_viajantes_fam");

include('header.php') ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">													
					<?php widget::Get("viajantes", array("itens" => 6,"show_bio"=>'yes','show_more'=>'yes')); ?>						
					<aside id="coluna-lateral-direita">
						<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"20px",'label'=>'page_viajantes','width'=>'280px'));  ?>
						<?php widget::Get("galeria", array('show_more' => 'no','itens'=> 2,'title'=>"Albuns de viagem",'orderby'=>'rand')) ?>								
						<?php widget::Get("ultimos-relatos", array('itens'=> 3,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>					
					</aside><!-- end blocodir -->
					<?php widget::Get("share", array('hideCommentBox'=>true,'show_share_bar' => 'yes')); ?>
					<?php widget::Get("share", array("comment_box_width" => '900')); ?>
					<? widget::Get('footer_adds', array('label'=>'page-viajantes')); ?>	
					<div class="clear"></div>					
				</div><!-- end page -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">					
					<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes')); ?>
					<?php widget::Get("ultimos-relatos", array('float' => "right", 'margin_right' => "25px")) ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>