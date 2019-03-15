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

      register_rest_route(WPP_API_NAMESPACE."/content", '/search', array(
        array(
          'methods'  => "GET",
          'callback' => array($this, 'get_search' ),
        )
      ));

      add_filter( 'rest_pre_insert_comment', array($this, 'wpp_pre_insert_comment'), 10, 2 );
    }

    /**
     * Check if the captach is correct before insert a comment
     *
     * @param Array $prepared_comment
     * @param Object $request
     * @return void
     */
    public function wpp_pre_insert_comment( $prepared_comment, $request ){
      $recaptchaToken = $request->get_param('recaptchaToken');
      $validCaptcha = validate_captcha($recaptchaToken);
      if ($validCaptcha === true) {
        return $prepared_comment;
      }
      return new WP_Error( 'invalid_capatch', "invalid_capatch" );  
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
      $posts = get_posts($args); 

      // If there is not related, try to get posts with same category
      if (count($posts) === 0) {
        unset($args["post_parent"]);        
        unset($args["post__in"]);     
        $content_id = $request->get_param('contentId');   
        $args['category__in']  = wp_get_post_categories( $content_id);
        $posts = get_posts($args);
      }

      $posts = $this->prepareRestData($posts, true);
      $response = $this->prepareRestResponseWithPagination($posts, $args);
      return $response;		
    } 
    
    /**
     * Get the posts from all post ypes
     *
     * @param [type] $request
     * @return WP_REST_Response
     */
    public function get_search( $request ) {
      $public_post_types = get_post_types(array("public"=>true));
      unset($public_post_types["attachment"]);
      unset($public_post_types["member"]);

	    $args = $this->prepareSearchArgs($request, $public_post_types);
      $posts = get_posts( $args ); 

      $posts = $this->prepareRestData($posts, true);
      $response = $this->prepareRestResponseWithPagination($posts, $args);
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

      $meta_query = $this->not_searchable_meta_query();

      $args = array(
        'paged' => $paged,
        'posts_per_page' => $per_page,            
        'post_type' => $public_post_types,
        'meta_query' => $meta_query,
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
     * Prepare related args based on the request
     *
     * @param Object $request
     * @return Array
     */
    public function prepareSearchArgs($request, $public_post_types) {
      // Receive and set the page parameter from the $request for pagination purposes
      $paged = $request->get_param( 'page' );
      $paged = (isset($paged) && !empty($paged)) ? $paged : 1;
      
      // Receive and set the per_page parameter from the $request for pagination purposes
      $per_page = $request->get_param( 'per_page' );
      $per_page = (isset($per_page) && !empty($per_page)) ? $per_page : 10;

      $search_term = esc_sql( like_escape($request->get_param('s')));

      $meta_query = $this->not_searchable_meta_query();
     
      $args = array(
        'paged' => $paged,
        'posts_per_page' => $per_page,            
        'post_type' => $public_post_types,
        'meta_query' => $meta_query,
        's' => $search_term
      ); 
      

      // When a section was specified, search 
      // only posts within this section
      $section = $request->get_param('section');
      if (isset($section) && is_numeric($section)) {
        $args['post_parent'] = $section;
      } 
      
      // TODO: not working when passing ptype!
      // I a post type was specified, search 
      // only posts of the passed post type
      $p_type = $request->get_param('ptype');
      if (isset($p_type)) {
        $args['post_type'] = $p_type;
      }

      // Receive and set the exclude parameter from the $request
      $exclude = $request->get_param( 'exclude' );
      $exclude = (isset($exclude) && !empty($exclude)) ? $exclude : [];
      if (!is_array($exclude) && isset($exclude)) {
        $exclude = explode(",", $exclude);
      }
      if (isset($exclude)) {
        $args["post__not_in"] = $exclude;
      }
      
      return $args;
    }

    /**
     * Returns the not searchable meta query array
     *
     * @return Array
     */
    public function not_searchable_meta_query() {
      $meta_query = array(
        'relation' => 'OR',
        array(
          'key'     => 'not_searchable',
          'value'   => 0,
          'compare' => '=',
        ),
        array(
          'key'     => 'not_searchable',
          'value'   => '', // This is ignored, but is necessary
          'compare' => 'NOT EXISTS',
        ),
      );
      return $meta_query;
    }

    /**
     * Prepare wp rest response for related items
     *
     * @param Array $items
     * @param Array $post_types
     * @return WP_REST_Response
     */
    public function prepareRestData($posts, $embedded = false) {    
      $rest_posts = [];
      foreach( $posts as $post ) {
          if ($embedded) {
            // Resolve author data
            $embedded = new stdClass();
            $author =new stdClass();
            $author->id = $post->post_author;
            $author->name = get_author_name($post->post_author);
            $author->avatar_urls = ["96"=>get_avatar_url($post->post_author, 96)];
            $embedded->author = [$author];
  
            // Resolve featured media data
            $feaured_media_key = "wp:featuredmedia";
            $featured_media = new stdClass();
            $featured_media->id = get_post_thumbnail_id($post->ID);
            if ($featured_media->id) {
              $media_data = wp_get_attachment_image_src($featured_media->id, "full", false );
              $details = new stdClass();
              $details->width = $media_data[1];
              $details->height = $media_data[2];
              $details->sizes = [];
              // Full image
              $details->sizes["full"] = [
                "source_url"=>$media_data[0],
                "width"=>$media_data[1],
                "height"=>$media_data[2]
              ];

              // Other sizes
              foreach(get_intermediate_image_sizes() as $size){
                if( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ){
                  $media_data = wp_get_attachment_image_src($featured_media->id, $size);
                  $details->sizes[$size] = [
                    "source_url"=>$media_data[0],
                    "width"=>$media_data[1],
                    "height"=>$media_data[2]
                  ];
                } 
              }

              $featured_media->media_details = $details;
              $featured_media->post_title = get_the_title($featured_media->id);
              $featured_media->type = "attachment";
              $featured_media->source_url = $media_data[0];
              $embedded->$feaured_media_key = [$featured_media];              
            }
            // Add embedded data
            $post->_embedded = $embedded;
          }

          // Resolve locale
          $terms = get_the_terms($post->ID, LOCALE_TAXONOMY_SLUG );
          $term = array_shift( $terms );
          $post->locale = $term->slug;
          $post->link = get_post_permalink($post->ID);

          // create rest post data like (without the 'post_' prefix in the properties)
          $rest_post = new stdClass();
          foreach ($post as $key => $value) {
            $cleaned_key = str_replace("post_", "", $key);
            $rest_post->$cleaned_key = $value;
          }
          // Add the cleaned post object to the array to be returned
          $rest_posts[] = $rest_post;
      }
      return $rest_posts;      
    }

    /**
     * Prepare the posts data for rest response includng pagination
     *
     * @param [type] $posts
     * @param [type] $post_types
     * @param [type] $posts_per_page
     * @return void
     */
    public function prepareRestResponseWithPagination($posts, $args) {
      $wp_rest_response = rest_ensure_response($posts);     

      // Prepare pagination
      unset($args["paged"]);
      $posts_per_page = intval($args["posts_per_page"]);
      unset($args["posts_per_page"]);

      $posts_to_count = new WP_Query($args);
      $total = $posts_to_count->post_count; 
      $wp_rest_response->header("X-WP-Total", $total);

      $pages = intval($total / $posts_per_page);
      if (($total % $posts_per_page ) > 0) {
        $pages++;
      }
      $pages = $pages > 0 ? $pages : 1;
      $wp_rest_response->header("X-WP-TotalPages", $pages);

      // Return wp rest response
      return $wp_rest_response;
    }
  }
?>