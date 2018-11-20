<? require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas('index');
include('header.php') ?>
	<div id="content-container">					
		<div id="content">				
			<div id="home">			
				<? widget::Get("viagens", array('show_view_all'=>'yes','itens'=>4,'margin_left'=>'20px','orderBy'=>'blog_id DESC','title'=>'Últimas viagens','other_countries'=> 'yes')) ?>	
				<? //widget::Get("atualizacoes", array('itens'=> 2,'content_lenght'=> 95, 'show_location'=> true, 'title'=>'Status recentes','show_map'=>'yes','float'=>'right','margin_right'=>'30px', 'link_on_content'=> 'yes','width'=>'500px','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px'));?>			
				<? widget::Get("mapa_viagens"); ?>
				<div class="add_home_after_map">
					<? widget::Get("booking_form", array('margin_right'=>'4px', 'margin_left'=>'-5px','show_search_box'=>'yes','float'=>'left','margin_top'=>"0px",'label'=>'home_geral','width'=>'524px','height'=>'170px'));  ?>
					<?/*<div style="float:left" class="rentalcars">	
							<a target="_blank" href="http://www.rentalcars.com/Home.do?affiliateCode=fazendoasmalas&preflang=pt" title="Os preços mais baixos garantidos para o aluguer de automóveis em mais de 6.000 locais em todo o mundo">
							<img src="/wp-content/FAMComponents/adds/rentalcars/Cheap_Car-Hire_pt.png" style="height: 170px; width: 167px;" border="0" alt="Os preços mais baixos garantidos para o aluguer de automóveis em mais de 6.000 locais em todo o mundo" /></a>
					</div>*/?>
				</div>
				<div id="after_viagens_slide"></div>		
				
				<? //widget::Get("ultimos-relatos", array('content_lenght'=> 450,'show_more' => 'yes','itens'=> 2,'width'=>'430px', 'title'=>'Relatos de viagens', 'orderby'=>'rand', 'authorId'=>2, 'site_id'=> 1, 'show_meta_data_label'=> 'yes', 'show_large_image'=> 'yes')) ?>								
				<? widget::Get("galeria", array('show_more' => 'yes','itens'=> 8,'width'=>'500px', 'title'=>'Albuns de viagens', 'orderby'=>'rand','site_id'=> 1)) ?>
				
				<div id="before_mapa_viagens" class="clear"></div>			
												
				<? //widget::Get("fotos", array('orderby'=>'rand','destaques_video'=>'yes','itens'=>4,'title'=>'Vídeos','width'=>'960px','show_ver_albuns'=>'no','max_visible_itens'=>'2')) ?>			
				
				<? widget::Get("forum", array('itens'=> 3,'width'=>'450px','show_more'=> 'no','margin_right'=>'12px')); ?>
				<? widget::Get("blog_posts", array('show_meta_data_label'=> 'yes','show_large_image'=> 'yes','itens'=> 2,'content_lenght'=> 250,'width'=>'450px','show_more'=> 'yes', 'float'=>'right','margin_right'=>'20px;')); ?>					
				<? //widget::Get("atualizacoes", array('content_lenght'=> "120", 'itens'=> 2,  'title'=> "Status de viagem",  'width'=> "450px", 'show_location'=> "yes", 'show_comment'=> 'no', 'show_media'=> 'no', 'foto_width'=> '70px', 'float'=> 'left', 'show_more'=> 'yes', 'margin_left'=> '20px', 'content_lenght'=> '130')); ?>			
				
				<div id="before_share" class="clear"></div>
				<? widget::Get("share",array('show_share_bar' => 'yes', "share_full" => "yes","hideCommentBox" => true)); ?>
				<div class="footer_adds_index">
					<? widget::Get('footer_adds', array('label'=>'index')); ?>	
				</div>
			</div>
			<div class="clear"></div>				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">				
				<? widget::Get("twitter-box"); ?>							
				<? widget::Get("facebook-box"); ?>					
			</div>
			<div class="clear"></div>			
			<? widget::Get("socialmedia");?>
			<? widget::Get("codigocriativo");?>
			
		</div><!-- end content-bottom -->
		
	</div><!-- end content -->
</div><!-- end geral -->

<?php include('footer-wp.php') ?>