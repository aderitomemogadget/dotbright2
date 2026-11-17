<?php
header("Content-Type: text/html; charset=utf-8", true);
$path = '../core/';
include $path.'core_files.php';
echo '<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">';
echo '<link rel="stylesheet" type="text/css" media="all" href="'.SITE_HTTP_URL.'layout/'.PROJETO.'/css/css_external/global_v_'.dumpcache.'.css">';
echo '<link href="'.SITE_HTTP_URL.'layout/'.PROJETO.'/css/template_v_'.dumpcache.'.css" rel="stylesheet" type="text/css">';
show_content($GLOBALS['termos_page_id']);