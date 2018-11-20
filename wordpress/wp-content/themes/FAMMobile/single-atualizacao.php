<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
require_once(ABSPATH."/FAMCore/BO/Atualizacao.php");
global 	$wp_query;
$id = $wp_query->post->ID;
$atualizacaoBO = new Atualizacao($id);
$atualizacao = $atualizacaoBO->DadosAtualizacao;
Conteudo::SetMetas("single-atualizacao", $atualizacao);

include('header.php');
?>
			
	<div id="content">	
			<div class="label_single_content status">Detalhe de status</div>		
			<article class="single_atualizacao">
								
				<div class="autorInfo">
					<img class="foto_viajante_relato" width='70px' src="<? echo $atualizacao->Autor->UserImage->ImageGaleryThumbSrc ?>"/>												
				</div>
				<header class="author_data">
					<div class="autor">
						<a href='<? echo $atualizacao->Autor->ViajanteUrl?>' ><? echo $atualizacao->Autor->FullName; ?></a>
					</div>
					<?
						$data = strtotime(str_replace('/', '-', $atualizacao->DataPublicacao));
						$dataFormated = utf8_encode(strftime("%d %b %Y %T", $data));
						$data = utf8_encode(strftime("%Y-%m-%d %T", $data));
					?>
					<time datetime="<? echo $data;?>">Em <? echo $dataFormated;?></time>
					<? if($atualizacao->Location->Local != null)
						{							
							?><h3 class="local-relato"><span class="location_ico_small"></span> <span><? echo $atualizacao->Location->GetLocalSubString(25); ?></span></h3> <?	
						}
					?>
				</header>
				<h1 class="single_content_title"><? echo $atualizacao->Titulo; ?></h1>							
				<div class="clear"></div>
				<p><?  echo $atualizacao->Conteudo; ?></p>					
				<? 
													
				if(is_array($atualizacao->MidiasAnexadas) && count($atualizacao->MidiasAnexadas) > 0)	
				{?>
					<div class="medias-status">								
						<ul>
							<?	
							$counter = 0;														
							foreach($atualizacao->MidiasAnexadas as $media)
							{ 										
								if($media->YoutubeCode != null)
								{		
									if($counter == 0)
									{
										$mediaSrc = "<img style='width:100%;'  title='".$media->Titulo."' src='".$media->YoutubeBaseThumbUrl."hqdefault.jpg' alt='".$media->Titulo."' />";
									}
									else
									{																														
										$mediaSrc = "<img style='width:120px;height:70px;' src='".$media->YoutubeBaseThumbUrl."1.jpg' alt='' />";
									}
									$mediaLink =  $media->MainUrl."?modestbranding=1&rel=0";
									$fancyIframe = " fancybox.iframe";									
								}
								else
								{	
									if($counter == 0)
									{
										$mediaSrc = "<img style='width:100%' src='".$media->ImageLargeSrc."' alt='".$media->Titulo."' />";
									}
									else
									{																			
										$mediaSrc = "<img src='".$media->ImageThumbSrc."' alt='' />";
									}
									$mediaLink = $media->ImageLargeSrc;									
								}							
								?>
								<li>
									<div class="post-foto">
										<a class="fancybox <? echo $fancyIframe;?>" data-fancybox-group="status-images-<?$atualizacao->AtualizacaoId;?>" href="<?echo $mediaLink;?>">
											<?echo $mediaSrc;?>
										</a>
									</div>
								</li>
								<?	
								$counter++;																	
							}
							?>
						</ul>
					</div><!-- end medias-status -->
					<?
				}			
				
				if($atualizacao->Location->Latitude != null && strlen($atualizacao->Location->Latitude) > 1)	
				{	
					?>
						<div class="locationInfo">
							<? widget::Get("content-location", array("location" => $atualizacao->Location, "locationImage" => null,'enable_controls'=>'false','image_map'=>'yes'));?>	
						</div>	
					<?	
				}		
				widget::Get("share",array('hideCommentBox'=>false,'send'=>true));?>	
			</article>
			<?widget::Get("atualizacoes", array('title'=>'Outros status recentes','itens'=> 5,'show_read_full'=>'yes','excluded_ids'=>$atualizacao->AtualizacaoId,'show_map'=>'yes','width'=>'100%','foto_width'=>'70px', 'show_more'=> 'yes','show_location'=> true,'default_archive'=>$archive_posts));?>
							
		</div><!-- end atualizacoes -->					
		<div class="clear"></div>	
	</div><!-- end content -->

<?php include('footer/footer-default.php') ?>

