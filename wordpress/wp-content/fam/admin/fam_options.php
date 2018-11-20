<?php

add_action('admin_menu', 'create_theme_options_page');
function create_theme_options_page() {  
	if(!$screen->is_network)
	{
		add_options_page('Opções do site FAM', 'Opções do site FAM', 'fam_settings', __FILE__, 'build_fam_options_page');
	}
}

function build_fam_options_page()
{
	?>
		<div id="theme-options-wrap">
			<div class="icon32" id="icon-tools"> 
				<br /> 
			</div>
			<h2>Configurações de site de viagen Fazendo as Malas</h2>
			<p>Defina as configurações de aparência e comportamentos do seu site de viagem</p>
			<form method="post" action="<? ECHO $PHP_SELF; ?>">
				<p class="submit">        
					<select name="tema_fam" id="tema_fam" class="select_fam_theme" >
						<option value="adventure" <?php if (get_option('tema_fam') == 'adventure'){echo "selected='selected'";}?> >Adventure</option>
						<option value="light" <?php if (get_option('tema_fam') == 'light'){echo "selected='selected'";}?>  >Light</option>
					</select>     
				</p>
				<p>Plano de fundo:</p>
				<p class="background_options">
					<input type="radio" name="background_fam" value="infinity_road"  <?php if (get_option('background_fam') == 'infinity_road'){echo "checked='checked'";}?>  />Estrada infinita<br/>
					<input type="radio"  name="background_fam" value="ice_world"  <?php if (get_option('background_fam') == 'ice_world'){echo "checked='checked'";}?> />Mundo gelado<br/>
					<input type="radio" name="background_fam" value="open_ocean" <?php if (get_option('background_fam') == 'open_ocean'){echo "checked='checked'";}?> />Mundo das águas<br/>					
					<input type="radio" name="background_fam" value="night_sea" <?php if (get_option('background_fam') == 'night_sea'){echo "checked='checked'";}?> />Mar noturno<br/>
					<input type="radio" name="background_fam" value="old_map" <?php if (get_option('background_fam') == 'old_map'){echo "checked='checked'";}?> />Mapa antigo<br/>
					<input type="radio" name="background_fam" value="word_map_blue" <?php if (get_option('background_fam') == 'word_map_blue'){echo "checked='checked'";}?> />Mundo azul<br/>
					<input type="radio" name="background_fam" value="dark_bridge" <?php if (get_option('background_fam') == 'dark_bridge'){echo "checked='checked'";}?> />Ponte ao pôr-do-sol<br/>
					<input type="radio" name="background_fam" value="sky_and_sea" <?php if (get_option('background_fam') == 'sky_and_sea'){echo "checked='checked'";}?> />Céu e Mar<br/>
					<input type="radio" name="background_fam" value="paradise_beach" <?php if (get_option('background_fam') == 'paradise_beach'){echo "checked='checked'";}?> />Praia do paraíso<br/>
					<input type="radio" name="background_fam" value="railway-sunset" <?php if (get_option('background_fam') == 'railway-sunset'){echo "checked='checked'";}?> />Pôr-do-sol no trilho<br/>
					<input type="radio" name="background_fam" value="canada_sunset" <?php if (get_option('background_fam') == 'canada_sunset'){echo "checked='checked'";}?> />Pôr-do-sol no Canadá<br/>
					<input type="radio" name="background_fam" value="canada-winter" <?php if (get_option('background_fam') == 'canada-winter'){echo "checked='checked'";}?> />Inverno canadense<br/>
					<input type="radio" name="background_fam" value="monte-roraima" <?php if (get_option('background_fam') == 'monte-roraima'){echo "checked='checked'";}?> />Monte Roraima<br/>
					
				</p>
				<p>Posição do plano de fundo:</p>
				<p class="background_options">
					<input type="radio" name="background_position_fam" value="50% 0%"  <?php if (get_option('background_position_fam') == 'center top'){echo "checked='checked'";}?>  />Centralizado e alinhado no topo<br/>
					<input type="radio"  name="background_position_fam" value="50% 100%"  <?php if (get_option('background_position_fam') == 'center bottom'){echo "checked='checked'";}?> />Centralizado e alinhado no rodapé<br/>
					<input type="radio"  name="background_position_fam" value="100% 100%"  <?php if (get_option('background_position_fam') == '100% 100%'){echo "checked='checked'";}?> />Alinhado no rodapé à direita<br/>
					<input type="radio"  name="background_position_fam" value="0% 0%"  <?php if (get_option('background_position_fam') == '0% 0%'){echo "checked='checked'";}?> />Alinhado no top à esquerda<br/>
					<input type="radio"  name="background_position_fam" value="50% 50%"  <?php if (get_option('background_position_fam') == '50% 50%'){echo "checked='checked'";}?> />Centralizado<br/>
					
				</p>
				<? if(get_current_blog_id() != 1)
				{ ?>
				<p>Exibir neve animada:</p>
				<p class="neve_options">
					<input type="radio" name="show_snow_fam" value="yes"  <?php if (get_option('show_snow_fam') == 'yes'){echo "checked='checked'";}?>  />Exibir neve animada no tema<br/>
					<input type="radio"  name="show_snow_fam" value="no"  <?php if (get_option('show_snow_fam') == 'no'){echo "checked='checked'";}?> />Não exibir neve animada no tema<br/>
					
				</p>
				<?}?>
				</p>				
				
				<p>Mundo:</p>
				<p class="neve_options">
					<input type="radio" name="fam_mundo" value="mundo"  <?php if (get_option('fam_mundo') == 'mundo'){echo "checked='checked'";}?>  />Mundo padrão<br/>
					<input type="radio"  name="fam_mundo" value="mundo-gelado"  <?php if (get_option('fam_mundo') == 'mundo-gelado'){echo "checked='checked'";}?> />Mundo gelado<br/>
					
				</p>
				
				<p class="submit">        
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
					<input type="hidden" name="save_fam_settings" value="yes" id="save_fam_settings">     
				</p>   
			</form>  
		</div>
	<?
}


if($_POST["save_fam_settings"] == "yes")
{	
	update_option("tema_fam", $_POST["tema_fam"]);
	update_option("background_fam", $_POST['background_fam']);
	update_option('background_position_fam', $_POST['background_position_fam']);
	update_option('fam_mundo', $_POST['fam_mundo']);
	if(get_current_blog_id() != 1)
	{
		update_option('show_snow_fam', $_POST['show_snow_fam']);
	}
	
	//throw new Exception(var_export($tema_fam));
}
	

