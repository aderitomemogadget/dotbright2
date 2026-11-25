<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'PAGAMENTOS');
	define('TBMODULO', 'encomendas');
	define('TBMODULOLANG', 'encomendas_produtos');
	define('TBID', 'ID');
	
	// BOTOES LATERAIS
	$BOTOES_L = array(
		'INSERIR NOVO PAGAMENTO' => 'pagina.php?ID=0&dp='.date('YmdHis')
	);	
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		// 'Remover Selecionados' => 1
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	$CMPS_MODULO_DADOS = array(
		"ID",
		"Contacto",
		"Password",
		"Titulo",
		"Nome",
		"Apelido",
		"Telefone",
		"Fax",
		"Endereco",
		"EFaturacao",
		"Empresa",
		"Creditos",		
		"Ativo",
		"Pais",
		"Grupo_ID"
	);
?>