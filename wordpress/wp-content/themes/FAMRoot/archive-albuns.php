<?php
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
global 	$wp_query;
$archive_posts = $wp_query->posts;
Conteudo::SetMetas("archive-albuns");
	
get_header(); ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">				
					<div class="galeria">				
						<?php widget::Get("galeria", array('show_more' => 'yes','itens'=> 16,'default_archive'=>$archive_posts,'onlycover'=>'yes')) ?>	
						<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true)); ?>
						<?php widget::Get("share", array("comment_box_width" => '600')); ?>
					</div><!-- end galeria -->
					<aside id="coluna-lateral-direita">
						<? widget::Get("add_box_top_right", array('show_search_box'=>'yes','float'=>'right','margin_top'=>"20px",'label'=>'archive_albuns','width'=>'274px'));  ?>	
						<? //widget::Get("atualizacoes", array('itens'=> 2,'content_lenght'=> 95, 'show_read_full'=>'yes','show_location'=> true, 'float'=>'right','margin_right'=>'0px', 'width'=>'93%','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px'));?>
						<?php widget::Get("ultimos-relatos", array('itens'=> 2,'title'=>'Relatos de viagem','float'=>'right','content_lenght'=> 200,'width'=>'93%','show_more'=> 'yes')); ?>					
						<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'93%','float'=>'right','title'=>'Outras viagens'));?>					
					</aside>
					<? widget::Get('footer_adds', array('label'=>'archive-albuns')); ?>					
				</div>			
				<div class="clear"></div>
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<style type="text/css">
						.relatos{float: right;margin-right: 25px;}
					</style>
					<?php widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id()))?>
					<?php widget::Get("ultimos-relatos", array('float' => "right", 'margin_right' => "25px")) ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
	
<?php include('footer-wp.php') ?>