<?php

/**
 * Class WppFollower
 *
 * @author: Amon Caldas
*/


class WppFollower  {

	// Defining follower values and keys
	public static $follower_post_type = "follower";
	public static $follower_initial_post_status = "pending";
	public static $follower_email_field = "email";
	public static $ip = "ip";
	public static $user_agent = "user_agent";
	public static $activated = "activated";
	public static $mail_list = "mail_list";	
	private static $encode_key = "wexrh!laoqpyTvA";

	function __construct () {
		add_action('init', array($this, 'register_custom_types'), 10);
	}

	/**
	 * Register custom types section and lang
	 *
	 * @return void
	 */
	public function register_custom_types () {
		$follower_args = array (
			'name' => self::$follower_post_type,
			'label' => 'Followers',
			'singular_label' => 'Follower',
			"description"=> "Emails about to be sent to newsletter subscribers",
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_rest' => false,
			'map_meta_cap' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'capability_type' => array(self::$follower_post_type, self::$follower_post_type."s"),
			'hierarchical' => false,
			'rewrite' => true,
			'rewrite_withfront' => false,	
			'show_in_menu' => true,
			'supports' => 
			array (
				0 => 'title',
				3 => 'revisions',
			),
		);

		register_post_type(self::$follower_post_type , $follower_args );
	}

	/**
	 * Deactivate a follower from so that it stops receiving news letter
	 *
	 * @param Integer $follower_id
	 * @return String already_deactivated|deactivated|not_found
	 */
	static function deactivate_follower ($follower_id) {
		$follower = get_post($follower_id);

		if ($follower) {
			if ($follower->post_status === "trash") {
				return "already_deactivated";
			}
			wp_trash_post($follower_id);
			$message = "Name: ". $follower->post_title."<br/><br/>";
			$message .= "Email: ". get_post_meta($follower_id, self::$follower_email_field, true);
			WppMailer::notify_admin("Follower opt out", $message, get_default_locale());
			return "deactivated";
		}
		return "not_found";
	}

	/**
	 * Register or upate a subscriber to receive news letters
	 *
	 * @param String $name
	 * @param String $email
	 * @param String $mail_list
	 * @param String $lang
	 * @return String updated|already_exists|created
	 */
	static function register_follower ($name, $email, $mail_list, $lang) {
		// Check if the user is already a subscriber
		$args = (
			array(
				"post_type"=> self::$follower_post_type, 
				"post_status"=> array("publish", self::$follower_initial_post_status),
				'meta_query' => array(
					array(
						'key'=> self::$follower_email_field,
						'value'=> $email
					)
				)
			)
		);
		$existing_followers = get_posts($args);

		if (count($existing_followers) > 0) {
			return "already_exists";
		} else {
			$follower_id = wp_insert_post(
				array(
					"post_type"=> self::$follower_post_type, 
					"post_status"=> self::$follower_initial_post_status,
					"post_author"=> 1, // 1 is always the admin, the first user created
					"post_title"=> strip_tags($name), 
					"meta_input"=> array(
						self::$ip => get_request_ip(),
						self::$follower_email_field => strip_tags($email),
						self::$user_agent => $_SERVER['HTTP_USER_AGENT'],
						self::$activated => 0,
						self::$mail_list => $mail_list
					)
				)
			);
			$term = get_term_by('slug', $lang, LOCALE_TAXONOMY_SLUG);
			if ($term) {
				$term_arr = [$term->term_id];
				wp_set_post_terms($follower_id, $term_arr, LOCALE_TAXONOMY_SLUG);
			}
			return $follower_id;
		}
	}

	/**
		* Get follower id by email
		*
		* @param String $email
		*/
	static function get_follower_unsubscribe_link ($email) {
		$email_encoded = base64_encode($email.self::$encode_key);
		$email_encoded = str_replace('=', '', $email_encoded);
		

		$uri = "/unsubscribe/$email_encoded";		
		$router_mode = get_option("wpp_router_mode");
		if ($router_mode === "hash") {
			$uri = "/#$uri";
		}
		$unsubscribe_link = network_home_url($uri); 
		return $unsubscribe_link;		
	}


	/**
		* Get follower email from unsubscribe code
		*
		* @param String $email
		*/
	static function get_follower_email_from_unsubscribe_code ($code) {
		$decoded = base64_decode($code."=");
		$email = str_replace(self::$encode_key, '', $decoded);
		return $email;		
	}
}
?>
