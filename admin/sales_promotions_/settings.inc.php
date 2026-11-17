<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][27])) die("Sem Permiss&otilde;es");
	// CONFIGURACAO MODULO
	define('MODULO', 'PROMOÇÕES');
	define('TBMODULO', 'sales_promotions');
	define('TBID', 'promo_id');
	
	// BOTOES LATERAIS
	$BOTOES_L = array(
		'INSERIR NOVA PROMOÇÃO' => 'pagina.php?promo_id=0&dp='.date('YmdHis'),
		'EXPORTAR PRODUTOS 100%' => 'exportar.php?dp='.date('YmdHis'),
	);	
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Remover Selecionados' => 1,
		'Ativar Selecionados' => 2,
		'Desativar Selecionados' => 3
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	$CMPS_MODULO_DADOS = array(
		"promo_id",
		"ATIVO",
		"DATA_INICIO",
		"DATA_FIM",
		"DESIGNACAO",
		"DESCRICAO",
		"TIPO",
		"MINIMO",
		"MAXIMO",
		"QUATIDADEMAXIMA",
		"TIPOPROMOCAO",
		"TIPOCAMPANHA",
		"DATACRIACAO",
		"DATAALTERACAO",
		"user",
		"IMAGEM_EXT",
		"TEMPO"
	);

	define('TBRENOVACOES', 'sales_promotions_products');
	define('TBRENOVACAOID', 'promo_prod_id');
	
	$CMPS_RENOCAOES = array(
		"promo_prod_id",
		"promo_id",
		"DESCONTO",
		"PROD_ID",
		"FAMILIA",
		"MARCA"
	);	
?>