<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	require_once 'funcoes_avancada.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Portes');
	define('TBMODULO', 'multi_portes');	
	define('TBID', 'id');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		'INSERIR NOVO' => 'pagina.php?id=0&dp='.date('YmdHis')
	);	
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	$CMPS_MODULO_DADOS = array(
		"id",
		"familia_id",
		"bloco_ext",
		"promo_ativo",
		"valor",
		"valor_promo",
		"ini",
		"fim",
		"visivel",
		"titulo",
		"info",
		"portes_pesos",
		"portes_pesos_formula"
	);
?>