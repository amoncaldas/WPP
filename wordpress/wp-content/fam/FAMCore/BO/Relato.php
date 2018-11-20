<?php

/**
 * class Relato
 *
 * Description for class Relato
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/BO/Conteudo.php' );
require_once( ABSPATH . '/FAMCore/VO/RelatoVO.php' );


class Relato extends Conteudo {	
	
	/**
	 * @var RelatoVO $DadosRelato
	*/
	public $DadosRelato;
	private $RelatoId;
	public $HasMoreData;
	
	/**
	 * Relato constructor. Can be instantiated passing the relato id or not and viagemId or not
	 *
	 * @param $id must be the project(post) unique id and $viagemId musb be the blog id
	 */	
	function Relato($id = null, $viagemId = null) {
		parent::Conteudo("relatos", $viagemId);
		if($id != null)
		{			
			$this->DadosRelato = new RelatoVO($id);									
		}				
	}
	
		
		
	/**
		* @return RelatoVO[]
	*/
	public  function GetRelatos($options) {
		
		if($options['orderby'] == null)
		{
			$options['orderby'] = 'menu_order';			
		}
		if($options['order'] == null)
		{			
			$options['order'] = 'ASC';
		}
		
		$relatos = $this->GetItens($options);		
		if ($relatos)
		{		
			if($this->MultiSiteData == true)
			{			
				return $relatos;
			}
			else
			{	
				$relatosVO = array();				
				foreach ($relatos as $relato)
				{		
					$relatoVO = new RelatoVO($relato->ID);	
					$relatosVO[] = $relatoVO;					
				}			
			}
			return $relatosVO;	
		}		
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
		$options["itens"]++;
		$this->HasMoreData = false;	
		$relatos = $this->GetRelatos($options);
			
		if(is_array($relatos) && count($relatos)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$relatos = array_slice($relatos, 0, count($relatos) -1);				
		}
			
		return $relatos;			
	}	
}

?>