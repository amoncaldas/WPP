<?php

/**
 * class RelatoVO
 *
 * Description for class RelatoVO
 *
 * @author:Amon Caldas
*/

require_once(FAM_PLUGIN_PATH . '/includes/FAMCore/Data/DataAccess.php' );
require_once("ViajanteVO.php");
require_once("LocationVO.php");
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Media.php');

class RelatoVO extends DataAccess {

	public $RelatoId;
	public $RelatoUrl;
	public $Titulo;
	public $Categorias = array();
	public $Tags = array();
	public $Conteudo;
	public $Resumo;	
	public $Location;
	public $DataRelato;
	public $DataPublicacao;
	public $MidiaPrincipal;
	public $MidiasAnexadas;
	public $Autor;
	public $Status;
	public $Temperatura;
	public $OgTitle;
	public $SkeenName;
	public $SEODesc;

	
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

	/**
	 * RelatoVO constructor
	 *
	 * @param 
	 */
	function RelatoVO($id = null) {
		parent::DataAcess("relatos");
		if($id != null)
		{
			$this->GetRelatoById($id);
		}
	}
	
	public function PopulateRelatoVO(RelatoVO &$relatoVO, $post)
	{	   
							
		$relatoVO->Titulo = $post->post_title;
		$relatoVO->RelatoId = $post->ID;
		$relatoVO->RelatoUrl =  get_bloginfo('home')."/relatos/".$post->post_name."/".$post->ID."/";
		global $GetMultiSiteData;
		if($GetMultiSiteData == true)
		{
			$relatoVO->RelatoId .= ";".$post->blog_id;
			$relatoVO->RelatoUrl = get_site_url($post->blog_id)."/relatos/".$post->post_name."/".$post->ID."/";
			switch_to_blog($post->blog_id);
			$this->SkeenName = (get_option('tema_fam') == false)? 'adventure': get_option('tema_fam');
			restore_current_blog();
		}
		else
		{
			$this->SkeenName = (get_option('tema_fam') == false)? 'adventure': get_option('tema_fam');
		}
		
		$content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $post->post_content);				
		$relatoVO->Conteudo = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);	//replace caption close		
		
		
		
		wp_reset_query();	
		
		if(is_single($relatoVO->PostId))
		{
			
			if(is_fam_mobile())
			{
				
				$this->AdjustMedizSizeForMobileView($relatoVO);
			}
			else
			{
				
				$this->AdjustMedizSizeForPCView($relatoVO);
			}
			
		}	
		
		wp_reset_query();	
		
		if(is_single($relatoVO->RelatoId))
		{
			
			$padraoH2 = '/\<h2>.*?<\/h2>/';			
			if(preg_match_all($padraoH2,$relatoVO->Conteudo,$listaH2))
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
							$relatoVO->Conteudo = str_replace($h2,$h2add,$relatoVO->Conteudo);
						}
						$counter++;
					}				
				}
			}
		}
			
		
		
		$relatoVO->Resumo = GetSubContent($relatoVO->Conteudo,600);	
		$relatoVO->SEODesc = get_post_meta($post->ID, "seo_desc", true);
		$relatoVO->DataPublicacao =  $post->post_modified;
		$relatoVO->Autor =  new ViajanteVO($post->post_author);
		$relatoVO->Status = $post->post_status;
		
		$cats = (get_the_category($post->ID));
		if(is_array($cats) && count($cats))	
		{
			foreach((get_the_category($post->ID)) as $cat) {
				$categoriaVO = new CategoriaVO();
				$categoriaVO->CategoriaID = $cat->cat_ID;
				$categoriaVO->CategoriaDescricao = $cat->cat_name;
				$relatoVO->Categorias[] = 	$categoriaVO;
			}
		}
		
		$tags = (get_the_tags($post->ID));
		if(is_array($tags) && count($tags) > 0)
		{
			foreach($tags as $tag) {			
				$tagVO  = new TagVO();
				$tagVO->TagID = $tag->term_id;
				$tagVO->TagDescricao = $tag->name;
				$relatoVO->Tags[] = $tagVO;
			}
		}	
		
		$medias_anexadas = array();
		preg_match_all('/wp-image-\d+/', $relatoVO->Conteudo, $matches);
					
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
				$relatoVO->MidiasAnexadas[] = $mediaBO->GetMedia($attachment);				
			}			
		}
		
		//recupera a imagem principal do relato		
		$midiaId = get_post_meta($post->ID, "_fam_upload_id_", true);
		
		if($midiaId != null && $midiaId != "0")
		{			
			$relatoVO->MidiaPrincipal = Media::GetMedia($midiaId);				
		}
	
		if($relatoVO->MidiaPrincipal->ImageFullSrc == null && is_array($relatoVO->MidiasAnexadas) && count($relatoVO->MidiasAnexadas) > 0)
		{
			$relatoVO->MidiaPrincipal = $relatoVO->MidiasAnexadas[0];						
		}		
				
		if($relatoVO->MidiaPrincipal->ImageFullSrc == null)
		{
			$relatoVO->MidiaPrincipal = $relatoVO->Autor->UserImage;
		}		
		
		$relatoVO->Location = new LocationVO();		
		
		$relatoVO->Temperatura = get_post_meta($post->ID, "temperatura", true);
		$relatoVO->Location->Local = get_post_meta($post->ID, "local", true);
		$relatoVO->Location->Latitude = get_post_meta($post->ID, "latitude", true);
		$relatoVO->Location->Longitude = get_post_meta($post->ID, "longitude", true);				
		$relatoVO->DataRelato = get_post_meta($post->ID, "data_de_visita", true);
		
		
		if($relatoVO->Location->Local != null)
		{
			$relatoVO->OgTitle = $relatoVO->Titulo." | Em ". $relatoVO->Location->GetLocalSubString(50)." | Por ".$relatoVO->Autor->FullName;			
		}
		else
		{
			$relatoVO->OgTitle = $relatoVO->Titulo." | Por ".$relatoVO->Autor->FullName;
		}		
		
	}
	
	/**
	 * @return RelatoVO[]
	*/
	public function GetRelatoVOList($postsRelato)
	{		
		$relatos = array();
		foreach($postsRelato as $postRelato)
		{			
			$relatoVO = new RelatoVO();
			$relatoVO->PopulateRelatoVO($relatoVO,$postRelato);			
			$relatos[] = $relatoVO;						
		}		
		return $relatos;
	}
	
	private function GetRelatoById($id)
	{		
		$post = parent::GetById($id);				
		$this->PopulateRelatoVO($this,$post);			
		return $this;
	}	
	
	
	private function AdjustMedizSizeForMobileView(RelatoVO $relatoVO)
	{
		$padraoImagem = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg]/';
		if(preg_match_all($padraoImagem,$relatoVO->Conteudo,$lista))
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
						$relatoVO->Conteudo = str_replace($imagemVO->ImageLargeSrc,$imagemVO->ImageMediumSrc,$relatoVO->Conteudo);
						$relatoVO->Conteudo = str_replace($imagemVO->ImageFullSrc,$imagemVO->ImageMediumSrc,$relatoVO->Conteudo);
					}
					catch(exception $ex){}	
				}				
			}	
		}			
			
		$padraoWidth = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg].*\s(width="580")/';
		if(preg_match_all($padraoWidth,$relatoVO->Conteudo,$listaw))
		{				
			for($i=0;$i<=sizeof($listaw[0]);$i++)
			{		
				$aux = 	str_replace ('width="580"','width="300"',$listaw[0][$i]);				
				$relatoVO->Conteudo = str_replace( $listaw[0][$i],$aux,$relatoVO->Conteudo);
			}	
		}
		$padraoWidth = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg].*\s(width="570")/';
		if(preg_match_all($padraoWidth,$relatoVO->Conteudo,$listaw))
		{				
			for($i=0;$i<=sizeof($listaw[0]);$i++)
			{		
				$aux = 	str_replace ('width="570"','width="300"',$listaw[0][$i]);				
				$relatoVO->Conteudo = str_replace( $listaw[0][$i],$aux,$relatoVO->Conteudo);
			}	
		}
		
		$padraoWidthIframe = '/\<iframe.*?(width="\d+)"/';
		
		if(preg_match_all($padraoWidthIframe,$relatoVO->Conteudo,$listaif))
		{				
			if(count($listaif) == 3)
			{
				$newIframeHtml =  str_replace($listaif[2],"100%",$listaif[0]);					
				$relatoVO->Conteudo = str_replace($listaif[0],$newIframeHtml,$relatoVO->Conteudo);
			}
			
			elseif(count($listaif) == 2)
			{
				$newIframeHtml =  str_replace($listaif[count($listaif) - 1][0],"width='100%'",$listaif[0]);
				$relatoVO->Conteudo = str_replace($listaif[0],$newIframeHtml,$relatoVO->Conteudo);
			}
		}
	}
	
	private function AdjustMedizSizeForPCView(RelatoVO $relatoVO)
	{
		$padraoImagem = '/src="(.*[jpg|png])"/';		
		if(preg_match_all($padraoImagem,$relatoVO->Conteudo, $lista))
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
							$relatoVO->Conteudo = str_replace($imagemVO->ImageMediumSrc, $imagemVO->ImageLargeSrc,$relatoVO->Conteudo);
						}					
					}
					catch(exception $ex){}	
				}				
			}	
		}			
			
		
		$padraoWidth = '/http:\/\/.*?\.(?!-300x[\d]\.[png|jpg])[png|jpg].*\s([width="(\d)"]).*\s([height="(\d)"]*)/';
		if(preg_match_all($padraoWidth,$relatoVO->Conteudo,$listaw))
		{					
			for($i=0;$i<=sizeof($listaw[0]);$i++)
			{	
				try
				{			
					$aux = preg_replace ('/width="\d+"/','width="570"',$listaw[0][$i]);				
					$aux = preg_replace('/height="\d+"/', "", $aux);										
					$relatoVO->Conteudo = str_replace( $listaw[0][$i],$aux,$relatoVO->Conteudo);
				}
				catch(exception $ex){}
			}	
		}		
	}
	
	
}

?>