<?php require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("index");
include('header.php') ?>
	<div id="content-container">					
		<div id="content">				
			<div id="home">	
				<? widget::Get("slideFotos") ?>				
				<? widget::Get("atualizacoes", array('add_css_class'=>'home_float_top_atualizacao','itens'=> 2,'hide_message_on_empty'=>'yes','show_more'=>'no','content_lenght'=> 60, 'link_on_content'=> 'yes','show_location'=> true, 'float'=>'right','margin_right'=>'15px', 'width'=>'258px','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px'));?>
				<div class="clear"></div>				
				<? 
					global $relatosCount;													
					if($relatosCount > 0)
					{
						widget::Get("ultimos-relatos", array('show_meta_data_label'=>'yes','show_large_image'=>'yes','itens'=> 2,'content_lenght'=> 300,'width'=>'463px','height'=>'260px','show_more'=> 'yes', 'margin_right'=>'22px')); 
					}					
					if($relatosCount == 0)
					{
						widget::Get("blog_posts", array('title'=>'Blog FAM','show_meta_data_label'=>'yes','show_large_image'=>'yes','itens'=> 2-$relatosCount,'content_lenght'=> 300,'width'=>'463px','show_more'=> 'yes', 'margin_right'=>'25px','margin_left'=>'22px','float'=>'left'));
					}
					
					widget::Get("roteiro_localizacao", array('show_last_location'=>'no','title'=>'Roteiro da viagem','idViagem'=>get_current_blog_id(), 'width'=>'435px', 'height'=>'350px'));
								
					if($relatosCount == 1)
					{
						widget::Get("blog_posts", array('title'=>'Blog FAM','show_meta_data_label'=>'yes','show_large_image'=>'yes','itens'=> 2-$relatosCount,'content_lenght'=> 300,'width'=>'463px','show_more'=> 'yes', 'margin_right'=>'25px','margin_left'=>'22px','float'=>'left'));
					}
					global $GetMultiSiteData;
					$GetMultiSiteData = wp_count_posts('albuns')->publish < 2? true: false;	
					$title = ($GetMultiSiteData === true)? "Albuns de viagens": 'Albuns de viagem';
					$margin_top = $relatosCount == 1? "-200px": "0px";
					widget::Get("galeria", array('title'=>$title,'show_more' => 'yes','itens'=> 6,'width'=>'474px','float'=>'right','margin_top'=>$margin_top));
					$GetMultiSiteData = false;				
					widget::Get("fotos", array('orderby'=>'rand','destaques_video'=>'yes','itens'=>10,'title'=>'Vídeos','width'=>'960px','show_ver_albuns'=>'no')) 
				?>				
				<div class="clear"></div>
				
				<? widget::Get("share",array('show_share_bar' => 'yes',"share_full" => "yes", "hideCommentBox" => true)); ?>
				<div class="footer_adds_index">
					<? widget::Get('footer_adds', array('label'=>'index')); ?>	
				</div>
			</div>
			<div class="clear"></div>				
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<? widget::Get("facebook-box"); ?>				
			</div>
			<div class="clear"></div>
			<? widget::Get("socialmedia");?>
			<? widget::Get("codigocriativo")?>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>