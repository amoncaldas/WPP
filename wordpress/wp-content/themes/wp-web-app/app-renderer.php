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
            }
            elseif (RENDER_AUDIENCE === 'CRAWLER_BROWSER') {
                $html = $this->getNoJSHtml();
                echo $html;
            }
            exit;
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
        $og_image_url = "image_path";
        
        $post_id = $this->getPostId();
        if ($post_id) {
            $title = get_the_title($post_id);
            $og_image_url = get_the_post_thumbnail_url($post_id);
        }
        $skeleton = str_replace("{{title}}", $title, $skeleton);
        $skeleton = str_replace("{{og_image}}", $og_image_url, $skeleton);
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
     * Get crawler html
     *
     * @return string
     */
    public function getNoJSHtml () {        
        $section = $this->getSection();
        if ($section) {
            $section_id = $section->ID;
        }            
        
        $last_url_segment = $uri_parts[count($uri_parts) -1];
        if(is_integer($last_url_segment)) {
            $post_id = $last_url_segment;
        }
        $skeleton = $this->getHTMLSkeleton();
        $html = str_replace('<div id="app"></div>', "", $skeleton);
        $html = str_replace('</body>', "", $html);
        $html = str_replace('</html>', "", $html);
        
        // add content there
        $html .= "</body>";
        $html .= "</html>";
        return $html;
    }

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