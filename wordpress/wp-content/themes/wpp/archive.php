<?php require_once("header.php");

// The query
$page = isset($_GET["page"])? $_GET["page"] : 1;
$posts_per_page = 12;
$args = array( "post_type"=> RENDER_ARCHIVE_POST_TYPE,  "post_status"=> "publish", "paged"=> $page, "posts_per_page"=> $posts_per_page);

if(defined("SECTION_ID")) {
	$args["post_parent"] = SECTION_ID;
} 
$the_query = new WP_Query($args);

if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) : $the_query->the_post();
		$post = get_post(); 
		$excerpt =  get_the_excerpt();
		if (!$excerpt || $excerpt === "") {
			$excerpt = strip_tags(apply_filters('the_content', get_the_content()));
			
			$append = "";
			if (strlen($excerpt) > 300) {
				$excerpt = substr($excerpt,0, 300);
				$excerpt .= " [...]";
			}			
		}
		$permalink = get_post_meta($post->ID, "custom_link", true);
		if (!$permalink) {
			$permalink = get_the_permalink($post->ID);
		}
		$raw_date = get_post_meta($post->ID, "custom_post_date", true);
		if (!$raw_date) {
			$raw_date = $post->post_date;
		}
		$formatted_date = date("j F Y",(strtotime($raw_date)));
		?>
		<article>
			<h2><a href="<?php echo $permalink; ?>"><?php the_title()?></a></h2>
			<time :datetime="<?php echo $raw_date;?>"><?php echo $formatted_date;?></time><br/><br/>
			<div> <?php echo get_the_post_thumbnail(get_the_ID()); ?></div><br/>
			<div><?php echo $excerpt; ?></div><br/>
			<a href="<?php the_permalink()?>"><?php the_title()?></a>
		</article>        
	<?php endwhile; ?>
	<?php wp_reset_postdata();


 	// Prepare pagination
	$pages = wpp_get_post_type_pages(RENDER_ARCHIVE_POST_TYPE, $posts_per_page);

	if ($pages > 1) {
		echo "<div>";
		for ($i=1; $i <= $pages; $i++) { 
			$REQUEST_URI = strtok($_SERVER["REQUEST_URI"],'?');
			$query = array("page"=>$i, "l"=> get_request_locale());
			$page_uri = $REQUEST_URI."?".http_build_query($query);
			$page_link = network_site_url($page_uri);
			echo "<a href='$page_link'>$i</a>";
		}
		echo "</div>";
	}
}

require_once("footer.php");