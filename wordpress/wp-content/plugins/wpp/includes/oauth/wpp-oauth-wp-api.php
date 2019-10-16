<?php

// Require the JWT library
use \Firebase\JWT\JWT;

/**
 * WPP OAUTH routes
 *
 * @package WPP_OAUTH
 */

class WppOauthApi {

	function __construct () {
		add_filter( 'rest_api_init', array( $this, 'register_routes' ) );		
	}

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
     * Get WPP oauth api namespace.
     *
     * @since 1.2.1
     * @return string
     */
    public static function get_plugin_namespace() {
        return WPP_API_NAMESPACE.'/oauth';
	}
	
	/**
	 * Register WPP oauth routes for WP API v2.
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

				return new WP_REST_Response($data, 201); // CREATED, NO CONTENT
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
			$user_id = wp_create_user( $email, $pass, $email );

			if ($user_id) { // USER WAS CREATED
				update_user_meta($user_id, '_signedup_via', 'github');
				wp_new_user_notification($user_id );
				$wp_user = get_user_by('email', $email);
				$wp_user->data->emailVerified = true;								
				return $wp_user;
			}
			// In the case that the expected registered user was not found
			// we return the reason why it was not registered
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
		$git_hub_client_id = get_option("wpp_git_hub_client_id");

		if (isset($git_hub_client_id)) {
			return $git_hub_client_id;
		}
	}

	/**
	 * Get the client secret according the environment
	 *
	 * @return string client secret
	 */
	protected function getGitHubClientSecret() {
		$git_hub_client_secret = get_option("git_hub_client_secret");

		if (isset($git_hub_client_secret)) {
			return $git_hub_client_secret;
		}
	}
}

