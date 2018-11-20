<?php
require_once(ABSPATH."/FAMCore/BO/Galeria.php");
$galeriaBO = new Galeria();
$albuns = $galeriaBO->GetData($options);

if($options["return"] != 'onlyitens')
{
	wp_reset_query();
	$hide = (is_fam_mobile() && (!is_array($albuns) || count($albuns) == 0) && is_home())? " display:none;" : "";
	?>	
	<section class="galeria_media" style="<? echo $hide;?>float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;margin-top:<?echo $options["margin_top"];?>;" >
		
		<? 
			if(is_content_archive('albuns')) echo '<h1 class="single_type_label galeria_icon topic_label_icon topic_font_color hand_font">'; else echo "<h2>";
			if($options["title"] != null)
			{
				echo $options["title"];
			}
			else
			{
				echo "Galeria";
			}
			if(is_content_archive('albuns')) echo '</h1>'; else echo "</h2>";
			?>
		
		<ul class="galeriafoto">
			<? 
			if(is_array($albuns) && count($albuns) > 0)
			{
				foreach($albuns as $album)
				{ 
					if($options["foto_size"] != "thumb")	
					{			
						$srcCapa =($album->MidiaPrincipal->ImageGaleryThumbSrc == null)? "/wp-content/themes/images/semcapa.png": $album->MidiaPrincipal->ImageGaleryThumbSrc;							
						?>
						<li>
							<div class="album"><a href="<?php echo $album->AlbumUrl ?>"><img src="<?echo $srcCapa;?>" alt="<?php echo $album->Titulo ?>"></a></div>
							<div class="album-info">
								<h3><a href="<?php echo $album->AlbumUrl ?>"><?php echo $album->Titulo ?></a><span> (<?php  echo $album->MediasCount; ?>)</span></h3>
								<h4><?php echo utf8_encode(strftime("%d %b %Y", strtotime(str_replace('/', '-', $album->DataPublicacao))));	?></h4>
								<h4 class="localAlbum"><span class="location_ico_small"></span><span><?php if($album->Location->Local != null) {echo $album->Location->GetShortLocation();}else {echo "Não especificado";} ?></span></h4>
							</div>
							<input type='hidden' class='itemId' value="<? echo $album->AlbumId; ?>"/>
							
							<?								
								if($options["show_admin_controls"] == "yes" && is_user_logged_in() && current_user_can('edit_albuns'))
								{
								?>
									<div class="admin_controls">
										<a href="?post_type=albuns&action=edit&p=<? echo $album->AlbumId ?>" class="ver">Editar</a><span class="separator"> |</span> 	<a onclick="return confirm('Tem certeza que deseja esxcluir o álbum [<? echo $album->AlbumId; ?>] com ID <? echo $album->AlbumId; ?>?');" href="?post_type=atualizacao&action=delete&p=<? echo $album->AlbumId ?>" class="ver">Excluir</a>				
									</div>
								<?
								}	
							?>
							
						</li>
						<?	
					}
					else
					{
						$srcCapa =($album->MidiaPrincipal->ImageThumbSrc == null)? "/wp-content/themes/images/semcapa.png": $album->MidiaPrincipal->ImageThumbSrc;							
						?>
						<li>
							<div class="album"><a href="<?php echo $album->AlbumUrl ?>"><img src="<?echo $srcCapa;?>" alt="<?php echo $album->Titulo ?>"></a></div>
							<div class="album-info">
								<h3><a href="<?php echo $album->AlbumUrl ?>"><?php echo $album->Titulo ?></a><span> (<?php  echo $album->MediasCount; ?>)</span></h3>
								<h4><?php echo utf8_encode(strftime("%d %b %Y", strtotime(str_replace('/', '-', $album->DataPublicacao))));	?></h4>
								<h4 class="localAlbum"><span class="location_ico_small"></span><span><?php if($album->Location->Local != null) {echo $album->Location->GetShortLocation();}else {echo "não especificado";} ?></span></h4>
							</div>
							
							<?								
								if($options["show_admin_controls"] == "yes" && is_user_logged_in() && current_user_can('edit_albuns'))
								{
								?>
									<div class="admin_controls">
										<a href="?post_type=albuns&action=edit&p=<? echo $album->AlbumId ?>" class="ver">Editar</a><span class="separator"> |</span> 	<a onclick="return confirm('Tem certeza que deseja esxcluir o status [<? echo $album->AlbumId; ?>] com ID <? echo $album->AlbumId; ?>?');" href="?post_type=atualizacao&action=delete&p=<? echo $album->AlbumId ?>" class="ver">Excluir</a>				
									</div>
								<?
								}	
							?>
							
							<input type='hidden' class='itemId' value="<? echo $album->AlbumId; ?>"/>
						</li>
						<?	
					}
					
										
				}	
					
				if($options["show_more"] == "yes" && $galeriaBO->HasMoreData)
				{						
					echo "<li class='loadmore'>";							
					widget::Get("load-more-content", array("itens"=> 4,"content_type"=>"galeria", 'show_admin_controls'=>$options["show_admin_controls"],'foto_size'=>$options["foto_size"], 'excluded_ids'=>$options["excluded_ids"]));					
					echo "</li>";
				}		
				if(! $galeriaBO->HasMoreData)
				{			
					echo "<li style='display:none;' class='no_more'></li>";		
				}
			}
			else
			{
				?><span class="no_content">Sem albuns até o momento<span><?
			}
			?>						
		</ul>
	</section>
<?
}
else
{
	if(is_array($albuns) && count($albuns) > 0)
	{
		foreach($albuns as $album)
		{ 	
			if($options["foto_size"] != "thumb")	
			{					
				$srcCapa =($album->MidiaPrincipal->ImageGaleryThumbSrc == null)?  "/wp-content/themes/images/semcapa.png": $album->MidiaPrincipal->ImageGaleryThumbSrc;							
				?>
				<li>
					<div class="album"><a href="<?php echo $album->AlbumUrl ?>"><img src="<?echo $srcCapa;?>" alt="<?php echo $album->Titulo ?>"></a></div>
					<div class="album-info">
						<h3><a href="<?php echo $album->AlbumUrl ?>"><?php echo $album->Titulo ?></a><span> (<?php  echo $album->MediasCount; ?>)</span></h3>
						<h4><?php echo utf8_encode(strftime("%d %b %Y", strtotime(str_replace('/', '-', $album->DataPublicacao))));	?></h4>
						<h4 class="localAlbum"><span class="location_ico_small"></span><span><?php if($album->Location->Local != null) {echo $album->Location->GetShortLocation();}else {echo "não especificado";} ?></span></h4>
					</div>
					<input type='hidden' class='itemId' value="<? echo $album->AlbumId; ?>"/>
					<?
						if($options["show_admin_controls"] == "yes" && is_user_logged_in() && current_user_can('edit_albuns'))
						{
							?>
								<div class="admin_controls">
									<a href="?post_type=albuns&action=edit&p=<? echo $album->AlbumId ?>" class="ver">Editar</a><span class="separator"> |</span> 	<a onclick="return confirm('Tem certeza que deseja esxcluir o status [<? echo $album->AlbumId; ?>] com ID <? echo $album->AlbumId; ?>?');" href="?post_type=atualizacao&action=delete&p=<? echo $album->AlbumId ?>" class="ver">Excluir</a>				
								</div>
							<?
						}	
					?>
				</li>
				<?
			}
			else
			{
				$srcCapa =($album->MidiaPrincipal->ImageThumbSrc == null)? "/wp-content/themes/images/semcapa.png": $album->MidiaPrincipal->ImageThumbSrc;							
				?>
				<li>
					<div class="album"><a href="<?php echo $album->AlbumUrl ?>"><img src="<?echo $srcCapa;?>" alt="<?php echo $album->Titulo ?>"></a></div>
					<div class="album-info">
						<h3><a href="<?php echo $album->AlbumUrl ?>"><?php echo $album->Titulo ?></a><span> (<?php  echo $album->MediasCount; ?>)</span></h3>
						<h4><?php echo utf8_encode(strftime("%d %b %Y", strtotime(str_replace('/', '-', $album->DataPublicacao))));	?></h4>
						<h4 class="localAlbum"><span class="location_ico_small"></span><span><?php if($album->Location->Local != null) {echo $album->Location->GetShortLocation();}else {echo "não especificado";} ?></span></h4>
					</div>
					<input type='hidden' class='itemId' value="<? echo $album->AlbumId; ?>"/>
					<?
						if($options["show_admin_controls"] == "yes" && is_user_logged_in() && current_user_can('edit_albuns'))
						{
							?>
								<div class="admin_controls">
									<a href="?post_type=albuns&action=edit&p=<? echo $album->AlbumId ?>" class="ver">Editar</a><span class="separator"> |</span> 	<a onclick="return confirm('Tem certeza que deseja esxcluir o status [<? echo $album->AlbumId; ?>] com ID <? echo $album->AlbumId; ?>?');" href="?post_type=atualizacao&action=delete&p=<? echo $album->AlbumId ?>" class="ver">Excluir</a>				
								</div>
							<?
						}	
					?>
				</li>
				<?	
			}			
			
		}
	
	
		if($options["show_more"] == "yes" && $galeriaBO->HasMoreData)
		{					
			echo "<li class='loadmore'>";					
			widget::Get("load-more-content", array("itens"=> 2,'show_admin_controls'=>$options["show_admin_controls"],"content_type"=>"galeria",'excluded_ids'=>$options["excluded_ids"]));					
			echo "</li>";
		}		
		if(!$galeriaBO->HasMoreData)
		{			
			echo "<li style='display:none;' class='no_more'></li>";		
		}
	}
	else
	{
		?><span class="no_content">Sem albuns até o momento<span><?
	}
}
					