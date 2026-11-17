<?php
	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	define('FILE_PATH', '../../../files/importar/');

	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	$arr_campos_importar[0] = array('CAMPO' => 'Marca', 				'OBRIGATORIO' => 0, 'INFO' => 'Código Marca');
	$arr_campos_importar[1] = array('CAMPO' => 'MarcaDesc',			'OBRIGATORIO' => 0, 'INFO' => 'Nome Marca');
	$arr_campos_importar[2] = array('CAMPO' => 'Categoria',			'OBRIGATORIO' => 0, 'INFO' => 'Código Categ. Nivel 1');
	$arr_campos_importar[3] = array('CAMPO' => 'CategoriaDesc',		'OBRIGATORIO' => 0, 'INFO' => 'Nome Categ. Nivel 1');
	$arr_campos_importar[4] = array('CAMPO' => 'Sub_Categoria', 		'OBRIGATORIO' => 0, 'INFO' => 'Cód. Sub Categ. Nivel 2');
	$arr_campos_importar[5] = array('CAMPO' => 'Sub_CategoriaDesc', 	'OBRIGATORIO' => 0, 'INFO' => 'Nome Sub Categ. Nivel 2');
	$arr_campos_importar[6] = array('CAMPO' => 'Sub_Categoria2',		'OBRIGATORIO' => 0, 'INFO' => 'Cód. Sub Categ. Nivel 3');
	$arr_campos_importar[7] = array('CAMPO' => 'Sub_Categoria2Desc',	'OBRIGATORIO' => 0, 'INFO' => 'Nome Sub Categ. Nivel 3');
	$arr_campos_importar[8] = array('CAMPO' => 'Codigo_produto', 	'OBRIGATORIO' => 1, 'INFO' => 'Referência Produto');
	$arr_campos_importar[9] = array('CAMPO' => 'Produto',  			'OBRIGATORIO' => 0, 'INFO' => 'Nome do Produto');
	$arr_campos_importar[10] = array('CAMPO' => 'Descricao', 			'OBRIGATORIO' => 0, 'INFO' => 'Resumo do Produto');
	$arr_campos_importar[11] = array('CAMPO' => 'Descricao2', 		'OBRIGATORIO' => 0, 'INFO' => 'Caract. do Produto');
	$arr_campos_importar[12] = array('CAMPO' => 'Classe_energetica', 	'OBRIGATORIO' => 0, 'INFO' => 'Classe energetica');
	$arr_campos_importar[13] = array('CAMPO' => 'Link_Classe_Energetica', 	'OBRIGATORIO' => 0, 'INFO' => 'Link Classe Energetica');
	$arr_campos_importar[14] = array('CAMPO' => 'Preco_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço');
	$arr_campos_importar[14] = array('CAMPO' => 'Preco2_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço2');
	$arr_campos_importar[14] = array('CAMPO' => 'Preco3_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço3');
	$arr_campos_importar[15] = array('CAMPO' => 'EAN', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[16] = array('CAMPO' => 'Stock', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[17] = array('CAMPO' => 'Imagem1',  			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[18] = array('CAMPO' => 'Imagem2', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[19] = array('CAMPO' => 'Imagem3', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[20] = array('CAMPO' => 'Imagem4', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[21] = array('CAMPO' => 'Peso', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[22] = array('CAMPO' => 'fora_gama', 			'OBRIGATORIO' => 0, 'INFO' => 'Fora Gama');