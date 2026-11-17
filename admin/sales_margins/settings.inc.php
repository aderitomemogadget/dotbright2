<?php

	header('Content-Type: text/html; charset=utf-8');

	$path = '../../core/';

	require_once $path . 'core_files.php';

	require_once $path . 'admin_globals.php';

	require_once 'funcoes.inc.php';

	global $admin_id;

	$dados = getDadosAdministrador($admin_id);

	if(!isset($dados['Permissoes'][25])) die("Sem Permiss&otilde;es");

	// CONFIGURACAO MODULO

	define('MODULO', 'CATEGORIZAÇÃO/AGRUPAMENTO DE ARTIGOS');

	define('TBMODULO', 'sales_margins');

	define('TBID', 'margin_id');



	// BOTOES LATERAIS

	$BOTOES_L = array(
		'CRIAR NOVO GRUPO' => 'pagina.php?margin_id=0&dp='.date('YmdHis'),
		'WORTEN' => $site_http_url.'feeds/worten_csv.php',
		'FNAC' => $site_http_url.'feeds/fnacexporter.php',
		'AMAZON' => $site_http_url.'feeds/amazon.php',
		'CSV' => $site_http_url.'feeds/csv_default.php',
		'DOTT' => $site_http_url.'feeds/dott_csv.php'
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

		"margin_id",

		"ATIVO",

		"DESIGNACAO",

		"VALOR",

		"TIPOMARGEM",

		"DATACRIACAO",

		"DATAALTERACAO",

		"Tipo_grupo",

		"user",

		"TIPORELACAO"

	);



	define('TBRENOVACOES', 'sales_margins_products');

	define('TBRENOVACAOID', 'promo_prod_id');



	$CMPS_RENOCAOES = array(

		"promo_prod_id",

		"margin_id",

		"PROD_ID",

		"FAMILIA",

		"MARCA"

	);

?>