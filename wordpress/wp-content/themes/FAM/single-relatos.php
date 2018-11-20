<?php
require_once(ABSPATH."/FAMCore/BO/Relato.php");
$id = $wp_query->post->ID;
$relato = new  Relato($id);

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("single-relatos",  $relato->DadosRelato);

get_header();?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">				
					<div class="detalheRelato single_type_label relato_icon topic_label_icon topic_font_color hand_font">Relato de viagem</div>
					<article class="relato-details">
						<header class="relato-details-header">							
							<div class="foto-conteiner largeImage">
								<a class="fancybox" title=" Imagem do relato <? echo $relato->DadosRelato->Titulo?>" href="<? echo $relato->DadosRelato->MidiaPrincipal->ImageLargeSrc ?>">
									<img class="foto_viajante_relato" style="width:100%" src="<? echo $relato->DadosRelato->MidiaPrincipal->ImageLargeSrc ?>"/>
								</a>
							</div>						
							<? 
							$data = ($relato->DadosRelato->DataRelato != null)? $relato->DadosRelato->DataRelato: $relato->DadosRelato->DataPublicacao;
							$data = strtotime(str_replace('/', '-', $data));
							$dataFormatted = utf8_encode(strftime("%d %b %Y", $data));
							$data = utf8_encode(strftime("%Y-%m-%d", $data));
							
							?>						
							<div class="meta_container place">
								<div class="content_meta">
									<span class="meta_place">
										<span class="meta_icon">i</span>
										<span class="meta_text">
											<a href="javascript:void(0);"><? echo $relato->DadosRelato->Location->Local; ?></a>
										</span>
									</span>
									<? if( $relato->DadosRelato->Temperatura != null){ ?>
										<span class="meta_temperature">
											<span class="meta_separator">s</span>
											<span class="meta_text">
												<? echo $relato->DadosRelato->Temperatura."° C"; ?>
											</span>
										</span>
									<?}?>
								</div>
							</div>
							<div class="meta_container author">
								<div class="content_meta">
									<span class="meta_author">
										<span class="meta_icon">i</span>
										<span class="meta_text">
											<a title="Veja o perfil de <? echo $relato->DadosRelato->Autor->FullName; ?>" href="<? echo $relato->DadosRelato->Autor->ViajanteUrl?>"><? echo $relato->DadosRelato->Autor->FullName; ?></a>
										</span>
									</span>
									<span class="meta_date">
										<span class="meta_separator">s</span>
										<span class="meta_text">
											<time datetime="<? echo $data;?>"><? echo $dataFormatted;?></time>
										</span>
									</span>
								</div>
						   </div>
						</header>
						<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true,'send'=>'true')); ?>	
						<div class="relato_info relato_single_title">
							<h1><span class="titulo"><? echo $relato->DadosRelato->Titulo; ?></span></h1>
						</div>
						<div id="locationInfo_relato">
							<? widget::Get("content-location", array("location" => $relato->DadosRelato->Location, "locationImage" =>  $relato->DadosRelato->Autor->UserImage->ImageThumbSrc)); ?>	
						</div>	
						<div class="html_content">												
							<? echo $relato->DadosRelato->Conteudo; ?>	
							<a class="comunicate_erro" href="javascript:void(0).">Encontrou erros nesse post? Comunique!</a>
						</div>					
					<?
					echo '<div class="clear"></div>';
					widget::Get("add_responsive", array('float'=>'left','margin_top'=>"10px"));
					
					widget::Get("posts_relacionados", array("post_id" => $relato->DadosRelato->RelatoId));
					widget::Get("share", array("comment_box_width" => '580','send'=>'true'));
					
					?>
						
				</article>
				<aside id="coluna-lateral-direita">							
					<? widget::Get("add_box_top_right", array('float'=>'right','margin_right'=>'-20px','margin_top'=>"-30px",'label'=>'single_relato','place'=>$relato->DadosRelato->Location->GetLocalSubString(25)));  ?>
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'current_viagemId'=>get_current_blog_id(),'show_more'=>'yes','width'=>'93%','float'=>'right','title'=>'Outras viagens'));?>
					<?php widget::Get("blog_posts", array('itens'=> 3,'show_share'=>'no','content_lenght'=> 100,'width'=>'95%','show_more'=> 'yes','width'=>'95%','float'=>'right','margin_right'=>'-5px')); ?>																	
					<? widget::Get("aside_middle_box", array('margin_left'=>'20px'));?>
					<?php //widget::Get("ultimos-relatos", array('itens'=> 2,'content_lenght'=> 120,'width'=>'93%','float'=>'right','excluded_ids'=> $relato->DadosRelato->RelatoId,'show_more'=> 'yes','orderby'=>'rand')); ?>					
					<?php widget::Get("galeria", array('title'=>'Álbuns','show_more' => 'yes','itens'=> 4,'float'=>'right','width'=>'94%')) ?>
					<div style="margin-top:20px; float:left;">
						<?php //widget::Get("fotos", array('itens'=> 8,'show_more' => 'yes', 'orderby'=>'rand')); ?>							
					</div>
						
				</aside>	
				<div class="outros-relatos">											
					<?php //widget::Get("ultimos-relatos", array('itens'=> 3,'content_lenght'=>300,'width'=>'100%','authorId'=> $relato->DadosRelato->Autor->UserId,'excluded_ids'=> $relato->DadosRelato->RelatoId,'show_more'=> 'no')); ?>
				</div>					
							
				<div class="clear"></div>
					
				<? widget::Get('footer_adds', array('label'=>'single-relatos')); ?>	
					
			</div><!-- end bloco-conteudo-central -->
				
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<?php global $viagemId; widget::Get("roteiro_localizacao", array('idViagem'=>$viagemId)) ?>
					<? widget::Get("facebook-box"); ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>

