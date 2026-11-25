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
	define('MODULO', 'RMAS ESTADOS');
	
	// TABELA HEADER
	define('TBMODULO', 'rma_estados');
	
	// ID DA TABELA HEADER
	define('TBID', 'estado_id');
	
	// PASTA DAS IMAGENS DO MODULO
	define('PASTA_IMGS', '');

	// BOTOES LATERAIS
	if(isset($_GET[TBID]) && $_GET[TBID] > 0)
	{
		$BOTOES_L = array(
			'<i class="fa fa-pencil-square-o" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">SUB-ESTADO</span>' => 'pagina.php?'.TBID.'=0&'.TBID.'_pai='.$_GET[TBID].'&dp='.date('YmdHis')
		);
	}
	else
	{
		$BOTOES_L = array(
			'<i class="fa fa-pencil-square-o" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">INSERIR</span>' => 'pagina.php?'.TBID.'=0&dp='.date('YmdHis'),
			'<i class="fa fa-backward" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">VOLTAR</span>' => '../list.php?dp='.date('YmdHis')
		);
		
	}

	// BOTOES PARA ACOES
	$BOTOES_B = array(
		// 'Ativar Selecionados' => 3,
		// 'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);	
		
	// CAMPOS TABELA HEADER
	$CMPS_MODULO_DADOS = array(
		"estado_id",
		"estado_id_pai",
		"Visivel",
		"Email",
		"Ordem",
		"Cor"
	);
	
	// TABELA LINGUA
	define('TBMODULO_LANG', 'rma_estados_lang');
	
	// ID DA TABELA HEADER
	define('TBID_LANG', 'estado_id_lang');
	
	// CAMPOS TABELA HEADER
	$CMPS_MODULO_DADOS_LANG = array(
		"estado_id_lang",
		"estado_id",
		"Cabecalho",
		"Cabecalho2",
		"Texto",
		"Lang"
	);
	
	// CAMPO DA LINGUA
	define('CAMPO_LINGUA', 'Lang');
	
	
	// ORDENAÇÃO LISTAGEM
	define('ORDER_BY', 'b.Ordem ASC');