<?php
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    if(!$db_link)die('ERRO BD');
    else echo 'OK';
?>