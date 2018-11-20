<?php require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("index");
include('header.php') ?>
	<div id="content-container">					
		<div id="content">				
			<div id="home">	
				<? widget::Get("slideFotos") ?>				
				<? widget::Get("atualizacoes", array('itens'=> 2,'hide_message_on_empty'=>'yes','show_more'=>'no','content_lenght'=> 60, 'link_on_content'=> 'yes','show_location'=> true, 'float'=>'right','margin_right'=>'15px', 'width'=>'258px','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px'));?>
				<div class="clear"></div>
				<? widget::Get("fotos", array('orderby'=>'rand')) ?>
				<? widget::Get("a-viagem", array('idViagem'=>get_current_blog_id())); ?>
				<? widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id(), 'width'=>'380px', 'height'=>'227px')) ?>
				<div class="clear"></div>
				<?php widget::Get("ultimos-relatos", array('itens'=> 3,'content_lenght'=> 160,'width'=>'450px','show_more'=> 'no')); ?>
				<?php widget::Get("blog_posts", array('itens'=> 3,'content_lenght'=> 150,'width'=>'450px','show_more'=> 'no', 'float'=>'right','margin_right'=>'20px;')); ?>
				<div class="clear"></div>
				<? widget::Get("share",array("showface" => 'false', "hideCommentBox" => true)); ?>
			</div>
			<div class="clear"></div>				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">				
				<? widget::Get("viagens", array('excluded_ids'=>get_current_blog_id(),'show_view_all'=>'yes', 'itens'=>2)); ?>					
				<? widget::Get("facebook-box") ?>
			</div>
			<div class="clear"></div>
			<? widget::Get("codigocriativo")?>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>