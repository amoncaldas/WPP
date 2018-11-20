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
	<div id="content">								
		<div class="escrita detalheBlog single_type_label blog_icon topic_label_icon topic_font_color hand_font">Post do blog</div>
		<article class="relato-details">
			<header class="relato-details-header">				
				<div class="foto-conteiner largeImage">
					<a class="fancybox" title="Viajante <? echo $postBlog->DadosPost->Autor->FirstName.' '.$postBlog->DadosPost->Autor->LastName; ?>" href="<? echo $postBlog->DadosPost->Autor->UserImage->ImageLargeSrc ?>">
						<img style="width:100%" class="foto_viajante_relato" src="<? echo $postBlog->DadosPost->MidiaPrincipal->ImageLargeSrc ?>"/>
					</a>
				</div>					
				<?
				$data = ($postBlog->DadosPost->DataPost != null)? $postBlog->DadosPost->DataPost: $postBlog->DadosPost->DataPublicacao;
				$data = strtotime(str_replace('/', '-', $data));
				$dataFormated = utf8_encode(strftime("%d %b %Y", $data));
				$data = utf8_encode(strftime("%Y-%m-%d %T", $data));
				?>							
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
								<time datetime="<? echo $data;?>"><? echo $dataFormated;?></time>
							</span>
						</span>
					</div>
				</div>
				
				<? if ($postBlog->DadosPost->Location->Local != null)
				{?>		
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
			<? widget::Get("share", array('show_share_bar' => 'yes','hideCommentBox'=>true,'send'=>true)); ?>
			<div class="relato_info">
				<h1><span class="titulo"><? echo $postBlog->DadosPost->Titulo; ?></span></h1>
			</div>		
			<div class="html_content">												
				<? echo $postBlog->DadosPost->Conteudo; ?>						
			</div>		
			<? widget::Get("posts_relacionados", array("post_id" => $postBlog->DadosPost->PostId)); ?>
			<div class="clear"></div>						
			<? widget::Get("share",array('hideCommentBox'=>false,'send'=>true));?>			
		</article>			
		<div class="clear"></div>					
				
	</div><!-- end content -->	
<?php include('footer/footer-default.php') ?>

