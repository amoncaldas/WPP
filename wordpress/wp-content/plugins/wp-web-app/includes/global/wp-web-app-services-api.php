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
  }
?>