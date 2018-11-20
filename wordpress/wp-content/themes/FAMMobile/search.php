<?php
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("search", null, $_GET["s"], $_GET["s"]);
if($_POST["is_ajax"] == null)
{
	include('header.php'); 
}
else
{
	require_once(ABSPATH."/wp-content/FAMComponents/widget.php");
}
	
 ?>			
	<div id="content">							
		<div class="search single_type_label search_icon topic_label_icon topic_font_color hand_font">Resultado de busca</div>		
		<h1 class="search_scope">Itens para a busca por <strong>'<? echo $_GET["s"];?>'</strong></h1>
		<div class="searchDiv">					
			<?php widget::Get("resultados_busca", array('itens'=> 20,'show_more'=> 'yes', 'term'=>$_GET["s"],'multisite'=>"yes")); ?>	
			<? 
				global $noSearchResult;							
				if($noSearchResult == "yes") 
				{					
					widget::Get("ultimos-relatos", array('title'=>'Veja os últimos relatos','itens'=> 8,'show_share'=>'yes','content_lenght'=> 350,'width'=>'100%', 'show_more'=> 'yes'));					
				}
			?>						
		</div>				
	</div><!-- end content -->		
	
<?php 
	if($_POST["is_ajax"] == null)
	{
		include('footer/footer-default.php');
	}
?>

