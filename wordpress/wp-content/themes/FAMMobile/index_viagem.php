<?	
	if (wp_count_posts('atualizacao')->publish > 0)
	{
		widget::Get("atualizacoes", array('itens'=> 1,'show_more'=>'yes','content_lenght'=> 200, 'show_location'=> true,'show_read_full'=>'yes', 'float'=>'right','margin_right'=>'0px', 'width'=>'100%','show_comment'=> 'no', 'show_media'=> "yes", 'foto_width'=>'70px'));
	}
	if (wp_count_posts('relatos')->publish > 0)
	{
		widget::Get("ultimos-relatos", array('show_large_image'=>'yes','show_meta_data_label'=>'yes','itens'=> 3,'content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes'));
	}
	else
	{
		widget::Get("blog_posts", array('title'=>'Blog FAM','show_large_image'=>'yes','show_meta_data_label'=>'yes','itens'=> 2,'content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes'));
	}
	
	widget::Get("booking_form", array('show_search_box'=>'yes','float'=>'left','margin_top'=>"0px",'label'=>'home_geral_mobile','width'=>'100%','height'=>'170px'));
	
	if (wp_count_posts('albuns')->publish < 2)
	{
		global $GetMultiSiteData;
		$GetMultiSiteData = true;		
	}
	$title = ($GetMultiSiteData === true)? "Albuns de viagens": 'Albuns de viagem';
	widget::Get("galeria", array('title'=>$title,'show_more' => 'yes','itens'=> 4));
	$GetMultiSiteData = false;
		
	widget::Get("viagens", array('list_type'=>'box','itens'=>2,'current_viagemId'=>get_current_blog_id(),'show_more'=>'yes','width'=>'99%','float'=>'left','title'=>'Outras viagens'));
?>	
