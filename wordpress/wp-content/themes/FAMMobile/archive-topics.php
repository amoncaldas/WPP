<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global $categoria;
Conteudo::SetMetas("archive-topics",null, $categoria->name,$categoria->name);

include('header.php');?>		
	<div id="content">			
		<?php widget::Get("forum_topics", array('itens'=> 4,'width'=>'100%','show_more'=> 'yes','margin_right'=>'12px','parentId'=> $categoria->term_id)); ?>			
		<div class="clear"></div>				
	</div><!-- end content -->
<?php include('footer/footer-default.php') ?>



