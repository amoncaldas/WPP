<?php

/**
 * class Destaque
 *
 * Description for class Destaque
 *
 * @author:
*/

require_once( ABSPATH . '/FAMCore/BO/Conteudo.php' );
require_once( ABSPATH . '/FAMCore/VO/DestaqueVO.php' );

	

class Destaque extends Conteudo  {

	/**
	 * Destaque constructor
	 *
	 * @param 
	 */
	function Destaque($viagemId = null) {		
		parent::Conteudo("Destaque", $viagemId);
	}
	
	public function GetDestaque($id)
	{
		return new DestaqueVO($id);
	}
	
	public function GetDestaquePrincipal()
	{
		$return = $this->GetDestaques(1);
		
		return	$return[0];		
	}
	
	public function GetDestaques($itens = null, $lastId = null)
	{
		if($itens == null)
		{
			$itens = 5;
		}
		
		$destaques = get_posts( array( 'post_type' => 'destaque','posts_per_page'=>$itens,'orderby'=>'menu_order','order'=>'asc'));			
		$destaquesVO = array();		
		foreach ($destaques as $destaque ) {				
			$destaqueVO = new DestaqueVO($destaque->ID);			
			if($destaqueVO->ImageCroppedSrc != null)
			{		
				$destaquesVO[] = $destaqueVO;
			}			
		}		
		return $destaquesVO;
			
			
		
	}
	
	
		
}

?>