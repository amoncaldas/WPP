<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }

?>

<div class="form_search">
	<form id="search" action="<?php bloginfo('home'); ?>" method="get">
		<div>
			<input type="text" placeholder="Busca" name="s" id="s" inputmode="predictOn" value="" />
			<input type="submit" name="submit_button" class="btn_mobile" value="Buscar" />
		</div>
	</form>
</div>
