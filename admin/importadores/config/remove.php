<?php

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	$path = dirname( __FILE__ ) . '/../../../core/';
	require_once $path . 'core_files.php';

	if(isset($_GET['remove']) && $_GET['remove'] != '' && file_exists('../../files/importar/'.$_GET['remove']))
	{

		@unlink('../../files/importar/'.$_GET['remove']);
		@unlink('../../files/importar/atualizar_temp.txt');
	}
	elseif(isset($_GET['remove2']) && $_GET['remove2'] != '' && file_exists('../../files/importar/atualizar.txt'))
	{
		@unlink('../../files/importar/atualizar.txt');
	}
?>