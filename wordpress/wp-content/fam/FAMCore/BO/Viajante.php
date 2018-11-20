<?php

/**
 * class Viajante
 *
 * Description for class Viajante
 *
 * @author:
*/
require_once( ABSPATH . '/FAMCore/BO/Conteudo.php' );
require_once( ABSPATH . '/FAMCore/VO/ViajanteVO.php' );
require_once( ABSPATH . '/FAMCore/VO/SiteViagemVO.php' );

class Viajante extends Conteudo {
	
	public $DadosViajante;	
	public $HasMoreData;
	
	function Viajante($id = null, $viagemId = null) {
		parent::Conteudo("user", $viagemId);
		if($id != null)
		{
			$this->DadosViajante = new ViajanteVO($id);									
		}				
	}
	
	
	/** 
	 * @return ViajanteVO[]
	**/
	public static function GetViajantesDeViagem($options)
	{		
		if($viagemId == null)
		{
			$viagemId = get_current_blog_id();
		}
		$viajantes = array();
		$args = array('blog_id'=>$options["viagemId"], 'orderby'=> 'id', 'order'=> 'asc','number'=>$options["itens"] );
		$arrayIds = array();
		$arrayIds[] = 1;
		
		if($options["excluded_ids"] != null)
		{
			$excluded_ids = $options["excluded_ids"];
			if(!is_array($excluded_ids))
			{
				$excluded_ids = split(",",$excluded_ids);					
			}
			if(is_array($excluded_ids) && count($excluded_ids > 0))
			{
				foreach($excluded_ids as $id)
				{						
					if(is_numeric($id))
					{
						array_push($arrayIds,$id);							
					}
				}					
			}
					
		}
		$args['exclude'] = $arrayIds;		
		$users = get_users( $args);
				
		if(is_array($users) && count($users))
		{
			foreach($users as $user)
			{
				$viajanteVO = new ViajanteVO($user->ID);
				$viajantes[] = $viajanteVO;
			}
		}		
		return $viajantes;
	}
	
	/** 
	 * @return ViajanteVO[]
	**/
	public static function GetViajantes($options)
	{			
		$viajantes = array();	
		$notIn = '';
		if($options["excluded_ids"] != null)
		{
			$excluded_ids = $options["excluded_ids"];
			if(!is_array($excluded_ids))
			{
				$excluded_ids = split(",",$excluded_ids);					
			}
			if(is_array($excluded_ids) && count($excluded_ids > 0))
			{
				$arrayIds = array();
				foreach($excluded_ids as $id)
				{						
					if(is_numeric($id))
					{							
						$notIn .= ' and ID <> '.$id;							
					}
				}					
			}					
		}			
		global $wpdb;
		//id <> is to do not show super admin and id <> 3 is to force do not show Viviane
		$sql = 	"SELECT ID FROM ". $wpdb->users." WHERE id <> '1' and id <> '3' and id <> '29' and deleted = '0' and spam = '0' ".$notIn. " order by ID ASC limit 0,".$options["itens"];						
		$users = $wpdb->get_results($sql);
					
		if(is_array($users) && count($users) > 0)
		{
			foreach($users as $user)
			{				
				$viagensViajante = Conteudo::GetSites(array('userId'=>$user->ID));		
				if(count($viagensViajante) > 0)
				{
					$viajanteVO = new ViajanteVO($user->ID);
					array_push($viajantes,$viajanteVO);					
				}				
			}				
		}	
			
		return $viajantes;
	}	
	
	
public function GetData($options)
	{		
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 1;
		}
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		
		$this->HasMoreData = false;	
		
		if($this->MultiSiteData == true && $options["viagemId"] == null)
		{				
			$users =  Viajante::GetViajantes($options);		
		}
		else
		{
			$users = $this->GetViajantesDeViagem($options);
		}	
		
		if(is_array($users) && count($users)> $itensOriginal)
		{		
						
			$this->HasMoreData = true;
			$users = array_slice($users, 0, count($users) -1);				
		}		
		return $users;		
	}
	
	public static function GetUserStatistics($user_email)
	{
		$data = new stdClass;
		global $wpdb;		
		$sql = 	'SELECT COUNT(comment_ID) FROM ' . $wpdb->comments. ' WHERE comment_author_email = "' .$user_email. '" and comment_approved = 1';			
		$data->CountForumMessages = $wpdb->get_var($sql);
		$user = get_user_by( 'email', $user_email);
		if($user->ID != NULL)
		{
			$data->CountForumTopics = count( get_posts(array('post_type' => 'forum', "author"=> $user->ID)));
		}
		else
		{
			$data->CountForumTopics = 0;
		}
		
		return $data;
	}
	
	public static function CheckIsValidViajante($viajanteId)
	{		
		if($viajanteId != null && $viajanteId > 1)
		{			
			//throw new exception(var_export(Conteudo::GetSites(array('userId'=>$viajanteId)),true));
			if(count(Conteudo::GetSites(array('userId'=>$viajanteId))) > 0)
			{    			
				return true;				
			}     			
		}
		return false;
		
	}
}

?>