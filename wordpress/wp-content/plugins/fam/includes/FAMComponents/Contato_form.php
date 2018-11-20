<section id="conteudo_form_contato" style="float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;">
	
	<h1 class="single_type_label contact_icon topic_label_icon topic_font_color hand_font">
	<?
		if($options["title"] != null)
		{
			echo $options["title"];
		}
		else
		{
			echo "Entre em contato";
		}
	?>
	</h1>	
									
	<div>
		<label for="nome">Nome:</label>
	</div>
	<div>																
		<input type="text" name="nome" class="nome" class="input" maxlength="50" />				
	</div>	
	<div>
		<label for="email">Email:</label>
	</div>
	<div>				
		<input type="text" class="email" name="email" class="input" maxlength="50" />				
	</div>
	<div>
		<label for="assunto">Assunto:</label>
	</div>
	<div>
		<select class="assunto" name="assunto">
			<option value="Selecione">Selecione</option>
			<option value="Informações">Informações</option>
			<option value="Sugestão">Sugestão</option>
			<option value="Contato comercial">Contato comercial</option>
			<option value="Solicitação">Solicitação</option>
			<option <? if($_POST["assunto"] == "error" ){ echo "selected='selected'";} ?> value="Comunicar erro" >Comunicar erro</option>
		</select>				
					
	</div>
	<div>
		<label for="txtarea">Mensagem:</label>
	</div>
	<div>				
		<textarea name="txtarea" cols="45" rows="4" class="txtarea" class="textarea" maxlength="2000" ><? if($_POST["content"]){ echo $_POST["content"];} ?></textarea>				
	</div>
	<div>				
		<input type="hidden" value="send_email" name="action"> 
		<div class="send_mail_btn"> enviar</div>                           
											
	</div>		
</section>


