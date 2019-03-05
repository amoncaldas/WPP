<?php

/**
 * Fam UserData Wp Api
 *
 * @package FAM\FamUserData
 */


class WppUser {

    private $baseNamespace;

    public function __construct() {
        $this->baseNamespace = WPP_API_NAMESPACE;

        // Load ors api routes/endpoints
		if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {

			add_action('rest_api_init', array($this, 'register_routes'));
        } 
        add_action('authenticate', array($this, 'after_determine_user'), 20, 3);
        //add_filter('determine_current_user', array($this, 'wpp_authenticate'), 9);
    }

    /**
     * Check if the user is not with pending status before login
     *
     * @param [type] $username
     * @return WP_User|false
     */
    public function after_determine_user($wp_user, $username, $password) {        
        $status = get_user_meta($wp_user->ID, "status", true);
        if ($status === "pending") {
            return false;
        }
        return $wp_user;
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
     * The recaptcha validation url
     *
     * @var string
     */
    private static $recaptchaValidationUrl = "https://www.google.com/recaptcha/api/siteverify";
    

    /**
     * Register ors tyke api routes for WP API v2.
     *
     * @since  1.0.0
     */
    public function register_routes() {
        register_rest_route( $this->get_namespace(), '/username-registered', array(
            array(
                'methods'  => "POST",
                'callback' => array( $this, 'usernameRegistered' ),
            )
        ) );
        register_rest_route( $this->get_namespace(), '/email-registered', array(
            array(
                'methods'  => "POST",
                'callback' => array( $this, 'emailRegistered' ),
            )
        ) );
    
        register_rest_route( $this->get_namespace(), '/register', array(
            array(
                'methods'  => "POST",
                'callback' => array( $this, 'registerUser' ),
            )
        ) );

        register_rest_route( $this->get_namespace(), '/activate/(?P<userId>[0-9]+)', array(
            array(
                'methods'  => "PUT",
                'callback' => array( $this, 'activateAccount' ),
            )
        ) );

        register_rest_route( $this->get_namespace(), '/activate/resend/(?P<userId>[0-9]+)', array(
            array(
                'methods'  => "POST",
                'callback' => array( $this, 'resendActivationEmail' ),
            )
        ) );
        
        register_rest_route( $this->get_namespace(), '/password/reset/request', array(
            array(
                'methods'  => "POST",
                'callback' => array( $this, 'requestPasswordReset' ),
            )
        ) );

        register_rest_route( $this->get_namespace(), '/password/reset/validate', array(
            array(
                'methods'  => "POST",
                'callback' => array( $this, 'validateResetKey' ),
            )
        ) );

        register_rest_route( $this->get_namespace(), '/password/reset/(?P<login>[a-zA-Z0-9,.!_-]+)', array(
            array(
                'methods'  => "PUT",
                'callback' => array( $this, 'redefinePassword' ),
            )
        ) );        
    }

    /**
     * Get the google recaptcha secret
     *
     * @var string
     */
    static function getRecaptchaSecret (){
       $secret = get_option("recaptcha_secret", "6LcOa2MUAAAAAMjC-Nqnxcs1u4mX62PSrXeixDeI");
       return $secret;
    }

    /**
     * Checks if a username on the request is already registered or not
     * If the username is the same of the logged user, it will return false, because it is `available` for the user
     *
     * @param object $request
     * @return WP_REST_Response
     */
    public static function usernameRegistered($request) {
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
    public static function emailRegistered($request) {
        $email = $request->get_param('email');
        $available = true;

        // if the email has a not valid domain we 
        // can stop the check here and return `true`
        // that means that the emails is not available
        // for registration
        if (!self::validateEmailDomain($email)) {
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
    public static function registerUser($request) {        
        $recaptchaToken = $request->get_param('recaptchaToken');
        $validCaptcha = self::validateCaptcha($recaptchaToken);        
        
        if ($validCaptcha === true) {

            $result = self::preValidateUserRegistration($request);
            if ($result !== true) {
                $data = ["message"=>$result];
                return new WP_REST_Response($data, 422); // CONFLICT 
            }

            $result = self::createWPUser($request);

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
     * Creates a new wordpress user using the data provided in the request
     *
     * @param object $request
     * @return WP_User|string user object or the fail reason
     */
    private static function createWPUser($request) {
        try {
            $metas = $request->get_param('metas');

            $userData = array(
                'username' => $request->get_param('username'),
                'password' => $request->get_param('password'),
                "password2" => $request->get_param('confirmPassword'),      
                'email' => $request->get_param('email'),
                'website' => $request->get_param('website'),
                'nickname' => "",
                'display_name' => $request->get_param('first_name'),
                'first_name' => $request->get_param('first_name'),
                'last_name' => $request->get_param('last_name'),
                'bio' => "",
                'select_role' => "subscriber",
                "receive_news" => $request->get_param('receive_news')
            );

            $user_id = wp_create_user($userData['username'], $userData['password'], $userData['email']);            

            if ($user_id) { // USER WAS CREATED

                // Update additional user data from request
                foreach (["website", "receive_news", "first_name", "last_name"] as $meta_key) {                    
                    $value = $userData[$meta_key];
                    update_user_meta($user_id, $meta_key, $value );                    
                }                

                if ($userData['receive_news'] == "true") { // can be boolean true or string true
                    $request_lang = get_request_locale();
                    WppFollower::register_follower($userData['display_name'], $userData['email'], "news",  $request_lang);                    
                }
                
                // Set activation code
                $activation_code = self::set_activation_and_expiration($user_id);
                self::send_new_user_notifications($user_id, $userData['email'], $activation_code);

                // Get the user object
                $wp_user = get_user_by('email', $userData['email']);
                return $wp_user;
            }
            // In the case that the expected registered user was not found
            // we return the reason why it was not registered
            // Unfortunately the ProfilePress_Registration_Auth returns and html with the reason, so we strip it
            $reason = strip_tags($response);
            return $reason;
        }
        catch (Exception $e) {                
            $reason = $e->getMessage();
            return $reason;            
        }
    }

    /**
     * Send user activation link emailand notification to admin about the new registration
     *
     * @param Integer $user_id
     * @param String $activation_code
     * @return void
     */
    private static function send_new_user_notifications ($user_id, $user_email, $activation_code) {
        // Notify the admin
        wp_new_user_notification($user_id);

        $request_lang = get_request_locale();
                
        // Send user activation link       
        $site_title = get_bloginfo("name");

        // Currecntly we only support english and portuguese
        $msg_txt = $request_lang === "pt-br" ? "Ative sua conta clicando no link abaixo" : "Activate your account clicking o the link below";
        $msg_title = $request_lang === "pt-br" ? "[$site_title] Ative sua conta" : "[$site_title] Activate your account";        
        
        // Build the activation link
        $activation_uri = "/#/activate/$user_id/$activation_code";
        $activation_link = network_home_url($activation_uri);     
        
        // Build the message
        $message = "$msg_txt <br><br><a href='$activation_link'>$activation_link</a>";
        
        WppMailer::send_message($user_email, $msg_title, $message);
    }

    /**
     * User's email confirmation key in the database.
     *
     * @param $user_id
     *
     * @return mixed
     */
    public function get_user_activation_code($user_id) {
        return get_user_meta($user_id, 'activation_code', true);
    }


    /**
     * User's email confirmation expiration time in database.
     *
     * @param int $user_id
     *
     * @return mixed
     */
    public function get_user_expiration_time($user_id) {
        return get_user_meta($user_id, 'activation_expiration', true);
    }

    /**
     * Store user activation code and expiration time
     *
     * @param Integer $user_id
     * @return void
     */
    static function set_activation_and_expiration ($user_id) {
        $activation_code = wp_generate_password(20, false);
        $expiration = time() + (60 * 30);

        add_user_meta($user_id, 'activation_code', $activation_code);
        add_user_meta($user_id, 'activation_expiration', $expiration);
        add_user_meta($user_id, 'status', "pending");
        return $activation_code;
    }

    /**
     * Pre validate the request registration, checking for already existing user or password missing/no matching
     *
     * @param object $request
     * @return true|string returns true or the fail reason
     */
    private static function preValidateUserRegistration($request) {
        $metas = $request->get_param('metas');
        $email = $metas['email'];

        if (!self::validateEmailDomain($email)) {
            return "Invalid e-mail domain";  
        }

        $wp_user = get_user_by('email', $email);
        if ($wp_user) {
            return "E-mail already registered";  
        } else {
            $wp_user = get_userdatabylogin($request->get_param('slug'));
            if ($wp_user) {
                return "Username already registered";
            }
        }

        $password = $request->get_param('password');
        $confirmPassword = $request->get_param('confirmPassword');
        if (!isset($password)) {
            return "The password fields are mandatory";
        }
        if($password != $confirmPassword) {
            return "The password fields must match";
        }
        return true;
    }

    /**
     * Validate the domain of the user's email about to be used to register a user
     * based in a black list of invalid domains
     *
     * @param string $email
     * @return boolean
     */
    private static function validateEmailDomain($email) {
        $emailParts = explode("@", $email);
        $emailDomain = $emailParts[1];
        $emailUser = $emailParts[0];

        // We dont consider gmail fake emails valid, like username+temp@gmail.com
        if ($emailDomain === "gmail.com" && strpos($emailUser, "+") > -1) {
            return false;
        }
        
        $invalidDomains = [];        
        $lines = file(__DIR__."/invalid-email-domains.txt");

        // Loop through our array
        foreach ($lines as $line_num => $line) {
            $invalidDomains[] = str_replace("\n", "", $line);
        }       

        if (in_array($emailDomain, $invalidDomains)) {
            return false;  
        }
        return true;
    }

    /**
     * Validates the temp captcha code
     *
     * @param string $recaptchaToken
     * @return boolean
     */
    private static function validateCaptcha($recaptchaToken) {
        $secret = self::getRecaptchaSecret();
        $post_data = http_build_query(
            array(
                'secret' => $secret,
                'response' => $recaptchaToken,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents(self::$recaptchaValidationUrl, false, $context);
        $result = json_decode($response);
        return $result->success;
        
    }

    /**
     * Request the start of a password reset flow, which will send an email with a password reset link
     * to the user's registered email
     *
     * @param object $request
     * @return WP_REST_Response
     */
    public function requestPasswordReset($request) {
        $user_login = $request["login"];

        // It is assumed that the ProfilePress plugin is installed and loaded
        $result = ProfilePress_Password_Reset::retrieve_password_func($user_login);

        $statusCode = 204; // NO CONTENT - the request was processed, but there is not content to be returned
        if (is_wp_error($result)) {
            $statusCode = 404;  // NOT FOUND - user login not found
        }
        return new WP_REST_Response(null, $statusCode);       
    }

    /**
     * Validate if the key of password reset procedure is still valid to be used
     *
     * @param object $request
     * @return WP_REST_Response
     */
    public function validateResetKey($request) {
        $reset_login = $request["login"];
        $reset_key = $request["key"];

        // Get the Wp_User object using the reset key and user login
        // This is a native wordpress function
        $user = check_password_reset_key($reset_key, $reset_login);

        $statusCode = 204;  // NO CONTENT - the request was processed, but there is not content to be returned
        if (is_wp_error($user)) {
            $statusCode = 404;  // NOT FOUND - the pair user login and valid key was not found
        }
        return new WP_REST_Response(null, $statusCode);       
    }

    /**
     * Validate if the key of password reset procedure is still valid to be used
     *
     * @param object $request
     * @return WP_REST_Response
     */
    public function redefinePassword($request) {
        // If passwords is empty, return an error
        if (empty($request['password1']) || empty($request['key'])) {
            return new WP_REST_Response(null, 400); // INVALID REQUEST
        }

        // If passwords mismatch, return an error
        if ($request['password1'] !== $request['password2']) {
            return new WP_REST_Response(null, 409); // CONFLICT
        }
        
        // Try to get the user by reset key and user login
        $user = check_password_reset_key($request["key"], $request["login"]);

        // If user variable contains an error, return and error
        if (is_wp_error($user)) {
            return new WP_REST_Response(null, 404); // NOT FOUND - the pair user login and valid key was not found
        }
        
        // If we arrived until here it is because there were no errors
        // So we can update the user password
        reset_password($user, $request['password1']);    
        return new WP_REST_Response(null, 204); // NO CONTENT - the request was processed, but there is not content to be returned
    }

    /**
     * Validate if the key of password reset procedure is still valid to be used
     *
     * @param object $request
     * @return WP_REST_Response
     */
    public function activateAccount($request) {      
        $wp_user = get_userdata($request["userId"]);
        if ($wp_user === false) {
            return new WP_REST_Response(null, 404); // NOT FOUND - user not found by its id  
        }

        $now = time();
        $user_activation_code = $this->get_user_activation_code($wp_user->ID);
        $activation_expiration = absint($this->get_user_expiration_time($wp_user->ID));
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
    public function resendActivationEmail($request) {
        $wp_user = get_userdata($request["userId"]);
        if ($wp_user === false) {
            return new WP_REST_Response(null, 404); // NOT FOUND - user not found by its id  
        }
        delete_user_meta($wp_user->ID, 'activation_code');
        delete_user_meta($wp_user->ID, 'activation_expiration');
        delete_user_meta($wp_user->ID, 'status');

        // Set activation code
        $activation_code = self::set_activation_and_expiration($user_id);
        self::send_new_user_notifications($wp_user->ID, $wp_user->data->user_email, $activation_code);

        return new WP_REST_Response(null, 204); // NO CONTENT - the request was processed, but there is not content to be returned
    }
}

