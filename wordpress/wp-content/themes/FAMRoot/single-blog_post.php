<?php
require_once(ABSPATH."/FAMCore/BO/BlogPost.php");
$id = $wp_query->post->ID;
if($id == null)
{
	global $blog_post_id;
	$id = $blog_post_id;		
}
$postBlog  = new BlogPost($id);

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("single-blog", $postBlog->DadosPost);

include('header.php') ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central">				
					<div class="escrita detalheBlog single_type_label blog_icon topic_label_icon topic_font_color hand_font">Blog Fazendo as Malas</div>
					<article class="relato-details">
						<header class="relato-details-header">							
							<div class="foto-conteiner largeImage">
								<a class="fancybox" title="<? echo $postBlog->DadosPost->MidiaPrincipal->Titulo ?>" href="<? echo $postBlog->DadosPost->MidiaPrincipal->ImageLargeSrc ?>">
									<img style="width:100%" class="foto_viajante_relato" src="<? echo $postBlog->DadosPost->MidiaPrincipal->ImageLargeSrc?>"/>
								</a>
							</div>
							<?
							$data = ($postBlog->DadosPost->DataPost != null)? $postBlog->DadosPost->DataPost: $postBlog->DadosPost->DataPublicacao;
							$dataFormated = strtotime(str_replace('/', '-', $data));
							$dataFormated = utf8_encode(strftime("%d %b %Y", $dataFormated));
							?>
							
							<? if ($postBlog->DadosPost->Location->Local != null)
							{
								
							?>
							
							<?}?>
							<div class="meta_container author">
								<div class="content_meta">
									<span class="meta_author">
										<span class="meta_icon">i</span>
										<span class="meta_text">
											<a title="Veja o perfil de <? echo $postBlog->DadosPost->Autor->FullName; ?>" href="<? echo $postBlog->DadosPost->Autor->ViajanteUrl?>"><? echo $postBlog->DadosPost->Autor->FullName; ?></a>
										</span>
									</span>
									<span class="meta_date">
										<span class="meta_separator">s</span>
										<span class="meta_text">
											<time datetime="<? echo $data;?>" ><? echo $dataFormated;?></time>
										</span>
									</span>
								</div>
							</div>	
							<? if ($postBlog->DadosPost->Location->Local != null)
							{
							?>
							<div class="meta_container place">
								<div class="content_meta">
									<span class="meta_place">
										<span class="meta_icon">i</span>
										<span class="meta_text">
											<a href="javascript:void(0);"><? echo $postBlog->DadosPost->Location->Local; ?></a>
										</span>
									</span>																	
								</div>
							</div>
							<?}?>
						</header>				
												
						<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true,'send'=>'true')); ?>	
						<div class="relato_info blog_single_title">
							<h1><span class="titulo"><? echo $postBlog->DadosPost->Titulo; ?></span></h1>	
						</div>
						<div id="locationInfo_relato">
							<? widget::Get("content-location", array("location" => $postBlog->DadosPost->Location, "locationImage" =>  $postBlog->DadosPost->Autor->UserImage->ImageThumbSrc)); ?>	
						</div>	
						<div class="html_content">									
							<? echo $postBlog->DadosPost->Conteudo; ?>
							<a class="comunicate_erro" href="javascript:void(0).">Encontrou erros nesse post? Comunique!</a>
						</div>
					<div class="clear"></div>
					<? widget::Get("add_responsive", array('float'=>'left','margin_top'=>"10px"));?>
					<? widget::Get("posts_relacionados", array("post_id" => $postBlog->DadosPost->PostId)); ?>			
					<? widget::Get("share", array("comment_box_width" => '580','send'=>'true')); ?>					
				</article>
				
				<aside id="coluna-lateral-direita" class="blog_single_lateral lateral_without_title_spacer" >
					<? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"-58px",'label'=>'single_blog'));  ?>
					<? //widget::Get("blog_sidebar", array('itens'=> 2,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>
					<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'100%','float'=>'right'));?>						
					<? widget::Get("blog_posts", array('excluded_ids'=>$postBlog->DadosPost->PostId,'itens'=> 3,'show_share'=>'no','content_lenght'=> 100,'show_more'=> 'yes','width'=>'100%','float'=>'right')); ?>	
					<? widget::Get("aside_middle_box", array('margin_left'=>'0px'));?>
					<? widget::Get("ultimos-relatos", array('itens'=> 2,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>
					<? widget::Get("galeria", array('title'=>'Álbuns','show_more' => 'yes','itens'=> 2,'float'=>'right','width'=>'100%')) ?>																
				</aside>						
				<div class="clear"></div>					
				<? widget::Get('footer_adds', array('label'=>'single-blog_post'));?>
			</div><!-- end bloco-conteudo-central -->
		</div><!-- end content -->
		<div id="contentBottom">
			<div id="bottom-boxes-container">
				<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes')); ?>
				<? widget::Get("facebook-box"); ?>
			</div>
			<div class="clear"></div>
			<? widget::Get("socialmedia");?>
			<? widget::Get("codigocriativo")?>
		</div><!-- end content-bottom -->
	</div><!-- end content -->
</div><!-- end geral -->
<? include('footer-wp.php') ?>

