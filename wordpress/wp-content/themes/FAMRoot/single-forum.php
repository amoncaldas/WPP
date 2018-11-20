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
Conteudo::SetMetas("single-forum", $postForum->DadosPost);

include('header.php') ?>
		<div id="content-container">		
			<div id="content">		
				<div id="bloco-conteudo-central" class="single-forum">				
					
					<div class="single_forum">
						<div class="forum single_type_label forum_icon topic_label_icon topic_font_color hand_font">Tópico de fórum</div>
						<header class="topic-details-header">							
							<div class="topic_info">
																						
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
										$dataFormated = strtotime(str_replace('/', '-', $data));
										$dataFormated = utf8_encode(strftime("%d %b %Y", $dataFormated));
									?>
									<time datetime="<? echo $data;?>">Em <? echo $dataFormated;?></time>
								</div>
								<h1><span class="titulo"><? echo $postForum->DadosPost->Titulo; ?></span></h1>	
							</div>
						</header>
						<div class="desc_topic"><? echo $postForum->DadosPost->Resumo;?></div>
						<?php widget::Get("comentarios", array('itens'=>10,'title'=>'Mensagens nesse tópico','width'=>'600px', 'show_more'=> 'yes','parentId'=>$id, 'show_message_on_empty'=>'yes')); ?>
						<?php widget::Get("share", array("comment_box_width" => '600', 'show_native_comment_form'=>'yes', 'native_comment_title'=>'Escreva sua mensagem neste tópico', 'parentId'=>$id)); ?>	
						<?php widget::Get("forum", array('itens'=> 3,'title'=>'Navegue pelo fórum','width'=>'600px','show_more'=> 'yes','margin_right'=>'12px')); ?>																		
												
						<div class="clear"></div>												
													
					</div>
					<aside id="coluna-lateral-direita" class="single_forum" >
						<? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"10px",'label'=>'single_forum','margin_right'=>'-5px'));  ?>
						<? widget::Get("viagens", array('list_type'=>'box','itens'=>2,'show_more'=>'yes','width'=>'100%','float'=>'right'));?>
						<?php widget::Get("ultimos-relatos", array('itens'=> 3,'title'=>'Relatos de viagem','content_lenght'=> 160,'width'=>'100%','show_more'=> 'yes')); ?>
					</aside>							
					<div class="clear"></div>
					<? widget::Get('footer_adds', array('label'=>'single-forum')); ?>	
				</div><!-- end bloco-conteudo-central -->
			</div><!-- end content -->
			<div id="contentBottom">
				<div id="bottom-boxes-container">
					<? widget::Get("viagens", array('itens'=>2,'show_view_all'=>'yes')); ?>
					<?php widget::Get("ultimos-relatos", array('float' => "right", 'margin_right' => "25px")) ?>
				</div>
				<div class="clear"></div>
				<? widget::Get("socialmedia");?>
				<?php widget::Get("codigocriativo")?>
			</div><!-- end content-bottom -->
		</div><!-- end content -->
	</div><!-- end geral -->
<?php include('footer-wp.php') ?>

