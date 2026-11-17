<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	require_once 'funcoes_avancada.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Extensão de Garantia');
	define('TBMODULO', 'extensao_garantia');	
	define('TBID', 'garantia_id');
	define('FOLDER', 'garantia');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		'INSERIR NOVO BLOCO' => 'pagina.php?garantia_id=0&dp='.date('YmdHis')
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
		"garantia_id",		
		"familia_id",
		"bloco_ext",
		"img_ext",
		"titulo",
		"info",
		"garantia_n_anos",
		"garantia_valor",
		"garantia_ini",
		"garantia_fim",
		"promo_ativo",
		"valor_promo",
		"garantia_visivel"
	);
?>