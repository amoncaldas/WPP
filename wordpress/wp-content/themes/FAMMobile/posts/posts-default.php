<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }

if(is_mobile_admin() && is_user_logged_in() && !in_array(get_user_role(), array("administrator","viajante")))
{
	wp_die( __( 'Você não tem permissão para administrar esse site' ) );	
}
else if(is_mobile_admin() && !is_user_logged_in())
{
	wp_redirect( home_url()."/wp-admin/" );exit;	
}

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas('index');

get_header();

?>

<div id="content">

	<?php global $current_user; get_currentuserinfo(); 
	if(is_user_logged_in())
	{	
	?>
		<div class="welcome">
			<? if(is_mobile_admin()){ ?>
				<h2 class="admin">Mobile Admin - </h2>
			<?}?>
			<span> <?  echo "Olá ".$current_user->display_name;?></span>
		</div>
		<?
	}

	//cfct_loop();
	
	if(get_current_blog_id() ==1)
	{		
		require_once( ABSPATH . '/wp-content/themes/FAMMobile/index_root.php' );
	}
	else
	{	
		
		if( strpos($_SERVER['REQUEST_URI'],"/m-admin/") > -1)
		{
			require_once( ABSPATH . '/wp-content/themes/FAMMobile/m-admin/index.php' );
		}
		else
		{	
			require_once( ABSPATH . '/wp-content/themes/FAMMobile/index_viagem.php' );
		}
	}

?>
</div><!--#content-->

<?php include(ABSPATH."/wp-content/themes/FAMMobile/footer/footer-default.php"); ?>