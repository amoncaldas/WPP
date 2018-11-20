<?php

/**
 * class Comentario
 *
 * Description for class Comentario
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/VO/ViajanteVO.php' );
require_once(ABSPATH. "/FAMCore/BO/Imagem.php");
require_once(ABSPATH. "/FAMCore/BO/Viajante.php");
class Comentario  {

	public $DadosComentario;		
	public $HasMoreData;
	/**
	 * Comentario constructor
	 *
	 * @param 
	 */
	function Comentario() {
		
		
	}
	
	public function GetData($options)
	{
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 3;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		if($options["viagemId"] == null)
		{
			$options["viagemId"] = get_current_blog_id();
		}
		
		$this->HasMoreData = false;			
				
		$comentarios = $this->GetComentarios($options);
		
		if(is_array($comentarios) && count($comentarios)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$comentarios = array_slice($comentarios, 0, count($comentarios) -1);				
		}	
		
		return $comentarios;
	}
	
	public function GetComentarios($options)
	{
		switch_to_blog($options["viagemId"]);
		$commentsTableName = "wp_fam_comments";
		if($options["viagemId"] > 1)
		{
			$commentsTableName = "wp_fam_".$options["viagemId"]."_comments";
		}
		$args = array('status' => 'approve','post_id' => $options["parentId"], 'number'=>$options["itens"]);
		$notIn = "";
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
						$notIn .= " and comment_ID <> ".$id;							
					}
				}					
			}					
		}
		global $wpdb;
		$sql = "SELECT * FROM  ".$commentsTableName." where comment_post_ID = ". $options["parentId"]." and comment_approved = '1' ".$notIn." order by comment_date ASC LIMIT 0 , ".$options["itens"];
		$comments = $wpdb->get_results($sql);
		if(is_array($comments) && count($comments) > 0)
		{
			foreach($comments as $comentario)
			{
				if($comentario->user_id > 0)
				{
					$comentario->Autor = new ViajanteVO($comentario->user_id, true);				
				}
				else
				{
					$user = get_user_by( 'email', $comentario->comment_author_email);
					if($user->ID != null)
					{
						$comentario->Autor = new ViajanteVO($user->ID,true);
					}
					else
					{
						$viajanteVO = new ViajanteVO(0);
						$viajanteVO->FullName = $comentario->comment_author;
						$viajanteVO->ViajanteUrl = "javascript:void(0)";
						$comentario->Autor = $viajanteVO;	
												
						$statiticsData = Viajante::GetUserStatistics($comentario->comment_author_email);						
						$comentario->Autor->CountForumMessages = $statiticsData->CountForumMessages;
						$comentario->Autor->CountForumTopics = $statiticsData->CountForumTopics;
					}											
				}
				
				$comentario->Url = get_comment_link($comentario);
				$comentario->Title = $this->GetCommentSubString($comentario->comment_content, 20);
				$comentario->Resumo = $this->GetCommentSubString($comentario->comment_content, 100);				
				$comentario->comment_content = nl2br($comentario->comment_content);
				
				$padraoLinkCompleto = "/((https|http|ftp)\:\/\/+(([a-z0-9A-Z]+\.[a-z0-9A-Z]+\.[a-z]{2,5})|([a-z0-9A-Z]+\.[a-z]{2,5}))([a-zA-Z]))+(([\/\&\=\#\?a-zA-Z\-\%\d\.]{0,200}))/";				
				if(preg_match_all($padraoLinkCompleto,$comentario->comment_content,$lista))
				{							
					for($i=0;$i<=sizeof($lista[0]);$i++)
					{
						$link = $lista[0][$i];						
							if($link != null && $link != "" && strpos($link, str_replace("teste.","",network_home_url())) !== false)	
						{							
							try
							{													
								$comentario->comment_content = str_replace($link,"<a target='_blank' href='".$link."' >".$link."</a>",$comentario->comment_content);								
							}
							catch(exception $ex){}	
						}					
					}	
				}
				/*$padraoLinkIncompleto = "/(([a-z0-9A-Z]+\.[a-z]{2,5})|([a-z0-9A-Z]+\.[a-z0-9A-Z]+\.[a-z]{2,5}))+[\/\\&\=\#\?a-zA-Z\-\%\d\.]{0,200}/";				
				if(preg_match_all($padraoLinkIncompleto,$comentario->comment_content,$lista))
				{		
							
					for($i=0;$i<=sizeof($lista[0]);$i++)
					{
						$link = $lista[0][$i];	
							
						if($link != null && $link != "")	
						{	
							$link = "http://".$link;
							try
							{													
								$comentario->comment_content = str_replace($link,"<a target='_blank' href='".$completelink."' >".$link."</a>",$comentario->comment_content);								
							}
							catch(exception $ex){}	
						}					
					}	
				}										*/
			}						
		}			
		restore_current_blog();	
		return $comments;
	}
	
	public function GetCommentSubString($content,$lenght)
	{
		
		if($content != null)
		{
			$content = trim($content);				
			$rtc = "";
			if(strlen($content) > $lenght)
			{		
				$rtc ="...";				
				if(strpos($content, " ") > 0)
				{		
					$spacePosition = strrpos($content, " ", $lenght);
					
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = strrpos($content, " ", $lenght -5);
						if($spacePosition == null || $spacePosition == 0)
						{
							$spacePosition = strrpos($content, " ", $lenght -10);
							if($spacePosition == null || $spacePosition == 0)
							{
								$spacePosition = strpos($content, " ");
							}
						}
					}								
					$content =  substr($content, 0, $spacePosition);
					
				}
				else
				{					
					$lenght = ($lenght > 5)? ($lenght -5): 0;								
					$content = substr($content, 0, strpos($content, " ", $lenght));
				}
					
				if(strlen($content) > $lenght)	
				{		
					$content = trim($content);		
					$spacePosition = strpos($content, " ", $lenght);
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = strpos($content, " ");
					}
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = $lenght;
					}				
					$content =  substr($content, 0, $spacePosition);				
				}	
					
			}
			
			return $content.$rtc;
			
		}
	}
	
	
}

?>