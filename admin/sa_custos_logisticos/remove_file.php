<?php 
include 'settings.inc.php';

if(isset($_GET['file_del']) && $_GET['file_del']!='')
{ 
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$sql = 'UPDATE '.TBMODULO.' SET bloco_ext="" WHERE bloco_ext="'.str_replace('../../files/'.FOLDER.'/','',str_replace('?embedded=true','',$_GET['file_del'])).'"';
	$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	if($res) unlink(str_replace('?embedded=true','',$_GET['file_del']));
}