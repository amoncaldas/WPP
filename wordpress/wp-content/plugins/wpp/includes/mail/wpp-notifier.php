<?php

/**
 * Class FamMail
 *
 * Description for class FamMail
 * WordPress options used: email_sender_name, email_sender_email, skip_email_sending_notification, deactivate_news_sending
 *
 * @author: Amon Caldas
*/

class WppNotifier  {

	public $base_insert_sent_sql = "insert into wp_mail_sent (email, id_pending_mail, mail_list_type, mail_title) values ";
	public $debug_output = "";
	public $max_notifications_per_time = 50;
	public $sent_mails = array();
	public $mail_list_field = "mail_list";

	// Defining values and keys to generate the notification
	public $notification_post_type = "notification";
	public $follower_initial_post_status = "pending";
	public $notification_content_type = "html";
	public $generated_post_id_meta_key = "generated_from_post_id";
	public $default_notification_type = "news";
	public $notification_content_type_desc = "newsletter";
	

	function __construct () {
		add_action( 'save_post', array($this, 'generate_notification_based_on_created_content'), 100, 2);
		add_filter( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action('init', array($this, 'register_custom_types'), 10);
		
	}

	/**
	 * Create the wp_mail_sent table when the plugin is activated
	 *
	 * @return void
	 */
	public function activate_wpp_plugin () {
		$create_wp_mail_sent_sql = "
			CREATE TABLE IF EXISTS `wp_mail_sent` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`email` varchar(300) NOT NULL,
			`id_pending_mail` int(11) NOT NULL,
			`mail_list_type` varchar(15) NOT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`mail_title` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,PRIMARY KEY (`ID`))
		";

		global $wpdb;
		$wpdb->query($create_wp_mail_sent_sql);
	}

	/**
	 * Register routes for WP API v2.
	 *
	 * @since  1.2.0
	 */
 public function register_routes() {
		register_rest_route(WPP_API_NAMESPACE."/notifications", '/send', array(
			array(
				'methods'  => "GET",
				'callback' => array($this, 'send_notifications' ),
			)
		));
		register_rest_route(WPP_API_NAMESPACE."/message", '/send', array(
			array(
				'methods'  => "POST",
				'callback' => array($this, 'send_message' ),
			)
		));
		register_rest_route(WPP_API_NAMESPACE."/notifications", '/subscribe', array(
			array(
				'methods'  => "POST",
				'callback' => array($this, 'subscribe_for_notifications' ),
			)
		));
		register_rest_route(WPP_API_NAMESPACE."/notifications", '/unsubscribe/(?P<code>[a-zA-Z0-9_.-]+)', array(
			array(
				'methods'  => "PUT",
				'callback' => array($this, 'unsubscribe_for_notifications' ),
			)
		));
		register_rest_route(WPP_API_NAMESPACE."/message", '/report-error', array(
			array(
				'methods'  => "POST",
				'callback' => array($this, 'report_error' ),
			)
		));
	}

	/**
	 * Register custom types section and lang
	 *
	 * @return void
	 */
	public function register_custom_types () {
		$notification_args = array (
			'name' => $this->notification_post_type,
			'label' => 'Notifications',
			'singular_label' => 'Notification',
			"description"=> "Emails about to be sent to newsletter subscribers",
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_rest' => false,
			'map_meta_cap' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'capability_type' => array($this->notification_post_type, $this->notification_post_type."s"),
			'hierarchical' => false,
			'rewrite' => true,
			'rewrite_withfront' => false,	
			'show_in_menu' => true,
			'supports' => 
			array (
				0 => 'title',
				2 => 'editor',
				3 => 'revisions',
			),
		);

		register_post_type( $this->notification_post_type , $notification_args );
	}

	/**
	 * Unsubscribe a follower to the notification
	 *
	 * @return void
	 */
	public function unsubscribe_for_notifications($request) {
		$code = $request->get_param('code');
		$follower_email_field = WppFollower::get_follower_email_from_unsubscribe_code($code);

		// Get the follower by email
		$args = (
			array(
				"post_type"=> WppFollower::$follower_post_type, 
				"post_status"=> array("publish", $this->follower_initial_post_status),
				'meta_query' => array( array( 'key'=> WppFollower::$follower_email_field, 'value'=> $follower_email_field ) )
			)
		);

		$followers = get_posts($args);
		
		if ($followers && count($followers) > 0) {
			$follower_id = $followers[0]->ID;
			$result = WppFollower::deactivate_follower($follower_id);
	
			if ($result === "already_deactivated") {
				return new WP_REST_Response(null, 400); // INVALID REQUEST
			} else if ($result === "deactivated") {			
				return new WP_REST_Response(null, 204); // ACCEPTED, NO CONTENT
			}			
		}
		return new WP_REST_Response(null, 404); // NOT FOUND
	}

	/**
	 * Report error to admin
	 *
	 * @return WP_REST_Response
	 */
	public function report_error($request) {
		$recaptchaToken = $request->get_param('recaptchaToken');
		$validCaptcha = validate_captcha($recaptchaToken);

		if ($validCaptcha === true) {
			$message = $request->get_param('message');
			$url = $request->get_param('url');
			$message = "URL: $url <br/><br/> $message";
			if (isset($message)) {
				$subject = "Error notification";
				WppMailer::notify_admin($subject, $message, get_default_locale());
				return new WP_REST_Response(null, 204); // ACCEPTED/UPDATED, NO CONTENT TO RETURN
			}
			return new WP_REST_Response(null, 409); // CONFLICT, MISSING DATA
		}
		return new WP_REST_Response(null, 403); // FORBIDDEN
	}


	/**
	 * Subscribe to notification list
	 *
	 * @param Object $request
	 * @return WP_REST_Response
	 */
	public function subscribe_for_notifications($request) {
		$recaptchaToken = $request->get_param('recaptchaToken');
		$validCaptcha = validate_captcha($recaptchaToken);        
		
		if ($validCaptcha === true) {
			$name = $request->get_param('name');
			$email = $request->get_param(WppFollower::$follower_email_field);
	
			if (isset($name) && isset($email)) {
				$lang = $request->get_param(LOCALE_TAXONOMY_SLUG) ? $request->get_param(LOCALE_TAXONOMY_SLUG) : get_default_locale();
				$mail_list =  $request->get_param($this->mail_list_field) ?  $request->get_param($this->mail_list_field) : $this->default_notification_type;
				
				$result = WppFollower::register_follower($name, $email, $mail_list, $lang);

				if($result === "updated") {
					return new WP_REST_Response(null, 204); // ACCEPTED/UPDATED, NO CONTENT TO RETURN
				}
				elseif  ($result === "already_exists") {
					return new WP_REST_Response(null, 409); // CONFLICT, ALREADY EXISTS
				} else { // is created
					$follower_id = $result;
					$this->send_subscription_notifications($follower_id, $name, $email, $lang);
					return new WP_REST_Response(["id" => $follower_id ], 201); // CREATED, return id
				}
	
			} else {
				return new WP_REST_Response(null, 400); // INVALID REQUEST
			}
		}
	}

	/**
		* Send subscription notifications
		*
		* @param Integer $follower_id
		* @param String $name
		* @param String $mail
		* @param String $lang
		* @return void
		*/
	public function send_subscription_notifications ($follower_id, $name, $email, $lang) {
		$user_msg_title = WppMailer::get_mail_subject_translation("subscription_registered", $request_lang, "Subscription registered");
		$unsubscribe_link = WppFollower::get_follower_unsubscribe_link($email);
		WppMailer::send_subscription_registered_email($email, $user_msg_title, $unsubscribe_link, $lang);
		
		$message = "Name: ". strip_tags($name)."<br/><br/>";
		$message .= "Email: ". strip_tags($email);
		WppMailer::notify_admin("New follower registration", $message, get_default_locale());		
	}


	/**
	 * Send a message to site admin
	 *
	 * @param Object $request
	 * @return WP_REST_Response
	 */
	public function send_message($request) {
		$recaptchaToken = $request->get_param('recaptchaToken');
		$validCaptcha = validate_captcha($recaptchaToken);

		if ($validCaptcha === true) {
			$subject = $request->get_param('subject');
			$message = $request->get_param('message');
			$email = $request->get_param('email');
			$name = $request->get_param('name');			
			
			if (isset($subject) && isset($message) && isset($email)) {
				$message .= "<br/>Name: $name";
				$message .= "<br/>Email: $email";
				WppMailer::notify_admin($subject, $message, get_default_locale());
				return new WP_REST_Response(null, 204); // ACCEPTED/UPDATED, NO CONTENT TO RETURN
			}
			return new WP_REST_Response(null, 409); // CONFLICT, MISSING DATA
		}
		return new WP_REST_Response(null, 403); // FORBIDDEN
	}

	/**
	 * Try to get the base64 representation o the image. If not, return the image full url
	 *
	 * @param string $relative_image_url
	 * @return string
	 */
	public function try_get_image_in_base64($relative_image_url) {
		$type = pathinfo($relative_image_url, PATHINFO_EXTENSION);
		$local_path = $_SERVER["DOCUMENT_ROOT"].$relative_image_url;
		$data = file_get_contents($local_path);
		if ($data) {
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			return $base64;
		}
		return network_home_url($relative_image_url);
	}
	
	/**
	 * Run mass mail sender
	 *
	 * @param 
	 */
	public function send_notifications () {
		$deactivated = get_option("wpp_deactivate_news_sending");
		if ($deactivated !== "yes") {
			$this->process_pending_notifications();
			$this->debug();
		}		
	}

	/**
	 * Get the news mail template
	 *
	 * @return String
	 */
	public function get_news_template($lang = null) {
		$lang = $lang ? $lang : get_default_locale();
		$template = file_get_contents(WPP_PLUGIN_PATH."/includes/mail/templates/$lang/news.html");
		return $template;
	}

	/**
	 * Get the other items news sub template
	 *
	 * @return String
	 */
	public function get_related_template($lang = null) {
		$lang = $lang ? $lang : get_default_locale();
		$template = file_get_contents(FAM_MAIL_PLUGIN_PATH."/includes/mail/templates/$lang/news_other_items.html");
		return $template;
	}

	/**
	 * checks whenever a notification must be created when a given post is saved
	 *
	 * @param Integer $post_ID from which a notification would be created
	 * @return boolean
	 */
	public function must_create_notification($post_ID) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
			return false;
		// AJAX? Not used here
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
			return false;	
		// Return if it's a post revision
		if ( false !== wp_is_post_revision( $post_ID ) ) {
			return false;		
		}
		
		// check if there is already a generated notification for this post
		$args = (
			array(
				"post_type"=> $this->notification_post_type, 
				"post_status"=> $this->follower_initial_post_status,
				'meta_query' => array(
					array(
						'key'=> $this->generated_post_id_meta_key,
						'value'=> $post_ID
					)
				)
			)
		);
		$already_generated = get_posts($args);

		// if there is already a notification, skip generating anew one
		if (count($already_generated) > 0) {
			return false;
		}

		// in not aborting condition is detected, return true
		return true;
	}

	/**
	 * Generate/store notification post when a new post is created
	 *
	 * @param Integer $post_ID
	 * @param WP_Post $post
	 * @return void
	 */
	public function generate_notification_based_on_created_content($post_ID, $post) {			
		if (!$this->must_create_notification($post_ID)) {
			return;
		}		

		$deactivated = get_option("wpp_deactivate_news_generation");
		if ($deactivated === "yes") {
			return;
		}
		
		// Get the pos types that supports `send_news`
		$support_send_news_types = get_post_types_by_support($this->notification_post_type);	
		
		// Built in `post` also supports notification
		$support_send_news_types[] = "post";
		
		// This covers the post types with no post type in permalink
		if (in_array($post->post_type, $support_send_news_types) && $post->post_status === "publish") {
			$content_image = get_the_post_thumbnail_url($post_ID);			
			$url_parts = explode("//", network_home_url());
			$content_lang_slug = $this->get_post_language($post_ID, "slug");
			$template = $this->get_news_template($content_lang_slug);
			
			$template = str_replace("{news-type}", $this->notification_content_type_desc, $template);	
			$template = str_replace("{content-url}", get_permalink($post_ID), $template);
			$template = str_replace("{content-image-src}", $content_image ? $content_image : "", $template);			
			
			if($content_image = ""){ $template = str_replace("{main-img-height}", "0", $template); } else {$template = str_replace("{main-img-height}", "290", $template);}

			$content = $post->post_excerpt !== "" ? $post->post_excerpt : get_sub_content($post->post_content, 500);
			$template = str_replace("{content-excerpt}", $content, $template);
			$template = str_replace("{content-title}", $post->post_title, $template);

			$template = $this->replace_related_template($template, $post_ID, $content_lang_slug);
			$message = str_replace("'","''", $template);

			$notification_id = wp_insert_post(
				array(
					"post_type"=> $this->notification_post_type, 
					"post_status"=> $this->follower_initial_post_status,
					"post_author"=> get_current_user_id(),
					"post_title"=> $post->post_title, 
					"post_content"=> $message, 
					"meta_input"=> array(
						"content_type"=> $this->notification_content_type, 
						"mail_list_type"=> $this->default_notification_type,
						$this->generated_post_id_meta_key=> $post->ID
					)
				)
			);
		
			$content_lang_id = $this->get_post_language($post_ID);
			if ($content_lang_id) {
				$term_arr = [$content_lang_id];
				wp_set_post_terms($notification_id, $term_arr, LOCALE_TAXONOMY_SLUG);
			}
		}
	}

	/**
	 * Get the post language
	 *
	 * @param Integer $post_id
	 * @param string $field
	 * @return id|slug
	 */
	public function get_post_language($post_ID, $field = "id") {
		$content_lang_taxonomies = wp_get_post_terms($post_ID, LOCALE_TAXONOMY_SLUG);
		if( is_array($content_lang_taxonomies) && count($content_lang_taxonomies) > 0) {
			if ($field === "id") {
				return $content_lang_taxonomies[0]->term_id;
			}
			return $content_lang_taxonomies[0]->slug;
		}
	}


	/**
	 * Replace related template in news mail template
	 *
	 * @param String $template
	 * @param Integer $post_ID
	 * @return String
	 */
	public function replace_related_template($template, $post_ID, $lang_slug) {
		$related_post_ids = get_field('related', $post_ID);
		$related = get_posts(array( 'post__in' => $related_post_ids));

		if(is_array($related) && count($related) > 2) {
			$template_other_items = $this->get_related_template($lang_slug);
			$counter = 1;
			foreach($related as $related_post)
			{
				$template_other_items = str_replace("{related-content-url-".$counter."}", get_permalink($related_post->ID), $template_other_items);
				$template_other_items = str_replace("{related-content-image-src-".$counter."}", get_the_post_thumbnail_url($related_post->ID), $template_other_items);
				$template_other_items = str_replace("{related-content-title-".$counter."}", $related_post->post_title, $template_other_items);
				
				if($counter == 3) {
					break;
				}
				$counter++;
			}				
			
			$template = str_replace("{other-items}", $template_other_items, $template);
		} else {
			$template = str_replace("{other-items}", "", $template);	
		}
		return $template;
	}
	
	/**
	 * Process pending notification
	 *
	 * @return void
	 */
	public function process_pending_notifications() {			
		$pending_notifications = get_posts( array( 'post_type' => 'notification', 'orderby'  => 'id', 'order' => 'ASC', 'post_status' => 'publish'));

		$to = array();	
				
		if (is_array($pending_notifications) && count($pending_notifications) > 0 ) { 
			
			$pending_notification = $pending_notifications[0];
			$this->debug_output .= "<br/>Logging mails sent to email as html | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
			$this->debug_output .=" <br/> Returned pending mail ID ".$pending_notification->ID." <br/>";
					
			$to = $this->get_mails_to($pending_notification);	
			$this->debug_output .=" <br/> Returned mail subscribers to send amount: ".count($to)." <br/>";	
			if(!is_array($to) || count($to) == 0) {
				// Delete pending mail
				$this->debug_output .= "<br/>No more mail to send, deleting pending mail sending mail ".$pending_notification->ID."...";
				wp_delete_post($pending_notification->ID)					;		
			} elseif(is_array($to) && count($to) > 0) {				
				$this->debug_output .= "<br/>start sending mail... ";					
				$this->send_pending_notifications($to,$pending_notification);		
				$this->notify_mail_sent();		
			}						
		} else {		
			$this->debug_output .= 'No pending mail to send';
		}		
	}
	
	/**
	 * Send pending email
	 *
	 * @param String $to
	 * @param WP_Post $pending_notification
	 * @return void
	 */
	public function send_pending_notifications($to, $pending_notification) {	
		$insert_sent_sql = $this->base_insert_sent_sql;	
		if(is_array($to) && count($to) > 0) {
			$sender_name = get_option("wpp_email_sender_name");
			$sender_email = get_option("wpp_email_sender_email");
			$force_sender_email = get_option("wpp_force_sender_email");	
			$headers = [];

			if ($sender_email && $sender_name) {
				if(!is_localhost() && $force_sender_email === "yes") {
					$headers[] = "From: $sender_email <$sender_name>";
					$headers[] = "Reply-To: <$sender_email>";
				}
				$headers[] = "Return-Path: $sender_email <$sender_name>";
				$headers[] = "Sender: <$sender_name>";			
			}
					
			$content_type = get_post_meta($pending_notification->ID, "content_type", true);
			$mail_list_type = get_post_meta($pending_notification->ID, "mail_list_type", true);
			
			if($content_type == "html") {
				add_filter('wp_mail_content_type', 'set_email_html_content_type');	
				$counter = 1;	
				foreach($to as $email) {
					// Individualize content link for unsubscribe
					$unsubscribe_link = WppFollower::get_follower_unsubscribe_link($email);
					$message = str_replace("{unsubscribe-link}", $unsubscribe_link, $pending_notification->post_content);
					$result = wp_mail($mail,$pending_notification->post_title, $message, $headers);
					
					// If the message was sent
					if($result) {
						$this->debug_output .= "<br/>sent as html->".$mail." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
						if($counter > 1) {
							$insert_sent_sql.= ", ";
						}
						$insert_sent_sql .= " ('".$mail."', ".$pending_notification->ID. ", '".$mail_list_type."', '".$pending_notification->post_title."') ";						
						$sent_mail = new stdClass();
						$sent_mail->mail = $mail;
						$sent_mail->mail_title = $pending_notification->post_title;						
						$this->sent_mails[] = $sent_mail;
						$counter++;
					} else {
						$this->debug_output .= "<br/>Failed to send as html-> $sender_email,".$mail." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
						$result_info = json_decode($result);
						$this->debug_output .= "<br/>Result-> $result_info";
					}
				}
				remove_filter('wp_mail_content_type', 'set_email_html_content_type');
				global $wpdb;
				$wpdb->query($insert_sent_sql);
			} else {
				$counter = 1;	
				foreach($to as $mail) {
					$result = wp_mail($mail, $pending_notification->post_title, $pending_notification->post_content, $headers);
					error_log( print_r( $mail, true ) );
					if($result) {
						$this->debug_output .= "<br/>sent -> ".$to." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
						if($counter > 1) {
							$insert_sent_sql.= ", ";
						}
						$insert_sent_sql .= " ('".$mail."', ".$pending_notification->ID. ", '".$mail_list_type."', '".$pending_notification->post_title."') ";						
						$counter++;
					} else {
						$this->debug_output .= "<br/>Failed to send-> $sender_email,".$to." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
						$result_info = json_decode($result);
						$this->debug_output .= "<br/>Result-> $result_info";
					}
				}
				global $wpdb;
				$wpdb->query($insert_sent_sql);
			}						
		}				
	}
	
	/**
	 * Get the email where sent the email message to
	 *
	 * @param WP_Post $pending_notification
	 * @return String (comma separated emails)
	 */
	public function get_mails_to($pending_notification) {
		$mail_list_type = get_post_meta($pending_notification->ID, "mail_list_type", true);					
		$to = $this->get_mail_list($pending_notification->ID, $pending_notification->post_title, $mail_list_type);
		$this->debug_output .="<br/> working on $mail_list_type mail...<br/>";
		return $to;
	}
	
	/**
	 * Get email list where send the email to
	 *
	 * @param Integer $id_pending_notification
	 * @param String $mail_title
	 * @param String $mail_list_type
	 * @return void
	 */
	public function get_mail_list($id_pending_notification, $mail_title, $mail_list_type) {
		$notification_term_list = wp_get_post_terms($id_pending_notification, LOCALE_TAXONOMY_SLUG, array("fields" => "all"));	
		$notification_lang_term_id = $notification_term_list[0]->term_id;

		global $wpdb;
		$prefix = $wpdb->prefix;
		$post_table_name = $prefix."posts";
		$post_meta_table_name = $prefix."postmeta";
		$term_relationship_table_name = $prefix."term_relationships";

		$sql = "select ID, (select meta_value from $post_meta_table_name where meta_key = '".WppFollower::$follower_email_field."' and post_id = ID limit 1) as email from 
		$post_table_name where post_type = '".WppFollower::$follower_post_type."' and (select meta_value from $post_meta_table_name where meta_key = '".WppFollower::$follower_email_field."' and post_id = ID limit 1) 
		not in (select wp_mail_sent.email from wp_mail_sent where mail_title = '".$mail_title."')		
		and ID in (SELECT post_id FROM $post_meta_table_name where post_id = ID and meta_key = '".WppFollower::$mail_list."' and meta_value = '".$mail_list_type."' )
		and ID in (SELECT post_id FROM $post_meta_table_name where post_id = ID and post_status = 'publish' )
		and ID in (SELECT object_id FROM $term_relationship_table_name where object_id = ID and term_taxonomy_id = $notification_lang_term_id) limit 0,".$this->max_notifications_per_time;
		
		$followers = $wpdb->get_results($sql);

		$insert_sql = $this->base_insert_sent_sql;
		$to_list = '';
		if(count($followers) > $this->max_notifications_per_time) {
			$followers	= array_slice($followers, 0, count($followers) -1);
		}
		$to_array = array();
		foreach($followers as $to) {
			$to_array[] = $to->email;
			
			if($to_list == "") {				
				$insert_sql .= " ( '".$to->email."', ".$id_pending_notification.", '".$mail_list_type."','".$mail_title."')";
			}
			else {				
				$insert_sql .= ", ( '".$to->email."', ".$id_pending_notification.", '".$mail_list_type."','".$mail_title."' )";
			}	
		}
		$wpdb->query($insert_sql);		
		return $to_array;
	}
	
	
	/**
	 * Notify the site admin about the email sent
	 *
	 * @return void
	 */
	public function notify_mail_sent() {
		$skip_email_sending_notification = get_option("wpp_skip_email_sending_notification");
		if(count($this->sent_mails) > 0 && $skip_email_sending_notification !== "yes")
		{
			$notify_send_mail_html = "Log of ".count($this->sent_mails)." sent emails at ".date("m/d/Y h:i:s", time()).":<br/><br/>";
			$notify_send_mail_html .= "Titles:".$this->sent_mails[0]->mail_title."<br/><br/>";
			foreach($this->sent_mails as $sent_mail)
			{
				$notify_send_mail_html .=  "To: ".$sent_mail->mail."<br/>";
			}
			$blogname = get_option("blogname");

			$title ="Mailing log - $blogname";
			WppMailer::notify_admin($title, $notify_send_mail_html, get_default_locale());
		}
	}
	
	/**
	 * Output debug about email sending
	 *
	 * @return void
	 */
	public function debug() {		
		if(isset($_GET["debug"]) && $_GET["debug"] === "yes") {
			// $content = "<html lang='pt-BR'><head><meta charset='UTF-8'></head><body><h2>debug is on</h2>".$this->debug_output."</body></html>";
			$breaks = array("<br />","<br>","<br/>");  
    	$content = str_ireplace($breaks, "\r\n",  $this->debug_output); 
			echo $content;
		} else {			
			wp_redirect( network_home_url()."", 301);
			exit;
		}
 }
}

register_activation_hook(__FILE__, array('WppNotifier', 'activate_wpp_plugin'));

?>
