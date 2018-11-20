<?php


/**
 * class Conteudo
 *
 * Description for class Conteudo
 *
 * @author:
*/
abstract class Conteudo  {
	
	private $PostType;
	public $PostMimeType;
	public $MultiSiteData;

	/**
	 * Conteudo constructor
	 *
	 * @param 
	 */
	function Conteudo($customPostType, $viagemId = null) {
		
		if(strpos($_SERVER["SERVER_NAME"],"teste.") === false)
		{
			error_reporting(0);
		}
			
		$this->MultiSiteData = false;
		global $GetMultiSiteData;		
		$this->MultiSiteData = $GetMultiSiteData;
			
		if($customPostType != null)
		{
			$this->PostType = $customPostType;
		}
		else
		{
			$this->PostType = "post";
		}
		
		if($viagemId != null && $viagemId > 0)
		{
			global $switched;
			switch_to_blog($viagemId, true); 
		}
		
	}		
	
	protected  function GetItens($options) {		
		
		if($this->MultiSiteData == true)
		{				
			if($options['orderby'] == 'rand')
			{
				$args['orderby'] = " rand() ";				
			}	
			
			return $this->GetMultiSiteContent($this->PostType, $options);
		}
		else
		{			
			global 	$wp_query;
			$sample_post = $options["default_archive"][0];										
			if(is_array($options["default_archive"]) && count($options["default_archive"]) >= $options["itens"] && $sample_post->post_type == $this->PostType)
			{
				$posts = array_slice($options["default_archive"], 0, $options["itens"]);														
			}
			else
			{					
				if($options['itens'] == null)
				{
					$options['itens'] = 2;
				}
					
				$args = array('post_type' => $this->PostType,'order' => 'DESC' ,'post_status'=>array('publish', 'inherit'));
				if($options['order'] != null)
				{
					$args['order'] = $options['order'];				
				}				
				
				if($options['orderby'] != null)
				{
					$args['orderby'] = $options['orderby'];				
				}
				else
				{
					$args['orderby'] = ' ID ';
				}
				if($this->PostMimeType != null)
				{
					$args['post_mime_type'] = $this->PostMimeType;			
				}			
				if($options['excluded_ids'] != null)
				{
					if(!is_array($options['excluded_ids']))
					{
						$options['excluded_ids'] = split(",",$options['excluded_ids']);										
					}
					if(is_array($options['excluded_ids']) && count($options['excluded_ids'] > 0))
					{
						$arrayIds = array();
						foreach($options['excluded_ids'] as $id)
						{						
							if(is_numeric($id))
							{							
								$arrayIds[] = $id;						
							}
						}					
					}
					if(is_array($arrayIds) && count($arrayIds) > 0)
					{
						$args['post__not_in'] = $arrayIds;
					}							
				}
				
				if($options['included_ids'] != null)
				{
					if(!is_array($options['included_ids']))
					{
						$options['included_ids'] = split(",",$options['included_ids']);										
					}
					if(is_array($options['included_ids']) && count($options['included_ids'] > 0))
					{
						$arrayIds = array();
						foreach($options['included_ids'] as $id)
						{						
							if(is_numeric($id))
							{							
								$arrayIds[] = $id;						
							}
						}					
					}
					if(is_array($arrayIds) && count($arrayIds) > 0)
					{
						$args['post__in'] = $arrayIds;
					}							
				}		
					
				if( $options['itens'] != null)
				{							
					$args['posts_per_page'] = $options['itens'];	
				}
				else
				{				
					$args['posts_per_page'] = 3;
				}	
				if($options["parentId"] != null)
				{
					$args["post_parent"] = $options["parentId"];
				}
				if($options["page"] != null && is_numeric($options["page"]))
				{
					$args["offset"] = ($options["itens"] * ($options["page"] -1));
				}
				if($options["authorId"] != null)
				{
					$args["author"] = $options["authorId"];
				}
				if($options["cat_id"] != null)
				{
					$args["category"] = $options["cat_id"];
				}
				if($options["cat_slug"] != null)
				{
					$args["category_name"] = $options["cat_slug"];
				}	
				
				$posts = get_posts($args);	
						
				
			}
			return $posts;	
		}				
	}	
		
	public static function SetMetas($screen, $contentObj = null, $title = null, $contentDesc = null)
	{		
		require_once(ABSPATH."/FAMCore/BO/Media.php");
			
		Media::SetMediaOG($screen,$contentObj);
		global $Meta;
		$Meta->Screen = $screen;
		require_once(ABSPATH."/FAMCore/BO/Viagem.php");
		$viagem = new Viagem(get_current_blog_id());
		$viagemId = get_current_blog_id();
		$Meta->KeyWords = "Fazendo as malas, viagens, aventureiros, grupo, grupo de aventureiros, viagem, turismo, roteiro de viagem, relato de viagem, aventura, europa de mochila, mochila, mala, malas, mochilas, blog de viagem, fotos de viagem, custo de viagem, viagem econômica, viagem de aventura, viagem entre amigos";						
		$Meta->KeyWords .= ", viagens pelo mundo, viagem de trem, viagem de barco, viagem de carro, viagem de moto,viajando pelo mundo, passaporte, dicas de viagem, fórum de viajantes, vídeos de viagem, visto, vistos, visto para brasileiro, imigração, milhas, passagem aérea, pontos múltiplus, tam, gol, aa, tap, klm, avianca, delta, airfrance, south african";
		$Meta->Autor = "Fazendo as Malas";
		$Meta->OGType = "website";
		$viajante = $contentObj;
		
		if($viajante->FullName == null)
		{
			$viajante = $contentObj->Autor;
		}
		
		if($viajante->FullName != null)		{
			
			$Meta->Autor = $viajante->FullName;
			$Meta->ArticlePublisher = '<meta property="article:publisher" content="https://www.facebook.com/fazendoasmalas" />';
			$Meta->OGType = "article";
		}
		
		$facebookProfile = $contentObj->Autor->UrlPerfilFacebook;
		if($facebookProfile == null)
		{
			$facebookProfile = $contentObj->UrlPerfilFacebook;
		}
		
		if($facebookProfile != null && strpos($facebookProfile ,"facebook.com/") === 0)
		{				
			$url = str_replace("https://www.","",$facebookProfile);
			$url = str_replace("http://www.","",$url);
			$url = "https://www.".$url;
			$Meta->AutorFacebookUrlOGHtml = "<meta property='article:author' content='".$url."' />";	
					
		}
		
		
		if($viagemId != 1)
		{
			if($viagem->DadosViagem->Roteiro != null && count($viagem->DadosViagem->Roteiro) > 0)
			{
				foreach ($viagem->DadosViagem->Roteiro as $trajeto)
				{
					if($trajeto->LocationPartida->Local != null && $trajeto->LocationChegada->Local != null )
					{
						$locais_e_transporte = trim($trajeto->LocationPartida->Local);				
						if(strpos($Meta->KeyWords,$locais_e_transporte) === false)
						{
							$Meta->KeyWords .= ",".$locais_e_transporte;
						}
							
						$locais_e_transporte = trim($trajeto->LocationChegada->Local);				
						if(strpos($Meta->KeyWords,$locais_e_transporte) === false)
						{
							$Meta->KeyWords .= ",".$locais_e_transporte;
						}
							
						$locais_e_transporte = trim($trajeto->transporte);
						if(strpos($Meta->KeyWords,$locais_e_transporte) === false)
						{
							$Meta->KeyWords .= ",".$locais_e_transporte;
						}	
					}							
				}
			}			
		}		
			
		switch($screen)
		{
			case "archive-relatos":		
				if(get_current_blog_id() == 1)	
				{
					$DescriptionText = "Relatos de viagens, aventuras e experiências incríveis pelo Brasil e pelo mundo | Fazendo as Malas  ";	
					$Meta->Title = "Relatos de viagens, aventuras e experiências incríveis pelo Brasil e pelo mundo | Fazendo as Malas  ";
				}
				else
				{			
					$DescriptionText = "Relatos, experiências e aventuras incríveis da viagem ".get_bloginfo('name')." | Fazendo as Malas  ";	
					$Meta->Title = "Relatos, experiências e aventuras incríveis da viagem ".get_bloginfo('name')." | Fazendo as Malas  ";	
				}
				break;
			case "archive-albuns":						
				$DescriptionText = "Albuns de fotos e vídeos de viagem publicados no site ".get_bloginfo('name').". Veja, comente, compartilhe e participe | Fazendo as Malas  ";	
				$Meta->Title = "Albuns de fotos e vídeos publicados no site ".get_bloginfo('name')." | Fazendo as Malas  .";	
				break;
			case "archive-atualizacao":		
				$DescriptionText = "Status da viagem ".get_bloginfo('name').". Acompanhe as publicações, locais atuais e vejas atividades dos viajantes | Fazendo as Malas  ";
				$Meta->Title = "Status da viagem ".get_bloginfo('name')." | Fazendo as Malas  ";
				break;
			case "single-atualizacao":		
				$DescriptionText = $contentObj->OgTitle." | ".$contentObj->Conteudo." |Status de viagem - Fazendo as Malas .";
				$Meta->Title = $contentObj->OgTitle." | Status de viagem - ".get_bloginfo('name')." | Fazendo as Malas  ";
				//check if lat and long a setted, and defines location ogs
				if($contentObj->Location->Latitude != null && $contentObj->Location->Longitude != null)
				{
					$Meta->Latitude = $contentObj->Location->Latitude;
					$Meta->Longitude = $contentObj->Location->Longitude;
					$Meta->LatOGHtml ='<meta property="og:latitude" content="'.$Meta->Latitude.'" /> <meta property="place:location:latitude" content="'.$Meta->Latitude.'" />';
					$Meta->LongOGHtml ='<meta property="og:longitude" content="'.$Meta->Longitude.'" /> <meta property="place:location:longitude content="'.$Meta->Longitude.'" />';					
				}
				
				$Meta->Cannonical = $contentObj->AtualizacaoUrl;
				break;
			case "archive-viagem":		
				$DescriptionText = "Viagem ".get_bloginfo('name')." | Fazendo as Malas | ".strip_tags($contentDesc, '');	
				$Meta->Title = get_bloginfo('name')." | Fazendo as Malas |  .";
				break;
			case "page-viagens":		
				$DescriptionText = "Viagens, aventuras e experiências pelo Brasil e pelo mundo. Descubra como foi cada viagem, os aprendizados e descobertas | Fazendo as Malas";	
				$Meta->Title = "Viagens, aventuras e experiências de viajantes pelo Brasil e pelo mundo | Fazendo as Malas";
				break;
			case "contato":		
				$DescriptionText = "Entre em contato com Fazendo as Malas. Nos envie um email, um tweet ou uma mensagem via Facebook | Fazendo as Malas";	
				$Meta->Title = "Contato com Fazendo as Malas. Envie email, tweet ou mensagem via Facebook | Fazendo as Malas";
				break;
			case "page":		
				$DescriptionText = $title."| Página Fazendo as Malas";	
				$Meta->Title = $title."| Página Fazendo as Malas";
				break;
			case "archive-videos":		
				$DescriptionText = $contentDesc;	
				$Meta->Title = $title;
				break;
			case "single-blog":	
			if($contentObj->SEODesc != null)
				{
					$DescriptionText = $contentObj->SEODesc. " | Fazendo as Malas  ";
				}	
				else
				{	
					$DescriptionText = $contentObj->OgTitle." | ".$contentObj->GetSubContent(300)." | Blog Fazendo as Malas";
				}
				$Meta->Title = $contentObj->OgTitle." | Blog FazendoAsMalas   ";
				//check if lat and long a setted, and defines location ogs
				if($contentObj->Location->Latitude != null && $contentObj->Location->Longitude != null)
				{
					$Meta->Latitude = $contentObj->Location->Latitude;
					$Meta->Longitude = $contentObj->Location->Longitude;
					$Meta->LatOGHtml ='<meta property="og:latitude" content="'.$Meta->Latitude.'" /> <meta property="place:location:latitude" content="'.$Meta->Latitude.'" />';
					$Meta->LongOGHtml ='<meta property="og:longitude" content="'.$Meta->Longitude.'" /> <meta property="place:location:longitude content="'.$Meta->Longitude.'" />';					
				}
				$Meta->Cannonical = $contentObj->PostUrl;
				break;
			case "archive-blog":		
				$DescriptionText = "Blog Fazendo as Malas. Viagens, destinos, aventuras e dicas. Acompanhe, comente, participe e veja as publicações | Fazendo as Malas  ";	
				$Meta->Title = "Blog Fazendo as Malas. Viagens, destinos, aventuras e dicas | Fazendo as Malas";
				break;
			case "archive-blog-category":		
				$DescriptionText = $title." | Categoria  do blog Fazendo as Malas. Viagens, destinos, aventuras e dicas. Acompanhe, comente, participe e veja as publicações | Fazendo as Malas  ";	
				$Meta->Title = $title." - Categoria do blog Fazendo as Malas. Viagens, destinos, aventuras e dicas de viagem | Fazendo as Malas";
				break;
			case "single-forum":		
				$DescriptionText = $contentObj->GetSubContent(100)." | Tópico de fórum | Fazendo as Malas";
				$Meta->Title = $contentObj->OgTitle." | Tópico de fórum - Fazendo as Malas  ";
				$Meta->Cannonical = $contentObj->PostUrl;
				break;
			case "archive-forum":		
				$DescriptionText = "Fórum para discussões sobre viagens, destinos, experiências e aventuras. Participe, converse, troque informações | Fazendo as Malas  ";	
				$Meta->Title = "Fórum sobre viagens, destinos, experiências e aventuras | Fazendo as Malas";
				break;
			case "archive-topics":		
				$DescriptionText = "Tópicos de fórum da categoria ".$title.". Participe, converse, troque infomrações sobre viagens, destinos, experiências e aventuras | Fazendo as Malas  ";	
				$Meta->Title = $title." | Tópicos de fórum da categoria | Fazendo as Malas";
				break;
			case "author":						
				$DescriptionText = $contentObj->UserProfile. " | ".$contentObj->FullName." - Viajante  ".get_bloginfo('name')." | Fazendo as Malas";
				if($viagemId != 1)	
				{
					$Meta->Title = $contentObj->FullName." - Viajante ".get_bloginfo('name')." | Fazendo as Malas";
				}
				else
				{
					$Meta->Title = $contentObj->FullName." - Viajante | Fazendo as Malas";
				}
				
				break;
			case "single-albuns":									
				$DescriptionText = $contentObj->Resumo." | Album de viagem  - ".get_bloginfo('name')." | Fazendo as Malas";;					
				$Meta->Title = $contentObj->OgTitle." | Album de viagem ".get_bloginfo('name')." | Fazendo as Malas";
				//check if lat and long a setted, and defines location ogs
				if($contentObj->Location->Latitude != null && $contentObj->Location->Longitude != null)
				{
					$Meta->Latitude = $contentObj->Location->Latitude;
					$Meta->Longitude = $contentObj->Location->Longitude;
					$Meta->LatOGHtml ='<meta property="og:latitude" content="'.$Meta->Latitude.'" /> <meta property="place:location:latitude" content="'.$Meta->Latitude.'" />';
					$Meta->LongOGHtml ='<meta property="og:longitude" content="'.$Meta->Longitude.'" /> <meta property="place:location:longitude content="'.$Meta->Longitude.'" />';					
				}
				$Meta->Cannonical = $contentObj->AlbumUrl;
				break;
			case "single-relatos":	
				if($contentObj->SEODesc != null)
				{
					$DescriptionText = $contentObj->SEODesc. " | Fazendo as Malas";
				}	
				else
				{				
					$DescriptionText = $contentObj->GetSubContent(100)." | ".get_bloginfo('name')." | Fazendo as Malas";	
				}
				$Meta->Title = $contentObj->OgTitle." | Relato de viagem - ".get_bloginfo('name')." | Fazendo as Malas";
				//check if lat and long a setted, and defines location ogs
				if($contentObj->Location->Latitude != null && $contentObj->Location->Longitude != null)
				{
					$Meta->Latitude = $contentObj->Location->Latitude;
					$Meta->Longitude = $contentObj->Location->Longitude;
					$Meta->LatOGHtml ='<meta property="og:latitude" content="'.$Meta->Latitude.'" /> <meta property="place:location:latitude" content="'.$Meta->Latitude.'" />';
					$Meta->LongOGHtml ='<meta property="og:longitude" content="'.$Meta->Longitude.'" /> <meta property="place:location:longitude content="'.$Meta->Longitude.'" />';					
				}
				$Meta->Cannonical = $contentObj->RelatoUrl;	
				break;
			case "search":		
				$siteame =	get_current_blog_id() == 1? "":get_bloginfo('name')." |";
				$DescriptionText = $title." - Resultados de busca | ".$siteame." Fazendo as Malas";;	
				$Meta->Title = $title." - Busca | ".$siteame." Fazendo as Malas";;	
				break;
			case "page-viajantes":						
				$DescriptionText = "Viajantes da viagem ".get_bloginfo('name').". Conheça cada participante, seu perfil e veja suas publicações | Fazendo as Malas";	
				$Meta->Title = "Viajantes da viagem ".get_bloginfo('name')." | Fazendo as Malas  .";	
				break;
			case "page_viajantes_fam":						
				$DescriptionText = "Viajantes Fazendo as Malas. Conheça cada participante, seu perfil e veja suas publicações | Fazendo as Malas";	
				$Meta->Title = "Viajantes | Fazendo as Malas  .";	
				break;
			case "404":						
				$DescriptionText = "Conteúdo não encontrado - ".get_bloginfo('name')." | Fazendo as Malas  ";	
				$Meta->Title = "Conteúdo não encontrado ".get_bloginfo('name')." | Fazendo as Malas";	
				break;
			case "index":
					$viagemDesc = "Grupo de aventureiros que faz as malas e viaja pelo mundo em busca de aventuras, cultura, experiências e novas descobertas. Relatos de viagens incríveis e dicas, fotos e vídeos";
				$Meta->Title = "Fazendo as Malas | Viagens pelo mundo, aventuras, cultura e novas descobertas.";					
				if($viagemId != 1)	
				{						
					$viagemDesc = get_bloginfo('name')." | Fazendo as Malas - ". str_replace("...","",$viagem->DadosViagem->GetSubContent(400))." - ".$viagemDesc;
					$Meta->Title = get_bloginfo('name')." | Fazendo as Malas";		
				}														
				$DescriptionText =  $viagemDesc;	
				break;					
		}
			
		
		if($_GET["media"] == null)
		{
			$Meta->DescriptionText = $DescriptionText;
		}
		elseif(strpos($Meta->Cannonical, "?media=") === false)
		{			
			$Meta->Cannonical .= "?media=".$_GET["media"];			
		}
			
	}
	
	public static  function GetMultiSiteContent($PostType, $options, $return_raw_post = false) {	
		
		global $wpdb;
		global $blog_id;
		$table_prefix = $wpdb->base_prefix;			
		$blog_list  = $wpdb->get_results( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"), ARRAY_A );	
		$itensAdded = 0;
		$postsData = array();
		$counter = 1;
		$sqlstr = "";
		foreach ($blog_list AS $blog) 
		{	
			$attachmentWhere = "";
			if($PostType == "attachment" )	
			{
				$attachmentWhere = " or post_status='inherit' ";
			}		
			
			//$sqlstr .= " SELECT wp_fam_posts.*, '1' as blog_id FROM wp_fam_posts  where (post_status='publish' ".$attachmentWhere.") and post_type = '".$PostType."' ";
			
			if ($blog['blog_id'] > 0) 
			{		
				if($counter > 1 )
				{					
					$sqlstr .= " union ";						
				}
				
				if($blog['blog_id'] == 1)
				{
					$sqlstr .= " SELECT wp_fam_posts.*, 1 as blog_id FROM wp_fam_posts where (post_status='publish' ".$attachmentWhere.") and post_type = '".$PostType."' ";
				}	
				else
				{						
					$sqlstr .= " SELECT ".$table_prefix .$blog['blog_id']."_posts.*, ".$blog['blog_id']." as blog_id from ".$table_prefix .$blog['blog_id']."_posts where (post_status='publish' ".$attachmentWhere.") and post_type = '".$PostType."' ";
				}
						
				if($options["parentId"] != null)
				{
					$sqlstr .= " and post_parent = '".$options["parentId"]."'";
				}
				
				if($options['excluded_ids'] != null)
				{
					if(!is_array($options['excluded_ids']))
					{
						$options['excluded_ids'] = split(",",$options['excluded_ids']);
					}
					if(is_array($options['excluded_ids']) && count($options['excluded_ids'] > 0))
					{
						foreach($options['excluded_ids'] as $id)
						{
							$ids = split(";", $id);
							if($blog['blog_id'] == $ids[1] && is_numeric($ids[0]))
							{																		
								$sqlstr .= " and ID <> ".$ids[0];								
							}
						}
					}	
					
										
				}
				if($options['included_ids'] != null)
				{
					if(!is_array($options['included_ids']))
					{
						$options['included_ids'] = split(",",$options['included_ids']);
					}
					if(is_array($options['included_ids']) && count($options['included_ids'] > 0))
					{
						$sqlstr .= " and ID IN ( 0 ";
						foreach($options['included_ids'] as $id)
						{
							$ids = split(";", $id);
							if($blog['blog_id'] == $ids[1] && is_numeric($ids[0]))
							{																		
								$sqlstr .= ", ".$ids[0];								
							}
						}
						$sqlstr .= " ) ";
					}	
				}
				if($options["authorId"] != null)
				{
					$sqlstr .= " and post_author = '".$options["authorId"]."'";
				}
				if($options["where"] != null)
				{
					$sqlstr .= " ".$options["where"];
				}
				
				if($options["destaques_video"] == "yes")
				{					
					$sqlstr .= " and post_mime_type = 'video/x-flv'";				
				}			
												
				$counter++;				
			}			
		}
		if($options["orderby"] != null)
		{
			if($options["orderby"] == "rand")
			{
				$options["orderby"] = "rand()";
			}
			$sqlstr .= " order by ".$options["orderby"] ." limit 0, ".$options["itens"];
		}
		else
		{
			$sqlstr .= " order by post_modified DESC limit 0, ".$options["itens"];
		}
		
		if($options["page"] != null && is_numeric($options["page"]))
		{			
			$sqlstr .= " offset ".($options["itens"] * ($options["page"] ));
		}
		
		
		$posts = $wpdb->get_results(($sqlstr));	
		
			
		
		if($return_raw_post)
		{
			return $posts;
		}	
		$data = array();
			
		if(is_array($posts) && count($posts) > 0)
		{	
			require_once( ABSPATH . '/FAMCore/VO/AtualizacaoVO.php' );
			require_once( ABSPATH . '/FAMCore/VO/RelatoVO.php' );
			require_once( ABSPATH . '/FAMCore/BO/Media.php' );
			require_once( ABSPATH . '/FAMCore/VO/ImagemVO.php' );
			require_once( ABSPATH . '/FAMCore/VO/DestaqueVO.php' );
			require_once( ABSPATH . '/FAMCore/VO/AlbumVO.php' );
			
			foreach($posts as $post)
			{
				$restoreBlog = "no";
				if($post->blog_id != null)
				{		
					switch_to_blog($post->blog_id);	
					$restoreBlog = "yes";		
				}
					
				switch($PostType)
				{
					case "relatos":
						$relatoVO = new RelatoVO();
						$relatoVO->PopulateRelatoVO($relatoVO,$post);	
						$data[] = $relatoVO;			
						break;
					case "Atualizacao":						
						$atualizacaoVO  = new AtualizacaoVO();
						$atualizacaoVO->PopulateAtualizacaoVO($atualizacaoVO,$post);	
						$data[] = $atualizacaoVO;			
						break;
					case "albuns":						
						$albumVO = new AlbumVO();
						$albumVO->PopulateAlbumVO($albumVO,$post);	
						$data[] = $albumVO;			
						break;
					case "Destaque":						
						$destaqueVO = new DestaqueVO();
						$destaqueVO->PopulateDestaqueVO($destaqueVO,$post);	
						$data[] = $destaqueVO;			
						break;
					case "attachment":										
						$mediaVO = Media::GetMedia($post->ID);																			
						$data[] = $mediaVO;									
						break;
				}
				
				if($restoreBlog == "yes")
				{		
					restore_current_blog();	
				}
			}
		}	
		return $data;					
	}	
	
	public static function GetSites($options)
	{		
		global $wpdb;
		
		$valid_sites_id = "";			
		$search_term = Conteudo::AdjusteSearchTerm($options["search_term"]);	
				
		$blog_list = $wpdb->get_results( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"), ARRAY_A );
				
		$likeBlog = Conteudo::GetSearchLike(" option_value like '%{term}%' ",$search_term);
		$likeViagemDesc = Conteudo::GetSearchLike(" post_content like '%{term}%'", $search_term); 
		
		$sqlBlogName;	
		$sqlLikeViagemDesc;
		
		foreach ($blog_list AS $blog) 
		{			
			if ($blog['blog_id'] != 1) 
			{
				$sqlstr = "SELECT count(ID) as has_destaque FROM wp_fam_".$blog['blog_id']."_posts WHERE post_type = 'destaque' and post_status = 'publish'";				
					
				$destaques = $wpdb->get_var($sqlstr);				
				if($destaques > 0)
				{
					$trajetos = $wpdb->get_var("SELECT count(*) FROM wp_fam_trajetos WHERE viagem_id = '".$blog['blog_id']."'");
					if($trajetos > 0)
					{
						if($valid_sites_id != "")
						{
							$valid_sites_id .= ", ".$blog['blog_id'];
						}
						else
						{
							$valid_sites_id .= $blog['blog_id'];
						}						
					}
				}
				if($sqlBlogName != null && $sqlBlogName != ""){$sqlBlogName .= " union ";}
				$sqlBlogName .= " SELECT ".$blog['blog_id']." as blog_id FROM wp_fam_".$blog['blog_id']."_options where (option_name = 'blogname' and (".$likeBlog.")) or exists (SELECT * FROM `wp_fam_".$blog['blog_id']."_posts` where post_type  = 'viagem' and post_status ='publish' and (".$likeViagemDesc.")) ";				
				
			}
		}	
		
		$table_prefix = $wpdb->base_prefix;		
		$sqlstr = "";				
					
		if($counter > 1)
		{
			$sqlstr .= " union ";
		}	
		$sqlstr .= "SELECT ".$table_prefix ."blogs.* FROM ".$table_prefix ."blogs ";		
		
		if($options["userId"] != null)
		{
			$sqlstr .= " inner JOIN ".$table_prefix ."usermeta on (".$table_prefix ."usermeta.meta_key = CONCAT('".$table_prefix ."', ".$table_prefix ."blogs.blog_id, '_capabilities'))  WHERE ".$table_prefix ."usermeta.user_id = '".$options["userId"]."'";
		}
		else
		{
			$sqlstr .= " where blog_id <> 1";
		}
		
		if($options["where"] != null)
		{			
			$sqlstr .= " and ".$options["where"];			
		}
		
		$sqlstr .= " and blog_id in (".$valid_sites_id.")";
		
		if($options["other_countries"] == 'yes')
		{
			$likeOtherCountries = " and (local_de_chegada not like '%(BR)%' or local_de_partida not like '%(BR)%' ) ";
		}
		
		if($search_term != null)
		{			
			$sqlLikeLocal .= " local_de_partida like '%{term}%' or  local_de_chegada like '%{term}%' or  transporte like '%{term}%' ";						
			$likeLocal = Conteudo::GetSearchLike($sqlLikeLocal,$search_term);				
									
			$likeViagemDesc = Conteudo::GetSearchLike($sqlLikeViagemDesc,$search_term);	
				
			$sqlstr .= 
			" and  ((
			blog_id in 
					(select distinct viagem_id from wp_fam_trajetos 
						where 
						blog_id = viagem_id 
						and (".$likeLocal.") ".$likeOtherCountries."
					) 
			or wp_fam_blogs.path like  '%".$search_term."%' )
			
			or blog_id in (".$sqlBlogName.")) ";
							
		}
		else
		{
			$sqlstr .= " and  ( blog_id in (select distinct viagem_id from wp_fam_trajetos where blog_id = viagem_id ".$likeOtherCountries.")) ";
		}
		
			
		if($options["excluded_ids"] != null)
		{ 
			$notIn = " ";
			$ids = $options["excluded_ids"];	
					
			if(!is_array($options["excluded_ids"]))
			{				
				$ids = 	split(",",$options["excluded_ids"]);									
			}	
			if(is_array($ids))
			{
				foreach($ids as $id)
				{					
					$sqlstr .= " and blog_id <> ".$id;					
				}
			}
			elseif(is_numeric($ids))
			{
				$sqlstr .= " and blog_id <> ".$ids;
			}				
		}
		
		
		if($options["orderBy"] != null)
		{
			$sqlstr .= " order by ".$options["orderBy"];
		}
		else
		{			
			$sqlstr .= " order by Rand()";
		}	
		
		if($options["itens"] != null)
		{
			$sqlstr .= " limit 0, ".$options["itens"];
		}			
			//var_dump($sqlstr);
		$posts = $wpdb->get_results(($sqlstr));	
		
		return $posts;
	}	
	
	public function GetMonths()
	{
		global $wpdb;
		$sql = 'SELECT DISTINCT count(POSTS.ID) as post_amount, date_format(post_date,\'%m\') as post_month, date_format(post_date,\'%Y\') as post_year
				FROM '.$wpdb->posts.' AS POSTS
				WHERE POSTS.post_type="'.$this->PostType.'" AND POSTS.post_status="publish" group by post_year,post_month ORDER BY POSTS.post_date DESC';
		
		$dates =  $wpdb->get_results(($sql));			
		foreach($dates as $date)
		{
			switch ($date->post_month)
			{
				case 01:
					$date->monthName = 'janeiro';
					break;
				case 02:
					$date->monthName = 'fevereiro';
					break;
				case 03:
					$date->monthName = 'março';
					break;
				case 04:
					$date->monthName = 'abril';
					break;
				case 05:
					$date->monthName = 'maio';
					break;
				case 06:
					$date->monthName = 'junho';
					break;
				case 07:
					$date->monthName = 'julho';
					break;
				case 08:
					$date->monthName = 'agosto';
					break;
				case 09:
					$date->monthName = 'setembro';
					break;
				case 10:
					$date->monthName = 'outubro';
					break;
				case 11:
					$date->monthName = 'novembro';
					break;
				case 12:
					$date->monthName = 'dezembro';
					break;
			}
		}
		return $dates;
			
	}	
	
	public function GetCategories()
	{
		global $wpdb;
		$sql_todas = "select count(POSTS.ID) as post_amount, 'todas' as slug, 'Todas' as name FROM ".$wpdb->posts." AS POSTS										
				where POSTS.post_status = 'publish' AND POSTS.post_type = 'blog_post' group by slug,name";

		$sql_categories  = 'select distinct count(POSTS.ID) as post_amount, slug, term.term_id as cat_id, name FROM '.$wpdb->posts.' AS POSTS
				LEFT JOIN '.$wpdb->term_relationships.' as term_relation on term_relation.object_id = POSTS.ID
				LEFT JOIN '.$wpdb->term_taxonomy.' as term_taxonomy on term_taxonomy.term_taxonomy_id = term_relation.term_taxonomy_id
				LEFT JOIN '.$wpdb->terms." as term on term.term_id = term_taxonomy.term_id							
				where POSTS.post_status = 'publish' and POSTS.post_type = '".$this->PostType."' group by slug, name ";
		
		$blog_categories = $wpdb->get_results($sql_categories);
		$all_categories = $wpdb->get_results($sql_todas);
		$blog_categories[]  = $all_categories[0];
		return $blog_categories;		
	}
	
	public function GetTrajetosViagem($viagemId)
	{
		global $wpdb;			
		$sqlstr = "SELECT * from wp_fam_trajetos where viagem_id = '".$viagemId."' order by id asc";
		$trajetos = $wpdb->get_results(($sqlstr));
		return $trajetos;
	}
	
	public function GetTypeCategoriesPostsAndComments($options)
	{
		$limit = "";
		if($options["itens"] != null)
		{
			$limit = " limit 0, ".$options["itens"];
		}
		$notIn = "";
		if($options["excluded_ids"] != null)
		{
			if(!is_array($options["excluded_ids"]))
			{
				$options["excluded_ids"] =	explode(',', $options["excluded_ids"]);				
			}
			foreach($options["excluded_ids"] as $excluded_id)
			{
				if(is_numeric($excluded_id))
				{
					$notIn .= " and term.term_id <> ".$excluded_id;
				}
			}
		}
		
		global $wpdb;
		$sql_categories  = "select distinct count(POSTS.ID) as post_amount, slug, term.term_id as cat_id, name,
		term_taxonomy.description as descricao,
			
			(select POST.ID from ".$wpdb->posts." as POST inner JOIN ".$wpdb->term_relationships." as term_relation2 on term_relation2.object_id = POST.ID 
			inner JOIN ".$wpdb->term_taxonomy." as term_taxonomy2 on term_taxonomy2.term_taxonomy_id = term_relation2.term_taxonomy_id 
			inner JOIN ".$wpdb->terms." as term2 on term2.term_id = term_taxonomy2.term_id where POST.post_status = 'publish' 
			and POST.post_type = '".$this->PostType."' and  term2.term_id = term.term_id ORDER BY POST.post_date desc limit 0,1 ) AS last_post_id,
			
				(
					select count(comment_ID) from ".$wpdb->base_prefix."comments as comments3 where comment_post_ID in 
					(
						select distinct POST3.ID   FROM ".$wpdb->posts." AS POST3
						INNER JOIN ".$wpdb->term_relationships." as term_relation3 on term_relation3.object_id = POST3.ID
						INNER JOIN ".$wpdb->term_taxonomy." as term_taxonomy3 on term_taxonomy3.term_taxonomy_id = term_relation3.term_taxonomy_id
						INNER JOIN ".$wpdb->terms." as term3 on term3.term_id = term_taxonomy3.term_id							
						where POST3.post_type = '".$this->PostType."' and term3.term_id = term.term_id 
					) and comment_approved = 1
				)  as comments_amount,
				
				(
					select comment_ID from ".$wpdb->base_prefix."comments as comments4 where comment_post_ID in 
					(
						select distinct POST4.ID   FROM ".$wpdb->posts." AS POST4
						INNER JOIN ".$wpdb->term_relationships." as term_relation4 on term_relation4.object_id = POST4.ID
						INNER JOIN ".$wpdb->term_taxonomy." as term_taxonomy4 on term_taxonomy4.term_taxonomy_id = term_relation4.term_taxonomy_id
						INNER JOIN ".$wpdb->terms." as term4 on term4.term_id = term_taxonomy4.term_id							
						where POST4.post_type = '".$this->PostType."' and term4.term_id = term.term_id 
					) and comment_approved = 1 order by comment_ID desc limit 0,1
				) as last_comment_id
		
		FROM ".$wpdb->posts." AS POSTS
		INNER JOIN ".$wpdb->term_relationships." as term_relation on term_relation.object_id = POSTS.ID
		INNER JOIN ".$wpdb->term_taxonomy." as term_taxonomy on term_taxonomy.term_taxonomy_id = term_relation.term_taxonomy_id
		INNER JOIN ".$wpdb->terms." as term on term.term_id = term_taxonomy.term_id	
		
		where POSTS.post_status = 'publish' and POSTS.post_type = '".$this->PostType."' ".$notIn." group by slug, name order by comments_amount desc, post_amount desc  ". $limit;		
		
		$data = $wpdb->get_results(($sql_categories));		
		return $data;		
	}	
	
	public function GetSearchResults($options)
	{		
		global $wpdb;
		$notInViajantes = '';
		$notInComments = '';
		$notInPosts = '';
		if(isset($options["excluded_ids_search"]) && $options["excluded_ids_search"] != null)
		{		
			$excludeds_search_ids = $options["excluded_ids_search"];	
				
			if(!is_array($excludeds_search_ids))
			{
				$excludeds_search_ids = split(",",$excludeds_search_ids);									
			}
			if(is_array($excludeds_search_ids) && count($excludeds_search_ids > 0))
			{
				$arrayIds = array();
				foreach($excludeds_search_ids as $id)
				{			
					$id_data = split(";", $id);			
					if(is_numeric($id_data[0]))
					{		
						switch($id_data[1])
						{
							case 'viajante':
								$notInViajantes .= " and id <> ".$id_data[0];
								break;
							case 'comment':
								$notInComments .= " and comment_ID <> ".$id_data[0];
								break;
							default:
								$notInPosts .= " and id <> ".$id_data[0];
								break;
							
						}				
						$notIn .= " and ID <> ".$id;							
					}
				}								
			}						
		}	
		
		if(get_current_blog_id() != 1)
		{
			$userFromCurrentBlog = " inner JOIN ".$wpdb->base_prefix ."usermeta on (".$wpdb->base_prefix ."usermeta.meta_key = CONCAT('".$wpdb->base_prefix ."', ".get_current_blog_id().", '_capabilities'))";
			$userFromCurrentBlogwhere = " and ".$wpdb->base_prefix ."usermeta.user_id = ".$wpdb->base_prefix."users.ID";
		}
		
		$term = Conteudo::AdjusteSearchTerm($options['term']);		
		
		if($options["multisite"] == "yes")
		{				
			$blog_list = get_blog_list( 0,'all');
			$table_prefix = $wpdb->base_prefix;	
					
			foreach ($blog_list AS $blog) 
			{	
				$table_post = $table_prefix .$blog['blog_id']."_posts";					
				$table_comment = $table_prefix.$blog['blog_id']."_comments";
				$table_post_meta = $table_prefix.$blog['blog_id']."_postmeta";
				
				if($blog['blog_id'] == 1)
				{
					$table_post = $table_prefix."posts";
					$table_comment = $table_prefix."comments";
					$table_post_meta = $table_prefix."postmeta";						
				}
					
				if(is_array($excludeds_search_ids) && count($excludeds_search_ids > 0))
				{
					$arrayIds = array();
					foreach($excludeds_search_ids as $id)
					{			
						$id_data = split(";", $id);		
							
						if($blog['blog_id'] == $id_data[1] && is_numeric($id_data[0]))
						{							
							if(is_numeric($id_data[0]))
							{		
								switch($id_data[1])
								{
									case 'viajante':
										$notInViajantes .= " and id <> ".$id_data[0];
										break;
									case 'comment':
										$notInComments .= " and comment_ID <> ".$id_data[0];
										break;
									default:
										$notInPosts .= " and id <> ".$id_data[0];
										break;
										
								}				
								$notIn .= " and ID <> ".$id;							
							}
						}
					}								
				}				
				
				$likePost = Conteudo::GetSearchLike(" post_title like '%{term}%' or post_content like '%{term}%' or ".$table_post_meta.".meta_value like '%{term}%'",$term);				
				$likePostTitle = Conteudo::GetSearchLike(" post_title like '%{term}%' ",$term);
				$likeComment = Conteudo::GetSearchLike(" comment_content like '%{term}%' ",$term);
								
				$sql .= " 
				SELECT  ".$blog['blog_id']." as blog_id,
				post_type as content_type,
				post_title as content_title,				
				ID, 
				post_date as content_date,
				".$table_post_meta.".meta_value as 'content_content_meta',
				post_content as  'content_content',
				(case when post_title like '%".$term."%' then '1' else ( case when (".$likePostTitle.") then '2' else '3' end) end ) as relevance
				FROM ".$table_post." as posts left outer join ".$table_post_meta." on ".$table_post_meta.".post_id = posts.ID and ( ".$table_post_meta.".meta_key = 'conteudo' or ".$table_post_meta.".meta_key = 'descricao_album')
				where (post_type = 'forum' or post_type = 'page' or post_type = 'blog_post' or post_type = 'relatos' or post_type = 'albuns' or post_type = 'atualizacao' ) and
				post_status = 'publish' and (".$likePost.") ".$notInPosts."

				union

				SELECT 
				".$blog['blog_id']." as blog_id,
				'comment' as content_type,				
				comment_content as content_title,
				comment_ID as ID, 
				comment_date as content_date, 
				'' as 'content_content_meta',
				comment_content as content_content ,
				'3' as relevance
				FROM ".$table_comment." where (".$likeComment.")
				and comment_approved = 1 ".$notInComments."

				union ";									
			}
			
						
			$likeViajante = Conteudo::GetSearchLike(" display_name like '%{term}%' ",$term);			
			
			$sql .= " select 0 as blog_id, 'viajante' as content_type, display_name as content_title, ID, user_registered as content_date, '' as 'content_content_meta', display_name as content_content, '2' as relevance
			from ".$wpdb->base_prefix."users ".$userFromCurrentBlog."		
			where  (".$likeViajante." ) and ID <> 1 ".$notInViajantes.$userFromCurrentBlogwhere. " order by relevance asc, content_date desc limit 0, ".$options['itens'];
		}
		else
		{	
			$likePost = Conteudo::GetSearchLike(" post_title like '%{term}%' or post_content like '%{term}%' or ".$wpdb->postmeta.".meta_value  like '%{term}%'",$term);			
			$likePostTitle = Conteudo::GetSearchLike(" post_title like '%{term}%' ",$term);			
			$likeComment = Conteudo::GetSearchLike(" comment_content like '%{term}%' ",$term);			
				
			$sql = "
			SELECT 
			post_type as content_type,
			post_title as content_title, 
			ID, 
			post_date as content_date,
			".$wpdb->postmeta.".meta_value as 'conten_content_meta',
			post_content as content_content,
			(case when post_title like '%".$term."%' then '1' else ( case when (".$likePostTitle.") then '2' else '3' end) end ) as relevance
			FROM ".$wpdb->posts." as posts left outer join ".$wpdb->postmeta." on ".$wpdb->postmeta.".post_id = posts.ID and (".$wpdb->postmeta.".meta_key = 'conteudo' or ".$wpdb->postmeta.".meta_key = 'descricao_album')
			where (post_type = 'forum' or post_type = 'page' or post_type = 'blog_post' or post_type = 'relatos' or post_type = 'albuns' or post_type = 'atualizacao' ) and
			post_status = 'publish' and (".$likePost.") ".$notInPosts."

			union

			SELECT 'comment' as content_type,
			comment_content as content_title,
			comment_ID as ID, 
			comment_date as content_date, 
			'' as 'content_content_meta',
			comment_content as content_content,
			'3' as relevance
			FROM ".$wpdb->comments." where (".$likeComment.")
			and comment_approved = 1 ".$notInComments." 

			union ";			
			
			$likeViajante = Conteudo::GetSearchLike(" display_name like '%{term}%' ",$term);
			
			$sql .= " select 'viajante' as content_type, display_name as content_title, ID, user_registered as content_date, '' as 'content_content_meta', display_name as content_content, '2' as relevance
			from ".$wpdb->base_prefix."users ".$userFromCurrentBlog."		
			where  ( ".$likeViajante." ) and ID <> 1 ".$notInViajantes.$userFromCurrentBlogwhere."
			order by relevance asc, content_date desc limit 0, ".$options['itens'];
		}				
		$sql = "select distinct * from ( ".$sql.") as result order by relevance asc, content_date desc limit 0,".$options['itens'];;
		$data = $wpdb->get_results(($sql));		
		//var_dump($sql);	
			
		$sites = $this->GetSites(array("search_term" => $term,"excluded_ids" => $options["excluded_ids_search"]));	
		
					
		$sitesResult = array();
		if(count($sites) > 0)
		{
			foreach($sites as $site)
			{				
				$viagem = new ViagemVO($site->blog_id);
				$dataResult = new stdClass();
				$dataResult->content_type = "viagem";
				$dataResult->content_title = $viagem->Titulo;
				$dataResult->ID = $viagem->ViagemId;
				$dataResult->content_date = $viagem->DataInicio;
				$dataResult->content_content =  $viagem->GetSubContent(500);
				$dataResult->roteiro = $viagem->Roteiro;
				$dataResult->url = $viagem->ViagemUrl;
				$dataResult->image = $viagem->MidiaPrincipal;				
				
				$termV = str_replace("-", " ",sanitize_title($term));
				if(0 < count(array_intersect(array_map('strtolower', explode('-', sanitize_title($viagem->Titulo))), Conteudo::GetSplitedSearchTerm($termV))))
				{
					array_unshift($data,$dataResult);
				}
				else{
					array_push($data,$dataResult);
				}
				
				
					
							
			}
		}			
						
		return $data;
	}
	
	public static function GetFeedItens()
	{
		$itens = array();
		require_once( ABSPATH . '/FAMCore/VO/AlbumVO.php' );
		require_once( ABSPATH . '/FAMCore/VO/AtualizacaoVO.php' );
		foreach(array('blog_post','forum','relatos','albuns','atualizacao') as $ptype)
		{
			$urlSlug = $ptype;
			
			if($ptype == "blog_post")
			{
				$urlSlug = "blog";
			}
			if($ptype == "atualizacao")
			{
				$urlSlug = "status";
			}
			if(in_array($ptype, array('blog_post','forum')))
			{
				$posts = get_posts(array('post_type'=>$ptype,'order'=>'desc','orderby'=>'post_date'));
			}
			else
			{
				$posts = Conteudo::GetMultiSiteContent($ptype,array('itens'=>20),true);				
			}
			foreach($posts as $post_current)
			{
				global $post;
				$post = $post_current;
				$post->post_title = apply_filters('the_title_rss', $post->post_title);
				$post->feed_date = mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true,$post), false);
				$post->author_name = apply_filters('the_author_' . $field, get_the_author_meta("user_nicename", $post->post_author), $post->post_author);					
				$post->guid = get_site_url($post->blog_id)."/".$urlSlug."/".$post->post_name."/".$post->ID."/";
				
				$post->post_comment_link =  esc_url( get_post_comments_feed_link(null, 'rss2') );
				$post->post_comment_number = get_comments_number();

				$graph_url = "https://graph.facebook.com/?fields=share&access_token=585138868164342|9Luxc3zO1RXMJR20BqjGB2W022o&id=".$post->guid;
				$response = file_get_contents(($graph_url));
      			$decoded_response = json_decode($response);				
				if (property_exists($decoded_response, 'share') && property_exists($decoded_response->share, 'comment_count')){
					// if(is_numeric($post->post_comment_number))
					// {
					// 	$post->post_comment_number = ($post->post_comment_number + $decoded_response->share->comment_count);
					// }
					// else{
						$post->post_comment_number = $decoded_response->share->comment_count;
					//}					
				} 
				
				if($post->blog_id == null)
				{
					$post->blog_id = get_current_blog_id();
				}
				if(in_array($ptype, array('blog_post','forum')))
				{
					$post->category = get_the_category_rss("rss2");
					if($ptype == "blog_post")
					{
						$content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $post->post_content);				
						$post->post_content = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);						
						$post->post_excerpt = GetSubContent($post->post_content, 500);
					}
					else
					{
						$post->post_content = get_the_content_feed('rss2');					
						$excerpt = get_the_excerpt();
						$post->post_excerpt =  apply_filters('the_excerpt_rss', $excerpt);
					}	
				}
				else
				{
					$post->category = "<category><![CDATA[".$urlSlug."]]></category> ";
								
					if($post->post_type == "relatos")
					{							
						$content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $post->post_content);				
						$post->post_content  = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);						
						$post->post_excerpt = GetSubContent($post->post_content, 500);
					}
					if($post->post_type == "albuns")
					{													
						switch_to_blog($post->blog_id);													
						$album = new AlbumVO($post->ID,true);							
						restore_current_blog();							
						
						$post->post_excerpt = $album->Resumo;
						$post->post_content  = "<img src='".$album->MidiaPrincipal->ImageMediumSrc."' title='".$album->Titulo."' alt='".$album->Titulo."' /><br/>" .$album->Resumo;
					}
					if($post->post_type == "atualizacao")
					{
						
						switch_to_blog($post->blog_id);
						$status = new AtualizacaoVO($post->ID);						
						restore_current_blog();					
						$post->post_excerpt = $status->Conteudo;	
						$post->post_content  = "<img src='".$status->MidiaPrincipal->ImageMediumSrc."' title='".$status->Titulo."' alt='".$status->Titulo."' /><br/>" .$status->Conteudo;
					}
				}
			}
			$itens = array_merge($itens, $posts);
		}
		
		usort($itens, array($this,"SortCompareByPostDate"));
		return $itens;
	}
	
	public static function SecureSql($str)
	{
		$str = str_replace("--","",$str);
		$str = str_replace("'","",$str);
		$str = str_replace('"',"",$str);		
		return $str;
	}
	
	
	public static function SortCompareByPostDate($a, $b)
	{		
		echo $a->post_date."-".$b->post_date;
		if ($a->post_date == $b->post_date) {
			return 0;
		}
		return ($a->post_date > $b->post_date) ? -1 : 1;
	}
	
	public static function GetSplitedSearchTerm($term)
	{
		$stopWords = array("de","o","a","que","e","do","da","em","um","para","é","com","não","uma","os","no","se","na","por","mais","as","dos","como","mas","foi","ao","ele","das","tem","à","seu","sua","ou","muito","há","nos","já","está","eu","também","só","pelo","pela","até","isso","ela","entre","era","dpois","sem","mesmo","aos","ter","seus","quem","nas","me","esses","eles","estão","você","tinha","foram","essa","num","nem","suas","meu","às","minha","numa","pelos","elas","lhe","deles","essas","esses","pelas","este","dele","tu","te","vocês","vos","lhes","meus","minhas","teu","tua","teus","tuas","nosso","nossos","nossas","dela","delas","esta","estas","aquele","aquela","aqueles","aquelas","isto","aquilo","estou","está","estamos","estão","estive","esteve","estivemos","estiveram","estava","estávamos","estavam","estivera","estivéramos","esteja","estejamos","estejam","estivesse","estivéssemos","estivessem","estiver","estivermos","estiverem","hei","há","havemos","hão","houve","houvemos","houveram","houvera","houvéramos","haja","hajamos","hajam","houvesse","houvéssemos","houvessem","houver","houvermos","houverão","houveria","houveríamos","houveriam","sou","somos","são","era","éramos","eram","fui","foi","fomos","foram","fora","fôramos","seja","sejamos","sejam","fosse","fôssemos","fossem","for","formos","forem","serei","será","seremos","serão","seria","seríamos","seriam","tenho","tem","temos","tém","tinha","tínhamos","tinham","tive","teve","tivemos","tenha","tenhamos","tenham","tivesse","tivéssemos","tivessem","tiver","tivermos","tiverem","terei","terá","teremos","terão","teria","teríamos","teriam","onde");
		//stop words de http://snowball.tartarus.org/algorithms/portuguese/stop.txt"
		$termsSplited = explode(" ",$term);
		$termsSplited = array_diff($termsSplited, $stopWords);
			
		if(count($termsSplited) == 0)
		{
			$termsSplited = array();			
		}		
		return $termsSplited;	
	}
	
	public static function GetSearchLike($sqlLikeClause, $term)
	{		
		$like = str_replace("{term}",$term, $sqlLikeClause);	
		$termsSplited = Conteudo::GetSplitedSearchTerm($term);		
		foreach($termsSplited as $termSplited)
		{
			$like .= " or ".str_replace("{term}",$termSplited, $sqlLikeClause);				
		}
		
		return $like;
		
	}
	
	public static function AdjusteSearchTerm($term)
	{
		$term = Conteudo::SecureSql($term);
		$term  = str_replace('[',"",$term);
		$term  = str_replace(']',"",$term);
		return $term;
	}
	
	public static function GetPostsRelacionados($options)
	{		
		$relacionados = array();
		$ids = array();
		$excluded_ids = array();
		$current_site_excluded_ids = array();
		$related_posts_ids = get_post_meta($options["post_id"], "related_posts_ids", true);	
		$related_posts_ids = str_replace(";", ",", $related_posts_ids);	
		$related_posts_ids = str_replace("-", ";", $related_posts_ids);
		
		if($related_posts_ids != null && strlen($related_posts_ids) > 0)
		{					
			if( strpos( $related_posts_ids,",") === -1)
			{
				$related_posts_ids.",";
			}
			$ids = explode(",", $related_posts_ids);			
			
			$current_composed_id = get_current_blog_id().";".$options["post_id"];
			$excluded_ids = $ids;
			array_push($excluded_ids, $current_composed_id);
			
			$current_site_excluded_ids = array();
			foreach($excluded_ids as $excluded_id)
			{
				if( strpos( $excluded_id,";") > 0)
				{
					$id = split(";",$excluded_id);
					if($id[0] == get_current_blog_id())
					{
						$current_site_excluded_ids[] = $id[1];
					}	
				}			
			}			
		}
		if(get_current_blog_id() > 1 && (!is_array($ids) || count($ids) < 3))
		{			
			require_once(ABSPATH."/FAMCore/BO/Relato.php");
			$conteudo = new Relato();						
			$relatos = $conteudo->GetItens(array('excluded_ids'=>$current_site_excluded_ids, 'itens'=> 3 - count($ids))); 
			foreach($relatos as $relato)
			{
				$ids[] = get_current_blog_id().";".$relato->ID;
			}
		}
		elseif (get_current_blog_id() == 1 && (!is_array($ids) || count($ids) < 3))
		{
			require_once(ABSPATH."/FAMCore/BO/BlogPost.php");
			$conteudo = new BlogPost();	
			
			$blogposts = $conteudo->GetItens(array('excluded_ids'=>$current_site_excluded_ids, 'itens'=> 3 - count($ids))); 
			foreach($blogposts as $blogpost)
			{
				$ids[] = get_current_blog_id().";".$blogpost->ID;
			}			
		}
		
							
		foreach($ids as $related_post_id)
		{				
			if($related_post_id != null && strlen($related_post_id) > 0)
			{
				$post_id_data = explode(';',$related_post_id);				
				$restoreBlog = "no";	
				if($post_id_data[0] != get_current_blog_id())
				{
					switch_to_blog( $post_id_data[0] );
					$post = get_post($post_id_data[1]);
					$restoreBlog = "yes";					
				}			
				else
				{
					$post = get_post($post_id_data[1]);	
				}
				if($post != null)
				{		
					switch($post->post_type)
					{
						case "relatos":
							require_once( ABSPATH . '/FAMCore/VO/RelatoVO.php' );
							$relato = new RelatoVO($post->ID);
							$relato->PostUrl = $relato->RelatoUrl;
							$relato->PostTypeLabel = "Relato";
							$relacionados[] = $relato;								
							break;
						case "albuns":
							require_once( ABSPATH . '/FAMCore/VO/AlbumVO.php' );
							$album  = new AlbumVO($post->ID, true);
							$album->PopulateAlbumVO($album,$post);
							$album->PostUrl = $album->AlbumUrl;
							$album->PostTypeLabel = "Álbum";
							$relacionados[] = $album;							
							break;
						case "blog_post":
							require_once( ABSPATH . '/FAMCore/VO/PostVO.php' );
							$blogpost = new PostVO("blog_post",$post->ID);	
							$blogpost->PostTypeLabel = "Blog";							
							$relacionados[] = $blogpost;								
							break;							
					}					
				}
				if($restoreBlog == "yes")
				{
					restore_current_blog();
				}
			}
		}
			
		
		return $relacionados;
	}

}

?>