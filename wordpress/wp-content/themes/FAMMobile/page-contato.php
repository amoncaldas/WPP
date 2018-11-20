<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("contato");

include('header.php') ?>			
	<div id="content">				
		<div class="contatos">												
			<? widget::Get('contato_form', array('width'=>'100%','title'=>'Nos envie uma mensagem')); ?>	
			<? widget::Get("twitter-box", array('show_footer'=>'yes','title'=>'Nos envie um tweet','width'=>500)); ?>
			<div class="facebook_message">							
				<span>Ou nos envie uma mensagem através do <a target="_blank" href="https://www.facebook.com/messages/fazendoasmalas">Facebook</a></span>
			</div>				
		</div>					
		<div class="clear"></div>				
	</div><!-- end content -->		
<?php include('footer/footer-default.php') ?>