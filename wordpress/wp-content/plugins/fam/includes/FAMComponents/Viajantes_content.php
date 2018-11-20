<? 
require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Viajante.php");
$viajanteBO = new Viajante();
$viajantes = $viajanteBO->GetData($options);
		
	
if($options['return'] != 'onlyitens')
{
?>
	<section class="viajantes">
		<? 
		if(is_content_archive('viajantes')) echo '<h1 class="single_type_label viajante_icon topic_label_icon topic_font_color hand_font">'; else echo "<h2>";
		if($options["title"] != null) 
		{
			echo $options["title"];
		}
		else
		{
			echo "Viajantes";
		}
		if(is_content_archive('status')) echo '</h1>'; else echo "</h2>";
		?>
	
		<ul>
		<?}	
			if(is_array($viajantes) && count($viajantes) > 0)
			{
		
				foreach($viajantes as $viajante) 
				{	
					if($options["show_short_name"] == "yes")
					{
						$name = $viajante->FirstName;
					}
					else
					{
						$name = $viajante->FullName;
					}														
					?>
						<li>
							<? if($options["show_image"] != "no")
							{?>
							<div class="foto">					
								<a class="fancybox" title="Viajante <? echo $name; ?>" href="<? echo $viajante->UserImage->ImageLargeSrc ?>">
								<img alt="Viajante <? echo $name; ?>" class="foto_viajante_relato" src="<? echo $viajante->UserImage->ImageGaleryThumbSrc ?>"/>
								</a>																	
							</div><!-- end foto -->
							<?}?>
							<h3><a href='<? echo $viajante->ViajanteUrl?>'><? echo $name;?></a></h3>
				
							<? if($options["show_bio"] == "yes"){ ?>								
								<p><? echo $viajante->UserProfile;?></p>
							
							<?}?>	
							<input type='hidden' class='itemId' value="<? echo $viajante->UserId; ?>"/>										
						</li>
					<?									
				}
								
			}
			else
			{
				if($options["return"] == "onlyitens")
				{
						?><span class="no_content">Sem mais viajantes<span><?
				}
				else
				{
					?><span class="no_content">Sem viajantes cadastrados até o momento<span><?
				}
			}
		
			if($options["show_more"] == "yes" && $viajanteBO->HasMoreData )
			{							
				echo "<li class='loadmore'>";	
				widget::Get("load-more-content", array("content_type"=>"viajantes","itens"=>2,'show_short_name'=>$options["show_short_name"], 'viagemId'=>$options["viagemId"],'show_bio'=>$options["show_bio"],'show_image'=>$options["show_image"], "excluded_ids"=>$options["excluded_ids"]));
				echo "</li>";			
			}
		
			if(!$viajanteBO->HasMoreData && ($options["show_more"] == "yes" || $options['return'] == 'onlyitens'))
			{	
				echo "<li style='display:none;' class='no_more'></li>";
			}		
		
			if($options['return'] != 'onlyitens')
			{
			?>
		</ul>
	</section>		
<?}?>
