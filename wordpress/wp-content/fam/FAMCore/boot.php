<?php
if($_COOKIE["mobile_switch"] == "true")
{	
	setcookie('mobile_switch', "true", time() - + 60*3,"/");
	header("Cache-Control: no-cache, must-revalidate");	
}
elseif(strpos($_SERVER["SERVER_NAME"],"teste.") === false)
{
	header("Cache-Control: max-age=600");	
}
if(strpos($_SERVER["SERVER_NAME"],"teste.") === false)
{
	error_reporting(0);
}
else
{
	//error_reporting(E_ALL); ini_set('display_errors', 'On'); 
}

setlocale(LC_ALL, NULL);
setlocale(LC_ALL, 'pt_BR.iso88591');
require_once(ABSPATH."/wp-content/FAMComponents/widget.php");

global $viagemId;
$viagemId = get_current_blog_id();
if($viagemId != 1)
{
	require_once( ABSPATH . '/FAMCore/BO/Trajeto.php' );
	$trajetoBO = new Trajeto();
	global $trajetos;
	$trajetos = $trajetoBO->GetTrajetos($viagemId);	
}

global $Meta;

