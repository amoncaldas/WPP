<?
require_once(ABSPATH . '/FAMCore/BO/Search.php');
$search = new Search();
if( strlen($options["term"]) > 2)
{		
	$data = $search->GetData($options);
}

?>

<section class="search-itens">
	<ul>
	<?php 	
		if(is_array($data) && count($data)> 0)
		{		
			require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/ViajanteVO.php' );
			foreach($data as $content)
			{	
				if($content->blog_id  > 0)
				{					
					global $switched;
					switch_to_blog($content->blog_id); 					
				}
				if($content->content_type == 'viajante')
				{					
					$viajante = new ViajanteVO($content->ID);									
				}
				if($content->content_type == "blog_post")
				{
					$content->content_type = "blog";
				}
				if($content->content_type == "atualizacao")
				{
					$content->content_type = "status";
				}
											
				?>
				<li>
					<div class="result_item">
						<div class="item_type">
							<?						
								switch($content->content_type)
								{
									case 'comment':											
										echo "Comentário";																
										break;
									case 'forum':											
										echo "Fórum";																
										break;
									case 'viajante':											
										echo "Viajante";																
										break;
									case 'viagem':											
										echo "Viagem";																
										break;
									case 'relatos':											
										echo "Relato";																
										break;
									case 'albuns':											
										echo "Álbum";																
										break;
									case 'status':											
										echo "Status";																
										break;
									case 'blog':											
										echo "Blog";																
										break;
									case 'page':											
										echo "Página";																
										break;
								}											
							?>
						</div>
						<div class="item_title">
							<h2><a href="
								<?	
														
									switch($content->content_type)
									{
										case 'comment':											
											echo trim(get_comment_link($content->ID).'" >'.GetSubContent($content->content_title,50));														
											break;
										case 'viajante':																														
											echo trim($viajante->ViajanteUrl.'" >'.$content->content_title);	
											break;
										case 'viagem':																					
											echo trim($content->url.'/viagem" >'.$content->content_title);																
											break;
										case 'page':																															
											echo "/".sanitize_title($content->content_title). '" > '.$content->content_title;																										
											break;										
										default:
											$url = get_site_url(get_current_blog_id())."/".$content->content_type."/".sanitize_title($content->content_title)."/".$content->ID."/";
											echo trim($url.'" >'.$content->content_title);	
									} 
								?>
							</a></h2>
						</div>
						<div class="item_content">
							<? 
								require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Imagem.php');	
								
								switch($content->content_type)
								{
									case 'albuns':											
										echo "<div class='result_image'>";											
										PrintImages( $content->ID, NULL, 1,"thumb");
										echo "</div>";
										echo "<div class='result_text'><h3>".get_post_meta($content->ID, "descricao_album", true)."</h3></div>";								
										break;									
									case 'status':										
										$imagemVO = Imagem::GetImage( get_post_meta($content->ID, "_fam_upload_id_", true));
										if($imagemVO->ImageThumbSrc != null)
										{
											echo 
											"<div class='result_image'>
												<img src='".$imagemVO->ImageThumbSrc."' />
											</div>";
										}																											
										echo "<div class='result_text'><h3>".GetSubContent(get_post_meta($content->ID, "conteudo", true),300)."</h3></div>";
										break;
									case 'forum':	
										$atualizacao = get_post($content->ID);
										echo "<div class='result_text'><h3>".GetSubContent($atualizacao->post_excerpt,300)."</h3></div>";
										break;
									case 'relatos':																	
										$imagemVO = Imagem::GetImage( get_post_meta($content->ID, "_fam_upload_id_", true));
										if($imagemVO->ImageThumbSrc != null)
										{
											echo 
											"<div class='result_image'>
												<img src='".$imagemVO->ImageThumbSrc."' />
											</div>";
										}
										$relato = get_post($content->ID);
										$content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $relato->post_content);				
										$relato->post_content = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);										
										echo "<div class='result_text'><h3>".GetSubContent($relato->post_content,300)."</h3></div>";
										break;
									case 'viagem':																				
										if($content->image->OriginalImageVO->ImageThumbSrc != null)
										{
											echo 
											"<div class='result_image'>
												<img src='".$content->image->OriginalImageVO->ImageThumbSrc."' />
											</div>";
										}	
										widget::Get('bandeiras', array('locations'=>$content->roteiro, 'width'=> 30,'limit_itens' => 15));
										echo "<div class='result_text'><h3>".GetSubContent($content->content_content,500)."</h3></div>";
										break;
									case 'viajante':									
										if($viajante->UserImage->ImageThumbSrc != null)
										{
											echo 
											"<div class='result_image'>
												<img width='120px' height='70px' src='".$viajante->UserImage->ImageThumbSrc."' />
											</div>";
										}	
										$bio = 	GetSubContent($viajante->UserProfile,300);
										if($bio != null && strlen($bio) > 3)
										{
											echo "<div class='result_text'><h3>".GetSubContent($viajante->UserProfile,300)."</h3></div>";
										}
										else
										{
											echo "<div class='result_text'><h3>Usuário cadastrado no site</h3></div>";
										}
										break;
									case 'blog':
										$imagemVO = Imagem::GetImage( get_post_meta($content->ID, "_fam_upload_id_", true));
										if($imagemVO->ImageThumbSrc != null)
										{
											echo 
											"<div class='result_image'>
												<img src='".$imagemVO->ImageThumbSrc."' />
											</div>";
										}
										$blog = get_post($content->ID);
										$content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $blog->post_content);				
										$blog->post_content = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);										
										echo "<div class='result_text'><h3>".GetSubContent($blog->post_content,300)."</h3></div>";
										break;									
									default:
										echo "<div class='result_text'><h3>".GetSubContent($content->content_content, 300)."</h3></div>";										
										break;									
									
								}
							?>
							<div class="search_result_view_more" >
								<? //echo "###".var_dump($content);?>
								<a  class=" hand_font text_link_color" href="
								<?						
									switch($content->content_type)
									{
										case 'comment':											
											echo trim(get_comment_link($content->ID).'"> Ver mais ->');														
											break;
										case 'viajante':																														
											echo trim($viajante->ViajanteUrl.'" > Ver mais ->');	
											break;
										case 'viagem':																					
											echo trim($content->url.'/viagem" > Ver mais ->');																
											break;	
										case 'page':
											echo "/".sanitize_title($content->content_title). '" > Ver mais -> ';															
											break;									
										default:
											$url = get_site_url(get_current_blog_id())."/".$content->content_type."/".sanitize_title($content->content_title)."/".$content->ID."/";
											echo trim($url.'" > Ver mais ->');	
									} 
								?>
							</a></div>
						</div>
					</div>
					<input type='hidden' class='itemId' value="<? echo $content->ID.";".$content->content_type; ?>"/>
				</li>
				<?	
				if($content->blog_id  > 0)
				{					
					restore_current_blog();
				}			
			}
			
			if($search->HasMoreData && $options["show_more"] && $_POST["is_ajax"] == null)
			{										
				echo "<li class='loadmore'>";							
					widget::Get("load-more-content", array("itens"=> 4,"content_type"=>"resultados_busca",'term'=>$options["term"], 'excluded_ids'=>$options["excluded_ids_search"]));					
				echo "</li>";				
			}
			if(!$search->HasMoreData || count($data) == 0 )
			{			
				echo "<li style='display:none;' class='no_more'></li>";		
			}
		}									
		else 
		{
			global $noSearchResult;
			$noSearchResult = "yes";
			
			if( $_POST["is_ajax"] == true)
			{
				echo "<li style='display:none;' class='no_more'></li>";	
			}
			else
			{
				if( strlen($options["term"]) > 2)
				{		
					?>
						<div class="not_result">			
							<p><?php echo "Desculpe, nenhum resultado para a sua busca.Tente usar outros termos."; ?></p>			
						</div>	
					<?
				}
				else
				{
					?>
						<div class="not_result">			
							<p><?php echo "O termo para a busca deve ter no mínimo 3 caracteres. Nenhum resultado para esta busca"; ?></p>			
						</div>	
					<?
				}			
			}
		} ?>
	</ul>
</section>

