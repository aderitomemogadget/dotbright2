<?php
// Historical admin modules use this spelling for the asset cache version.
if (!defined('dumpacache')) {
	define('dumpacache', defined('dumpcache') ? constant('dumpcache') : '1');
}
if (!defined('filter')) {
	define('filter', 'filter');
}
// die('deixou de ter permissoes');
echo comprime('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="copyright" content="memogadget.com">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="owner" content="memogadget.com 1 ilimited license to '.$site_title.'">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/bootstrap-3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/admin_estilos.css?d=11" />
<style type="text/css">
.menutitle{cursor:pointer;margin:0px;width:100%;padding:0px}
.subm{margin:0px;padding:0px; position:relative}
.subm{display:none;}
</style>
<script type="text/javascript">

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




<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/reveal/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/fontawesome-free-5.7.2-web/css/all.css" media="screen" />


<script defer src="'.$GLOBALS['site_http_url'].'admin/layout/fontawesome-free-5.7.2-web/js/all.js"></script>


<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->

<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/bootstrap/bootstrap-4.1.1/css/bootstrap-grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/bootstrap-calendar/css/bootstrap_calendar.css" media="screen" />
<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.css" media="screen" />

<!-- ================== BEGIN BASE JS ================== -->

<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/jquery.cookie.js"></script>

<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/bootstrap-3.3.7/js/bootstrap.js"></script>

<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'admin/layout/jquery-ui-1.10.3.css" />

<link rel="stylesheet" type="text/css" href="'.$GLOBALS['site_http_url'].'admin/layout/datepicker/jquery-ui-timepicker-addon.css" media="screen" />
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/datepicker/jquery-1.8.2.js"></script>

<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/jquery-ui-1.10.3.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/datepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/datepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/datepicker/localization/jquery-ui-timepicker-en.js"></script>
<script type="text/javascript" src="'.$GLOBALS['site_http_url'].'admin/layout/jquery.easing.1.3.js"></script>

<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/pace/pace.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/sparkline/jquery.sparkline.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.time.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.resize.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.pie.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.stack.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.crosshair.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/jquery.flot.categories.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/flot/CurvedLines/curvedLines.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-world-merc-en.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/chart-js/Chart.js"></script>
<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/plugins/gritter/js/jquery.gritter.js"></script>

<!--script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/js/page-widgets.demo.min.js"></script-->
<!--script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/js/page-index.demo.js"></script-->
<!--script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/js/page-index-v2.demo.min.js"></script-->
<!--script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/js/demo.min.js"></script-->



<script language="JavaScript" src="'.$GLOBALS['site_http_url'].'admin/layout/theme/js/apps.js"></script>

<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/css/animate.min.css" />
<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/css/style.min.css?d=11" />

<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/css/style.css?d=11" />
<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'admin/layout/theme/css/style-responsive.min.css" />
<link rel="stylesheet" href="'.$GLOBALS['site_http_url'].'admin/layout/admin-ui-polish.css?v='.filemtime(__DIR__.'/admin-ui-polish.css').'" />
<script defer src="'.$GLOBALS['site_http_url'].'admin/layout/dotbright-admin.js?v='.filemtime(__DIR__.'/dotbright-admin.js').'"></script>

<script>jQuery(function(){$(\'table\').each(function() {
var cp = parseInt($( this ).attr(\'cellpadding\'));
if(cp > 0){
$(this).children(\'tbody\').children(\'tr\').children(\'td\').css(\'padding\',cp+\'px\');
}
if($( this ).attr(\'bgcolor\') !== \'\'){
$(this).css(\'background-color\',$( this ).attr(\'bgcolor\'));
}
});});

$(document).ready(function()
{
	App.init();
});


</script>
');
