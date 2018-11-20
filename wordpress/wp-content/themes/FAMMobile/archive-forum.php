<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global $categoria;
if($categoria->term_id != null)
{
	Conteudo::SetMetas("archive-topics",null, $categoria->name,$categoria->category_description);
}
else
{
	Conteudo::SetMetas("archive-forum");
}

include('header.php');?>
	<div id="content">			
		<?php 
			if($categoria->term_id != null)
			{
				widget::Get("forum_topics", array('itens'=> 10,'show_register_link'=>'yes','width'=>'100%','show_more'=> 'yes','margin_right'=>'12px','parentId'=> $categoria->term_id)); 
			}
			else
			{
				widget::Get("forum", array('itens'=> 10,'title'=>'Categorias do fórum','width'=>'100%','show_register_link'=>'yes','show_more'=> 'yes','margin_right'=>'12px'));
			}	
		 ?>				
					
		<div class="clear"></div>	
	</div><!-- end content -->

<?php include('footer/footer-default.php') ?>



