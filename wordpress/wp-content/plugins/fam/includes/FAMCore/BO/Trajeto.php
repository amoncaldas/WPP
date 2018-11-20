<?php

/**
 * class Trajeto
 *
 * Description for class Trajeto
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/TrajetoVO.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/LocationVO.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Conteudo.php' );
class Trajeto extends Conteudo  {

	/**
	 * Trajeto constructor
	 *
	 * @param 
	 */
	function Trajeto() {
		parent::Conteudo('trajeto');
	}
	
	public  function GetTrajetos($viagemId) {			
				
		$trajetos = $this->GetTrajetosViagem($viagemId);				
		
		if ($trajetos)
		{
			$trajetosVO  = array();				
			foreach ($trajetos as $trajeto)
			{				
				
				$trajetoVO  = new TrajetoVO();
				$trajetoVO->PopulateTrajetoVO($trajetoVO,$trajeto);
				$trajetosVO[] = $trajetoVO;								
			}	
					
		}
		
		return $trajetosVO;	
			
	}
	
	public function SaveTrajetos($trajetosPostArray, $viagemId)
	{
		global $wpdb;
		$trajetosItens = $this->SetTrajetoArray($trajetosPostArray);
		
		//delete all trajetos	
		if(is_array($trajetosItens) && count($trajetosItens)> 0)
		{
			$wpdb->query($wpdb->prepare('delete from wp_fam_trajetos where viagem_id = "'.$viagemId.'"'));				
		}			 
			
		foreach($trajetosItens as $trajeto)				
		{					
			$sql = 	'INSERT INTO wp_fam_trajetos (viagem_id, local_de_partida, latitude_de_partida, longitude_de_partida, data_de_partida, local_de_chegada, latitude_de_chegada, longitude_de_chegada, data_de_chegada, transporte) VALUES
					("'.$viagemId.'", "'.$trajeto['local_de_partida'].'", "'.$trajeto['latitude_de_partida'].'", "'.$trajeto['longitude_de_partida'].'", "'.$trajeto['data_de_partida'].'","'.$trajeto['local_de_chegada'].'", "'.$trajeto['latitude_de_chegada'].'", "'.$trajeto['longitude_de_chegada'].'", "'.$trajeto['data_de_chegada'].'", "'.$trajeto['transporte'].'")';
			$wpdb->query($wpdb->prepare($sql));			
		}
	}
	
	
	function SetTrajetoArray($trajetosPostArray)
	{
		foreach ($trajetosPostArray as $trajeto)
		{
			$trajetoValues = explode('|', $trajeto);
								
			//data example
			//-12.9703817,-38.512382|-25.3935271,-51.45617010000001|Salvador - Bahia, Brazil|12/10/2012 15:22:17 | Guarapuava - Parana, Brazil |12/10/2012 15:22:17|Carro
		
		
			$posicaoPartida = $trajetoValues[0];
			$posicaoChegada = $trajetoValues[1];
		
			$coordenadasPartida = explode(',',$posicaoPartida);
			$coordenadasChegada = explode(',',$posicaoChegada);
			$latPartida =  $coordenadasPartida[0];
			$longPartida = $coordenadasPartida[1];
			$latChegada = $coordenadasChegada[0];
			$longChegada =  $coordenadasChegada[1];
		
			$localPartida = $trajetoValues[2];
			$dataHoraPartida = $trajetoValues[3];
			$localChegada = $trajetoValues[4];
			$dataHoraChegada = $trajetoValues[5];
			$transporte	 = $trajetoValues[6];			
					

			// set the values to save and options to use
			$params = array
			(				
				'local_de_partida' => $localPartida, 
				'latitude_de_partida' => $latPartida, 
				'longitude_de_partida' => $longPartida,
				'data_de_partida' => $dataHoraPartida, 
				'local_de_chegada' => $localChegada, 
				'latitude_de_chegada' => $latChegada, 
				'longitude_de_chegada' => $longChegada, 
				'data_de_chegada' => $dataHoraChegada, 
				'transporte' => $transporte				
			);
		
			// add item to array
			$trajetosItens[] = $params;		
		}
		return $trajetosItens;
	}
}

?>