<?php

/**
 * class AlbumVO
 *
 * Description for class AlbumVO
 *
 * @author:
*/

require_once(FAM_PLUGIN_PATH . '/includes/FAMCore/Data/DataAccess.php' );
require_once(ABSPATH. "/FAMCore/BO/Imagem.php");
require_once(ABSPATH. "/FAMCore/BO/Galeria.php");
require_once("CategoriaVO.php");
require_once("TagVO.php");
require_once("ViajanteVO.php");
require_once("LocationVO.php");



class AlbumVO extends DataAccess {

	public $AlbumId;
	public $AlbumUrl;
	public $Titulo;
	public $Categorias = array();
	public $Tags = array();
	public $Resumo;	
	public $Location;
	public $MediasAlbum = array();
	public $MidiaPrincipal;
	public $DataPublicacao;
	public $Autor;
	public $Status;
	public $GetOnlyCover = false;
	public $MediasCount;
	public $OgTitle;

	/**
	 * AlbumVO constructor
	 *
	 * @param 
	 */
	function AlbumVO($id = null, $onlyCover = null) {
		parent::DataAcess("albuns");
		if($id != null)
		{
			$this->GetOnlyCover = $onlyCover;
			$this->GetAlbum($id);
		}
	}
	
	private function GetAlbum($id)
	{		
		$post = parent::GetById($id);					
		$this->PopulateAlbumVO($this,$post);					
		return $this;
	}
	
	public function GetAlbumItens($idAlbum, $itens)
	{		
		$post = parent::GetById($idAlbum);					
		$this->PopulateAlbumVO($this,$post, $itens);								
		return $this;
	}	
	
	private function SortCompare($a, $b)
	{		
		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? -1 : 1;
	}
	
	public function PopulateAlbumVO(AlbumVO &$albumVO, $post, $itens = null)
	{		
		$albumVO->Titulo = $post->post_title;
		$albumVO->AlbumId = $post->ID;
		$albumVO->AlbumUrl = get_bloginfo('home')."/albuns/".$post->post_name."/".$post->ID."/";
		global $GetMultiSiteData;
		if($GetMultiSiteData == true)
		{			
			$albumVO->AlbumId .= ";".$post->blog_id;
			$albumVO->AlbumUrl = get_site_url($post->blog_id)."/albuns/".$post->post_name."/".$post->ID."/";
		}
		
		
		$albumVO->Resumo = get_post_meta($post->ID, "descricao_album", true);
		$albumVO->DataPublicacao =  $post->post_modified;
		$albumVO->Autor =  new ViajanteVO($post->post_author);
		$albumVO->Status = $post->post_status;	
				
		$cats = (get_the_category($post->ID));
		if(is_array($cats) && count($cats))	
		{
			foreach((get_the_category($post->ID)) as $cat) {
				$categoriaVO = new CategoriaVO();
				$categoriaVO->CategoriaID = $cat->cat_ID;
				$categoriaVO->CategoriaDescricao = $cat->cat_name;
				$albumVO->Categorias[] = 	$categoriaVO;
			}
		}
		
		$tags = (get_the_tags($post->ID));
		if(is_array($tags) && count($tags) > 0)
		{
			foreach($tags as $tag) {			
				$tagVO  = new TagVO();
				$tagVO->TagID = $tag->term_id;
				$tagVO->TagDescricao = $tag->name;
				$albumVO->Tags[] = $tagVO;
			}
		}	
				
		$albumVO->Location = new LocationVO();
		$albumVO->Location->Local = get_post_meta($post->ID, "local", true);
		$albumVO->Location->Latitude = get_post_meta($post->ID, "latitude", true);
		$albumVO->Location->Longitude = get_post_meta($post->ID, "longitude", true);		
		
		$imagemBO = new Imagem();
		
		$mediasAlbum = Galeria::GetMediasId(array('parentId'=>$albumVO->AlbumId));			
		
		$this->MediasCount = count($mediasAlbum);			

		//usort($mediasAlbum, array($this,"SortCompare"));			
		
		if(is_array($mediasAlbum) && count($mediasAlbum) > 0)
		{
			$exit = false;
			$counter = $itens;
			
			foreach($mediasAlbum as $mediaAlbumId)
			{		
				$contains = false;
				if(is_array($albumVO->MediasAlbum) && count($albumVO->MediasAlbum) > 0)
				{
					foreach($albumVO->MediasAlbum as $mediaAdicionada )	
					{
						if($mediaAlbumId == $mediaAdicionada->MediaId)
						{
							$contains = true;
						}
					}
				}
					
				if(!$contains)
				{
					if($counter != NULL)
					{
						$counter--;
					}
					
					$isCover = get_post_meta($mediaAlbumId, '_fam_upload_is_cover_', true) == "cover"? true: false;
									
					if(strpos(get_post_mime_type($mediaAlbumId), "video") > -1)
					{												
						$MediaItemVO = new MediaVO($mediaAlbumId);
						$albumVO->MediasAlbum[] = $MediaItemVO;							
						
						if($isCover == true)
						{								
							$imagemVOVideo = new ImagemVO();
							$imagemVOVideo->ImageGaleryThumbSrc = $MediaItemVO->YoutubeBaseThumbUrl."hqdefault.jpg";
							$imagemVOVideo->ImageThumbSrc = $MediaItemVO->YoutubeBaseThumbUrl."default.jpg";
							$imagemVOVideo->ImageLargeSrc = $MediaItemVO->YoutubeBaseThumbUrl."maxresdefault.jpg";
							$imagemVOVideo->ImageFullSrc = $MediaItemVO->YoutubeBaseThumbUrl."maxresdefault.jpg";
														
							$imagemVOVideo->MainUrl = $MediaItemVO->MainUrl;
							$imagemVOVideo->MimeType = "video";
							$albumVO->MidiaPrincipal = $imagemVOVideo;													
							$isCover = false;		
						}								
					}
					else
					{										
						$albumVO->MediasAlbum[] = Imagem::GetImage($mediaAlbumId);	
						
						//se o album nao tiver capa definida, seta a primera imagem como capa	
						if( $isCover == true)
						{											
							$albumVO->MidiaPrincipal =  new ImagemVO($mediaAlbumId);														
						}
						
						//se for somente para recuperar a capa do album, e a capa ja estiver definifa sai do loop
						if($this->GetOnlyCover == true && $albumVO->MidiaPrincipal->MediaId != null)
						{							
							$exit = true;										
						}						
					}	
				}
				
				if($counter < 1)
				{
					break;
				}
				if($exit == true)
				{
					break;
				}			
			}
			
			if( $albumVO->MidiaPrincipal->ImageGaleryThumbSrc == null)
			{		
				$coverFiund = false;
				foreach($mediasAlbum as $mediaAlbumId)
				{
					$isCover = get_post_meta($mediaAlbumId, '_fam_upload_is_cover_', true) == "cover"? true: false;
				
					if($isCover == true)
					{	
						if(strpos(get_post_mime_type($mediaAlbumId), "video") > -1)
						{												
							$MediaItemVO = new MediaVO($mediaAlbumId);													
							$imagemVOVideo = new ImagemVO();
							$imagemVOVideo->ImageGaleryThumbSrc = $MediaItemVO->YoutubeBaseThumbUrl."hqdefault.jpg";
							$imagemVOVideo->ImageThumbSrc = $MediaItemVO->YoutubeBaseThumbUrl."default.jpg";
							$imagemVOVideo->ImageLargeSrc = $MediaItemVO->YoutubeBaseThumbUrl."maxresdefault.jpg";
							$imagemVOVideo->ImageFullSrc = $MediaItemVO->YoutubeBaseThumbUrl."maxresdefault.jpg";
							$imagemVOVideo->MainUrl = $MediaItemVO->MainUrl;
							$imagemVOVideo->MimeType = "video";
							$albumVO->MidiaPrincipal = $imagemVOVideo;		
															
						}	
						else
						{
							$albumVO->MidiaPrincipal = new ImagemVO($mediaAlbumId);
						}	
						$coverFound = true;
					}	
				}	
				
				if($coverFound == false)
				{
					if(strpos(get_post_mime_type($albumVO->MediasAlbum[0]->MediaId), "video") > -1)
					{													
						$imagemVOVideo = new ImagemVO();
						$imagemVOVideo->ImageGaleryThumbSrc = $albumVO->MediasAlbum[0]->YoutubeBaseThumbUrl."hqdefault.jpg";
						$imagemVOVideo->ImageThumbSrc = $albumVO->MediasAlbum[0]->YoutubeBaseThumbUrl."default.jpg";
						$imagemVOVideo->ImageLargeSrc = $albumVO->MediasAlbum[0]->YoutubeBaseThumbUrl."maxresdefault.jpg";
						$imagemVOVideo->ImageFullSrc = $albumVO->MediasAlbum[0]->YoutubeBaseThumbUrl."maxresdefault.jpg";
						$imagemVOVideo->MainUrl = $albumVO->MediasAlbum[0]->MainUrl;
						$imagemVOVideo->MimeType = "video";							
						$albumVO->MidiaPrincipal = $imagemVOVideo;
					}	
					else
					{
						$albumVO->MidiaPrincipal = $albumVO->MediasAlbum[0];
					}	
				}			
																		
			}
			
		}	
		if($albumVO->Location->Local != null)
		{
			$albumVO->OgTitle = $albumVO->Titulo." | Em ". $albumVO->Location->GetLocalSubString(50)." | Por ".$albumVO->Autor->FullName;			
		}
		else
		{
			$albumVO->OgTitle = $albumVO->Titulo." | Por ".$albumVO->Autor->FullName;
		}	
		
		
	}
	
	/**
	 * @return AlbumVO[]
	*/
	public function GetAlbumVOList($postsAlbum)
	{		
		$albuns = array();
		foreach($postsAlbum as $postAlbum)
		{							
			$albumVO = new AlbumVO();
			$albumVO->GetOnlyCover = $this->GetOnlyCover;
			$albumVO->PopulateAlbumVO($albumVO,$postAlbum);	
					
			$albuns[] = $albumVO;	
			
		}	
		
		return $albuns;
	}
}

?>