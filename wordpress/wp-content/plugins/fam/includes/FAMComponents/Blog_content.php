<?
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/BlogPost.php");
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viajante.php");
$blogBO = new BlogPost();
$posts = $blogBO->GetPosts($options); 


if($options["return"] != "onlyitens")
{?>
	<section class="blog" style="float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important;'; ?> margin-left:<?echo $options["margin_left"].' !important;'?>;width:<?echo $options["width"];?>!important;">
		<?
			global $is_dica;
			if(is_content_archive('blog')) echo '<h1 class="single_type_label blog_icon topic_label_icon topic_font_color hand_font">'; else echo "<h2>";
			if($options['title'] != null)
			{				
				echo $options['title'];
			}
			else
			{			
				if($is_dica == true)
				{
					echo "Dicas de viagem";					
				}
				else
				{
					echo "Blog";
				}
			}
			if(is_content_archive('blog')) echo '</h1>'; else echo "</h2>";
		?>						
		<ul>
			<? }	
				if(is_array($posts) && count($posts)> 0)
				{		
					foreach($posts as $post) 
					{						
						$data = ($post->DataPost != null)? $post->DataPost: $post->DataPublicacao;
						$data = strtotime(str_replace('/', '-', $data));
						$data = utf8_encode(strftime("%d %b %Y", $data));
						//$intermediateImage  = $post->MidiaPrincipal->GetIntermediateImageSrc();						
						//$mainImage = ($intermediateImage == null)? $post->MidiaPrincipal->ImageLargeSrc : $intermediateImage;
						?>
							<li <? if($options["show_large_image"] == "yes") echo "class='largeImage'"; else echo "class='smallImg'" ?> >								 														
							<div  class="autorInfo">
								<img  <? if($options["show_large_image"] == "yes") {echo "style='width:100%'"; } ?>   alt="<? echo $post->Titulo; ?>" class="foto_viajante_relato" src="<? if($options["show_large_image"] == "yes") { echo $post->MidiaPrincipal->ImageLargeSrc;} else{ echo $post->MidiaPrincipal->ImageGaleryThumbSrc;} ?>"/>									
								</div>
								<? if($options["show_meta_data_label"] == "yes") { ?>									
									<div class="meta_container author">
										<div class="content_meta">
											<span class="meta_author">
												<span class="meta_icon">i</span>
												<span class="meta_text">
													<a title="Veja o perfil de <? echo $post->Autor->FullName; ?>" href="<? echo $post->Autor->ViajanteUrl?>"><? echo $post->Autor->FullName; ?></a>
												</span>
											</span>
											<span class="meta_date">
												<span class="meta_separator">s</span>
												<span class="meta_text">
													<time datetime="<? echo $post->DataPublicacao;?>"><? echo $data;?></time>
												</span>
											</span>
										</div>
								    </div>
								<?}
								else{?>
									<div class="author_data">
										<div class="autor"><a href='<? echo $post->Autor->ViajanteUrl?>' ><? echo $post->Autor->FullName?></a></div>
										<time datetime="<? echo $post->DataPublicacao;?>"><? echo $data;?></time>
									</div>
								<?}?>
								
								<?
									if($options['show_share'] == "yes")
									{
										echo "<div style='width:100%;float:left'>";
										widget::Get("share", array("customUrl" =>  $post->PostUrl,'send'=>'true','show_share_bar' => 'yes','hideCommentBox'=>true));
										echo "</div>";
									}
								?>
								<h3><a href="<? echo $post->PostUrl ?>"><? echo $post->Titulo; ?></a></h3>
								<p><? echo $post->GetSubContent($options["content_lenght"]); ?></p>
								<a href="<? echo $post->PostUrl ?>" class="ver">Continue lendo -></a>
								
								<input type='hidden' class='itemId' value="<? echo $post->PostId; ?>"/>
								
							</li>	
						<?				
					}
				}
				else
				{
					echo '<span class="no_content">Sem posts publicados até o momento<span>';
				}			
			?>			
			
		<? 
			
		if($options["show_more"] == "yes" && $blogBO->HasMoreData)
		{						
			echo "<li class='loadmore'>";				
				widget::Get("load-more-content", array("content_type"=>"blog_posts","itens"=>3, "content_lenght"=>$options["content_lenght"], "excluded_ids"=>$options["excluded_ids"],'show_large_image'=>$options["show_large_image"],'show_meta_data_label'=>$options['show_meta_data_label']));
			echo "</li>";
				
		}
		if(!$blogBO->HasMoreData)
		{	
			echo "<li style='display:none;' class='no_more'></li>";
		}
		
				
if($options["return"] != "onlyitens")
{
?>
	</ul>
</section><!-- end relatos -->
<?}?>