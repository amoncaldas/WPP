<? if(!is_mobile_admin()) { widget::Get("socialmedia");}

	if(!is_mobile_admin())	
	{
		if(!in_array($Meta->Screen,array('single-albuns','single-atualizacao','single-blog','single-forum','single-relatos')))
		{			
			global $Meta;
			widget::Get("share",array('hideCommentBox'=>$Meta->Screen == "index",'show_share_bar'=>"yes"));			
		}
	}
?>

<?
	
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }
	echo "<a id='main_menu' name='menu'></a><nav>";
	
	if(get_current_blog_id() == 1 || "blog_post" == get_post_type())
	{	
		?>		
			<ul class="disclosure table group menu_home">				
				<li class="page_item page-item-32"><a href="/viagens/"><img src="/wp-content/themes/images/icons/icon-viagem.png">Viagens</a></li>	
				<li class="page_item page-item-286"><a href="/blog/"><img src="/wp-content/themes/images/icons/icon-blog.png">Blog</a></li>
				<li class="page_item page-item-32"><a href="/relatos/"><img src="/wp-content/themes/images/icons/icon-relatos.png">Relatos</a></li>	
				<li class="page_item page-item-32"><a href="/albuns/"><img src="/wp-content/themes/images/icons/icon-albuns.png">Álbuns</a></li>	
				<li class="page_item page-item-32"><a href="/viajantes/"><img src="/wp-content/themes/images/icons/icon-viajantes.png">Viajantes</a></li>		
				<li class="page_item page-item-30"><a href="/forum/"><img src="/wp-content/themes/images/icons/icon-forum.png">Fórum</a></li>
				
				<?
					if(is_user_logged_in())
					{	
						?>
							<li class="page_item page-item-30"><a href="<?php echo wp_logout_url( home_url());?>"><img src="/wp-content/themes/images/icons/icon-logout.png">Sair</a></li>
						<?
					}
				?>						
			</ul>
		
		<?
	}
	else
	{	
		if( is_mobile_admin())
		{
			?>		
			<ul class="disclosure table group menu_home">
				<li class="page_item page-item-30"><a href="?post_type=atualizacao&action=new"><img src="/wp-content/themes/images/icons/icon-status.png">Novo status</a></li>								
				<li class="page_item page-item-30"><a href="?post_type=atualizacao"><img src="/wp-content/themes/images/icons/icon-status.png">Listar status</a></li>
				<li class="page_item page-item-30"><a href="?post_type=albuns&action=new"><img src="/wp-content/themes/images/icons/icon-albuns.png">Novo álbum</a></li>								
				<li class="page_item page-item-30"><a href="?post_type=albuns"><img src="/wp-content/themes/images/icons/icon-albuns.png">Listar álbuns</a></li>
				<li class="page_item page-item-30"><a href="<?php echo wp_logout_url( home_url());?>"><img src="/wp-content/themes/images/icons/icon-logout.png">Sair</a></li>				
			</ul>		
			<?	
		}
		else
		{	
			?>
			<ul class="disclosure table group menu_home">					
				<li class="page_item page-item-32"><a href="<?php bloginfo('url')?>/viagem/"><img src="/wp-content/themes/images/icons/icon-viagem.png">A viagem</a></li>				
				<li class="page_item page-item-30"><a href="<?php bloginfo('url')?>/status/"><img src="/wp-content/themes/images/icons/icon-status.png">Status</a></li>
				<li class="page_item page-item-286"><a href="<?php bloginfo('url')?>/albuns/"><img src="/wp-content/themes/images/icons/icon-albuns.png">Albuns</a></li>
				<li class="page_item page-item-32"><a href="<?php bloginfo('url')?>/viajantes/"><img src="/wp-content/themes/images/icons/icon-viajantes.png">Viajantes</a></li>					
				<li class="page_item page-item-32"><a href="<?php bloginfo('url')?>/relatos/"><img src="/wp-content/themes/images/icons/icon-relatos.png">Relatos</a></li>
				<li class="page_item page-item-32"><a href="/viagens/"><img src="/wp-content/themes/images/icons/icon-viagem.png">Outras Viagens</a></li>
				<li class="page_item page-item-32"><a href="/blog"><img src="/wp-content/themes/images/icons/icon-blog.png">Blog</a></li>
				<li class="page_item page-item-32"><a href="/"><img src="/wp-content/themes/skeens/adventure/images/logo.png">Inicial</a></li>	
				<?
					if(is_user_logged_in())
					{	
						?>
							<li class="page_item page-item-30"><a href="<?php echo wp_logout_url( home_url());?>"><img src="/wp-content/themes/images/icons/icon-logout.png">Sair</a></li>
						<?
					}
				?>			
				
			</ul>
			<?	
		}	
		
	}
	?>	
	<a class="go_to_top" href="#topo">↑ Conteúdo</a>
	</nav>
	

<div class="clear"></div>
<div class="spacer_bottom"></div>
</div>
<? cfct_template_file('footer', 'bottom');?>