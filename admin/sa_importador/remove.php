<?php
$path = dirname( __FILE__ ) . '/../../core/';
require_once $path . 'core_files.php';
	
if(isset($_GET['remove']) && $_GET['remove'] != '' && file_exists('../../files/importar/'.$_GET['remove']))
{
	// $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);		
	// $sql = 'TRUNCATE TABLE products_import';
	// $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	
	@unlink('../../files/importar/'.$_GET['remove']);
	@unlink('../../files/importar/atualizar_temp.txt');
}
elseif(isset($_GET['remove2']) && $_GET['remove2'] != '' && file_exists('../../files/importar/atualizar.txt'))
{
	@unlink('../../files/importar/atualizar.txt');
}