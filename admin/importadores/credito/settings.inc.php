<?php
	
	$path = dirname(__FILE__) . '/../../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';
	
	// CAMINHO DO FICHEIRO APÓS IMPORTAÇÃO
	define('FILE_PATH', '../../../files/importar/');
	
	$Supplier_ID=4;
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




	function addtableorfields($table='',$addcols=''){


	if($table == 'help'){
		echo "function arguments: addtableorfields(	&#x0024;table='',	&#x0024;addcols='')<br>";
		echo '&#x0024;table is the tablename<br>';
		echo '&#x0024;addcols is an array, follow this example<br>';
		echo "&#x0024;addcols = array(array('name'=>'ID_PAI','type'=>'INT(6)'),<br>
		      array('name'=>'imge_ext','type'=>'VARCHAR(6) NOT NULL'),<br>
		      array('name'=>'imgd_ext','type'=>'VARCHAR(6) NOT NULL')<br>
		     );<br><br>";
		die('end addtableorfields');

	}

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

 /* Buscar tabela -- se não existe -- adicionar*/
	$sql = "SELECT *
	FROM information_schema.tables
	WHERE table_schema = '".$DB_name."'
	    AND table_name = '".$table."'
	LIMIT 1";



	$rs = new DataObj;
	$rs->db_open($db_link, $DB_name, $sql);
	if ($rs->EOF()) // adiciona a tabela e os campos
	{
		$sql = 'CREATE TABLE '.$table.' (
				ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
				)';
		Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();

 //Adicionar campos a tabela
	$fields=db_list_fields($DB_name,$table,$db_link);
	$columns=db_num_fields($fields);
	$tv = sizeof($addcols);
	for($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
		foreach ($addcols as $col) {
			if(!in_array($col,$field_array))
				{
					$sql = 'ALTER TABLE '.$table.' ADD '.$col['name'].' '.$col['type'].' NOT NULL ';
					$res=Runquery($db_link,$DB_name,$sql);
				}
		}
		
}
