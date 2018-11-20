<?php

/**
 * class Galeria
 *
 * Description for class Galeria
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Conteudo.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/AlbumVO.php' );

class Galeria  extends Conteudo {

	public $Albuns;
	public $HasMoreData;
	/**
		* Galeria constructor
		*
		* @param 
		*/
	function Galeria($viagemId = null) {
		parent::Conteudo("albuns", $viagemId);
	}		
	
	/**
		* @return RelatoVO[]
	*/
	public  function GetAlbuns($options) {
		$albuns = $this->GetItens($options);
			
		if ($albuns)
		{		
			if($this->MultiSiteData == true)
			{			
				return $albuns;
			}
			else
			{	
				$albunsVO = array();				
				foreach ($albuns as $album)
				{		
					$albumVO = new AlbumVO($album->ID,$options['onlycover'] == "yes");					
					$albunsVO[] = $albumVO;					
				}			
			}
			return $albunsVO;	
		}		
	}	
	
	public static function GetMediasId($options)
	{
		$mediasId = get_post_meta($options["parentId"], $options["prefix"]."_fam_upload_id_", true);	
		
		if(strpos($mediasId,";") > -1)
		{			
			$ids_medias = explode(";",$mediasId);
			
			if($options["excluded_ids"] != null)
			{
				if(!is_array($options["excluded_ids"]))
				{
					$options["excluded_ids"] = split(",",$options["excluded_ids"]);
				}							
			}
			
			$ids_medias = explode(";",$mediasId);
			$itensAdded = 0;
			foreach($ids_medias as $mediaId)
			{	
				if($options["excluded_ids"] == null || !in_array($mediaId, $options["excluded_ids"]))
				{			
					$ids[] = $mediaId;
					$itensAdded++;					
					if($options["itens"] != null && $options["itens"] > 0 && $itensAdded >= $options["itens"])
					{
						break;
					}
				}					
			}			
		}
		elseif($mediasId != null && $mediasId != "" )
		{							
			$ids = array($mediasId);			
		}
		else
		{
			$ids = array();
		}
		
		return $ids;
	}
		
	public static function GetAlbumById($id, $itens = null) {		
			
		if($itens != null && $itens > 0)
		{				
			$albumVO = new AlbumVO();
			$album = $albumVO->GetAlbumItens($id, $itens);						
			return $album;
		}
		else
		{
			return $albumVO =  new AlbumVO($id);
		}		
		//return $relatoVO->GetRelatoVOList($posts);				
	}
		
	public  function GetData($options) {	
		$itens = $options["itens"];
		$options["itens"]++;		
		$albuns = $this->GetAlbuns($options);		
		if(is_array($albuns) && count($albuns) > $itens)
		{	
			$this->HasMoreData = true;
			$albuns	= array_slice($albuns, 0, count($albuns) -1);
		}		
			
		return $albuns;				
	}
	
	public function Save($dataArray)
	{
		$return = array();
		$return["result"] = true;
		if(!current_user_can('edit_albuns'))
		{
			$return["errors"][] = "Usuário sem permissão para salvar álbum.";
		}
		if($dataArray["p"] != null && !current_user_can('edit_albuns',$dataArray["p"]))
		{
			unset($return["errors"]["Usuário sem permissão para salvar álbum"]);
			$return["errors"][] = "Usuário sem permissão para salvar este álbum.";
		}
		if($dataArray["post_title"] == null || strlen($dataArray["post_title"]) == 0)
		{
			$return["errors"][] = "O título deve ser informado.";
		}
		if($dataArray["local"] == null || strlen($dataArray["local"]) == 0)
		{
			$return["errors"][] = "O local deve ser informado.";
		}
		if(strpos($dataArray["descricao_album"],"Digite a descrição do álbum aqui") > -1)
		{
			$dataArray["descricao_album"] = str_replace("Digite a descrição do álbum aqui","",$dataArray["descricao_album"]);
		}
		
		if($dataArray["descricao_album"] == null || strlen($dataArray["descricao_album"]) == 0)
		{
			$return["errors"][] = "A descrição do álbum deve ser preenchida.";
		}
		
		if(is_array($return["errors"]) && count($return["errors"]) > 0)
		{
			$return["result"] = false;
			return $return;
		}
		else
		{
			$_p = array();
			$_p['post_title'] = $dataArray["post_title"];
			$_p['post_name'] = sanitize_title($dataArray["post_title"]);			
			$_p['post_status'] = 'publish';
			if($dataArray["action"] == "draft")
			{
				$_p['post_status'] = 'draft';
			}
			$_p['post_type'] = 'albuns';
			$_p['comment_status'] = 'open';
			$_p['ping_status'] = 'open';
			$_p['post_category'] = array(1); // the default 'Uncatrgorised'	
		
			if($dataArray["p"] != null && $dataArray["p"] > 0)
			{
				$_p["ID"] = $dataArray["p"];
				$albumId = $dataArray["p"];				
				wp_update_post($_p);
				if($dataArray["action"] == "update")
				{
					$return["success"] = "Álbum atualizado com sucesso.";
				}				
				elseif($dataArray["action"] == "draft")
				{
					$return["success"] = "O álbum salvo como rascunho e atualizado com sucesso. Lembre-se que ao salvá-lo como rascunho o mesmo deixa de estar visível para o público.";
				}
				elseif($dataArray["action"] == "save")
				{
					$return["success"] = "Álbum publicado com sucesso.";
				}	
				SaveImages($albumId, $dataArray);
				update_post_meta($albumId, 'local', $dataArray["local"]);
				update_post_meta($albumId, 'latitude', $dataArray["latitude"]);
				update_post_meta($albumId, 'longitude', $dataArray["longitude"]);
				update_post_meta($albumId, 'descricao_album',$dataArray["descricao_album"]);
				$return["result"] = true;
				$return["data"]  = new AlbumVO($albumId);
				return $return;				
			}
			else
			{
				$return["result"] = false;
				$return["errors"][] = "Deculpe, mas ocorreu um erro ao salvar o álbum";				
				return $return;	
			}			
			
			
		}			
	}
	
	public function GetMobileForm($dataArray)
	{
		$return = array();
		$return["result"] = true;
		$albumId = $dataArray["p"];
		$draft_label = "rascunho";
		$return = array();
		$return["result"] = true;
		
		if($albumId != null)
		{
			$post = get_post($albumId);
			
			if($post->ID > 0)
			{
				if(!current_user_can('edit_albuns', $albumId))	
				{
					$return["errors"][] = "Usuário sem permissão para editar este álbum";
					return $return;
				}
				else
				{		
					$albumVO = new AlbumVO($albumId);					
					$albumVO->AlbumUrl = str_replace($draft_label."/","", $albumVO->AlbumUrl);		
				}
			}
			else
			{
				$return["result"] = false;
				$return["errors"][] = "Não existe um álbum com id ".$albumId.".";
				return $return;
			}
			
		}
		else
		{
			if($albumId == null)
			{
				$_p = array();
				$_p['post_title'] = $draft_label;
				$_p['post_name'] = $draft_label;			
				$_p['post_status'] = 'auto-draft';			
				$_p['post_type'] = 'albuns';
				$_p['comment_status'] = 'open';
				$_p['ping_status'] = 'closed';
				$_p['post_category'] = array(1); 
				$this->DeleteAutoDraftPosts();		
				$albumId = wp_insert_post($_p);				
				$albumVO = new AlbumVO($albumId);					
				$albumVO->AlbumUrl = str_replace($draft_label."/","", $albumVO->AlbumUrl);
				
			}	
		}
		if($albumVO->Titulo == 'rascunho')
		{
			$albumVO->Titulo = $dataArray["post_title"];
			$albumVO->Location = new LocationVO();
			$albumVO->Location->Local = $dataArray["local"];
			$albumVO->Location->Latitude = $dataArray["latitude"];
			$albumVO->Location->Longitude = $dataArray["longitude"];
			$albumVO->Resumo = $dataArray["descricao_album"];
		}				
		if($return["result"] == true)
		{	
			if($albumVO->Resumo == null || strlen($albumVO->Resumo) == 0)	
			{
				$albumVO->Resumo = "Digite a descrição do álbum aqui";
			} 
			?>
				<form id="content_form" name="content_form" method="POST">
					<h2 class="form_title"><? echo ($dataArray["action"] == "edit")? "Alterar álbum":"Novo álbum"; ?></h2>
					<a class="delete_item" onclick="return confirm('Tem certeza que deseja excluir o álbum com ID <? echo $albumVO->AlbumId; ?>?');" href="?post_type=albuns&action=delete&p=<? echo $albumVO->AlbumId ?>" class="ver">Excluir</a>				
					<input type="hidden" name="post_ID" id="post_ID" value="<?echo $albumVO->AlbumId; ?>">
					<input type="text"  class="post_title" name="post_title" size="30" value="<? echo $albumVO->Titulo; ?>" id="title" placeholder="Digite o título aqui" autocomplete="off"  />
					<span id="sample-permalink" tabindex="-1"><? echo $albumVO->AlbumUrl; ?></span>
					<textarea style="width:100%; height:150px" class="atualizacaoContent" maxlength="500" name="descricao_album" id="conteudo"><? echo $albumVO->Resumo; ?></textarea>
					<input class="local" type="text" style="width:100%;" name="local" id="local" value="<? echo $albumVO->Location->Local; ?>" class="regular-text" autocomplete="off">
					<input type="text" style="width:100%;display:none;" name="latitude" id="latitude" value="<? echo $albumVO->Location->Latitude; ?>"/>
					<input type="text" style="width:100%;display:none;" name="longitude" id="longitude" value="<? echo $albumVO->Location->Longitude; ?>"/>
					<div style="display:none;" id="locationmap"></div>
				
					<? 
						GetFamUploaderHtml(true,$albumId);
					
						if($albumId != null)
						{
							echo GetSocialPublishHtml(get_post_status($albumId),"albuns");
						}
						else
						{
							echo GetSocialPublishHtml("draft","albuns");
						}
						$save_action = ($dataArray["action"] == "edit")? "Atualizar":"Publicar";
					?>
					<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<? echo $save_action;?>" accesskey="p" >
					<div id="publishing-action"><span class="spinner" style="display: none;"></span></div>
				
					<input type="submit" style="display:none;" name="draft" id="draft" class="button button-primary button-large"  value="Salvar como rascunho" accesskey="p" >
				</form>
			<?
		}
		else
		{
			return $return;
		}
		
	}
	
	public function Delete($dataArray)
	{
		$albumId = $dataArray["p"];
		$return = array();
		$return["result"] = true;
		if($albumId == null || !is_numeric($albumId) || $albumId <= 0)
		{
			$return["result"] = false;
			$return["errors"][] = "Desculpe, mas há um erro na sua solicitação ou url. O álbum não foi excluído.";
			return $return;
		}
		if($albumId > 0 && !current_user_can('delete_atualizacao',$albumId))
		{
			$return["result"] = false;
			$return["errors"][] = "Usuário sem permissão para excluir esse álbum.";
			return $return;
		}
		
		$album = get_post($albumId);
		if($album->ID == null)
		{
			$return["result"] = false;
			$return["errors"][] = "O álbum já havia sido excluído anteriormente.";
			return $return;
		}	
		
		if(wp_delete_post($albumId) === false)
		{			
			$return["result"] = false;
			$return["errors"][] = "Desculpe, ocorreu um erro ao excluir o álbum e ele pode não ter sido excluído.";
			return $return;
		}
		else
		{
			$return["success"] = "Álbum excluído com sucesso.";
			return $return;
		}
		
	}
	
	public function DoAction($action, $dataArray)
	{
		$return;
		switch($action)
		{
			case "save":
				$return = $this->Save($dataArray);
				break;
			case "update":
				$return = $this->Save($dataArray);
				break;
			case "delete":
				$return = $this->Delete($dataArray);
				break;
			case "edit":
				$return = $this->GetMobileForm($dataArray);
				break;
			case "new":
				$return = $this->GetMobileForm($dataArray);
				break;
			case "draft":
				$return = $this->Save($dataArray);
				break;
		}
		return $return;
	}
	
	function DeleteAutoDraftPosts()
	{
		global $wpdb;
		$querystr = "DELETE FROM ". $wpdb->posts." where post_status = 'auto-draft' and post_name = 'rascunho' and post_type='albuns' and post_author ='".get_current_blog_id()."'";	
		$wpdb->query($querystr);	
		
	}
}
	
	

?>