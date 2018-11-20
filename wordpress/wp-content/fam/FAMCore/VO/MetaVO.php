<?php

/**
 * class MetaVO
 *
 * Description for class MetaVO
 *
 * @author:
*/
class MetaVO  {

	public $MediaOGHtml;
	public $ImgSrc;
	public $DescriptionText;
	public $KeyWords;
	public $Title;
	public $Screen;
	public $Latitude;
	public $Longitude;
	public $Locality;
	public $Country;
	public $Author;
	public $Cannonical;
	public $BodyStyle;
	public $SkeenStyleSrc;
	public $SnowEffect;
	public $Mundo;
	public $LatOGHtml;
	public $LongOGHtml;
	public $Autor;
	public $AutorFacebookUrlOGHtml;
	public $ArticlePublisher;
	public $OGType;
	public $SkeenFolder;
	public $prevPage;
	public $nextPage;
	
	
	/**
	 * MetaVO constructor
	 *
	 * @param 
	 */
	
	function MetaVO() {
		$this->Cannonical =  "http://".$_SERVER["SERVER_NAME"].str_replace(":",";",$_SERVER["REQUEST_URI"]);
		if(strpos($this->Cannonical,"?") > -1)
		{
			$this->Cannonical = strtok($this->Cannonical,'?');
			if($_GET["media"] != null)
			{
				$this->Cannonical .= "?media=".$_GET["media"];
			}
		}
		
		$skeenName = (get_option('tema_fam') == false)? 'adventure': get_option('tema_fam');
		
		$this->SkeenStyleSrc = "/wp-content/themes/skeens/".$skeenName."/style.css";
		$this->SkeenFolder = "/wp-content/themes/skeens/".$skeenName."/";
		
		$this->BodyStyle = "";
		if(get_option('background_fam') == false){
			$this->BodyStyle .=  "background-image:url(/wp-content/themes/images/backgrounds/infinity_road.jpg);";
		} else{
			$this->BodyStyle .= "background-image:url(/wp-content/themes/images/backgrounds/".get_option('background_fam').".jpg);";
		}
		if(get_option('background_position_fam') == false){
			$this->BodyStyle .= " background-position:center top;";
		} else{
			$this->BodyStyle .= " background-position:".get_option('background_position_fam').";";
		}
		if(get_option('show_snow_fam') == "yes"){
				$this->SnowEffect = '
				<script type="text/javascript" src="/wp-content/themes/js/jquery.snow.js"></script>
				<script  type="text/javascript">
					$(document).ready( function(){
						$.fn.snow();
					});
				</script>';
		}
		
		if(get_option('fam_mundo') == false){
			$this->Mundo .= " style='background:url(/wp-content/themes/images/mundos/mundo.png);'";
		} else{
			$this->Mundo .= "style='background:url(/wp-content/themes/images/mundos/".get_option('fam_mundo').".png);'";
		}
		
		//paged seo infinity scroll data
		global $postCount;
		$paged_type = get_paged_archive_type();
		$postCount = wp_count_posts($paged_type)->publish;		
		$page = $_GET["page"];
		$this->nextPage = '';
		$this->prevPage = '';
		
		$url = $_SERVER["REQUEST_URI"];			
		$queryInit = strpos($url,"?");
		if($queryInit !== false)
		{
			$url = substr($url,0,$queryInit);
		}	
		$url =trim($url,"/");			
		
		if($paged_type != null && $page != null && is_numeric($page) )
		{
			if($page * 8 < $postCount)
			{
				$this->nextPage = '<link rel="next" href="/'.$url.'/?page='.($page+1).'" />';
			}
			if($page > 1)
			{
				$this->prevPage = '<link rel="prev" href="/'.$url.'/?page='.($page-1).'" />';
			}
		}
		elseif($postCount > 8)
		{
			$this->nextPage = '<link rel="next" href="/'.$url.'/?page=2" />';
		}
		
	}
}

?>