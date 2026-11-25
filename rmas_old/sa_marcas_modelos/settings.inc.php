<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Marcas');
	define('TBMODULO', 'marcas_rmas');	
	define('TBID', 'ID');
	define('FOLDER', 'marcas_rmas');
	
	
	define('MODULO2', 'Modelos');
	define('TBMODULO2', 'modelos_rmas');	
	define('TBID2', 'ID');

	
	// ALTER TABLE `rmas_estados` ADD `f_estado` INT( 11 ) NOT NULL ;
	// BOTOES LATERAIS
	$BOTOES_L = array
	(
		'NOVA MARCA' => 'pagina.php?ID=0&dp='.date('YmdHis'),
		'NOVO MODELO' => 'pagina_modelo.php?ID=0&dp='.date('YmdHis')
	);
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		// 'Ativar Selecionados' => 3,
		// 'Desativar Selecionados' => 2
		'Remover Selecionados' => 4
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);
	
	$CMPS_MODULO_DADOS = array(
		"ID",
		"Ordem",
		"marca"
	);
	
	$CMPS_MODULO2_DADOS = array(
		"ID",
		"Ordem",
		"marca_id",
		"modelo"
	);
	
	
	define('TBMODULO_LANG', '');
	
	$CMPS_MODULO_DADOS_LANG = array();