<?php 
require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("archive-relatos");


include('header.php');?>
	<div id="content-container">			
		<div id="content">				
			<div id="bloco-conteudo-central">				
				<div class="relatosInterna">
					<h2>Post do blog</h2>
					<ul>
						<?php widget::Get("blog_posts", array('itens'=> 1,'content_lenght'=> 200,'width'=>'95%','show_more'=> 'yes')); ?>											
					</ul>					
				</div>
				<!-- end relatos -->
				
				<div id="coluna-lateral-direita">
					<?php widget::Get("ultimos-relatos", array('itens'=> 3,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>
					<?php widget::Get("fotos", array('show_more' => 'yes','itens'=>12)) ?>					
				</div>	
				<?php widget::Get("share", array("comment_box_width" => '900')); ?>
				
			</div>			
			<div class="clear"></div>
				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<?php widget::Get("roteiro_localizacao", array('idViagem'=>get_current_blog_id()));?>
				<?php widget::Get("facebook-box");?>				
				<div class="clear"></div>
				<?php widget::Get("codigocriativo");?>				
			</div>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<?php include('footer-wp.php') ?>



