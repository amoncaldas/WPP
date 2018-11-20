<?php

/**
 * class ViajanteVO
 *
 * Description for class ViajanteVO
 *
 * @author:
*/

require_once(ABSPATH. "/FAMCore/BO/Imagem.php");
require_once("LocationVO.php");
require_once("ViagemVO.php");


class ViajanteVO  {

	public $DataNascimento;
	public $LocalNascimento;
	public $LocalResidencia;
	public $UserLogin;
	public $UserLevel;
	public $UserId;
	public $UserImage;
	public $FirstName;
	public $LastName;
	public $FullName;
	public $SitesViagem = array();
	public $UserProfile;
	public $UserEmail;
	public $UserNickName;	
	public $ViajanteUrl;
	public $CountForumTopics;
	public $CountForumMessages;
	public $UserRole;
	public $UserRoles;
	public $UrlPerfilFacebook;
	
	
	
	/**
	 * ViajanteVO constructor
	 *
	 * @param 
	 */
	function ViajanteVO($id, $populateStatistics = false) {	
		if($id > 0)
		{			
			$checked_user_id = $id;
			if($checked_user_id == 1)
			{
				$checked_user_id = 2;
			}
			$userDataChecked = get_userdata($checked_user_id);			
			$userData = get_userdata($id);	
			
			$wp_user = new WP_User( $id );
			
			
				
			if ( !empty( $wp_user->roles ) && is_array( $wp_user->roles ) ) {
				$this->UserRole = $wp_user->roles[0];
				$this->UserRoles = $wp_user->roles;					
			}	
							
			$this->FirstName = $userData->first_name;
			if($this->FirstName == null || $this->FirstName == "")
			{
				$this->FirstName = $userData->display_name;
			}
			$this->LastName = $userData->last_name;
			$this->UserLogin = $userData->user_login;
			$this->FullName = $userDataChecked->display_name;
				
			if($this->FullName == null || $this->FullName == "")
			{
				$this->FullName = $this->UserLogin;
			}
				
			if($this->FirstName == null || $this->FirstName == "")
			{
				$userData->display_name = $this->display_name;
			}	
			if($id ==1)
			{
				$this->FullName = $this->FullName." adm";
				$this->display_name = $this->display_name." adm";
			}			
				
			$this->UserLevel = $userData->user_level;
			$this->UserId = $userData->ID;
				
			$this->UserProfile = $userData->description;
			$this->UserEmail = $userData->user_email;						
			$this->UserNickName = $userDataChecked->user_nicename;				
				
			$this->LocalNascimento = new LocationVO();
			$this->LocalResidencia = new LocationVO();
			$this->DataNascimento =  get_the_author_meta("data_de_nascimento", $id);				
			$this->LocalNascimento->Local = get_the_author_meta("local_de_nascimento", $id);			
			$this->LocalResidencia->Local = get_the_author_meta("local_de_residencia", $id);
			if($this->LocalResidencia->Local == null)
			{
				$this->LocalResidencia->Local = "Não informado";
			}
			$this->UrlPerfilFacebook = get_the_author_meta("url_perfil_facebook", $id);				
			
			$site_id = get_the_author_meta("_fam_upload_site_id", $checked_user_id);
			$img_id = get_the_author_meta("_fam_upload_id_", $checked_user_id);				
			
			if($site_id > 0 && switch_to_blog($site_id))
			{			
				try
				{		
					$this->UserImage = Imagem::GetImage($img_id);
				}
				catch(exception $ex){
					delete_user_meta($userId, '_fam_upload_id_');
					delete_user_meta($userId, '_fam_upload_site_id');				
				}						
				restore_current_blog();
			}			
				
			$this->LocalNascimento->Latitude = get_the_author_meta("latitude_de_nascimento", $id);		
			$this->LocalNascimento->Longitude = get_the_author_meta("longitude_de_nascimento", $id);		
			$this->LocalResidencia->Longitude = get_the_author_meta("longitude_de_residencia", $id);		
			$this->LocalResidencia->Latitude = get_the_author_meta("latitude_de_residencia", $id);		
			
			$this->ViajanteUrl = str_replace("blog/author","viajantes", get_author_posts_url($checked_user_id, $this->UserNickName));	
				
			if($populateStatistics === true)
			{		
				$data = Viajante::GetUserStatistics($this->UserEmail);						
				$this->CountForumMessages = $data->CountForumMessages;
				$this->CountForumTopics = $data->CountForumTopics;						
			}
		}	
		if($this->UserImage == null)
		{
			$imagemVO = new ImagemVO();
			$imagemVO->ImageGaleryThumbSrc = network_home_url()."wp-content/themes/images/user/user_galerythumb.png";
			$imagemVO->ImageFullSrc = network_home_url()."wp-content/themes/images/user/user_full.png";
			$imagemVO->ImageLargeSrc = network_home_url()."wp-content/themes/images/user/user_large.png";
			$imagemVO->ImageMediumSrcs = network_home_url()."wp-content/themes/images/user/user_medium.png";
			$imagemVO->ImageThumbSrc = network_home_url()."wp-content/themes/images/user/user_thumb.png";
			$this->UserImage = $imagemVO;			
		}	
		
			
	}
	
	public static function GetViajanteSites($userId)
	{
		$viagensVO = array();
		$sitesData = get_blogs_of_user($userId);
		if(is_array($sitesData) && count($sitesData) > 0)
		{
			foreach($sitesData as $siteData)
			{
				if($siteData->userblog_id != 1)
				{
					$viagemVO = new ViagemVO($siteData->userblog_id);			
					$viagensVO[] = $viagemVO;
				}
			}
		}
		
		return $viagensVO;
	}
	
	public function HasRole($role)
	{
		if($this->UserRoles == null)
		{
			return false;
		}		
		return array_key_exists($role, $this->UserRoles);		
	}
}

?>