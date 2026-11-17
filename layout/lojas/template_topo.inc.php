<?php

$body_style=''; $body_script=''; $imagem_fundo=''; $body_defs='';
$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
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

?>