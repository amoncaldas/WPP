<?php
/**
 * RSS 0.92 Feed Template for displaying RSS 0.92 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="0.92">
<channel>
	<title><?php bloginfo_rss('name');  ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<?php do_action('rss_head'); ?>
	
	<?php 
		$posts = $posts = getFeedItems();
		foreach($posts as $post)
		{				
			?>
				<item>
					<title><?php echo  $post->post_title; ?></title>
					<description><![CDATA[<?php echo $post->post_excerpt; ?>]]></description>
					<link><?php echo  $post->guid; ?></link>
					<?php do_action('rss_item'); ?>
				</item>
			<?		
		}	
	?>
</channel>
</rss>
