
<div class="social_media_container">	
	<div class="optin_news">
		<input type="text" value="" placeholder="Aventuras e viagens no seu email" class="news_mail" />	
		<div style="display:none;" class="news_options">
			<input  type="text" value="" placeholder="Digite o seu nome" maxlength="30" class="news_name" />	
			<div style="display:none;" class="optin_type">
				<input type="radio" id="all_trips" name="trips_selection" selected="selected" value="all"  />
				<label for="all_trips">Todas as viagens</label>
				<input style="margin-left:20px;" type="radio" id="selected_trips"  name="trips_selection" value="selected"  />
				<label for="selected_trips">Escolher viagens</label>
			</div>
			<div style="display:none;" class="trip_list">
				<? 		
					/*$viagens = Conteudo::GetSites(array());
					foreach($viagens as $viagem)
					{
						echo '<div class="trip_choice"><input id="trip_'.$viagem->blog_id.'" name="trip_news_select" type="checkbox"  />
						<label for="trip_'.$viagem->blog_id.'">/'.trim($viagem->path,"/").'</label></div>';
					}*/
				?>
			</div>
			<? 		
			if(is_user_logged_in() && get_current_blog_id() == 1 && in_array(get_user_role(),array('administrator','adm_fam_root'))) 
			{
				?><input type='checkbox' class='opt_out' name='opt_out' value='yes'/> <label for='opt_out'>Remover</label> <?
			}
			?>
			<div class="save_optin_in_news">Enviar</div>
		</div>	
		<div class="email_valid"></div>
	</div>
	
	<div class="social_network">
		<ul>
			<li><a target="_blank" title="FazendoAsMalas no YouTube" href="//youtube.com/fazendoasmalas?sub_confirmation=1"><img alt="YouTube" src="/wp-content/themes/images/icons/social_media/youtube.png" /></a></li>
			<li><a target="_blank" title="FazendoAsMalas no Twitter" href="//twitter.com/fazendoasmalas"><img alt="Twitter" src="/wp-content/themes/images/icons/social_media/twitter.png" /></a></li>
			<li><a target="_blank" title="FazendoAsMalas no Facebook" href="//facebook.com/fazendoasmalas"><img alt="Twitter" src="/wp-content/themes/images/icons/social_media/facebook.png" /></a></li>
			<li><a target="_blank" title="FazendoAsMalas no Gplus" href="//google.com/+fazendoasmalas/"><img alt="Google Plus" src="/wp-content/themes/images/icons/social_media/gplus.png" /></a></li>
			<li><a target="_blank" title="Feed RSS FazenndoAsMalas " href="/feed"><img alt="Feed RSS" src="/wp-content/themes/images/icons/social_media/feed.png" /></a></li>		
		</ul>
	</div>
</div>