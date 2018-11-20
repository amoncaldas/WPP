<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }

if (have_posts()) {
	echo '<ul class="disclosure table group">';
	while (have_posts()) {
		the_post();
		?>
			<li>
		<?php
				cfct_excerpt();
		?>
			</li>
		<?php
	}
	echo '<li class="pagination">', cfct_misc('nav-list'),'</li>';
	echo '</ul>';
}

?>