<?php
	$ajax = 1;
	$path = dirname( __FILE__ ).'/../core/';
	include $path . 'core_files.php';
	
	$q = $_GET['q'];
	$voucher_code = preg_replace("/[^A-Za-z0-9_.]/", '', $q);
		
	$verifica = verifica_voucher($voucher_code,$voucher);
	if($verifica['erro'] == '')
	{
		echo 'OK';
	}
	else
	{
		$erro = $verifica['erro'];
		echo $erro;
	}