<?php

/**
 * class BlogPost
 *
 * Description for class BlogPost
 *s
 * @author:
*/

require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Post.php' );


class BlogPost  extends Post {

	public $DadosPost;
	private $PostId;
	public $HasMoreData;

	/**
	 * BlogPost constructor
	 *
	 * @param 
	 */
	function BlogPost($id = null) {			
		parent::Post("blog_post",$id);
		$this->MultiSiteData = false;		
	}
	
	function GetPosts($options)
	{
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 3;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		$this->HasMoreData = false;		
		$PostVO  = new PostVO($this->PostType);
		$excluded_ids = $options["excluded_ids"];
		if(!is_array($options["excluded_ids"]))
		{
			$excluded_ids = explode(",",$options["excluded_ids"]);
		}		
			
		switch_to_blog(1);	
		global $ano_blog;
		global $mes_blog;			
		global $categoria_blog;		
		
		$args = array(
			'category_name' => $categoria_blog,
			'orderby' => 'date',
			'order' => 'DESC',
			'year' => $ano_blog,
			'monthnum' => $mes_blog,
			'posts_per_page' => $options["itens"],
			'post_type' => 'blog_post',
			'post__not_in'=> $excluded_ids,
			);
		$posts = get_posts( $args );		
					
			
		
		if(is_array($posts) && count($posts)> $itensOriginal)
		{	
			$this->HasMoreData = true;
			$posts = array_slice($posts, 0, count($posts) -1);
		}
		$VOList = $PostVO->GetPostVOList($posts);
		restore_current_blog();
		return $VOList;	
	}
	
	
	
		
}

?>