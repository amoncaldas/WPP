<?php

/**
 * Wpp User
 *
 * @package WPP
 */


class WppUser {

    public function __construct() {
        add_action('authenticate', array($this, 'after_determine_user'), 20, 3);
        add_filter('jwt_auth_token_before_dispatch', array($this, 'set_jwt_user_data'), 10, 2);
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
     * Add extra user data to the token data
     *
     * @param Array $data
     * @param WP_User $user
     * @return Array $data
     */
    public function set_jwt_user_data( $data, $user) {
        $data["avatar_url"] = get_avatar_url($data["user_email"], array("size"=>48));
        return $data;
    }

    /**
     * Creates a new wordpress user using the data provided in the request
     *
     * @param object $request
     * @return WP_User|string user object or the fail reason
     */
    public static function create_wp_user($request) {
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
                    update_user_meta($user_id, "registration_user_agent", $_SERVER['HTTP_USER_AGENT'] );
                    update_user_meta($user_id, "registration_ip", get_request_ip() );                    
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
    public static function send_pasword_reset_email ($user_login, $key) {
        $request_lang = get_request_locale();

        // Send user activation link       
        $site_title = get_bloginfo("name");

        // Currenctly we only support english and portuguese
        $msg_title = $request_lang === "pt-br" ? "[$site_title] Redefinição de senha" : "[$site_title] Password reset";            
        
        // Build the message
        $reset_link = network_home_url("/#/password/reset/$key/$user_login");

        $wp_user = get_user_by("login", $user_login);
        if (!$wp_user) {
            $wp_user = get_user_by("email", $user_login);
        }
        
        WppMailer::send_reset_password_email($wp_user->data->user_email, $msg_title, $reset_link, $request_lang);
    }

    /**
     * Send user activation link emailand notification to admin about the new registration
     *
     * @param Integer $user_id
     * @param String $activation_code
     * @return void
     */
    public static function send_new_user_notifications ($user_id, $user_email, $activation_code) {
        
        $request_lang = get_request_locale();
        
        // Notify the admin
        WppMailer::notify_admin("New user registration", $user_email, $request_lang);
                
        // Send user activation link       
        $site_title = get_bloginfo("name");

        // Currenctly we only support english and portuguese
        $msg_title = $request_lang === "pt-br" ? "[$site_title] Ative sua conta" : "[$site_title] Activate your account";        
        
        // Build the activation link
        $activation_uri = "/#/activate/$user_id/$activation_code";
        $activation_link = network_home_url($activation_uri);    
        
        WppMailer::send_registration_email($user_email, $msg_title, $activation_link, $request_lang);
    }

    /**
     * User's email confirmation key in the database.
     *
     * @param $user_id
     *
     * @return mixed
     */
    public static function get_user_activation_code($user_id) {
        return get_user_meta($user_id, 'activation_code', true);
    }


    /**
     * User's email confirmation expiration time in database.
     *
     * @param int $user_id
     *
     * @return mixed
     */
    public static function get_user_expiration_time($user_id) {
        return absint(get_user_meta($user_id, 'activation_expiration', true));
    }

    /**
     * Store user activation code and expiration time
     *
     * @param Integer $user_id
     * @return String $activation_code
     */
    public static function set_activation_and_expiration ($user_id) {
        $activation_code = wp_generate_password(20, false);
        $expiration = time() + (60 * 30);

        add_user_meta($user_id, 'activation_code', $activation_code);
        add_user_meta($user_id, 'activation_expiration', $expiration);
        add_user_meta($user_id, 'status', "pending");
        return $activation_code;
    }

    /**
     * Store user activation code and expiration time
     *
     * @param Integer $user_id
     * @return String $password_reset_key
     */
    public static function set_pass_reset_key_and_expiration ($user_id) {
        $password_reset_key = wp_generate_password(20, false);
        $expiration = time() + (60 * 30);

        update_user_meta($user_id, 'password_reset_key', $password_reset_key);
        update_user_meta($user_id, 'password_reset_expiration', $expiration);
        return $password_reset_key;
    }

    /**
     * Store user activation code and expiration time
     *
     * @param String $reset_key
     * @return String $reset_login
     * @return WP_User $wp_user
     */
    public static function get_user_by_pass_reset_key_login ($reset_key, $reset_login, $remove = false) {

        $wp_user = get_user_by("login", $reset_login);
        if (!$wp_user) {
            $wp_user = get_user_by("email", $reset_login);
        }

        $password_reset_key = get_user_meta($wp_user->ID, 'password_reset_key', true);
        $password_reset_expiration = get_user_meta($wp_user->ID, 'password_reset_expiration', true);
        
        $now = time();

        if ($now > $password_reset_expiration) {
            return false;
        } elseif ($reset_key !== $password_reset_key) {
            return false;
        } else {
            if ($remove) {
                delete_user_meta($wp_user->ID, 'password_reset_key');
                delete_user_meta($wp_user->ID, 'password_reset_expiration');
            }
            return $wp_user;
        }
    }

    /**
     * Pre validate the request registration, checking for already existing user or password missing/no matching
     *
     * @param object $request
     * @return true|string returns true or the fail reason
     */
    public static function pre_validate_user_registration($request) {
        $metas = $request->get_param('metas');
        $email = $metas['email'];

        if (!self::validate_email_domain($email)) {
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
    public static function validate_email_domain($email) {
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
	 * Add custom data to the wp/v2/users/<user-id> endpoint
	 *
	 * @param Wp_User $user
	 * @param string $field_name
	 * @param Object $request
	 * @return array of metas to be added in the response
	 */
	static function get_user_meta_callback( $user, $field_name, $request) {
		unset($user["capabilities"]);
		unset($user["extra_capabilities"]);
		unset($user["meta"]); 
		$metas = $user;
		$extra_meta = get_user_meta( $user['id']);
		
		foreach ($extra_meta as $key => $value) {
			if (in_array($key, ["website", "receive_news"])) {
                if (is_array($value) && count($value) == 1) {
                    $value_data = $value[0];
                    if($value_data !== "?") {
                        $value_data = $value_data === "1"? true: $value_data;
                        $metas[$key] = maybe_unserialize($value_data);
                    }				
                } else {
                    $metas[$key] = $value;
                }
			}
		}
		return $metas;
	}

	/**
	 * Update custom user meta fields on rest api user update
	 *
	 * @param array $metas
	 * @param Wp_User $user
	 * @param string $field_name field being updated
	 * @return void
	 */
	static function update_user_meta_callback($metas, $user, $field_name) {
		global $wpdb;
		// Set user's display name as first name, if first name is provided
		if (isset($metas["first_name"])) {
			$wpdb->update($wpdb->users, array('display_name' => $metas["first_name"]), array('ID' => $user->ID));
		}
		
		if ($field_name === "metas") {
			$user_id = $user->ID;
			foreach ($metas as $key => $value) {
				if (in_array($key, ["website", "receive_news", "first_name", "last_name"])) {
                    update_user_meta( $user_id, $key, $value );
                    if ($key === "receive_news" && $value == "false") {
                        WppFollower::deactivate_follower($user->ID);   
                    }
				}
			}
		}
	}
}

