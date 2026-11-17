<?php
	$ajax=1;
	$path='../core/';
	include $path.'core_files.php';
	$dados=cliente_logado();

	if($dados['cli_id'] != '') $cli_id=$dados['cli_id'];
	else $cli_id=0;
	$n_prods=getComparadorCliente($cli_id, $_SESSION['CLIENTE_ID_TEMP']);

	echo $n_prods;

?>