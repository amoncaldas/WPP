<?php

/**
 * class AtualizacaoVO
 *
 * Description for class AtualizacaoVO
 *
 * @author:Amon Caldas
*/

require_once( ABSPATH . '/FAMCore/Data/DataAccess.php' );
require_once("ViajanteVO.php");
require_once("LocationVO.php");
require_once( ABSPATH . '/FAMCore/BO/Media.php');
require_once(ABSPATH. "/FAMCore/BO/Galeria.php");

class AtualizacaoVO extends DataAccess {

	public $AtualizacaoId;
	public $AtualizacaoUrl;
	public $AtualizacaoHash;
	public $Titulo;
	public $Conteudo;
	public $Location;
	public $DataPublicacao;
	public $MidiaPrincipal;
	public $MidiasAnexadas;
	public $Autor;
	public $Status;
	public $OgTitle;
	
		
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
	 * AtualizacaoVO constructor
	 *
	 * @param 
	 */
	function AtualizacaoVO($id = null) {
		
		parent::DataAcess("atualizacao");
		if($id != null)
		{
			$this->GetAtualizacaoById($id);
		}
	}
	
	public function PopulateAtualizacaoVO(AtualizacaoVO &$AtualizacaoVO, $post)
	{
		$AtualizacaoVO->Titulo = $post->post_title;
		$AtualizacaoVO->AtualizacaoId = $post->ID;
		$AtualizacaoVO->AtualizacaoUrl = get_bloginfo('home')."/status/".$post->post_name."/".$post->ID."/";//get_bloginfo('home').'/status/'."#".$AtualizacaoVO->AtualizacaoHash;
		global $GetMultiSiteData;
		if($GetMultiSiteData == true)
		{
			$AtualizacaoVO->AtualizacaoId .= ";".$post->blog_id;
			$AtualizacaoVO->AtualizacaoUrl = get_site_url($post->blog_id)."/status/".$post->post_name."/".$post->ID."/";
		}	
			
		$hash = split("status/",get_permalink($post->ID));
		$AtualizacaoVO->AtualizacaoHash = str_replace("/", "", $hash[1]);		
		$AtualizacaoVO->DataPublicacao =  $post->post_date;
		$AtualizacaoVO->Autor =  new ViajanteVO($post->post_author);
		$AtualizacaoVO->Status = $post->post_status;	
		
		//recupera a imagem principal da Atualizacao
		
		$mediasStatus = Galeria::GetMediasId(array('parentId'=>$AtualizacaoVO->AtualizacaoId));	
		$mediaBO = new Media();
		if(is_array($mediasStatus) && count($mediasStatus) > 0)
		{
			foreach($mediasStatus as $mediaStatus)
			{	
				$media = $mediaBO->GetMedia($mediaStatus);
				$AtualizacaoVO->MidiasAnexadas[] = $media;
				if($AtualizacaoVO->MidiaPrincipal == null && $media->YoutubeCode == null)	
				{
					$AtualizacaoVO->MidiaPrincipal = $media;
				}
			}
		}	
		else
		{
			
		}	
		
		/*$midiaId = get_post_meta($post->ID, "_fam_upload_id_", true);	
		$AtualizacaoVO->MidiaPrincipal = Media::GetMedia($midiaId);*/
		if($AtualizacaoVO->MidiaPrincipal->ImageFullSrc == null)
		{
			$AtualizacaoVO->MidiaPrincipal = $AtualizacaoVO->Autor->UserImage;
		}
		$AtualizacaoVO->Conteudo = get_post_meta($post->ID, "conteudo", true);		
		$AtualizacaoVO->Location = new LocationVO();			
		
		$AtualizacaoVO->Location->Local = get_post_meta($post->ID, "local", true);
		$AtualizacaoVO->Location->Latitude =  get_post_meta($post->ID, "latitude", true);
		$AtualizacaoVO->Location->Longitude = get_post_meta($post->ID, "longitude", true);	
		if($AtualizacaoVO->Location->Local != null)
		{
			$AtualizacaoVO->OgTitle = $AtualizacaoVO->Titulo." | Em ". $AtualizacaoVO->Location->GetLocalSubString(50)." | Por ".$AtualizacaoVO->Autor->FullName;
						
		}
		else
		{
			$AtualizacaoVO->OgTitle = $AtualizacaoVO->Titulo." | Por ".$AtualizacaoVO->Autor->FullName;
		}	
		
			
		
	}
	
	/**
	 * @return AtualizacaoVO[]
	*/
	public function GetAtualizacaoVOList($postsAtualizacao)
	{		
		$Atualizacaos = array();
		foreach($postsAtualizacao as $postAtualizacao)
		{			
			$AtualizacaoVO = new AtualizacaoVO();
			$AtualizacaoVO->PopulateAtualizacaoVO($AtualizacaoVO,$postAtualizacao);			
			$Atualizacaos[] = $AtualizacaoVO;						
		}		
		return $Atualizacaos;
	}
	
	private function GetAtualizacaoById($id)
	{		
		$post = parent::GetById($id);				
		$this->PopulateAtualizacaoVO($this,$post);			
		return $this;
	}	
	
}

?>