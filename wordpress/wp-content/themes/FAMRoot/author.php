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
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">	
					<div class="viajante">				
						<div class="search single_type_label viajante_icon topic_label_icon topic_font_color hand_font">Viajante</div>												
						<!-- aqui vem o conteudo do viajante -->
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
										<h4>Residência: <span><? echo $viajante->LocalResidencia->Local?></span></h4>
									</div>
									<p><? echo $viajante->UserProfile;?></p>									
									<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true,'send'=>true)); ?>
									<?										
										if($viajante->UserRole != 'usuario_forum')
										{
											widget::Get("viagens", array('userId'=>$viajante->UserId,'itens'=>7,'show_more'=>'yes'));											
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
							
							<? widget::Get("forum_topics", array('itens'=> 3,'show_more'=> 'yes','title'=>'Tópicos no fórum','width'=>'615px','show_more'=> 'yes','margin_right'=>'12px','authorId'=>$viajante->UserId)); ?>						
							
							
						</div><!-- end blocoesq -->
						<aside id="coluna-lateral-direita">
							<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"-20px",'label'=>'archive_albuns','width'=>'280px','margin_right'=>'-5px'));  ?>	
							<? widget::Get("blog_posts", array('itens'=> 3,'content_lenght'=> 200,'width'=>'95%','show_more'=> 'yes')); ?>													
							<? widget::Get("galeria", array('show_more' => 'yes','itens'=> 2, 'title' => 'Albuns de viagens')) ?>								
											
						</aside><!-- end blocodir -->
						<? widget::Get("share", array("comment_box_width" => '900')); ?>
						<? widget::Get('footer_adds', array('label'=>'author')); ?>	
					</div>
					<div class="clear"></div>
					
				</div><!-- end page -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">					
					<? widget::Get("twitter-box") ?>
					<? widget::Get("facebook-box") ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
	
<?php include('footer-wp.php') ?>