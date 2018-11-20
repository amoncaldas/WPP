<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("page-viajantes");

include('header.php') ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">					
					<?php widget::Get("viajantes", array("itens" => 10,"show_bio"=>'yes','show_more'=>'yes','viagemId'=>get_current_blog_id())); ?>
					<aside id="coluna-lateral-direita">
						<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"20px",'label'=>'page_viajantes','width'=>'280px'));  ?>	
						<?php widget::Get("a-viagem", array('idViagem'=>get_current_blog_id())); ?>							
						<?php widget::Get("fotos", array('itens'=> 8, 'orderby'=>'rand','show_more'=>'yes')); ?>					
					</aside><!-- end blocodir -->
					<?php widget::Get("share", array('hideCommentBox'=>true,'show_share_bar' => 'yes')); ?>
					<?php widget::Get("share", array("comment_box_width" => '900')); ?>
					<? widget::Get('footer_adds', array('label'=>'page-viajantes')); ?>
					<div class="clear"></div>					
				</div><!-- end page -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<? widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id())) ?>
					<?php widget::Get("ultimos-relatos", array('float' => "right", 'margin_right' => "25px")) ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>