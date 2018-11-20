<?php

/**
 * class widget
 *
 * Description for class widget
 *
 * @author:
*/
class widget  {

	/**
	 * widget constructor
	 *
	 * @param 
	 */
	function widget() {

	}
	
	public static function Get($widgetName, $options = array())
	{
		
		switch($widgetName)
		{
			case "a-viagem":
				widget::GetAViagem($options);				
				break;
			case "resultados_busca":
				widget::GetSearchResults($options);
				break;
			case "mapa_viagens":
				widget::GetMapaViagens($options);				
				break;
			case "busca_form":
				widget::GetBuscaForm($options);				
				break;
			case "viagens":				
				widget::GetViagens($options);				
				break;
			case "facebook-box":
				include('Facebook_content.php');
				break;
			case "links":
				include('Links_content.php');
				break;
			case "fotos":
				widget::GetMedia($options);
				break;
			case "ultimos-relatos":				
				widget::GetRelatos($options);
				break;
			case "forum":							
				widget::GetCategoriasForum($options);
				break;
			case "forum_topics":							
				widget::GetTopicosCategoria($options);
				break;
			case "roteiro_localizacao":
				widget::GetRoteiroLocalizacao($options);				
				break;
			case "share":
				widget::GetShare($options);
				break;
			case "slideFotos":			
				include('SlideFotos_content.php');
				break;
			case "twitter-box":
				include('Twitter_content.php');
				break;
			case "socialmedia":
				include('Socialmedia_content.php');
				break;
			case "codigocriativo":
				include('CodigoCriativo_content.php');
				break;
			case "content-location":			
				widget::GetLocation($options);	
				break;
			case "galeria":							
				widget::GetGaleria($options);	
				break;
			case "load-more-content":																				
				widget::GetMore($options);	
				break;
			case "atualizacoes":							
				widget::GetAtualizacoes($options);	
				break;
			case "bandeiras":							
				widget::GetBandeiras($options);	
				break;
			case "comentarios":							
				widget::GetComentarios($options);	
				break;				
			case "blog_posts":							
				widget::GetBlogPosts($options);	
				break;
			case "blog_sidebar":							
				widget::GetBlogSideBar($options);	
				break;
			case "dicas":							
				widget::GetDicas($options);	
				break;
			case "viajantes":							
				widget::GetViajantes($options);					
				break;
			case "contato_form":							
				widget::GetContatoForm($options);	
				break;
			case "booking_form":							
				$options["addtype"] = "booking_form";						
				widget::GetAdd($options);	
				break;
			case "add_box_top_right":	
				$options["addtype"] = "add_box_top_right";
				//$options["addtype"] = "booking_form";						
				widget::GetAdd($options);	
				break;
			case "add_responsive":	
				$options["addtype"] = "add_responsive";						
				widget::GetAdd($options);	
				break;							
			case "footer_adds":			
				$options["addtype"] = "add_footer";						
				widget::GetAdd($options);
				break;
			case "aside_middle_box":			
				$options["addtype"] = "aside_middle_box";						
				widget::GetAdd($options);
				break;				
			case "banner_350x50_add":							
				widget::GetAdd_baner_320x50($options);	
				break;
			case "posts_relacionados":										
				widget::GetPostsRelacionados($options);	
				break;
		}
	}	
	
	private static function GetRelatos($options)
	{							
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "390px";;
		}
		if($options["content_lenght"] == null)
		{
			$options["content_lenght"] = 90;
		}	
		if($options["show_location"] == null)
		{
			$options["show_location"] = false;
		}	
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}		
		include('Relatos_content.php');
	}
	
	private static function GetComentarios($options)
	{							
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "600px";;
		}		
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}		
		include('Comentarios_content.php');
	}
	
	private static function GetCategoriasForum($options)
	{						
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}
		if($options["content_lenght"] == null)
		{
			$options["content_lenght"] = 90;
		}	
		if($options["show_location"] == null)
		{
			$options["show_location"] = false;
		}	
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}	
		if($options["itens"] == null)
		{
			$options["itens"] = 4;
		}		
		include('Forum_content.php');
	}
	
	private static function GetTopicosCategoria($options)
	{						
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}
		if($options["content_lenght"] == null)
		{
			$options["content_lenght"] = 90;
		}	
		if($options["show_location"] == null)
		{
			$options["show_location"] = false;
		}	
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}	
		if($options["itens"] == null)
		{
			$options["itens"] = 4;
		}		
		include('Forum_topics_content.php');
	}	
	
	private static function GetMedia($options)
	{						
		if($options["itens"] == null || $options["itens"] <= 0)
		{
			$options["itens"] = 6;
		}
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}
		if($options["width"] == null )
		{
			$options["width"] = "254px";;
		}												
			
		include('Media_content.php');
	}
	
	private static function GetShare($options)
	{			
		if($options["showface"] == null)
		{
			$options["showface"] = 'true';
		}
		if($options["comment_box_width"] == null)
		{
			$options["comment_box_width"] = '500';
		}
		if($options["layout"] == null)
		{
			$options["layout"] = 'button_count';
		}
		if($options["send"] == null)
		{
			$options["send"] = 'false';
		}							
		include('Share_content.php');
	}
	
	private static function GetLocation($options)
	{
		if(is_array($options) && count($options) > 0)
		{	
			if($options["location"] != null)
			{				
				include('ContentLocation_content.php');
			}					
		}	
	}
	
	private static function GetBuscaForm($options)
	{							
		include('Busca_form.php');			
	}
	
	private static function GetAViagem($options)
	{
		if(is_array($options) && count($options) > 0)
		{	
			if($options["idViagem"] != null)
			{				
				include('Aviagem_content.php');
			}					
		}	
	}
	
	private static function GetViagens($options)
	{		
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}
		if($options["itens"] == null)
		{
			$options["itens"] = "2";
		}							
		include('Viagens_content.php');		
	}
	
	private static function GetMapaViagens($options)
	{		
		if($options["itens"] == null)
		{
			$options["itens"] = "10";
		}							
		include('Mapa_viagens_content.php');		
	}	
	
	private static function GetRoteiroLocalizacao($options)
	{
		if(is_array($options) && count($options) > 0)
		{			
			if($options["width"] == null)
			{
				$options["width"] = '410px';	
			}
			if($options["height"] == null)
			{
				$options["height"] = '300px';	
			}			
			if($options["idViagem"] != null)
			{				
				include('RoteiroLocalizacao_content.php');
			}					
		}	
	}
	
	private static function GetMore($more_options)
	{
		if(is_array($more_options) && count($more_options) > 0)
		{						
			if($more_options["content_type"] != null)
			{					
				if($more_options["itens"] == null)
				{
					$more_options["itens"] = 3;	
				}	
				$viagemId = get_current_blog_id();
				if($more_options["viagemId"] == null && $viagemId > 1)
				{
					$more_options["viagemId"] = $viagemId;	
				}
				require_once('More_content.php');																
				GetBtn($more_options);				
			}							
		}	
	}
	
	private static function GetGaleria($options)
	{							
		if($options["itens"] == null && $options["itens"] == 0)
		{
			$options["itens"] = 9;
		}		
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "100%";;
		}
		if($options["margin_top"] == null)
		{
			$options["margin_top"] = "0px";;
		}
		
		include('Galeria_content.php');
	}
	
	private static function GetAtualizacoes($options)
	{							
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}				
		if($options["width"] == null)
		{
			$options["width"] = '"414px"';
		}			
		if($options["show_location"] == null)
		{
			$options["show_location"] = false;
		}	
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}			
		if($options["show_media"] == null)
		{
			$options["show_media"] = 'yes';
		}
		if($options["show_comment"] == null)
		{
			$options["show_comment"] = 'yes';
		}								
		
		include('Atualizacoes_content.php');
	}
	
	private static function GetBandeiras($options)
	{					
		if( $options["width"] == null || $options["width"] == 0)
		{
			$options["width"]  = 45;
		}
		include('Bandeiras_content.php');		
	}
	
	private static function GetBlogPosts($options)
	{					
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}
		if($options["content_lenght"] == null)
		{
			$options["content_lenght"] = 90;
		}	
		if($options["show_location"] == null)
		{
			$options["show_location"] = false;
		}	
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}		
		include('Blog_content.php');		
	}
	
	private static function GetBlogSideBar($options)
	{					
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}			
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}		
		include('Blog_sidebar.php');		
	}	
	
	private static function GetDicas($options)
	{					
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}
		if($options["content_lenght"] == null)
		{
			$options["content_lenght"] = 90;
		}	
		if($options["show_location"] == null)
		{
			$options["show_location"] = false;
		}	
		if($options["show_more"] == null)
		{
			$options["show_more"] = 'no';
		}		
		include('Dicas_content.php');		
	}
	
	private static function GetViajantes($options)
	{		
		include('Viajantes_content.php');		
	}
	
	private static function GetSearchResults($options)
	{		
		include('Resultados_busca_content.php');		
	}
	
	private static function GetContatoForm($options)
	{		
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "400px";;
		}
		include('Contato_form.php');		
	}	
	
		
	private static function GetAdd($options)
	{	
		if($options["float"] == null)
		{
			
			if($options["addtype"] == "add_responsive")
			{
				$options["float"] = 'left';
			}
			else
			{
				$options["float"] = 'none';
			}
		}
		if($options["margin_bottom"] == null)
		{
			if($options["addtype"] == "add_responsive")
			{
				$options["margin_bottom"] = '20';
			}
			else
			{
				$options["margin_bottom"] = '0';
			}			
		}
		
		
		if($options["margin_right"] == null)
		{
			if($options["addtype"] == "add_box_top_right")
			{
				$options["margin_right"] = '-5px';
			}
			else
			{
				$options["margin_right"] = '0px';
			}			
		}
		if($options["margin_left"] == null)
		{
			$options["margin_left"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "300";
		}
		if($options["height"] == null)
		{
			if($options["addtype"] == "add_footer")
			{
					$options["height"] = "100";
			}	
			elseif($options["addtype"] == "add_responsive")
			{
				$options["height"] = is_fam_mobile()? "100" : "80";					
			}
			else
			{
				$options["height"] = "250";
			}
			
		}
		if($options["label"] == null)
		{
			$options["label"] = "default";
		}
		switch($options["addtype"])
		{
			case "add_box_top_right":
				include('adds/add_box_top_right.php');
				break;
			case "booking_form":
				include('adds/Booking_form.php');
				break;
			case "add_responsive":
				include('adds/add_responsive.php');
				break;
			case "add_footer":
				include('adds/ad_footer.php');
				break;
			case "aside_middle_box":
				include('adds/aside_middle_box.php');
				break;			
						
		}
				
	}
	
		
	private static function GetAdd_baner_320x50($options)
	{		
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "100%";
		}
		if($options["label"] == null)
		{
			$options["label"] = "default";
		}
		include('adds/add_banner_320x50.php');		
	}
	
	private static function GetPostsRelacionados($options)
	{		
		if($options["float"] == null)
		{
			$options["float"] = 'left';
		}
		if($options["margin_right"] == null)
		{
			$options["margin_right"] = '0px';
		}
		if($options["margin_left"] == null)
		{
			$options["margin_left"] = '0px';
		}			
		if($options["width"] == null)
		{
			$options["width"] = "100%";
		}
		
		include('Posts_relacionados.php');		
	}	
	
}

?>