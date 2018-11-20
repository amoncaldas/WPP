<?php

/**
 * class Atualizacao
 *
 * Description for class Atualizacao
 *
 * @author:
*/
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreBO/Conteudo.php' );
require_once(FAM_PLUGIN_PATH . '/includes/FAMCoreVO/AtualizacaoVO.php' );


class Atualizacao extends Conteudo {	
	
	/**
	 * @var AtualizacaoVO $DadosAtualizacao
	*/
	public $DadosAtualizacao;
	public $HasMoreData;
	
	/**
	 * Atualizacao constructor. Can be instantiated passing the Atualizacao id or not and viagemId or not
	 *
	 * @param $id must be the (post) unique id and $viagemId musb be the blog id
	 */	
	function Atualizacao($id = null, $viagemId = null) {
		parent::Conteudo("Atualizacao", $viagemId);
		if($id != null)
		{			
			$this->DadosAtualizacao = new AtualizacaoVO($id);									
		}				
	}
	
		
	/**
	 * @return AtualizacaoVO[]
	*/
	public  function GetAtualizacoes($options)
	{
		$atualizacoes = $this->GetItens($options);		
		
		if ($atualizacoes)
		{		
			if($this->MultiSiteData == true)
			{			
				return $atualizacoes;
			}
			else
			{	
				$atualizacoesVO = array();				
				foreach ($atualizacoes as $atualizacao)
				{		
					$atualizacaoVO = new AtualizacaoVO($atualizacao->ID);	
					$atualizacoesVO[] = $atualizacaoVO;					
				}			
			}
			return $atualizacoesVO;	
		}			
	}
	
		
			
	/**
	* @return AtualizacaoVO[]
	*/
	public  function GetData($options) {		
		
		if($options["itens"] == null || $options["itens"] == 0)
		{
			$options["itens"] = 2;
		}	
		$itensOriginal = $options["itens"];
		$options["itens"]++;
		$this->HasMoreData = false;	
		$atualizacoes = $this->GetAtualizacoes($options);
		
		if(is_array($atualizacoes) && count($atualizacoes)> $itensOriginal)
		{				
			$this->HasMoreData = true;
			$atualizacoes = array_slice($atualizacoes, 0, count($atualizacoes) -1);				
		}		
		return $atualizacoes;				
	}	
	
	public function Save($dataArray)
	{
		$return = array();
		$return["result"] = true;
		if(!current_user_can('edit_atualizacao'))
		{
			$return["errors"][] = "Usuário sem permissão para salvar status.";
		}
		if($dataArray["p"] != null && !current_user_can('edit_atualizacao',$dataArray["p"]))
		{
			unset($return["errors"]["Usuário sem permissão para salvar status"]);
			$return["errors"][] = "Usuário sem permissão para salvar este status.";
		}
		if($dataArray["post_title"] == null || strlen($dataArray["post_title"]) == 0)
		{
			$return["errors"][] = "O título deve ser informado.";
		}
		if($dataArray["local"] == null || strlen($dataArray["local"]) == 0)
		{
			$return["errors"][] = "O local deve ser informado.";
		}
		if(strpos($dataArray["conteudo"],"Digite o texto do status aqui") > -1)
		{
			$dataArray["conteudo"] = str_replace("Digite o texto do status aqui","",$dataArray["conteudo"]);
		}
		if($dataArray["conteudo"] == null || strlen($dataArray["conteudo"]) == 0)
		{
			$return["errors"][] = "O conteudo deve ser informado.";
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
			$_p['post_type'] = 'atualizacao';
			$_p['comment_status'] = 'open';
			$_p['ping_status'] = 'open';
			$_p['post_category'] = array(1); // the default 'Uncatrgorised'	
		
			if($dataArray["p"] != null && $dataArray["p"] > 0)
			{
				$_p["ID"] = $dataArray["p"];
				$atualizacaoId = $dataArray["p"];				
				wp_update_post($_p);
				if($dataArray["action"] == "update")
				{
					$return["success"] = "Status atualizado com sucesso.";
				}				
				elseif($dataArray["action"] == "draft")
				{
					$return["success"] = "O status salvo como rascunho e atualizado com sucesso. Lembre-se que ao salvá-lo como rascunho o mesmo deixa de estar visível para o público.";
				}
				elseif($dataArray["action"] == "save")
				{
					$return["success"] = "Status publicado com sucesso.";
				}	
				SaveImages($atualizacaoId, $dataArray);
				update_post_meta($atualizacaoId, 'local', $dataArray["local"]);
				update_post_meta($atualizacaoId, 'latitude', $dataArray["latitude"]);
				update_post_meta($atualizacaoId, 'longitude', $dataArray["longitude"]);
				update_post_meta($atualizacaoId, 'conteudo',$dataArray["conteudo"]);
				$return["result"] = true;
				$return["data"] = new AtualizacaoVO($atualizacaoId);
				return $return;				
			}
			else
			{
				$return["result"] = false;
				$return["errors"][] = "Deculpe, mas ocorreu um erro ao salvar o status";				
				return $return;	
			}			
			
			
		}			
	}
	
	public function GetMobileForm($dataArray)
	{
		$return = array();
		$return["result"] = true;
		$atualizacaoId = $dataArray["p"];
		$draft_label = "rascunho";
		$return = array();
		$return["result"] = true;
		
		if($atualizacaoId != null)
		{
			$post = get_post($atualizacaoId);
			
			if($post->ID > 0)
			{				
				if(!current_user_can('edit_atualizacao', $atualizacaoId))	
				{
					$return["errors"][] = "Usuário sem permissão para editar este status";
					return $return;
				}
				else
				{		
					$atualizacaoVO = new AtualizacaoVO($atualizacaoId);
					$atualizacaoVO->AtualizacaoUrl = str_replace($draft_label."/","", $atualizacaoVO->AtualizacaoUrl);				
				}
			}
			else
			{			
				$return["result"] = false;
				$return["errors"][] = "Não existe um status com id ".$atualizacaoId.".";					
				return $return;
				
			}
			
		}
		else
		{
			if($atualizacaoId == null)
			{
				$_p = array();
				$_p['post_title'] = $draft_label;
				$_p['post_name'] = $draft_label;			
				$_p['post_status'] = 'auto-draft';			
				$_p['post_type'] = 'atualizacao';
				$_p['comment_status'] = 'open';
				$_p['ping_status'] = 'closed';
				$_p['post_category'] = array(1); 	
				$this->DeleteAutoDraftPosts();
				$atualizacaoId = wp_insert_post($_p);				
				$atualizacaoVO = new AtualizacaoVO($atualizacaoId);	
				$atualizacaoVO->AtualizacaoUrl = str_replace($draft_label."/","", $atualizacaoVO->AtualizacaoUrl);
				
			}	
		}
		if($atualizacaoVO->Titulo == 'rascunho')
		{
			$atualizacaoVO->Titulo = $dataArray["post_title"];
			$atualizacaoVO->Location = new LocationVO();
			$atualizacaoVO->Location->Local = $dataArray["local"];
			$atualizacaoVO->Location->Latitude = $dataArray["latitude"];
			$atualizacaoVO->Location->Longitude = $dataArray["longitude"];
			$atualizacaoVO->Conteudo = $dataArray["conteudo"];
		}				
		if($return["result"] == true)
		{	
			if($atualizacaoVO->Conteudo == null || strlen($atualizacaoVO->Conteudo) == 0)	
			{
				$atualizacaoVO->Conteudo = "Digite o texto do status aqui";
			} 	 
			?>
				<form id="content_form" name="content_form" method="POST">
					<h2 class="form_title"><? echo ($dataArray["action"] == "edit")? "Alterar status":"Novo status"; ?></h2>
					<a class="delete_item" onclick="return confirm('Tem certeza que deseja esxcluir o status com ID <? echo $atualizacaoVO->AtualizacaoId; ?>?');" href="?post_type=atualizacao&action=delete&p=<? echo $atualizacaoVO->AtualizacaoId ?>" class="ver">Excluir</a>				
					<input type="hidden" name="post_ID" id="post_ID" value="<?echo $atualizacaoVO->AtualizacaoId; ?>">
					<input type="text"  class="post_title" name="post_title" size="30" value="<? echo $atualizacaoVO->Titulo; ?>" id="title" placeholder="Digite o título aqui" autocomplete="off"  />
					<span id="sample-permalink" tabindex="-1"><? echo $atualizacaoVO->AtualizacaoUrl; ?></span>
					<textarea style="width:100%; height:150px" class="atualizacaoContent" maxlength="1000" name="conteudo" id="conteudo"><? echo $atualizacaoVO->Conteudo; ?></textarea>
					<input class="local" type="text" style="width:100%;" name="local" id="local" value="<? echo $atualizacaoVO->Location->Local; ?>" class="regular-text" autocomplete="off">
					<input type="text" style="width:100%;display:none;" name="latitude" id="latitude" value="<? echo $atualizacaoVO->Location->Latitude; ?>"/>
					<input type="text" style="width:100%;display:none;" name="longitude" id="longitude" value="<? echo $atualizacaoVO->Location->Longitude; ?>"/>
					<div style="display:none;" id="locationmap"></div>
					<? 
						GetFamUploaderHtml(true,$atualizacaoId);
					
						if($atualizacaoId != null)
						{
							echo GetSocialPublishHtml(get_post_status($atualizacaoId),"atualizacao");
						}
						else
						{
							echo GetSocialPublishHtml("draft","atualizacao");
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
		$atualizacaoId = $dataArray["p"];
		$return = array();
		$return["result"] = true;
		if($atualizacaoId == null || !is_numeric($atualizacaoId) || $atualizacaoId <= 0)
		{
			$return["result"] = false;
			$return["errors"][] = "Desculpe, mas há um erro na sua solicitação ou url. O status não foi excluído.";
			return $return;
		}
		if($atualizacaoId > 0 && !current_user_can('delete_atualizacao',$atualizacaoId))
		{
			$return["result"] = false;
			$return["errors"][] = "Usuário sem permissão para excluir esse status.";
			return $return;
		}
		
		$atualizacao = get_post($atualizacaoId);
		if($atualizacao->ID == null)
		{
			$return["result"] = false;
			$return["errors"][] = "O status já havia sido excluído anteriormente.";
			return $return;
		}	
		
		if(wp_delete_post($atualizacaoId) === false)
		{			
			$return["result"] = false;
			$return["errors"][] = "Desculpe, ocorreu um erro ao excluir o status e ele pode não ter sido excluído.";
			return $return;
		}
		else
		{
			$return["success"] = "Status excluído com sucesso.";
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
		$querystr = "DELETE FROM ". $wpdb->posts." where post_status = 'auto-draft' and post_name = 'rascunho' and post_type='atualizacao' and post_author ='".get_current_blog_id()."'";	
		$wpdb->query($querystr);	
		
	}
}

?>