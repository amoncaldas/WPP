<?php
/**
 * Atom Feed Template for displaying Atom Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('atom') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:thr="http://purl.org/syndication/thread/1.0"
  xml:lang="<?php bloginfo_rss( 'language' ); ?>"
  xml:base="<?php bloginfo_rss('url') ?>/wp-atom.php"
  <?php do_action('atom_ns'); ?>
 >
	<title type="text"><?php bloginfo_rss('name'); ?></title>
	<subtitle type="text"><?php bloginfo_rss("description") ?></subtitle>

	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></updated>

	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php bloginfo_rss('url') ?>" />
	<id><?php bloginfo('atom_url'); ?></id>
	<link rel="self" type="application/atom+xml" href="<?php self_link(); ?>" />

	<?php do_action('atom_head'); ?>
	<?php
		$posts = getFeedItems();
		foreach($posts as $post)
			{ ?>
				<entry>
					<author>
						<name><?php echo $post->author_name;  ?></name>
						<?php $author_url = get_the_author_meta('url', $post->post_author); if ( !empty($author_url) ) : ?>
						<uri><?php echo get_the_author_meta('url',$post->post_author)?></uri>
						<?php endif;
						do_action('atom_author'); ?>
					</author>
					<title type="<?php html_type_rss(); ?>"><![CDATA[<?php echo $post->post_title ?>]]></title>
					<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php echo  $post->guid; ?>" />
					<id><?php echo  $post->guid; ?></id>
					<updated><?php echo $post->feed_date; ?></updated>
					<published><?php echo $post->feed_date; ?></published>
					<?php echo $post->category; ?>
					<summary type="<?php html_type_rss(); ?>"><![CDATA[<?php echo $post->post_excerpt; ?>]]></summary>
					<?php if ( !get_option('rss_use_excerpt') ) : ?>
							<content type="<?php html_type_rss(); ?>" xml:base="<?php echo  $post->guid; ?>"><![CDATA[<?php $post->post_content; ?>]]></content>
					<?php endif; ?>
					<?php atom_enclosure(); ?>
					<?php do_action('atom_entry'); ?>
					<link rel="replies" type="<?php bloginfo_rss('html_type'); ?>" href="<?php echo $post->post_comment_link; ?>" thr:count="<?php echo $post->post_comment_number;?>"/>
					<link rel="replies" type="application/atom+xml" href="<?php echo esc_url( $post->post_comment_link; ?>" thr:count="<?php echo $post->post_comment_number;?>"/>
					<thr:total><?php echo $post->post_comment_number;?></thr:total>
				</entry>
		<?php } ?>
</feed>
