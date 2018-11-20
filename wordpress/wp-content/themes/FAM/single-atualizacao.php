<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
require_once(ABSPATH."/FAMCore/BO/Atualizacao.php");
global 	$wp_query;
$id = $wp_query->post->ID;
$atualizacaoBO = new Atualizacao($id);
$atualizacao = $atualizacaoBO->DadosAtualizacao;
Conteudo::SetMetas("single-atualizacao", $atualizacao );


include('header.php');
?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">				
				<article class="atualizacao">
					<div class="label_single_content status">Detalhe de status</div>
					<div class="single_atualizacao">
						<header class="autorInfo">
							<img class="foto_viajante_relato" width='70px' src="<? echo $atualizacao->Autor->UserImage->ImageGaleryThumbSrc ?>"/>
							<div class="autor_atualizacao">
								<div class="autor"><a href='<? echo $atualizacao->Autor->ViajanteUrl?>' ><? echo $atualizacao->Autor->FullName; ?></a></div>
									<?
										$data = strtotime(str_replace('/', '-', $atualizacao->DataPublicacao));
										$data = utf8_encode(strftime("%d %b %Y %T", $data));
									?>
									<time datetime="<? echo $atualizacao->DataPublicacao;?>">Em <? echo $data;?></time>
									<? if($atualizacao->Location->Local != null)
										{							
											?><h3 class="local-relato"><span class="location_ico_small"></span> <span><? echo $atualizacao->Location->GetLocalSubString(25); ?></span></h3> <?	
										}
									?>
							</div>								
						</header>	
						<h1 class="single_content_title"><? echo $atualizacao->Titulo; ?></h1>						
						<div class="clear"></div>
											
						<p><?  echo $atualizacao->Conteudo; ?></p><?								
												
						if(is_array($atualizacao->MidiasAnexadas) && count($atualizacao->MidiasAnexadas) > 0)	
						{?>
							<div class="medias-status">								
								<ul>
									<?	
									$counter = 0;														
									foreach($atualizacao->MidiasAnexadas as $media)
									{ 										
										if($media->YoutubeCode != null)
										{	if($counter == 0)
											{
												$mediaSrc = "<img style='width:100%;'  title='".$media->Titulo."' src='".$media->YoutubeBaseThumbUrl."hqdefault.jpg' alt='".$media->Titulo."' />";
											}	
											else
											{									
												$mediaSrc = "<img style='width:190px;height:140px;' title='".$media->Titulo."' src='".$media->YoutubeBaseThumbUrl."hqdefault.jpg' alt='' />";
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
												$mediaSrc = "<img src='".$media->ImageGaleryThumbSrc."' alt='".$media->Titulo."'  title='".$media->Titulo."' />";
												
											}	
											$mediaLink = $media->ImageLargeSrc;										
										}															
							
										?>
											<li>
												<div class="post-foto">
													<a class="fancybox<? echo $fancyIframe;?>" data-fancybox-group="status-images-<?$atualizacao->AtualizacaoId;?>" href="<?echo $mediaLink;?>">
														<?echo $mediaSrc;?>
													</a>
												</div>
											</li>
										<?
										$counter++;																	
									}
									?>
								</ul>
							</div><!-- end medias-relato -->
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
						widget::Get("share", array("customUrl" => $atualizacao->AtualizacaoUrl , 'show_share_bar' => 'yes', "comment_box_width" => '600')); ?>
					
					</div>
					<? widget::Get("atualizacoes", array('title'=>'Outros status recentes','itens'=> 10,'excluded_ids'=>$atualizacao->AtualizacaoId,'show_map'=>'yes','width'=>'630px','foto_width'=>'70px', 'show_more'=> 'yes','show_location'=> true,'default_archive'=>$archive_posts));?>					
								
				</article><!-- end atualizacoes -->
				<aside id="coluna-lateral-direita">
					<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"20px",'label'=>'archive_relatos','width'=>'280px','margin_right'=>'-5px'));  ?>	
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'current_viagemId'=>get_current_blog_id(),'show_more'=>'yes','width'=>'93%','float'=>'right','title'=>'Outras viagens'));?>
					<?php widget::Get("ultimos-relatos", array('itens'=> 2,'content_lenght'=> 120,'width'=>'93%','float'=>'right','excluded_ids'=> array($relato->DadosRelato->RelatoId),'show_more'=> 'yes')); ?>										
					<?php widget::Get("fotos", array('show_more' => 'yes','itens'=>6, 'orderby'=>'rand')) ?>
				</aside>	
				<? widget::Get('footer_adds', array('label'=>'single-atualizacao')); ?>				
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<?php widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id()));?>
				<?php widget::Get("facebook-box");?>				
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo");?>				
			</div>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<?php include('footer-wp.php') ?>

