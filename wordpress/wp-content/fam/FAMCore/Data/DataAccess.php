<?php

/**
 * class DataVO
 *
 * Description for class DataAcess
 *
 * @author:
*/


if(!isset($wpdb))
{
	require_once( ABSPATH . '/wp-config.php');
	require_once( ABSPATH . '/wp-includes/wp-db.php');
	require_once( ABSPATH . '/wp-includes/query.php');
}

require_once( ABSPATH . '/wp-load.php' );


abstract class DataAccess  {

	private $PostType;
	public $ViagemId;
	
	
	/**
	 * DataVO constructor
	 *
	 * @param 
	 */
	protected function DataAcess($customPostType) {
		$this->PostType = $customPostType;		
	}
	
	private function SetBlogId()
	{
		if($this->ViagemId != null && $this->ViagemId > 0)
		{
			global $switched;
			switch_to_blog($this->ViagemId); 
		}
	}
	
	
	public function QueryDB($queryString)
	{
		$this->SetBlogId();
		global $wpdb;
		return	$wpdb->get_results($queryString);		
	}
	
	public function QueryWPDB($wpQueryParans)
	{		
		$this->SetBlogId();				
		return query_posts($wpQueryParans);		
	}
	
	
	public function GetAll($itens = null)
	{		
		if($itens !=null && $itens > 0)
		{
			$args = array('post_type' => $this->PostType, 'posts_per_page' => $itens);
		}
		else
		{
			$args = array('post_type' => $this->PostType, 'posts_per_page'=> -1);	
		}			
		$result = $this->QueryWPDB($args);
		return $result;	
	}
	
	protected function GetByField($fieldName, $fieldValue)
	{
		if($fieldName == null || $fieldValue == null)
		{
			throw new Exception("parans FieldName and FieldValue can not be null");
		}
		$args = array('post_type' => $this->PostType, $fieldName => $fieldValue);
		return  $this->QueryWPDB($args);		
	}
	
	public function GetByFields($fields = array(), $customFieldsList = array())
	{			
		$args = array('post_type' => $this->PostType);				
		foreach (($fields) as $field =>$item){
			if(!array_key_exists($field, $args))
			{			
				$args[$field] = $item;				
			}
		}
		
		if(count($customFieldsList) > 0)
		{			
			$metaQueries = array();					
			foreach (($customFieldsList) as $customfield)
			{				
				$metaQuery = array();	
				$metaQuery["key"] = $customfield['fieldName'];
				$metaQuery["value"] = $customfield['fieldValue'];				
				if(array_key_exists('fieldCompare', $customfield))
				{
					$metaQuery["compare"] = $customfield['fieldCompare'];
				}
				else
				{
					$metaQuery["meta_compare"] = "==";
				}
				if(array_key_exists('fieldType', $customfield))
				{
					$metaQuery["type"] = $customfield['fieldType'];
				}
				
				$metaQueries[] = $metaQuery;				
			}
			$args["meta_query"] = $metaQueries;			
		}	
			
		return $this->QueryWPDB($args);				
	}	
	
	protected function GetByName($name)
	{
		$args = array('post_type' => $this->PostType, 'name' => $name);
		return $this->QueryWPDB($args);
	}
	
	protected function GetById($id)
	{			
		global $wp_query;
		if($wp_query->post->ID == $id && $wp_query->post->post_type == $this->PostType)
		{
			$post = $wp_query->post;				
		}
		else
		{
			$post = get_post($id);
		}		
		if($post == null || $post->ID == null)
		{
			throw new Exception('Exception:A post with id "'.$id.'"  was not foud while trying to get post for type "'.$this->PostType.'"');
		}
		
		if($post != null && $post->post_type != null && strtolower($post->post_type) != strtolower($this->PostType))
		{			
			throw new Exception("Exception:The post with ID ".$id." is not a post type of type ".$this->PostType."  but of type ".$post->post_type);
		}
		
		return $post;
	}
	
	protected function GetFirst()
	{		
		$all = $this->GetAll(1);
		return $all[0];		
	}		
}

?>