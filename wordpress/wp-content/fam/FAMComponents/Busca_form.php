
<form class="busca_form" action="<?php bloginfo('url')?>?s=">	
	<div <? if($options["resizable"] == true){ echo "style='width:".($options["width_val"] +10)."px;'"; } ?> class="busca_div <? if($options["resizable"] == true){ echo "search_form_resizable"; } else echo "search_form";?>">
		<input value="<? echo $_GET["s"];?>" autocomplete="off" id="s" name="s" <? if($options["resizable"] == true){ echo "style='width:".($options["width_val"] - 33)."px;'"; } ?>  placeholder="digite sua busca" type="text"/>
		<input id="search_btn" type="submit"/>
		<ul></ul>
	</div>	
	<? 
	if(get_current_blog_id() == 1 )
	{
		echo "<input type='hidden' id='site_url' value='/' />";
	}	
	?>
</form>	


	


