<?php
//CREATE TABLE test ENGINE=MEMORY
$path = dirname( __FILE__ ) . '/../../core/';
require_once $path . 'core_files.php';
require_once dirname( __FILE__ ).'/../../admin/products/funcoesfiltros.inc.php';
require_once dirname( __FILE__ ).'/../../admin/products/filtros/funcoes.inc.php';

if(!isset($_GET['log'])) $log = 0;
error_reporting(E_ERROR);
ini_set('display_errors',0);

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time','60000');
ini_set('max_input_time','60000');
ini_set('memory_limit','-1');


global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

$rs = new DataObj;

//Para evitar estar a repetir sempre a mesma acção, vamos criar uma flag
$sql = 'SELECT ID,idtime,udtime FROM products WHERE Sync=0 and Aproved=1 AND idtime < ( NOW() - INTERVAL 30 DAY ) AND udtime < ( NOW() - INTERVAL 30 DAY )';
$rs->db_open( $db_link,$DB_name,$sql);
while(!$rs->EOF())
{
	$id_produto = $rs->db_data["ID"];

	$sql='UPDATE products SET Aproved=0 WHERE ID = '.$id_produto;
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";


	$sql = "delete from acelerador_pesquisa where ID_Prod=$id_produto";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";

	$rs->db_next();
	echo "*";
}
$rs->db_close();

echo "FIM";
?>