<?php
	header('Content-Type: text/html; charset=utf-8');
	
	$path = dirname( __FILE__ ) . '/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Templates');	// TITULO MODULO
	define('EDITOR', 0);			// TEM EDITOR	

	// BOTOES LATERAIS
	
	
	// PAGINACAO
	define('N_PAGINAS', 20);
	
	// TABELA PRINCIPAL
	define('TBMODULO', 'templates'); 	// TABELA
	define('TBID', 'ID'); 				// ID PRINCIPAL DO TABELA
	define('TBIDORG', 'ID_ORG'); 				// ID PRINCIPAL DO TABELA
	$CMPS_MODULO_DADOS = array(
		'ID' => 'int(11) NOT NULL AUTO_INCREMENT',
		'ID_ORG' => 'int(11) NOT NULL',
		'Ordem' => 'int(5) NOT NULL DEFAULT "0"',
		'Data' => 'date NOT NULL DEFAULT "0000-00-00"',
		'Data2' => 'date DEFAULT "0000-00-00"',
		'img_ext' => 'varchar(5) DEFAULT NULL',
		'Video' => 'varchar(5) DEFAULT NULL',
		'Visivel' => 'tinyint(1) NOT NULL DEFAULT "0"',
		'Visivel2' => 'tinyint(1) NOT NULL DEFAULT "0"',
		'Destaque' => 'tinyint(1) NOT NULL DEFAULT "0"',
		'margem_top' => 'int(2) NOT NULL DEFAULT "0"',
		'bg_color' => 'varchar(7) NOT NULL DEFAULT ""',
		'efeito' => 'tinyint(1) NOT NULL DEFAULT "0"',
		'URL' => 'varchar(255) NOT NULL DEFAULT ""',
		'site_width' => 'varchar(11) DEFAULT NULL',
		'estilo' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
		'estilo2' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
		'Protected' => 'tinyint(1) NOT NULL'
	);
	
	// TABELA LINGUAS
	define('TBMODULO_LANG', 'templates_lang');	// TABELA LINGUA
	define('TBIDLANG', 'ID'); 					// ID PRINCIPAL DO TABELA	
	define('TBPAI', 'ID_PAI'); 					// CAMPO QUE RECEBE O ID DA TABELA PRINCIPAL
	define('TBLANG', 'Lang'); 					// CAMPO QUE RECEBE A LINGUA ATIVA
	$CMPS_MODULO_DADOS_LANG = array(
		'ID' => 'int(11) NOT NULL AUTO_INCREMENT',
		'ID_PAI' => 'int(11) NOT NULL DEFAULT "0"',
		'Cabecalho' => 'varchar(255) NOT NULL DEFAULT ""',
		'Texto1' => 'text',
		'Texto2' => 'text',
		'Texto3' => 'text',
		'Texto4' => 'text',
		'Lang' => 'varchar(2) NOT NULL DEFAULT ""'
	);
	
	// CRIAÇÃO DE TABELA / ALTERAÇÃO DE TABELA	
	createtable(TBID, TBMODULO, $CMPS_MODULO_DADOS); // TABELA PRINCIPAL
	createtable(TBIDLANG, TBMODULO_LANG, $CMPS_MODULO_DADOS_LANG); // TABELA LINGUA
	
	$BOTOES_L = array(
		'CRIAR NOVO GRUPO' => 'pagina.php?'.TBID.'=0&dp='.date('YmdHis'),
		'CRIAR NOVO TEMPLATE' => 'pagina.php?'.TBID.'=0&ID_ORG='.$_GET[TBID].'&dp='.date('YmdHis')
	);
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);
?>