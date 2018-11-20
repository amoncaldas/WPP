<? if($options["width"] == null) $options["width"] = 500;?>

<div id="twitter-box">	
	<a class="boxlink" href="http://twitter.com/fazendoasmalas" target="_blank"><h2 class="twitter-title"><? if($options["title"] != null){echo $options["title"];}else echo "Últimos Tweets"; ?></h2></a>
	<a class="twitter-timeline" width="<? echo $options["width"]; ?>" target="_blank" href="https://twitter.com/fazendoasmalas" data-chrome="noheader <? if($options["show_footer"] != "yes"){echo " nofooter ";} ?>noborders transparent"  data-widget-id="347439457508655104"><h2 class="twitter-title">Últimos Tweets</h2></a>	
	<div class="twitter">	
		<script>
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");	
		</script>	
	</div>
</div>
<!--end twitter -->

