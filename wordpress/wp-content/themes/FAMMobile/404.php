<?php
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("404");
get_header(); ?>		
	<div id="content">				
		<div class="failmsg">
			<div class="ooops">
				<h2 >Ooops!</h2>
			</div>
			<div class="oopsContent">				
				<div class="orientation">
					<h2 class="not_found">Conteúdo não encontrado!</h2>
					<div class="clear"></div>
					<h2 class="sugiro_buscar">Tente realizar uma busca</h2>
					<div class="busca_404">
						<? widget::Get("busca_form") ?>
					</div>
				</div>
			</div>						
		</div>	
		<div class="div404_separator">
			<hr/>
		</div>									
		<div class="clear"></div>					
		<? widget::Get("ultimos-relatos", array('title'=>'Veja os últimos relatos','itens'=> 8,'show_share'=>'yes','content_lenght'=> 350,'width'=>'100%', 'show_more'=> 'yes'));?>
	</div>
<?php include('footer/footer-default.php') ?>