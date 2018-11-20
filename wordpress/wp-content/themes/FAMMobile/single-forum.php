<?php
require_once(ABSPATH."/FAMCore/BO/Forum.php");
$id = $wp_query->post->ID;
if($id == null)
{
	global $topic_post_id;
	$id = $topic_post_id;		
}
$postForum = new Forum($id);

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("single-forum",  $postForum->DadosPost);

include('header.php') ?>			
	<div id="content">
			<article class="single_forum">
				<div class="forum single_type_label forum_icon topic_label_icon topic_font_color hand_font">Tópico de fórum</h2>
				<header class="topic-details-header">							
					<div class="topic_info">
						<h1><span class="titulo"><? echo $postForum->DadosPost->Titulo; ?></span></h1>															
						<div class="autorInfo">
							<img class="foto_usuario_forum" src="<? echo $postForum->DadosPost->Autor->UserImage->ImageGaleryThumbSrc ?>"/>																										
						</div>
						<div class="info">
							<div class="autor"><a href='<? echo $postForum->DadosPost->Autor->ViajanteUrl?>' ><? echo $postForum->DadosPost->Autor->FullName; ?></a></div>
							<div class="statistics_user">
								<span>Tópicos:</span><div class="counter"><? echo $postForum->DadosPost->Autor->CountForumTopics; ?></div>
								<span>Mensagens:</span><div class="counter"><? echo $postForum->DadosPost->Autor->CountForumMessages; ?></div>
							</div>
							<? $data = ($postForum->DadosPost->DataPost != null)? $postForum->DadosPost->DataPost: $postForum->DadosPost->DataPublicacao;
								$data = strtotime(str_replace('/', '-', $data));
								$dataFormated = utf8_encode(strftime("%d %b %Y", $data));
								$data = utf8_encode(strftime("%Y-%m-%d %T", $data));
							?>
							<time datetime="<? echo $data;?>">Em <? echo $dataFormated;?></time>
						</div>
								
					</div>
				</header>
				<div class="desc_topic"><? echo $postForum->DadosPost->Resumo;?></div>
				<?php widget::Get("comentarios", array('itens'=>10,'title'=>'Mensagens nesse tópico','width'=>'100%', 'show_more'=> 'yes','parentId'=>$id, 'show_message_on_empty'=>'yes')); ?>
				<?php widget::Get("share", array('hideShareBtns'=>false,'send'=>true)); ?>	
				<?php widget::Get("forum", array('itens'=> 3,'title'=>'Navegue pelo fórum','width'=>'100%','show_more'=> 'yes','margin_right'=>'12px')); ?>												
				<div class="clear"></div>													
			</article>											
			<div class="clear"></div>			
	</div><!-- end content -->	
<?php include('footer/footer-default.php') ?>

