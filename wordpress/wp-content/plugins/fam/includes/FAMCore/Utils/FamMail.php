<?php

/**
 * class FamMail
 *
 * Description for class FamMail
 *
 * @author:
*/

try
{
	require_once("../../wp-load.php");
}
catch(Exception $e){}
	
try
{
	//require_once("/var/www/teste.fazendoasmalas.com/wp-load.php");
}
catch(Exception $e){}



class FamMail  {

	public $base_insert_sent_sql = "insert into wp_fam_mail_sent (email, id_pending_mail,mail_list_type, mail_title) values ";
	public $Debug_output = "";
	public $Times = 0;
	public $MaxNotificationsPerTime = 50;
	public $Sent_mails = array();

	/**
	 * FamMail constructor
	 *
	 * @param 
	 */
	function FamMail() {
		$this->ProcessPendingMails();
		$this->Debug();
	}
	
	function ProcessPendingMails()
	{		
		global $wpdb;
		$sql = "SELECT * FROM  wp_fam_pending_mail LIMIT 0 , 2";		
		$pending_mails = $wpdb->get_results($sql);
		$to = array();	
				
		if (is_array($pending_mails) && count($pending_mails) > 0 ) { 
			
			$this->Debug_output .= "<br/>Loging mails sent to email as html | ".$pending_mail->subject." on - ".date('m/d/Y h:i:s', time());
			$pending_mail = $pending_mails[0];
			$this->Debug_output .=" <br/> Returned pendging mail ID ".$pending_mail->ID." <br/>";
					
			$to = $this->GetMailsTo($pending_mail);	
			$this->Debug_output .=" <br/> Returned mail subscribers to send amount: ".count($to)." <br/>";	
			if(!is_array($to) || count($to) == 0)	
			{
				//delete pending mail
				$this->Debug_output .= "<br/>No more mail to send, deleting pending mail sedind mail ".$pending_mail->ID."...";
				$delete_pending_sql = " delete from wp_fam_pending_mail where ID = ".$pending_mail->ID;						
				$wpdb->query($delete_pending_sql);	//desabilitado temporariamente			
			}
			elseif(is_array($to) && count($to) > 0)
			{				
				$this->Debug_output .= "<br/>start sedind mail... ";					
				$this->SendPendingMails($to,$pending_mail);		
				$this->NotifiMailSent();		
			}
						
		}	
		else
		{		
			$this->Debug_output .= 'No pending mail to send';
		}		
	}
	
	function SendPendingMails($to, $pending_mail)
	{	
		$insert_sent_sql = $this->base_insert_sent_sql;	
		if(is_array($to) && count($to) > 0)
		{		
			$headers[] = 'From: Fazendo as Malas <contato@fazendoasmalas.com>';	
			$headers[] = 'Return-Path: <contato@fazendoasmalas.com>';
			$headers[] = "Sender: <contato@fazendoasmalas.com>";
												
			//$this->Debug_output .= "<br/>Header:".var_export($headers,true)."<br/>";
							
			if($pending_mail->content_type == "html")
			{
				add_filter('wp_mail_content_type', 'set_html_content_type' );	
				$counter = 1;	
				foreach($to as $mail)
				{
					$success = wp_mail($mail,$pending_mail->subject,$pending_mail->content,$headers);
					if($success)
					{
						$this->Debug_output .= "<br/>sent as html->".$mail." | ".$pending_mail->subject." on - ".date('m/d/Y h:i:s', time());
						if($counter > 1)
						{
							$insert_sent_sql.= ", ";
						}
						$insert_sent_sql .= " ('".$mail."', ".$pending_mail->ID. ", '".$pending_mail->mail_list_type."', '".$pending_mail->subject."') ";						
						$sent_mail = new stdClass();
						$sent_mail->mail = $mail;
						$sent_mail->mail_title = $pending_mail->subject;						
						$this->Sent_mails[] = $sent_mail;
						$counter++;
					}
					else
					{
						$this->Debug_output .= "<br/>fail to send as html-> noreply@fazendoasmalas.com,".$mail." | ".$pending_mail->subject." on - ".date('m/d/Y h:i:s', time());
					}
				}
				remove_filter('wp_mail_content_type', 'set_html_content_type');
				global $wpdb;
				$wpdb->query($insert_sent_sql);
				//$this->Debug_output .= "<br/><br/>Html insert sent sql->".$insert_sent_sql." </br>";
			}
			else
			{
				$counter = 1;	
				foreach($to as $mail)
				{
					$success = wp_mail($mail,$pending_mail->subject,$pending_mail->content,$headers);
					if($success)
					{
						$this->Debug_output .= "<br/>sent -> ".$to." | ".$pending_mail->subject." on - ".date('m/d/Y h:i:s', time());
						if($counter > 1)
						{
							$insert_sent_sql.= ", ";
						}
						$insert_sent_sql .= " ('".$mail."', ".$pending_mail->ID. ", '".$pending_mail->mail_list_type."', '".$pending_mail->subject."') ";
						
						$counter++;
					}
					else
					{
						$this->Debug_output .= "<br/>fail to send-> noreply@fazendoasmalas.com,".$to." | ".$pending_mail->subject." on - ".date('m/d/Y h:i:s', time());
					}
				}
				global $wpdb;
				$wpdb->query($insert_sent_sql);
				//$this->Debug_output .= "<br/><br/>No html insert sent sql->".$insert_sent_sql." </br>";
			}	
									
		}				
	}
	
	function GetMailsTo($pending_mail)
	{
		if($pending_mail->mail_list_type == "news")
		{			
			$to = $this->GetNewsMailList($pending_mail->ID, $pending_mail->subject);
			$this->Debug_output .="<br/> working on news mail...<br/>";
		}
		elseif($pending_mail->mail_list_type == "temp")
		{				
			$to = $this->GetTempMailList($pending_mail->ID);
			$this->Debug_output .=" <br/> working on temp mail...<br/>";
		}
		elseif( strpos($pending_mail->mail_list_type,"comment_post_")> -1)
		{
			$post_ID_parts = explode("comment_post_",$pending_mail->mail_list_type);
			$post_ID = $post_ID_parts[1];
			$to = $this->GetCommentersMailList($post_ID, $pending_mail->ID, $pending_mail->site_id, $pending_mail->subject);
			$this->Debug_output .="<br/> working on comment_post with ID ".$post_ID."...<br/>";
			$this->Debug_output .="<br/> sending to mail list: ".$to."...<br/>";
		}
		
		return $to;
	}
	
	function GetNewsMailList($id_pending_mail, $mail_title)
	{
		global $wpdb;
		//$sql = "select news_mail from wp_fam_news_subscribers LEFT JOIN wp_fam_mail_sent ON news_mail = email AND mail_list_type = 'news' and id_pending_mail = ".$id_pending_mail." WHERE email IS NULL limit 0, ".$this->MaxNotificationsPerTime;
		$sql = "select news_mail from wp_fam_news_subscribers where news_mail not in (select email from wp_fam_mail_sent where mail_title = '".$mail_title."') limit 0,".$this->MaxNotificationsPerTime;
		
		//$this->Debug_output .=" <br/> SQL for get mails to:".$sql."<br/>";
				
		$mail_list = $wpdb->get_results($sql);
		$insert_sql = $this->base_insert_sent_sql;
		$to_list = '';
		if(count($mail_list) > $this->MaxNotificationsPerTime)
		{
			$mail_list	= array_slice($mail_list, 0, count($mail_list) -1);
		}
		$to_array = array();
		foreach($mail_list as $to)
		{
			$to_array[] =$to->news_mail;
			
			if($to_list == "")
			{				
				$insert_sql .= " ( '".$to->news_mail."', ".$id_pending_mail.", 'news','".$mail_title."')";
			}
			else
			{				
				$insert_sql .= ", ( '".$to->news_mail."', ".$id_pending_mail.", 'news','".$mail_title."' )";
			}	
		}
		$wpdb->query($wpdb->prepare($insert_sql));		
		return $to_array;
	}
	
	function GetTempMailList($id_pending_mail)
	{
		global $wpdb;
		//$sql = "select temp_email from wp_fam_temp_mail_list  where id_pending_mail = ".$id_pending_mail." limit 0, ".$this->MaxNotificationsPerTime;
		
		$mail_list = $wpdb->get_results($sql);
		if(count($mail_list) > $this->MaxNotificationsPerTime)
		{
			$mail_list	= array_slice($mail_list, 0, count($mail_list) -1);
		}
		$insert_sql = $this->base_insert_sent_sql;
		$to_list = '';
		$delete_temp_sent = "DELETE FROM wp_fam_mail_temp where temp_email =  ";
		$to_array = array();
		foreach($mail_list as $to)
		{
			$to_array[] = $to->email;
			if($to_list == "")
			{				
				$delete_temp_sent .= "'".$to->temp_email."'";
			}
			else
			{				
				$delete_temp_sent .= " or temp_email = '".$to->temp_email."'";
			}	
		}
		$wpdb->query($wpdb->prepare($delete_temp_sent));		
		return $to_array;
	}
	
	function NotifiMailSent()
	{
		if(count($this->Sent_mails) > 0)
		{
			$notify_send_mail_html = "Log de ".count($this->Sent_mails)." emails enviados para assinantes FAM em ".date("m/d/Y h:i:s", time()).":<br/><br/>";
			$notify_send_mail_html .= "Título:".$this->Sent_mails[0]->mail_title."<br/><br/>";
			foreach($this->Sent_mails as $sent_mail)
			{
				$notify_send_mail_html .=  "Para: ".$sent_mail->mail."<br/>";
			}
					
			$headers[] = 'From: Fazendo as Malas <contato@fazendoasmalas.com>';	
			$headers[] = 'Return-Path: <contato@fazendoasmalas.com>';
			$headers[] = "Sender: <contato@fazendoasmalas.com>";
			add_filter('wp_mail_content_type', 'set_html_content_type' );			
			$success = wp_mail("amoncaldas@gmail.com","FAM - Log de emails enviados",$notify_send_mail_html,$headers);
			remove_filter('wp_mail_content_type', 'set_html_content_type');
			
			//$this->Debug_output .="<br/> notifi list: ".var_export($this->Sent_mails,true)."<br/>";
		}
	}
	
	function GetCommentersMailList($post_ID, $id_pending_mail, $site_id, $email_title)
	{
		$this->Debug_output .="<br/> working on pending mail with ID ".$id_pending_mail."...<br/>";
		global $wpdb;
		if($site_id == 1)
		{
			$site_id = "";
		}
		else
		{
			$site_id = "_".$site_id;
		}
		$sql = "SELECT distinct comment_author_email FROM wp_fam".$site_id."_comments LEFT JOIN wp_fam_mail_sent ON comment_author_email = email AND mail_list_type = 'comment_post_".$post_ID."' and id_pending_mail = ".$id_pending_mail." WHERE email IS NULL AND comment_post_ID = ".$post_ID ;
		$sql .= " union SELECT user_email as comment_author_email FROM wp_fam_posts inner join wp_fam_users on wp_fam_users.ID = wp_fam_posts.post_author  where wp_fam_posts.ID = ".$post_ID."  limit 0, ".$this->MaxNotificationsPerTime;
		$mail_list = $wpdb->get_results($sql);	
		
		$this->Debug_output .= " <br/> SQL executed to get users: ".$sql."<br/><br/>";	
		
		$this->Debug_output .= " <br/> Returned objet from database: ".var_export($mail_list,true)."<br/>";	
		
		if(count($mail_list) > $this->MaxNotificationsPerTime)
		{
			$mail_list	= array_slice($mail_list, 0, count($mail_list) -1);
		}
		$insert_sql = $this->base_insert_sent_sql;	
		$to_array = array();
		foreach($mail_list as $to)
		{	
			$to_array[] = $to->comment_author_email;
			if($to_list == "")
			{				
				$insert_sql .= " ( '".$to->comment_author_email."', ".$id_pending_mail.", 'comment_post_".$post_ID."','".$email_title."' )";
			}
			else
			{				
				$insert_sql .= ", ( '".$to->comment_author_email."', ".$id_pending_mail.", 'comment_post_".$post_ID."','".$email_title."' )";
			}		
		}
		$wpdb->query($wpdb->prepare($insert_sql));	
		$this->Debug_output .= " <br/> Returned TO list: ".implode(",",$to_array);
		return $to_array;
	}
	
	function Debug()
	{		
		if((is_user_logged_in() && in_array(get_user_role(),array('adm_fam_root','administrator')) || Is_test_enviroment()) && $_GET["debug"] == "yes")
		{
			$content = "<html lang='pt-BR'><head><meta charset='UTF-8'></head><body><h2>debug is on</h2>".$this->Debug_output."</body></html>";
			echo $content;
		}
		else
		{			
			wp_redirect( network_home_url()."", 301);exit;
		}
	}
}

if($_GET["fake"] == "yes")
{
	if( is_user_logged_in() && in_array(get_user_role(),array('adm_fam_root','administrator')));
	{			
		$current_date = date("m/d/Y h:i:s", time());
		$sql_test_pending = "INSERT INTO wp_fam_pending_mail( subject, content, content_type, mail_list_type, site_id ) VALUES ('auto generated teste',  'auto generated teste -".$current_date."',  'html',  'news', 1)";	
		$wpdb->query($wpdb->prepare($sql_test_pending));
	
		$sql_test_pending = "INSERT INTO wp_fam_pending_mail( subject, content, content_type, mail_list_type, site_id ) VALUES ('auto generated teste 2',  'auto generated teste -".$current_date."',  'html',  'news', 1)";	
		$wpdb->query($wpdb->prepare($sql_test_pending));
	}
}


new FamMail();

?>