<?php

/**
 * class DestaqueVO
 *
 * Description for class DestaqueVO
 *
 * @author:
*/

require_once( ABSPATH . '/FAMCore/Data/DataAccess.php' );
require_once('ImagemVO.php' );

class DestaqueVO extends DataAccess  {

	public $ImageCroppedSrc;
	public $Descricao;		
	public $OriginalImageVO;
	public $DestaqueId;
	public $ImagemDestaqueViagem;

	/**
	 * DestaqueVO constructor
	 *
	 * @param 
	 */
	function DestaqueVO($id = null) {
		parent::DataAcess("destaque");
		if($id != null)
		{
			$post = get_post($id);
			$this->PopulateDestaqueVO($this,$post);
		}
	}
	
	public function PopulateDestaqueVO(&$destaqueVO, $post)
	{		
		define('DESTAQUE_FOLDER', 'cropped_destaque/');
		$rootdirFolder = get_bloginfo('url').'/files/';
		
		if($post->blog_id != null)
		{		
			switch_to_blog($post->blog_id)	;	
			$rootdirFolder = get_bloginfo('url').'/files/';
			restore_current_blog();
		}		
		$destaqueVO->ImageCroppedSrc = $rootdirFolder.DESTAQUE_FOLDER.get_post_meta($post->ID, "cropped_img_destaque", true);
		$destaqueVO->Descricao =  $post->post_title;
		$destaqueVO->OriginalImageVO = $this->GetDestaqueOriginalImageVO($destaqueVO->ImageCroppedSrc);
		$destaqueVO->DestaqueId = $post->ID;		
	}
	
	public function GetDestaqueOriginalImageVO($crpSrc)
	{
		$urlParts = explode("/destaque_",$crpSrc);
		$id_parts = explode("_", $urlParts[1]);
		$mediaId = $id_parts[0];
		$imageVO  = new ImagemVO($mediaId);		
		return 	$imageVO;						
	}	
	
	
}

?>