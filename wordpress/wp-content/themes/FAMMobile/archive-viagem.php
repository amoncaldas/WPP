<?php 
require_once(ABSPATH."/FAMCore/BO/Viagem.php");
require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$viagem = new Viagem(get_current_blog_id());
$viajante = new Viajante();
$viajantes = $viajante->GetViajantesDeViagem(get_current_blog_id());

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("archive-viagem", null, "Viagem",$viagem->DadosViagem->Conteudo);
get_header();
?>				
	<div id="content">
		<article>
			<header class="descricao-viagem">
				<h1 class="single_type_label viagem_icon topic_label_icon topic_font_color hand_font">A Viagem <? bloginfo('name') ?></h1>
				<div id="texto-descricao"><? echo $viagem->DadosViagem->Conteudo;?></div>
				<? widget::Get("share", array('hideShareBtns' => false,'hideCommentBox'=>true)); ?>
			</header>		
			<div class="viajantes_viagem">										
				<?php widget::Get("viajantes", array("itens" => 20, "show_short_name"=> "no", 'viagemId'=>get_current_blog_id())); ?>				
			</div>
			<div class="roteiro-e-mapa">
				<? widget::Get("roteiro_localizacao", array('title'=>'Roteiro de viagem','idViagem'=>get_current_blog_id(),'width'=>'100%',"height"=>'325px','show_last_location'=>'no','link_more_details'=>'no')); ?>					
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
										.$trajeto->LocationPartida->GetShortLocation(15).'
										</div>
										<div class="transporte">'
										.$trajeto->transporte.'</div>
										<div class="destino">'
										.$trajeto->LocationChegada->GetShortLocation(15).'
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
			</div>
		</article>									
	</div><!-- end content -->
<?php include('footer/footer-default.php') ?>
