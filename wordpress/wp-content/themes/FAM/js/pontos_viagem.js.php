<?php
header('Content-type: text/javascript');

require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
require_once(ABSPATH."/FAMCore/BO/Viagem.php");
$viagem = new Viagem($_GET["idViagem"]);

?>
	if (typeof (roteirosViagem) != 'object') {
		var roteirosViagem = [];
	}
		
	if (typeof (pontosRoteiroViagem) != 'object') {
		var pontosRoteiroViagem = [];
	}
	pontosRoteiroViagem = [];
			
	<?			
		
	if(is_array($viagem->DadosViagem->Roteiro) && count($viagem->DadosViagem->Roteiro) > 0)
	{
		$counter =1;
		foreach($viagem->DadosViagem->Roteiro as $trajeto)
		{		
			?>var pontoViagem;<?		
			if($counter == count($viagem->DadosViagem->Roteiro))
			{
				?>pontoViagem = new Object();<?					
				?>pontoViagem.local = '<? echo trim($trajeto->LocationPartida->GetLocalSubString(30));?>';	
				pontoViagem.lat = '<? echo trim($trajeto->LocationPartida->Latitude);?>';
				pontoViagem.long = '<? echo trim($trajeto->LocationPartida->Longitude);?>';					
				pontoViagem.deslocamento = '<? echo trim($trajeto->transporte);?>';
				pontoViagem.deslocamentoAteAqui = '<? echo trim($trajeto->transporte);?>';
				pontosRoteiroViagem.push(pontoViagem);					
					
				pontoViagem = new Object();<?
				?>pontoViagem.local = '<? echo trim($trajeto->LocationChegada->GetLocalSubString(30));?>';
				pontoViagem.lat = '<? echo trim($trajeto->LocationChegada->Latitude);?>';
				pontoViagem.long = '<? echo trim($trajeto->LocationChegada->Longitude);?>';
				pontoViagem.deslocamentoAteAqui = '<? echo trim($trajeto->transporte);?>';
				pontoViagem.data = '<? echo trim($trajeto->data_de_chegada);?>';<?	
				?>pontoViagem.deslocamentoSaindoDaqui = 'Fim do trajeto';<?		
			}
			else
			{		
				?>pontoViagem = new Object();<?					
				?>pontoViagem.local = '<? echo trim($trajeto->LocationPartida->GetLocalSubString(30));?>';	
				pontoViagem.lat = '<? echo trim($trajeto->LocationPartida->Latitude);?>';
				pontoViagem.long = '<? echo trim($trajeto->LocationPartida->Longitude);?>';
				pontoViagem.deslocamentoSaindoDaqui = '<? echo trim($trajeto->transporte);?>';
				<? if($counter == 1) 
				{
					?>pontoViagem.deslocamentoAteAqui = 'Ponto inicial da viagem';<?						
				}
				else
				{
					$trajetoAnterior = $viagem->DadosViagem->Roteiro[$counter -2];
					?>pontoViagem.deslocamentoAteAqui = '<? echo trim($trajetoAnterior->transporte);?>';<?
						
						
				}?>
				pontoViagem.data = '<? echo trim($trajeto->data_de_partida);?>';<?
				
			}
			?>						
				pontoViagem.urlViagem = '<? echo trim($viagem->DadosViagem->ViagemUrl);?>'
				pontoViagem.imagemViagem = '<? echo trim($viagem->DadosViagem->MidiaPrincipal->OriginalImageVO->ImageThumbSrc);?>';
				pontoViagem.titulo = '<? echo trim($viagem->DadosViagem->Titulo);?>';
				pontoViagem.qtdViajantes = <? echo trim($viagem->DadosViagem->QtdViajantes);?>;
				pontoViagem.qtdLocais = <? echo trim($viagem->DadosViagem->NumLocais);?>;
							
				pontosRoteiroViagem.push(pontoViagem);
			<?					
			$counter++;	
		}
			?>
			var roteiroViagem = new Object();
			<? if($_GET["show_map_controls"] == "no"){echo "roteiroViagem.show_controls = false;";} else {echo "roteiroViagem.show_controls = true;"; }?>
			roteiroViagem.pontosRoteiroViagem = pontosRoteiroViagem;
			roteiroViagem.mapa_id = 'mapa_viagem_<?echo $_GET["idViagem"]?>';
			roteirosViagem.push(roteiroViagem);
				
		<?	
	}									  			
				
