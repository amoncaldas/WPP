<?php

/**
 * class MediaVO
 *
 * Description for class MediaVO
 *
 * @author:
*/

require_once( ABSPATH . '/FAMCore/Data/DataAccess.php' );
require_once( ABSPATH . '/FAMCore/VO/LocationVO.php' );
require_once("ViajanteVO.php");

 class MediaVO extends DataAccess  {

	public $MediaId;
	public $Titulo;
	public $Legenda;
	public $Descricao;
	public $Location;
	public $MimeType;
	public $Autor;
	public $MainUrl;
	public $MediaUrl;	
	public $Content;
	public $DataPublicacao;
	public $YoutubeCode;
	public $YoutubeBaseThumbUrl;
	public $IsCover;
	
	/**
	 * MediaVO constructor
	 *
	 * @param 
	 */
	public function MediaVO($id = null) {
		parent::DataAcess("attachment");
		if($id != null)
		{
			$post = parent::GetById($id);
			$this->PopulateMediaVO($this,$post);		
		}
	}
	
	public function PopulateMediaVO(&$mediaVO, $post)
	{
		$mediaVO->Titulo =  $post->post_title;
		$mediaVO->MediaId = $post->ID;
		$mediaVO->IsCover = get_post_meta($post->ID, '_fam_upload_is_cover_', true) == "cover"? true: false;		
		
		$mediaVO->MediaUrl = get_permalink($post->ID);
		global $GetMultiSiteData;
		if($GetMultiSiteData == true && $post->blog_id > 0)
		{
			$mediaVO->MediaId .= ";".$post->blog_id;
			//$mediaVO->MediaUrl = get_site_url($post->blog_id)."?media=".$post->post_name."/".$post->ID."/";
		}
		
		$mediaVO->Descricao = $post->post_title;
		$mediaVO->DataPublicacao = $post->post_date;
		$mediaVO->Content = $post->guid;			
		if(!$mediaVO->CheckImageParentIsUser($id))
		{
			$mediaVO->Autor = new ViajanteVO($post->post_author);
		}
		$mediaVO->MimeType = $post->post_mime_type;	
		$mediaVO->MainUrl = $post->guid;			
		$mediaVO->Legenda = $post->post_excerpt;		
		
		
		
		
				
		$mediaVO->Location = new LocationVO();
		$mediaVO->Location->Local = get_post_meta($post->ID, "attachment_location", true);
		$mediaVO->Location->Latitude = get_post_meta($post->ID, "attachment_latitude", true);
		$mediaVO->Location->Longitude = get_post_meta($id, "attachment_longitude", true);
		
		if($post->post_excerpt != null)
		{
			$mediaVO->Descricao = $post->post_excerpt;
			
		}
		if($mediaVO->Location->Local != null)
		{
			$mediaVO->Descricao .=	" - capturada em ".$mediaVO->Location->Local;
		}
		
		$video = strpos($mediaVO->MimeType,"video");
								
		if($video === 0)
		{																	
			$videoUrlParts = explode("embed", $mediaVO->MainUrl);			
			$videoCode = trim($videoUrlParts[1],"/");	
			$mediaVO->YoutubeCode = $videoCode;	
			$mediaVO->YoutubeBaseThumbUrl = "http://img.youtube.com/vi/".$videoCode."/";													
		}	
				
	}
	
	private function CheckImageParentIsUser($imageId)
	{
		$isUser = false;
		$blogusers = get_users_of_blog();
		if ($blogusers) {
			foreach ($blogusers as $bloguser) {				
				$UserimgId = get_the_author_meta("_fam_upload_id_", $bloguser->user_id);				
				if($UserimgId == $imageId)
				{
					$isUser = true;
				}		
				
			}
		}
		return $isUser;
	}	
	
	public function PopulateMediaData(&$mediaVO, $post)
	{
		$this->PopulateMediaVO($mediaVO, $post);
		
		$video = strpos($post->post_mime_type,"video");	
		if($video > -1)
		{
			//$mediaVO->SetVideoServer();
		}
		else
		{
			$mediaVO->PopulateImageVO($mediaVO, $post);			
		}		
	}
}

?>