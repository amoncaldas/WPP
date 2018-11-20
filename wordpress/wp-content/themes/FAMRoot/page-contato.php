<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("contato");

include('header.php') ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">
					<div class="contatos">												
						<? widget::Get('contato_form', array('width'=>'550px','title'=>'Nos envie uma mensagem')); ?>	
						<? widget::Get("twitter-box", array('show_footer'=>'yes','title'=>'Nos envie um tweet')); ?>
						<div class="facebook_message">							
							<span>Ou nos envie uma mensagem através do </span><a target="_blank" href="https://www.facebook.com/messages/fazendoasmalas">Facebook</a>
						</div>				
					</div>
					<aside id="coluna-lateral-direita" class="coluna_lateral_viagens">
						<? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"10px",'label'=>'page_contato','margin_right'=>'-5px'));  ?>						
						<?php widget::Get("ultimos-relatos", array('itens'=> 3,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>					
					</aside><!-- end blocodir -->
					<?php widget::Get("share", array("comment_box_width" => '900')); ?>
					<? widget::Get('footer_adds', array('label'=>'page-contato')); ?>	
					<div class="clear"></div>					
				</div><!-- end page -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<? widget::Get("facebook-box") ?>
					<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes')); ?>	
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>