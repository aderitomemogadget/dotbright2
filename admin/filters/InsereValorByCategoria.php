<?
include "settings.inc.php";

$categoria = $_GET['categoria'];
$valor = $_GET['valor'];
global $DB_host,$DB_user,$DB_pass,$DB_name, $admin_lang; 

$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);


$sql = 'INSERT INTO filters_values(category_id,value_order,value_active) VALUES ("'.$categoria.'","0","1")';
$res = Runquery($hConn,$DB_name,$sql);
if($res)
{
	$new_id = db_get_last_insert_id($hConn);
	$sql = 'INSERT INTO filters_values_lang(values_id,values_name,values_lang) VALUES ("'.$new_id.'","'.$valor.'","'.$admin_lang.'")';
	$res = Runquery($hConn,$DB_name,$sql);
}
?>
