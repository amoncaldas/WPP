<?php

function fam_feed_request($qv) {	
    	
    if (isset($qv['feed']))
    {		
    	$qv['post_type'] = get_post_types();					
    	unset($qv['post_type']['attachment']);
    	unset($qv['post_type']['revision']);
    	unset($qv['post_type']['nav_menu_item']);    		
    }
    return $qv;
}

//add custom feed content
function add_feed_content($content) {    	
    if(is_feed()) {
    	global $post;
    	$post->content;
    }
    return $content;
}

//add custom feed content
function fam_set_feed_title($title) {
    return $title;
}

add_filter('the_excerpt_rss', 'add_feed_content');
add_filter('the_content', 'add_feed_content');
add_filter('request', 'fam_feed_request');
add_filter('the_title_rss', 'fam_set_feed_title');
remove_action('wp_head', 'feed_links_extra', 3 );
remove_action('wp_head', 'feed_links', 2 );