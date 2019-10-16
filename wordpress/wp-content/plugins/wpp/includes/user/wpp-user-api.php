<?php

/**
 * Wpp user Api
 *
 * @package WPP
 */


class WppUserApi {
  private $baseNamespace;

  public function __construct() {
    $this->baseNamespace = WPP_API_NAMESPACE;

    // Load WPP api routes/endpoints
    if (!defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {
      add_action('rest_api_init', array($this, 'extend_wp_api'));
      add_filter('jwt_auth_token_before_dispatch', array( $this, 'jwt_rest_auth_user_data'), 10, 2);
    }
  }

  /**
   * Get plugin namespace.
   *
   * @since 1.0.0
   * @return string
   */
  public function get_namespace() {
      return $this->baseNamespace."/user";
  }
  

  /**
   * Register WPP user api routes for WP API v2.
   *
   * @since  1.0.0
   */
  public function extend_wp_api() {
      register_rest_route( $this->get_namespace(), '/username-registered', array(
          array(
              'methods'  => "POST",
              'callback' => array( $this, 'is_username_registered' ),
          )
      ) );
      register_rest_route( $this->get_namespace(), '/email-registered', array(
          array(
              'methods'  => "POST",
              'callback' => array( $this, 'is_email_registered' ),
          )
      ) );
  
      register_rest_route( $this->get_namespace(), '/register', array(
          array(
              'methods'  => "POST",
              'callback' => array( $this, 'register_user' ),
          )
      ) );

      register_rest_route( $this->get_namespace(), '/activate/(?P<userId>[0-9]+)', array(
          array(
              'methods'  => "PUT",
              'callback' => array( $this, 'activate_account' ),
          )
      ) );

      register_rest_route( $this->get_namespace(), '/activate/resend/(?P<userId>[0-9]+)', array(
          array(
              'methods'  => "POST",
              'callback' => array( $this, 'resend_activation_email' ),
          )
      ) );
      
      register_rest_route( $this->get_namespace(), '/password/reset/request', array(
          array(
              'methods'  => "POST",
              'callback' => array( $this, 'request_password_reset' ),
          )
      ) );

      register_rest_route( $this->get_namespace(), '/password/reset/validate', array(
          array(
              'methods'  => "POST",
              'callback' => array( $this, 'validate_reset_key' ),
          )
      ) );

      register_rest_route( $this->get_namespace(), '/password/reset/(?P<key>[a-zA-Z0-9,.!_-]+)', array(
          array(
              'methods'  => "PUT",
              'callback' => array( $this, 'redefine_password' ),
          )
      ) );  
      
      register_rest_field(
        'user',
        'metas',
        array(
          'get_callback'      => function ($user, $field_name, $request) {
            return WppUser::get_user_meta_callback($user, $field_name, $request);
          },
          'update_callback'   => function ($metas, $user, $field_name) {
            return WppUser::update_user_meta_callback($metas, $user, $field_name);
          },
          'schema'            => null,
        )
      );

      add_filter('authenticate', array($this,'rest_api_authenticate_error_filter'), 99, 3);
  }

  /**
	 * Add user id in the jwt rest auth plugin response
	 *
	 * @param array $data
	 * @param Wp_User $user
	 * @return void
	 */
	public function jwt_rest_auth_user_data($data, $user) {
		$data["id"] = $user->ID;
		// replace the default user's display name for the user's first name, if available
		$firstName = get_user_meta($user->ID,'first_name',true);
		if (isset($firstName) && $firstName !== "") {
			$data["user_display_name"] = $firstName;
		}
		return $data;
	}

  /**
  * Remove html tags and undesired segment from authentication errors when running in rest api request
  *
  * @param object $user
  * @return object $user
  */
  public function rest_api_authenticate_error_filter($user){
    if(is_wp_error($user)) {
      foreach ($user->errors as $key => $value) {
        $message = strip_tags($user->errors[$key][0]);
        if ($key === "uec_error") {
          $undesiredMessageText = "Click to resend.";
          $message = str_replace($undesiredMessageText,"",$message);
        } elseif ($key === "invalid_username") {
          $message = "Invalid username/email or password";
        }

        $user->errors[$key][0] = $message;			 
      }
    }
    return $user;
  }

  

  /**
   * Checks if a username on the request is already registered or not
   * If the username is the same of the logged user, it will return false, because it is `available` for the user
   *
   * @param object $request
   * @return WP_REST_Response
   */
  public static function is_username_registered($request) {
      $registered = false;
      $logged_wp_user = wp_get_current_user();
      $wp_user = get_userdatabylogin($request->get_param('username'));

      if ($wp_user !== false) {
          if ($logged_wp_user->ID > 0) {
              $registered = $logged_wp_user->data->user_login !== $wp_user->data->user_login;
          } else {
              $registered = true;
          }            
      }        

      $data = ["data"=>["registered"=>$registered]];
      return new WP_REST_Response($data, 200); // OK
  }

  /**
   * Checks if a email on the request is already registered or not
   * If the email is the same of the logged user, it will return false, because it is `available` for the user
   *
   * @param object $request
   * @return WP_REST_Response
   */
  public static function is_email_registered($request) {
      $email = $request->get_param('email');
      $available = true;

      // if the email has a not valid domain we 
      // can stop the check here and return `true`
      // that means that the emails is not available
      // for registration
      if (!WppUser::validate_email_domain($email)) {
        $data = ["data"=>["registered"=>true]];
        return new WP_REST_Response($data, 200); // OK
      }
      $logged_wp_user = wp_get_current_user();
      $wp_user = get_user_by('email', $email);

      if ($wp_user !== false) {
        // If the user is editing the profile, we should consider 
        // the already registered email as available for editing
        if ($logged_wp_user->ID > 0) {
          $available = $logged_wp_user->data->user_email !== $wp_user->data->user_email;
        } else {
          $available = $wp_user->ID === 0;
        }            
      }        

      $data = ["data"=>["registered"=>!$available]];
      return new WP_REST_Response($data, 200); // OK
  }
  
  /**
   * Validate and register a user
   *
   * @param array $request (injected)
   * @return array new token
   */
  public static function register_user($request) {        
      $recaptchaToken = $request->get_param('recaptchaToken');
      $validCaptcha = validate_captcha($recaptchaToken);        
      
      if ($validCaptcha === true) {
        $result = WppUser::pre_validate_user_registration($request);
        if ($result !== true) {
          $data = ["message"=>$result];
          return new WP_REST_Response($data, 422); // CONFLICT 
        }

        $result = WppUser::create_wp_user($request);

        if ($result instanceof WP_User) {
          return new WP_REST_Response($result,201); // CREATED
        } else {
          $data = ["message"=>$result];
          return new WP_REST_Response($data, 409); // CONFLICT 
        }			
      } 
      $data = ["message"=>null];
      return new WP_REST_Response($data, 409); // CONFLICT
  }

  

  /**
   * Request the start of a password reset flow, which will send an email with a password reset link
   * to the user's registered email
   *
   * @param object $request
   * @return WP_REST_Response
   */
  public function request_password_reset($request) {
    try {
      $user_login = $request["login"];

      $wp_user = get_user_by("login", $user_login);
      if (!$wp_user) {
        $wp_user = get_user_by("email", $user_login);
      }

      $reset_key = WppUser::set_pass_reset_key_and_expiration($wp_user->ID);

      WppUser::send_password_reset_email($user_login, $reset_key);
      return new WP_REST_Response(null, 204); 
      
    } catch (\Exception $ex) {
      return new WP_REST_Response(null, 404);       
    }
  }

  /**
   * Validate if the key of password reset procedure is still valid to be used
   *
   * @param object $request
   * @return WP_REST_Response
   */
  public function validate_reset_key($request) {
    $reset_login = $request["login"];
    $reset_key = $request["key"];

    // Get the Wp_User object using the reset key and user login
    // This is a native wordpress function
    $wp_user = WppUser::get_user_by_pass_reset_key_login($reset_key, $reset_login);

    $statusCode = 404; // NOT FOUND - the pair user login and valid key was not found
    if ($wp_user) {
      $statusCode = 204;  // NO CONTENT - the request was processed, but there is not content to be returned 
    }
    return new WP_REST_Response(null, $statusCode);       
  }

  /**
   * Validate if the key of password reset procedure is still valid to be used
   *
   * @param object $request
   * @return WP_REST_Response
   */
  public function redefine_password($request) {
    // If passwords is empty, return an error
    if (empty($request['password1']) || empty($request['key'])) {
      return new WP_REST_Response(null, 400); // INVALID REQUEST
    }

    // If passwords mismatch, return an error
    if ($request['password1'] !== $request['password2']) {
      return new WP_REST_Response(null, 409); // CONFLICT
    }

    $reset_login = $request["login"];
    $reset_key = $request["key"];
    
    // Try to get the user by reset key and user login
    $wp_user = WppUser::get_user_by_pass_reset_key_login($reset_key, $reset_login, true);

    // If user variable contains an error, return and error
    if (is_wp_error($wp_user)) {
      return new WP_REST_Response(null, 404); // NOT FOUND - the pair user login and valid key was not found
    }
    
    // If we arrived until here it is because there were no errors
    // So we can update the user password
    reset_password($wp_user, $request['password1']);    
    return new WP_REST_Response(null, 204); // NO CONTENT - the request was processed, but there is not content to be returned
  }

  /**
   * Validate if the key of password reset procedure is still valid to be used
   *
   * @param object $request
   * @return WP_REST_Response
   */
  public function activate_account($request) {      
      $wp_user = get_userdata($request["userId"]);
      if ($wp_user === false) {
          return new WP_REST_Response(null, 404); // NOT FOUND - user not found by its id  
      }

      $now = time();
      $user_activation_code = WppUser::get_user_activation_code($wp_user->ID);
      $activation_expiration = WppUser::get_user_expiration_time($wp_user->ID);
      if ($now > $activation_expiration) {
          return new WP_REST_Response(null, 410); // GONE - activation code is not valid anymore because the user is already activated
      }

      if ($request["activationCode"] !== $user_activation_code) {
          return new WP_REST_Response(null, 409); // CONFLICT - activation code does not belong to specified user
      }        

      delete_user_meta($wp_user->ID, 'activation_code');
      delete_user_meta($wp_user->ID, 'activation_expiration');
      update_user_meta($wp_user->ID, 'status', "activated", "pending");

      return new WP_REST_Response(null, 204); // NO CONTENT - the request was processed, but there is not content to be returned   
  }

  /**
   * Resend email confirmation link
   *
   * @param object $request
   * @return void WP_REST_Response
   */
  public function resend_activation_email($request) {
    $wp_user = get_userdata($request["userId"]);
    if ($wp_user === false) {
        return new WP_REST_Response(null, 404); // NOT FOUND - user not found by its id  
    }
    delete_user_meta($wp_user->ID, 'activation_code');
    delete_user_meta($wp_user->ID, 'activation_expiration');
    delete_user_meta($wp_user->ID, 'status');

    // Set activation code
    $activation_code = WppUser::set_activation_and_expiration($user_id);
    
    // Send notifications to admin and to the user
    WppUser::send_new_user_notifications($wp_user->ID, $wp_user->data->user_email, $activation_code);

    return new WP_REST_Response(null, 204); // NO CONTENT - the request was processed, but there is not content to be returned
  }
}

