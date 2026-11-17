<?php 
include 'settings.inc.php';

if(isset($_GET['file_del']) && $_GET['file_del']!='')
{
	$res = 1;
	if(isset($_GET['fielddel']) && $_GET['fielddel']!='')
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$sql = 'UPDATE blocos_vinhos SET '.$_GET['fielddel'].'="" WHERE '.$_GET['fielddel'].'="'.str_replace('../../files/documentos/','',str_replace('?embedded=true','',$_GET['file_del'])).'"';
		echo $sql;
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	if($res) unlink(str_replace('?embedded=true','',$_GET['file_del']));
}
?>