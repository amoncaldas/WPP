<?php
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/LocationVO.php' );
?>
<span class="travelFlags">
	<ul>	
		<?	
			$flagSize = $options["width"];
			$liWidth = "style='width:".$flagSize."px;'";
			$height = $flagSize / 1.4;
			if(is_array($options["locations"]) && count($options["locations"])> 0)
			{				
				$flags = array();				
				$counter = 0;
				$counterOutput = 1;
				
				foreach($options["locations"] as $trajetoVO)
				{								
					if($counter > 0)
					{	
						if($options["limit_itens"] == null || $options["limit_itens"] == 0 || $options["limit_itens"] >= ($counterOutput))	
						{
							
							$Pais =  split(",",$trajetoVO->LocationPartida->Local);	
							$flag = strtolower($trajetoVO->LocationPartida->SiglaPais);												    
							$imgSrc = $trajetoVO->LocationPartida->GetBandeiraPais($flagSize);
											
							$imgEl = '<li '.$liWidth.'><img alt="'.$Pais[count($Pais) -1].'" title="'.$Pais[count($Pais) -1].'" width="'.$flagSize.'" height="'.$height.'" class="countryFlag" src="'.$imgSrc.'"></li>';
							$hasFlag = false;
							$hasFlag = in_array($flag, $flags);							
							if ($hasFlag === false) {								
								$flags[]  = $flag;
								echo $imgEl;
								$counterOutput++;						
							}
						}
					}
					
					if($options["limit_itens"] == null || $options["limit_itens"] == 0 || $options["limit_itens"] >= ($counterOutput))	
					{
						$Pais =  split(",",$trajetoVO->LocationChegada->Local);					
						$flag = strtolower($trajetoVO->LocationChegada->SiglaPais);					                       
						$imgSrc = $trajetoVO->LocationChegada->GetBandeiraPais($flagSize);	
										
						$imgEl = '<li '.$liWidth.'><img alt="'.$Pais[count($Pais) -1].'" title="'.$Pais[count($Pais) -1].'" width="'.$flagSize.'" height="'.$height.'" class="countryFlag" src="'.$imgSrc.'"></li>';					
						$hasFlag = false;
						$hasFlag = in_array($flag, $flags);						
						if ($hasFlag === false) {							
							$flags[]  = $flag;
							echo $imgEl;
							$counterOutput++;
						}	
					}
					$counter++;				
				}
			}
			else
			{
				echo "<li ".$liWidth."><img alt='Brasil' title='Brasil' width='".$flagSize."px' height='".$height."px' class='countryFlag' src='http://www.geonames.org/flags/x/br.gif'></li>";	
			}
		?>			
	</ul>		
</span>
