<?php
header('Content-type: text/javascript');
$file = dirname(__FILE__).'/viagens.js';

if(file_exists($file) &&  time() < filemtime($file) + 1000000)
{
	$file = file_get_contents( dirname(__FILE__).'/viagens.js');	
	echo "/* using cached file*/ ".$file;	
}
else
{	
	require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/FAMCore/BO/Viagem.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/FAMCore/VO/PostVO.php");
	$viagem = new Viagem();
	$viagens = $viagem->GetData(array('itens'=>100));
	$hasViagem = false;	
	$content = 	"/* Generated at ". date('d-m-Y h:i:s')."*/";
	$content .= " var pontosViagem = [];";
	
	foreach($viagens as $viagemVO)
	{		
		if(is_array($viagemVO->Roteiro) && count($viagemVO->Roteiro) > 0)
		{
			$hasViagem = true;
			$counterViagem = 1;
			
			foreach($viagemVO->Roteiro as $trajeto)
			{
				$content .= "var pontoViagem = new Object();";
				if($counterViagem == 1)
				{
					$content .= 'pontoViagem.local = "'.$trajeto->LocationPartida->Local.'";';
					$content .= "pontoViagem.lat = '". $trajeto->LocationPartida->Latitude."';";
					$content .= "pontoViagem.long = '". $trajeto->LocationPartida->Longitude."';";					
				}
				else
				{
					$content .= 'pontoViagem.local = "'. $trajeto->LocationChegada->Local.'";';
					$content .= "pontoViagem.lat = '". $trajeto->LocationChegada->Latitude."';";
					$content .= "pontoViagem.long = '". $trajeto->LocationChegada->Longitude."';";
				}
				$content .= " pontoViagem.urlViagem = '".$viagemVO->ViagemUrl."';";
				$content .= " pontoViagem.imagemViagem = '". $viagemVO->MidiaPrincipal->OriginalImageVO->ImageThumbSrc."';";
				$content .= " pontoViagem.titulo = '".$viagemVO->Titulo."';";
				$content .= " pontoViagem.qtdViajantes = ".$viagemVO->QtdViajantes.";";
				$content .= " pontoViagem.qtdLocais = ".$viagemVO->NumLocais.";";
					
				$content .= " pontosViagem.push(pontoViagem);";
				
					
				$counterViagem++;	
			}	
		}									  			
	}	

	$args = array(
		'posts_per_page' => '100',
		'post_type' => 'blog_post',	
		'meta_query'=> array(		
			array(
				'key' => 'local',
				'compare' => '!=',
				'value' => '',		  
				)
			)
		);

	$posts = get_posts($args);	

	foreach($posts as $blog_post)
	{	
		$content .= " var pontoViagem = new Object();";
			
		$blogVO = new PostVO("blog_post",$blog_post->ID);
		$content .= "pontoViagem.local = '".$blogVO->Location->Local."';";
		$content .= "pontoViagem.lat = '".$blogVO->Location->Latitude."';";
		$content .= "pontoViagem.long = '".$blogVO->Location->Longitude."';";
			
			
		$content .= "pontoViagem.urlViagem = '".$blogVO->PostUrl."';";
		$content .= "pontoViagem.imagemViagem = '".$blogVO->MidiaPrincipal->ImageThumbSrc."';";
		$content .= "pontoViagem.titulo = '".$blogVO->Titulo."';";	
		$content .= "pontoViagem.tipo = 'blog';";							
		$content .= "pontosViagem.push(pontoViagem);";
			
	}

	$logtime = "";
	$file = dirname(__FILE__).'/viagens.js';
	if(file_exists($file)){
		$logtime = "/* time:".time()." < filetime:".(filemtime($file) + 10000)." */";
	}
				
	file_put_contents($file, $content,LOCK_EX);
	
	echo "/* using just regenerated file*/ ".$logtime.$content;	
		
}
