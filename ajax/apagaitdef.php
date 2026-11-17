<?php
	$path=dirname( __FILE__ ).'/../core/';	
	include $path.'core_files.php';	

$table = 'templates_imgs';
$sql = 'DELETE FROM '.$table.' WHERE ID='.$_POST['id'];
$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
Runquery($db_link,DB_NAME,$sql);

$str = templateimages($_POST['ID_PAI']);

echo json_encode($str);
?>