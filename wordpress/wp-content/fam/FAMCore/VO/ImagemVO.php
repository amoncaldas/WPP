<?php

/**
 * class ImagemVO
 *
 * Description for class ImagemVO
 *
 * @author:
*/

require_once( ABSPATH . '/FAMCore/VO/MediaVO.php' );

class ImagemVO extends MediaVO  {	
		
	public $ImageFullSrc;
	public $ImageFullWidth;
	public $ImageFullHeight;	
	
	public $ImageLargeSrc;
	public $ImageLargeWidth;
	public $ImageLargeHeight;	
	
	public $ImageMediumSrc;
	public $ImageMediumWidth;
	public $ImageMediumHeight;	
	
	public $ImageThumbSrc;
	public $ImageThumbWidth;
	public $ImageThumbHeight;
	
	public $ImageGaleryThumbSrc;
	public $ImageGaleryThumbWidth;
	public $ImageGaleryThumbHeight;
	
	public $ImageCroppedSrc;
	
	public $Location;	
	
	/**
	 * ImagemVO constructor
	 *
	 * @param $id
	 */
	function ImagemVO($id = null) {
		if($id != null)
		{			
			$post = get_post($id);	
					
			if(CheckUploadedFileExists($post->guid))
			{
				parent::MediaVO($id);	
				$this->PopulateImageVO($this,$post);				
			}						
		}
	}
	
	public function PopulateImageVO(&$imagemVO, $post)
	{
		
		$imageOriginal = wp_get_attachment_image_src($post->ID, "full", false);			
		$this->ImageFullSrc = $this->ConstructImagemUrl($imageOriginal[0]);				
			
		$this->ImageFullWidth = $imageOriginal[1];
		$this->ImageFullHeight = $imageOriginal[2];
			
		$imageLarge = wp_get_attachment_image_src($post->ID, "large", false);	
		
				
		$this->ImageLargeSrc = $this->ImageFullSrc;			
		$this->ImageLargeWidth = $this->ImageFullWidth;
		$this->ImageLargeHeight = $this->ImageFullHeight;
		
			
		$imageMedium = wp_get_attachment_image_src($post->ID,"medium",false);		
		$this->ImageMediumSrc = $this->ConstructImagemUrl($imageMedium[0]);
		$this->ImageMediumWidth = $imageMedium[1];
		$this->ImageMediumHeight = $imageMedium[2];
			
		$imageThumb = wp_get_attachment_image_src($post->ID, "tinythumb", false);
		$src = $imageThumb[0];
		
		//work around
		$ext = pathinfo($src, PATHINFO_EXTENSION);
			
		if(strpos($src,"-120x70.".$ext) === false)
		{
			$src = str_replace('-119x69.'.$ext,'-120x70.'.$ext, $src);
			if(!CheckUploadedFileExists($src))
			{
				$src = str_replace('-120x70.'.$ext,'-119x69.'.$ext, $src);
			}
		}

		$this->ImageThumbSrc = $this->ConstructImagemUrl($src);


		$this->ImageThumbWidth = $imageThumb[1];
		$this->ImageThumbHeight = $imageThumb[2];

		$imageGalleryThumb = wp_get_attachment_image_src($post->ID, "galerythumb", false);
		$src = $imageGalleryThumb[0];
       
		$ext = pathinfo($src, PATHINFO_EXTENSION);
		if(strpos($src,"-190x140.".$ext) === false)
		{
			$src = str_replace('-191x141.'.$ext,'-190x140.'.$ext, $src);
			if(!CheckUploadedFileExists($src))
			{
				$src = str_replace('-190x140.'.$ext,'-191x141.'.$ext, $src);
			}
			if(!CheckUploadedFileExists($src))
			{
				$src =  $imageGalleryThumb[0];
			}
		}

		$this->ImageGaleryThumbSrc = $this->ConstructImagemUrl($src);

		$this->ImageGaleryThumbWidth = $imageGalleryThumb[1];
		$this->ImageGaleryThumbHeight = $imageGalleryThumb[2];


	}

	public function GetIntermediateImageSrc()
	{
		$croppedSrc = null;
		try {			
				$croppedSrc = CropImage($this, 600, 0.7);
		}	
		catch(Exception $e){
			
		}
		return $croppedSrc;
	}
	
	
	
	private function ConstructImagemUrl($originalUrl)
	{		
		//http://fazendoasmalas.com/fimdomundo/files/2013/09/DSCN1533.jpg
		//http://fazendoasmalas.com/fimdomundo/wp-content/blogs.dir/10/files/2013/09/DSCN1533.jpg"
		
        //fix the malformed url when building main site images url in subsites
        if(get_current_blog_id() == 1 && strpos($originalUrl,"/wp-content/") > -1){
            $url_parts = explode("/wp-content/",$originalUrl);
            $originalUrl = network_home_url( '/' )."wp-content/".$url_parts[1];
        }
        
		if(strpos($originalUrl,"/wp-content/blogs.dir/") > -1)
		{
			$url_parts = explode("/files/",$originalUrl);
			$originalUrl =  get_bloginfo('url')."/files/".$url_parts[1];
			
		}
		if(strpos($originalUrl,"/wp-content/uploads/") > -1 && get_current_blog_id() > 1)
		{			
			$url =  str_replace("/wp-content/uploads/", "/files/", $originalUrl);
			$imgUrlparts = explode('files',$url);
			$guiidParts = explode("files",$this->Content);
			$guiidBaseUrl = $guiidParts[0];		
			$baseUrl = $imgUrlparts[0];			
			$url = str_replace($baseUrl,$guiidBaseUrl,$url);	
			return $url;
			
		}
		
		return  $originalUrl;	
		
		
		
	}
}
?>