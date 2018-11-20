<?php

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("404");

get_header(); ?>

		<div id="content-container">
			<div id="content">
				<div id="bloco-conteudo-central" style="height:1560px">
					<div class="failmsg">
						<div class="ooops">
							<h2 >Ooops!</h2>
						</div>
						<div class="oopsContent">
							<div class="sad">
								<img src="/wp-content/themes/images/oops.png">
							</div>
							<div class="orientation">
								<h2 class="not_found">Conteúdo não encontrado!</h2>
								<h2 class="sugiro_buscar">Tente realizar uma busca</h2>
								<div class="busca_404">
									<? widget::Get("busca_form") ?>
								</div>
							</div>
						</div>						
					</div>	
					<div class="div404_separator">
						<hr/>
					</div>									
					<div class="clear"></div>					
					<?php widget::Get("ultimos-relatos", array('itens'=> 2,'content_lenght'=> 150,'width'=>'430px','show_more'=> 'yes', 'show_meta_data_label'=> 'yes','show_large_image'=> 'yes', 'orderby'=>"rand",'title'=> 'Alguns relatos de viagens','authorId'=>'2')); ?>
					<?php widget::Get("blog_posts", array('show_meta_data_label'=> 'yes','show_large_image'=> 'yes','itens'=> 2,'content_lenght'=> 250,'width'=>'450px','show_more'=> 'yes', 'float'=>'right','margin_right'=>'20px;')); ?>					
					<? //widget::Get('footer_adds', array('label'=>'404')); ?>	
				</div>
				</div>
				
			</div><!-- #content -->
			<div id="contentBottom">
					<div id="bottom-boxes-container">
						<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes')); ?>							
						<? widget::Get("facebook-box"); ?>
					</div>
					<div class="clear"></div>
					<? widget::Get("socialmedia");?>
					<?php widget::Get("codigocriativo")?>
				</div><!-- end content-bottom -->
		</div>
	</div><!-- end geral -->

<?php include('footer-wp.php') ?>