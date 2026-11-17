<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Configuração Tipos');
	define('TBMODULO', 'entrega');	
	define('TBID', 'ID');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		'NOVO TEMPO de ENTREGA' => 'pagina.php?ID=0&dp='.date('YmdHis')
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
		"ID",
		"Ordem",
		"Visivel",
		"dias",
		"custo_entrega",
		"Tipo"
		
	);
	
	define('TBMODULO_LANG', 'entrega_lang');
	
	$CMPS_MODULO_DADOS_LANG = array(
		"ID",
		"ID_PAI",
		"Nome",
		"Lang"
	);
?>