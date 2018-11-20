<? global $Meta;?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" xmlns:og="http://ogp.me/ns#" dir="ltr" <?php language_attributes(); ?> >
<head>
	<title><?php echo $Meta->Title; ?></title>
	<? echo $Meta->nextPage;?>	
	<? echo $Meta->prevPage;?>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />	
	<meta name="viewport" content="width=device-width" />
	<meta name="google-translate-customization" content="44513b70a047488f-e0dfc23342abd02a-g510b8b6d99334391-12" />
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
	<meta property="fb:app_id" content="585138868164342" />
    <meta property="using-new-vps" content="true" />
	
		
	<?if(strpos($_SERVER["SERVER_NAME"],"teste.") === 0 || strpos($_SERVER["SERVER_NAME"],"dev.") === 0){ echo '<meta name="robots" content="noindex, nofollow">';} ?>
		
	<meta name="author" content="<? echo $Meta->Autor; ?>"/>
	<meta name="copyright" content="Fazendo as Malas"/>
	
	
	<META http-equiv="Cache-Control" content="private, max-age=600, pre-check=600">
	<META http-equiv="Expires" content="<?php echo date(DATE_RFC822,strtotime("+10 minutes")); ?>">
	
		
	<link rel="stylesheet" id="skeen_style" type="text/css" href="<? echo $Meta->SkeenStyleSrc;?>" />
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/FAM/css/style.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/wp-content/themes/css/nivo-slider.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/wp-content/themes/js/nivo_themes/default/default.css" />
	<link rel="stylesheet" href="/wp-content/themes/js/fancybox/jquery.fancybox.css?v=2.1.2" type="text/css" media="screen" />
	<link rel="stylesheet" href="/wp-content/themes/js/jalert/jquery.alerts.css" type="text/css" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel='image_src'  type="image/jpeg" href="<?  echo $Meta->ImgSrc;?>" />
	<link rel="canonical" id="page_canonical" href="<? echo $Meta->Cannonical;?>" />
	<link rel="alternate" href="<? echo get_site_url(1)?>/feed/" type="application/rss+xml" title="RSS Feed Fazendo as Malas" />	
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/js/fancybox/helpers/jquery.fancybox-thumbs.css" />
			
			
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<link rel="apple-touch-icon-precomposed" href="/apple-touch-icon-precomposed.png">	
	<link rel="apple-touch-icon-precomposed apple-touch-icon" href="/apple-touch-icon-precomposed.png">
	<meta name="application-name" content="Fazendo as Malas - Descobrindo o Mundo"/>
	<meta name="msapplication-tooltip" content="Experiências de viagens, aventuras e dicas"/>	
	<meta name="msapplication-navbutton-color" content="#678713"/>
	
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language=pt-BR" async defer></script>	
	<!--[if lt IE 9]> <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script><![endif]-->
	
	<?php wp_head();?>	
</head>

<body class="pc" style="<? echo $Meta->BodyStyle;?>" >

<span style="display:none;" id="current_blog_id"><? echo get_current_blog_id(); ?></span>
<nav id="menu_scroll" style="display:none">				
	<div class="inner" >
		<ul>				
			<li class="home"><a title="Página incial Fazendo as Malas" href="<?php bloginfo('url')?>/" ><img alt="logo FAM" src="<? echo $Meta->SkeenFolder; ?>images/logo.png"/> </a> </li>
			<li class="viagens_scroll"><a title="Veja todas as viagens" href="/viagens" >Viagens</a></li>
			<li  class="blog"><a title="Veja o blog Fazendo as Malas" href="/blog" >Blog</a></li>	
			<li class="galeria"><a title="Veja os álbuns" href="<?php bloginfo('url')?>/albuns" >Álbuns</a></li>
			<li class="relatos-viagem">
				<?global $statusCount,$relatosCount; ?>
				<? $statusCount = wp_count_posts('atualizacao')->publish; $relatosCount = wp_count_posts('relatos')->publish; ?>
				<? $postWriteTypeCount = ($statusCount > 0 && $relatosCount > 0)? 2 : (($statusCount > 0 || $relatosCount > 0)? 1 : 0 );?>
				<? $link = ($postWriteTypeCount == 1 ?  ($statusCount > 0 ? get_bloginfo('url')."/status" : get_bloginfo('url')."/relatos" ) : get_bloginfo('url')."/relatos");?>						
				<a href="<? echo $link;?>">Posts</a>
				<? if($postWriteTypeCount == 2){?>
					<ul>
						<?if($statusCount > 0){?>
						<li>
							<a title="Veja os relatos da viagem" href="<?php bloginfo('url')?>/relatos">Relatos</a>
						</li>
						<?}?>
						<?if($relatosCount > 0){?>
						<li>
							<a title="Veja os posts de status" href="<?php bloginfo('url')?>/status">Status</a>
						</li>
						<?}?>
					</ul>
				<?}?>
			</li>
			<li class="viajantes" ><a title="Conheça os viajantes" href="<?php bloginfo('url')?>/viajantes" >Viajantes</a></li>
			<li  class="viagem"><a title="Veja detalhes do roteiro e da viagem" href="<?php bloginfo('url')?>/viagem" >Roteiro</a></li>						
			<li class="busca"><? widget::Get("busca_form", array('resizable'=> true, 'width_val'=>'160')) ?></li>		
		</ul>
	</div><!-- end inner -->
</nav><!-- end menu -->
<div id="geral">
	<div id="topo-wrapper">
		<div id="topo_elements">
			<div id="mundo" <? echo $Meta->Mundo;?> ></div>
			<div class="topo_right">
				<div id="google_translate_element">					
				</div>	
				<? widget::Get("share",array("showface" => 'false', "hideCommentBox" => true, "customUrl" => network_home_url())); ?>		
			</div>
						
			<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
			<div id="docs"></div>
		</div>
		
		<div id="topo">				
			<header id="topo-content">
				<a class="link_home_logo" title="Ir para a página inicial do Fazendo as Malas" href="/">Fazendo as Malas</a>
				
				<div class="lista-paises" >					
					<? global $trajetos; widget::Get('bandeiras', array('locations'=>$trajetos, 'width'=> 30,'limit_itens'=>8)) ?>
				</div><!--end paises -->
				<h1 class='site_title'>
					<a title="Ir para página inicial da viagem <? echo bloginfo('name');?>" href="<? echo bloginfo('url');?>"><? echo bloginfo('name');?>
					</a>
					<span style='display:none'> | Fazendo as Malas</span>
				</h1>
				<a class="link_home_fam site_sub_title" title="Ir para a página inicial do Fazendo as Malas" href="/"> Fazendo as Malas</a>						
				
			</header><!-- end topo-content-->
			<nav id="menu">				
				<div class="inner" style="margin:0 auto; width:990px; position:relative;">
				<ul>	
					<li class="viagens"><a title="Veja todas as viagens" href="/viagens" >Viagens</a></li>				
					<li class="galeria"><a title="Veja os álbuns de viagens" href="<?php bloginfo('url')?>/albuns" >Álbuns</a></li>
					<li class="relatos-viagem">										
						<a title="Veja os posts dessa viagem" href="<? echo $link;?>">Posts</a>
						<? if($postWriteTypeCount == 2){?>
							<ul>
								<?if($statusCount > 0){?>
								<li>
									<a title="Veja os relatos da viagem" href="<?php bloginfo('url')?>/relatos">Relatos</a>
								</li>
								<?}?>
								<?if($relatosCount > 0){?>
								<li>
									<a title="Veja os posts de status" href="<?php bloginfo('url')?>/status">Status</a>
								</li>
								<?}?>
							</ul>
						<?}?>
					</li>
					<li  class="viagem viagem-rotate rotateItem"><a title="Veja o roteiro e detalhes da viagem" href="<?php bloginfo('url')?>/viagem" >Roteiro</a></li>
					<li class="viajantes"><a title="Veja os viajantes da viagem" href="<?php bloginfo('url')?>/viajantes" >Viajantes</a></li>
					<li class="busca rotateItem"><? widget::Get("busca_form", array('resizable'=> true, 'width_val'=>'160')) ?></li>
				</ul>
				</div><!-- end inner -->
			</nav><!-- end menu -->
		</div><!-- end topo -->
		<div id="repeatContainer">
			<div id="cintaRepeater"></div><!-- end repeatContainer -->
		</div><!-- end  repeatContainer -->
	</div><!-- end topo-wrapper -->	
	<!-- div geral is closed in footer -->
