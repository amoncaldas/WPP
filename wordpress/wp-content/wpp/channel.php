<?php
	$cache_expire = 60*60*24*365;
	header("Pragma: public");
	header("Cache-Control: maxage=".$cache_expire);
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+$cache_expire).' GMT');
?>
<HTML>
	<HEAD>
		<script src="//connect.facebook.net//pt_BR/all.js"></script>
        <meta name="robots" content="noindex, nofollow">
		<TITLE></TITLE>
	</HEAD>
	<BODY>
	</BODY>
</HTML>