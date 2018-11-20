<?php
define('ABSPATH', $_SERVER["DOCUMENT_ROOT"]."/");
require_once(ABSPATH."wp-load.php");
?>
<html>
	<head>
		<script type="text/javascript">
			if ( window.self === window.top ) { location.href = 'http://' + location.host;} 
		</script>
		<style>
			#addRoteiro
			{
			font-family:sans-serif;
			font-size: 12px;
			font-weight: normal;
			padding: 7px 10px;
			margin: 0;
			line-height: 1;
			}
			#wpadminbar
			{
			display:none;
			}
			#screen-options-link-wrap
			{
			display:none;
			}
			#footer
			{
			display:none;
			}

			.pods-field, p.pods-add-file {
			padding: 0px !important;
			}

			#addRoteiro
			{
			padding-right:20px;
			}

			.pods-submit
			{
			display:none;
			}

			.pods-form-fields li
			{
			margin-bottom:10px !important;
			list-style:none;
			}

			.pods-field-input
			{
			width:100% !important;
			}

			#saveTrajeto
			{
			border: 3px solid #DFDFDF;
			padding: 10px;
			background: #21759B;
			color: white;
			font-size: 18px;
			text-decoration: none;
			float: left;
			margin-left: 40px;
			}

			#pods-form-ui-comment {
			display: none;
			}
			.pods-field, p.pods-add-file {
			padding: 0px;
			}
			.pods-submittable-fields input
			{
			width:100%;
			}
			#footer-thankyou, #footer-upgrade, #footer-left{
			display:none;
			}
		</style>		
	</head>
	<body>					
		<div id='addRoteiro'>
			<? 
			$vid = $_GET["vid"];
			if($vid != null && is_numeric($vid) && $vid > 0)
			{
				switch_to_blog($vid);
			}	
			
			
			if(is_user_logged_in() /*&& current_user_can('edit_viagem')*/)
			{
				restore_current_blog();
				?>
				<script type='text/javascript' src='/wp-includes/js/jquery/jquery.js?ver=1.7.2'></script>			
				<form action="/passeiopelooriente/wp-content/themes/FAM/admin/trajeto.php?_p_submitted=1" method="post" class="pods-submittable pods-form pods-form-front pods-form-pod-trajeto pods-submittable-ajax" data-location="/passeiopelooriente/wp-content/themes/FAM/admin/trajeto.php?success=1">
					<div class="pods-submittable-fields">			
						<ul class="pods-form-fields">
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-local-de-partida">
								<div class="pods-field-label">
								<label class="localPartida" for="pods-form-ui-pods-field-local-de-partida">Local de partida</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_local_de_partida" data-name-clean="pods-field-local-de-partida" id="pods-form-ui-pods-field-local-de-partida" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-local-de-partida localPartida" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-data-de-partida">
								<div class="pods-field-label">
								<label class="dataPartida fam_date_picker" for="pods-form-ui-pods-field-data-de-partida">Data de partida</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_data_de_partida" data-name-clean="pods-field-data-de-partida" id="pods-form-ui-pods-field-data-de-partida" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-data-de-partida dataPartida fam_date_picker" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-local-de-chegada">
								<div class="pods-field-label">
								<label class="LocalChegada" for="pods-form-ui-pods-field-local-de-chegada">Local de chegada</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_local_de_chegada" data-name-clean="pods-field-local-de-chegada" id="pods-form-ui-pods-field-local-de-chegada" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-local-de-chegada LocalChegada" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-data-de-chegada">
								<div class="pods-field-label">
								<label class="fam_date_picker" for="pods-form-ui-pods-field-data-de-chegada">Data de chegada</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_data_de_chegada" data-name-clean="pods-field-data-de-chegada" id="pods-form-ui-pods-field-data-de-chegada" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-data-de-chegada dataChegada fam_date_picker" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-pick pods-form-ui-row-name-transporte">
								<div class="pods-field-label">
								<label class="pods-form-ui-label pods-form-ui-label-pods-field-transporte" for="pods-form-ui-pods-field-transporte">Transporte</label>
								</div>
								<div class="pods-field-input">
									<div class="pods-select2">
										<select name="pods_field_transporte" data-name-clean="pods-field-transporte" id="pods-form-ui-pods-field-transporte" class="pods-form-ui-field-type-pick pods-form-ui-field-name-pods-field-transporte pods-form-ui-field-type-select2" data-field-type="select2" tabindex="2" >
											<option  value="Avião comercial">Avião comercial</option>
											<option  value="Avião particular">Avião particular</option>
											<option  value="Táxi">Táxi</option>
											<option  value="Planador">Planador</option>
											<option  value="Ultraleve">Ultraleve</option>
											<option  value="Asa-delta">Asa-delta</option>
											<option  value="Balão">Balão</option>
											<option  value="Parapente">Parapente</option>
											<option  value="Outro meio aéreo">Outro meio aéreo</option>
											<option  value="Carro">Carro</option>
											<option  value="Avião comercial">Moto</option>
											<option  value="Moto">Ônibus</option>
											<option  value="Caminhão">Caminhão</option>
											<option  value="Carona">Carona</option>
											<option  value="Bicicleta">Bicicleta</option>
											<option  value="A pé">A pé</option>
											<option  value="Metrô">Metrô</option>
											<option  value="Trem">Trem</option>
											<option  value="Veleiro">Veleiro</option>
											<option  value="Ferry-boat">Ferry-boat</option>
											<option  value="Catamarã">Catamarã</option>
											<option  value="Balsa">Balsa</option>
											<option  value="Lancha">Lancha</option>
											<option  value="Escuna">Escuna</option>
											<option  value="Canoa">Canoa</option>
											<option  value="Jet-sky">Jet-sky</option>										
											<option  value="Caiaque">Caiaque</option>
											<option  value="Outra embarcação">Outra embarcação</option>
											<option  value="Natação">Natação</option>
											<option  value="Outro transporte">Outro transporte</option>
										</select>
									</div>
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-latitude-de-partida">
								<div class="pods-field-label">
									<label class="latitudePartida" for="pods-form-ui-pods-field-latitude-de-partida">Latitude de partida</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_latitude_de_partida" data-name-clean="pods-field-latitude-de-partida" id="pods-form-ui-pods-field-latitude-de-partida" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-latitude-de-partida latitudePartida" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-longitude-de-partida">
								<div class="pods-field-label">
								<label class="longitudePartida" for="pods-form-ui-pods-field-longitude-de-partida">Longitude de partida</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_longitude_de_partida" data-name-clean="pods-field-longitude-de-partida" id="pods-form-ui-pods-field-longitude-de-partida" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-longitude-de-partida longitudePartida" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-latitude-de-chegada">
								<div class="pods-field-label">
								<label class="latitudeChegada" for="pods-form-ui-pods-field-latitude-de-chegada">Latitude de chegada</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_latitude_de_chegada" data-name-clean="pods-field-latitude-de-chegada" id="pods-form-ui-pods-field-latitude-de-chegada" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-latitude-de-chegada latitudeChegada" type="text" tabindex="2" />
								</div>
							</li>
							<li class="pods-field pods-form-ui-row-type-text pods-form-ui-row-name-longitude-de-chegada">
								<div class="pods-field-label">
								<label class="longitudeChegada" for="pods-form-ui-pods-field-longitude-de-chegada">Longitude de chegada</label>
								</div>
								<div class="pods-field-input">
								<input name="pods_field_longitude_de_chegada" data-name-clean="pods-field-longitude-de-chegada" id="pods-form-ui-pods-field-longitude-de-chegada" class="pods-form-ui-field-type-text pods-form-ui-field-name-pods-field-longitude-de-chegada longitudeChegada" type="text" tabindex="2" />
								</div>
							</li>
						</ul>
						<p class="pods-submit">
						<img class="waiting" src="http://fazendoasmalas.com/passeiopelooriente/wp-admin/images/wpspin_light.gif" alt="">
						<input type="submit" value=" Save Changes " class="pods-submit-button" />
						</p>
					</div>
				</form>
				<a id='saveTrajeto' href='javascript:void()'>Salvar trajeto</a>
				<? require_once($_SERVER['DOCUMENT_ROOT'].'/wp-admin/admin-footer.php');?>
				<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
				<script type='text/javascript' src='http://maps.googleapis.com/maps/api/js?sensor=false&#038;libraries=places%2Cweather&#038;language=pt-BR&#038;ver=3.0'></script>
				<script type="text/javascript" src="/wp-content/themes/FAM/CustomContent/js/trajeto.js"></script>
				<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
				<script type="text/javascript" src="/wp-content/themes/js/jquery.datepick-pt-BR.js"></script>
				<?
			}
			else
			{
				if($vid != null && is_numeric($vid) && $vid > 0)
				{
					restore_current_blog();
				}				
		
				wp_die( __('<div style="color:red;font-size:20px">Sem permissão</div>
				<div style="font-weight:bold">Você não tem permissão para acessar essa página. Volte ao painel</div>') );
			} 
			?>
		</div>		
	</body>
</html>


