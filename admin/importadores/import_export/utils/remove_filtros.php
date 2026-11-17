<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// ATENÇÃO, NOVO SCRIPT, FAZER BACKUP ANTES DE MODIFICAR ///////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

$path = dirname( __FILE__ ) . '/../../../../core/';
require_once $path . 'core_files.php';
$path2=dirname( __FILE__ ) . '/../../../../';
include $path2 . 'admin/products/filtros/funcoes.inc.php';
if(!isset($_GET['log'])) $log = 0;
error_reporting(E_ERROR); ini_set('display_errors',1);

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time','60000');
ini_set('max_input_time','60000');
ini_set('memory_limit','-1');


// SE AINDA NÃO EXISTIR, FAZ DOWNLOAD DO FICHEIRO DA MACORLUX
echo "a remover produtos 1\n";


	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT ID FROM products WHERE Aproved=0 AND fornecedor_importacao = "dotbright"';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$rs->db_data['ID']."_pt.json") as $filename)
			{
			echo	@unlink($filename);
			$del++;
			}

			$rs->db_next();
		}
	}
	$rs->db_close();
?>