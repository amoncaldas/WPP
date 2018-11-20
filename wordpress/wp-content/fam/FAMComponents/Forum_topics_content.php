<?
require_once(ABSPATH."/FAMCore/BO/Forum.php");
require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$forumBO  = new Forum();	
$topicos = $forumBO->GetTopicosCategoriaForum($options);	

global $categoria;
//$categoria != null || is_category()
if($options["return"] != "onlyitens")
{?>
	<section class="topicos" style="float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;">
		<?			
			if($options["title"] == null) { $options["title"] = "Categoria de fórum";}			
			?><div class="single_type_label category_icon topic_label_icon topic_font_color hand_font" ><? echo $options["title"];?></div>
			<? if($categoria != null) echo '<h1 style="font-size: 24px;margin-bottom: 10px;color:#333" class="">'.$categoria->category_description.'</h1>'; else echo '<h2 style="font-size: 24px; margin-bottom: 10px; background: none; font-family: Tahoma;color: #333; padding-left: 0px;" class="">'.$categoria->category_description.'</h2>';
		?>
						
		<ul>
			<? }	
				if(is_array($topicos) && count($topicos)> 0)
				{		
					foreach($topicos as $post) 
					{						
						$data = ($post->DataPost != null)? $post->DataPost: $post->DataPublicacao;
						$dataFormated = strtotime(str_replace('/', '-', $data));
						$dataFormated = utf8_encode(strftime("%d %b %Y", $dataFormated));
					
						$dataLastComment = $post->LastComment->comment_date;
						if($dataLastComment != null)
						{
							$dataLastCommentFormated = strtotime(str_replace('/', '-', $dataLastComment));
							$dataLastCommentFormated = utf8_encode(strftime("%d %b %Y", $dataLastCommentFormated));
						}
						?>
							<li>	
								<div class="topic_item">
									<h3><a href="<? echo $post->PostUrl ?>"><? echo $post->Titulo; ?></a></h3>
									<div class="posts_count">Mensagens:<span><? echo count($post->Comments) ?></span></div>	
										</div>
										<div class="details_topic">			
									<div class="autor">Criado em <time datetime="<? echo $data;?>"><? echo $dataFormated;?></time> por <a href='<? echo $post->Autor->ViajanteUrl?>' ><? echo $post->Autor->FullName?></a></div>
									<p><? echo $post->Resumo; ?></p>
								</div>	
								<div class="last_comment">
									<? if($post->LastComment->Resumo != null)
									{ ?>
									<span class="last_comment_label">Último post em </span>
									<time datetime="<? echo $dataLastComment;?>" ><? echo $dataLastCommentFormated;?></time>
									<?if($post->LastComment->Autor->FullName != null)
									{		
											?><span class="last_comment_label">por</span> <div class="autor"> <a href='<? echo $post->LastComment->Autor->ViajanteUrl?>' ><? echo $post->LastComment->Autor->FullName?></a></div><?											
									}?>
	
									<span class="comment_content"><a href="<? echo $post->LastComment->Url; ?>"><? echo $post->LastComment->Resumo;?></a></span>
									<?}
									else
									{
										?><span class="last_comment_label">Sem posts até o momento </span><?
									}
									?>
								</div>						 
								<input type='hidden' class='itemId' value="<? echo $post->PostId; ?>"/>
							</li>	
						<?				
					}
				}
				else
				{
					echo '<span class="no_content">Sem tópicos publicados até o momento<span>';
				}			
			?>			
			
		<? 
			
		if($options["show_more"] == "yes" && $forumBO->HasMoreData)
		{						
			echo "<li class='loadmore'>";				
				widget::Get("load-more-content", array("content_type"=>"forum_topics","itens"=>3, 'parentId'=>$options["parentId"],"excluded_ids"=>$options["excluded_ids"]));
			echo "</li>";
				
		}
		if(!$forumBO->HasMoreData)
		{	
			echo "<li style='display:none;' class='no_more'></li>";
		}
						
	if($options["return"] != "onlyitens")
	{		echo "</ul>";
		if(is_array($topicos) && count($topicos)> 0)
		{
			echo "<div class='all_forum_categories'><a href='/forum'>Veja todas as categorias</a></div>";
		}
		if($options["show_register_link"] == "yes")
		{
			?>
				<div class="register_link_div">	
					<a class="register_open">Participe do fórum </a>
				</div>
			<?
		}
		?>	
	
	</section><!-- end relatos -->
<?}?>