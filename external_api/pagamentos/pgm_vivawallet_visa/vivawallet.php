<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	require_once $path.'core_files.php';
	include  "funcoes.php";
	// ORDER
	$idEncomenda = $_POST['idEncomenda'];

	//ESTE EXEMPLO PERMITE INVOCAR UM PAGAMENTO
	//$resultados=pagar_com_visa_sandbox($idEncomenda);
	$resultados=pagar_com_visa($idEncomenda);
?>