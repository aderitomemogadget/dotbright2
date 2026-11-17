<?php
	ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');
	set_time_limit(0);
	$path = '../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';

	// CONFIGURACAO MODULO
	define('MODULO', 'telemoveis_emp');
	define('TBMODULO', 'telemoveis_emp');	 // TABELA
	define('TBID', 'id'); // ID PRINCIPAL DA TABELA

	// CAMOS DA TABELA
	$CMPS_MODULO_DADOS = array(
		"id",
		"marca",
		"modelo",
		"imei",
		"estado"
	);