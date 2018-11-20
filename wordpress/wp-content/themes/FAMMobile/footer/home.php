<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }
?>
<div class="home_footer">	
	<? widget::Get("share", array('hideCommentBox'=>true)); ?>	
</div>

<?php
cfct_template_file('footer', 'bottom');

?>