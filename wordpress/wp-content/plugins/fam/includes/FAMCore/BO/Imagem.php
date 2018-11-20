<?php

/**
 * class Imagem
 *
 * Description for class Imagem
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Conteudo.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/ImagemVO.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/LocationVO.php' );
require_once(ABSPATH. "/FAMCore/BO/Galeria.php");

class Imagem extends Conteudo {

	/**
	 * Imagem constructor
	 *
	 * @param 
	 */
	function Imagem($viagemId = null) {			
		parent::Conteudo("attachment", $viagemId);			
	}
	
	/**
	 * @return ImagemVO
	*/
	public static function GetImage($imageId)
	{	
		$imagemVO = new ImagemVO($imageId);
		if($imagemVO->ImageFullSrc != null)
		{
			return new ImagemVO($imageId);
		}		
	}
	
	/**
	 * @return ImagemVO
	*/
	public static function GetMidiaPrincipal($postId, $size)
	{
		$MidiaPrincipalId = get_post_meta($postId,'_thumbnail_id',true);
		return Imagem::GetImage($MidiaPrincipalId, $size);
	}
	
	
	/**
	 * @return ImagemVO[]
	*/
	public static function GetImagensAnexadas($postId)
	{
		$MidiaPrincipalId = get_post_meta($postId, 'imagem_principal', true);
		$Imagens = array();		
		$args = array('post_type' => 'attachment','numberposts' => -1,'post_parent' => $postId);
		$attachments = get_posts($args);
		if ($attachments)
		{
			foreach ($attachments as $attachment)
			{
				if($attachment->ID != $MidiaPrincipalId)
				{
					$Imagens[] = Imagem::GetImage($attachment->ID, 'thumbnail');
				}
			}			
		}
		return $Imagens;
	}
	
	
	/**
	 * @return ImagemVO[]
	*/
	public static function GetImagens($options)
	{
		//$MidiaPrincipalId = get_post_meta($postId,'_thumbnail_id',true);
		$Imagens = array();		
		if($options["itens"] == null  || $options["itens"] == 0)
		{
			$options["itens"] -1;
		}		
		$imagemBO = new Imagem();
		$attachments = $imagemBO->GetItens($options);		
		
		$Imagens = array();	
		if ($attachments)
		{
			if($imagemBO->MultiSiteData == true)
			{				
				return $attachments;
			}
			else
			{
				foreach ($attachments as $attachment)
				{			
					$ImageVO = 	Imagem::GetImage($attachment->ID, 'thumbnail');
					if($ImageVO->ImageThumbWidth == 119)
					{
						$Imagens[] = $ImageVO;					
					}
					else
					{
						$this->ReplaceImage($Imagens, $options);
					}
						
				}	
				return $Imagens;
			}		
		}
		
	}
	
	
	
	private function ReplaceImage(&$Imagens, $options)
	{
		$replaced = false;
		$counter = 0;
		do {
			$replaceImage = $this->GetImagens($options);
			$theSame = false;
			if(is_array($attachments) && count($attachments) > 0)
			{
				foreach ($attachments as $attachment)
				{
					if($attachment->ID == $replaceImage[0]->MediaId)
					{
						$theSame = true;
					}
				}
				if(!$theSame)
				{
					$Imagens[] = $replaceImage[0];
					$replaced = true;							
				}
			}
			$counter++;
		} while (!$replaced && $counter < 10);
	}
	
	


}

?>