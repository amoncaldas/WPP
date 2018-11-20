<?php 
require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$viajante = new Viajante();
$viajantes = $viajante->GetViajantes(10);

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("page-viagens");

include('header.php') ?>				
	<div id="content">				
		<div class="lista_viagens">												
			<? widget::Get("viagens", array('list_type'=>'box','itens'=>6,'show_more'=>'yes','width'=>'100%','float'=>'none','margin_right'=>'none'));;?>						
		</div>					
		<div class="clear"></div>		
	</div><!-- end content -->		
<?php include('footer/footer-default.php') ?>