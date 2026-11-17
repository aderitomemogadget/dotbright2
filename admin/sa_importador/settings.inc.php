<?php
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';
	
	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	define('FILE_PATH', '../../files/importar/');
	
// $arr_campos_importar[] = array('CAMPO' => 'Tipo - <span style="font-weight:bold;color:#FF0000">1</span>', 'OBRIGATORIO' => 0);
	
	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	$arr_campos_importar[] = array('CAMPO' => 'Marca', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Categoria',			'OBRIGATORIO' => 1, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Sub Categoria', 		'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Sub Categoria 2',	'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Tipo', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Tipo', 				'OBRIGATORIO' => 0, 'INFO' => ' - <span style="font-weight:bold;color:#FF0000">1</span>');
	$arr_campos_importar[] = array('CAMPO' => 'Código do produto', 	'OBRIGATORIO' => 1, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Produto',  			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Descrição', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Medidas', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Preço SITE com IVA',	'OBRIGATORIO' => 1, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'EAN', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Stock', 				'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem 1',  			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem 2', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem 3', 			'OBRIGATORIO' => 0, 'INFO' => '');
	$arr_campos_importar[] = array('CAMPO' => 'Imagem 4', 			'OBRIGATORIO' => 0, 'INFO' => '');