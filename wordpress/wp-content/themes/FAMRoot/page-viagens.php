<?php 
/*require_once(ABSPATH."/FAMCore/BO/Viajante.php");
$viajante = new Viajante();
$viajantes = $viajante->GetViajantes(10);*/

require_once(ABSPATH."/FAMCore/BO/Conteudo.php");
Conteudo::SetMetas("page-viagens");
$options = array('list_type'=>'box','viagem_medium_image'=>'yes','itens'=>10,'show_more'=>'yes','width'=>'590px','viagem_medium_image'=>'yes','viagem_show_map'=>'yes');
if($_GET["viagens_modal"] == "yes"){    
    $options = array('list_type'=>'default','viagem_medium_image'=>'yes','itens'=>2,'show_more'=>'no','viagem_show_map'=>'no');   
}

include('header.php') ?>
        <div id="content-container">		
            <div id="content">		
                <div id="bloco-conteudo-central">
                    
                    <div class="lista_viagens">	
                        <? widget::Get("mapa_viagens"); ?>											
                        <? widget::Get("viagens", $options);?>						
                    </div>
                    <aside id="coluna-lateral-direita" class="coluna_lateral_viagens">	
                        <? widget::Get("add_box_top_right", array('width'=>'300','float'=>'right','margin_top'=>"0px",'label'=>'page_viagens','margin_right'=>'-5px'));  ?>					
                        <?php widget::Get("blog_posts", array('itens'=> 3,'show_share'=>'no','content_lenght'=> 100,'show_more'=> 'yes','width'=>'100%','float'=>'right')); ?>	
                        <? widget::Get("aside_middle_box", array('margin_left'=>'0px'));?>
                        <?php widget::Get("ultimos-relatos", array('itens'=> 3,'title'=>'Relatos de viagem','content_lenght'=> 200,'width'=>'100%','show_more'=> 'yes')); ?>					
                    </aside><!-- end blocodir -->
                    <?php widget::Get("share", array("comment_box_width" => '900')); ?>
                    <? widget::Get('footer_adds', array('label'=>'page-viagens')); ?>	
                    <div class="clear"></div>					
                </div><!-- end page -->
            </div><!-- end content -->
            <div id="contentBottom">
                <div id="bottom-boxes-container">
                    <? widget::Get("twitter-box"); ?>							
                    <? widget::Get("facebook-box"); ?>
                </div>
                <div class="clear"></div>
                <? widget::Get("socialmedia");?>
                <?php widget::Get("codigocriativo")?>
            </div><!-- end content-bottom -->
        </div><!-- end content -->
    </div><!-- end geral -->
<? include('footer-wp.php');?>