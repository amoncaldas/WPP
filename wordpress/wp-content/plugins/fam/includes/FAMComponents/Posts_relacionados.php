<?php 
require_once(ABSPATH. "/FAMCore/BO/Conteudo.php");
$relacionados = Conteudo::GetPostsRelacionados($options);

?>
<section class="related_posts" style="float:<?echo $options["float"].' !important';?>;margin-left:<?echo $options["margin_left"].' !important'; ?>; margin-right:<?echo $options["margin_right"].' !important'; ?>;width:<?echo $options["width"];?>!important;" >
	<hr/>
	<h2><? if($options["title"] != null) echo $options["title"]; else echo "Recomendados para você";?></h2>
	<ul>	
	
		<?
		foreach($relacionados as $relacionado)
		{				
			echo "<li>
					<a href='".$relacionado->PostUrl."'>
						<div class='post_image'>
							<img src='".$relacionado->MidiaPrincipal->ImageGaleryThumbSrc."'/>
						</div>
						<div class='post_title'><span>".$relacionado->PostTypeLabel."</span><h3>".$relacionado->Titulo."</h3></div>
					</a>
				</li>";		
		}
		?>
	</ul>
</section>
	

