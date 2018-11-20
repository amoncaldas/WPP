<?php

/**
 * class Search
 *
 * Description for class Search
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Conteudo.php' );
class Search extends Conteudo {
	
	public $HasMoreData;

	/**
	 * Search constructor
	 *
	 * @param 
	 */
	function Search() {

	}
	
	/**
		* @return RelatoVO[]
	*/
	public  function GetData($options) {		
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 2;
		}	
		$itensOriginal = $options["itens"];
		
		if(isset($_POST["excluded_ids_search"]) && $_POST["excluded_ids_search"] != null)
		{		
			$options['excluded_ids_search'] = $_POST["excluded_ids_search"];							
		}
		
		$options["itens"]++;
		$this->HasMoreData = false;	
		$resultados = $this->GetSearchResults($options);
		
		if(is_array($resultados) && count($resultados)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$resultados = array_slice($resultados, 0, count($resultados) -1);				
		}		
		return $resultados;			
	}
}

?>