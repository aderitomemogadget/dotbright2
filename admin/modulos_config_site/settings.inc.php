<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	require_once 'funcoes_avancada.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'Definições da Plataforma');
	define('TBMODULO', 'modulos_config_site');	
	define('TBID', 'ID');
	define('FOLDER', 'modulos_config_site');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		// 'INSERIR NOVO BLOCO' => 'pagina.php?garantia_id=0&dp='.date('YmdHis')
	);	
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	// CAMPOS DE IMAGEM DEVEM COMEÇAR SEMPRE APARTIR DA 3ª POSICAO E DEVEM TERMINAR COM '_ext'
	$CMPS_MODULO_DADOS = array(
		"ID",		
		"revendedor_id",
		"img_ext",
		"img2_ext",
		"img3_ext",
		"img4_ext",
		"img5_ext",
		"pais_id_default",
		"exec_caminho",
		"forcar_https",
		"FromName",
		"form_recipient",
		"Default_SMTP",
		"Default_SMTP_login",
		"Default_SMTP_pass",
		"Default_SMTP_Email",
		"Default_SMTP_Porta",
		"Default_notifications_email",
		"TAG_EMPRESA",
		"TAG_DESIGSOCIAL",
		"TAG_COPYRIGHT",
		"SEO_FB",
		"SEO_PHONE",
		"SEO_DESCRIPTION",
		"iva_default",
		"offline",
		"offline_txt",
		"offline_cor",
		"Seta_scrollup",
		"emails_encomenda",
		
		"stock_azul_aparece",
		"stock_vermelho_aparece",
		"stock_amarelo_aparece",
		"stock_verde_aparece",
		
		"stock_azul_qtd",
		"stock_vermelho_qtd",
		"stock_amarelo_qtd",
		"stock_verde_qtd",
		
		"stock_azul",
		"stock_vermelho",
		"stock_amarelo",
		"stock_verde",
		
		"stock_azul_ativo",
		"stock_vermelho_ativo",
		"stock_amarelo_ativo",
		"stock_verde_ativo",

		"facebook_id",
		"facebook_secret",
		"facebook_ativo",

		"google_id",
		"google_secret",
		"google_ativo",

		"twitter_id",
		"twitter_secret",
		"twitter_ativo",

		"dblocalhost",
		"dbusername",
		"dbpassword",
		"dbname",

		
		"title",
		"url",
		"url_email",
		
		"addthis_id",
		"addthis_ativo",
		"icon_bc",
		"texto_bc",

		"cookie_txt_barra",
		"cookie_txt_modal",
		"cookie_posicao",
		"cred_min",
		"onoff_valor_min"
	);