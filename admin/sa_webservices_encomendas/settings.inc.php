<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';

	// CONFIGURACAO MODULO
	define('MODULO', 'WEBSERVICE »» IPS AUTORIZADOS');
	define('TBMODULO', 'encomendas_ips_webservices');
	define('TBID', 'ID');
	define('FOLDER', 'encomendas');

	// BOTOES LATERAIS
	$BOTOES_L = array
	(
		'INSERIR NOVO' => 'pagina.php?ID=0&dp='.date('YmdHis')
	);

	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2
		// 'Remover Selecionados' => 4
	);

	// PAGINACAO
	define('N_PAGINAS', 30);

	$CMPS_MODULO_DADOS = array(
		"ID",
		"Activo",
		"IP"
	);


?>