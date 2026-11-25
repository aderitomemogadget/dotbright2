<?php
	header('Content-Type: text/html; charset=utf-8');
	
	$path = '../../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';	

	// PASTA JAVASCRIPT / CSS
	define('JS', 'js/');
	define('CSS', 'css/');
	define('CHOSEN', 'chosen/'); // selectbox	
	define('CAMINHO_EDITOR', '../../editor/ckeditor/'); // selectbox	
	
	// PAGINACAO DA LISTAGEM
	define('N_PAGINAS', 20);
	
	// NOME DO MODULO
	define('MODULO', 'RMAS LOCAIS');
	
	// TABELA HEADER
	define('TBMODULO', 'rma_locais');
	
	// ID DA TABELA HEADER
	define('TBID', 'local_id');
	
	// PASTA DAS IMAGENS DO MODULO
	define('PASTA_IMGS', '');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		'<i class="fa fa-pencil-square-o" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">INSERIR</span>' => 'pagina.php?'.TBID.'=0&dp='.date('YmdHis'),
		'<i class="fa fa-backward" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">VOLTAR</span>' => '../list.php?dp='.date('YmdHis')
	);
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		// 'Ativar Selecionados' => 3,
		// 'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);	
		
	// CAMPOS TABELA HEADER
	$CMPS_MODULO_DADOS = array(
		"local_id",
		"local_ordem"
	);
	
	// TABELA LINGUA
	define('TBMODULO_LANG', 'rma_locais_lang');
	
	// ID DA TABELA HEADER
	define('TBID_LANG', 'local_lang_id');
	
	// CAMPOS TABELA HEADER
	$CMPS_MODULO_DADOS_LANG = array(
		"local_lang_id",
		"local_id",
		"local_titulo",
		"local_lang"
	);
	
	// CAMPO DA LINGUA
	define('CAMPO_LINGUA', 'local_lang');
	
	
	// ORDENAÇÃO LISTAGEM
	define('ORDER_BY', 'b.local_ordem ASC');