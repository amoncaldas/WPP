<?php

/**
 * class Media
 *
 * Description for class Media
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Imagem.php');
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/AlbumVO.php');
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Conteudo.php');
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Galeria.php");
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/MediaVO.php');
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/MetaVO.php');
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/ImagemVO.php');

class Media extends Conteudo   {

	public $HasMoreData;
	public $AlbumData;

	/**
	 * Media constructor
	 *
	 * @param 
	 */
	function Media($viagemId = null) {
		parent::Conteudo("attachment", $viagemId);
	}
		
	public function GetData($options)
	{		
		$itens = $options["itens"];
		$options["itens"]++;
		$imagemBO = new Imagem();	
		$galeriaBO = new Galeria();		
		if($options["medias"] != null)
		{
			$mediasAlbum = $options["medias"];
		}
		else
		{
			$mediasAlbum = $this->GetMedias($options);			
		}
			
		$this->SetMediaItens($medias,$openMedia,$mediasAlbum);
		
		if($options["parentId"] != null)
		{						
			$this->AlbumData = new AlbumVO($options["parentId"], true);					
		}					
			
		if(is_array($medias) && count($medias)> $itens)
		{	
			$this->HasMoreData = true;
			$medias	= array_slice($medias, 0, count($medias) -1);
		}	
						
		return $medias;
		
	}
	
	private function SetMediaItens(&$mediasArray, $openMedia, $sourceMedias )
	{	
		$openMedia = $this->GetOpenMedia();	
			
		if($openMedia != null)
		{
			$mediasArray[] = $openMedia;
										
			if(is_array($sourceMedias) && count($sourceMedias) > 0)
			{
				$arrayHasOpenMedia = false;
				foreach($sourceMedias as $item)
				{
					if($openMedia->MediaId == $item->MediaId)
					{
						$arrayHasOpenMedia = true;
					}
				}
				
				if($arrayHasOpenMedia == false)
				{
					$itens = array_slice($sourceMedias, 1, count($sourceMedias) -1);
				}	
				else
				{
					$itens = $sourceMedias;
				}			
				foreach($itens as $item)
				{
					if($openMedia->MediaId != $item->MediaId)
					{
						$mediasArray[] = $item;
					}
				}
			}					
		}
		else
		{
			$mediasArray = $sourceMedias;
		}
	}
	
	public static function SetMediaOG($screen, $contentObj = null)
	{
		$media  = new Media();
		$mediaOG =  $media->GetOpenMedia();	
		global $Meta;
		$MediaOGUrl = "";	
		$Meta = new MetaVO();
		if($mediaOG != null)//if screen has an open media
		{
			$Meta->DescriptionText = $mediaOG->Descricao." | Mídia publicada no site de viagens ".get_bloginfo('name')." | Fazendo as Malas - Descobrindo o mundo. Viaje junto com a gente.";	
		}
		else//if not, set media og properties
		{		
			switch($screen)//set ogs based on screen type 
			{
				case "archive-relatos":						
					require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");
					$destaque = new Destaque();
					$destaques = $destaque->GetDestaques(1);
					$mediaOG = $destaques[0];
					break;
				case "archive-albuns":						
					require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Galeria.php");
					$galeriaBO = new Galeria();
					$albuns = $galeriaBO->GetAlbuns(array('itens'=>1));
					$mediaOG = $albuns[0]->MidiaPrincipal;
					break;
				case "archive-videos":				
					$videos = $media->GetMedias(array('itens'=>1,'destaques_video'=> "yes",'orderby'=> "rand"));
					$mediaOG = $videos[0];
					$mediaOG->ImageLargeSrc = $mediaOG->YoutubeBaseThumbUrl."hqdefault.jpg";								
					break;
				case "archive-atualizacao":						
					require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");
					$destaque = new Destaque();
					$destaques = $destaque->GetDestaques(1);
					$mediaOG = $destaques[0];
				case "archive-viagem":		
					require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");				
					$destaque = new Destaque();
					$destaques = $destaque->GetDestaques(1);
					$mediaOG = $destaques[0];
					break;
				case "author":									
					$mediaOG = $contentObj->UserImage;
					break;
				case "single-albuns":					
					$mediaOG = $contentObj->MidiaPrincipal;								
					$Meta->Latitude = $contentObj->Location->Latitude;
					$Meta->Longitude = $contentObj->Location->Longitude;
					break;
				case "single-relatos":					
					$mediaOG = $contentObj->MidiaPrincipal;
					$Meta->Latitude = $relato->Location->Latitude;
					$Meta->Longitude = $relato->Location->Longitude;						
					break;
				case "single-blog":				
					$mediaOG = $contentObj->MidiaPrincipal;										
					break;
				case "single-atualizacao":					
					$mediaOG = $contentObj->MidiaPrincipal;
					$Meta->Latitude = $contentObj->Location->Latitude;
					$Meta->Longitude = $contentObj->Location->Longitude;								
					break;
				case "page-viajantes":						
					require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viajante.php");
					$viajante = new Viajante();
					$viajantes = $viajante->GetData(array('itens'=>1,'viagemId'=>get_current_blog_id()));
					$mediaOG = $viajantes[0]->UserImage;
					break;
				case "page-viagens":						
					require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viagem.php");
					$viagem = new Viagem();					
					break;				
				case "index":					
					if(get_current_blog_id() != 1)
					{							
						require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");				
						$destaque = new Destaque();
						$destaques = $destaque->GetDestaques(1);
						$mediaOG = $destaques[0];
						
					}										
					break;
				default:						
					$viagemId = get_current_blog_id();	
					if($viagemId != 1)
					{							
						require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");				
						$destaque = new Destaque();
						$destaques = $destaque->GetDestaques(1);						
						$mediaOG = $destaques[0];
					}
					else
					{
						$mediaOG = new MediaVO();
						$mediaOG->DescriptionText = "Fazendo as malas";
						$mediaOG->MimeType = "image/jpg";
					}						
					break;					
										
			}
			
			//check if lat and long a setted, and defines location ogs
			if($Meta->Latitude != null && $Meta->Longitude != null)
			{
				$Meta->LatOGHtml ='<meta property="og:latitude" content="'.$Meta->Latitude.'" /> <meta property="place:location:latitude" content="'.$Meta->Latitude.'" />';
				$Meta->LongOGHtml ='<meta property="og:longitude" content="'.$Meta->Longitude.'" /> <meta property="place:location:longitude content="'.$Meta->Longitude.'" />';					
			}
			
			if($mediaOG == null || ($mediaOG->ImageCroppedSrc == null && $mediaOG->ImageLargeSrc == null) && get_current_blog_id() != 1)
			{							
				require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");				
				$destaque = new Destaque();
				$destaques = $destaque->GetDestaques(1);
				$mediaOG = $destaques[0];
						
			}	
			
			if($mediaOG == null || ($mediaOG->ImageCroppedSrc == null && $mediaOG->ImageLargeSrc == null))//if media og was not defined above
			{				
				//if is still not defined (is main site or has some problem in media), set default logo as media og				
				require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/ImagemVO.php');	
				$mediaOG = new ImagemVO();
				$mediaOG->ImageCroppedSrc = get_bloginfo('url').'/fazendo_as_malas.jpg';//todo change to jpg imagem with new logo style
				$MediaOGUrl = $mediaOG->ImageCroppedSrc;
				$Meta->MediaOGHtml = "<meta property='og:image' content='".$MediaOGUrl."' />";
				$Meta->ImgSrc = $MediaOGUrl;				
			}				
		}
		
		if(strpos($mediaOG->MimeType,"video") === 0)//if is video
		{	
			$Meta->DescriptionText = $mediaOG->Descricao." | Vídeo publicado no site de viagens ".get_bloginfo('name')." | Fazendo as Malas - Descobrindo o mundo. Viaje junto conosco.";		
			$MediaOGUrl = $mediaOG->MainUrl;
			$videoUrlParts = explode("embed", $mediaOG->MainUrl);
			
					
			$videoCode = trim($videoUrlParts[1],"/");
			$MediaOGUrl = str_replace("embed","v",$MediaOGUrl);
			$Meta->MediaOGHtml = "<meta property='og:video' content='".$MediaOGUrl."' />
			<meta property='og:image' content='http://img.youtube.com/vi/".$videoCode."/maxresdefault.jpg' />
			<meta property='og:type' content='video'>
			<meta property='og:video:type'content='application/x-shockwave-flash'>
			<meta property='og:video:width' content='398'>
			<meta property='og:video:height' content='224'>";
			$Meta->ImgSrc = "http://img.youtube.com/vi/".$videoUrlParts[1]."/maxresdefault.jpg";			
						
		}
		else//if media is image
		{	
				
			if($MediaOGUrl == null || strlen($MediaOGUrl) == 0)	
			{					
				$MediaOGUrl = $mediaOG->ImageLargeSrc;
				if($MediaOGUrl == null)
				{
					$MediaOGUrl = $mediaOG->OriginalImageVO->ImageLargeSrc;
				}
				if($MediaOGUrl == null)
				{
					$MediaOGUrl = get_bloginfo('url').'/fazendo_as_malas.jpg';
				}
				//try { $MediaOGUrl = @CropImage($mediaOG, 200);}	catch(Exception $e){}
			}		
			$Meta->MediaOGHtml = "<meta property='og:image' content='".$MediaOGUrl."' />";
			$Meta->ImgSrc = $MediaOGUrl;
		}	
				
	}
	
	public function GetOpenMedia()
	{
			
		if(isset($_GET["media"]) && $_GET["media"] != null)
		{	
			
			if( strpos($_GET["media"], "jpg")> -1 || strpos($_GET["media"], "png")> -1 )
			{	
				$mediaUrl = $_GET["media"];										
				$mediaUrl =	html_entity_decode($mediaUrl,null,'UTF-8');
				
				$mediaUrl = str_replace("__","/",$mediaUrl);							
				$filename = basename($mediaUrl);
					
				if(strpos($filename, "jpg") > -1 && strpos($filename, "-") > -1 &&  strpos($filename, "x") > -1 )
				{
					$fileSegments =explode('-', $filename);
					$originalFileName = $fileSegments[0].".jpg";
					$mediaGuiid = str_replace($filename,$originalFileName,$mediaUrl);
					return $this->GetMediaByGuid($mediaUrl);	
				} 
				else
				{
					if(strpos($mediaUrl, "/destaque_") > -1)
					{
						$urlParts = explode("/destaque_",$mediaUrl);
						$id_parts = explode("_", $urlParts[1]);
						$mediaId = $id_parts[0];
						$_position =strpos($urlParts[1],"_");
						$likeMediaGuiid = mb_substr($urlParts[1],$_position + 1);											
					}
					else
					{							
						$mediaGuiid = $mediaUrl;					
					}
					return $this->GetMediaByGuid($mediaGuiid);
				}			
			}
			else
			{		
				//ytv_-9db4lZEfMs	
				if(strpos($_GET["media"], "ytv_") > -1)
				{					
					$videoCode = str_replace("ytv_","",$_GET["media"]);
				}
				else
				{
						
					$mediaUrl = $_GET["media"];								
					$mediaUrl =	html_entity_decode($mediaUrl,null,'UTF-8');				
					$mediaUrl = str_replace("__","/",$mediaUrl);
					$mediaUrl = str_replace(";",":",$mediaUrl);
					$mediaUrl = str_replace("%3B",":",$mediaUrl);	
						
					$videoUrlParts = explode("embed", $mediaUrl);			
					$videoCode = trim($videoUrlParts[1],"/");	
				}			
				$mediaUrl = "http://www.youtube.com/embed/".$videoCode."";								
				return $this->GetMediaByGuid($mediaUrl);
			}
		}
		
		return $openMedia;			
		
	}
	
	/**
		* @return MediasVO[]
	*/
	public  function GetMedias($options) {	
		
		if($options["parentId"] == null && $this->MultiSiteData == true)
		{
			if($options["destaques_video"] == "yes")
			{
				$this->PostMimeType = "video/x-flv";				
			}
			if($options["media_mime_type"] != null)
			{
				$this->PostMimeType = $options["media_mime_type"];				
			}
			
			
			$mediasAlbum = $this->GetItens($options);
			return $mediasAlbum;
		}
		else
		{
			if($options["parentId"] != null)	
			{			
				$mediasAlbum = Galeria::GetMediasId($options);	//$options["prefix"]					
			}
			else
			{
				if($options["destaques_video"] == "yes")
				{
					$this->PostMimeType = "video/x-flv";				
				}
				$mediasAlbum = $this->GetItens($options);
					
			}		
				
			if(!is_array($mediasAlbum) )	
			{	
				if($mediasAlbum != null && $mediasAlbum != "")
				{
					$uniqueId = $mediasAlbum;
					$mediasAlbum = array();
					$mediasAlbum[] = $uniqueId;
				}
				else
				{
					$mediasAlbum = array();
				}
					
			}
				
			foreach($mediasAlbum as $mediaAlbum)
			{					
				$id = $options["parentId"] == null? $mediaAlbum->ID:$mediaAlbum;
							
				$type = get_post_mime_type($id);									
				$video = strpos($type,"video");					
				if(get_post($id) != null)
				{
					if($video === 0)
					{
						$Medias[] = new MediaVO($id);							
					}
					else
					{
						$Medias[] = Imagem::GetImage($id);									
					}	
				}						
			}	
				
			return $Medias;	
		}
				
	}	
	
	public static function InsertVideo($title, $url, $location =  null, $lat = null, $long = null)
	{
		$result = array();
		if($title && $title !='' && $url && $url != '')
		{	
			//http://www.youtube.com/watch?v=Rkphnurc4EQ
			if(strpos($url, "watch?v=") > 12 || (strpos($url, "youtu.be/") > -1 && (strlen($url) + 5) > strpos($url, "youtu.be/") ))
			{
				$urlparts = explode("watch?v=",$url);
				if(strpos($url, "youtu.be/") > -1)
				{
					$urlparts = explode("youtu.be/",$url);
				}
					
				$embedCode = $urlparts[1];
				$embedurl = "http://www.youtube.com/embed/".$embedCode;				
				$attachment = array(
					 'guid' => $embedurl, 
					 'post_mime_type' => 'video/x-flv',
					 'post_title' => $title,
					 'post_content' => $title,
					 'post_status' => 'inherit',
					 'post_author'=>get_current_user_id(),
					 'post_excerpt'=>$title,
				  );
				$videoId = wp_insert_attachment($attachment);
				if($videoId != null && $videoId > 0)
				{
					$result["status"] = "success";
					$result["videoId"] = $videoId;
					if($location != null) {
						update_post_meta($videoId, 'attachment_location',$location);
					} else {
						delete_post_meta($videoId, 'attachment_location');
					}

					if($lat != null) {
						update_post_meta($videoId, 'attachment_latitude',$lat);

					} else {
						delete_post_meta($videoId, 'attachment_latitude');
					}

					if($long != null) {
						update_post_meta($videoId, 'attachment_longitude', $long);
					} else {
						delete_post_meta($videoId, 'attachment_longitude');
					}
				}											
			}
			else
			{
				$result["status"] = "error";
				$result["error"] = "invalid_url";
				$result["error_desc"] = "Url do vídeo inválida";				
			}			
		}
		else
		{
			$result["status"] = "error";
			$result["error"] = "parans_not_valid";
			$result["error_desc"] = "Algum campo contém valor inválido. Os dois campos são obrigatórios";						
		}
		
		return $result;
	}	
	
	public static function GetMedia($mediaId)
	{
			
			if(get_post($mediaId) != null)
			{
				$video = strpos(get_post_mime_type($mediaId),"video");										
				if($video === 0)
				{
					
					return new MediaVO($mediaId);
				}
				else
				{					
					$teste = new ImagemVO($mediaId);					
					return new ImagemVO($mediaId);
				}
			}
		
	}
	
	function GetMediaByGuid($guid)
	{		
		if($this->MultiSiteData)
		{
			$options["itens"] = 1;					
			$options["where"] = " and guid like '%".$guid."%'";						
			$openMedia = $this->GetMultiSiteContent("attachment", $options );	
			$openMedia = $openMedia[0];				
			return $openMedia;
					
		}
		else
		{
			global $wpdb;					
			$sql = "SELECT ID FROM ".$wpdb->posts." WHERE guid like '%".$guid."%' limit 0,1";	
			$media_ID =	$wpdb->get_var($sql);				
			$openMedia = $this->GetMedia($media_ID);						
			return $openMedia;	
					
		}
	}
}

?>