<?php

/**
 * class LocationVO
 *
 * Description for class LocationVO
 *
 * @author:
*/
class LocationVO  {

	private $Local;
	public $Latitude;
	public $Longitude;
	public $Cidade;
	public $Pais;
	public $SiglaPais;
	public $LocalComPais;
	
		

	/**
	 * LocationVO constructor
	 *
	 * @param 
	 */
	function LocationVO() {

	}
	
	public function __get($property) {
		if (property_exists($this, $property)) {
		  return $this->$property;
		}
	}

	public function __set($property, $value) {
			
		if (property_exists($this, $property)) {
				
			if($property == "Local")
			{				
				preg_match_all('/\((.*?)\)/', $value, $sigla);				
					
				if(is_array($sigla) && count($sigla) > 0 && is_array($sigla[0]) && count($sigla[0]) > 0)
				{																
					$this->SiglaPais = str_replace( ")","", str_replace("(","", $sigla[0][0]));					
				}			
				$local = str_replace("(".$this->SiglaPais.")","",$value);
				$this->Local = $local;	
				if($this->SiglaPais != null && $this->SiglaPais != "")
				{
					$this->LocalComPais = 	$local."(".$this->SiglaPais.")";	
				}
					
			}
			else
			{
				$this->$property = $value;
			}
		}

    return $this;
  }
	
	public function GetLocalSubString($locationLenght)
	{
		if($this->Local != null)
		{
			$Local = trim($this->Local);
			if(strlen($Local) > $locationLenght)
			{		
				if(strpos($Local, "-")> -1)
				{		
					$spacePosition = strpos($Local, "-", $locationLenght);
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = strpos($Local, "-");
					}							
					$Local = substr($Local, 0, $spacePosition);
				}
				elseif(strpos($Local, ",")> -1)
				{		
					$spacePosition = strpos($Local, ",", $locationLenght);
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = strpos($Local, ",");
					}			
					$Local = substr($Local, 0, $spacePosition);
				}
				elseif(strpos($Local, " ") > 0)
				{		
					$spacePosition = strpos($Local, " ", $locationLenght);
					if($spacePosition == null || $spacePosition == 0)
					{
						if(strpos($Local, " ") < $locationLenght)
						{
							$spacePosition = strpos($Local, " ", $locationLenght -5);
							if($spacePosition == null || $spacePosition == 0)
							{
								$spacePosition = strpos($Local, " ", $locationLenght -10);
							}
							if($spacePosition == null || $spacePosition == 0)
							{
								$spacePosition = strpos($Local, " ");
							}
							
						}
						else
						{
							$spacePosition = strpos($Local, " ");
						}
					}								
					$Local =  substr($Local, 0, $spacePosition);
				}
				else
				{					
					$lenght = ($locationLenght > 5)? ($locationLenght -5): 0;														
					$Local = substr($Local, 0, strpos($Local, " ", $lenght));
				}
					
				if(strlen($Local) > $locationLenght + 10)	
				{		
					$Local = trim($Local);		
					$spacePosition = strpos($Local, " ", $locationLenght);
					
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = strpos($Local, " ");
					}
					if($spacePosition == null || $spacePosition == 0)
					{
						$spacePosition = $locationLenght;
					}				
					$Local =  substr($Local, 0, $spacePosition);				
				}
						
					
			}
			
			$Local = rtrim($Local, '.');
			$Local = rtrim($Local, ',');
			$Local = rtrim($Local, '-');
			
			return $Local;
			
		}
	}
	
	public function GetBandeiraPais($width)
	{		
		
		if($this->SiglaPais != null)
		{
			$extension = $width > 40? ".gif" : ".png";
			$flag = strtolower($this->SiglaPais);
				$relativeLocation = "/wp-content/flags/country/". ($width > 40? "big/" : "small/").$flag.$extension;
			$localFlag =  $_SERVER["DOCUMENT_ROOT"].$relativeLocation;			
			if(file_exists($localFlag))
			{
				$imgSrc = $relativeLocation;				
			}
			else
			{
				$baseSrc = $width > 40? "http://www.geonames.org/flags/x/" : "http://www.geonames.org/flags/m/";				
				$content = file_get_contents($baseSrc.$flag.$extension);
				file_put_contents($localFlag, $content);
				$imgSrc = $relativeLocation;
			}			
						
			return $imgSrc;
		}
	}
	
	public function GetShortLocation($lenght = 20)
	{
		if($this->SiglaPais != null && $this->SiglaPais != "")
		{
			return $this->GetLocalSubString($lenght)." (".$this->SiglaPais.")";
		}
		else
		{
			return $this->GetLocalSubString($lenght);
		}
	}
}

?>