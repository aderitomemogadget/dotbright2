<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Personalização');
	define('TBMODULO', 'templates_blocos');	
	define('TBID', 'bloco_id');
	define('FOLDER', 'templates_blocos');

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
		"bloco_ordem",
		"bloco_tipo",
		"bloco_ativo",
		"bloco_titulo"
	);
	
	define('TBMODULO_LANG', '');
	
	$CMPS_MODULO_DADOS_LANG = array();
	
	
	
	$templates_tipos[0]  = 'Homepage Principal';
	// $templates_tipos[1] = 'Homepage Secundárias';
	$templates_tipos[2]  = 'Listagem de Produtos';
	$templates_tipos[3]  = 'Ficha de Produto';
	$templates_tipos[4]  = 'Carrinho de Compras';
	$templates_tipos[5]  = 'Páginas';
	$templates_tipos[6]  = 'Subscrever Newsletter';
	$templates_tipos[7]  = 'Minha Conta';
	$templates_tipos[8]  = 'Minhas Encomendas';
	$templates_tipos[9]  = 'Registo Utilizador';
	$templates_tipos[10] = 'Página de Pesquisa';
	$templates_tipos[20] = 'Listagem de Favoritos';
	$templates_tipos[30] = 'Bloco do Produto';	
	
?>