<?php

/**
 * Class FamMail
 *
 * Description for class FamMail
 * WordPress options used: email_sender_name, email_sender_email, skip_email_sending_notification, deactivate_news_sending
 *
 * @author: Amon Caldas
*/


class WppMailer  {

	/**
	 * Notify admin via email using basic notification template
	 *
	 * @param [type] $title
	 * @param [type] $message
	 * @param [type] $type
	 * @return void
	 */
	public static function notify_admin($title, $message, $lang = null) {
		$site_title = get_bloginfo("name");
		$title = "[$site_title] $title";
		$message .= "<br/><br/>";	
		$message .= "IP: http://www.ip2location.com/". get_request_ip();
		$message .= "<br/><br/>";	
		$message .=	"UserAgent:". $_SERVER['HTTP_USER_AGENT'];	

		$template = self::get_basic_notification_template($title, $message, $lang);		
		
		$sender_name = get_option("wpp_email_sender_name");
		$sender_email = get_option("wpp_email_sender_email");
		$headers[] = "From: $sender_email <$sender_name>";	
		$headers[] = "Return-Path: <$sender_name>";
		$headers[] = "Sender: <$sender_name>";			

		add_filter('wp_mail_content_type','set_email_html_content_type');	
		$admin_email = get_option("admin_email");
		wp_mail($admin_email, $title, $message, $headers);			
		remove_filter('wp_mail_content_type', 'set_email_html_content_type');
  }
  
  /**
	 * Notify admin via email using basic notification template
	 *
	 * @param String $to_email
   * @param String $title
	 * @param String $message
	 * @param String $lang
	 * @return void
	 */
	public static function send_message($to_email, $title, $message, $lang = null) {
		$html_message = self::get_basic_notification_template($title, $message, $lang);
		self::send_email($to_email, $title, $html_message, $lang);
	}

	/**
	 * Send registration/activation mail
	 *
	 * @param String $to_email
   * @param String $title
	 * @param String $link
	 * @param String $lang
	 * @return void
	 */
	public static function send_registration_email($to_email, $title, $link, $lang = null) {
		$html_message = self::get_registration_notification_template($link, $lang);
		self::send_email($to_email, $title, $html_message, $lang);
	}

	/**
	 * Send registration/activation mail
	 *
	 * @param String $to_email
   * @param String $title
	 * @param String $link
	 * @param String $lang
	 * @return void
	 */
	public static function send_reset_password_email($to_email, $title, $link, $lang = null) {
		$html_message = self::get_password_reset_notification_template($link, $lang);
		self::send_email($to_email, $title, $html_message, $lang);
	}

	/**
	 * Notify admin via email using basic notification template
	 *
	 * @param String $to_email
   * @param String $title
	 * @param String $message
	 * @param String $lang
	 * @return void
	 */
	public static function send_email($to_email, $title, $html_message, $lang = null) {		
		$sender_name = get_option("wpp_email_sender_name");
		$sender_email = get_option("wpp_email_sender_email");		

		$headers = [];
		
		if ($sender_email && $sender_name) {
			if(!is_localhost()) {
				$headers[] = "From: $sender_email <$sender_name>";
				$headers[] = "Reply-To: <$sender_email>";
			}
			$headers[] = "Return-Path: $sender_email <$sender_name>";
			$headers[] = "Sender: <$sender_name>";			
		}

		add_filter('wp_mail_content_type','set_email_html_content_type');	
		wp_mail($to_email, $title, $html_message, $headers);			
		remove_filter('wp_mail_content_type', 'set_email_html_content_type');
	}


	/**
	 * Get the news mail template
	 *
   * @param String $lang
	 * @return String
	 */
	public function get_news_template($lang = null) {
		$lang = $lang ? $lang : get_default_locale();
		$template = file_get_contents(WPP_PLUGIN_PATH."/includes/mail/templates/$lang/news.html");
		return $template;
	}

	/**
	 * Get the news mail template
	 * @param String $title
   * @param String $message
   * @param String $lang
	 * @return String html
	 */
	public static function get_basic_notification_template($title, $message, $lang = null) {
		$lang = $lang ? $lang : get_default_locale();
    $template = file_get_contents(WPP_PLUGIN_PATH."/includes/mail/templates/$lang/notification.html");
    $logo_url = network_home_url(get_option("wpp_site_relative_logo_url"));
    $url_parts = explode("//", network_home_url());
    
    $template = str_replace("{site-url}", network_home_url(), $template);
		$template = str_replace("{site-name}", get_bloginfo("name"), $template);
		$template = str_replace("{site-domain}", $url_parts[1], $template);
		$template = str_replace("{site-logo-url}", $logo_url, $template);			
		$template = str_replace("{content-title}", $title, $template);
		$template = str_replace("{content}", $message, $template);
    $html_message = str_replace("{current-year}", date('Y'), $template);	
    
		return $html_message;
	}

	/**
	 * Get the news mail template
	 * @param String $link
   * @param String $lang
	 * @return String html
	 */
	public static function get_registration_notification_template($link, $lang = null) {
		$lang = $lang ? $lang : get_default_locale();
    $template = file_get_contents(WPP_PLUGIN_PATH."/includes/mail/templates/$lang/registration.html");
    $logo_url = network_home_url(get_option("wpp_site_relative_logo_url"));
    $url_parts = explode("//", network_home_url());
    
		$template = str_replace("{site-url}", network_home_url(), $template);
		$template = str_replace("{link}", $link, $template);
		$template = str_replace("{site-name}", get_bloginfo("name"), $template);
		$template = str_replace("{site-domain}", $url_parts[1], $template);
		$template = str_replace("{site-logo-url}", $logo_url, $template);			
    $html_message = str_replace("{current-year}", date('Y'), $template);	
    
		return $html_message;
	}

	/**
	 * Get the news mail template
	 * @param String $link
   * @param String $lang
	 * @return String html
	 */
	public static function get_password_reset_notification_template($link, $lang = null) {
		$lang = $lang ? $lang : get_default_locale();
    $template = file_get_contents(WPP_PLUGIN_PATH."/includes/mail/templates/$lang/reset-password.html");
    $logo_url = network_home_url(get_option("wpp_site_relative_logo_url"));
    $url_parts = explode("//", network_home_url());
    
		$template = str_replace("{site-url}", network_home_url(), $template);
		$template = str_replace("{link}", $link, $template);
		$template = str_replace("{site-name}", get_bloginfo("name"), $template);
		$template = str_replace("{site-domain}", $url_parts[1], $template);
		$template = str_replace("{site-logo-url}", $logo_url, $template);			
    $html_message = str_replace("{current-year}", date('Y'), $template);	
    
		return $html_message;
	}
}

?>
