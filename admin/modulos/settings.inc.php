<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path.'core_files.php';
	require_once $path.'admin_globals.php';
	require_once 'funcoes.inc.php';
	$dados = getDadosAdministrador($admin_id);

	// CONFIGURACAO MODULO
	define('MODULO', 'MÓDULOS');
	define('TBMODULO', 'modulos');
	define('TBID', 'ID');

  // BOTOES LATERAIS
	if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4)
	{
		if($_GET['preview']==1)$preview=0;
		else $preview=1;

		$BOTOES_L = array(
			'CONFIGURAÇÕES SITE' => array('url'  => '../modulos_config_site/pagina.php?dp='.date('YmdHis'), 'target' => '_blank', 'confirm' => ''),
			'CONFIGURAÇÕES CSS' => array('url'  => '../modulos_config/list.php?ID=0&dp='.date('YmdHis'), 'target' => '_blank', 'confirm' => ''),
			'PRE-VISUALIZAR' => array('url'  => 'pagina.php?ID='.$_GET['ID'].'&preview='.$preview.'&dp='.date('YmdHis'), 'target' => '_self', 'confirm' => "Este evento vai fazer refresh à página. Tem a certeza que deseja continuar?"),
			'NOVO ELEMENTO' => array('url'  => 'pagina.php?ID=0&ID_ORG='.$_GET['ID'].'&dp='.date('YmdHis'), 'target' => '_blank', 'confirm' => '')
		);
	}
	else
	{
		$BOTOES_L = array(
			'INSERIR ELEMENTO' => 'pagina.php?ID=0&ID_ORG='.$_GET['ID'].'&dp='.date('YmdHis')
		);
	}
	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Reordenar Selecionados' => 1,
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);

	// PAGINACAO
	define('N_PAGINAS', 20);
	if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4)
	{
		$CMPS_MODULO_DADOS = array(
			"ID",
			"ID_ORG",
			"visivel",
			"ocultar",
			"destaque_home",
			"destaque_lateral",
			"destaque_topo",
			"categorias",
			"ordem",
			"data",
			"data2",
			"permanente",
			"img_ext",
			"pdf_ext",
			"open",
			"users",
			"permissoes",
			"Qtd",
			"Anchor",
			"Posicao",
			"titulo_visivel",
			"titulo_cor",
			"titulo_tamanho",
			"titulo_font_weight",
			"Texto1_cor",
			"Texto1_tamanho",
			"Texto1_font_weight",
			"Texto1_line_height",
			"Texto2_cor",
			"Texto2_tamanho",
			"Texto2_font_weight",
			"Texto2_line_height",
			"Texto3_cor",
			"Texto3_tamanho",
			"Texto3_font_weight",
			"Texto3_line_height",
			"Tem_paginacao",
			"Tem_setas",
			"Tem_H1",
			"Tem_sombra",
			"Layout",
			"Estilo",
			"Largura",
			"Altura",
			"altura_max",
			"Estilo_Back",
			"Modulo_animacao",
			"bc_hometype",
			"bc_homename"

		);
	}
	else
	{
		$CMPS_MODULO_DADOS = array(
			"ID",
			"ID_ORG",
			"visivel",
			"ocultar",
			"destaque_home",
			"destaque_lateral",
			"destaque_topo",
			"categorias",
			"ordem",
			"data",
			"data2",
			"permanente",
			"img_ext",
			"pdf_ext",
			"open",
			"Qtd",
			"Anchor",
			"Posicao",
			"titulo_visivel",
			"titulo_cor",
			"titulo_tamanho",
			"titulo_font_weight",
			"Texto1_cor",
			"Texto1_tamanho",
			"Texto1_font_weight",
			"Texto1_line_height",
			"Texto2_cor",
			"Texto2_tamanho",
			"Texto2_font_weight",
			"Texto2_line_height",
			"Texto3_cor",
			"Texto3_tamanho",
			"Texto3_font_weight",
			"Texto3_line_height",
			"Tem_paginacao",
			"Tem_setas",
			"Tem_H1",
			"Tem_sombra",
			"Layout",
			"Estilo",
			"Largura",
			"Altura",
			"altura_max",
			"Estilo_Back",
			"Modulo_animacao",
			"bc_hometype",
			"bc_homename"
		);
	}
	define('TBMODULO_LANG', 'modulos_lang');
	$CMPS_MODULO_DADOS_LANG = array(
		"ID",
		"ID_PAI",
		"titulo",
		"titulo_2",
		"titulo_3",
		"resumo",
		"texto",
		"texto3",
		"lang"
	);
	define('TBIMAGENS', 'modulos_imgs');
	define('TBIMGID', 'bloco_img_id');
	$CMPS_MODULO_IMGS = array(
		"bloco_img_id",
		"bloco_img_ID_ORG",
		"bloco_img_titulo_visivel",
		"bloco_img_url_default",
		"bloco_img_titulo_cor",
		"bloco_img_titulo_tamanho",
		"bloco_img_titulo_font_weight",
		"bloco_img_limite",
		"bloco_img_txt_cor",
		"bloco_img_txt_tamanho",
		"bloco_img_txt_font_weight",
		"bloco_img_txt_line_height",
		"bloco_img_url",
		"bloco_img_video",
		"bloco_img_ext",
		"bloco_img2_ext",
		"bloco_pdf_ext",
		"bloco_img_ordem",
		"bloco_img_data",
		"bloco_img_data_visivel",
		"bloco_img_animacao",
		"bloco_img_layout",
		"bloco_img_lightbox",

		"a_data_x",
		"a_data_y",
		"a_data_voffset",
		"a_data_hoffset",
		"a_data_speed",
		"a_data_start",
		"a_data_easing",
		"a_data_endspeed",
		"a_data_text_align",
		"b_data_x",
		"b_data_y",
		"b_data_voffset",
		"b_data_hoffset",
		"b_data_speed",
		"b_data_start",
		"b_data_easing",
		"b_data_endspeed",
		"b_data_text_align",
		"c_data_x",
		"c_data_y",
		"c_data_voffset",
		"c_data_hoffset",
		"c_data_speed",
		"c_data_start",
		"c_data_easing",
		"c_data_endspeed",
		"c_data_text_align",
		"d_data_x",
		"d_data_y",
		"d_data_voffset",
		"d_data_hoffset",
		"d_data_speed",
		"d_data_start",
		"d_data_easing",
		"d_data_endspeed",
		"d_data_text_align",

		"bloco_layout"
	);
	define('TBIMAGENS_LANG', 'modulos_imgs_lang');
	$CMPS_MODULO_IMGS_LANG = array(
		"bloco_img_lang_id",
		"bloco_img_ID_PAI",
		"bloco_img_titulo",
		"bloco_img_txt",
		"bloco_img2_txt",
		"bloco_video_code",
		"bloco_video_yt",
		"bloco_video_vimeo",
		"lang"
	);



	// MODULOS
	$arr_familias[0]['brand_id'] = 1; 	$arr_familias[0]['marca_nome'] 	= "GRUPO de MODULOS";
	$arr_familias[1]['brand_id'] = 2; 	$arr_familias[1]['marca_nome'] 	= "Banner";
	$arr_familias[2]['brand_id'] = 3; 	$arr_familias[2]['marca_nome'] 	= "Texto simples centrado";
	$arr_familias[3]['brand_id'] = 4;	$arr_familias[3]['marca_nome'] 	= "Carrocell Imagens - Bolas";
	$arr_familias[4]['brand_id'] = 5;	$arr_familias[4]['marca_nome'] 	= "Carrocell";
	$arr_familias[5]['brand_id'] = 6;	$arr_familias[5]['marca_nome'] 	= "Conteudos livres";
	$arr_familias[6]['brand_id'] = 7;	$arr_familias[6]['marca_nome'] 	= "Texto Livre";
	$arr_familias[7]['brand_id'] = 8;	$arr_familias[7]['marca_nome'] 	= "Conteudos lado a lado";
	$arr_familias[8]['brand_id'] = 9;	$arr_familias[8]['marca_nome'] 	= "Destaques tipo Blogue";
	$arr_familias[9]['brand_id'] = 10; 	$arr_familias[9]['marca_nome'] 	= "Caminho";
	$arr_familias[10]['brand_id'] = 11; $arr_familias[10]['marca_nome'] = "Galeria";
	$arr_familias[11]['brand_id'] = 12; $arr_familias[11]['marca_nome'] = "3 Textos lado a lado";
	$arr_familias[12]['brand_id'] = 13; $arr_familias[12]['marca_nome'] = "Galeria2 imagens";
	$arr_familias[13]['brand_id'] = 14; $arr_familias[13]['marca_nome'] = "Galeria3";
	$arr_familias[14]['brand_id'] = 15; $arr_familias[14]['marca_nome'] = "Accordion Group";
	$arr_familias[15]['brand_id'] = 16; $arr_familias[15]['marca_nome'] = "Google Maps";
	$arr_familias[16]['brand_id'] = 17; $arr_familias[16]['marca_nome'] = "Acordião";
	$arr_familias[17]['brand_id'] = 18; $arr_familias[17]['marca_nome'] = "";
	$arr_familias[18]['brand_id'] = 19; $arr_familias[18]['marca_nome'] = "Apoios";
	$arr_familias[19]['brand_id'] = 20; $arr_familias[19]['marca_nome'] = "";
	$arr_familias[20]['brand_id'] = 21; $arr_familias[20]['marca_nome'] = "";
	$arr_familias[21]['brand_id'] = 22; $arr_familias[21]['marca_nome'] = "Organização";
?>