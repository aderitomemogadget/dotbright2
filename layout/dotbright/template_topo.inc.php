<?php

$body_style=''; $body_script=''; $imagem_fundo=''; $body_defs='';
$discovery_entrypoints=array('categorias.php','novidades.php','destaques.php');
$is_discovery_entrypoint=in_array(basename($_SERVER['SCRIPT_NAME']??''),$discovery_entrypoints,true);
if($is_discovery_entrypoint)$body_defs.=' class="db-home-polish"';
$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
if($is_discovery_entrypoint)
{
	$home_template_rs=new DataObj;
	$home_template_rs->db_open($db_link,DB_NAME,'SELECT Template FROM pages WHERE DefaultHome=1 LIMIT 1');
	if(!$home_template_rs->EOF() && (int)$home_template_rs->db_data['Template'] > 0)$GLOBALS['template']=(int)$home_template_rs->db_data['Template'];
	$home_template_rs->db_close();
}
$rs=new DataObj;
// LER DADOS DO TEMPLATE
// SÓ VAMOS TER TEMPLATES DA LINGUA DEFAULT
if($GLOBALS['template'] > 0) $sql="SELECT templates.*,Texto1,Texto2 FROM templates inner join templates_lang on templates_lang.ID_PAI=templates.ID where Visivel=1 and templates.ID=".$GLOBALS['template']." and Lang='".$GLOBALS['site_lang']."'";
else $sql="select templates.*,Texto1,Texto2 from templates inner join templates_lang on templates_lang.ID_PAI=templates.ID where Visivel=1 and Lang='".$GLOBALS['site_lang']."' order By templates.ID ASC";
$rs->db_open($db_link,DB_NAME,$sql);
if(!$rs->EOF())
{
	$GLOBALS['template']=$rs->db_data['ID'];
	if(!$GLOBALS['site_template'])$GLOBALS['site_template']=$GLOBALS['template'];
	if($rs->db_data['img_ext'])
	{
		$imagem_fundo=$GLOBALS['site_http_url'].'files/templates/'.$rs->db_data['ID'].'_1.'.$rs->db_data['img_ext'];
		//Agora vamos obter a data de alteração do ficheiro
		$cache_date=filemtime('files/templates/'.$rs->db_data['ID'].'_1.'.$rs->db_data['img_ext']);
		$imagem_fundo=$imagem_fundo.'?d='.$cache_date;

	}
	$margem_top=$rs->db_data['margem_top'];
	$bg_color=$rs->db_data['bg_color'];
	$efeito=$rs->db_data['efeito'];
	$url=$rs->db_data['URL'];
	$site_width=$rs->db_data['site_width'];
	$estilo=$rs->db_data['estilo'];
	$estilo2=$rs->db_data['estilo2'];
	$topo=limpa_editor_lixo($rs->db_data['Texto1']);
	$rodape=limpa_editor_lixo($rs->db_data['Texto2']);
	if($url){if(!substr_count($url,'http'))$url='http://'.$url;}
}
$rs->db_close();
$imagem_fundo2='';//show_media_background();
if($imagem_fundo2) $imagem_fundo=$imagem_fundo2;

if($imagem_fundo)
{
	if($bg_color)$body_style .= 'background-color:'.$bg_color.';';
	if($imagem_fundo) $body_style .= 'background-image:url('.$imagem_fundo.');';
	if($efeito==1) $body_style .= ' background-repeat:repeat-x;';
	if($efeito==2) $body_style .= ' background-repeat:repeat-y;';
	if($efeito==3) $body_style .= ' background-repeat:no-repeat;background-position:center 180px;';
	if($efeito==4) $body_style .= ' background-repeat:no-repeat;background-position:top;';
	if($efeito==5) $body_style .= ' background-repeat:no-repeat;background-position:top right;';
}
else
{
	$body_defs .= ' ';
	if($bg_color) $body_style .= 'background-color:'.$bg_color.';';
}
echo '<body'; if($body_defs){ echo $body_defs;} if($body_style){ echo " style='$body_style' "; } echo  ' id="page1">';

if($margem_top) echo '<div style="width:100%;height:'.$margem_top.'px; z-index:1"></div>';
if($estilo==1) echo '<div style="width:100%;text-align:center">';
if($site_width)
{
	if($estilo2==1) $txt_align="center";else $txt_align="left";
	if(isset($PageEnter) && !$GLOBALS['online_edit']) echo "<div id='content' style='width:$site_width;text-align:".$txt_align.";float:none;margin-left:auto;margin-right:auto;z-index:2;display:none'>";
	else echo "<div style='width:$site_width;text-align:".$txt_align.";float:none;margin-left:auto;margin-right:auto;z-index:2'>";
}
else{ if($PageEnter && !$GLOBALS['online_edit']) echo "<div id='content' style='width:100%;text-align:".$txt_align.";float:none;margin-left:auto;margin-right:auto;z-index:2;display:none'>";}

if($topo && !isset($_GET['popup']))
{
	if($GLOBALS['online_edit'] && !$GLOBALS['prev']) edit_content($page_id,$topo); else put_content($topo);
}


function novo_topo($path_2='')
{

  global $site_http_url;
  $html ='
    <div id="page">
    <div id="toTop" style="display:block"></div>
    <div class="page-container">
    <nav class="menu responsive">';
    ob_start();
    $return2='';
    show_menu_topologado(1,1);
    $return2=ob_get_contents(); //Grab output
    $html .= $return2;
    ob_end_clean(); //Discard output buffer
      
      $html.='</nav>
      <header id="header">
        <div class="topo">
            <div class="container">
                <div class="row">
                    <div class="col-md-1">
                        <div class="clear"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="dados_portes">
                            <span>PORTES GRÁTIS <strong>EM ENCOMENDAS SUPERIORES A </strong>15€</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="dados_contacto">
                            <span style="color:#fff;padding: 5px 10px; font-size: 13px; font-weight: bold; font-family: Roboto;">Ligue já » 229 429 211 abb</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="menu_fav">
                            <div class="row">
                                <a style="color: #fff;padding: 5px 10px;background-color: #ec870e; font-size: 13px; font-weight: bold; font-family: Roboto;" href="'.$site_http_url.'p141-traking-pt">Consulte o estado da sua reparação</a>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="menu_enc">
                            <div class="row">
                                <img src="'.$path_2.'imgs/minhas_encomendas.svg" alt="Encomendas">
                                <a style="color:#fff;padding: 5px 0px; font-size: 13px; font-weight: bold; font-family: Roboto;" href="'.$site_http_url.'p5-registo-utilizador-pt?show_compras=1"><span>As Minhas Encomendas</span></a>
                            </div>                            
                        </div>
                    </div>
                </div>               
            </div>
        </div>
        <div class="middle">
          <div class="container">
            <div class="row ">
              <div class="col-xs-12 col-sm-3 col-md-3">
                <div id="header_logo" class="header_logo">
                  <a href="'.$site_http_url.'" title=""><img class="logo img-responsive" src="'.$path_2.'imgs/logo_top_white.png" alt="" /></a>
                </div>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-6">
                <div id="header_access" style="position:relative">';
    ob_start();
    $return2='';
    show_search2();
    $return2=ob_get_contents(); //Grab output
    $html .= $return2;
    ob_end_clean(); //Discard output buffer
    $html.='</div>
              </div>
              <div class="col-xs-12 col-sm-3 col-md-3">             
                <div class="col-right" style="margin-top: 55px">';
    // ob_start();
    $return2='';
    
    // $html.=carrinho_small3();
    ob_start();
    $return2='';
    carrinho_small4();
    $return2=ob_get_contents(); //Grab output
    $html .= $return2;
    ob_end_clean(); //Discard output buffer
    //login / registo
    $html.=show_menu_login4();
    // $return2=ob_get_contents(); //Grab output
    // $html .= $return2;
    // ob_end_clean(); //Discard output buffer
    $html.='</div>
              </div>
              

                      </div>
          </div>

      <div class="footer">
        <div class="container container-menu">
        <div class="row">
            <div class="col-xs-6">';  
  
    ob_start();
    $return2='';
    show_menu_lateral_novo(1);
    $return2=ob_get_contents(); //Grab output
    $html .= $return2;
    ob_end_clean(); //Discard output buffer
    $html.='</div>
            <div class="col-xs-6 text-center"><span class="top-message">';
    
    ob_start();
    $return2='';
    show_messages_media(6);
    $return2=ob_get_contents(); //Grab output
    $html .= $return2;
    ob_end_clean(); //Discard output buffer
    $html.='</span></div>
      </div>
    </div>
  </div>
  </header>
  <div class="clear"></div>


  <div class="left-col hidden-xs hidden-sm">
    <div class="filtros_wrapper">';

    ob_start();
    $return2='';
    show_menus_families(225);
    $return2=ob_get_contents(); //Grab output
    $html .= $return2;
    ob_end_clean(); //Discard output buffer


    $html.='</div>
    </div>
    <div class="right-col">
    <div class="catprod_wrap">


  ';

  echo $html;
}
?>
