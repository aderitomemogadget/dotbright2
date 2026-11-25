<?php
echo comprime('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="copyright" content="memogadget.com">
<meta name="owner" content="memogadget.com 1 ilimited license to cosmeticdelivery.com">
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'rmas/admin_estilos.css" />
<style type="text/css">
.menutitle{cursor:pointer;margin:0px;width:100%;padding:0px}
.subm{margin:0px;padding:0px; position:relative}
</style>
<script type="text/javascript">
if(document.getElementById){document.write(\'<style type="text/css">\');document.write(\'.subm{display:none;}\');document.write(\'</style>\')}
function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
function makeSelection(selObj,campo)
{
selObj2=document.getElementById(selObj);
if(selObj2.checked==true)for(i=0; i<eval("document."+campo+".elements.length");i++){if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")=="chk_") eval("document."+campo+".elements[i].checked=true");}
if(selObj2.checked==false)for(i=0; i<eval("document."+campo+".elements.length");i++){if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")=="chk_") eval("document."+campo+".elements[i].checked=false");}
}
<!--
function checkFields(){if(document.lista_paginas.flag.value==1){var r=confirm("Confirma que pretende remover todas as paginas seleccionadas?");if(r==true) return true;else return false;}return true;}
//-->
</script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'core/admin_global.js"></script>
<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'external_api/jquery-ui-1.10.3.css" />
<link rel="stylesheet" media="all" type="text/css" href="'.$GLOBALS['site_http_url'].'external_api/datepicker/jquery-ui-timepicker-addon.css" />	

<script src='.$GLOBALS['site_http_url'].'rmas/layout/js/jquery-1.12.3.min.js?dp='.dumpcache.'"></script>
<script src="'.$GLOBALS['site_http_url'].'rmas/layout/js/jquery-migrate-1.3.0.min.js?dp='.dumpcache.'"></script>



<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'external_api/jquery-ui-1.10.3.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'external_api/datepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'external_api/datepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'external_api/datepicker/localization/jquery-ui-timepicker-en.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'external_api/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'external_api/reveal/jquery.fancybox.js"></script>
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'external_api/reveal/jquery.fancybox.css" media="screen" />
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'rmas/sa.js"></script>
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'rmas/sa.css" media="screen" /><link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'rmas/layout/choosen/chosen.css?dp='.dumpacache.'>" />');

if(!defined('PROJETO')) define('PROJETO', 'dotbright');