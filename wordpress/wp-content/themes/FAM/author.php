<?php
require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$author = get_user_by( 'slug', get_query_var( 'author_name'));
$id = $author->ID;
$viajante = new Viajante($id);
$viajante = $viajante->DadosViajante;

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
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
									<h1 class="single_viajante_name hand_font"><? echo  $viajante->FullName;?></h1>
									<div class="outros_dados">
										<h4 style="display:none">Nascimento:<span><? echo $viajante->LocalNascimento->Local?></span></h4>
										<h4>Cidade:<span><? echo $viajante->LocalResidencia->Local?></span></h4>
									</div>
									<p><? echo $viajante->UserProfile;?></p>
									<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true,'send'=>true)); ?>
									<? widget::Get("viagens", array('userId'=>$viajante->UserId,'itens'=>7,'show_more'=>'yes'));?>
								</li>									
							</ul>
																
							<? widget::Get("atualizacoes", array('itens'=> 1,'title'=>'Posts de status','foto_width'=>'70px','authorId'=> $viajante->UserId,'content_lenght'=> 145,'show_location'=> true, 'width'=>'100%','show_comment'=> 'no', 'show_media'=> "no",'show_more'=> 'yes','show_read_full'=>'yes'));?>								
							<? widget::Get("ultimos-relatos", array('itens'=> 3,'content_lenght'=> 200,'width'=>'100%','authorId'=> $viajante->UserId,'show_more'=> 'yes')); ?>
							
						</div><!-- end blocoesq -->
						<aside id="coluna-lateral-direita">
							<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"-20px",'label'=>'archive_albuns','width'=>'280px','margin_right'=>'15px'));  ?>	
							<?php widget::Get("a-viagem", array('idViagem'=>get_current_blog_id())); ?>							
							<?php widget::Get("fotos", array('itens'=> 8, 'show_more'=> 'yes', 'orderby'=>'rand')); ?>					
						</aside><!-- end blocodir -->
						<?php widget::Get("share", array("comment_box_width" => '900')); ?>
					</div>
					<div class="clear"></div>
					<? widget::Get('footer_adds', array('label'=>'author')); ?>
				</div><!-- end page -->
				
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">					
					<?php widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id())) ?>
					<? widget::Get("facebook-box"); ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
	
<?php include('footer-wp.php') ?>