<?php

/**
 * class TrajetoVO
 *
 * Description for class TrajetoVO
 *
 * @author:
*/



class TrajetoVO  {
	
	public $LocationPartida;
	public $LocationChegada;
	public $transporte;	
	public $data_de_partida;	
	public $data_de_chegada;	
	
	/**
	 * TrajetoVO constructor
	 *
	 * @param 
	 */
	function TrajetoVO() {

	}
	
	public function PopulateTrajetoVO(&$trajetoVO, $dadosTrajeto)
	{		
		$trajetoVO->data_de_chegada = 	$dadosTrajeto->data_de_chegada;
		$trajetoVO->data_de_partida = $dadosTrajeto->data_de_partida;					
				
		$trajetoVO->LocationPartida  = new LocationVO();
		$trajetoVO->LocationChegada = new LocationVO();					
				
		$trajetoVO->LocationPartida->Pais =  $dadosTrajeto->Pais;
		$trajetoVO->LocationPartida->Cidade =  $dadosTrajeto->Cidade;
		$trajetoVO->LocationPartida->Latitude = $dadosTrajeto->latitude_de_partida;
		$trajetoVO->LocationPartida->Longitude = $dadosTrajeto->longitude_de_partida;					
				
		$trajetoVO->LocationChegada->Pais = $dadosTrajeto->Pais;
		$trajetoVO->LocationChegada->Cidade = $dadosTrajeto->Cidade;
		$trajetoVO->LocationChegada->Latitude = $dadosTrajeto->latitude_de_chegada;;
		$trajetoVO->LocationChegada->Longitude = $dadosTrajeto->longitude_de_chegada;				
				
		$transporte = json_decode($dadosTrajeto->transporte);
		$trajetoVO->transporte = $dadosTrajeto->transporte;					
				
		$trajetoVO->LocationPartida->Local =  $dadosTrajeto->local_de_partida;
		$trajetoVO->LocationChegada->Local = $dadosTrajeto->local_de_chegada;	
		
			
	}
}

?>