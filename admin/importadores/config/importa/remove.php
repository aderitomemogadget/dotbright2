<?php

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	$myFile = dirname( __FILE__ )."/../../../../files/importar/analisar_csv".$Supplier_ID.".txt";
	unlink($myFile);
?>