<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = dirname( __FILE__ ) . '/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Configuração Linguas');
	define('TBMODULO', 'blocos_linguas');	
	define('TBID', 'bloco_id');

	// BOTOES LATERAIS
	$BOTOES_L = array
	(
		'INSERIR NOVO' => 'pagina.php?bloco_id=0&dp='.date('YmdHis'),
		'GERAR FICHEIROS' => 'gerar_ficheiros.php'
	);	
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		// 'Ativar Selecionados' => 3,
		// 'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	$CMPS_MODULO_DADOS = array(
		"bloco_id",
		"bloco_titulo"
	);
	
	define('TBMODULO_LANG', 'blocos_linguas_lang');
	
	$CMPS_MODULO_DADOS_LANG = array(
		"bloco_lang_id",
		"bloco_id",
		"bloco_variavel",
		"bloco_texto",
		"bloco_lang"
	);
?>