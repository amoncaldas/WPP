<?php

/**
 * class VideoVO
 *
 * Description for class VideoVO
 *
 * @author:
*/

require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/MediaVO.php' );

//fix it
if(class_exists(MediaVO) )
{
	class VideoVO extends MediaVO  {
			
		public $VideoServer;

		/**
			* VideoVO constructor
			*
			* @param 
			*/
		function VideoVO($id = null) {
				
			if($id != null)
			{
				parent::MediaVO($id);				
				$this->SetVideoServer();		
			}

		}	
			
		public function SetVideoServer()
		{
			if(strpos($this->MainUrl, "fazendoasmalas.com") > -1)
			{
					
				$this->VideoServer = "local";
			}
			else
			{
				$this->VideoServer = "youtube";
			}
		}
	}
}
	


	//function GetIncludingFile()
	//{
	//    $file = false;
	//    $backtrace =  debug_backtrace();
	//    $include_functions = array('include', 'include_once', 'require', 'require_once');
	//    for ($index = 0; $index < count($backtrace); $index++)
	//    {
	//        $function = $backtrace[$index]['function'];
	//        if (in_array($function, $include_functions))
	//        {
	//            $file = $backtrace[$index]['file'];
	//            break;
	//        }
	//    }
	//    return $file;
	//}

	//var_dump(get_included_files());

?>