<?php

/**
 * class Forum
 *
 * Description for class Forum
 *
 * @author:
*/

require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Post.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/ViajanteVO.php' );
require_once(ABSPATH. "/FAMCore/BO/Viagem.php");
class Forum extends Post {	
	
	/**
	 * Forum constructor
	 *
	 * @param 
	 */
	function Forum($id = null) {
		$multisite = $this->MultiSiteData;
		$this->MultiSiteData = false;
		parent::Post("forum",$id);
		if($id != null)
		{
			$this->GetTopicComments($this->DadosPost);
			$statistics = Viajante::GetUserStatistics($this->DadosPost->Autor->UserEmail);
			$this->DadosPost->Autor->CountForumTopics = $statistics->CountForumTopics;
			$this->DadosPost->Autor->CountForumMessages =  $statistics->CountForumMessages;	
									
		}
		$this->MultiSiteData = $multisite;				
	}
	
	function GetTopicosCategoriaForum($options)
	{		
		$options["cat_id"] = $options["parentId"];
		$options["parentId"] = null;
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 3;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		$this->HasMoreData = false;
		
		$multisite = $this->MultiSiteData;	
		switch_to_blog(1);
		$this->MultiSiteData = false;
		$postsVO = $this->GetPosts($options);		
		foreach($postsVO as $postVO)
		{
			$this->GetTopicComments($postVO);			
		}	
		if(is_array($postsVO) && count($postsVO)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$postsVO = array_slice($postsVO, 0, count($postsVO) -1);				
		}	
		$this->MultiSiteData = $multisite;
		restore_current_blog();
		return $postsVO;
	}
	
	public function GetTopicComments(PostVO &$postVO)
	{
		switch_to_blog(1);
		$args = array('status' => 'approve','post_id' => $postVO->PostId);						
		$postVO->Comments = get_comments($args);
		if(is_array($postVO->Comments) && count($postVO->Comments) > 0)
		{
			foreach($postVO->Comments as $comentario)
			{
				$this->GetCommentData($comentario);									
			}				
			$postVO->LastComment = $postVO->Comments[0];				
		}			
		restore_current_blog();	
	}
	
	public function GetCommentData(&$comentario)
	{
		switch_to_blog(1);		
		$comentario->Autor = new ViajanteVO($comentario->user_id);
		$comentario->Url = get_comment_link($comentario);
		$comentario->Title = $this->GetCommentSubString($comentario->comment_content, 20);
		$comentario->Resumo = $this->GetCommentSubString($comentario->comment_content, 100);					
		restore_current_blog();	
	}
	
	public function GetData($options)
	{
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 3;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		$this->HasMoreData = false;			
		switch_to_blog(1);
		$categorias = $this->GetTypeCategoriesPostsAndComments($options);		
		if(is_array($categorias) && count($categorias)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$categorias = array_slice($categorias, 0, count($categorias) -1);				
		}	
		foreach($categorias as $categoria)
		{									
			$args = array('category_name' => $categoria->slug,'orderby' => 'date','order' => 'DESC','posts_per_page' => 5,	'post_type' => 'forum');
			$posts = get_posts($args);
			$postsVO = array();
			foreach($posts as $post)
			{					
				$forumBO = new Forum($post->ID);				
				$postsVO[] = $forumBO->DadosPost;							
			}	
			$categoria->LastTopics = $postsVO;			
			$categoria->LastTopic = $categoria->LastTopics[0];
				
			if($categoria->last_comment_id != null && $categoria->last_comment_id > 0)
			{
				$commentario = get_comment($categoria->last_comment_id);
				$this->GetCommentData($commentario);
				$categoria->LastComment = $commentario;
			}
			
		}
		restore_current_blog();	
		return $categorias;
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