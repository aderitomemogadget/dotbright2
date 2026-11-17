<?php 
$path = dirname( __FILE__ ).'/../../../core/';
include $path . 'core_files.php';
include dirname( __FILE__ ).'/funcoes.inc.php';

error_reporting(E_ALL);
ini_set('display_errors',0);
header('Content-Type: text/html; charset=utf-8');
ini_set("max_execution_time","60000");
ini_set("max_input_time","60000");
ini_set("memory_limit","-1");

gera_produtos_filtros('pt');
gera_produtos_filtros('en');
?>