<?php
// Historical RMA templates use this spelling for the asset cache version.
if (!defined('dumpacache')) { define('dumpacache', defined('dumpcache') ? constant('dumpcache') : '1'); }

if (!function_exists('rma_asset_version')) {
	function rma_asset_version($relative_path)
	{
		$path = dirname(__DIR__, 2).'/'.ltrim($relative_path, '/');
		$version = @filemtime($path);
		return $version ? $version : dumpacache;
	}
}

$is_rma_home = substr_count($_SERVER['SCRIPT_FILENAME'], 'index.php') > 0;
$rma_viewport = !empty($rma_full_sheet_mobile)
	? 'width=1280, user-scalable=yes'
	: 'width=device-width, initial-scale=1';
$rma_css = array(
	'rmas/admin_estilos.css',
	'external_api/jquery-ui-1.10.3.css',
	'external_api/datepicker/jquery-ui-timepicker-addon.css',
	'external_api/reveal/jquery.fancybox.css',
	'rmas/sa.css',
	'rmas/layout/choosen/chosen.css',
	'rmas/layout/css/font-awesome.min.css',
	'rmas/layout/css/rma-modern.css',
	'rmas/layout/css/rma-list.css',
	'rmas/layout/css/rma-workflow.css'
);

echo ('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt">
<head>
<title>ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="'.$rma_viewport.'">
<meta name="copyright" content="memogadget.com">
<meta name="owner" content="memogadget.com 1 ilimited license to dotbright.pt">

'.($is_rma_home ? '<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'rmas/layout/css/bootstrap.min.css?v='.rma_asset_version('rmas/layout/css/bootstrap.min.css').'">' : '').'
'.($is_rma_home ? '<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'rmas/layout/css/style.default.css?v='.rma_asset_version('rmas/layout/css/style.default.css').'" id="theme-stylesheet">' : '').'
'.($is_rma_home ? '<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'rmas/layout/css/custom.css?v='.rma_asset_version('rmas/layout/css/custom.css').'">' : '').'
'.($is_rma_home ? '<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'rmas/myfontastic/icons.css?v='.rma_asset_version('rmas/myfontastic/icons.css').'">' : '').'
');

foreach ($rma_css as $stylesheet) {
	echo '<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].$stylesheet.'?v='.rma_asset_version($stylesheet).'">';
}

echo ('

<style type="text/css">
	/* Minimal first-render geometry; the complete visual system lives in rma-modern.css. */
	:root{--rma-canvas:#f4f6f9;--rma-ink:#18202f;--rma-navy:#151b29}
	html{min-height:100%;overflow-y:scroll;background:var(--rma-canvas)}
	body{height:auto;min-height:100%;margin:0;background:var(--rma-canvas);color:var(--rma-ink);font-family:"Segoe UI",Arial,sans-serif;font-size:13px;line-height:1.45;text-align:left}
	.topo{width:100%;min-height:76px;background:var(--rma-navy)}
	TABLE.t3{width:min(1400px,calc(100vw - 40px));margin-right:auto;margin-left:auto}
	#navigation_horiz{min-height:58px}
	.menutitle{cursor:pointer;margin:0px;width:100%;padding:0px}
	.subm{display:none;margin:0px;padding:0px;position:relative}
</style>

<script type="text/javascript">
function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}

function makeSelection(selObj,campo)
{
	selObj2=document.getElementById(selObj);
	if(selObj2.checked==true)for(i=0; i<eval("document."+campo+".elements.length");i++){if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")=="chk_") eval("document."+campo+".elements[i].checked=true");}
	if(selObj2.checked==false)for(i=0; i<eval("document."+campo+".elements.length");i++){if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")=="chk_") eval("document."+campo+".elements[i].checked=false");}
}

function checkFields(){if(document.lista_paginas.flag.value==1){var r=confirm("Confirma que pretende remover todas as paginas seleccionadas?");if(r==true) return true;else return false;}return true;}


	function load(url){
		try{var ajax=new ActiveXObject("Microsoft.XMLHTTP");}catch(e){try{var ajax=new ActiveXObject("Msxml2.XMLHTTP");}catch(ex){try{var ajax=new XMLHttpRequest();}catch(exc){alert("Browser not accept Ajax");ajax=null;}}}
	if(ajax){
		ajax.open("GET",url,true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		ajax.onreadystatechange=function()
		{
			if(ajax.readyState==4)
			{
				var texto=ajax.responseText;texto=texto.replace(/\+/g," ");
				texto=unescape(texto);
				var brokenstring=texto.split(";");
				setTimeout("load(\''.$GLOBALS['site_http_url'].'rmas/rmas/keepalive.php\');",1000*15);
			}
		}
		ajax.send(null);
		}
	}
	setTimeout("load(\''.$GLOBALS['site_http_url'].'rmas/rmas/keepalive.php\');",1000*15);


</script>
<script src="'.$GLOBALS['site_http_url'].'core/admin_global.js?v='.rma_asset_version('core/admin_global.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'rmas/layout/js/jquery-1.12.3.min.js?v='.rma_asset_version('rmas/layout/js/jquery-1.12.3.min.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'rmas/layout/js/jquery-migrate-1.3.0.min.js?v='.rma_asset_version('rmas/layout/js/jquery-migrate-1.3.0.min.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'external_api/jquery-ui-1.10.3.js?v='.rma_asset_version('external_api/jquery-ui-1.10.3.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'external_api/datepicker/jquery-ui-timepicker-addon.js?v='.rma_asset_version('external_api/datepicker/jquery-ui-timepicker-addon.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'external_api/datepicker/jquery-ui-sliderAccess.js?v='.rma_asset_version('external_api/datepicker/jquery-ui-sliderAccess.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'external_api/datepicker/localization/jquery-ui-timepicker-en.js?v='.rma_asset_version('external_api/datepicker/localization/jquery-ui-timepicker-en.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'external_api/jquery.easing.1.3.js?v='.rma_asset_version('external_api/jquery.easing.1.3.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'external_api/reveal/jquery.fancybox.js?v='.rma_asset_version('external_api/reveal/jquery.fancybox.js').'"></script>
<script src="'.$GLOBALS['site_http_url'].'rmas/sa.js?v='.rma_asset_version('rmas/sa.js').'"></script>
<script defer blocking="render" src="'.$GLOBALS['site_http_url'].'rmas/layout/js/rma-form-ui.js?v='.rma_asset_version('rmas/layout/js/rma-form-ui.js').'"></script>
<script defer blocking="render" src="'.$GLOBALS['site_http_url'].'rmas/layout/js/rma-list-ui.js?v='.rma_asset_version('rmas/layout/js/rma-list-ui.js').'"></script>
<script defer blocking="render" src="'.$GLOBALS['site_http_url'].'rmas/layout/js/rma-workflow-ui.js?v='.rma_asset_version('rmas/layout/js/rma-workflow-ui.js').'"></script>
</head>');

if(!defined('PROJETO')) define('PROJETO', 'dotbright');

//
