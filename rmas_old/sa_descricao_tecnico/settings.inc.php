<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Descrição do Técnico');
	define('TBMODULO', 'rmas_descricao_tecnico');	
	define('TBID', 'ID');
	define('FOLDER', 'rmas_descricao_tecnico');

	
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
	define('N_PAGINAS', 20);
	
	$CMPS_MODULO_DADOS = array(
		"ID",
		"ordem",
		"title"
	);
	
	define('TBMODULO_LANG', '');
	
	$CMPS_MODULO_DADOS_LANG = array();
	