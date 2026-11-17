<?
	include "settings.inc.php";
	$retorno="list.php?dump=".uniqid("");
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$flag=true;
	if($flag)$res=Runquery($db_link,$DB_name,"UPDATE nl_emails_registados SET ID_ORG=$grupo_id WHERE ID_ORG=$ID_Grupo");
	if(!$res)$flag=false;
	if($flag)$res=Runquery($db_link,$DB_name,"UPDATE nl_emails_removidos SET ID_ORG=$grupo_id WHERE ID_ORG=$ID_Grupo");
	if(!$res)$flag=false;
	if($flag) show_box_OK(2);
	else show_box_ERROR(2);
?>