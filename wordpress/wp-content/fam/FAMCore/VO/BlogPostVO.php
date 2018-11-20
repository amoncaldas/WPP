<?php

/**
 * class BlogPostVO
 *
 * Description for class BlogPostVO
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/Data/DataAccess.php' );
require_once("ViajanteVO.php");
require_once("LocationVO.php");

class BlogPostVO extends DataAccess {


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
	public $ImagemPrincipal;
	public $ImagensAnexadas;
	public $Autor;
	public $Status;	

	/**
	 * BlogPostVO constructor
	 *
	 * @param 
	 */
	function BlogPostVO($id = null) {
		parent::DataAcess("blog_post");
		if($id != null)
		{
			$this->GetBlogPostById($id);
		}
	}
	
	private function GetBlogPostById($id)
	{		
		$post = parent::GetById($id);				
		$this->PopulateBlogPostVO($this,$post);			
		return $this;
	}	
	
	public function GetSubContent($lenght)
	{
		$content = strip_tags($this->Conteudo, '');
		if(strlen($content) > $lenght)
		{			
			if(strpos($content, " ", $lenght) > $lenght)
			{
				return substr($content, 0, strpos($content, " ", $lenght))." ...";
			}
			else
			{
				$lenght = ($lenght > 20)? ($lenght -20): 0;								
				return substr($content, 0, strpos($content, " ", $lenght))." ...";
			}				
		}
		else
		{
			return $content;
		}			
	}
	
	public function PopulateBlogPostVO(BlogPostVO &$blogPostVO, $post)
	{	   
							
		$blogPostVO->Titulo = $post->post_title;
		$blogPostVO->PostId = $post->ID;
		$blogPostVO->PostUrl =  get_permalink($post->ID);
		$blogPostVO->Conteudo = $post->post_content;
		$blogPostVO->Resumo = $post->post_excerpt;	
		$blogPostVO->DataPublicacao =  $post->post_date;
		$blogPostVO->Autor =  new ViajanteVO($post->post_author);
		$blogPostVO->Status = $post->post_status;
		
		$cats = (get_the_category($post->ID));
		if(is_array($cats) && count($cats))	
		{
			foreach((get_the_category($post->ID)) as $cat) {
				$categoriaVO = new CategoriaVO();
				$categoriaVO->CategoriaID = $cat->cat_ID;
				$categoriaVO->CategoriaDescricao = $cat->cat_name;
				$blogPostVO->Categorias[] = 	$categoriaVO;
			}
		}
		
		$tags = (get_the_tags($post->ID));
		if(is_array($tags) && count($tags) > 0)
		{
			foreach($tags as $tag) {			
				$tagVO  = new TagVO();
				$tagVO->TagID = $tag->term_id;
				$tagVO->TagDescricao = $tag->name;
				$blogPostVO->Tags[] = $tagVO;
			}
		}	
		
		//recupera a imagem principal do relato	
		
		$imagemId = get_post_thumbnail_id( $post->ID );
		
		$blogPostVO->ImagemPrincipal	 = Imagem::GetImage( $imagemId);
		
		//recupera as imagens adicionais do relato	
		
		$blogPostVO->ImagensAnexadas = Imagem::GetImagens($post->ID);
		//var_dump($relatoVO->ImagensAnexadas);
		
		
		$blogPostVO->Location = new LocationVO();		
		
		
		$blogPostVO->Location->Local = get_post_meta($post->ID, "local", true);
		$blogPostVO->Location->Latitude = get_post_meta($post->ID, "latitude", true);
		$blogPostVO->Location->Longitude = get_post_meta($post->ID, "longitude", true);				
		$blogPostVO->DataPost = get_post_meta($post->ID, "data_de_visita", true);
		
		if( ($blogPostVO->ImagemPrincipal == null)) 
		{
			$blogPostVO->ImagemPrincipal =  $blogPostVO->Autor->UserImage;
		}		
		
	}
	
	
	/**
	 * @return RelatoVO[]
	*/
	public function GetBlogPostVOList($postsBlog)
	{		
		$posts = array();
		foreach($postsBlog as $postBlog)
		{			
			$postVO  = new BlogPostVO();
			$postVO->PopulateBlogPostVO($postVO,$postBlog);			
			$posts[] = $postVO;						
		}		
		return $posts;
	}
}

?>