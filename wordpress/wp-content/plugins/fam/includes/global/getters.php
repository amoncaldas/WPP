<?php
/**
 * Get post from all sites in wordpress
 *
 * @param String $PostType
 * @param Array $options
 * @return void
 */
function getMultiSiteContent($PostType, $options) {		
    global $wpdb;
    global $blog_id;
    $table_prefix = $wpdb->base_prefix;			
    $blog_list  = $wpdb->get_results( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"), ARRAY_A );	
    $itensAdded = 0;
    $postsData = array();
    $counter = 1;
    $sqlstr = "";
    foreach ($blog_list AS $blog) 
    {	
        $attachmentWhere = "";
        if($PostType == "attachment" )	
        {
            $attachmentWhere = " or post_status='inherit' ";
        }		
        
        if ($blog['blog_id'] > 0) 
        {		
            if($counter > 1 )
            {					
                $sqlstr .= " union ";						
            }
            
            if($blog['blog_id'] == 1)
            {
                $sqlstr .= " SELECT wp_fam_posts.*, 1 as blog_id FROM wp_fam_posts where (post_status='publish' ".$attachmentWhere.") and post_type = '".$PostType."' ";
            }	
            else
            {						
                $sqlstr .= " SELECT ".$table_prefix .$blog['blog_id']."_posts.*, ".$blog['blog_id']." as blog_id from ".$table_prefix .$blog['blog_id']."_posts where (post_status='publish' ".$attachmentWhere.") and post_type = '".$PostType."' ";
            }
                    
            if($options["parentId"] != null)
            {
                $sqlstr .= " and post_parent = '".$options["parentId"]."'";
            }
            
            if($options['excluded_ids'] != null)
            {
                if(!is_array($options['excluded_ids']))
                {
                    $options['excluded_ids'] = split(",",$options['excluded_ids']);
                }
                if(is_array($options['excluded_ids']) && count($options['excluded_ids'] > 0))
                {
                    foreach($options['excluded_ids'] as $id)
                    {
                        $ids = split(";", $id);
                        if($blog['blog_id'] == $ids[1] && is_numeric($ids[0]))
                        {																		
                            $sqlstr .= " and ID <> ".$ids[0];								
                        }
                    }
                }	
                
                                    
            }
            if($options['included_ids'] != null)
            {
                if(!is_array($options['included_ids']))
                {
                    $options['included_ids'] = split(",",$options['included_ids']);
                }
                if(is_array($options['included_ids']) && count($options['included_ids'] > 0))
                {
                    $sqlstr .= " and ID IN ( 0 ";
                    foreach($options['included_ids'] as $id)
                    {
                        $ids = split(";", $id);
                        if($blog['blog_id'] == $ids[1] && is_numeric($ids[0]))
                        {																		
                            $sqlstr .= ", ".$ids[0];								
                        }
                    }
                    $sqlstr .= " ) ";
                }	
            }
            if($options["authorId"] != null)
            {
                $sqlstr .= " and post_author = '".$options["authorId"]."'";
            }
            if($options["where"] != null)
            {
                $sqlstr .= " ".$options["where"];
            }
            
            if($options["destaques_video"] == "yes")
            {					
                $sqlstr .= " and post_mime_type = 'video/x-flv'";				
            }			
                                            
            $counter++;				
        }			
    }
    if($options["orderby"] != null)
    {
        if($options["orderby"] == "rand")
        {
            $options["orderby"] = "rand()";
        }
        $sqlstr .= " order by ".$options["orderby"] ." limit 0, ".$options["itens"];
    }
    else
    {
        $sqlstr .= " order by post_modified DESC limit 0, ".$options["itens"];
    }
    
    if($options["page"] != null && is_numeric($options["page"]))
    {			
        $sqlstr .= " offset ".($options["itens"] * ($options["page"] ));
    }
    
    
    $posts = $wpdb->get_results(($sqlstr));	
    
    return $posts;							
}

/**
 * Get feed items
 *
 * @return void
 */
function getFeedItems() {
    $itens = array();
    foreach(array('blog_post','forum','relatos','albuns','atualizacao') as $ptype)
    {
        $urlSlug = $ptype;
        
        if($ptype == "blog_post")
        {
            $urlSlug = "blog";
        }
        if($ptype == "atualizacao")
        {
            $urlSlug = "status";
        }
        if(in_array($ptype, array('blog_post','forum')))
        {
            $posts = get_posts(array('post_type'=>$ptype,'order'=>'desc','orderby'=>'post_date'));
        }
        else
        {
            $posts = getMultiSiteContent($ptype,array('itens'=>20),true);				
        }
        foreach($posts as $post_current) {
            global $post;
            $post = $post_current;
            $post->post_title = apply_filters('the_title_rss', $post->post_title);
            $post->feed_date = mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true,$post), false);
            $post->author_name = apply_filters('the_author_' . $field, get_the_author_meta("user_nicename", $post->post_author), $post->post_author);					
            $post->guid = get_site_url($post->blog_id)."/".$urlSlug."/".$post->post_name."/".$post->ID."/";
            
            $post->post_comment_link =  esc_url( get_post_comments_feed_link(null, 'rss2') );
            $post->post_comment_number = get_comments_number();

            $graph_url = "https://graph.facebook.com/?fields=share&access_token=585138868164342|9Luxc3zO1RXMJR20BqjGB2W022o&id=".$post->guid;
            $response = file_get_contents(($graph_url));
            $decoded_response = json_decode($response);				
            if (property_exists($decoded_response, 'share') && property_exists($decoded_response->share, 'comment_count')){					
                $post->post_comment_number = $decoded_response->share->comment_count;									
            } 
            
            if($post->blog_id == null)
            {
                $post->blog_id = get_current_blog_id();
            }
            if(in_array($ptype, array('blog_post','forum')))
            {
                $post->category = get_the_category_rss("rss2");
                if($ptype == "blog_post")
                {
                    $content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $post->post_content);				
                    $post->post_content = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);						
                    $post->post_excerpt = getSubContent($post->post_content, 500);
                }
                else
                {
                    $post->post_content = get_the_content_feed('rss2');					
                    $excerpt = get_the_excerpt();
                    $post->post_excerpt =  apply_filters('the_excerpt_rss', $excerpt);
                }	
            }
            else
            {
                $post->category = "<category><![CDATA[".$urlSlug."]]></category> ";
                            
                if($post->post_type == "relatos")
                {							
                    $content_replaced_caption_open = preg_replace('/\[caption.*?\]/', '<div class="caption_wrapper"><div class="caption">', $post->post_content);				
                    $post->post_content  = preg_replace('/\[\/caption\]/', '</div></div>', $content_replaced_caption_open);						
                    $post->post_excerpt = getSubContent($post->post_content, 500);
                }
                if($post->post_type == "albuns")
                {	
                    // $post->post_excerpt = $album->Resumo;
                    // $post->post_content  = "<img src='".$album->MidiaPrincipal->ImageMediumSrc."' title='".$album->Titulo."' alt='".$album->Titulo."' /><br/>" .$album->Resumo;
                }
                if($post->post_type == "atualizacao")
                {
                    
                    // switch_to_blog($post->blog_id);
                    // $status = new AtualizacaoVO($post->ID);						
                    // restore_current_blog();					
                    // $post->post_excerpt = $status->Conteudo;	
                    // $post->post_content  = "<img src='".$status->MidiaPrincipal->ImageMediumSrc."' title='".$status->Titulo."' alt='".$status->Titulo."' /><br/>" .$status->Conteudo;
                }
            }
        }
        $itens = array_merge($itens, $posts);
    }
    
    usort($itens, array($this,"SortCompareByPostDate"));
    return $itens;
}


/**
 * Get sub content of a string
 *
 * @param String $content
 * @param Integer $length
 * @param boolean $forcePrecision
 * @return void
 */
function getSubContent($content, $length, $forcePrecision = false)
{
	$length = $length-3;
	$content = strip_tags($content, '');
	if(strlen($content) > $length)
	{		
		if($forcePrecision == true)	
		{			
			$content = trim($content);					
			if(strpos($content, " ") > 0 )
			{		
				$content = substr($content, 0,$length + 200);						
				$offset = strlen($content);	
				$spacePosition = 	strlen($content);	
				while( ($pos = strrpos(($content)," ",$offset)) != false) {
					$offset = $pos;
					if($pos < $length)
					{						
						$spacePosition = $pos;							
						break;
					}
				}		
													
				$content = substr($content, 0, $spacePosition)."...";
				
				if(strlen($content) > $length + 3)
				{
					$content = substr($content, 0, $length)."...";
				}				
			}
			else
			{																		
				$content = substr($content, 0,$length)."...";
			}
		}
		else
		{
			if(strlen($content) > $length)
			{			
				if(strpos($content, " ", $length) > $length)
				{
					$content =  substr($content, 0, strpos($content, " ", $length))."...";
				}
				else
				{
					$length = ($length > 20)? ($length -20): 0;								
					$content =  substr($content, 0, strpos($content, " ", $length))."...";
				}				
			}
		}	
		$content = rtrim($content, ',');
		$content = rtrim($content, '-');		
		return $content;
	}
	else
	{
		return $content;
	}			
}


