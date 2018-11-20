
<div id="links-bottom">
	<? 
	if($options["site_type"] == "sub")
	{?>		
			<a href="/">Fazendoasmalas.com</a>
			<span class="separator">|</span>
		<a href="<?php bloginfo('url')?>/relatos">Relatos</a>
			<span class="separator">|</span>		
		<a href="<?php bloginfo('url')?>/status">Status</a>		
			<span class="separator">|</span>
			<a href="/contato/">Contato</a>
			<span class="separator">|</span>
			<a href="/blog/">Blog</a>
			<span class="separator">|</span>
			<a href="/forum/">Fórum</a>	
			<span class="separator">|</span>		
			
			<a class="comunicate_erro" href="javascript:void(0;)">Comunicar erro</a>
			
	<?}
	else
		{?>		
		<a href="/viagens">Viagens</a>
		<span class="separator">|</span>
		<a href="/contato">Contato</a>
		<span class="separator">|</span>
		<a href="/termos-de-uso-do-forum/">Termos de uso</a>
		<span class="separator">|</span>
		<a href="/blog/">Blog</a>
		<span class="separator">|</span>
		<a href="/forum/">Fórum</a>
		<!--<span class="separator">|</span>
		<a href="/dicas-de-viagem/">Dicas de viagem</a>	-->
		<span class="separator">|</span>		
		<a class="comunicate_erro" href="javascript:void(0;)">Comunicar erro</a>
	<?}
?>
</div>
