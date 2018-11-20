<?php
require_once(ABSPATH."/FAMCore/BO/Destaque.php");
$destaque = new Destaque();
$destaques = $destaque->GetDestaques(5);
?>
<section class="slide-fotos theme-default">
	<div id="slider" class="nivoSlider">		
		<?php
		$validItens = 0;
		if(is_array($destaques) && count($destaques) > 0)	
		{
			foreach($destaques as $destaque)			
			{	
				if(CheckUploadedFileExists($destaque->ImageCroppedSrc))
				{	
					$validItens++;
					?><a class="fancybox" href="<? echo $destaque->OriginalImageVO->ImageLargeSrc; ?>"><img src="<? echo $destaque->ImageCroppedSrc; ?>" data-thumb="<? echo $destaque->ImageCroppedSrc; ?>" alt="<? echo $destaque->Descricao; ?>" /></a><?						
				}
			}
		}
		if($validItens == 0)				
		{
			?><a class="fancybox" href="/wp-content/themes/images/famtravel.png"><img src="/wp-content/themes/images/famtravel.png" data-thumb="/wp-content/themes/images/famtravel.png" alt="slide de exemplo" /></a><?						
		}
		?>  
		
		      
    </div><!-- end slider -->
</section><!-- end slide-fotos -->
