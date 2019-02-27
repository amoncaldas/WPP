<?php

	class WppServicesApi {
		
		function __construct() {
			add_filter( 'rest_api_init', array( $this, 'register_routes' ) );
		}
		
      /**
     * Register routes for WP API v2.
     *
     * @since  1.2.0
     */
    public function register_routes() {
      register_rest_route(WPP_API_NAMESPACE."/services", '/options', array(
        array(
          'methods'  => "GET",
          'callback' => array($this, 'get_options' ),
        )
      ));

      register_rest_route(WPP_API_NAMESPACE."/content", '/(?P<contentId>[0-9]+)/related', array(
        array(
          'methods'  => "GET",
          'callback' => array($this, 'get_related' ),
        )
      ));
    }

    /**
     * Get all wpp options, format them and return
     *
     * @return void
     */
    public function get_options($request) {
      $all_options = wp_load_alloptions();

      $wpp_options = [];
      foreach ($all_options as $key => $value) {
        if($key === "wpp_site_relative_logo_url")  {
          $value = network_site_url(trim($value));
        }
        if ( strpos($key, "wpp_meta_") === 0) {
          $clean_key =  $meta_property_name = str_replace("wpp_meta_", "", $key);
          $wpp_options[$clean_key] = $value;
        } 
        elseif ( strpos($key, "wpp_") === 0) {          
          $clean_key =  $meta_property_name = str_replace("wpp_", "", $key);

          $value =  $value === "yes" ? true : $value;
          $value =  $value === "no" ? false : $value;
          
          $value = str_replace("\\", "", $value);
          $json_as_array_value = json_decode($value, true);

          if ($json_as_array_value) {
            $value = $json_as_array_value;
          } elseif (strpos($value, ",") > -1) {              
            $value = explode(",", $value);              
          }          
         
          $wpp_options[$clean_key] = $value;
        }
      }
      $wpp_options["site_title"] = get_bloginfo("name");
      return new WP_REST_Response($wpp_options, 200); // OK
    }

    /**
     * Get the posts from all post ypes
     *
     * @param [type] $request
     * @return WP_REST_Response
     */
    public function get_related( $request ) {

      $public_post_types = get_post_types(array("public"=>true));
      unset($public_post_types["attachment"]);
      unset($public_post_types[SECTION_POST_TYPE]);

	    $args = $this->prepareRelatedArgs($request, $public_post_types);
    
      // Get the posts using the public post types
      $items = get_posts($args); 

      // If there is not related, try to get posts with same category
      if (count($items) === 0) {
        unset($args["post_parent"]);        
        unset($args["post__in"]);     
        $content_id = $request->get_param('contentId');   
        $args['category__in']  = wp_get_post_categories( $content_id);
        $items = get_posts($args);
      }

      // $parent_id = get_post_meta($post_id, SECTION_POST_TYPE, true);

      $response = $this->prepareRelatedResponse($items, $public_post_types, $args);
      return $response;		
	  } 

    /**
     * Prepare related args based on the request
     *
     * @param Object $request
     * @return Array
     */
    public function prepareRelatedArgs($request, $public_post_types) {
      // Receive and set the page parameter from the $request for pagination purposes
      $paged = $request->get_param( 'page' );
      $paged = (isset($paged) && !empty($paged)) ? $paged : 1;
      
      // Receive and set the per_page parameter from the $request for pagination purposes
      $per_page = $request->get_param( 'per_page' );
      $per_page = (isset($per_page) && !empty($per_page)) ? $per_page : 10;

      $args = array(
        'paged' => $paged,
        'posts_per_page' => $per_page,            
        'post_type' => $public_post_types
      );

      $content_id = $request->get_param('contentId');
      $exclude = [$content_id];

      // Receive and set the exclude parameter from the $request
      $additional_exclude = $request->get_param( 'exclude' );
      $additional_exclude = (isset($exclude) && !empty($exclude)) ? $exclude : [];
      if (!is_array($additional_exclude) && isset($additional_exclude)) {
        $additional_exclude = explode(",", $additional_exclude);
      }
      $exclude = array_merge($exclude, $additional_exclude);
      if (isset($exclude)) {
        $args["post__not_in"] = $exclude;
      }

      // Get the include aprameters from the content related saved in meta
      $include = get_post_meta($content_id, "related", true);
      if (isset($include) && $include !== "") {
        $args["post__in"] = $include;
      } else {
        // If there is not related, try to get posts with same category
        $post = get_post($content_id);
        $args['post_parent']  = $post->post_parent;      
      }
      
      return $args;
    }

    /**
     * Prepare wp rest response for related items
     *
     * @param Array $items
     * @param Array $post_types
     * @return WP_REST_Response
     */
    public function prepareRelatedResponse($items, $post_types, $args) {      
      foreach( $items as $post ) {
          $id = $post->ID; 
          $embedded = new stdClass();
          $author =new stdClass();
          $author->id = $post->post_author;
          $author->name = get_author_name($post->post_author);
          $author->avatar_urls = [get_avatar_url($post->post_author)];
          $embedded->author = [$author];

          $feaured_media_key = "wp:featuredmedia";
          $featured_media = new stdClass();
          $featured_media->id = get_post_thumbnail_id($post->ID);
          if ($featured_media->id) {
            $media_data = wp_get_attachment_image_src($featured_media->id, null, false );
            $details = new stdClass();
            $details->width = $media_data[1];
            $details->height = $media_data[2];
            $details->sizes = ["full" => ["source_url"=>$media_data[0]]];
            $featured_media->media_details = $details;
            $featured_media->post_title = get_the_title($featured_media->id);
            $featured_media->type = "attachment";
            $featured_media->source_url = $media_data[0];
            $embedded->$feaured_media_key = [$featured_media];
          }
          $post->_embedded = $embedded;

          $terms = get_the_terms($post->ID, LOCALE_TAXONOMY_SLUG );
          $term = array_shift( $terms );
          $post->locale = $term->slug;
          $post->link = get_post_permalink($post->ID);
      }
      
      $wp_rest_response = rest_ensure_response($items);     
      
      // Prepare pagination
      $posts_to_count = new WP_Query(array('post_type' => array($post_types)));
      $total = $posts_to_count->post_count; 
      $wp_rest_response->header("X-WP-Total", $total);

      $pages = intval($total / $args["posts_per_page"]);
      if (($total % $args["posts_per_page"] ) > 0) {
        $pages++;
      }
      $pages = $pages > 0 ? $pages : 1;
      $wp_rest_response->header("X-WP-TotalPages", $pages);

      // REturn wp rest response
      return $wp_rest_response;
    }
  }
?>