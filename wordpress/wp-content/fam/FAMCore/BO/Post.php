<?php

/**
 * class Post
 *
 * Description for class Post
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/BO/Conteudo.php' );
require_once( ABSPATH . '/FAMCore/VO/PostVO.php' );

class Post extends Conteudo {

	public $DadosPost;
	private $PostId;
	public $HasMoreData;
	public $viagemId;
	/**
	 * Post constructor
	 *
	 * @param 
	 */
	function Post($post_type, $id = null, $viagemId = null) {		
		parent::Conteudo($post_type, $viagemId);
		$this->viagemId = $viagemId;
		if($id != null)
		{			
			$this->DadosPost = new PostVO($post_type,$id); 									
		}
	}
	
	public  function GetPosts($options)
	{		
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 3;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		$this->HasMoreData = false;		
		$PostVO  = new PostVO($this->PostType);		
		switch_to_blog($this->viagemId);		
		$posts = $this->GetItens($options);	
			
		restore_current_blog();		
		if(is_array($posts) && count($posts)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$posts = array_slice($posts, 0, count($posts) -1);				
		}			
		return $PostVO->GetPostVOList($posts);				
	}
}

?>