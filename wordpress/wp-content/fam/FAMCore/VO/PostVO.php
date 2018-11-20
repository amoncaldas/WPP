<?php

/**
 * class PostVO
 *
 * Description for class PostVO
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/Data/DataAccess.php' );
require_once( ABSPATH . '/FAMCore/BO/Media.php' );
require_once( ABSPATH . '/FAMComponents/widget.php' );
require_once("ViajanteVO.php");
require_once("LocationVO.php");

class PostVO extends DataAccess {
	
	public $PostId;
	public $PostUrl;
	public $Titulo;
	public $Categorias = array();
	public $Tags = array();
	public $Conteudo;
	public $Resumo;	
	public $Location;
	public $DataPost;
	public $DataPublicacao;
	public $MidiaPrincipal;
	public $MidiasAnexadas;
	public $Autor;
	public $Status;
	public $OGTitle;
	public $SEODesc;

	/**
	 * PostVO constructor
	 *
	 * @param 
	 */
	function PostVO($post_type, $id= null) {
		parent::DataAcess($post_type);
		if($id != null)
		{
			$this->GetPostById($id);
		}
	}	
	
	private function GetPostById($id)
	{		
		$post = parent::GetById($id);				
		$this->PopulatePostVO($this,$post);			
		return $this;
	}	
	
	public function GetSubContent($lenght)
	{
		$content = preg_replace('/\<h[\d]>.*?<\/h[\d]>/', '', $this->Conteudo);		
		$content = strip_tags($content, '');		
		if(strlen($content) > $lenght)
		{			
			if(strpos($content, " ", $lenght) > $lenght)
			{
				return substr($content, 0, strpos($content, " ", $lenght))." [...]";
			}
			else
			{
				$lenght = ($lenght > 20)? ($lenght -20): 0;								
				return substr($content, 0, strpos($content, " ", $lenght))." [...]";
			}				
		}
		else
		{
			return $content;
		}			
	}
	
	public function PopulatePostVO(PostVO &$postVO, $post)
	{	   
							
		$postVO->Titulo = $post->post_title;
		$postVO->PostId = $post->ID;
		$postVO->PostUrl =  get_permalink($post->ID);
		$blog_id = get_current_blog_id();
		if($post->blog_id != null)
		{
			$blog_id = $post->blog_id;					
		}
			
		$post_type = $post->post_type;
		if($post->post_type == "blog_post") $post_type ="blog";
		if($post->post_type == "atualizacao") $post_type ="status";	
		$postVO->PostUrl =  get_site_url($blog_id)."/".$post_type."/".$post->post_name."/".$post->ID."/";	
		
		$postVO->Conteudo = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $post->post_content);				
		$postVO->Conteudo = preg_replace('/\[\/caption\]/', '</div></div>', $postVO->Conteudo);	//replace caption close
			
		
		wp_reset_query();	
		
			
		if(is_single($postVO->PostId))
		{
			
			if(is_fam_mobile())
			{
				
				$this->AdjustMedizSizeForMobileView($postVO);
			}
			else
			{
				
				$this->AdjustMedizSizeForPCView($postVO);
			}
			
		}
		
		wp_reset_query();		
		if(is_single($postVO->PostId))
		{
			$padraoH2 = '/\<h2>.*?<\/h2>/';			
			if(preg_match_all($padraoH2,$postVO->Conteudo,$listaH2))
			{			
				if(count($listaH2) > 0 && is_array($listaH2) && count($listaH2[0]) > 0)
				{
					$counter = 1;
					foreach($listaH2[0] as $h2)
					{					
						if($counter == 1 || $counter == 3 || count($listaH2[0]) < 3)
						{						
							ob_start();
							widget::Get("add_responsive", array('float'=>'left','margin_top'=>"10px")); 
							$output = ob_get_clean();
							$h2add = $output.$h2;						
							$postVO->Conteudo = str_replace($h2,$h2add,$postVO->Conteudo);
						}
						$counter++;
					}
					
				}
			}/*
			
			$padraoEmptyPAfeterImg = '/\<div class="caption_wrapper"\>\<div class="caption">.*\<\/div\>\<\/div\>[\r\n]*\<p\>&nbsp;\<\/p\>';			
			if(preg_match_all($padraoEmptyPAfeterImg, $postVO->Conteudo,$listaP))
			{		
					
				if(count($listaP) > 0 && is_array($listaP) && count($listaP[0]) > 0)
				{					
					foreach($listaP[0] as $p)
					{	
						try
						{
							$pWihoutEmptyP = str_replace("<p>&nbsp;</p>","",$p);
							$postVO->Conteudo = str_replace($p,$pWihoutEmptyP,$postVO->Conteudo);	
						}
						catch(exception $ex){}						
					}
					
				}
			}
			*/
		}
		
					
		
		$postVO->Resumo = $post->post_excerpt;
		if($postVO->Resumo == null)
		{
			$postVO->Resumo = GetSubContent($postVO->Conteudo,600);	
		}
		$postVO->DataPublicacao =  $post->post_modified;
		$postVO->Autor =  new ViajanteVO($post->post_author);
		$postVO->Status = $post->post_status;
		$postVO->SEODesc = get_post_meta($post->ID, "seo_desc", true);
		
		$cats = (get_the_category($post->ID));
		if(is_array($cats) && count($cats))	
		{
			foreach((get_the_category($post->ID)) as $cat) {
				$categoriaVO = new CategoriaVO();
				$categoriaVO->CategoriaID = $cat->cat_ID;
				$categoriaVO->CategoriaDescricao = $cat->cat_name;
				$postVO->Categorias[] = 	$categoriaVO;
			}
		}
		
		$tags = (get_the_tags($post->ID));
		if(is_array($tags) && count($tags) > 0)
		{
			foreach($tags as $tag) {			
				$tagVO  = new TagVO();
				$tagVO->TagID = $tag->term_id;
				$tagVO->TagDescricao = $tag->name;
				$postVO->Tags[] = $tagVO;
			}
		}	
		
		//recupera as imagens do post	
		
		$medias_anexadas = array();
		preg_match_all('/wp-image-\d+/', $postVO->Conteudo, $matches);
					
		if($matches != null && is_array($matches) && count($matches) > 0 && is_array($matches[0]) && count($matches[0]) > 0)
		{			
			foreach($matches[0] as $imgid)
			{
				$id = str_replace("wp-image-","",$imgid);						
				
				if(!in_array($id,$medias_anexadas))
				{
					$medias_anexadas[] = $id;	
				}						
								
			}				
		}
			
		
		if ( is_array($medias_anexadas) && count($medias_anexadas) > 0)
		{
			$mediaBO = new Media();
			foreach ($medias_anexadas as $attachment)
			{				
				$postVO->MidiasAnexadas[] = $mediaBO->GetMedia($attachment);				
			}			
		}
		
		$midiaId = get_post_meta($post->ID, "_fam_upload_id_", true);
		
		if($midiaId == null || $midiaId == "0")
		{
			$midiaId = get_post_thumbnail_id($post->ID);			
		}
		if($midiaId == null || $midiaId == "0")
		{
			preg_match_all('/wp-image-\d+/', $relatoVO->Conteudo, $matches);				
			if($matches != null && is_array($matches) && count($matches) > 0)
			{
				foreach($matches as $imgid)
				{
					$id = str_replace("wp-image-","",$matches[0]);						
					if(is_array($id))
					{
						$midiaId = $id[0];							
						break;
					}
				}					
			}
				
		}			
			
		$midiaId = get_post_meta($post->ID, "_fam_upload_id_", true);
		if($midiaId != null && $midiaId != "0")
		{			
			$postVO->MidiaPrincipal = Media::GetMedia($midiaId);			
		}
        	
		if($postVO->MidiaPrincipal->ImageFullSrc == null && is_array($postVO->MidiasAnexadas) && count($postVO->MidiasAnexadas) > 0)
		{
			$postVO->MidiaPrincipal = $postVO->MidiasAnexadas[0];						
		}		
				
		if($postVO->MidiaPrincipal->ImageFullSrc == null)
		{
			$postVO->MidiaPrincipal = $postVO->Autor->UserImage;
		}		
				
		$postVO->Location = new LocationVO();		
		
		
		$postVO->Location->Local = get_post_meta($post->ID, "local", true);
		$postVO->Location->Latitude = get_post_meta($post->ID, "latitude", true);
		$postVO->Location->Longitude = get_post_meta($post->ID, "longitude", true);				
		$postVO->DataPost = get_post_meta($post->ID, "data_de_visita", true);
		
		if( ($postVO->MidiaPrincipal == null)) 
		{
			$postVO->MidiaPrincipal =  $postVO->Autor->UserImage;
		}	
		
		$postVO->OgTitle = $postVO->Titulo." | Por ".$postVO->Autor->FullName;
		
	}
	
	private function AdjustMedizSizeForMobileView(PostVO $postVO)
	{
		$padraoImagem = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg]/';
		if(preg_match_all($padraoImagem,$postVO->Conteudo,$lista))
		{				
			for($i=0;$i<=sizeof($lista[0]);$i++)
			{
				$imageGuid = $lista[0][$i];
				if($imageGuid != null)
				{
					try
					{
						$mediaBO = new Media();					
						$imagemVO = $mediaBO->GetMediaByGuid($imageGuid);
						$postVO->Conteudo = str_replace($imagemVO->ImageLargeSrc,$imagemVO->ImageMediumSrc,$postVO->Conteudo);
						$postVO->Conteudo = str_replace($imagemVO->ImageFullSrc,$imagemVO->ImageMediumSrc,$postVO->Conteudo);
					}
					catch(exception $ex){}	
				}				
			}	
		}			
			
		$padraoWidth = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg].*\s(width="580")/';
		if(preg_match_all($padraoWidth,$postVO->Conteudo,$listaw))
		{				
			for($i=0;$i<=sizeof($listaw[0]);$i++)
			{		
				$aux = 	str_replace ('width="580"','width="300"',$listaw[0][$i]);				
				$postVO->Conteudo = str_replace( $listaw[0][$i],$aux,$postVO->Conteudo);
			}	
		}
		$padraoWidth = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg].*\s(width="570")/';
		if(preg_match_all($padraoWidth,$postVO->Conteudo,$listaw))
		{				
			for($i=0;$i<=sizeof($listaw[0]);$i++)
			{		
				$aux = 	str_replace ('width="570"','width="300"',$listaw[0][$i]);				
				$postVO->Conteudo = str_replace( $listaw[0][$i],$aux,$postVO->Conteudo);
			}	
		}
		
		$padraoWidthIframe = '/\<iframe.*?(width="\d+)"/';
		
		if(preg_match_all($padraoWidthIframe,$postVO->Conteudo,$listaif))
		{				
			if(count($listaif) == 3)
			{
				$newIframeHtml =  str_replace($listaif[2],"100%",$listaif[0]);					
				$postVO->Conteudo = str_replace($listaif[0],$newIframeHtml,$postVO->Conteudo);
			}
			
			elseif(count($listaif) == 2)
			{
				$newIframeHtml =  str_replace($listaif[count($listaif) - 1][0],"width='100%'",$listaif[0]);
				$postVO->Conteudo = str_replace($listaif[0],$newIframeHtml,$postVO->Conteudo);
			}
		}
	}
	
	private function AdjustMedizSizeForPCView(PostVO $postVO)
	{
		$padraoImagem = '/src="(.*[jpg|png])"/';
		
		if(preg_match_all($padraoImagem,$postVO->Conteudo, $lista))
		{				
			
			for($i=0;$i<=sizeof($lista[1]);$i++)
			{
				$imageGuid = $lista[1][$i];
				
						
				if($imageGuid != null)
				{
					try
					{						
						$imageGuid = preg_replace('/-300x(\d+)/', "", $imageGuid);
						$imageGuid = preg_replace('/-(\d+)x300/', "", $imageGuid);	
											
						$mediaBO = new Media();					
						$imagemVO = $mediaBO->GetMediaByGuid($imageGuid);
						
						if($imagemVO != null)
						{
							$postVO->Conteudo = str_replace($imagemVO->ImageMediumSrc, $imagemVO->ImageLargeSrc,$postVO->Conteudo);
						}
						
						
					}
					catch(exception $ex){}	
				}				
			}	
		}			
			
		
		$padraoWidth = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg].*\s([width="(\d)"]).*\s([height="(\d)"]*)/';
		if(preg_match_all($padraoWidth,$postVO->Conteudo,$listaw))
		{		
					
			for($i=0;$i<=sizeof($listaw[0]);$i++)
			{	
				try
				{			
					$aux = preg_replace ('/width="\d+"/','width="570"',$listaw[0][$i]);				
					$aux = preg_replace('/height="\d+"/', "", $aux);										
					$postVO->Conteudo = str_replace( $listaw[0][$i],$aux,$postVO->Conteudo);
				}
				catch(exception $ex){}
			}	
		}		
		
	}
	
	
	/**
	 * @return RelatoVO[]
	*/
	public function GetPostVOList($posts)
	{		
		$postsVO = array();
		foreach($posts as $post)
		{			
			$postVO  = new PostVO($this->PostType);
			$this->PopulatePostVO($postVO,$post);			
			$postsVO[] = $postVO;						
		}	
			
		return $postsVO;
	}
}

?>