<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Personalização');
	define('TBMODULO', 'personalizacao');	
	define('TBID', 'bloco_id');
	define('FOLDER', 'personalizacao');

	// BOTOES LATERAIS
	
	$BOTOES_L = array(
		// 'INSERIR FRAMES' => 'pagina.php?bloco_id=0&dp='.date('YmdHis'),
		'INSERIR' => 'pagina.php?bloco_id=0&bloco_id_pai='.(isset($_GET['bloco_id']) && $_GET['bloco_id'] > 0 ? $_GET['bloco_id'] : 0).'&dp='.date('YmdHis')
	);	
	
	// BOTOES PARA ACOES
	if(isset($_GET['bloco_id']) && $_GET['bloco_id'] > 0)
	{
		$BOTOES_B = array(
			'Ativar Selecionados' => 3,
			'Desativar Selecionados' => 2,
			'Remover Selecionados' => 4
		);
	}
	else
	{
	
		$BOTOES_B = array(
			'Ativar Selecionados' => 3,
			'Desativar Selecionados' => 2
		);
	}
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	$CMPS_MODULO_DADOS = array(
		"bloco_id",		
		"m_familia",
		"m_marca",
		"bloco_id_pai",
		"bloco_tipo",
		"bloco_obrigatorio",
		"bloco_visivel",
		"bloco_ext",
		"width",
		"height",
		"bloco_preco",
		"bloco_ordem"
	);
	
	define('TBMODULO_LANG', 'personalizacao_lang');
	
	$CMPS_MODULO_DADOS_LANG = array(
		"bloco_lang_id",
		"bloco_id",
		"bloco_titulo",	
		"bloco_resumo",	
		"bloco_texto",	
		"bloco_lang"
	);
	

	// $campos_tipo[6] = 'Envio Ficheiro/Imagem';
	
	
	
	
?>