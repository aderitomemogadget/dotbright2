<?php
	$path = dirname(__FILE__) . '/../../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';

	define('FILE_PATH', '../../../files/importar/');

	$rs = new DataObj;
	$sql = 'SELECT * FROM a_importador_configuracoes WHERE conf_id='.$Supplier_ID;
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$key_field = $rs->db_data['key_field']; //Vai obter o campo chave do produto que sera usado para as importacoes
	}
	$rs->db_close();

	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	if($key_field==1)
	{
		$arr_campos_importar[] = array('CAMPO' => 'Codigo_produto', 	'OBRIGATORIO' => 1, 'INFO' => 'Referência Produto');
		$arr_campos_importar[] = array('CAMPO' => 'EAN', 				'OBRIGATORIO' => 1, 'INFO' => '');
	}
	else
	{
		$arr_campos_importar[] = array('CAMPO' => 'Codigo_produto', 	'OBRIGATORIO' => 1, 'INFO' => 'Referência Produto');
		$arr_campos_importar[] = array('CAMPO' => 'EAN', 				'OBRIGATORIO' => 0, 'INFO' => '');
	}

	$arr_campos_importar[] = array('CAMPO' => 'Marca', 				'OBRIGATORIO' => 0, 'INFO' => 'Código Marca');
	$arr_campos_importar[] = array('CAMPO' => 'MarcaDesc',			'OBRIGATORIO' => 0, 'INFO' => 'Nome Marca');
	$arr_campos_importar[] = array('CAMPO' => 'Categoria',			'OBRIGATORIO' => 0, 'INFO' => 'Código Categ. Nivel 1');
	$arr_campos_importar[] = array('CAMPO' => 'CategoriaDesc',		'OBRIGATORIO' => 0, 'INFO' => 'Nome Categ. Nivel 1');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_Categoria', 		'OBRIGATORIO' => 0, 'INFO' => 'Cód. Sub Categ. Nivel 2');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_CategoriaDesc', 	'OBRIGATORIO' => 0, 'INFO' => 'Nome Sub Categ. Nivel 2');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_Categoria2',		'OBRIGATORIO' => 0, 'INFO' => 'Cód. Sub Categ. Nivel 3');
	$arr_campos_importar[] = array('CAMPO' => 'Sub_Categoria2Desc',	'OBRIGATORIO' => 0, 'INFO' => 'Nome Sub Categ. Nivel 3');
	$arr_campos_importar[] = array('CAMPO' => 'Produto',  			'OBRIGATORIO' => 0, 'INFO' => 'Nome do Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Descricao', 			'OBRIGATORIO' => 0, 'INFO' => 'Resumo do Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Descricao2', 		'OBRIGATORIO' => 0, 'INFO' => 'Caract. do Produto');
	$arr_campos_importar[] = array('CAMPO' => 'Classe_energetica', 	'OBRIGATORIO' => 0, 'INFO' => 'Classe energetica');
	$arr_campos_importar[] = array('CAMPO' => 'Link_Classe_Energetica', 	'OBRIGATORIO' => 0, 'INFO' => 'Link Classe Energetica');
	$arr_campos_importar[] = array('CAMPO' => 'Preco_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço');
	$arr_campos_importar[] = array('CAMPO' => 'Preco2_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço2');
	$arr_campos_importar[] = array('CAMPO' => 'Preco3_IVA',			'OBRIGATORIO' => 0, 'INFO' => 'Preço3');
	$arr_campos_importar[] = array('CAMPO' => 'Stock', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem1',  			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem2', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem3', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem4', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Peso', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'fora_gama', 			'OBRIGATORIO' => 0, 'INFO' => 'Fora Gama');