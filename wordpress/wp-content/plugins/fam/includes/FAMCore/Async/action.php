<?php
if($_SERVER["SERVER_NAME"] == "teste.fazendoasmalas.com" || $_SERVER["SERVER_NAME"] == "fazendoasmalas.com")
{
	require_once("../../wp-load.php");
	require_once(ABSPATH."/wp-content/FAMComponents/widget.php");
	
	define('DOING_AJAX',true);//in observation
	
	$action = $_POST["action"];
	if($action == null)
	{
		$action = $_GET["action"];
	}


	if($action == "logout")
	{
		wp_logout();
		echo "success";
	}

	else if($action == "login")
	{
		$creds = array();		
		$creds['user_login'] = $_POST["username"];
		$creds['user_password'] = $_POST["password"];
		$creds['remember'] = true;
		$user = wp_signon( $creds, false);	
		global $wpdb;		
		$sql = "SELECT meta_value FROM wp_fam_usermeta WHERE meta_key = 'status_viajante' and user_id = ".$user->ID;	
		$status =	$wpdb->get_var($sql);		
		if($status == 'disabled')
		{
			wp_logout();
			echo "disabled";
		}
		elseif ($user->ID > 0)
		{
			if ( is_wp_error($user) )
			{
				echo "error";
			}
			else
			{
				echo "success";
			}
		}
		
	}


	else if($action == "login_form")
	{	
		echo "<div class='container' id='login_container_div' style='display:none' >
					<div class='login_container'>
						<p>
							<label for='user_login'>
								Nome de usuário
								<br/>
								<input type='text' id='user_login' class='input' size='20' />
							</label>
						</p>
						<p>
							<label for='user_pass'>
								Senha
								<br/>
								<input type='password' id='user_pass' class='input' size='20' />
							</label>
						</p>
						<p>
							<a class='login_btn' id='login_btn' href='javascript:void(0);'>login</a>
						</p>
					</div>
				</div>			
			";
	}
	
	else if($action == "register_form")
	{	
			echo "<div class='container' id='register_container_div' style='display:none' >
					<div class='login_container'>
						<p>
							<label for='user_name'>
								Nome
								<br/>
								<input type='text' id='user_name' class='input' size='20' />
							</label>
						</p>
						<p>
							<label for='user_email'>
								Email
								<br/>
								<input type='text' id='user_email' class='input' size='20' />
							</label>
						</p>
						<div class='accept_terms'>
							<input id='termos_uso' class='termos_de_uso_checkbox' type='checkbox' />
							<label for='termos_uso' name='lbl_termo' class='lbl_termo'>aceito o</label>
							<a class='termos_de_uso_link' href='javascript:void(0);' >termo de uso</a>
						</div>
						<p>
							<a class='register_btn'  href='javascript:void(0);'>Cadastrar</a>
						</p>
						
					</div>
				</div>			
			";
	}

	else if($action == "reset_password_form")
	{
		echo "<div class='container' id='reset_password_container_div' style='display:none' >
					<div class='login_container'>
						<p>
							<label for='user_login'>
								Nome de usuário ou email
								<br/>
								<input type='text' id='user_login' class='input' size='20' />
							</label>
						</p>						
						<p>
							<a class='login_btn' id='send_password_btn' href='javascript:void(0);'>enviar</a>
						</p>
					</div>
				</div>			
			";
	}

	else if($action == "get_comment_form")
	{
		$parentId = $_GET["parentId"];
		widget::Get('share', array('hideCommentBox'=>true,'show_native_comment_form'=>'yes','hideShareBtns'=>true, 'parentId'=>$parentId));
	}

	else if($action == "register_user")
	{			
		$user_name =  $_POST["username"];
		$email = str_replace("'","",$_POST["email"]);
		$email = str_replace('"','',$email);
		global $wpdb;
		$sql = "select * from wp_fam_users where user_email = '".$email."'";
		$email_exist = $wpdb->get_results($sql);	
		if (count($email_exist) == 0  ) { //&& 1==2 cadastro de novos usuario desabilitado no momento
			$pass = wp_generate_password();
			$user_id = wp_create_user( $email, $pass, $email);		
			$sanitizedUserName = str_replace(" ","", sanitize_user($user_name));
			if($user_id != null && $user_id > 0) //cadastrado com sucesso
			{
				wp_update_user( array('ID' => $user_id,'user_nicename' => $sanitizedUserName, 'display_name' => $user_name));
				update_user_meta($user_id, 'nickname', $sanitizedUserName);
				$user = new WP_User($user_id); 
				$user->set_role('usuario_forum');
				wp_new_user_notification($user_id, $pass);	
				echo 'success';
			}
			else { 
				echo 'error'; 
			}

		} //usuario ja existe
		else {
				echo 'exists';
		}	
	}

	else if($action == "get_current_blog_id")
	{
		echo GetSiteIdFromUrl($_POST["site_url"]);	
	}



	else if($action == "send_reset_password_link")
	{
		$userLogin = $_POST["user_login"];			
		if ( strpos( $_POST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', trim($userLogin) );				
		} else {
			$login = trim($userLogin);
			$user_data = get_user_by('login', $login);
		}

		do_action('lostpassword_post');			

		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
			
		do_action('retrieve_password', $user_login);

		$allow = apply_filters('allow_password_reset', true, $user_data->ID);	

		$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
		if ( empty($key) ) {
			// Generate something random for a key...
			$key = wp_generate_password(20, false);
			do_action('retrieve_password_key', $user_login, $key);
			// Now insert the new md5 key into the db
			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
		}
		$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
		$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

		if ( is_multisite())
		{
			$blogname = $GLOBALS['current_site']->site_name;
		}
		else	
		{			
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		}

		$title = sprintf( __('[%s] Password Reset'), $blogname );
		$title = apply_filters('retrieve_password_title', $title);
		$message = apply_filters('retrieve_password_message', $message, $key);
		$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>'. "\r\n";		
		$headers .= 'Reply-To: Fazendo as Malas <contato@fazendoasmalas.com>';
		$sent = wp_mail($user_email, $title, $message,$headers);
		if($sent === true)
		{
			echo "success";
		}
		else
		{
			echo "error";
		}		
	}

	elseif ($action == "send_email")
	{				
		$subject = $_POST["assunto"];
		$message .= $_POST["mensagem"];
		$message .= "<br/><br/>";
		$message .= "Enviado por ".$_POST["nome"];
		$message .= "<br/><br/>";
		$message .= "Email: ".$_POST["email"];			
		$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>'. "\r\n";		
		$headers .= 'Reply-To: '.$_POST["nome"].' <'.$_POST["email"].'>';
		$template = file_get_contents(ABSPATH.'/Templates/Mail/notification.html');
		
		add_filter('wp_mail_content_type', 'set_html_content_type' );
		
		$template = file_get_contents(ABSPATH.'/Templates/Mail/notification.html');
		$template = str_replace("{site-url}", get_bloginfo('url'), $template);			
		$template = str_replace("{content-excerpt}", $message, $template);
		$template = str_replace("{content-title}", $subject, $template);	
		$template = str_replace("teste.faz", "faz", $template);				
		$template = str_replace("{current_year}", date('Y'), $template);		
		$template = str_replace("{news-type}", "Contato Fazendo as Malas", $template);							
		$message = $template;
		$sent = wp_mail("amoncaldas@gmail.com",$subject,$message,$headers);			
						
		if($sent === true)
		{	
			$sent = false;		
			$subject = "Fazendo as Malas - Recebemos seu email";
			$message = "Recebemos a mensagem abaixo. Em breve entraremos em contato.";
			$message .= "<br/><br/>";	
			$message .= '"'.$_POST["mensagem"].'"';	
			$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>'. "\r\n";		
			$headers .= 'Reply-To: Fazendo as Malas <contato@fazendoasmalas.com>';
			
			$template = file_get_contents(ABSPATH.'/Templates/Mail/notification.html');
			$template = str_replace("{site-url}", get_bloginfo('url'), $template);			
			$template = str_replace("{content-excerpt}", $message, $template);
			$template = str_replace("{content-title}", "Confirmação de recebimento", $template);	
			$template = str_replace("teste.faz", "faz", $template);				
			$template = str_replace("{current_year}", date('Y'), $template);		
			$template = str_replace("{news-type}", "Contato pelo site", $template);							
			$message = $template;		
			$sent = wp_mail($_POST["email"],$subject,$message,$headers);		
			echo "success";
		}
		else
		{
			echo "error";
		}
		remove_filter('wp_mail_content_type', 'set_html_content_type');		
	
	}
	
	elseif ($action == "notify_fam")
	{				
		$subject = $_POST["assunto"];
		$message .= $_POST["mensagem"];
		$message .= "<br/>";		
		$message .= "<br/>";	
		$message .= "Enviado por ".$_POST["nome"];
		$message .= "<br/>";	
		$message .=	"IP: http://www.ip2location.com/". $_SERVER["REMOTE_ADDR"];
		$message .= "<br/><br/>";	
		$message .=	"UserAgent:". $_SERVER['HTTP_USER_AGENT'];				
					
		$template = file_get_contents(ABSPATH.'/Templates/Mail/notification.html');		
		$template = str_replace("{site-url}", get_bloginfo('url'), $template);			
		$template = str_replace("{content-excerpt}", $message, $template);
		$template = str_replace("{content-title}", $_POST["assunto"], $template);	
		$template = str_replace("teste.faz", "faz", $template);				
		$template = str_replace("{current_year}", date('Y'), $template);		
		$template = str_replace("{news-type}", "Notificação", $template);							
		$message = $template;			
			
		$headers = 'From: Fazendo as Malas Comment <comments@fazendoasmalas.com>'. "\r\n";		
		$headers .= 'Reply-To: Fazendo as Malas Comment <comments@fazendoasmalas.com>';	
						
		add_filter('wp_mail_content_type', 'set_html_content_type' );		
		$sent = wp_mail("amoncaldas@gmail.com",$subject,$message,$headers);
		remove_filter('wp_mail_content_type', 'set_html_content_type');	
				
		if($sent === true)
		{			
			echo "success";
		}
		else
		{
			echo "error";
		}	
	}

	elseif($action == "get_widget")
	{	
		if($_POST["site_id"] != null)
		{			
			switch_to_blog($_POST["site_id"]);			
			widget::Get($_POST["name"], $_POST);
			restore_current_blog();
		}
		else
		{
			widget::Get($_POST["name"], $_POST);
		}
	}

	elseif($action == "get_places")
	{		
		if(is_user_logged_in() === true)
		{
			global $facebook_loader;			
			$fb_token = $facebook_loader->credentials["access_token"];
			
			$lat = $_POST['latitude'];
			$long = $_POST['longitude'];
			
			if($lat != null && $long != null)
			{
				if ($_POST['place_name'] != null)
				{
					$place_name = $_POST['place_name'];				
					$fql = 'SELECT%20name,page_id,name,description,type%20FROM%20place%20WHERE%20strpos(lower(name),%20lower(%20%22'.$place_name.'%22%20)%20)%20>%3D%200%20and%20distance(latitude,longitude,%20%22'.$lat.'%22,%20%22'.$long.'%22)%20%3C%201000%20ORDER%20BY%20distance(latitude,%20longitude,%20%22'.$lat.'%22,%20%22'.$long.'%22)%20LIMIT%2010';								
				}
				else
				{
					$fql = 'SELECT%20name,page_id,name,description,type%20FROM%20place%20WHERE%20is_city%20and%20distance(latitude,longitude,%20%22'.$lat.'%22,%20%22'.$long.'%22)%20%3C%2010000%20ORDER%20BY%20distance(latitude,%20longitude,%20%22'.$lat.'%22,%20%22'.$long.'%22)%20LIMIT%202';
					
					
				}
			}			
			
			if($fql != null)
			{
				$fql_URL = 'https://graph.facebook.com/fql?q=' . ($fql) . '&access_token=' . $fb_token;					
				$result = json_decode(file_get_contents($fql_URL));
				if (count($result->data)>0) {					
					echo json_encode($result);			
				}
				else
				{
					echo "error";
				}
			}
			
			
		}
		
	}
	elseif($action == "get_fb_access_token")
	{		
		if( is_user_logged_in() === true && (get_user_role() != "usuario_forum"))
		{
			global $facebook_loader;			
			echo $facebook_loader->credentials["access_token"];			
		}
		else
		{
			echo get_user_role();
		}
		
	}
	elseif($action == "get_youtube_thumb")
	{
		if(is_user_logged_in() === true && (get_user_role() != "usuario_forum") || Is_test_enviroment())
		{
			$post_id = $_POST['post_id'];
			$blog_url = $_POST['blog_url'];
		
			$url = str_replace("http://","", $blog_url);
			$url = str_replace("https://","", $url);
			$urlparts = split("/", $url);
			$domain = trim($urlparts[0], "/");
			$sub_site = split("/",$urlparts[1]);
			$sub_site =  trim($sub_site[0], "/");
			
			$table_prefix = "wp_fam_posts";
			if($sub_site == "wp-admin")
			{
				$blog_id = 1;
			}
			else
			{					
				$blog_id = get_blog_id_from_url($domain, "/".$sub_site."/");	
				$table_prefix = "wp_fam_".$blog_id."_posts";
			}			
			
			global $wpdb;	
				
			$sql = "SELECT guid FROM ".$table_prefix." where ID = ".$post_id." and post_mime_type = 'video/x-flv'";	
			$guid =	$wpdb->get_var($sql);		
								
			if($guid != null)//if is video
			{				
				$videoUrlParts = explode("embed", $guid);				
				$icon = "http://img.youtube.com/vi/".trim($videoUrlParts[1],"/")."/hqdefault.jpg";	
				echo $icon;					
			}
		}
		
	}
	
	elseif($action == "reorder_posts")
	{		
		if($_POST["save_posts_order"] == "yes" && CheckLoggedIn() && $_POST["site_id"] > 1)
		{					
			switch_to_blog( $_POST["site_id"]);				
			$itens = explode(";",$_POST["posts_itens_order"]);
			$result = 'error';
			foreach($itens as $item)
			{
				$item_parts = explode(",",$item);					
				//if ( current_user_can('edit_destaque', $item_parts[0]))	
				//{
					$postitem = array('ID' => $item_parts[0], 'menu_order' => $item_parts[1]);		
					wp_update_post($postitem);
					$result = 'success';
				//}
			}
			echo $result;
			restore_current_blog();	
		}
	}
	
	elseif($action == "update_post")
	{		
		
		if($_POST["site_url"] != null && (is_user_logged_in()) && $_POST["id"] > 0)
		{					
			$post_id = $_POST["id"];
			$site_id = GetSiteIdFromUrl($_POST["site_url"]);								
			switch_to_blog($site_id);								
				
			$_p = array();
			$_p["ID"] = $post_id;
				
			$post_type = get_post_type($post_id);				
			$userslug = CheckLoggedIn();			
			//$user = get_user_by( 'slug', $userslug);				
			$user = wp_get_current_user();
			$post = get_post( $post_id );										
				
			if (in_array("administrator", $user->roles) || is_super_admin($user->ID) || ( user_can($user, "edit_".$post_type) && $user->data->ID ==  $post->post_author ) )
			{				
				$_p['post_title'] = $_POST["title"];
				$_p['post_name'] = sanitize_title($_POST["title"]);	
				if($_POST["status"] != null && $_POST["status"] != 'keep-status')
				{		
					$_p['post_status'] =  $_POST["status"];		
				}			
				if($post_type == "relatos")
				{
					$_p["post_content"] = $_POST["content"];
				}
				wp_update_post($_p);
				update_post_meta($post_id, $_POST["upload_prefix"]."_fam_upload_id_",$_POST['mediasId']);
				update_post_meta($post_id, 'seo_desc', $_POST["seo_desc"]);
				if($_POST["latitude"] != "" && $_POST["longitude"] != "")
				{
					update_post_meta($post_id, 'local', $_POST["local"]);
					update_post_meta($post_id, 'latitude', $_POST["latitude"]);
					update_post_meta($post_id, 'longitude', $_POST["longitude"]);
					
				}
				if($post_type == "atualizacao")
				{						
					$content = str_replace("Digite o texto do status aqui","",$_POST["content"]);
					update_post_meta($post_id, 'conteudo',$_POST["content"]);
				}
				if($post_type == "albuns")
				{						
					$content = str_replace("Digite a descrição do álbum aqui","",$_POST["content"]);						
					update_post_meta($post_id, 'descricao_album',$content);
				}					
					
				if($post_type == "destaque")
				{
					echo get_site_url();
				}
				elseif($post_type == "viagem")
				{
					echo get_site_url()."/viagem";
				}
				else
				{
					if($post_type == "atualizacao") $post_type = "status";
					if($post_type == "blog_post") $post_type = "blog";
					$permalink = "/".$post_type."/". $_p['post_name']."/".$post_id."/";
					$permalink = str_replace("//","/",$permalink);	
					echo get_site_url().$permalink;
				}			
			}			
			restore_current_blog();	
		}
	}
	
	elseif($action == "optin_news")
	{		
		if (!filter_var($_POST["news_mail"], FILTER_VALIDATE_EMAIL)) {
			echo "error";
		}
		else
			{
				if($_POST["news_mail"] != null)
				{				
					$email = str_replace("'","",str_replace('"',"",$_POST["news_mail"]));
					$name = str_replace("'","",str_replace('"',"",$_POST["news_name"]));
					global $wpdb;
					$sqlstr = "SELECT id from wp_fam_news_subscribers where news_mail = '".$email."'";	
					if(is_user_logged_in() && get_current_blog_id() == 1 && in_array(get_user_role(),array('administrator','adm_fam_root'))  && $_POST["opt_out"] == "yes")
					{		
						$news_user_exist = $wpdb->get_results($sqlstr);								
						if(is_array($news_user_exist) && count($news_user_exist) > 0)
						{
							$sqldelete = " delete from wp_fam_news_subscribers where news_mail = '".$email."'";					
							$wpdb->query($wpdb->prepare($sqldelete));
							$subject = "Cancelamento da assinatura Fazendo as Malas";
							$body = "Olá. ".$name." \n\n";
							$body .= "A inscrição para receber atualizações de viagem para o email ".$email."  foi cancelada. ";						
							
							$template = file_get_contents(ABSPATH.'/Templates/Mail/notification.html');
							$template = str_replace("{site-url}", get_bloginfo('url'), $template);							
							
							$template = str_replace("{content-excerpt}", $body, $template);
							$template = str_replace("{content-title}", "Cancelamento da assinatura", $template);	
							$template = str_replace("teste.faz", "faz", $template);				
							$template = str_replace("{current_year}", date('Y'), $template);				
							$template = str_replace("{news-type}", "Cancelamento da assinatura", $template);							
							$message = $template;
							
							
							$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>'. "\r\n";		
							$headers .= 'Reply-To: Fazendo as Malas <contato@fazendoasmalas.com>';					
							add_filter('wp_mail_content_type', 'set_html_content_type' );		
							wp_mail($email,$subject,$message,$headers);
							remove_filter('wp_mail_content_type', 'set_html_content_type');
							
																	
							echo "excluded";
						}	
						else
						{
							echo "not_excluded";
						}		
						
					}
					else
					{			
						if($name == null || $name == "" || strlen($name) == 0)
						{
							$name = "viajante";
						}					
						
						$news_user_exist = $wpdb->get_results($sqlstr);					
						if(is_array($news_user_exist) && count($news_user_exist) > 0)
						{
							echo "exist";
						}
						else
						{
							$ip = $_SERVER["REMOTE_ADDR"];	
							$user_agent = $_SERVER['HTTP_USER_AGENT'];					
							$sql = "insert into wp_fam_news_subscribers (news_name, news_mail, ip, user_agent) values ('".$name."', '".$email."', '".$ip."', '".$user_agent."')";	
							$wpdb->query($wpdb->prepare($sql));				
							
							$subject = "Assinatura para atualizações de viagem Fazendo as Malas";
							$body = "Olá ".$name." <br/><br/>";
							$body .= "Você se cadastrou com sucesso para receber atualizações de viagem no site Fazendo as Malas. Se considerar que seu email foi cadastrado por engano ou desejar remover o mesmo do nosso site responda esse email com o título 'Cancelar'.";
							
							$template = file_get_contents(ABSPATH.'/Templates/Mail/notification.html');
							$template_original = $template;
							$template = str_replace("{site-url}", get_bloginfo('url'), $template);							
							
							$template = str_replace("{content-excerpt}", $body, $template);
							$template = str_replace("{content-title}", "Assinatura de atualizações Fazendo as Malas", $template);	
							$template = str_replace("teste.faz", "faz", $template);				
							$template = str_replace("{current_year}", date('Y'), $template);				
							$template = str_replace("{news-type}", "Assinatura Fazendo as Malas", $template);							
							$message = $template;	
							
							add_filter('wp_mail_content_type', 'set_html_content_type' );													
							
							$sent = wp_mail($email, $subject, $message, $headers);
							
							$subject = "Um novo usuário assinou a news Fazendo as Malas";
							$body = "O usuário ".$name." (".$email.")  se cadastrou para receber atualizações de viagem.";
							$body .= "<br/><br/>";	
							$body .= "IP: ".$ip." e UserAgent:".$user_agent;
							$body .= "<br/><br/>";	
							$body .= "http://www.ip2location.com/".$ip;
							
							$template = str_replace("{site-url}", get_bloginfo('url'), $template_original);							
							
							$template = str_replace("{content-excerpt}", $body, $template);
							$template = str_replace("{content-title}", "Assinatura de atualizações Fazendo as Malas", $template);	
							$template = str_replace("teste.faz", "faz", $template);				
							$template = str_replace("{current_year}", date('Y'), $template);				
							$template = str_replace("{news-type}", "Assinatura de atualizações", $template);							
							$message = $template;	
							
							$sent = wp_mail("amoncaldas@gmail.com", $subject, $message, $headers);	
							remove_filter('wp_mail_content_type', 'set_html_content_type');						
							
							echo "success";
							
							
						}
					}
				}
				else
				{
					echo "error";
				}
			}
	}
	elseif($action == "get_site_main_image")
	{		
		$site_id = GetSiteIdFromUrl($_POST["site_url"]);
		if($site_id > 1)
		{
			switch_to_blog($site_id);										
			require_once(FAM_PLUGIN_PATH."/FAMCore/BO/Destaque.php");				
			$destaque = new Destaque();
			$destaques = $destaque->GetDestaques(1);
			$mainimage = $destaques[0]->ImageCroppedSrc;
			echo $mainimage;	
			restore_current_blog();
		}						
		
	}
	elseif($action == "teste_envio_informativo")
	{		
		if(is_user_logged_in())
		{	
			switch_to_blog(1);				
			if (current_user_can("edit_informativo") )
			{			
				$url = $_POST["url_destiny"];		
				if($url == null){$url = network_home_url();}
				$template = file_get_contents(ABSPATH.'/Templates/Mail/news.html');
				$template = str_replace("{site-url}", get_bloginfo('url'), $template);
				$template = str_replace("{content-url}", $url, $template);
				$template = str_replace("{content-image-src}", "", $template);
				$template = str_replace("{main-img-height}", "0", $template);
				$content = str_replace('"',"'" ,  $_POST["content"]);
				$content = str_replace("\\", "", $content);							
				
				$template = str_replace("{content-excerpt}", $content, $template);
				$template = str_replace("{content-title}", $_POST["title"], $template);	
				$template = str_replace("teste.faz", "faz", $template);				
				$template = str_replace("{current_year}", date('Y'), $template);				
				$template = str_replace("{news-type}", "Novo informativo", $template);
				$template = str_replace("{other-itens}", "", $template);
				$message = $template;
					
				$test_destiny_email = $_POST["destiny_email"];
				$headers = 'From: Fazendo as Malas <contato@fazendoasmalas.com>'. "\r\n";		
				$headers .= 'Reply-To: Fazendo as Malas <contato@fazendoasmalas.com>';					
				add_filter('wp_mail_content_type', 'set_html_content_type' );		
				wp_mail($test_destiny_email,"[teste] ".$_POST["title"],$message,$headers);
				remove_filter('wp_mail_content_type', 'set_html_content_type');					
			}
			else
			{
				echo "error";
			}			
			restore_current_blog();	
		}
		else
		{
			echo "error";
		}
	}
	
	elseif($action = "get_image_map_src")
	{			
		$map_service_width = $_POST["map_service_width"];
		$map_service_height = $_POST["map_service_height"];
		$map_service_zoom = $_POST["map_service_zoom"];
		$map_service_lat = $_POST["map_service_lat"];
		$map_service_long = $_POST["map_service_long"];		
		
		$localFileName = "image_map|". str_replace(".",",",$map_service_lat)."|".str_replace(".",",",$map_service_long)."|".$map_service_zoom."|".$map_service_width."|".$map_service_height;
		$locaFile = "/wp-content/static_maps/".$localFileName.".png";		
		$absoluteFileLocation = $_SERVER["DOCUMENT_ROOT"].$locaFile;
					
		if(file_exists($absoluteFileLocation))
		{
			$imgSrc = $locaFile;				
		}
		else
		{
			$local_map_icon = 'http://fazendoasmalas.com/wp-content/themes/images/icoFam.png';
			$map_service_url = 'http://maps.googleapis.com/maps/api/staticmap?center'.$map_service_lat.','.$map_service_long.'&markers=icon:'.$local_map_icon.'|'.$map_service_lat.','.$map_service_long.'&zoom='.$map_service_zoom.'&size='.$map_service_width.'x'.$map_service_height.'&sensor=false&format=png8';
			$gmap = new SplFileObject($absoluteFileLocation,'w');
			$image = file_get_contents($map_service_url);
			$gmap->fwrite($image);		
			$imgSrc = $locaFile;
		}			
			
		echo $imgSrc;
	}	
}

function GetSiteIdFromUrl($url)
{
	$url = str_replace("http://","", $url);
	$url = str_replace("https://","", $url);
	$urlparts = split("/", $url);
	$domain = trim($urlparts[0], "/");
	$sub_site = split("/",$urlparts[1]);
	$sub_site =  trim($sub_site[0], "/");
	return get_blog_id_from_url($domain, "/".$sub_site."/");
}

function CheckLoggedIn()
{
	$logged_in = false;
	// checks are performed on usernames ONLY
	//$allowed_users = array($userName);
	if (count($_COOKIE)) {
		foreach ($_COOKIE as $key => $val) {
			if (substr($key, 0, 19) === "wordpress_logged_in") {
				//if (preg_match('/^(' . implode('|', $allowed_users) . ')/', $val, $matches)) {
					$userdata = explode("|",$_COOKIE[$key]);
					$logged_in = $userdata[0];
					
				//}
			}
		}
	}
	return $logged_in;
}
