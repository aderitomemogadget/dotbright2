<?php
	include "settings.inc.php";
	global $site_shell_url;
	header('Content-Type: text/html; charset=utf-8');
	//require_once($site_shell_url."admin/layout/header.inc.php");

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT email_cliente from encomendas WHERE ID=".$_GET['id_enc']."";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		$email_cliente 				= $rs->db_data["email_cliente"];

		echo html_entity_decode(stripslashes($email_cliente));
	}
	$rs->db_close();

?>