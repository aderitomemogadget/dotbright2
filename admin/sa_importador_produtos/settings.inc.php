<?php
	$path = dirname(__FILE__) . '/../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';

	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	define('FILE_PATH', '../../files/importar/');

// $arr_campos_importar[] = array('CAMPO' => 'Tipo - <span style="font-weight:bold;color:#FF0000">1</span>', 'OBRIGATORIO' => 0);

	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	$arr_campos_importar[] = array('CAMPO' => 'Marca', 				'OBRIGATORIO' => 0, 'INFO' => 'Código Marca');
	$arr_campos_importar[] = array('CAMPO' => 'MarcaDesc',			'OBRIGATORIO' => 0, 'INFO' => 'Nome Marca');
	$arr_campos_importar[] = array('CAMPO' => 'Categoria',			'OBRIGATORIO' => 0, 'INFO' => 'Código Categ. Nivel 1');
	$arr_campos_importar[] = array('CAMPO' => 'CategoriaDesc',		'OBRIGATORIO' => 0, 'INFO' => 'Nome Categ. Nivel 1');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_Categoria', 		'OBRIGATORIO' => 0, 'INFO' => 'Cód. Sub Categ. Nivel 2');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_CategoriaDesc', 	'OBRIGATORIO' => 0, 'INFO' => 'Nome Sub Categ. Nivel 2');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_Categoria2',		'OBRIGATORIO' => 0, 'INFO' => 'Cód. Sub Categ. Nivel 3');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_Categoria2Desc',	'OBRIGATORIO' => 0, 'INFO' => 'Nome Sub Categ. Nivel 3');
	$arr_campos_importar[] = array('CAMPO' => 'Codigo_produto', 	'OBRIGATORIO' => 1, 'INFO' => 'Referência Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Produto',  			'OBRIGATORIO' => 0, 'INFO' => 'Nome do Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Descricao', 			'OBRIGATORIO' => 0, 'INFO' => 'Resumo do Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Descricao2', 		'OBRIGATORIO' => 0, 'INFO' => 'Caract. do Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Classe_energetica', 	'OBRIGATORIO' => 0, 'INFO' => 'Classe energetica');
	$arr_campos_importar[] = array('CAMPO' => 'Link_Classe_Energetica', 	'OBRIGATORIO' => 0, 'INFO' => 'Link Classe Energetica');
	$arr_campos_importar[] = array('CAMPO' => 'Preco_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço');
	$arr_campos_importar[] = array('CAMPO' => 'EAN', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Stock', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem1',  			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem2', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem3', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem4', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Peso', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'fora_gama', 			'OBRIGATORIO' => 0, 'INFO' => 'Fora Gama');