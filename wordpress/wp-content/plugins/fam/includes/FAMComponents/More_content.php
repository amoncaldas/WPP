<?php
setlocale(LC_ALL, NULL);
setlocale(LC_ALL, 'pt_BR.iso88591');
require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
define('ABSPATH',$_SERVER['DOCUMENT_ROOT']);
require_once(ABSPATH."/wp-content/FAMComponents/widget.php");

if (isset($_POST["is_ajax"]) && !empty($_POST["is_ajax"]) && isset($_POST["content_type"]) && !empty($_POST["content_type"])) //Checks if required parans
{ 
	$type = $_POST["content_type"];
	$lastId = 2147483647;
	global $GetMultiSiteData;
	$GetMultiSiteData = false;
	if ($_POST["multiSiteData"] == 'yes')
	{
		global $GetMultiSiteData;
		$GetMultiSiteData = true;		
	}
	if (isset($_POST["lastId"]) && !empty($_POST["lastId"]))
	{
		$lastId = $_POST["lastId"];
	}
	if (isset($_POST["itens"]) && !empty($_POST["itens"]))
	{
		$itens = $_POST["itens"];
	}
	if (isset($_POST["excluded_ids"]) && !empty($_POST["excluded_ids"]))
	{
		$excluded_ids = $_POST["excluded_ids"];
	}	
	if (isset($_POST["content_lenght"]) && !empty($_POST["content_lenght"]))
	{
		$content_lenght = $_POST["content_lenght"];
	}	
	if (isset($_POST["userId"]) && !empty($_POST["userId"]))
	{
		$userId = $_POST["userId"];
	}
	if (isset($_POST["foto_size"]) && !empty($_POST["foto_size"]))
	{
		$foto_size = $_POST["foto_size"];
	}
	if (isset($_POST["parentId"]) && !empty($_POST["parentId"]))
	{
		$parentId = $_POST["parentId"];
	}
	if (isset($_POST["show_media"]) && !empty($_POST["show_media"]))
	{
		$show_media = $_POST["show_media"];
	}
	if (isset($_POST["show_comment"]) && !empty($_POST["show_comment"]))
	{
		$show_comment = $_POST["show_comment"];
	}	
	if (isset($_POST["foto_width"]) && !empty($_POST["foto_width"]))
	{
		$foto_width = $_POST["foto_width"];
	}
	if (isset($_POST["link_on_content"]) && !empty($_POST["link_on_content"]))
	{
		$link_on_content = $_POST["link_on_content"];
	}
	if (isset($_POST["show_map"]) && !empty($_POST["show_map"]))
	{
		$show_map = $_POST["show_map"];
	}
	if (isset($_POST["show_location"]) && !empty($_POST["show_location"]))
	{
		$show_location = $_POST["show_location"];
	}
	if (isset($_POST["orderby"]) && !empty($_POST["orderby"]))
	{
		$orderby = $_POST["orderby"];
	}
	if (isset($_POST["show_bio"]) && !empty($_POST["show_bio"]))
	{
		$show_bio = $_POST["show_bio"];
	}
	if (isset($_POST["show_image"]) && !empty($_POST["show_image"]))
	{
		$show_image = $_POST["show_image"];
	}
	if (isset($_POST["show_meta_data_label"]) && !empty($_POST["show_meta_data_label"]))
	{
		$show_meta_data_label = $_POST["show_meta_data_label"];
	}
	if (isset($_POST["list_type"]) && !empty($_POST["list_type"]))
	{
		$list_type = $_POST["list_type"];
	}
	
	
	
	
	
	if (isset($_POST["viagemId"]) && !empty($_POST["viagemId"]) && $_POST["viagemId"] != "")
	{
		$viagemId = $_POST["viagemId"];			
		global $switched;
		switch_to_blog($viagemId, true);				
	}	
	else
	{
		$viagemId = null;		
	}
	if (isset($_POST["show_short_name"]) && !empty($_POST["show_short_name"]))
	{
		$show_short_name = $_POST["show_short_name"];
	}
	if (isset($_POST["show_share"]) && !empty($_POST["show_share"]))
	{
		$show_share = $_POST["show_share"];
	}
	if(isset($_POST["show_admin_controls"]) && !empty($_POST["show_admin_controls"]))
	{
		$show_admin_controls = $_POST["show_admin_controls"];
	}
	
	if(isset($_POST["viagem_medium_image"]) && !empty($_POST["viagem_medium_image"]))
	{
		$viagem_medium_image = $_POST["viagem_medium_image"];
	}
	
	if(isset($_POST["viagem_show_map"]) && !empty($_POST["viagem_show_map"]))
	{
		$viagem_show_map = $_POST["viagem_show_map"];
	}
	
	if(isset($_POST["show_read_full"]) && !empty($_POST["show_read_full"]))
	{
		$show_read_full = $_POST["show_read_full"];
	}	
	if(isset($_POST["show_large_image"]) && !empty($_POST["show_large_image"]))
	{
		$show_large_image = $_POST["show_large_image"];
	}	
	
	switch($type)
	{
		case "relatos":			
			widget::Get("ultimos-relatos", array('show_meta_data_label'=>$show_meta_data_label,'show_large_image'=>$show_large_image,'itens'=> $itens,'show_map'=>$show_map,'show_share'=>$show_share,'lastId'=> $lastId,'return'=>'onlyitens','content_lenght'=> $content_lenght,'authorId'=> $userId,'excluded_ids'=> $excluded_ids));
			break;
		case "fotos":			
			widget::Get("fotos", array('itens'=> $itens,'orderby'=>$orderby,'lastId'=> $lastId,'foto_size'=> $foto_size, 'return'=>'onlyitens','parentId'=> $parentId,'excluded_ids'=> $excluded_ids));
			break;
		case "galeria":			
			widget::Get("galeria", array('itens'=> $itens,'lastId'=> $lastId,'return'=>'onlyitens','excluded_ids'=> $excluded_ids,'show_admin_controls'=>$show_admin_controls));
			break;
		case "atualizacoes":
			widget::Get("atualizacoes", array('itens'=> $itens,'show_read_full'=>$show_read_full,'show_location'=>$show_location,'show_map'=>$show_map,'show_comment'=>$show_comment,'link_on_content'=>$link_on_content,'authorId'=> $userId,'show_media'=>$show_media,'lastId'=> $lastId,'return'=>'onlyitens','content_lenght'=> $content_lenght,'foto_width'=>$foto_width, 'excluded_ids'=> $excluded_ids,'show_admin_controls'=>$show_admin_controls));
			break;
		case "blog_posts":
			widget::Get("blog_posts", array('show_meta_data_label'=>$show_meta_data_label,'show_large_image'=>$show_large_image,'itens'=> $itens,'show_comment'=>$show_comment,'link_on_content'=>$link_on_content,'return'=>'onlyitens','content_lenght'=> $content_lenght,'foto_width'=>$foto_width, 'excluded_ids'=> $excluded_ids));
			break;
		case "forum":
			widget::Get("forum", array('itens'=> $itens,'show_comment'=>$show_comment,'return'=>'onlyitens','excluded_ids'=> $excluded_ids));
			break;
		case "forum_topics":
			widget::Get("forum_topics", array('itens'=> $itens,'parentId'=>$parentId,'return'=>'onlyitens','excluded_ids'=> $excluded_ids));
			break;	
		case "comentarios":
			widget::Get("comentarios", array('itens'=> $itens,'parentId'=>$parentId,'return'=>'onlyitens','excluded_ids'=> $excluded_ids));
			break;
		case "viagens":
			widget::Get("viagens", array('list_type'=>$list_type,'itens'=> $itens,'return'=>'onlyitens','excluded_ids'=> $excluded_ids,'viagem_medium_image'=>$viagem_medium_image,'userId'=>$userId, 'viagem_show_map'=>$viagem_show_map));
			break;
		case "viajantes":
			widget::Get("viajantes", array('show_short_name'=>$show_short_name,'itens'=> $itens,'viagemId'=>$viagemId,'show_image'=>$show_image,'show_bio'=>$show_bio,'return'=>'onlyitens','excluded_ids'=> $excluded_ids));
			break;					
					
	}		
}



function GetBtn($more_options)
{
	global $GetMultiSiteData;	
	if($GetMultiSiteData || get_current_blog_id() == 1)
	{
		$multiSiteData = "yes";
	}
	echo "<div class='moreContent' onclick='javascript:GetMoreContent(this);'>
	<span>mais</span>
	<input type='hidden' class='userId' value='".$more_options["userId"]."'/>
	<input type='hidden' class='itens' value='".$more_options["itens"]."'/>
	<input type='hidden' class='content_type' value='".$more_options["content_type"]."'/>	
	<input type='hidden' class='excluded_ids' value='".$more_options["excluded_ids"]."'/>
	<input type='hidden' class='content_lenght' value='".$more_options["content_lenght"]."'/>
	<input type='hidden' class='foto_size' value='".$more_options["foto_size"]."'/>
	<input type='hidden' class='parentId' value='".$more_options["parentId"]."'/>
	<input type='hidden' class='multiSiteData' value='".$multiSiteData."'/>	
	<input type='hidden' class='link_on_content' value='".$more_options["link_on_content"]."'/>
	<input type='hidden' class='show_media' value='".$more_options["show_media"]."'/>
	<input type='hidden' class='foto_width' value='".$more_options["foto_width"]."'/>
	<input type='hidden' class='show_comment' value='".$more_options["show_comment"]."'/>
	<input type='hidden' class='show_map' value='".$more_options["show_map"]."'/>
	<input type='hidden' class='show_location' value='".$more_options["show_location"]."'/>
	<input type='hidden' class='orderby' value='".$more_options["orderby"]."'/>
	<input type='hidden' class='show_bio' value='".$more_options['show_bio']."'/>
	<input type='hidden' class='show_image' value='".$more_options['show_image']."'/>
	<input type='hidden' class='viagemId' value='".$more_options['viagemId']."'/>
	<input type='hidden' class='show_short_name' value='".$more_options['show_short_name']."'/>	
	<input type='hidden' class='show_share' value='".$more_options['show_share']."'/>	
	<input type='hidden' class='show_admin_controls' value='".$more_options['show_admin_controls']."'/>	
	<input type='hidden' class='search_term' value='".$_GET['s']."'/>	
	<input type='hidden' class='viagem_medium_image' value='".$more_options['viagem_medium_image']."'/>
	<input type='hidden' class='viagem_show_map' value='".$more_options['viagem_show_map']."'/>	
	<input type='hidden' class='show_read_full' value='".$more_options['show_read_full']."'/>
	<input type='hidden' class='show_large_image' value='".$more_options['show_large_image']."'/>	
	<input type='hidden' class='show_meta_data_label' value='".$more_options['show_meta_data_label']."'/>
	<input type='hidden' class='list_type' value='".$more_options['list_type']."'/>
	
	
	<input type='hidden' class='base_path' value='".get_bloginfo('template_url')."'/>
	
	
	</div>";
}



?>