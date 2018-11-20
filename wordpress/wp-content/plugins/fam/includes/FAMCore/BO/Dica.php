<?php

/**
 * class Dica
 *
 * Description for class Dica
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Post.php' );

class Dica extends Post  {

	/**
	 * Dica constructor
	 *
	 * @param 
	 */
	function Dica($id = null, $viagemId = null) {
		parent::Post("dicas",$id,$viagemId);
		$this->MultiSiteData = false;
	}
}

?>