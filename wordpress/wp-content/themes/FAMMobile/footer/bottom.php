<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }


?>
</div>
</div>
</div>
<footer id="footer">
	<ul class="footer_links">
		<li>
			<?php
			if (function_exists('cfmobi_mobile_exit')) {
				cfmobi_mobile_exit();
			}
			?>
		</li>
		<li class="separator">|</li>
		<li>
			<? if(is_mobile_admin())
			{ 				
				echo '<a href="'.home_url().'">Ver site</a>';
			}
					
			else  
			{				
				echo '<a href="'.admin_url().'">Admin</a>';
			}
			?>
			
		</li>
		<li class="separator">|</li>
		<li>
			<a href="/contato">Contato</a>
		</li>
	</ul>
	<hr />
	<div class="copyright">
		Fazendo as Malas ® - Copyrigth <? echo date('Y') ?>
	</div>

	<div class="clear"></div>
</footer>
<?php wp_footer(); ?>
	<script type='text/javascript'>
	/* <![CDATA[ */
	var FB_WP=FB_WP||{};FB_WP.queue={_methods:[],flushed:false,add:function(fn){FB_WP.queue.flushed?fn():FB_WP.queue._methods.push(fn)},flush:function(){for(var fn;fn=FB_WP.queue._methods.shift();){fn()}FB_WP.queue.flushed=true}};window.fbAsyncInit=function(){FB.init({"channelUrl":"http:\/\/fazendoasmalas.com\/channel.php","xfbml":true,"appId":"585138868164342"});if(FB_WP && FB_WP.queue && FB_WP.queue.flush){FB_WP.queue.flush()}}
	/* ]]> */
	</script>
	<div id="fb-root"></div><script type="text/javascript">(function(d){var id="facebook-jssdk";if(!d.getElementById(id)){var js=d.createElement("script"),ref=d.getElementsByTagName("script")[0];js.id=id,js.async=true,js.src="http:\/\/connect.facebook.net\/pt_BR\/all.js",ref.parentNode.insertBefore(js,ref)}})(document)</script>	
	<!--- end mising scripts in footer --->
	<script type="text/javascript" src="/wp-content/themes/js/jquery-1.8.1.min.js" media="screen"></script>
	<script type="text/javascript" src="/wp-content/themes/js/fancybox/jquery.fancybox.js?v=2.1.3" media="screen"></script>	
	<script type="text/javascript" src="/wp-content/themes/js/jalert/jquery.alerts.js"></script>
	<script type='text/javascript' src='/wp-content/themes/js/action.js' ></script>
	<script type="text/javascript" src=" /wp-content/themes/js/oms.js" ></script>
	<script type="text/javascript" src=" /wp-content/themes/js/show-roteiro.js" ></script>
	<script type="text/javascript" src="/wp-content/themes/js/show-location.js" ></script>
	<script type="text/javascript" src="/wp-content/themes/FAMMobile/js/mobile.js" ></script>

<?
	if (is_mobile_admin())
	{		
		fam_plupload_loader();
		
		echo '<script type="text/javascript" src="/wp-content/themes/js/uploadField.js" ></script>';
		echo '<script type="text/javascript" src="/wp-content/themes/js/famAdminFunctions.js" ></script>';
		echo '<script type="text/javascript" src="/wp-content/themes/FAM/CustomContent/js/atualizacao.js" ></script>';
		echo '<script type="text/javascript" src="/wp-content/themes/FAM/CustomContent/js/album.js" ></script>';
				
	} 
?>	

<div style="display:none">
	<? widget::Get("fotos", array('itens'=>'1','return'=>'onlyitens')) ?>
</div>	

</body>
</html>