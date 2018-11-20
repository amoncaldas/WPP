<?php

/**
 * class RoteiroVO
 *
 * Description for class RoteiroVO
 *
 * @author:
*/

require_once("TrajetoVO.php");
require_once("LocationVO.php");
require_once( ABSPATH . '/FAMCore/BO/Trajeto.php' );

class RoteiroVO  {
	
	public $trajetos = array();

	/**
	 * RoteiroVO constructor
	 *
	 * @param 
	 */
	function RoteiroVO($viagemId = null) {		
		
			
		if($viagemId == null)
		{
			$viagemId = get_current_blog_id();
		}
			
		$trajeto = new Trajeto();
		$this->trajetos = $trajeto->GetTrajetos($viagemId);			
		
	}
}



