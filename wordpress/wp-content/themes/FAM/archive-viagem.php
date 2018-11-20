<?php 
require_once(ABSPATH."/FAMCore/BO/Viagem.php");
$viagem = new Viagem(get_current_blog_id());

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("archive-viagem", null, "Viagem",$viagem->DadosViagem->Conteudo);
include('header.php');

?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">				
				<article id="container_viagem">				
					<header class="descricao-viagem">						
						<h1 class="single_type_label viagem_icon topic_label_icon topic_font_color hand_font">A Viagem</h1>
						<div id="texto-descricao"><? echo $viagem->DadosViagem->Conteudo;?></div>
						<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true)); ?>
					</header>													
					<?php widget::Get("viajantes", array("itens" => 20, "show_short_name"=> "no", 'viagemId'=>get_current_blog_id())); ?>
				
					<div class="roteiro-e-mapa">
						<? widget::Get("roteiro_localizacao", array('title'=>'Roteiro de viagem','idViagem'=>get_current_blog_id(),'width'=>'100%',"height"=>'460px','show_last_location'=>'no','link_more_details'=>'no')); ?>					
						<div class="roteiro-lista">	
							<h2 class="trajetos">Trajetos</h2>					
							<ul>
								<?
									if(is_array($viagem->DadosViagem->Roteiro) && count($viagem->DadosViagem->Roteiro) > 0)
									{
										foreach($viagem->DadosViagem->Roteiro as $trajeto)
										{									
											$data = strtotime(str_replace('/', '-', $trajeto->data_de_partida));								
											echo '<li>
											<div class="dataPartida">
												<div class="data">
													'.utf8_encode(strftime("%d", $data)).'
												</div>
												<div class="ano">
													'.utf8_encode(strftime("%b %Y", $data)).'
												</div>
											</div>
											<div class="roteiroTecho">
												<div class="origem">'
												.$trajeto->LocationPartida->GetShortLocation(40).'
												</div>
												<div class="transporte">'
												.$trajeto->transporte.'</div>
												<div class="destino">'
												.$trajeto->LocationChegada->GetShortLocation(40).'
												</div>
											</div>
										</li>';									
										}
									}
									else
									{
										echo "<div class='sem_roteiro'>Trajetos ainda não definidos</div>";
									}	
								?>
							</ul>
						</div>
					</div><!-- end roteiro-e-mapa -->
					<?php widget::Get("share", array("comment_box_width" => '620'));?>
				</article>
				<aside id="coluna-lateral-direita">
					<? widget::Get("add_box_top_right", array('float'=>'right','margin_top'=>"20px",'label'=>'archive_viagem'));  ?>
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'93%','float'=>'right','title'=>'Outras viagens'));?>
					<?php widget::Get("ultimos-relatos", array('itens'=> 2,'content_lenght'=> 120,'width'=>'93%','float'=>'right','excluded_ids'=> array($relato->DadosRelato->RelatoId),'show_more'=> 'yes')); ?>														
					<?php widget::Get("fotos", array('show_more' => 'yes','itens'=>6, 'orderby'=>'rand')) ?>
				</aside>	
				<? widget::Get('footer_adds', array('label'=>'archive-viagem')); ?>			
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<? widget::Get("facebook-box"); ?>	
				<? widget::Get("twitter-box") ?>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo");?>				
			</div>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<?php include('footer-wp.php') ?>