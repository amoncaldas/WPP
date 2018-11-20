<?php
require_once(ABSPATH."/FAMCore/BO/Viagem.php");	
$viagemBO = new Viagem();
$options['itens'] = (isset($_GET["itens"]) && is_numeric($_GET["itens"]) && $_GET["itens"] > 0) ? $_GET["itens"] : $options['itens'];
$viagens = $viagemBO->GetData($options);
$counter = 1;
if($_GET["viagens_modal"] == "yes") {$options["list_type"] = "default";}


if($options["list_type"] == "box")
{ 
	if($options["return"] != 'onlyitens')
	{
		?>
			<section id="lista_viagens" style="float:<?echo $options["float"].' !important';?>;  margin-left:<?echo $options["margin_left"].' !important'; ?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;" >	
			<? 				
				if($options["title"] != null)
				{
					echo "<h2>".$options["title"]."</h2>";
				}
				elseif( $options["current_viagemId"] != null)
				{
					echo "<h2>Outras viagens</h2>";
				}
				else
				{
					echo "<h2>Viagens</h2>";
				}
			?>
			<ul id="itens_viagens">
				<? }
					if(is_array($viagens) && count($viagens)> 0)
					{	
						foreach($viagens as $viagem) 
						{ 										
							if($viagem->ExibirViagem)
							{
								//$intermediateImage  = $viagem->MidiaPrincipal->OriginalImageVO->GetIntermediateImageSrc();									
								//$mainImage = ($intermediateImage == null)? $viagem->MidiaPrincipal->OriginalImageVO->ImageLargeSrc : $intermediateImage;
								
								?>		
								<li class="panel<?echo $counter;?>">																
									<div class="box_container">	
										<a title="Veja a viagem <?  echo $viagem->Titulo?>" href="<?  echo $viagem->ViagemUrl?>" >	
											<div class="viagem_image">																																
												<img style="width:100%" src="<?echo ($options["viagem_medium_image"] != "yes" && !is_fam_mobile())? $viagem->MidiaPrincipal->OriginalImageVO->ImageMediumSrc : $viagem->MidiaPrincipal->OriginalImageVO->ImageLargeSrc; ?>" alt="<?echo "Veja a viagem ".$viagem->Titulo;?>" title="<?echo $viagem->Titulo;?>"/>																							
											</div>	
															
											<div class="meta_container qtd_viajantes">
												<div class="content_meta">
													<span class="meta_qtd_viajantes">											
														<span class="meta_text">
															<?echo $viagem->QtdViajantes;?>
														</span>
														<span class="meta_icon <?echo $viagem->SkeenName;?>">i</span>
													</span>										
												</div>
											</div>
											<div class="meta_container qtd_dias">
												<div class="content_meta">
													<span class="meta_dias">
														<span class="meta_text">
															<?echo $viagem->DiasDeViagem;?> dias
														</span>
														<span class="meta_icon <?echo $viagem->SkeenName;?>">i</span>
													</span>										
												</div>
											</div>
						
											<div class="box_viagem_bottom">	
												<div class="meta_container titulo_viagem">
													<div class="content_meta">
														<span class="meta_viagem">
															<span class="meta_icon <?echo $viagem->SkeenName;?>">i</span>
															<span class="meta_text">																
																<h3><?echo $viagem->Titulo;?></h3>
																<? widget::Get('bandeiras', array('locations'=>$viagem->Roteiro, 'width'=> 15,'limit_itens' => 10));?>
																	
																<span class="lista_locais">
																	<? if($viagem->NumPaises < 2)
																	{ 
																		if(strlen($viagem->ListaLocais) > 72)
																		{
																			$viagem->ListaLocais = substr($viagem->ListaLocais,0,72)."...";
																		}
																		?>
																		<span><? echo $viagem->ListaLocais; ?></span>
																	<?}?> 
																</span>																
															</span>
														</span>										
													</div>
												</div>									
											</div>
										</a>
									</div>
									<input type='hidden' class='itemId' value="<? echo $viagem->ViagemId; ?>"/>											
								</li>												
								<?
								$counter++;
							}				
						}
			
						if($options["show_more"] == "yes" && $viagemBO->HasMoreData)
						{							
							echo "<li class='loadmore'>";	
							widget::Get("load-more-content", array("content_type"=>"viagens","itens"=>2,'list_type'=>'box','excluded_ids'=> $options["excluded_ids"],'userId'=> $options["userId"], 'show_more'=>$options["show_more"]));
							echo "</li>";			
						}				
						if(!$viagemBO->HasMoreData && $options["show_more"] == "yes")
						{	
							echo "<li style='display:none;' class='no_more'></li>";
						}
					}
					else
					{			
						if($options["return"] != 'onlyitens')
						{
							?><span class="no_content">Sem viagens até o momento<span><?
								
								?>
								</ul>
							</div>
							<?
						}
					}
																		
			if($options["return"] != 'onlyitens')
			{
			?>
			</ul>
		</section>
		<?
	}		
}
else
{	
	if($options["return"] != 'onlyitens')
	{
	?>
	<div id="lista_viagens" style="float:<?echo $options["float"].' !important';?>;  margin-left:<?echo $options["margin_left"].' !important'; ?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;" >	
		<? 
		if($options["title"] != null)
		{
			echo "<h2>".$options["title"]."</h2>";
		}
		elseif( $options["current_viagemId"] != null)
		{
			echo "<h2>Outras viagens</h2>";
		}
		else
		{
			echo "<h2>Viagens</h2>";
		}
		?>
	
		<ul id="itens_viagens">
			<? }
			if(is_array($viagens) && count($viagens)> 0)
			{			
				foreach($viagens as $viagem) { 
					//$intermediateImage  = $viagem->MidiaPrincipal->OriginalImageVO->GetIntermediateImageSrc();									
					//$mainImage = ($intermediateImage == null)? $viagem->MidiaPrincipal->OriginalImageVO->ImageLargeSrc : $intermediateImage;
						
					$imagem_src = ( ($options["viagem_medium_image"] == "yes" && $_GET["viagens_modal"] != 'yes') || is_fam_mobile())? $viagem->MidiaPrincipal->OriginalImageVO->ImageLargeSrc : $viagem->MidiaPrincipal->OriginalImageVO->ImageThumbSrc;
					$imagem_height = ( ($options["viagem_medium_image"] == "yes" && $_GET["viagens_modal"] != 'yes') || is_fam_mobile())? $viagem->MidiaPrincipal->OriginalImageVO->ImageMediumHeight: $viagem->MidiaPrincipal->OriginalImageVO->ImageThumbHeight;
					if($viagem->ExibirViagem)
					{
											
						?>		
							<li class="panel<?echo $counter;?>">
								<div class="imagem_principal">
									<a style="max-height:<? echo $imagem_height ?>px" href="<? echo (is_fam_mobile())? $viagem->ViagemUrl: $viagem->MidiaPrincipal->OriginalImageVO->ImageLargeSrc;?>"><img alt="<?echo $viagem->Titulo;?>" src="<? echo $imagem_src;?>"/></a>
								</div>
								<div class="viagem_info">
									<div class="tituloViagem"><h3><a class="urlsite" href="<?  echo $viagem->ViagemUrl?>" ><?echo $viagem->Titulo;?></a></h3></div>
									<div class="dados_viagem"><span><?echo $viagem->DiasDeViagem;?></span> dias de viagem e <span><?echo $viagem->QtdViajantes;?></span> viajante(s)</div>			
									<? widget::Get('bandeiras', array('locations'=>$viagem->Roteiro, 'width'=> 30,'limit_itens' => 10));?>
									<div class="lista_locais">
										<? if($viagem->NumPaises < 2)
										{ 
											if(strlen($viagem->ListaLocais) > 65)
											{
												$viagem->ListaLocais = substr($viagem->ListaLocais,0,65)."...";
											}
											?>
											<span><? echo $viagem->ListaLocais; ?></span>
										<?}?> 
									</div>
							
								</div>	
								<? if ($options["viagem_show_map"] == "yes" && $_GET["viagens_modal"] != 'yes')
								{
									?>
										<div class="mapa_viagem_lista_viagem">
											<? widget::Get("roteiro_localizacao", array('idViagem'=>$viagem->ViagemId,'link_more_details'=>'no', 'show_map_controls'=>'no','width'=>'250px', 'height'=>'100px','show_last_location'=>'no', 'show_title'=>'no')) ?>
										</div>
									<?
								}
								?>		
								<div class="clear"></div>
								<input type='hidden' class='itemId' value="<? echo $viagem->ViagemId; ?>"/>			
							</li>												
						<? 
						$counter++;
					}
				}				
			}
			else if($options["return"] != 'onlyitens')
			{
				?><span class="no_content">Sem viagens com este(s) termo(s)<span><?				
			}
			
			if($options["show_more"] == "yes" && $viagemBO->HasMoreData)
			{							
				echo "<li class='loadmore'>";					
				widget::Get("load-more-content", array("content_type"=>"viagens","itens"=>2, 'viagem_medium_image'=>$options["viagem_medium_image"],'viagem_show_map'=>$options["viagem_show_map"],'excluded_ids'=> $options["excluded_ids"],'userId'=> $options["userId"]));
				echo "</li>";			
			}				
			if(!$viagemBO->HasMoreData && $options["show_more"] == "yes")
			{	
				echo "<li style='display:none;' class='no_more'></li>";
			}
			
			if($options["return"] != 'onlyitens')
			{
			?>														
		</ul>
		<?  if($options["show_view_all"] == "yes"){echo "<a class='todas_viagens' href='/viagens'>Ver todas</a>";}?>
	</div>	
	<?
	}
}


