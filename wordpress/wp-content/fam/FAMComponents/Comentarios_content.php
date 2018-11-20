<?php
require_once(ABSPATH."/FAMCore/BO/Comentario.php");
$comentarioBO  = new Comentario();
$comentarios = $comentarioBO->GetData($options);	

if($options["return"] != "onlyitens")
{
	?>
	<section class="comentarios" style="float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;">
		<h2><? if($options["title"] == null){echo "Comentários";} else{echo $options["title"];} ?></h2>				
		<ul>
			<? }
			if(is_array($comentarios) && count($comentarios) > 0)
			{
				foreach($comentarios as $comentario)
				{
					$dataLastComment = $comentario->comment_date;									
					$dataLastComment = strtotime(str_replace('/', '-', $dataLastComment));
					$dataLastComment = utf8_encode(strftime("%d %b %Y", $dataLastComment));
								
					?>
						<li>
							<a name="comment-<? echo $comentario->comment_ID; ?>">
							<div class="comentario">
								<div class="autor">
									<img class="foto_usuario" src="<? echo $comentario->Autor->UserImage->ImageGaleryThumbSrc ?>"/>									
								</div>
								<div class="info">
									<a class="autor_url" href='<? echo $comentario->Autor->ViajanteUrl?>'><? echo $comentario->Autor->FullName?></a>
									<div class="statistics_user">
										<span>Tópicos:</span><div class="counter"><?echo $comentario->Autor->CountForumTopics;?></div><span>Mensagens:</span><div class="counter"><?echo $comentario->Autor->CountForumMessages;?></div>
									</div>
									<div class="date"> 
										em <time datetime="<? echo $comentario->comment_date;?>"><? echo $dataLastComment;?></time>
									</div>
									
								</div>															
								<div class="comment_content"><? echo $comentario->comment_content;?></div>											
							</div>
							<input type='hidden' class='itemId' value="<? echo $comentario->comment_ID; ?>"/>
						</li>
					<?
				}
			}
			else
			{		
				if($options["show_message_on_empty"] == "yes")
				{		
					echo '<span class="no_content">Sem mensagens publicadas até o momento. Seja o primeiro!<span>';
				}				
			}
			
			if($options["show_more"] == "yes" && $comentarioBO->HasMoreData)
			{						
				echo "<li class='loadmore'>";				
				widget::Get("load-more-content", array("content_type"=>"comentarios","itens"=>3,'parentId'=>$options["parentId"], "excluded_ids"=>$options["excluded_ids"]));
				echo "</li>";
				
			}
			if(!$comentarioBO->HasMoreData)
			{	
				echo "<li style='display:none;' class='no_more'></li>";
			}



if($options["return"] != "onlyitens")
{
	?>
	</ul>
</section>	
<? }