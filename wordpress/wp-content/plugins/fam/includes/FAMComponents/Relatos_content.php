<?
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Relato.php");
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viajante.php");
$relatoBO = new Relato();
$relatos = $relatoBO->GetData($options); //options array cames from parent include

$viajante = new ViajanteVO($options["authorId"]);


if($options["return"] != "onlyitens")
	{
		wp_reset_query();
		
		$hide = (is_fam_mobile() && (!is_array($relatos) || count($relatos) == 0) && is_home())? " display:none;" : "";
		?>
		<section class="relatos" style="<? echo $hide;?>float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;">
			<? if(is_content_archive('relatos')) echo '<h1 class="single_type_label relato_icon topic_label_icon topic_font_color hand_font">'; else echo '<h2>'; ?>
				<?
					if($options['title'] != null)
					{
						echo $options['title'];					
					} 
					else if( $viajante->FirstName != null)
					{
						if($options['excluded_ids'] != null)
						{							
							echo "Outros relatos de ".$viajante->FirstName;
						}
						else
						{
							echo "Relatos de ".$viajante->FirstName;
						}
					}
					else
					{
						echo 'Últimos relatos';
					}
			if(is_content_archive('relatos')) echo "</h1>"; else echo "</h2>";
			
			?>
			
			<ul>
				<? }		
					if(is_array($relatos) && count($relatos)> 0)
					{	
						foreach($relatos as $relato) 
						{						
							$data = ($relato->DataRelato != null)? $relato->DataRelato: $relato->DataPublicacao;
							$dataFormated = strtotime(str_replace('/', '-', $data));
							$dataFormated = utf8_encode(strftime("%d %b %Y", $dataFormated));
							$data = strtotime(str_replace('/', '-', $data));
							$data = utf8_encode(strftime("%Y-%m-%d", $data));
							
							//$intermediateImage  = $relato->MidiaPrincipal->GetIntermediateImageSrc();						
							//$mainImage = ($intermediateImage == null)? $relato->MidiaPrincipal->ImageLargeSrc : $intermediateImage;
							
							?>
								<li <? if($options["show_large_image"] == "yes") echo "class='largeImage'"; ?>>																		
									<div class="autorInfo">
										<img alt="<? echo $relato->Titulo; ?>" class="foto_viajante_relato" src="<?  if($options["show_large_image"]) { echo  $relato->MidiaPrincipal->ImageLargeSrc;} else { echo  $relato->MidiaPrincipal->ImageGaleryThumbSrc;}  ?>"/>											
									</div>
	
									<? if($options["show_meta_data_label"] == "yes") { ?>
									<div class="meta_container place">
										<div class="content_meta">
											<span class="meta_place">
												<span class="meta_icon <?echo $relato->SkeenName;?>">i</span>
												<span class="meta_text">
													<a href="javascript:void(0);"><? echo $relato->Location->GetLocalSubString(30); ?></a>
												</span>
											</span>
											<? if( $relato->Temperatura != null){ ?>
												<span class="meta_temperature">
													<span class="meta_separator <?echo $relato->SkeenName;?>">s</span>
													<span class="meta_text">
														<? echo $relato->Temperatura."° C"; ?>
													</span>
												</span>
											<?}?>
										</div>
									</div>
									<div class="meta_container author">
										<div class="content_meta">
											<span class="meta_author">
												<span class="meta_icon <?echo $relato->SkeenName;?>">i</span>
												<span class="meta_text">
													<a title="Veja o perfil de <? echo $relato->Autor->FullName; ?>" href="<? echo $relato->Autor->ViajanteUrl?>"><? echo $relato->Autor->FullName; ?></a>
												</span>
											</span>
											<span class="meta_date">
												<span class="meta_separator <?echo $relato->SkeenName;?>">s</span>
												<span class="meta_text">
													<time datetime="<? echo $data;?>"><? echo $dataFormated;?></time>
												</span>
											</span>
										</div>
								    </div>
									<?}
									else{?>
									<div class="author_data">
										<div class="autor"><a href='<? echo $relato->Autor->ViajanteUrl?>' ><? echo $relato->Autor->FullName?></a></div>
										<time datetime="<? echo $data;?>"><? echo $dataFormated;?></time>
										<div class="local-relato"><span class="location_ico_small"></span><span><? echo $relato->Location->GetLocalSubString(30); ?></span></div>																		
									</div>
									<?}?>
									
									
									<?	
									if($options['show_share'] == "yes")
									{
										widget::Get("share", array("customUrl" =>  $relato->RelatoUrl,'send'=>'true','show_share_bar' => 'yes','hideCommentBox'=>true));
									}
									?>
									<h3><a href="<? echo $relato->RelatoUrl ?>"><? echo $relato->Titulo; ?></a></h3>
									<h4 style="display:none"><? echo $relato->Location->GetLocalSubString(30); ?></h4>
									<h4 style="display:none"><time datetime="<? echo $data;?>"><? echo $dataFormated;?></time></h4>
									<p><? echo $relato->GetSubContent($options["content_lenght"]); ?></p>
									<a href="<? echo $relato->RelatoUrl ?>" class="ver">Continue lendo -></a>
									<?
									if($relato->Location->Latitude != null && strlen($relato->Location->Latitude) > 1 && $options["show_map"] == "yes")	
									{	
										?>
											<div class="locationInfo">
												<? widget::Get("content-location", array("location" => $relato->Location, "locationImage" => null,'enable_controls'=>'false','image_map'=>'yes'));?>	
											</div>	
										<?	
									}	
									?>
									
									
									<input type='hidden' class='itemId' value="<? echo $relato->RelatoId; ?>"/>
								</li>	
							<?				
						}
					}	
					else
					{
						if( $viajante->FirstName != null)
						{
							if($options['excluded_ids'] != null)
							{
								echo '<span class="no_content">Sem mais relatos publicados até o momento<span>';
							}
							else
							{
								echo '<span class="no_content">Sem relatos publicados até o momento<span>';
							}
						} 
						else
						{
							?><span class="no_content">Sem relatos publicados até o momento<span><?
						}
					}		
				?>			
			
			<? 
			
			if($options["show_more"] == "yes" && $relatoBO->HasMoreData)
			{						
				echo "<li class='loadmore'>";				
					widget::Get("load-more-content", array("content_type"=>"relatos",'show_map'=>$options['show_share'],'show_share'=>$options['show_share'],"itens"=>3, "content_lenght"=>$options["content_lenght"], "userId"=>$options["authorId"], "excluded_ids"=>$options["excluded_ids"],'show_large_image'=>$options["show_large_image"],'show_meta_data_label'=>$options['show_meta_data_label']));
				echo "</li>";
				
			}
			if(!$relatoBO->HasMoreData)
			{	
				echo "<li style='display:none;' class='no_more'></li>";
			}
			
		
			
	
if($options["return"] != "onlyitens")
	{?>
	</ul>
</section>
			
<?}
?>