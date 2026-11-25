<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	
	// CONFIGURACAO MODULO
	define('MODULO', 'RMAS');
	define('TBMODULO', 'blocos');	
	define('TBID', 'bloco_id');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		'<i class="fa fa-sitemap" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">ACESSÓRIOS</span>' => 'pagina.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis'),
		'<i class="fa fa-wrench" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">AVARIAS</span>' => 'pagina.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis'),
		'<i class="fa fa-users" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">CLIENTES</span>' => 'clientes/list.php?dp='.date('YmdHis'),
		// '<i class="fa fa-file-text-o" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">LISTAGENS</span>' => 'locais/list.php?dp='.date('YmdHis'),
		'<i class="fa fa-globe" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">LOCALIZAÇÕES</span>' => 'locais/list.php?dp='.date('YmdHis'),
		// '<i class="fa fa-truck" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">FORNECEDORES</span>' => 'pagina.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis'),
		'<i class="fa fa-mobile" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">TELÉMOVEIS</span>' => 'pagina.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis'),
		'<i class="fa fa-file-pdf-o" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">RELATÓRIOS</span>' => 'pagina.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis'),
		'<i class="fa fa-home" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">DADOS LOJAS</span>' => 'pagina.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis'),
		'<i class="fa fa-cogs" style="width:30px; text-align: center;"></i>&nbsp;<span style="font-size:12px">ESTADOS</span>' => 'estados/list.php?bloco_id=0&bloco_id_pai='.$_GET['bloco_id'].'&dp='.date('YmdHis')
	);
	
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);
	
	// PAGINACAO
	define('N_PAGINAS', 20);	
	
	$CMPS_MODULO_DADOS = array(
		"bloco_id",
		"bloco_id_pai",
		"bloco_visivel",
		"bloco_categorias",
		"bloco_ordem",
		"bloco_data",
		"bloco_data2",
		"bloco_permanente",
		"bloco_ext",
		"bloco_pdf_ext",
		"bloco_opcao_embalagem",
		"bloco_opcao_cor",
		"bloco_opcao_vinhos",
		"bloco_opcao_iguarias",
		"bloco_opcao_cartao",
		"bloco_opcao_placafoto",
		"bloco_opcao_placafrase",
		"bloco_opcao_embrulho",
		"bloco_preco",
		"bloco_promo",
		"bloco_preco_promo",
		"bloco_embalagens_multiple",
		"bloco_cores_multiple",
		"bloco_vinhos_multiple",
		"bloco_iguarias_multiple",
		"bloco_maximo_vinhos",		
		"bloco_maximo_iguarias"
	);
	
	define('TBMODULO_LANG', 'blocos_lang');
	
	$CMPS_MODULO_DADOS_LANG = array(
		"bloco_lang_id",
		"bloco_id",
		"bloco_titulo",
		"bloco_titulo_2",
		"bloco_titulo_3",
		"bloco_resumo",
		"bloco_texto",
		"bloco_lang"
	);	
	
	define('TBIMAGENS', 'blocos_imgs');
	define('TBIMGID', 'bloco_img_id');
	
	$CMPS_MODULO_IMGS = array(
		"bloco_img_id",
		"bloco_id",
		"bloco_img_titulo",
		"bloco_img_ext",
		"bloco_rel"
	);
	
	define('TBIMAGENS2', 'blocos_cores_imgs');
	define('TBIMGID2', 'bloco_img_id');
	
	$CMPS_MODULO_IMGS2 = array(
		"bloco_img_id",
		"bloco_id",
		"bloco_img_titulo",
		"bloco_cor_img_ext",
		"bloco_rel",
		"bloco_rel2"
	);
?>