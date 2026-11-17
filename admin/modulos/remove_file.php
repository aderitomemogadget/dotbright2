<?php
include 'settings.inc.php';


	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);

if(isset($_GET['file_del']) && $_GET['file_del']!='')
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$sql = 'DELETE FROM modulos_imgs WHERE bloco_img_ext ="'.str_replace('../../files/documentos/','',str_replace('?embedded=true','',$_GET['file_del'])).'"';

	$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	if($res) unlink(str_replace('?embedded=true','',$_GET['file_del']));
}
?>