<?
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viagem.php");
$viagem = new Viagem($options["idViagem"]);
$lastLocation = $viagem->GetLastLocation(15);
$mapHeightPixel = (strrpos($options["height"], "%") === false)? (str_replace("px", "",  $options["height"]) -80)."px" : $options["height"];

?>
<section class="roteiroelocalizacao" style="width:<? echo $options["width"];?> !important; height:<? echo $options["height"];?> !important;">
	<? 
		if ($options["show_title"] != 'no')
		{
			if ($options["title"] != null)
			{
				echo "<h2>".$options["title"]."</h2>";
			}
			else
			{
				echo "<h2>Roteiro e Localização</h2>";
			}
		}
	
	?>	
	<script type="text/javascript" src="/wp-content/themes/FAM/js/pontos_viagem.js.php?idViagem=<?echo $options["idViagem"]?>&show_map_controls=<?echo $options["show_map_controls"]?>"></script>		
	
	<div class="mapa_roteiro_viagem" style="height:<? echo $mapHeightPixel?>" id="mapa_viagem_<?echo $options["idViagem"]?>">
		
	</div><!-- end mapa -->
	<? if ($lastLocation["url"] != null && $options["show_last_location"] != 'no')
	{
		?><span class="local">Último local: <a href="<? echo $lastLocation["url"]?>"><? echo $lastLocation["local"]; ?></a></span><?
	}?>
	
	<? if ($options["link_more_details"] != 'no')
	{
		?><a class="ver" href="<?=bloginfo('url')?>/viagem/">+ detalhes</a><?
	}?>
	
</section><!-- end roteiroelocalizacao -->