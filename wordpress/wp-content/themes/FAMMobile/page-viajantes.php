<?php 
require_once(ABSPATH."/FAMCore/BO/Viajante.php");

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("page_viajantes_fam");
$viagem_id = get_current_blog_id();
if($viagem_id == 1)
{
	$viagem_id = null;
}

include('header.php') ?>				
<div id="content">																	
	<?php widget::Get("viajantes", array("itens" => 5,"show_bio"=>'yes','show_more'=>'yes', 'viagemId'=>$viagem_id)); ?>				
	<div class="clear"></div>				
</div><!-- end content -->		
<?php include('footer/footer-default.php') ?>