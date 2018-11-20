<?
$currentUrl = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
require_once(ABSPATH."/FAMCore/BO/Atualizacao.php");
$atualizacaoBO  = new Atualizacao();
$atualizacoes = $atualizacaoBO->GetData($options);


if($options["return"] != 'onlyitens' )
{
wp_reset_query();
$hide = (is_fam_mobile() && (!is_array($atualizacoes) || count($atualizacoes) == 0) && is_home())? " display:none;" : "";
?>
<section class="atualizacao"  style="<? echo $hide;?> float:<?echo $options["float"].' !important';?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;" >
	<?
		if(is_post_type_archive('blog_post')) echo '<h1 class="single_type_label blog_icon topic_label_icon topic_font_color hand_font">'; else echo "<h2>";
		if($options["title"] == null)
		{
			echo "Status";
		}
		else
		{
			echo $options["title"];
		}
		if(is_post_type_archive('blog_post')) echo '</h1>'; else echo "<h2>";
	?>
	
	<ul>
		<? 
		}
		if(is_array($atualizacoes) && count($atualizacoes)> 0)
		{			
			foreach($atualizacoes as $atualizacao) 
			{							
			?>
				<li class="item_content">
					<a name="<? echo $atualizacao->AtualizacaoHash;?>"></a>
					<div class="autorInfo">
						<img class="foto_viajante_relato" <? if($options["foto_width"] != null) echo "width='".$options["foto_width"]."'"; ?> src="<? echo $atualizacao->Autor->UserImage->ImageGaleryThumbSrc ?>"/>						
					</div>	
					<div class="author_data">
						<div class="autor"><a href='<? echo $atualizacao->Autor->ViajanteUrl?>' ><? echo $atualizacao->Autor->FirstName.' '.$atualizacao->Autor->LastName; ?></a></div>
						<?
							$data = strtotime(str_replace('/', '-', $atualizacao->DataPublicacao));
							$data = utf8_encode(strftime("%d %b %Y %T", $data));
						?>
						<time datetime="<? echo $atualizacao->DataPublicacao;?>">Em <? echo $data;?></time>
						<? if($atualizacao->Location->Local != null && $options["show_location"] == true)
						{							
							?><h4 class="local-relato"><span class="location_ico_small"></span> <span><? echo $atualizacao->Location->GetLocalSubString(25); ?></span></h4> <?	
						}
						?>	
					</div>
					
					<div class="clear"></div>
					<h3><span class="titulo"><? echo $atualizacao->Titulo; ?></span><br/></h3>
					<? 
					if($options["link_on_content"] == "yes")
					{
						?>
						<a href="<? echo $atualizacao->AtualizacaoUrl ?>" title="veja o conteúdo completo" >
							<p>
								<? if($options["content_lenght"] != null){ echo $atualizacao->GetSubContent($options["content_lenght"]);} else{ echo $atualizacao->Conteudo;} ?>
							</p>
						</a><?
					} 
					else
					{
					?>			
						<p>
							<? if($options["content_lenght"] != null){ echo $atualizacao->GetSubContent($options["content_lenght"]);} else{ echo $atualizacao->Conteudo;} ?>
						</p>
					<?
					}									
					if( $options["show_media"] == "yes" && $atualizacao->MidiaPrincipal != null)
					{
						if(is_array($atualizacao->MidiasAnexadas) && count($atualizacao->MidiasAnexadas) > 0)	
						{?>
							<div class="medias-status">								
								<ul>
									<?															
									foreach($atualizacao->MidiasAnexadas as $media)
									{ 																	
										if($media->YoutubeCode != null)
										{			
											if(is_fam_mobile())
											{
												$mediaSrc = "<img style='width:120px;height:70px;'  title='".$media->Titulo."' src='".$media->YoutubeBaseThumbUrl."1.jpg' alt='' />";
											}	
											else
											{
												$mediaSrc = "<img style='width:190px;height:140px;'  title='".$media->Titulo."' src='".$media->YoutubeBaseThumbUrl."hqdefault.jpg' alt='' />";
											}								
												
											$mediaLink =  $media->MainUrl."?modestbranding=1&rel=0";
											$fancyIframe = " fancybox.iframe";
										}
										else
										{		
											if(is_fam_mobile())
											{									
												$mediaSrc = "<img src='".$media->ImageThumbSrc."' alt='' />";
											}
											else
											{
												$mediaSrc = "<img src='".$media->ImageGaleryThumbSrc."' alt='' />";
											}
											$mediaLink = $media->ImageLargeSrc;											
										}																							
							
									?>
										<li>
											<div class="post-foto">
												<a class="fancybox<? echo $fancyIframe;?>" href="<?echo $mediaLink;?>">
													<?echo $mediaSrc;?>
												</a>
											</div>
										</li>
										<?																	
									}
									?>
								</ul>
							</div><!-- end medias-status -->
							<?
						}			
							
					}
					
				    
					if($atualizacao->Location->Latitude != null && strlen($atualizacao->Location->Latitude) > 1 && $options["show_map"] == "yes")	
					{	
						?>
							<div class="locationInfo">
								<? widget::Get("content-location", array("location" => $atualizacao->Location, "locationImage" => null,'enable_controls'=>'false','image_map'=>'yes'));?>	
							</div>	
						<?	
					}	
					
					if(($options["content_lenght"] != null && (strlen($atualizacao->Conteudo) > $options["content_lenght"]) && $options["show_admin_controls"] != 'yes') || $options["show_read_full"] == "yes")
					{
					?>
						<a href="<? echo $atualizacao->AtualizacaoUrl ?>" class="ver">post completo -></a>					
						<?
					}
					if($options["show_admin_controls"] == "yes" && is_user_logged_in() && current_user_can('edit_atualizacao'))
					{
					?>
						<a href="?post_type=atualizacao&action=edit&p=<? echo $atualizacao->AtualizacaoId ?>" class="ver">Editar</a><span class="separator"> |</span> 	<a onclick="return confirm('Tem certeza que deseja esxcluir o status [<? echo $atualizacao->Titulo; ?>] com ID <? echo $atualizacao->AtualizacaoId; ?>?');" href="?post_type=atualizacao&action=delete&p=<? echo $atualizacao->AtualizacaoId ?>" class="ver">Excluir</a>				
					<?
					}
					
					if($options["show_comment"] == "yes")
					{															
						widget::Get("share", array("customUrl" => $atualizacao->AtualizacaoUrl , "comment_box_width" => '600','send'=>'true','hideShareBtns' => false,'hideCommentBox'=>is_fam_mobile()));							 
					}
					?>
					<input type='hidden' class='itemId' value="<? echo $atualizacao->AtualizacaoId; ?>"/>
				</li>	
				<?					
				
			}
		}
		else
		{			
			if($options["return"] != 'onlyitens' && $options["hide_message_on_empty"] == null )
			{
				?><span class="no_content">Sem atualização de status até o momento<span><?
			}
		}
		if($options["show_more"] == "yes" && $atualizacaoBO->HasMoreData)
		{							
			echo "<li class='loadmore'>";	
			widget::Get("load-more-content", array("content_type"=>"atualizacoes","itens"=>4, 'show_admin_controls'=>$options["show_admin_controls"],'show_location'=> $options["show_location"],'show_map'=>$options["show_map"],'userId'=> $options["authorId"],'show_comment'=>  $options["show_comment"],'show_media'=> $options["show_media"], 'foto_width'=>$options["foto_width"],'link_on_content'=> $options["link_on_content"],"content_lenght"=>1000, "excluded_ids"=>$options["excluded_ids"],'show_read_full'=>$options["show_read_full"]));
			echo "</li>";			
		}				
		if(!$atualizacaoBO->HasMoreData && $options["show_more"] == "yes")
		{	
			echo "<li style='display:none;' class='no_more'></li>";
		}
		if($options["return"] != 'onlyitens' )
		{
		?>
	</ul>				
</section><!-- end atualizacoes -->
<?}	

