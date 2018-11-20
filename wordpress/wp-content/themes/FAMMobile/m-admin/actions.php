<?php
	
function ScreenAction($getArray, $postArray)
{
	if($getArray["action"] != null && $getArray["post_type"] != null && in_array($getArray["post_type"], array('atualizacao','albuns')) )
	{
		$action = $getArray["action"];
		$post_type = $getArray["post_type"];
		$dataArray = $postArray;
		$dataArray["p"] = $getArray["p"];
		if($dataArray["p"] == null)
		{
			$dataArray["p"] = $dataArray["post_ID"];
		}
		$dataArray["action"] = $getArray["action"];
			
		if($post_type == "atualizacao")
		{
			require_once(ABSPATH."/FAMCore/BO/Atualizacao.php");
			$BO = new Atualizacao();
		}
		if($post_type == "albuns")
		{
			require_once(ABSPATH."/FAMCore/BO/Galeria.php");
			$BO = new Galeria();
		}
			
			
		if(in_array($action, array('delete','save','draft','update')))
		{		
			$result = $BO->DoAction($action, $dataArray);		
			if($result["result"] === true)
			{
				echo "<p class='success'>".$result["success"]."</p>";	
				ListData($post_type);
			}
			else
			{
				if(is_array($result["errors"]) && count($result["errors"]) > 0)
				{
					foreach($result["errors"] as $error)
					{
						echo "<p class='error'>".$error."</p>";
					}
					if(in_array($action, array('delete')))
					{
						ListData($post_type);
					}			
				}
				else
				{
					echo "<p>Desculpe, ocorreu um erro ao processar sua solicitação e ela pode não ter sido concluída.</p>";
				}
				if(in_array($_GET["action"], array('update')))
				{
					$BO->DoAction('edit', $dataArray);	
				}
				else if(in_array($action, array('save','draft')))
				{
					$BO->DoAction('new', $dataArray);	
				}
			}	
		}
		else if(in_array($action, array('edit','new')))
		{
			$result = $BO->DoAction($action, $dataArray);	
			if(is_array($result["errors"]) && count($result["errors"]) > 0)
			{
				foreach($result["errors"] as $error)
				{
					echo "<p class='error'>".$error."</p>";
				}
				
				ListData($post_type);
							
			}	
		}
	}
	else
	{
		
		ListData($getArray["post_type"]);
	}
}


function ListData($type)
{
	switch($type)
	{
		case "atualizacao":
			widget::Get("atualizacoes", array('itens'=> 3,'show_admin_controls'=>'yes','show_more'=>'yes','show_read_full'=>'no','content_lenght'=> 300, 'show_location'=> true, 'float'=>'right','margin_right'=>'0px', 'width'=>'100%','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px','authorId'=>get_current_user_id()));
			break;
		case "albuns":
			widget::Get("galeria", array('show_more' => 'yes','itens'=> 4, 'show_admin_controls'=>'yes','authorId'=>get_current_user_id()));
			break;
		default:
			widget::Get("atualizacoes", array('itens'=> 3,'show_admin_controls'=>'yes','show_more'=>'yes','show_read_full'=>'no','content_lenght'=> 300, 'show_location'=> true, 'float'=>'right','margin_right'=>'0px', 'width'=>'100%','show_comment'=> 'no', 'show_media'=> "no", 'foto_width'=>'70px','authorId'=>get_current_user_id()));
	}
}
