<?php
 class AppRender {
    
    /**
     * render te html content according the audience
     */
    function __construct () {
		if (defined('RENDER_AUDIENCE')) {
            if (RENDER_AUDIENCE === 'USER_BROWSER') {
                $webapp = $this->getWebAppHtml();
                // Output the basic html app here
                // the content will be rendered via javascript
                echo $webapp;
                exit;                 
            }
            elseif (RENDER_AUDIENCE === 'CRAWLER_BROWSER') {
                $this->renderNoJSHtml();
            }            
        }	
    }
    /**
     * Het the html web page skeleton
     *
     * @return string
     */
    public function getHTMLSkeleton () {
        $skeleton = file_get_contents("/var/www/webapp/index.html");
        $skeleton = str_replace("=static/", "=/static/", $skeleton);
        $all_options = wp_load_alloptions();

        $head_inject = "";
        foreach ($all_options as $key => $value) {
            if ( strpos($key, "wpp_meta_") === 0) {
                $meta_property_name = str_replace("wpp_meta_", "", $key);
                $head_inject .= "<meta property='$meta_property_name' content='$value'>";
            }
        }
        $head_inject .= "</head>";

        $skeleton = str_replace("</head>", $head_inject, $skeleton);

        $title = get_bloginfo("name");
        $og_image_url = network_site_url(trim(get_option("wpp_site_relative_logo_url")));
        
        $post_id = $this->getPostId();
        if ($post_id) {
            $title = get_the_title($post_id). " | ". $title;
            $og_image_url = get_the_post_thumbnail_url($post_id);
        }
        $header_injection = "<title>$title</title>";
        $ext = pathinfo($og_image_url, PATHINFO_EXTENSION);
        $header_injection .= "<link rel='image_src' type='image/$ext' href='$og_image_url' />";
        $header_injection .= "<meta property='og:title' content='$title' />";
        $header_injection .= "<meta property='og:image' content='$og_image_url' />";

        $url = network_site_url($_SERVER["REQUEST_URI"]);
        $header_injection .= "<link rel='canonical' id='page_canonical' href='$url' />";

        $skeleton = preg_replace("/<title[^>]*>.*?<\/title>/i", $header_injection, $skeleton);
        return $skeleton;
    }

    /**
     * Get the webapp html
     *
     * @return string
     */
    public function getWebAppHtml () {
        if ($_SERVER["REQUEST_URI"] !== "/") {
            return "<html><title>".get_bloginfo("name")."</title><script> window.location = '/#$uri' </script></html>";
        }
        return $this->getHTMLSkeleton();
    }

    /**
     * Render crawler html
     *
     * @return void
     */
    public function renderNoJSHtml () {
        // render the section that is the some page for the request locale
        if ($_SERVER["REQUEST_URI"] === "/") {
            $home_section = $this->get_home_section_post();
            if($home_section) {
                $post_or_page_object = $home_section;
                define('IS_HOME_SECTION', TRUE);          
            } 
        } else { // Define the single page or post
            $uri = ltrim($_SERVER["REQUEST_URI"], '/');
            $uri_segments = explode("/", $uri);
            $uri_segments = array_map('trim', $uri_segments);
            $last_segment = $uri_segments[count($uri_segments) - 1 ];

            $public_post_types = get_post_types(array("public"=>true));
            unset($public_post_types["attachment"]);

            if(in_array($last_segment, $public_post_types)){
                define('RENDER_ARCHIVE_POST_TYPE', $last_segment);
            } else {
                $post_or_page_object = $this->get_single($last_segment);
            }
        }

        // If a page or post is defined, set it as global object
        if($post_or_page_object) {
            global $section;
            $section = $post_or_page_object;
            //setup_postdata( $post);
            if(defined("IS_HOME_SECTION")) {
                require_once("index.php");
            } else {                
                require_once("single.php");
            }
        } else { // if not, or we are in an archive or it is 404
            if (defined('RENDER_ARCHIVE_POST_TYPE')){
                require_once("archive.php");
            } else {
                require_once("404.php");
            }
        }   
    }

    /**
     * Get home section post based on the request locale
     *
     * @return void
     */
    public function get_home_section_post() {
        // Get section home for the request locale
        $locale = get_request_locale();
    
        $args = array(
            "post_type"=> SECTION_POST_TYPE, 
            "post_status"=> "publish", 
            'tax_query' => array (
                array(
                    'taxonomy' => LOCALE_TAXONOMY_SLUG,
                    'field' => 'slug',
                    'terms' => array($locale)
                ),
            ),
            'meta_query' => array(
				array(
					'key'=> SECTION_TYPE_FIELD_SLUG,
					'value'=> SECTION_POST_HOME_FIELD_VALUE
				)
			),
        );
        $home_sections = get_posts($args);
        if(is_array($home_sections) && count($home_sections) > 0) {
            $home_section = $home_sections[0];
            return $home_section;             
        } 
    }


    /**
     * Get the single post based on the last url segment (id or page name)
     *
     * @param string $last_uri_segment
     * @return void
     */
    public function get_single($last_uri_segment) {
        $post_object = null;
        if (is_integer($last_uri_segment)) {
            $post_object = get_post($last_uri_segment);
            
        } else {
            $post_object = get_page_by_path($last_uri_segment);
            if(!$post_object) {
                $posts = get_posts(array("name"=>$last_uri_segment));
                if(is_array($posts) && count($posts) > 0) {
                    $post_object = $posts[0];
                }
            }
        }
        return $post_object;
    }

    /**
     * Get the section for the current request
     *
     * @return void
     */
    public function getSection() {
        $uri_parts = explode("/", $_SERVER["REQUEST_URI"]);
        $sections = get_posts( array( 'post_type' => 'section', 'post_name'  => $uri_parts[0]));		
        if (count($sections) > 0) {
            return $sections[0];
        }
    }

    /**
     * Get current url post ID
     *
     * @return Integer
     */
    public function getPostId() {
        if ($_SERVER["REQUEST_URI"] !== "/") {
            $uri_parts = explode("/", $_SERVER["REQUEST_URI"]);
            $last_url_segment = $uri_parts[count($uri_parts) -1];
            if(is_integer($last_url_segment)) {
                return $last_url_segment;
            } else {
                $sections = get_posts( array( 'post_type' => 'section', 'post_name'  => $uri_parts[0]));		
                if (count($sections) > 0) {
                    return $sections[0]->ID;
                } else {
                    $page = get_page_by_path( $uri_parts[0]);		
                    if ($page !== null) {
                        return $page->ID;
                    }
                }
            }
        }
    }
}

new AppRender();