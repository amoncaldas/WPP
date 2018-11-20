<?php
require_once(ABSPATH. "/FAMCore/BO/Media.php");
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/MediaVO.php' );


function youtube_tab($tabs) {
    /* name of custom tab */
	$new_tab = array('youtube_video' => __('Youtube Vídeo', 'youtube_video_upload'));
    return array_merge($tabs, $new_tab);
}

/* create tab youtube video*/
add_filter('media_upload_tabs', 'youtube_tab');
function create_youtube_video_page() {
	if (!current_user_can('add_videos'))
	{
		wp_die( __( 'Cheatin&#8217; uh?' ) );
	}
	
    media_upload_header();
    wp_enqueue_style('media');        
		echo "
	<div class='youtube_video'>
		<h3 class='media-title'>Adicione um vídeo do Youtube</h3>
		<div class='steps_to_add_youtube_video'>
			<h4>Passos para adicionar um vídeo do youtube:</h4>
			<ol>
				<li>
					<span>Faça o upload do vídeo no </span> <a target='_blank' href='http://www.youtube.com/upload'>site youtube.com/upload</a>
				</li>
				<li>
					<span>Copie a url  <strong>completa</strong>  do vídeo no youtube. A url deve conter o termo <strong>watch?v=</strong> como na imagem abaixo </span>
					<div class='youtube_url_image'>
						<img src='/wp-content/themes/images/youtube-url.png'>
					</div>
				</li>
				<li>
					<span>Cole a url copiada no campo Youtube url e preencha o título. Os campos de localização são opcionais</span>
				</li>
			</ol>
		</div>
		<table cellpadding='10px' cellspacing='0' style='width:100%' class='manual_add_table'>
			<tr>
				<td style='/*border-right:solid silver 1px;*/ padding-top:0px;' valign='top'>
					<form name='manualAddForm' method=post>
						<table cellpadding='0' class='widefat'>
							<tr>
								<td class='field'>		
									Título*<br>									
									<input type='text' class='text ' id='mlv_title' name='mlv_title' value='' />
									<p class='help'>Informe a url do vídeo no Youtube</p>																		
								</td>
							</tr>
							<tr >								
								<td class='field'>
									Youtube url (link)*<br>
									<input type='text' class='text' id='mlv_embedCod' name='mlv_embedCode' value='' />
									<p class='help'>Informe o título do vídeo</p>
								</td>
							</tr>
							<tr class='compat-field-attachment_location'>								
								<td class='field'>
									Localização da mídia<br>
									<input type='text' class='text' id='video_location' name='video_location' value='' />
									<p class='help'>Informe a localização geográfica da mídia.</p>
								</td>
							</tr>
							<tr class='compat-field-attachment_latitude'>							
								<td class='field'>
									Latitude da mídia<br>
									<input type='text' class='text' id='video_latitude' name='video_latitude' value=''  />
									<p class='help'>Latitude (digite o local para preencher automaticamente)</p>
								</td>
							</tr>
							<tr class='compat-field-attachment_longitude'>							
								<td class='field'><input type='text' class='text' id='video_longitude' name='video_longitude' value='' />
									Longitude da mídia<br>
									<p class='help'>Longitude (digite o local para preencher automaticamente)</p>
								</td>
							</tr>
							<tr class='compat-field-attachment_script'>
								<th valign='top' scope='row' class='label'>
									<label for='attachments-2314-attachment_script'>
										<span class='alignleft'>Carregando...</span>								
									</label>
								</th>
								<td class='field'>
									<div id='media_location_script_container'>
										<script type='text/javascript'>jQuery(document).ready(function () { CheckMediaLoaded();});</script>
									</div>
								</td>
							</tr>
							<tr class='compat-field-attachment_script'>
								<td>
									<input type='submit' value='Adicionar Video' class='button'>									
									<input type='hidden' name='save_fam_youtube_video' value='yes' id='save_fam_youtube_video'>									
								</td>
							</tr>
						</table>
					</form>
				</td>				
			</tr>
		</table>
		<div class='youtube_iframe'>
				
		</div>	
		".ProcessVideoSave()."		
	</div>";		      
}


	
function ProcessVideoSave()
{	
	if ($_POST["save_fam_youtube_video"] == "yes") {			
		$result = Media::InsertVideo($_POST["mlv_title"], $_POST["mlv_embedCode"], $_POST["video_location"], $_POST["video_latitude"], $_POST["video_longitude"]);
			
		if($result["status"] == "success")
		{
			$videoVO = new MediaVO($result["videoId"]);
			$videoImagemUrl = "http://teste.fazendoasmalas.com/passeiopelooriente/wp-includes/images/crystal/video.png";
			$click_event = 'onclick="';
			$click_event .= "selectAsUploadedImg(".$result["videoId"].", '".$videoImagemUrl."',this.parentNode);$(this).html('ok')";
			$click_event .= '"';
			return "
			<div class='video_saved'>
				<span>Vídeo cadastrado com sucesso!</span>
				<iframe class='fancybox' class='youtube-player' type='text/html' width='190' height='140' src='".$videoVO->MainUrl."?origin=".$videoVO->MainUrl."' frameborder='0'></iframe>
			</div>
			<div class='select_video'>
				<div class='filename' style='display:none;'>
					<span class='title'>".$_POST["mlv_title"]."</span>
				</div>
				<a href='javascript:void(0);' ".$click_event." class='button'>Selecionar vídeo</a>
			</div>
			";
		}
		else		
		{
			return "<div class='video_save_error'><span>".$result["error_desc"]."</span></div>";
		}	
	}	
}

function insert_youtube_video_iframe() {
		return wp_iframe( 'create_youtube_video_page');
}
add_action('media_upload_youtube_video', 'insert_youtube_video_iframe');
add_action( 'admin_head', 'youtube_video_frame_css' );

function youtube_video_frame_css() {
        echo '
		<style type="text/css">
			.media-title{margin-left:25px;}	
			.youtube_video .widefat{width:580px !important;}
			.youtube_video .video_saved, .youtube_video video_save_error{margin-left:22px;font-size:16px;float:left;width:300px}
			.youtube_video .video_saved {float:left; width:50%;}
			.youtube_video .video_saved span {float:left; width:90%;display:block;margin-bottom:20px;}
			.youtube_video input[type=text]{width:100%;}
			.youtube_video .select_video {float:left;}
			.steps_to_add_youtube_video{margin-left:20px;}
			.steps_to_add_youtube_video ol li{margin-bottom:10px;}
			.steps_to_add_youtube_video h4{margin-left:5px;}
			.steps_to_add_youtube_video ol li span{}
			.steps_to_add_youtube_video ol li a{}
			.steps_to_add_youtube_video .youtube_url_image{}			
        </style>';
}
