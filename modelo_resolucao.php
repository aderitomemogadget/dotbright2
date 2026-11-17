<?php

	$_GET ='';
	$_POST ='';
	$_REQUEST ='';

	unset($_GET);
	unset($_POST);
	unset($_REQUEST);

	header('Content-Type: text/html; charset=utf-8');
	$path = 'core/';
	include $path.'core_files.php';

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>FORMULÁRIO DE LIVRE RESOLUÇÃO</title>
<style>
html, body{padding:0;margin:0}
.popup{width:700px;margin:0 auto;font-size:14px;font-family:arial, sans-serif;border:#c0c0c0 1px solid;padding:50px}
</style>
</head>
<body>
<div class="popup"><?php show_content(49);?></div>
<script type="text/javascript">window.onload = function(){window.print();};</script>
</body></html>