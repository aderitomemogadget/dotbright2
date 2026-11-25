<?php
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$ID=intval($_GET["ID"]);
	$sql = 'UPDATE rmas set lida = 1 WHERE ID = '.$ID;
	$res = Runquery($db_link, $DB_name, $sql);

?>