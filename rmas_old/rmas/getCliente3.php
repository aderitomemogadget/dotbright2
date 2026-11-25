<?php
include "settings.inc.php";

$email = trim($_GET['email']);
$tel = trim($_GET['tel']);
$tel2 = trim($_GET['tel2']);
$nif = trim($_GET['nif']);

if($email != '' || $tel != '' ||  $tel2 != '' ||  $nif != '')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$cond = array();
	if($nif) 	$cond[] = " Nif='".$nif."'";
	if($email) 	$cond[] = " Username='".$email."'";
	if($tel) 	$cond[] = " Telefone='".$tel."'";
	if($tel2) 	$cond[] = " Telefone='".$tel2."'";
	
	// $cond .= " OR Apelido LIKE '%".$keyword."%'";
	if(count($cond) > 0)
	{
		$sql = "Select * from clientes WHERE ".implode(' OR ', $cond);
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())echo $rs->db_data['ID'].'&;'.$rs->db_data['Nome'].'&;'.$rs->db_data['Apelido'].'&;'.$rs->db_data['Username'].'&;'.$rs->db_data['Telefone'].'&;'.$rs->db_data['Empresa'].'&;'.$rs->db_data['Nif'].'&;'.$rs->db_data['Morada'].'&;'.$rs->db_data['Codigo_Postal'].'&;'.$rs->db_data['Telefone'].$rs->db_data['Telefone_ent'].$rs->db_data['Telefone'];
		$rs->db_close(1);
	}
}
?>