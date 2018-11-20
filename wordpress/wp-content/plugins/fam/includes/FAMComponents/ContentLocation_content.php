<?
if($options["location"] != null)
{
	?>		
		<div class="content-location">	
			<input type="hidden" class="latitude" value="<? echo $options["location"]->Latitude;?>"/>
			<input type="hidden" class="longitude" value="<? echo $options["location"]->Longitude;?>"/>
			<input type="hidden" class="local" value="<? echo $options["location"]->GetLocalSubString(25);?>"/>
			<input type="hidden" class="locationImage" value="<? echo $options["locationImage"];?>"/>
			<input type="hidden" class="enable_controls" value="<? echo $options["enable_controls"];?>"/>
			<input type="hidden" class="image_map" value="<? echo $options["image_map"];?>"/>		
		</div><!-- end content-location -->
		
	<?
}
?>