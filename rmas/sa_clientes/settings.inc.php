<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';

	// CONFIGURACAO MODULO
	define('MODULO', 'Clientes Ligados aos RMAS');
	define('TBMODULO', 'clientes');
	define('TBID', 'ID');
	define('FOLDER', 'clientes');


	// ALTER TABLE `rmas_estados` ADD `f_estado` INT( 11 ) NOT NULL ;
	// BOTOES LATERAIS
	$BOTOES_L = array
	(
		'INSERIR NOVO' => 'pagina.php?ID=0&dp='.date('YmdHis')
	);

	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2
		// 'Remover Selecionados' => 4
	);

	// PAGINACAO
	define('N_PAGINAS', 20);

	$CMPS_MODULO_DADOS = array(
		"ID",
		"Username",
		"Nome",
		"Apelido",
		"Empresa",
		"Morada",
		"Cidade",
		"Codigo_Postal",
		"Pais",
		"Telefone",
		"Telefone2",
		"tem_email",
		"Sobrenome",
		"Estado",
		"Nif",
		"Grupo_ID"
	);

	define('TBMODULO_LANG', '');

	$CMPS_MODULO_DADOS_LANG = array();
