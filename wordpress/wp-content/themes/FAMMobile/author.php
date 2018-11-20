<?php
require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$author = get_user_by( 'slug', get_query_var( 'author_name' ));

if($author->ID == NULL)
{
	global $viajante_id;
	$author->ID = $viajante_id;
}
$viajante = new Viajante($author->ID);
$viajante = $viajante->DadosViajante;
$contentImageUrl = $viajante->UserImage->ImageFullSrc;
Conteudo::SetMetas("author",$viajante);

include('header.php') ?>			
			<div id="content">					
					<div class="search single_type_label viajante_icon topic_label_icon topic_font_color hand_font">Viajante</div>							
					<div class="viajante_details">
						<ul>								
							<li>
								<div class="foto">
									<a class="fancybox" title="Viajante <? echo $viajante->FirstName.' '.$viajante->LastName; ?>" href="<? echo $viajante->UserImage->ImageLargeSrc ?>">
										<img class="foto_viajante_relato" src="<? echo $viajante->UserImage->ImageGaleryThumbSrc ?>"/>
									</a>												
								</div><!-- end foto -->
								<h1 class="single_viajante_name hand_font"><? echo $viajante->FullName;?></h1>
								<div class="outros_dados">
									<h4 style="display:none">Nascimento:<span><? echo $viajante->LocalNascimento->Local?></span></h4>
									<h4>Residência:<span><? echo $viajante->LocalResidencia->Local?></span></h4>
								</div>
								<p><? echo $viajante->UserProfile;?></p>
								<?										
									if($viajante->UserRole != 'usuario_forum')
									{										
										widget::Get("viagens", array('list_type'=>'box','userId'=>$viajante->UserId,'itens'=>7,'show_more'=>'yes','width'=>'100%')); 
									}
								?>
							</li>									
						</ul>
						<?
						if($viajante->UserRole != 'usuario_forum')
						{																	
							widget::Get("atualizacoes", array('itens'=> 2,'show_read_full'=>'yes','title'=>'Posts de status','authorId'=>$viajante->UserId,'content_lenght'=> 95,'show_more'=> 'yes', 'show_location'=> true, 'float'=>'left','margin_right'=>'10px', 'width'=>'95%','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px'));										
							widget::Get("ultimos-relatos", array('itens'=> 3,'content_lenght'=> 200,'width'=>'100%','authorId'=>$viajante->UserId,'show_more'=> 'yes'));									
						}
						?>							
					</div><!-- end blocoesq -->				
				<div class="clear"></div>				
			</div><!-- end content -->
<?php include('footer/footer-default.php') ?>