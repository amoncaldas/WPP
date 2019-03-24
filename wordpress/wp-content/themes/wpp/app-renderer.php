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
            } elseif (RENDER_AUDIENCE === "MANIFEST") {
                $manifest = $this->getManifest();
                header('Content-Type: application/json');
                echo $manifest;
                exit;
            }         
        }	
    }

    /**
     * Build the app manifest json
     *
     * @return String
     */
    public function getManifest() {
        $main_image_url = get_option("wpp_site_relative_logo_url");
        $ext = "";
        if ($main_image_url && strlen($main_image_url) > 5) {
            $ext = pathinfo($og_image_url, PATHINFO_EXTENSION);
        }
        $locale = get_request_locale();
        $short_name = get_option("wpp_short_name");
        $title = get_bloginfo("name");
        $home_section = get_home_section();
        $bg_color = get_post_meta($home_section->ID, "bg_color", true);

        $manifest = new stdClass();
        $manifest->short_name = $short_name;
        $manifest->name = $title;
        $icons = [];
        $icon192 = new stdClass();
        $icon192->src = $main_image_url;
        $icon192->type = "image/$ext";
        $icon192->sizes = "192x192";
        $icons[] = $icon192;

        $icon512 = new stdClass();
        $icon512->src = $main_image_url;
        $icon512->type = "image/$ext";
        $icon512->sizes = "512x512";
        $icons[] = $icon512;

        $manifest->icons = $icons;
        $manifest->start_url = "/?l=$locale";
        $manifest->background_color = $bg_color;
        $manifest->display = "standalone";
        $manifest->scope = "/";
        $manifest->theme_color = $bg_color;

        return json_encode($manifest);
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
        $locale = get_request_locale();
        $description = get_bloginfo("description");
        $ext = "";

        $main_image_url = get_option("wpp_site_relative_logo_url");
        if ($main_image_url && strlen($main_image_url) > 5) {
            $main_image_url = network_site_url(trim($main_image_url));
            $ext = pathinfo($og_image_url, PATHINFO_EXTENSION);
        }
        
        $post_id = $this->getPostId();
        if ($post_id) {
            $post = get_post($post_id);
            if ($post) {
                $title = $post->post_title. " | ". $title;
                $main_image_url = get_the_post_thumbnail_url($post_id);
                $description = get_sub_content($post->post_content, 200);
            }
        }
        $header_injection = "<title>$title</title>";  
        $header_injection = "<link rel='manifest' href='/manifest.json'>";        
        $header_injection .= "<link rel='image_src' type='image/$ext' href='$main_image_url' />";
        $header_injection .= "<meta property='og:title' content='$title' />";        
        $header_injection .= "<meta property='og:image' content='$main_image_url' />";
        $header_injection .= "<meta property='og:locale' content='$locale' />";
        $header_injection .= "<meta property='og:description' content='$description' />";

        $faveico_path = get_option("wpp_faveico");
        if ($faveico_path) {
            $faveico_full_url = network_site_url($faveico_path);
            $header_injection .= "<link rel='shortcut icon' href='$faveico_full_url' type='image/x-icon' />";
        }

        $url = network_site_url($_SERVER["REQUEST_URI"]);
        $header_injection .= "<link rel='canonical' id='page_canonical' href='$url' />";
        $header_injection .= "<meta property='og:url' content='$url' />";

        $skeleton = preg_replace("/<title[^>]*>.*?<\/title>/i", $header_injection, $skeleton);
        return $skeleton;
    }

    /**
     * Get the webapp html
     *
     * @return string
     */
    public function getWebAppHtml () {
        $router_mode = get_option("wpp_router_mode");
        $REQUEST_URI = strtok($_SERVER["REQUEST_URI"],'?');
        if ( $REQUEST_URI !== "/" && $router_mode === "hash") {
            $uri = $_SERVER["REQUEST_URI"];
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
        $REQUEST_URI = strtok($_SERVER["REQUEST_URI"],'?');
        // render the section that is the some page for the request locale
        if ($REQUEST_URI === "/") {
            $home_section = $this->get_home_section_post();
            if($home_section) {
                $post_or_page_object = $home_section;
                define('IS_SECTION', TRUE);
                define('WPP_TITLE', bloginfo('name'));          
            } 
        } else { // Define the single page or post
            $uri = trim($REQUEST_URI, '/');
            $uri_segments = explode("/", $uri);
            $uri_segments = array_map('trim', $uri_segments);
            $last_segment = $uri_segments[count($uri_segments) - 1 ];

            if (isset($last_segment) && $last_segment !== "") {
                $post_or_page_object = $this->get_single($last_segment);
                if (!$post_or_page_object) {
                    $this->set_archive_rendering_data($last_segment, $uri_segments);
                } else {
                    define('WPP_TITLE', ucfirst($post_or_page_object->post_title) . " | ". get_bloginfo('name'));
                    if ($post_or_page_object->post_type === SECTION_POST_TYPE) {
                        define('IS_SECTION', true);
                    }
                }
            }
        }

        // If a page or post is defined, set it as global object
        if(isset($post_or_page_object)) {
            // Define OG:DECRIPTION
            $this->set_content_wpp_og_constans($post_or_page_object);
            
            // Define the template
            if(defined("IS_SECTION")) {
                global $section;
                $section = $post_or_page_object;                
                require_once("section.php");
            } else {   
                global $post;
                $post = $post_or_page_object;
                require_once("single.php");
            }
        } else { // if not, or we are in an archive or it is 404
            if (defined('RENDER_ARCHIVE_POST_TYPE')){
                $this->set_default_og_image_constants();
                define('WPP_OG_DESCRIPTION', get_bloginfo("description"));
                require_once("archive.php");
            } else {
                require_once("404.php");
            }
        }   
    }

    /**
     * Set archive rendering contansts
     *
     * @param String $last_segment
     * @param Array $uri_segments
     * @return void
     */
    public function set_archive_rendering_data ($last_segment, $uri_segments) {
        $post_type = get_post_type_by_endpoint($last_segment);
        if($post_type !== false){
            $archive_title = ucfirst(get_post_type_title_translation($post_type, get_request_locale()));
            define('WPP_TITLE', $archive_title . " | ". get_bloginfo('name'));
            define('RENDER_ARCHIVE_POST_TYPE', $post_type);
            if (count($uri_segments) > 1) {
                $first_segment = $uri_segments[0];
                global $wpdb;
                $sql = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' && post_type = '".SECTION_POST_TYPE."' && post_name = '".$first_segment."'";
                $post_id = $wpdb->get_var($sql);
                if ($post_id) {
                    define('SECTION_ID', $post_id);
                }
            }
        }
    }

    /**
     * Set the default WPP_OG_URL and WPP_OG_IMAGE_EXT constants
     *
     * @return void
     */
    public function set_default_og_image_constants() {        
        $ext = "";
        $main_image_url = get_option("wpp_site_relative_logo_url");
        if ($main_image_url && strlen($main_image_url) > 5) {
            $main_image_url = network_site_url(trim($main_image_url));
            $ext = pathinfo($main_image_url, PATHINFO_EXTENSION);
        }
        define('WPP_OG_URL', $main_image_url);
        define('WPP_OG_IMAGE_EXT', $ext);        
    }

    /**
     * Set the og WPP_OG_DESCRIPTION, WPP_OG_URL and WPP_OG_IMAGE_EXT  constants
     *
     * @param [type] $post
     * @return void
     */
    public function set_content_wpp_og_constans($post) {
        $content = strip_tags(apply_filters('the_content', $post->post_content));
        if ($content && strlen($content) > 1) {
            $description = get_sub_content($content, 200);
            define('WPP_OG_DESCRIPTION', $description);     
        } else {
            define('WPP_OG_DESCRIPTION', get_bloginfo("description"));
        }  

        $featured_image_url = get_the_post_thumbnail_url($post->ID);
        if ($featured_image)  {
            define('WPP_OG_URL', $featured_image_url);
        } else {
            $this->set_default_og_image_constants();
        }
    }
    
    /**
     * Get the post url slug translated, if available
     *
     * @param String $post_url_slug
     * @param String $lang
     * @return String|false
     */
    public function get_post_type_translation($post_url_slug, $lang) {
        $dictionary = get_option("wpp_post_type_translations", "{}");
        $dictionary = str_replace("\\", "", $dictionary);
        $dictionary = json_decode($dictionary, true);

        if (!isset($dictionary[$post_url_slug])) {
            return false;
        } elseif (!isset($dictionary[$post_url_slug][$lang])) {
            return false;
        } elseif (!isset($dictionary[$post_url_slug][$lang]["path"])) {
            return false;
        } else {
            return $dictionary[$post_url_slug][$lang]["path"];
        }
    }

    /**
     * Get home section for the considering the request locale
     *
     * @return void
     */
    public function get_home_section_post() {
        // Get the request locale
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
        if (is_numeric($last_uri_segment)) {
            $post_object = get_post($last_uri_segment);
            
        } else {
            global $wpdb;
            $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' && post_name = '".$last_uri_segment."'");
            if ($post_id) {
                $post_object = get_post($post_id);
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
        $REQUEST_URI = strtok($_SERVER["REQUEST_URI"],'?');
        $uri_parts = explode("/",  $REQUEST_URI);
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
        $REQUEST_URI = strtok($_SERVER["REQUEST_URI"],'?');
        if ($REQUEST_URI !== "/") {
            $uri_parts = explode("/", $REQUEST_URI);
            $last_url_segment = $uri_parts[count($uri_parts) -1];
            if(is_numeric($last_url_segment)) {
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