<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Estados RMAS');
	define('TBMODULO', 'rmas_estados');	
	define('TBID', 'ID');
	define('FOLDER', 'rmas');

	
	// ALTER TABLE `rmas_estados` ADD `f_estado` INT( 11 ) NOT NULL ;
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
		"Visivel",
		"Email",
		"sms_envia",
		"Ordem",
		"Obs",
		"Cor",
		"f_estado",
		"f_leitura"
	);
	
	define('TBMODULO_LANG', 'rmas_estados_lang');
	
	$CMPS_MODULO_DADOS_LANG = array(
		"ID",
		"ID_PAI",
		"Cabecalho",	
		"Cabecalho2",	
		"Texto",
		"Texto2",
		"Sms",		
		"Lang"
	);
	