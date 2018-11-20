<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }
global $Meta;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $Meta->Title; ?></title>
	<? echo $Meta->nextPage;?>	
	<? echo $Meta->prevPage;?>
	<link rel="stylesheet" id="skeen_style" type="text/css" href="<? echo $Meta->SkeenStyleSrc;?>" />
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>	
	<link rel="stylesheet" href="/wp-content/themes/FAMMobile/style.css" type="text/css" media="screen" charset="utf-8" />
   
	<style type="text/css">
		@import '/wp-content/themes/FAMMobile/css/advanced.css'; ?>);
	</style>	
	
	<meta charset="<?php bloginfo( 'charset' ); ?>" />	
	<meta name="google-translate-customization" content="19ce010534a72d5c-e0dfc23342abd02a-g510b8b6d99334391-18"></meta>
	<meta name="msvalidate.01" content="46CF6C951FE1C64CF96BB19615D9F1FF" />
	<meta name="p:domain_verify" content="5ad2fc0a3fdc5e989ad9529a02c32247"/>
	<meta name="keywords" content="<? echo $Meta->KeyWords; ?>" />
		
	<meta property="og:title" content="<? echo $Meta->Title; ?>" />	
	<?echo $Meta->MediaOGHtml;?>	
	<meta property="og:description" content="<? echo $Meta->DescriptionText; ?>" />
	<meta name="description" content="<? echo $Meta->DescriptionText; ?>" />	
	<?echo $Meta->LatOGHtml;?>
	<?echo $Meta->LongOGHtml;?>
	<? echo $Meta->AutorFacebookUrlOGHtml;?>
	<? echo $Meta->ArticlePublisher;?>	
	<meta property="og:type" content="<? echo $Meta->OGType;?>">
	<meta property="fb:admins" content="593941235"/>
		
	
	<?if(strpos($_SERVER["SERVER_NAME"],"teste.") === 0){ echo '<meta name="robots" content="noindex, nofollow">';} ?>
		
	<meta name="author" content="<? echo $Meta->Autor; ?>"/>
	<meta name="copyright" content="Fazendo as Malas"/>
	
	<meta name="description" content="<? echo $Meta->DescriptionText; ?>" />
	<META http-equiv="Cache-Control" content="private, max-age=600, pre-check=600">
	<META http-equiv="Expires" content="<?php echo date(DATE_RFC822,strtotime("+10 minutes")); ?>">	
	<link rel="stylesheet" href="/wp-content/themes/js/fancybox/jquery.fancybox.css?v=2.1.2" type="text/css" media="screen" />
	<link rel="stylesheet" href="/wp-content/themes/js/jalert/jquery.alerts.css" type="text/css" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel='image_src'  type="image/jpeg" href="<?  echo $Meta->ImgSrc;?>" />
	<link rel="canonical" href="<? echo $Meta->Cannonical;?>" />
	
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<link rel="apple-touch-icon-precomposed" href="/apple-touch-icon-precomposed.png">	
	<link rel="apple-touch-icon-precomposed apple-touch-icon" href="/apple-touch-icon-precomposed.png">
	<meta name="application-name" content="Fazendo as Malas - Descobrindo o Mundo"/>
	<meta name="msapplication-tooltip" content="Experiências de viagens, aventuras e dicas"/>	
	<meta name="msapplication-navbutton-color" content="#678713"/>
	
				
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places&amp;language=pt-BR" async defer></script>	
	<?	if (is_mobile_admin()){
			echo '<link rel="stylesheet" href="'.get_bloginfo('template_url').'/m-admin/mobile_admin.css" type="text/css" />';
			echo '<link rel="stylesheet" href="/wp-content/themes/css/uploadField.css" type="text/css" />';			
		}
	?>		
	<script type="text/javascript">
	<!--
	<?php

	is_page() ? $page = 'true' : $page = 'false';
	echo '	CFMOBI_IS_PAGE = '.$page.';';
	echo "	CFMOBI_PAGES_TAB = '".str_replace("'", "\'", __('Páginas', 'carrington-mobile'))."';";
	echo "	CFMOBI_POSTS_TAB = '".str_replace("'", "\'", __('Posts recentes', 'carrington-mobile'))."';";

	global $cfmobi_touch_browsers;
	if (!isset($cfmobi_touch_browsers) || !is_array($cfmobi_touch_browsers)) {
		$cfmobi_touch_browsers = array(
			'iPhone',
			'iPod',
			'Android',
			'BlackBerry9530',
			'LG-TU915 Obigo', // LG touch browser
			'LGE VX',
			'webOS', // Palm Pre, etc.
		);
	}
	if (count($cfmobi_touch_browsers)) {
		$touch = array();
		foreach ($cfmobi_touch_browsers as $browser) {
			$touch[] = str_replace('"', '\"', trim($browser));
		}

	?>
	var CFMOBI_TOUCH = ["<?php echo implode('","', $touch); ?>"];
	for (var i = 0; i < CFMOBI_TOUCH.length; i++) {
		if (navigator.userAgent.indexOf(CFMOBI_TOUCH[i]) != -1) {
			document.write('<?php echo str_replace('/', '\/', '<link rel="stylesheet" href="'.trailingslashit(get_bloginfo('template_url')).'css/touch.css" type="text/css" media="screen" charset="utf-8" />'); ?>');
			break;
		}
	}
	<?php

	}

	?> 
	document.write('<?php

	ob_start();
	wp_print_scripts();
	$wp_scripts = ob_get_contents();
	ob_end_clean();

	echo trim(str_replace(
		array("'", "\n", '/'), 
		array("\'", '', '\/'),
		$wp_scripts
	));

?>');
//-->

</script>
</head>
<body<?php if(is_single() || is_page()) {echo '';} else { echo ' id="is-list"';} ?>>
	<span style="display:none;" id="is_mobile">true</span>
	<div id="wraper">
		<div class="top-and-content">
			<header class="top">	
				<? $subtitle = "<span style='display:none'>". ((get_current_blog_id() == 1)? " - Descobrindo o Mundo": " | Fazendo as Malas"). "</span>";?>
				<h1 class='site_title'><a class="link_home_logo hand_font" title="Ir para página inicial" rel="home" href="<? bloginfo('url');?>"><? bloginfo('name');?></a><? echo $subtitle?></h1>		
				
					<a class="main_menu_anchor" name="topo" href="#menu">
						<div class="burger">
						  <b></b>
						  <b></b>
						  <b></b>
						</div>			
					</a>
					<a class="search_open" name="search_open" href="javascript:void(0);">
						<label for="s" class="glass-container">
							<div class="glass">
								<div class="iglass css3pie">
								</div>
							</div>
						</label>		
					</a>
					<div class="search_close" name="search_close">
						<div class="glass-container">
							<div class="glassAxis1">							
							</div>
							<div class="glassAxis2">							
							</div>
						</div>		
					</div>
					<? if(!is_mobile_admin()) {?>
						<div class="search_container">
							<? cfct_form('search');?>
						</div>
					<?}?>			
			</header>			
			
			
			

