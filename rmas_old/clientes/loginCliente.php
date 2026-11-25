<? 

include "settings.inc.php"; 

$NIF = $_GET['nif'];
$EMAIL = $_GET['email'];
$TELEFONE = $_GET['telef'];

if($NIF != '' || $EMAIL != '' || $TELEFONE != ''){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	if($NIF != '') $cond = "Nif='".$NIF."'";
	if($EMAIL != '') $cond = "Username='".$EMAIL."'";
	if($TELEFONE != '') $cond = "Telefone='".$TELEFONE."'";

	$sql="Select * from clientes WHERE ".$cond;

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		echo $rs->db_data['ID'].'&;'.$rs->db_data['Nome'].'&;'.$rs->db_data['Apelido'].'&;'.$rs->db_data['Username'].'&;'.$rs->db_data['Telefone'].'&;'.$rs->db_data['Empresa'].'&;'.$rs->db_data['Nif'].'&;'.$rs->db_data['Morada'].'&;'.$rs->db_data['Codigo_Postal'];
	}
}

?>