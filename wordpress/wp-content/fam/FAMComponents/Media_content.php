<?	
$options["excluded_ids"] = $_POST["excluded_ids"];	
require_once(ABSPATH."/FAMCore/BO/Media.php");
$mediaBO  = new Media();
$medias = $mediaBO->GetData($options);


if($options["destaques_video"] != "yes" || (is_array($medias) && count($medias) > 0))
{
	if($options["return"] != "onlyitens")
	{
	?>
	<div class="fotosevideos" style="width:<?echo $options["width"].' !important'?>;float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important';?>">
		<h2>
			<? if($options["title"] != null){
					echo $options["title"];
				}
				else{
					echo "Fotos";
				}
			?>
		</h2>	
		<?		
			if(is_array($medias) && count($medias) > 0)	
			{	
				echo "<ul>";	
				$counter = 1;	
				foreach($medias as $media)
				{						
					if($options["destaques_video"] == "yes" &&  strpos($media->MimeType,"video") === 0)					
					{	
						
						$src = $media->YoutubeBaseThumbUrl."hqdefault.jpg";											
						$mediaSrc = "<img style='width:445px;height:333px;' src='".$src."' alt='".$media->Titulo."' />";
						
						$liCss = "";	
						if($options["max_visible_itens"] != null)	
						{
							if($counter > $options["max_visible_itens"])
							{
								$liCss = " style='display:none;'";
							}
						}	
						else if ($counter > 2)
						{
							$liCss = " style='display:none;'";
						}									
						
						?>
							<li class="video_destaque" <? echo $liCss;?> >								
								<a class="fancybox fancybox.iframe" href="<?php echo  $media->MainUrl; ?>?modestbranding=1&rel=0&autoplay=1" data-fancybox-group="videos_destaque_main"  title="<?echo $media->Titulo?>"><? echo $mediaSrc; ?></a>													
								<a class="fancybox fancybox.iframe play_video_destaque" href="<?php echo  $media->MainUrl; ?>?modestbranding=1&rel=0&autoplay=1" data-fancybox-group="videos_destaque_play"  title="<?echo $media->Titulo?>"></a>
								<a class="fancybox fancybox.iframe video_title" href="<?php echo  $media->MainUrl; ?>?modestbranding=1&rel=0&autoplay=1" data-fancybox-group="videos_destaque_title"  title="<?echo $media->Titulo?>"><? echo GetSubContent($media->Titulo, 50, true); ?></a>
								<input type='hidden' class='itemId' value="<? echo $media->MediaId; ?>"/>
							</li>				
						<?	
						$counter++;	
					}
					else
					{				
						if($media->YoutubeBaseThumbUrl != null)
						{																
							$src = $media->YoutubeBaseThumbUrl."hqdefault.jpg";											
							$mediaSrc = "<img style='width:120px;height:68px;' src='".$src."' alt='".$media->Titulo."' />";
							$mediaLink =  $media->MainUrl."?modestbranding=1&rel=0&autoplay=1";
							$iframeclass = " fancybox.iframe";											
						}
						else
						{
							$src = $media->ImageThumbSrc;
							
							$mediaSrc = "<img src='".$src."' alt='' />";
							$mediaLink = $media->ImageLargeSrc;
						}
						if($src != null)
						{	
							$hide = ($options["destaques_video"] == "yes" && $media->YoutubeBaseThumbUrl == null)? " style='display:none;'" : "";			
							?>
							<li <?echo $hide;?>>
								<a class="fancybox <? echo $iframeclass;?>" title="<? echo $media->Titulo?>" rel="ultimas-medias" href="<? echo $mediaLink;?>"><? echo $mediaSrc; ?></a>
								<input type='hidden' class='itemId' value="<? echo $media->MediaId; ?>"/>
							</li>
							<?
						}
					}
				}	
						
				
				if($options["show_more"] == "yes" && $mediaBO->HasMoreData)
				{					
					echo "<li class='loadmore'>";				
					widget::Get("load-more-content", array("itens"=> 4,"content_type"=>"fotos", 'orderby'=>$options["orderby"]));					
					echo "</li>";
				}		
				if(!$mediaBO->HasMoreData)
				{			
					echo "<li style='display:none;' class='no_more'></li>";		
				}
				echo "</ul>";	
				if($options['show_ver_albuns'] == null)
				{		
					?><a href="<?=bloginfo('url')?>/albuns" class="ver">Ver albuns</a><?
				}
			}
			else
			{
				echo "Sem fotos no momento";
			}
				
		?>
		</div><!-- end fotosevideos -->
	<?
	}
	else
	{	
		if(is_array($medias) && count($medias) > 0)	
		{				
			foreach($medias as $media)
			{	
				if($options["foto_size"] != "gallery")
				{											
					if($media->YoutubeBaseThumbUrl != null)
					{																
						$src = $media->YoutubeBaseThumbUrl."hqdefault.jpg";											
						$mediaSrc = "<img style='width:120px;height:68px;' src='".$src."' alt='".$media->Titulo."' />";
						$mediaLink =  $media->MainUrl."?modestbranding=1&rel=0&autoplay=1";
						$iframeclass = " fancybox.iframe";											
					}
					else
					{
						$src = $media->ImageThumbSrc;
						$mediaSrc = "<img src='".$src."' alt='".$media->Titulo."' />";
						$mediaLink = $media->ImageLargeSrc;
					}
					if($src != null)
					{				
						?>
						<li>
							<a class="fancybox <? echo $iframeclass;?>" title="<? echo $media->Titulo?>" rel="ultimas-medias" href="<? echo $mediaLink;?>"><? echo $mediaSrc; ?></a>
							<input type='hidden' class='itemId' value="<? echo $media->MediaId; ?>"/>
						</li>
						<?
					}
				}
				else
				{													
					if($media->YoutubeBaseThumbUrl != null)
					{						
						$src = $media->YoutubeBaseThumbUrl."hqdefault.jpg";											
						$mediaSrc = "<img style='width:190px;height:140px;' src='".$src."' alt='' /><div class='play_video'></div>";
						$mediaLink =  $media->MainUrl."?modestbranding=1&rel=0&autoplay=1";
						$fancyIframe = " fancybox.iframe";					
					}
					else
					{
						$mediaSrc = "<img src='".$media->ImageGaleryThumbSrc."' alt='".$media->Titulo."' />";
						$mediaLink = $media->ImageLargeSrc;						
					}	
					if($mediaSrc != null)					
					?>
						<li>
							<div class="foto">
								<a class="fancybox <?echo $fancyIframe;?>" href="<?php echo $mediaLink; ?>" data-fancybox-group="album-<?echo $options["parentId"];?>"  title="<?echo $media->Titulo?>"><?echo $mediaSrc;?></a>
							</div>
							
							<div class="album-info">
									<h3><a class="fancybox <?echo $fancyIframe;?>" title="<?echo $media->Titulo;?>" data-fancybox-group="<?if($video === 0) echo "album-".$options["parentId"]; else echo "album-lk-".$options["parentId"];?>" href="<?php echo $mediaLink ?>"><?php echo GetSubContent($media->Titulo,25,true) ?></a></h3>
									<h4><?php echo utf8_encode(strftime("%d %b %Y", strtotime(str_replace('/', '-', $media->DataPublicacao)))); ?></h4>
									<? if($media->Location->Local != null || $mediaBO->AlbumData->Location->Local != null){ ?>
									<h4><span class="location_ico_small_white"></span><?php if($media->Location->Local != null) {echo $media->Location->GetLocalSubString(15);}else { if($mediaBO->AlbumData->Location) {echo $mediaBO->AlbumData->Location->GetLocalSubString(15);}} ?></h4>
									<?}?>
							</div>
							<input type='hidden' class='itemId' value="<? echo $media->MediaId; ?>"/>
						</li>				
					<?			  
				}			
			}		
		
			if($options["show_more"] == "yes" && $mediaBO->HasMoreData)
			{			
				$itens = $options["show_more_itens"];
				if($itens == null)
				{
					$itens = 8;
				}		
				echo "<li class='loadmore'>";				
				widget::Get("load-more-content", array("itens"=>$itens,"content_type"=>"fotos",'orderby'=>$options["orderby"],'parentId'=>$mediaBO->AlbumData->AlbumId,'foto_size'=>$options["foto_size"]));					
				echo "</li>";
			}		
			if(!$mediaBO->HasMoreData)
			{			
				echo "<li style='display:none;' class='no_more'></li>";		
			}		
		}
	
	}
}