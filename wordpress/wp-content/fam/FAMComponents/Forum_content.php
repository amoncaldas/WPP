<?
require_once(ABSPATH."/FAMCore/BO/Forum.php");
require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$forumBO   = new Forum();	
$categorias = $forumBO->GetData($options);

if($options["return"] != "onlyitens")
{	
?>
	<section class="forum list_forum_categories" style="float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;">
		<? if(is_content_archive('forum')) echo '<h1 class="single_type_label category_icon topic_label_icon topic_font_color hand_font">'; else echo '<h2>'; ?>
		<? if($options["title"] != null) echo $options["title"]; else echo "Fórum"; if(is_content_archive('forum')) echo "</h1>"; else echo "</h2>";?>				
			<ul>				
				<? }	
				if(is_array($categorias) && count($categorias)> 0)
				{				
					foreach($categorias as $categoria) 
					{									
						$dataLastPost = ($categoria->LastTopic->DataPost != null)? $categoria->LastTopic->DataPost: $categoria->LastTopic->DataPublicacao; 
						if($dataLastPost != null)
						{
							$dataLastPostFormated = strtotime(str_replace('/', '-', $dataLastPost));
							$dataLastPostFormated = utf8_encode(strftime("%d %b %Y", $dataLastPostFormated));
						}
				
						$dataLastComment = $categoria->LastComment->comment_date;
						if($dataLastComment != null)
						{
							$dataLastCommentFormated = strtotime(str_replace('/', '-', $dataLastComment));
							$dataLastCommentFormated = utf8_encode(strftime("%d %b %Y", $dataLastCommentFormated));
						}
						?>
						<li>
							<div class="header_cat">
								<div class="categoria">				
									<h3><a href="<? echo '/forum/'.$categoria->slug;?>"><? echo $categoria->name; ?></a></h3>	
																									
								</div>
								<div class="topics">
									<div class="cat_topics">Tópicos:<span><? echo $categoria->post_amount; ?></span></div>
									</div>
									<div class="posts">
									<div class="cat_comments">Mensagens:<span><? echo $categoria->comments_amount; ?></span></div>
								</div>
							</div>
								<div class="content_cat">
								<div class="categoria">	
									<span class="cat_desc"><span><? echo $categoria->descricao; ?></span></span>							
									<span class="cat_topics_label">Tópico(s) recente(s):</span>
									<?
										if(is_array($categoria->LastTopics) && count($categoria->LastTopics))
										{
											$counter = 1;
											foreach($categoria->LastTopics as $topic)
											{
												if($counter != count($categoria->LastTopics))
												{
													?><span class="topic_title"><a href="<? echo $topic->PostUrl ?>"> <? echo $topic->Titulo; ?></a>,</span><?
												}
												else
												{
													?><span class="topic_title"><a href="<? echo $topic->PostUrl ?>"> <? echo $topic->Titulo; ?> </a></span><?
												}
												$counter++;
											}
										}
									?>
								</div>
							
								<div class="last_topic">								
									<span class="last_topic_label">Último tópico:</span>
									<span class="last_topic_title"><a href="<? echo $categoria->LastTopic->PostUrl ?>"> <? echo $categoria->LastTopic->Titulo; ?></a></span>				
									<div class="autor">Autor:<a href='<? echo $categoria->LastTopic->Autor->ViajanteUrl?>' ><? echo $categoria->LastTopic->Autor->FullName?></a></div>
									<time datetime="<? echo $dataLastPost;?>"><? echo $dataLastPostFormated;?></time>
								</div>
								<div class="last_comment">	
								<div class="last_comment_title">Última mensagem:</div>
									<span><a href="<? echo $categoria->LastComment->Url; ?>"><? echo $categoria->LastComment->Title;?></a></span>
									<?if($categoria->LastComment->Autor->FullName != null)
									{		
										?><div class="autor">Autor:<a href='<? echo $categoria->LastComment->Autor->ViajanteUrl?>' ><? echo $categoria->LastComment->Autor->FullName?></a></div><?
								
									}?>
									<time datetime="<? echo $dataLastComment;?>"><? echo $dataLastCommentFormated;?></time>
								</div>
							</div>
							<input type='hidden' class='itemId' value="<? echo $categoria->cat_id; ?>"/>
						</li>	
					<?			
					}			
					?>			
			
			<? 
			
			if($options["show_more"] == "yes" && $forumBO->HasMoreData)
			{						
				echo "<li class='loadmore'>";				
				widget::Get("load-more-content", array("content_type"=>"forum","itens"=>3, "excluded_ids"=>$options["excluded_ids"]));
				echo "</li>";
				
			}
			if(!$forumBO->HasMoreData)
			{	
				echo "<li style='display:none;' class='no_more'></li>";
			}
		}
			
	if($options["return"] != "onlyitens"){
			echo "</ul>";
			if($options["show_register_link"] == "yes")
			{
				?>
				<div class="register_link_div">	
					<a class="register_open">Participe do fórum</a>
				</div>
				<?
			}
			?>	
		</section>
		<?
	}
	
?>