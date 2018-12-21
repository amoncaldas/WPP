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

	public $base_insert_sent_sql = "insert into wp_mail_sent (email, id_pending_notification,mail_list_type, mail_title) values ";
	public $debug_output = "";
	public $Times = 0;
	public $MaxNotificationsPerTime = 50;
	public $sent_mails = array();

	// Defining values and keys to generate the notification
	public $notification_post_type = "notification";
	public $notification_post_status = "pending";
	public $notification_content_type = "html";
	public $generated_post_id_meta_key = "generated_from_post_id";
	public $default_notification_type = "news";
	public $notification_content_type_desc = "newsletter";

	function __construct () {
		add_action( 'save_post', array($this, 'generate_notification_based_on_created_content'), 100, 2);
		add_filter( 'rest_api_init', array( $this, 'register_routes' ) );		
	}

	/**
     * Register ORS oauth routes for WP API v2.
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
	}

	/**
	 * Run mass mail sender
	 *
	 * @param 
	 */
	public function send_notifications () {
		$deactivated = get_option("deactivate_news_sending");
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
	function get_news_template($lang = "pt-br") {
		$template = file_get_contents(WPP_PLUGIN_PATH."/includes/mail/templates/$lang/news.html");
		return $template;
	}

	/**
	 * Get the other items news sub template
	 *
	 * @return String
	 */
	function get_related_template($lang = "pt-br") {
		$template = file_get_contents(FAM_MAIL_PLUGIN_PATH."/includes/mail/templates/$lang/news_other_items.html");
		return $template;
	}

	/**
	 * checks whenever a notification must be created when a given post is saved
	 *
	 * @param Integer $post_ID from which a notification would be created
	 * @return boolean
	 */
	function must_create_notification($post_ID) {
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
				"post_status"=> $this->notification_post_status,
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
	function generate_notification_based_on_created_content($post_ID, $post) {			
		if (!$this->must_create_notification($post_ID)) {
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
			$logo_url = network_home_url(get_option("site_relative_logo_url"));

			$content_lang_slug = $this->get_post_language($post_ID, "slug");

			$template = $this->get_news_template($content_lang_slug);
			
			$template = str_replace("{site-url}", network_home_url(), $template);
			$template = str_replace("{news-type}", $this->notification_content_type_desc, $template);
			
			$template = str_replace("{site-name}", get_bloginfo("name"), $template);
			$template = str_replace("{site-domain}", $url_parts[1], $template);
			$template = str_replace("{site-logo-url}", $logo_url, $template);			
			$template = str_replace("{content-url}", get_permalink($post_ID), $template);
			$template = str_replace("{content-image-src}", $content_image ? $content_image : "", $template);
			$template = str_replace("{site-url}", network_home_url(), $template);
			
			
			if($content_image = ""){ $template = str_replace("{main-img-height}", "0", $template); } else {$template = str_replace("{main-img-height}", "290", $template);}

			$content = $post->post_excerpt !== "" ? $post->post_excerpt : $this->get_sub_content($post->post_content, 500);
			$template = str_replace("{content-excerpt}", $content, $template);
			$template = str_replace("{content-title}", $post->post_title, $template);
			$template = str_replace("{current-year}", date('Y'), $template);

			$template = $this->replace_related_template($template, $post_ID, $content_lang_slug);
			$message = str_replace("'","''", $template);

			$notification_id = wp_insert_post(
				array(
					"post_type"=> $this->notification_post_type, 
					"post_status"=> $this->notification_post_status,
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
				wp_set_post_terms($notification_id, $term_arr, "lang");
			}

			add_action( 'admin_notices', array($this,'show_notification_created_admin_notice'));
		}
	}

	function show_notification_created_admin_notice() {		
		?>
		<div class="notice notice-info is-dismissible">
			<p>A notification was created and is pending for revision in the notifications <a href="/wp-admin/edit.php?post_type=<?php echo $this->notification_post_type ?>>">outbox</a></p>
		</div>
		<?php		
	}

	/**
	 * Get the post language
	 *
	 * @param Integer $post_id
	 * @param string $field
	 * @return id|slug
	 */
	function get_post_language($post_ID, $field = "id") {
		$taxonomy = "lang";
		$content_lang_taxonomies = wp_get_post_terms($post_ID, $taxonomy);
		if( is_array($content_lang_taxonomies) && count($content_lang_taxonomies) > 0) {
			if ($field === "id") {
				return $content_lang_taxonomies[0]->term_id;
			}
			return $content_lang_taxonomies[0]->slug;
		}
	}


	/**
	 * Get sub content of a string
	 *
	 * @param String $content
	 * @param Integer $length
	 * @param boolean $forcePrecision
	 * @return String
	 */
	function get_sub_content($content, $length, $forcePrecision = false) {
		$length = $length-3;
		$content = strip_tags($content, '');
		if(strlen($content) > $length) {		
			if($forcePrecision == true)	{			
				$content = trim($content);					
				if(strpos($content, " ") > 0 ) {		
					$content = substr($content, 0,$length + 200);						
					$offset = strlen($content);	
					$spacePosition = 	strlen($content);	
					while( ($pos = strrpos(($content)," ",$offset)) != false) {
						$offset = $pos;
						if($pos < $length) {						
							$spacePosition = $pos;							
							break;
						}
					}		
														
					$content = substr($content, 0, $spacePosition)."...";
					
					if(strlen($content) > $length + 3) {
						$content = substr($content, 0, $length)."...";
					}				
				}
				else {																		
					$content = substr($content, 0,$length)."...";
				}
			}
			else {
				if(strlen($content) > $length) {			
					if(strpos($content, " ", $length) > $length) {
						$content =  substr($content, 0, strpos($content, " ", $length))."...";
					}
					else {
						$length = ($length > 20)? ($length -20): 0;								
						$content =  substr($content, 0, strpos($content, " ", $length))."...";
					}				
				}
			}	
			$content = rtrim($content, ',');
			$content = rtrim($content, '-');		
			return $content;
		}
		else {
			return $content;
		}			
	}


	/**
	 * Replace related template in news mail template
	 *
	 * @param String $template
	 * @param Integer $post_ID
	 * @return String
	 */
	function replace_related_template($template, $post_ID, $lang_slug) {
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
	function process_pending_notifications() {			
		$pending_notifications = get_posts( array( 'post_type' => 'notification', 'orderby'  => 'id', 'order' => 'ASC', 'post_status' => 'publish'));
		$to = array();	
				
		if (is_array($pending_notifications) && count($pending_notifications) > 0 ) { 
			
			$pending_notification = $pending_notifications[0];
			$this->debug_output .= "<br/>Logging mails sent to email as html | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
			$this->debug_output .=" <br/> Returned pending mail ID ".$pending_notification->ID." <br/>";
					
			$to = $this->get_mails_to($pending_notification);	
			$this->debug_output .=" <br/> Returned mail subscribers to send amount: ".count($to)." <br/>";	
			if(!is_array($to) || count($to) == 0)	
			{
				// Delete pending mail
				$this->debug_output .= "<br/>No more mail to send, deleting pending mail sending mail ".$pending_notification->ID."...";
				wp_delete_post($pending_notification->ID)					;		
			}
			elseif(is_array($to) && count($to) > 0)
			{				
				$this->debug_output .= "<br/>start sending mail... ";					
				$this->send_pending_notifications($to,$pending_notification);		
				$this->notify_mail_sent();		
			}						
		}	
		else
		{		
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
	function send_pending_notifications($to, $pending_notification) {	
		$insert_sent_sql = $this->base_insert_sent_sql;	
		if(is_array($to) && count($to) > 0)
		{		
			$senderName = get_option("email_sender_name");
			$senderEmail = get_option("email_sender_email");
			$headers[] = "From: $senderEmail <$senderName>";	
			$headers[] = "Return-Path: <$senderName>";
			$headers[] = "Sender: <$senderName>";
					
			$content_type = get_post_meta($pending_notification->ID, "content_type", true);
			$mail_list_type = get_post_meta($pending_notification->ID, "mail_list_type", true);
			
			if($content_type == "html") {
				add_filter('wp_mail_content_type', 'set_html_content_type' );	
				$counter = 1;	
				foreach($to as $mail)
				{
					$success = wp_mail($mail,$pending_notification->post_title, $pending_notification->post_content, $headers);
					if($success)
					{
						$this->debug_output .= "<br/>sent as html->".$mail." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
						if($counter > 1)
						{
							$insert_sent_sql.= ", ";
						}
						$insert_sent_sql .= " ('".$mail."', ".$pending_notification->ID. ", '".$mail_list_type."', '".$pending_notification->post_title."') ";						
						$sent_mail = new stdClass();
						$sent_mail->mail = $mail;
						$sent_mail->mail_title = $pending_notification->post_title;						
						$this->sent_mails[] = $sent_mail;
						$counter++;
					}
					else
					{
						$this->debug_output .= "<br/>Failed to send as html-> $senderEmail,".$mail." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
					}
				}
				remove_filter('wp_mail_content_type', 'set_html_content_type');
				global $wpdb;
				$wpdb->query($insert_sent_sql);
			}
			else// Get the pos types that supports `no_post_type_in_permalink`
			$no_post_type_in_permalink_types = get_post_types_by_support("no_post_type_in_permalink");
	
			// Get the post types that supports `parent_section` and `section_in_permalink`
			$section_in_permalink_types = get_post_types_by_support(array("parent_section","section_in_permalink"));				
			
			// This covers the post types with no post type in permalink
			if (in_array($post->post_type, $no_post_type_in_permalink_types)) {
				$permalink = $this->get_permalink_with_no_post_type_in_it($post, $permalink);
			} 
			{
				$counter = 1;	
				foreach($to as $mail)
				{
					$success = wp_mail($mail, $pending_notification->post_title, $pending_notification->post_content, $headers);
					if($success)
					{
						$this->debug_output .= "<br/>sent -> ".$to." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
						if($counter > 1)
						{
							$insert_sent_sql.= ", ";
						}
						$insert_sent_sql .= " ('".$mail."', ".$pending_notification->ID. ", '".$mail_list_type."', '".$pending_notification->post_title."') ";
						
						$counter++;
					}
					else
					{
						$this->debug_output .= "<br/>fail to send-> $senderEmail,".$to." | ".$pending_notification->post_title." on - ".date('m/d/Y h:i:s', time());
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
	function get_mails_to($pending_notification) {
		$mail_list_type = get_post_meta($pending_notification->ID, "mail_list_type", true);					
		$to = $this->get_mail_list($pending_notification->ID, $pending_notification->post_title, $mail_list_type);
		$this->debug_output .="<br/> working on news mail...<br/>";
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
	function get_mail_list($id_pending_notification, $mail_title, $mail_list_type) {
		$notification_term_list = wp_get_post_terms($id_pending_notification, "lang", array("fields" => "all"));	
		$notification_lang_term_id = $notification_term_list[0]->term_id;

		global $wpdb;
		$prefix = $wpdb->prefix;
		$post_table_name = $prefix."posts";
		$post_meta_table_name = $prefix."postmeta";
		$term_relationship_table_name = $prefix."term_relationships";

		$sql = "select ID, (select meta_value from $post_meta_table_name where meta_key = 'email' and post_id = ID limit 1) as email from 
		$post_table_name where post_type = 'follower' and (select meta_value from $post_meta_table_name where meta_key = 'email' and post_id = ID limit 1) 
		not in (select wp_mail_sent.email from wp_mail_sent where mail_title = '".$mail_title."')		
		and ID in (SELECT post_id FROM $post_meta_table_name where post_id = ID and meta_key = 'mail_list' and meta_value = '".$mail_list_type."' )
		and ID in (SELECT post_id FROM $post_meta_table_name where post_id = ID and meta_key = 'activated' and meta_value = 1 )
		and ID in (SELECT object_id FROM $term_relationship_table_name where object_id = ID and term_taxonomy_id = $notification_lang_term_id) limit 0,".$this->MaxNotificationsPerTime;
		
		$followers = $wpdb->get_results($sql);

					
		// $followers = get_posts( array( 'post_type' => 'follower', 'orderby'  => 'id', 'order' => 'ASC', 'post_status' => 'publish', 'meta_key' => 'mail_list', 'meta_value' => $mail_list_type));
		$insert_sql = $this->base_insert_sent_sql;
		$to_list = '';
		if(count($followers) > $this->MaxNotificationsPerTime)
		{
			$followers	= array_slice($followers, 0, count($followers) -1);
		}
		$to_array = array();
		foreach($followers as $to)
		{

			$to_array[] = $to->email;
			
			if($to_list == "")
			{				
				$insert_sql .= " ( '".$to->email."', ".$id_pending_notification.", '".$mail_list_type."','".$mail_title."')";
			}
			else
			{				
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
	function notify_mail_sent() {
		$skip_email_sending_notification = get_option("skip_email_sending_notification");
		if(count($this->sent_mails) > 0 && $skip_email_sending_notification !== "yes")
		{
			$notify_send_mail_html = "Log of ".count($this->sent_mails)." sent emails at ".date("m/d/Y h:i:s", time()).":<br/><br/>";
			$notify_send_mail_html .= "Titles:".$this->sent_mails[0]->mail_title."<br/><br/>";
			foreach($this->sent_mails as $sent_mail)
			{
				$notify_send_mail_html .=  "Para: ".$sent_mail->mail."<br/>";
			}
				
			$senderName = get_option("email_sender_name");
			$senderEmail = get_option("email_sender_email");
			$adminEmail = get_option("admin_email");
			$blogname = get_option("blogname");
			
			
			$headers[] = "From: $senderName <$senderEmail>";	
			$headers[] = 'Return-Path: <$senderEmail>';
			$headers[] = "Sender: <$senderEmail>";
			add_filter('wp_mail_content_type', 'set_html_content_type' );			
			$success = wp_mail($adminEmail,"Mailing log - $blogname",$notify_send_mail_html,$headers);
			remove_filter('wp_mail_content_type', 'set_html_content_type');
		}
	}
	
	/**
	 * Output debug about email sending
	 *
	 * @return void
	 */
	function debug() {		
		if(isset($_GET["debug"]) && $_GET["debug"] === "yes")
		{
			$content = "<html lang='pt-BR'><head><meta charset='UTF-8'></head><body><h2>debug is on</h2>".$this->debug_output."</body></html>";
			echo $content;
		}
		else
		{			
			wp_redirect( network_home_url()."", 301);
			exit;
		}
  	}
  
	function insert_fake_test() {
		if( is_user_logged_in() && in_array(get_user_role(),array('adm_fam_root','administrator')));
		{			
		$current_date = date("m/d/Y h:i:s", time());
		$sql_test_pending = "INSERT INTO wp_fam_pending_notification( subject, content, content_type, mail_list_type, site_id ) VALUES ('auto generated teste',  'auto generated teste -".$current_date."',  'html',  'news', 1)";	
		$wpdb->query($wpdb->prepare($sql_test_pending));
		
		$sql_test_pending = "INSERT INTO wp_fam_pending_notification( subject, content, content_type, mail_list_type, site_id ) VALUES ('auto generated teste 2',  'auto generated teste -".$current_date."',  'html',  'news', 1)";	
		$wpdb->query($wpdb->prepare($sql_test_pending));
		}
	}
}

?>
