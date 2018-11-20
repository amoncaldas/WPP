<?php
require_once(ABSPATH."/FAMCore/BO/Relato.php");
$id = $wp_query->post->ID;
$relato = new  Relato($id);
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("single-relatos", $relato->DadosRelato);

include('header.php') ?>			
	<div id="content">							
		<div class="escrita detalheRelato single_type_label relato_icon topic_label_icon topic_font_color hand_font">Relato de viagem</div>
		<article class="relato-details">
			<header class="relato-details-header">				
				<div class="foto-conteiner largeImage">
					<a class="fancybox" title="Viajante <? echo "Imagem do relato".$relato->DadosRelato->Titulo; ?>" href="<? echo $relato->DadosRelato->MidiaPrincipal->ImageLargeSrc ?>">
						<img style="width:100%" class="foto_viajante_relato" src="<? echo $relato->DadosRelato->MidiaPrincipal->ImageLargeSrc ?>"/>
					</a>
				</div>
				
				<? 
				$data = ($relato->DadosRelato->DataRelato != null)? $relato->DadosRelato->DataRelato: $relato->DadosRelato->DataPublicacao;
				$data = strtotime(str_replace('/', '-', $data));
				$dataFormated = utf8_encode(strftime("%d %b %Y", $data));
				$data = utf8_encode(strftime("%Y-%m-%d %T", $data));
				?>						
				<div class="meta_container place">
					<div class="content_meta">
						<span class="meta_place">
							<span class="meta_icon">i</span>
							<span class="meta_text">
								<a href="javascript:void(0);"><? echo $relato->DadosRelato->Location->GetLocalSubString(30); ?></a>
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
								<time datetime="<? echo $data;?>"><? echo $dataFormated;?></time>
							</span>
						</span>
					</div>
				</div>
			</header>		
			<div class="locationInfo">
				<? widget::Get("content-location", array("location" => $relato->DadosRelato->Location, "locationImage" => null,'enable_controls'=>'false','image_map'=>'yes'));?>	
			</div>
			
			<? widget::Get("share", array('hideShareBtns' => false,'hideCommentBox'=>true,'send'=>true)); ?>	
			<div class="relato_info">
				<h1><span class="titulo"><? echo $relato->DadosRelato->Titulo; ?></span></h1>					
			</div>
			<div class="html_content">													
				<? echo $relato->DadosRelato->Conteudo; ?>
			</div>	
								
			<? widget::Get("posts_relacionados", array("post_id" => $relato->DadosRelato->RelatoId)); ?>
			<?	widget::Get("share",array('hideCommentBox'=>false,'send'=>true)); ?>
								
		</article>								
		<div class="clear"></div>			
	</div><!-- end content -->	
<?php include('footer/footer-default.php') ?>

