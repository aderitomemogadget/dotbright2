<?php 
include 'settings.inc.php';

if(isset($_GET['file_del']) && $_GET['file_del']!='')
{ 
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$sql = 'DELETE FROM blocos_imgs WHERE bloco_img_ext="'.str_replace('../../files/documentos/','',str_replace('?embedded=true','',$_GET['file_del'])).'"';
	echo $sql;
	$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	if($res) unlink(str_replace('?embedded=true','',$_GET['file_del']));
}
?>