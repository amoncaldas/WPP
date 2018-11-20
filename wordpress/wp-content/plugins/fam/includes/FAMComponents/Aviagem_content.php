<?
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viagem.php");
$viagem = new Viagem($options["idViagem"]);

?>

<section class="aviagem">
	<h2>A Viagem</h2>
	<div class="a_viagem_text">
		<p><? echo $viagem->DadosViagem->GetSubContent(390);?></p>
	</div>
	<a href="<? echo $viagem->DadosViagem->ViagemUrl;?>/viagem" class="ver">+ informações</a>
</section><!-- end aviagem -->