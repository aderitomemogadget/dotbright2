<?php
	$path=dirname( __FILE__ ).'/../core/';	
	include $path.'core_files.php';	

$table = 'templates_imgs';

$id = $_POST['id'];
$color = $_POST['color'];
$link = $_POST['link'];
$s = $_POST['s'];
if($s == 'e'){
$addsql = ' set link_imge_ext="'.$link.'", bgcolore="'.$color.'" ';
}
if($s == 'd'){
$addsql = ' set link_imgd_ext="'.$link.'", bgcolord="'.$color.'" ';
}

$sql = 'UPDATE '.$table.' '.$addsql.' WHERE ID='.$id;
$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
echo json_encode(1);
?>