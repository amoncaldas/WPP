<?php

// Require the JWT library
use \Firebase\JWT\JWT;

/**
 * ORS OAUTH routes
 *
 * @package ORS_OAUTH
 */

class OrsOauthWPApi {

	/**
	 * Github client ids
	 *
	 * @var string
	 */
	protected $ghClientIds = [
		"dev" => 'bac0172a938adda77c3e',
		"staging" => '55a0a8aa4caceab8faf8',
		"prod"=> 'fcfe0d9a52baf824cdcd'
	];

	/**
	 * Github client secrets
	 *
	 * @var string
	 */
	protected $ghClientSecrets = [
		"dev" => '555d422ce0b177eceed2a4c242109bcddb5d874b',
		"staging" => '46c159393e6da57e75fd23f0f1c199055546b299',
		"prod"=> '17926513767b9ba42e37c50ec508bef599a2d654'
	];

	/**
	 * Env hosts
	 *
	 * @var string
	 */
	protected $envHosts = [
		"dev"=> 'localhost',
		"staging" => '129.206.7.40',
		"prod"=> 'openrouteservice.org'
	];

	/**
	 * Github api base url
	 *
	 * @var string
	 */
	protected $githubApiBaseUrl = "https://api.github.com";

	/**
	 * Github oauth access token url
	 *
	 * @var string
	 */
	protected $githubOAuthAccessTokenUrl = "https://github.com/login/oauth/access_token";

    /**
     * Get ORS oauth api namespace.
     *
     * @since 1.2.1
     * @return string
     */
    public static function get_plugin_namespace() {
        return 'ors-oauth';
	}
	
	 /**
     * the ProfilePress form id used
     *
     * @return void
     */
    private static function getSignFormId() {
        return 15;
    }


    /**
     * Register ORS oauth routes for WP API v2.
     *
     * @since  1.2.0
     */
    public function register_routes() {
        register_rest_route( self::get_plugin_namespace(), '/github/login', array(
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'processGitHubOauthLogIn' ),
            )
		) );
		register_rest_route( self::get_plugin_namespace(), '/github/signup', array(
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'processGitHubOauthSignUp' ),
            )
		) );
		register_rest_route( self::get_plugin_namespace(), '/social-client-data', array(
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'socialClientData' ),
            )
        ) );
	}

	/**
	 * Process the github oauth login action
	 *
	 * @param object $request
	 * @return WP_REST_Response
	 */
	public function processGitHubOauthLogIn($request) {
		return $this->processGitHubOauth($request);
	}

	/**
	 * Process the github oauth signup action
	 *
	 * @param object $request
	 * @return WP_REST_Response
	 */
	public function processGitHubOauthSignUp($request) {
		return $this->processGitHubOauth($request, true);
	}


	/**
	 * Process the github authentication receiving the oauth temp code and get the corresponding
	 * wordpress linked to the github user's email address
	 * We are following github oauth flow from step 2. Step 1 is made on the front-end client
	 * and the oauth temp code is sent to this plugin
	 * @see https://developer.github.com/apps/building-oauth-apps/authorizing-oauth-apps/#web-application-flow
	 *
	 * @param Object $request
	 * @return WP_REST_Response
	 */
	protected function processGitHubOauth($request, $autoRegister = false) {
		$code = $request->get_param('code');
		$gh_user = $this->getGitHubUser($code);

		if ($gh_user !== FALSE) {

			$wp_user = $this->getWPUser($gh_user);

			// we only auto register a new user if s/he is not already registered 
			// and if $autoRegister is true (what means that we are in the signup)
			if (!$wp_user && $autoRegister) {
				$result = $this->createWPUser($gh_user);
				if (!($result instanceof WP_User)) {
					$data = ["message"=>$result];
					return new WP_REST_Response($data, 409); // CONFLICT 
				} else {
					$wp_user = $result;
				}
			}
			
			if (!$wp_user) {
				$data = ["message"=>"Your Github account is not linked/registered wth an account on openrouteservice. Please signup first."];
				return new WP_REST_Response($data, 404 ); // INTERNAL SERVER ERROR
			}

			if ($wp_user->data->emailVerified !== true) {
				$data = ["message"=>"Your account was not activated. Please check your email and activate your account before logging in."];
				return new WP_REST_Response($data, 422 ); // INTERNAL SERVER ERROR
			}

			if ($wp_user !== false) {
				$jwt_token = $this->generateJWTToken($wp_user);

				/** The token is signed, now createWPUser create the object with no sensible user data to the client*/
				$data = array(
					// the front-end expect and object containing an object with the github access_token attribute and additional data
					"access_token"=>$gh_user->gh_access_token,
					"user" => [ // put here additional user data
						'token' => $jwt_token,
						'id' => $wp_user->ID,
						'user_email' => $wp_user->data->user_email,
						'user_nicename' => $wp_user->data->user_nicename,
						'user_display_name' => $wp_user->data->display_name,
					]
				);

				return new WP_REST_Response($data, 201); // OK
			}

			return new WP_REST_Response(null, 404 ); // Local wordpress user linked to github email address not found
		}

		$data = ["message"=>"It was not possible to authenticate using github account"];
		return new WP_REST_Response($data, 500 ); // INTERNAL SERVER ERROR
	}

	/**
	 * Generate JWT token with user data
	 *
	 * @param WP_User $user
	 * @return string $token
	 */
	protected function generateJWTToken(WP_User $user) {
		$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

		$issuedAt = time();
        $notBefore = apply_filters('jwt_auth_not_before', $issuedAt, $issuedAt);
        $expire = apply_filters('jwt_auth_expire', $issuedAt + (DAY_IN_SECONDS * 7), $issuedAt);

        $token = array(
            'iss' => get_bloginfo('url'),
            'iat' => $issuedAt,
            'nbf' => $notBefore,
            'exp' => $expire,
            'data' => array(
                'user' => array(
                    'id' => $user->data->ID,
                ),
            ),
        );

        /** Let the user modify the token data before the sign. */
		$token = JWT::encode(apply_filters('jwt_auth_token_before_sign', $token, $user), $secret_key);
		return $token;
	}

	/**
	 * Get the github user based in the oauth temp code
	 * We are following github oauth flow from step 2. Step 1 is made on the front-end client
	 * and the oauth temp code is sent to this plugin
	 * @see https://developer.github.com/apps/building-oauth-apps/authorizing-oauth-apps/#web-application-flow
	 *
	 * @param string $oauth_code
	 * @return stdClass $gh_user
	 */
	protected function getGitHubUser($oauth_code) {

		// First we make a request to exchange the temp oauth code generated by GitHub
		// to an access token.
		$data = array(
		  'client_id' => $this->getGitHubClientId(),
		  'client_secret' => $this->getGitHubClientSecret(),
		  'code' => $oauth_code
		);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($this->githubOAuthAccessTokenUrl, false, $context);

		// If the request has not resulted in false (has not failed)
		// we use the access token to authenticated get the user data
		if ($result !== false) {
			parse_str($result, $result_data);
			$gh_access_token = $result_data["access_token"];

			$response = wp_remote_get($this->githubApiBaseUrl."/user?access_token=$gh_access_token");
			$body = wp_remote_retrieve_body($response);
			$gh_user = json_decode( $body );

			$gh_user->gh_access_token = $gh_access_token;

			// if the user has no public email address, so we need to get all its emails
			// to be used to get the local wordpress user
			if(!isset($gh_user->email)) {
				$response = wp_remote_get($this->githubApiBaseUrl."/user/emails?access_token=$gh_access_token");
				$body = wp_remote_retrieve_body($response);
				$gh_user_emails = json_decode( $body );
				$gh_user->emails = $gh_user_emails;
			}

			// return the github user data, containing the email attribute
			return $gh_user;
		}

		return false;
	}

	/**
	 * Tries to find the WP User based on the Github user data
	 *
	 * @param stdClass $gitHubUser
	 * @return WP_User
	 */
	protected function getWPUser($gitHubUser) {
		$wp_user = false;
		$email = $this->getGhUserEmail($gitHubUser);
		if ($email) {
			$wp_user = get_user_by( 'email', $email);
		}
		if ($wp_user) {
			$firstName = get_user_meta($wp_user->ID,'first_name',true);

			if (isset($firstName) && $firstName !== "") {
				$wp_user->data->user_display_name = $firstName;
			}

			// check if the user's email was verified and set the emailVerified attribute
			$wp_user->data->emailVerified = false;
			$emailVerified = get_user_meta($wp_user->ID,'pp_email_verified',true);
			$activationCode = get_user_meta($wp_user->ID,'pp_activation_code',true);

			// some users are created without the verification processes via admin, so they do not
			// have the pp_email_verified meta, but in this case they also dont have the pp_activation_code meta
			if (($emailVerified == true || $emailVerified === "1" ) || !isset($activationCode) || $activationCode === "") {
				$wp_user->data->emailVerified = true;
			}
		}
		return $wp_user;
	}

	/**
	 * Creates a new wordpress user from a github user
	 *
	 * @param stdClass $gh_user
	 * @return WP_User|string user object or the error message
	 */
	private function createWPUser($gh_user) {
        try {
			$email = $this->getGhUserEmail($gh_user);
			$pass = uniqid();
            $userData = array(
                'reg_username' => $email,
                'reg_password' => $pass,
                "reg_password2" => $pass,
                "reg_password_present" => "true",            
                'reg_email' => $email,
                'reg_email2' => $email,
                'reg_website' => null,
                'reg_nickname' => "",
                'reg_display_name' => $gh_user->login,
                'reg_first_name' => $gh_user->login,
                'reg_last_name' => null,
                'reg_bio' => "",
                'reg_select_role' => null
			);

			// Disable new User Notification for registration via github
			add_filter( 'pp_new_user_notification', '__return_false' );

			$sigUpFormId = self::getSignFormId();

            // It is assumed that the ProfilePress plugin is installed and loaded
            $response = ProfilePress_Registration_Auth::register_new_user($userData, $sigUpFormId, [], null, false);

            $wp_user = get_user_by('email', $userData['reg_email']);

			if ($wp_user) { // USER WAS CREATED
				
				// Add custom ProfilePress expected meta key to define that the user was created via github
				update_user_meta($wp_user->ID, '_pp_signup_via', 'github');
					

				// Set user as verified, because we don't need to wait e-mail confirmation
				// update_user_meta($wp_user->ID, 'pp_email_verified', "true" );
				PP_User_Email_Confirmation_Addon::confirm_user_email($wp_user->ID);
				$wp_user->data->emailVerified = true;
				
                // Add the user to the news letter list, if s/he has selected this option on the form
				// It is assumed that the PP_Mailchimp plugin is installed and loaded
				if (isset($userData['reg_email'])) {
					$mailChimp = new PP_Mailchimp_Addon();
					$mailChimp->add_to_list($userData['reg_email'], $userData['reg_first_name'], $userData['reg_last_name']);
				}
                
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
	 * Get the valid email from the github user object
	 * from the mail email property or from the list of email
	 * selecting the one which is verified
	 *
	 * @param object $gitHubUser
	 * @return string email
	 */
	protected function getGhUserEmail($gitHubUser) {
		if(isset($gitHubUser->email)) {
			return $gitHubUser->email;
		} else if (isset($gitHubUser->emails)) {
			foreach($gitHubUser->emails as $email) {
				if ($email->verified === true) {
					return $email->email;
					break;					
				}
			}
		}
	}

	/**
	 * Get GitHub client id
	 *
	 * @param Object $request
	 * @return WP_REST_Response
	 */
	public function socialClientData($request) {
		$data = [
			"github"=> [
				"clientId"=>$this->getGitHubClientId()
			]
		];
        return new WP_REST_Response($data, 200 ); // OK
	}

	/**
	 * Get the client id according the environment
	 *
	 * @return string client Id
	 */
	protected function getGitHubClientId() {
		$socialOptions = get_option("pp_social_login");

		if (isset($socialOptions["github_client_id"])) {
			return $socialOptions["github_client_id"];
		} else {
			foreach ($this->envHosts as $key => $value) {
				if ($value === $_SERVER["SERVER_NAME"]) {
					return $this->ghClientIds[$key];
				}
			}
		}
	}

	/**
	 * Get the client secret according the environment
	 *
	 * @return string client secret
	 */
	protected function getGitHubClientSecret() {
		// We stopped using the github credentials as ProfilePress social login
		// because they use different callback urls
		$socialOptions = get_option("pp_social_login");

		if (isset($socialOptions["github_client_secret"])) {
			return $socialOptions["github_client_secret"];
		} else {
			foreach ($this->envHosts as $key => $value) {
				if ($value === $_SERVER["SERVER_NAME"]) {
					return $this->ghClientSecrets[$key];
				}
			}
		}
	}
}

