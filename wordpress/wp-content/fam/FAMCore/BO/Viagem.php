<?php

/**
 * class Viagem
 *
 * Description for class Viagem
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/BO/Conteudo.php' );
require_once( ABSPATH . '/FAMCore/VO/ViagemVO.php' );
require_once( ABSPATH . '/FAMCore/VO/LocationVO.php' );
require_once( ABSPATH . '/FAMCore/BO/Relato.php' );
require_once( ABSPATH . '/FAMCore/VO/AtualizacaoVO.php' );
require_once( ABSPATH . '/FAMCore/VO/AlbumVO.php' );
require_once( ABSPATH . '/FAMCore/BO/Viagem.php' );

class Viagem extends Conteudo  {
	
	/**
	 * @var ViagemVO $DadosViagem
	*/
	public $DadosViagem;
	public $HasMoreData;
	
	/**
	 * Viagem constructor. Can be instantiated passing the project id or not
	 *
	 * @param $id must be the project(post) unique id
	 */	
	function Viagem($viagemId = null) {				
		parent::Conteudo("viagem", $viagemId);
		$this->DadosViagem = new ViagemVO($viagemId);
		require_once( ABSPATH . '/FAMCore/BO/Post.php');											
	}
	
		
		
	
	/**
	 * @return ViagemVO[]
	*/
	public function GetViagens($options)
	{		
		$blog_list = Conteudo::GetSites($options);
		
		$viagens = array();	
		foreach ($blog_list AS $blog)
		{										
			$viagemVO = new ViagemVO();
			$viagemVO->PopulateViagemVO($viagemVO,$blog->blog_id);	
			$viagens[] = $viagemVO;						
		}		
			
		return $viagens;	
	}
	
	
	
	/**
	 * @return ViagemVO[]
	*/
	private function AddViagemsToArray(&$receiveItensArray, $sourceArray)
	{		
		if(count($receiveItensArray) > 0)
		{					
			foreach($sourceArray as $viagemVO_S)
			{		
				$viagemJaExiste = false;		
				foreach($receiveItensArray as $viagemVO_R)
				{					
					if($viagemVO_R->ViagemId == $viagemVO_S->ViagemId)
					{	
						$viagemJaExiste = true;
						break 1;							
					}
				}	
				if(!$viagemJaExiste)
				{
					$receiveItensArray[] = $viagemVO_S;
					$viagemJaExiste = false;
				}			
			}
		}
		else
		{
			foreach($sourceArray as $viagemVO_S)
			{
				$receiveItensArray[] = $viagemVO_S;				
			}
		}		
	}
	
	
	
	public function GetLastLocation($locationLenght = null)
	{		
		global $wpdb;
		$sql = 'SELECT '.$wpdb->posts.'.id, '.$wpdb->postmeta.'.meta_value,  '.$wpdb->posts.'.post_type FROM '.$wpdb->postmeta.' inner join '.$wpdb->posts.' on '.$wpdb->posts.'.id = '.$wpdb->postmeta.'.post_id and '.$wpdb->postmeta.'.meta_key = "local"  
		where (post_type = "atualizacao" or post_type = "relatos" or post_type = "albuns")  and (post_status="publish")  order by post_date DESC limit  1';		
		$results = $wpdb->get_results($sql);			
		
		if(is_array($results) && count($results) > 0)
		{
			$location = $results[0]->meta_value;
			if($locationLenght != null && $locationLenght > 0)
			{
				if(strlen($location) > $locationLenght)
				{			
					if(strpos($location, ",", $locationLenght) > $locationLenght)
					{							
						$location =  substr($location, 0, strpos($location, ",", $locationLenght));
					}
					elseif(strpos($location, " ", $locationLenght) > $locationLenght)
					{							
						$location =  substr($location, 0, strpos($location, " ", $locationLenght));
					}
					else
					{
						$lenght = ($locationLenght > 5)? ($locationLenght -5): 0;								
						$location = substr($location, 0, strpos($location, " ", $locationLenght));
					}				
				}
				
			}
			switch($results[0]->post_type)
			{
				case("relatos"):
					$relatoVO = new RelatoVO($results[0]->id);
					return array('url'=> $relatoVO->RelatoUrl,'local'=>$location);
					break;
				case("atualizacao"):
					$atualizacaoVO = new AtualizacaoVO($results[0]->id);
					return array('url'=> $atualizacaoVO->AtualizacaoUrl,'local'=>$location);
					break;
				case("albuns"):
					$albumVO = new AlbumVO($results[0]->id);
					return array('url'=> $albumVO->AlbumUrl,'local'=>$location);
					break;					
			}		
		}
		else
		{
			return array('url'=> '#','local'=>'Nenhuma atualização de local');
		}
	}	
	
	
	public function GetData($options)
	{		
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 1;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		
		$this->HasMoreData = false;
		
		if($options["current_viagemId"] != null)
		{
			if( is_array( $options["excluded_ids"]))
			{
				$options["excluded_ids"][] = $options["current_viagemId"];
			}
			else
			{
				$options["excluded_ids"] = array($options["current_viagemId"]);
			}
		}
		
		if($_POST["search_term"] != null)
		{
			$options["search_term"] = $_POST["search_term"];
		}
			
		$viagens = $this->GetViagens($options);	
			
		if(is_array($viagens) && count($viagens)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$viagens = array_slice($viagens, 0, count($viagens) -1);				
		}	
			
		
		return $viagens;		
	
	}
}

?>