<?php
/**
 * Plugin Name: ORS
 * Description: Add custom behaviors and functions to change WordPress and third party plugins regarding JWT authentication and wp rest api returning data. It assumes that the jwt-authentication-for-wp-rest-api, 
 * profilepress and pp-mailchimp plugins are installed adn active.
 * Version:     0.0.1
 *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 * Text Domain: ors
 *
 * @package ORS
 */

/**
 * This plugin assumes that the jwt-authentication-for-wp-rest-api, 
 * profilepress and pp-mailchimp plugins are already installed ac active
 */

 class UserEventsListener {

	/**
	 * The base path for website dev webapp
	 *
	 * @var string
	 */
	private static $app_base_path = ""; // we are not using any base path for instance

	/**
	 * Constructor that sets the listeners to user related events
	 */
	function __construct() {
        // Append user data on jwt plugin rest api authentication result
		add_filter('jwt_auth_token_before_dispatch', array( $this, 'jwtRestAuthUserData'), 10, 2);

		// Change email confirmation link in the activation account email
		// add_filter('wp_mail', array($this,'changeEmailConfirmationContent'), 10,1);
			
		// Include user custom metas in get and update user rest api endpoint
		add_action( 'rest_api_init', array($this, 'addUserMetaRestCallback'));

		// Treat authentication errors
		add_action('rest_api_init', array( $this, 'addAuthenticationFilter'));	
    }

	/**
	 * Register user meta data to get and update callbacks user wp rest api
	 *
	 * @return void
	 */
	public function addUserMetaRestCallback() {
		register_rest_field( 
			'user',
			'metas',
			array(
				'get_callback'      => function ($user, $field_name, $request) {
					return self::getUserMetaCallback($user, $field_name, $request);
				},
				'update_callback'   => function ($metas, $user, $field_name) {
					return self::updateUserMetaCallback($metas, $user, $field_name);
				},
				'schema'            => null,
			)
		);
	}


	/**
	 * Add user id in the jwt rest auth plugin response
	 *
	 * @param array $data
	 * @param Wp_User $user
	 * @return void
	 */
	function jwtRestAuthUserData($data, $user) {
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
	function authenticateFilter($user){
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
	 * Replace the reset password link for a custom one for the ProfilePress component
	 *
	 * @param Array $replacements
	 * @param String $user_login
	 * @param String $key
	 * @param String $user_email
	 * @return Array $replacements
	 */
	function replacePasswordResetLink($replacements, $user_login, $key, $user_email){
		$customResetUrl = network_home_url(self::$app_base_path."/#/password/reset/$key/$user_login");
		$replacements[1] = 	$customResetUrl;
		return $replacements;
	}

	/**
	 * Replace the reset password link for a custom one for the ProfilePress component
	 *
	 * @param Array $replacements
	 * @param String $user_login
	 * @param String $key
	 * @param String $user_email
	 * @return Array $replacements
	 */
	function replacePasswordResetLinkInWelcomeMsg($replacements){
		$userEmail = $replacements[2];
		$wp_user = get_user_by('email', $userEmail);		

    if ($wp_user !== false) {
			$userLogin = $wp_user->data->email;
			$key = get_password_reset_key($wp_user);
			$customResetUrl = network_home_url(self::$app_base_path."/#/password/reset/$key/$userLogin");
			$replacements[6] = 	$customResetUrl;       
    }  
		
		return $replacements;
	}	


	/**
	 * Treat authentication error
	 *
	 * @param Object $wp_rest_server
	 * @return void
	 */
	function addAuthenticationFilter($wp_rest_server){
		add_filter('authenticate', array($this,'authenticateFilter'), 99, 3);
	}

	/**
	 * Add custom data to the wp/v2/users/<user-id> endpoint
	 *
	 * @param Wp_User $user
	 * @param string $field_name
	 * @param Object $request
	 * @return array of metas to be added in the response
	 */
	static function getUserMetaCallback( $user, $field_name, $request) {
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
	static function updateUserMetaCallback($metas, $user, $field_name) {

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
				}
			}
		}
	}

	
	/**
	 * Replace the activation link to a custom one of a activation email message.
	 * As there is no hook to set a custom activation link in ProfilePress
	 * we have to add a hook to run this function every time  and email is about 
	 * to be sent. So, we check the email subject (defined in the ProfilePress admin area)
	 * and the email recipient (if it is not the admin). If if it is the right message 
	 * (subject and recipient) it is the email that is being sent to the the user with 
	 * the activation link and then we customize the link applying a regex in the message content
	 *
	 * @param Array $email_Data
	 * @return Array $email_Data
	 */
	function changeEmailConfirmationContent ($email_Data) {
		// Get the ProfilePress activation message subject
		$ppEmailConfirmationSubject = "New User Registration";

		// Get the wordpress admin email
		$admin_email = get_option("admin_email");
		
		// If is the email confirmation and is not sending to the admin, change the link
		if ($email_Data["subject"] === $ppEmailConfirmationSubject && $email_Data["to"] !== $admin_email ) {

			// Get all the links in the message
			preg_match_all('/https?\:\/\/[^\" ]+/', $email_Data["message"], $matches);

			// The activation link must start with the defined login url
			$original_login_url = network_home_url("/profile");

			// we have to loop over all links on the email
			if(is_array($matches) && is_array($matches[0])) {
				foreach ($matches[0] as $match_url) {
					// check if the current link matches the base url
					if (strpos($match_url, $original_login_url) !== false) {
						// Get the url parameters of the url and put them, as an array in $params variable
						parse_str(parse_url($match_url, PHP_URL_QUERY), $params);

						// After the change applied by the customizeLoginUrl method the
						// In production, url in the email is expected to be like this: https://domain.tld/?user_id=12006&activation_code=qhCRMzHLkT/
						
						// Build a new url using the parameters
						$new_url = network_home_url(self::$app_base_path."/#/activate/".$params["user_id"]."/".$params["activation_code"]);
						$email_Data["message"] = str_replace($match_url, $new_url, $email_Data["message"]);						
					}
				}
			}
		}
		return $emailData;
	}
}




		
  
