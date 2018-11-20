<?php

/**
 * class ViagemVO
 *
 * Description for class ViagemVO
 *
 * @author:Amon Caldas
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCore/Data/DataAccess.php' );
require_once(ABSPATH. "/FAMCore/BO/Imagem.php");
require_once(ABSPATH. "/FAMCore/BO/Viajante.php");
require_once(ABSPATH. "/FAMCore/BO/Destaque.php");
require_once("RoteiroVO.php");
require_once("ViajanteVO.php");
require_once("CategoriaVO.php");
require_once("RelatoVO.php");
require_once("AtualizacaoVO.php");
require_once("TagVO.php");

class ViagemVO  extends DataAccess {

	public $ViagemId;
	public $ViagemUrl;
	public $Titulo;
	public $Conteudo;
	public $Roteiro;
	public $LastUpdate;
	public $DataInicio;
	public $DataFim;
	public $QtdViajantes;	
	public $MidiaPrincipal;
	public $ListaLocais;
	public $NumPaises;
	public $SiglasPaises;
	public $NumLocais = 0;
	public $DiasDeViagem;
	public $ExibirViagem;
	public $SkeenName;
	
	
	/**
	 * Viagem constructor
	 *
	 * @param $id must be projetoID
	 */
	function ViagemVO($id = null) {
		parent::DataAcess("viagem");
		if($id != null)
		{
			$this->GetViagem($id);
		}
						
	}	
	
	public function GetSubContent($lenght)
	{
		$content = strip_tags($this->Conteudo, '');
		if(strlen($content) > $lenght)
		{			
			if(strpos($content, " ", $lenght) > $lenght)
			{
				return substr($content, 0, strpos($content, " ", $lenght))." ...";
			}
			else
			{
				$lenght = ($lenght > 20)? ($lenght -20): 0;								
				return substr($content, 0, strpos($content, " ", $lenght))." ...";
			}				
		}
		else
		{
			return $content;
		}			
	}
	
	/**
	 * @return ViagemVO
	*/
	private function GetViagem($viagemId)
	{					
		$this->PopulateViagemVO($this,$viagemId);					
		return $this;
	}	
	
	
	public function PopulateViagemVO(ViagemVO &$viagemVO, $viagemId = null)
	{	   
		if($viagemId == null)
		{
			$viagemId = get_current_blog_id();
		}
	    
		switch_to_blog($viagemId);
		$post =	$viagemVO->GetFirst();
		$blogDetails = get_blog_details($viagemId, true);
		$viagemVO->Titulo = $blogDetails->blogname;
		$viagemVO->ViagemId = $blogDetails->blog_id;
		$viagemVO->ViagemUrl = $blogDetails->siteurl;
		$viagemVO->Conteudo = $post->post_content;
		$resumo = strip_tags($post->post_content);
		$viagemVO->LastUpdate = $blogDetails->last_updated;	
				
		//recupera o roteiro da viagem		
		$roteiroVO = new RoteiroVO($viagemId);		
		$viagemVO->Roteiro = $roteiroVO->trajetos;
		$viagemVO->DataInicio = $roteiroVO->trajetos[0]->data_de_partida;		
		$viagemVO->DataFim = $roteiroVO->trajetos[count($roteiroVO->trajetos) - 1]->data_de_chegada;
		$dataInicio = strtotime(str_replace('/', '-', $viagemVO->DataInicio));
		$dataFim = strtotime(str_replace('/', '-', $viagemVO->DataFim));
		$viagemVO->DiasDeViagem = ceil(abs($dataFim - $dataInicio) / 86400);
		
		$viagemVO->QtdViajantes = count(get_users('blog_id='.$viagemId.'&exclude=1'));	
		$destaque  = new Destaque();
		$viagemVO->MidiaPrincipal = $destaque->GetDestaquePrincipal();
		
		if($viagemVO->MidiaPrincipal != null)
		{
			//$viagemVO->MidiaPrincipal->ImagemDestaqueViagem = ResizeImage($viagemVO->MidiaPrincipal->ImageCroppedSrc, 960);
			$viagemVO->MidiaPrincipal->ImagemDestaqueViagem = $viagemVO->MidiaPrincipal->ImageCroppedSrc;
		}		
		$viagemVO->SkeenName = (get_option('tema_fam') == false)? 'adventure': get_option('tema_fam');
		$count = 1;
		$lista = "";
		$flags = array();
		if(is_array($viagemVO->Roteiro) && count($viagemVO->Roteiro) > 0)
		{				
			foreach($viagemVO->Roteiro as $trajeto)
			{					
				if($count > 1)
				{
					$flag = strtolower($trajeto->LocationPartida->SiglaPais);					
					if (!in_array($flag, $flags)) {
						$flags[]  = $flag;											
					}
				}
				
				$flag = strtolower($trajeto->LocationChegada->SiglaPais);					
				if (!in_array($flag, $flags)) {
					$flags[]  = $flag;											
				}
				
					
				if($trajeto->LocationPartida->Local != null)
				{
					$localPartida = trim($trajeto->LocationPartida->GetLocalSubString(30));
				}
				if($trajeto->LocationChegada->Local != null)
				{
					$localChegada = trim($trajeto->LocationChegada->GetLocalSubString(30));
				}
				if($count == 1)
				{						
					if(strpos($lista,$localPartida) === false)
					{							
						$lista .= $localPartida;							
						$viagemVO->NumLocais++;
					}
					if(strpos($lista,$localChegada) === false)
					{							
						$lista .= " - ".$localChegada;							
						$viagemVO->NumLocais++; 
					}					
				}
				else
				{			
					if(strlen($lista) > strlen($localPartida) &&  strlen($localPartida) > 0 )
					{
						if(strpos($lista, $localPartida) === false)
						{
							$lista .= " - ".$localPartida;							
							$viagemVO->NumLocais++; 
						}							
					}
					elseif( strlen($localPartida) > 0)
					{	
						$lista .= " - ".$localPartida;
						$viagemVO->NumLocais++;
					}
						
					if(strlen($lista) > strlen($localChegada) &&  strlen($localChegada) > 0)
					{
						if(strpos($lista,$localChegada) === false)
						{								
							$lista .= " - ".$localChegada;							
							$viagemVO->NumLocais++;
						}							
					}	
					elseif( strlen($localChegada) > 0)
					{							
						$lista .= " - ".$localChegada;							
						$viagemVO->NumLocais++;
					}					
						
				}
				$count++;
			}
				
			if(strlen($lista) > 185)
			{
				$separator = strpos($lista," - ",175);
				$remove = substr($lista,$separator,strlen($lista) -1);
				$lista = str_replace($remove,"",$lista)."...";
			}
			$viagemVO->ListaLocais = $lista;			
			$viagemVO->NumPaises = (count($flags) > 0)? count($flags) : 1;
			$viagemVO->SiglasPaises = $flags;
		}
		
		$viagemVO->ExibirViagem = false;
		if($viagemVO->MidiaPrincipal->ImagemDestaqueViagem != null && $viagemVO->ListaLocais != null && CheckUploadedFileExists($viagemVO->MidiaPrincipal->ImagemDestaqueViagem))
		{
			$viagemVO->ExibirViagem = true;
		}
		
		restore_current_blog();							
	}
	
	/**
	 * @return ViagemVO[]
	*/
	public function GetViagemVOList($itens, $excluded_ids = null)
	{	
		/*global $wpdb;
		$sites = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_blogs ORDER BY blog_id" ) );	*/
		
		$blog_list = get_blog_list( 0, 'all' );	
		$viagens = array();	
		$counter = 1;	
		foreach ($blog_list AS $blog)
		{
			if ($blog['blog_id'] != 1) 
			{							
				$viagemVO = new ViagemVO();
				$viagemVO->PopulateViagemVO($viagemVO,$blog['blog_id']);	
				$viagens[] = $viagemVO;				
			}
			
		}				
		return $viagens;
	}	
}

?>