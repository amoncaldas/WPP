<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name');  ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	
	<?php 
		require_once( FAM_PLUGIN_PATH . '/includes/FAMCore/BO/Conteudo.php' );
		$posts = Conteudo::GetFeedItens();
		foreach($posts as $post)
		{							
			?>
				<item>
					<title><?php echo $post->post_title; ?></title>
					<link><?php echo  $post->guid; ?></link>
					<comments><?php echo $post->guid."#comments"; ?></comments>
					<pubDate><?php echo $post->feed_date; ?></pubDate>
					<dc:creator><?php echo $post->author_name;  ?></dc:creator>
					<?php echo $post->category; ?>

					<guid isPermaLink="false"><?php echo  $post->guid; ?></guid>
	
					<description><![CDATA[<?php echo $post->post_excerpt; ?>]]></description>
	
						<?php if ( strlen( $post->post_content ) > 0 ) : ?>
							<content:encoded><![CDATA[<?php echo $post->post_content; ?>]]></content:encoded>
						<?php else : ?>
							<content:encoded><![CDATA[<?php echo $post->post_excerpt; ?>]]></content:encoded>
						<?php endif; ?>

					<wfw:commentRss><?php echo $post->post_comment_link; ?></wfw:commentRss>
					<slash:comments><?php echo $post->post_comment_number; ?></slash:comments>
					<?php rss_enclosure(); ?>
					<?php do_action('rss2_item'); ?>
				</item>
			<?
		}	
	?>
</channel>
</rss>
