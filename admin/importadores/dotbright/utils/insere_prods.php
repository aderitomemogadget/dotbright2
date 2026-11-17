<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = dirname( __FILE__ ) . '/../../../../core/';
	require_once $path . 'core_files.php';
	require_once dirname( __FILE__ ).'/../../../../admin/products/funcoesfiltros.inc.php';
	require_once dirname( __FILE__ ).'/../../../../admin/products/filtros/funcoes.inc.php';

	error_reporting(0);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors',-1);

	set_time_limit(0);
	ini_set("max_execution_time","3600000");
	ini_set("memory_limit","-1");
	ini_set("post_max_size","8M");
	ini_set("upload_max_filesize","8M");



	$familia_novas = 0; // TODAS AS FAMILIAS NOVAS VÃO PARA ESTAS
	$category_id_import = 0;

	$linguas_importa[] = 'pt';

	$estados_stock['Sem_Stock'] = 0;
	$estados_stock['Por_encomenda'] = 1;
	$estados_stock['Brevemente'] = 0;
	$estados_stock['Imediata'] = 3;


	$campos = array("Ref","Ref_Fabricante","Designacao","Preco_Final","Stock","Unidade_Negocio","Linha_Produto","Familia","Sub-Familia","Categoria","Marca","Data_modificacao","Descricao","Imagem","URL_Imagem","URL_Imagem2","URL_Imagem3","URL_Imagem4","URL_Imagem5","URL_Imagem6","URL_Imagem7","URL_Imagem8","URL_Imagem9","URL_Imagem10","ean","Preco_Unitario","Taxa");


	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj; $rs2 = new DataObj;


	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'products',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('fornecedor_importacao',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD fornecedor_importacao varchar(255) NOT NULL');



	$Supplier_ID=3;//dotbright

	define('FORNECEDOR_IMPORTACAO', 'dotbright');
	define('FAMILIA_TEMP', 14);






	$separator = ';'; $separator2 = '';


	$file_path = dirname( __FILE__ )."/../../../../files/importar/dotbright_feed_export.txt";
	$file_path_done = dirname( __FILE__ )."/../../../../files/importado/dotbright_feed_export.txt";


	$myFile = dirname( __FILE__ )."/../../../../files/importar/atualizar_dotbright.txt";
	$fh = fopen($myFile, 'a+');
	fwrite($fh, "ON\n");
	chmod($myFile, 0755);

	// PARA SABER O TOTAL DE LINHAS
	$total_produtos = 0;
	$fp = fopen($file_path, "r");


	//FASE 1

	/*while (($line = fgetcsv($fp)) !== FALSE) {	  $total_produtos ++;	}*/
	$sql='UPDATE products SET Flag=0 WHERE fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'"';
	$res=Runquery($db_link, $DB_name, $sql);
	$linha = 1;
	echo $file_path;

	while(($data = fgetcsv($fp,'',';')) !== FALSE)
	{
		echo "*";
		if($linha==1)
		{
			$names = array();
			foreach($data as $field) $names[] = trim($field);
			if(empty($names)){ log_error('Não foi especificadas nome das colunas'); exit; }
		}
		else
		{
			$Ref = ''; $Ref_Fabricante = ''; $Designacao = ''; $Preco_Final = ''; $Stock2 = $Stock = ''; $Unidade_Negocio = ''; $Linha_Produto = ''; $Familia = ''; $Sub_Familia = '';  $Categoria = '';
			$Marca = ''; $Data_modificacao = ''; $Imagem = ''; $ean = ''; $Preco_Unitario = ''; $Taxa = '';
			$URL_Imagem = '';
			$URL_Imagem2 = '';
			$URL_Imagem3 = '';
			$URL_Imagem4 = '';
			$URL_Imagem5 = '';
			$URL_Imagem6 = '';
			$URL_Imagem7 = '';
			$URL_Imagem8 = '';
			$URL_Imagem9 = '';
			$URL_Imagem10 = '';
			if(in_array('Ref', $names))
			{
				$Ref = devolvevalor($data,"Ref");

				$sql = 'UPDATE products SET Flag=1, fornecedor_importacao = "'.FORNECEDOR_IMPORTACAO.'" WHERE Ref = "'.$Ref.'"';
				$res = Runquery($db_link, $DB_name, $sql);

				echo $sql."\n";
			}
		}
		$total_produtos ++;
		$linha++;
	}
	fclose($fp);

	$fp = fopen($file_path, "r");



	$sql='UPDATE products SET Aproved=1 WHERE fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" AND Flag=1';
	$res=Runquery($db_link, $DB_name, $sql);

	$sql='UPDATE products SET Aproved=0 WHERE fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" AND Flag=0';
	$res=Runquery($db_link, $DB_name, $sql);

	foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p*.php") as $filename)
	{
		$fl = explode('_p',$filename);
		$prod_id = str_replace(array('_en.php', '_pt.php', '_es.php', '_de.php', '_fr.php'), '', $fl[1]);
		$sql = 'SELECT products.ID FROM products WHERE products.ID="'.$prod_id.'" AND Aproved=1';
		$rs->db_open($db_link, $DB_name, $sql);
		if($rs->EOF())
		{
			@unlink($filename);
		}
		$rs->db_close();
	}

	echo 'FASE 2';



	//FASE 2:



	// if($total_produtos > 0)
	// {
	// }

	//fclose($fp);
	echo $total_produtos;

	/*$fp = fopen($file_path, "r");
	if($fp == null || ($data = fgetcsv($fp, filesize($file_path), $separator, $separator2)) === false)
	{
		// print_r($data);
		log_error('Ocorreu um erro na leitura do ficheiro');
		exit;
	}
	*/
	// $total_produtos = count($data);
	/*$names = array();
	foreach($data as $field) $names[] = trim($field);
	// print_r($names);

	if(empty($names)){ log_error('Não foi especificadas nome das colunas'); exit; }
	*/
	$linha = 1;

	$myFile2 = "/home/www/on4tech.pt/public_html/files/produtos_precos_errados.csv";
	$fh2 = fopen($myFile2, 'a+');

	$total_produtos2 = 1;
	//while($data = fgetcsv($fp, filesize($file_path), $separator, $separator2))
	while(($data = fgetcsv($fp,'',';')) !== FALSE)
	{
		if($linha==1)
		{
			/*print_r($data);*/
			$names = array();
			foreach($data as $field) $names[] = trim($field);
			if(empty($names)){ log_error('Não foi especificadas nome das colunas'); exit; }
		}
		else
		{
			/*print_r($data);*/
			$Ref = ''; $Ref_Fabricante = ''; $Designacao = ''; $Preco_Final = ''; $Stock2 = $Stock = ''; $Unidade_Negocio = ''; $Linha_Produto = ''; $Familia = ''; $Sub_Familia = '';  $Categoria = '';
			$Marca = ''; $Data_modificacao = ''; $Imagem = ''; $ean = ''; $Preco_Unitario = ''; $Taxa = '';

			$URL_Imagem = '';
			$URL_Imagem2 = '';
			$URL_Imagem3 = '';
			$URL_Imagem4 = '';
			$URL_Imagem5 = '';
			$URL_Imagem6 = '';
			$URL_Imagem7 = '';
			$URL_Imagem8 = '';
			$URL_Imagem9 = '';
			$URL_Imagem10 = '';

			if(in_array('Ref', $names)){ $Ref = devolvevalor($data,"Ref");}






			$data_tmp=$data;
			$data_tmp[12]='';
			$data_tmp[14]='';
			//Temp file
			/*$tmp_file = $site_shell_url.'files/tmp/prod_db_'.base64_encode($Ref).'.tmp';
			$last_file = $site_shell_url.'files/tmp/prod_db_'.base64_encode($Ref).'.last';
			file_put_contents($tmp_file, var_export($data_tmp, TRUE));*/
			$equal=0;
			/*if(file_exists($last_file))
			{
				//Vamos comparar os 2
				if(md5_file($tmp_file) === md5_file($last_file))
				{
					$equal=1;
				}
				else file_put_contents($last_file, var_export($data_tmp, TRUE));// VOU ATUALIZAR O LAST FILE COM A INFORMAÇAO DE HOJE
			}
			else file_put_contents($last_file, var_export($data_tmp, TRUE));*/







			if(in_array("Ref_Fabricante", $names)){ $Ref_Fabricante = devolvevalor($data,"Ref_Fabricante"); }
			if(in_array("Designacao", $names)){ $Designacao = devolvevalor($data,"Designacao"); }
			if(in_array("Preco_Final", $names)){ $Preco_Final = devolvevalor($data,"Preco_Final"); }
			if(in_array("Stock", $names)){ $Stock = $Stock2 = devolvevalor($data, "Stock"); }
			if(in_array("Unidade_Negocio", $names)){ $Unidade_Negocio = devolvevalor($data,"Unidade_Negocio"); }
			if(in_array("Linha_Produto", $names)){ $Linha_Produto = devolvevalor($data,"Linha_Produto"); }
			if(in_array("Familia", $names)){ $Familia = devolvevalor($data,"Familia"); }
			if(in_array("Sub-Familia", $names)){ $Sub_Familia = devolvevalor($data,"Sub-Familia"); }
			if(in_array("Categoria", $names)){ $Categoria = devolvevalor($data,"Categoria"); }
			if(in_array("Marca", $names)){ $Marca = devolvevalor($data,"Marca"); }
			if(in_array("Data_modificacao", $names)){ $Data_modificacao = devolvevalor($data,"Data_modificacao"); }
			if(in_array("Descricao", $names)){ $Descricao = devolvevalor($data,"Descricao"); }
			if(in_array("Imagem", $names)){ $Imagem = devolvevalor($data,"Imagem"); }
			if(in_array("URL_Imagem", $names)){ $URL_Imagem = devolvevalor($data,"URL_Imagem"); }
			if(in_array("URL_Imagem2", $names)){ $URL_Imagem2 = devolvevalor($data,"URL_Imagem2"); }
			if(in_array("URL_Imagem3", $names)){ $URL_Imagem3 = devolvevalor($data,"URL_Imagem3"); }
			if(in_array("URL_Imagem4", $names)){ $URL_Imagem4 = devolvevalor($data,"URL_Imagem4"); }
			if(in_array("URL_Imagem5", $names)){ $URL_Imagem5 = devolvevalor($data,"URL_Imagem5"); }
			if(in_array("URL_Imagem6", $names)){ $URL_Imagem6 = devolvevalor($data,"URL_Imagem6"); }
			if(in_array("URL_Imagem7", $names)){ $URL_Imagem7 = devolvevalor($data,"URL_Imagem7"); }
			if(in_array("URL_Imagem8", $names)){ $URL_Imagem8 = devolvevalor($data,"URL_Imagem8"); }
			if(in_array("URL_Imagem9", $names)){ $URL_Imagem9 = devolvevalor($data,"URL_Imagem9"); }
			if(in_array("URL_Imagem20", $names)){ $URL_Imagem10 = devolvevalor($data,"URL_Imagem10"); }
			if(in_array("ean", $names)){ $ean = devolvevalor($data,"ean"); }
			if(in_array("Preco_Unitario", $names)){ $Preco_Unitario = devolvevalor($data,"Preco_Unitario"); }
			if(in_array("Taxa", $names)){ $Taxa = devolvevalor($data,"Taxa"); }

			//echo $Ref."\n";
			$Preco_Unitario_reven=0; $Preco_Unitario_reven=$Taxa;
			// $Preco_Unitario=$Preco_Unitario*1.23;
			// $Preco_Final=$Preco_Final*1.23;

			// tem que ter ref e o stock tem que ser imediato (3)
			// if($Ref!='' && $Stock2 == 3)
			//

			//if($Ref!='' && $Stock2 > 0 && $Preco_Unitario_reven < $Preco_Final)
			if($Ref!='' && $Stock2 > 0 && $Preco_Final > 0)
			{
				/* FAMILIAS */
				// LINGUAS A IMPORTAR
				$fam_id_p = 0; $fam_id = 0; $subfam_id = 0; $subfam_id2 = 0; $prod_fam_id = 0;



				/* $Titulos_fam_p[] = array('titulo' => $Unidade_Negocio, 'descricao' => '', 'lingua' => 'pt');
				$prod_fam_p = detect_family($Unidade_Negocio, $Titulos_fam_p, $familia_novas, $Unidade_Negocio);
				if($prod_fam_p['fam_id'] > 0)
				{

					unset($Titulos_fam_p); $Titulos_fam_p = array();
					$Titulos_fam[] = array('titulo' => $Linha_Produto, 'descricao' => '', 'lingua' => 'pt');

					$fam_id_p = $prod_fam_p['fam_id'];
					$prod_fam = detect_family($Linha_Produto, $Titulos_fam, $fam_id_p, $Unidade_Negocio.'_'.$Linha_Produto);
					if($prod_fam['fam_id'] > 0)
					{
						unset($Titulos_fam); $Titulos_fam = array();
						$Titulos_fam2[] = array('titulo' => $Familia, 'descricao' => '', 'lingua' => 'pt');

						$fam_id = $prod_fam['fam_id'];
						$prod_subfam = detect_family($Familia, $Titulos_fam2, $fam_id, $Unidade_Negocio.'_'.$Linha_Produto.'_'.$Familia);
						if($prod_subfam['fam_id'] > 0)
						{
							unset($Titulos_fam2); $Titulos_fam2 = array();
							$Titulos_fam3[] = array('titulo' => $Sub_Familia, 'descricao' => '', 'lingua' => 'pt');

							$subfam_id = $prod_subfam['fam_id'];
							$prod_subfam2 = detect_family($Sub_Familia, $Titulos_fam3, $subfam_id, $Unidade_Negocio.'_'.$Linha_Produto.'_'.$Familia.'_'.$Sub_Familia);
							$subfam_id2 = $prod_subfam2['fam_id'];

							unset($Titulos_fam3);$Titulos_fam3 = array();
						}
					}
				}

				if($subfam_id2 > 0) $prod_fam_id = $subfam_id2; elseif($subfam_id > 0) $prod_fam_id = $subfam_id; else $prod_fam_id = $fam_id;*/

				$prod_fam_id = 0;
				/* MARCAS */
				$marca_id = 0;
				if($Marca != '')
				{
					$prod_marca = detect_brand($Marca);
					$marca_id = $prod_marca['marca_id'];
				}

				/* PRODUTOS */
				$t_pt = '';$d_pt = '';
				/*if($Descricao!='') $Titulos[] = array('titulo' => $Designacao, 'descricao' => base64_decode($Descricao), 'lingua' => 'pt');
				else $Titulos[] = array('titulo' => $Designacao, 'lingua' => 'pt');*/

				if($Descricao!='') $Titulos = array('titulo' => $Designacao, 'descricao' => base64_decode($Descricao), 'lingua' => 'pt');
				else $Titulos = array('titulo' => $Designacao, 'lingua' => 'pt');

				/*echo "antes".$Ref;*/

				$prod_info = detect_product($prod_fam_id, $marca_id, $Ref, $ean, $Titulos);


				if($equal)
				{
					//Não vamos fazer mais nada porque este produto nao sofreu alteracoes mas vamos alterar na mesma a flag
					$sql='UPDATE products SET Flag=1,udtime="'.date("Y-m-d H:m:s").'" WHERE ID="'.$prod_info['prod_id'].'"';
					$res=Runquery($db_link, $DB_name, $sql);
				}
				elseif($prod_info['prod_id'] > 0)
				{
					$sql='UPDATE products SET Flag=1,udtime="'.date("Y-m-d H:m:s").'" WHERE ID="'.$prod_info['prod_id'].'"';
echo $sql."\n";
					$res=Runquery($db_link, $DB_name, $sql);

					unset($Titulos);$Titulos = array();
					detect_famprod($prod_info['prod_id'], $prod_info['prod_fam_id']);

					//update_products_precos($prod_info['prod_id'], $val_p, $prod_QtdMinEnc, $Stock2, $prod_Referencia, 'EAN', 0, ($Stock2 !='' ? 1 : 0),$Supplier_ID,$iva_default);
					$PRECO_PRODUTO=$Preco_Final;
					$STOCK_PRODUTO=$Stock2;
					$CODIGO_PRODUTO=$Ref_Fabricante;
					$EAN_PRODUTO='';
					update_products_precos($prod_info['prod_id'],$PRECO_PRODUTO, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock2 !='' ? 1 : 0),$Supplier_ID,$iva_default);
					//detect_products_objs($prod_info['prod_id'], 0, $Stock2, $Preco_Final, 0);
					detect_products_objs($prod_info['prod_id'], 0, $Stock2, $Preco_Unitario_reven, 2); // este é o preço de revenda, podemos deixar atualizar normalmente
					detect_filter_products($prod_info['prod_id'], $prod_info['prod_fam_id'], $marca_id, 0, 0);


					if($URL_Imagem !='')
					{
						$ext = pathinfo($URL_Imagem, PATHINFO_EXTENSION);
						$url_new = dirname( __FILE__ ).'/../../../../files/products/'.$prod_info['prod_id'].'_1.'.$ext;

						if(!file_exists($url_new))
						{
							copy($URL_Imagem, $url_new);
							$res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
							$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem=1');
							$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",1)');
						}
					}
					if($URL_Imagem2 !='')
					{
						$ext = pathinfo($URL_Imagem2, PATHINFO_EXTENSION);
						$url_new = dirname( __FILE__ ).'/../../../../files/products/'.$prod_info['prod_id'].'_2.'.$ext;
						if(!file_exists($url_new))
						{
							copy($URL_Imagem2, $url_new);
							$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem=2');
							$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",2)');
						}
					}

					if($URL_Imagem3 !='')
					{
						$ext = pathinfo($URL_Imagem2, PATHINFO_EXTENSION);
						$url_new = dirname( __FILE__ ).'/../../../../files/products/'.$prod_info['prod_id'].'_3.'.$ext;
						if(!file_exists($url_new))
						{
							copy($URL_Imagem3, $url_new);
							$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem=3');
							$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",3)');
						}
					}

					if($URL_Imagem4 !='')
					{
						$ext = pathinfo($URL_Imagem2, PATHINFO_EXTENSION);
						$url_new = dirname( __FILE__ ).'/../../../../files/products/'.$prod_info['prod_id'].'_4.'.$ext;
						if(!file_exists($url_new))
						{
							copy($URL_Imagem4, $url_new);
							$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem=4');
							$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",4)');
						}
					}

					if($URL_Imagem5 !='')
					{
						$ext = pathinfo($URL_Imagem2, PATHINFO_EXTENSION);
						$url_new = dirname( __FILE__ ).'/../../../../files/products/'.$prod_info['prod_id'].'_5.'.$ext;
						if(!file_exists($url_new))
						{
							copy($URL_Imagem5, $url_new);
							$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem=5');
							$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",5)');
						}
					}


					/*if($prod_info['prod_fam_id'] > 0 && $marca_id > 0)
					{
						gera_produtos($prod_info['prod_id'], $prod_info['prod_fam_id'], $marca_id, 1, 'pt');
					}*/

				}

				echo $Ref.'|';

				fwrite($fh, $Ref." | ");
				if(!file_exists($myFile)) die();
			}
			else
			{
				fwrite($fh2,"$Ref;$Preco_Unitario_reven;$Preco_Final\n");

				// $log_error .= "LINHA ".$linha.", Um dos seguintes campos não existem: REFERENCIA(".$REFERENCIA.")\n";
			}
			echo $total_produtos2."\n";
			$total_produtos2++;
		}

		$linha++;
	}

	fclose($fh2);


	if(!file_exists($myFile)) die();


	$sql='UPDATE products SET Aproved=1 WHERE fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" AND Flag=1';
	$res=Runquery($db_link, $DB_name, $sql);

	/*$sql='UPDATE products SET Aproved=0 WHERE fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" AND Flag=0';
	$res=Runquery($db_link, $DB_name, $sql);

	foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p*.php") as $filename)
	{
		$fl = explode('_p',$filename);
		$prod_id = str_replace(array('_en.php', '_pt.php', '_es.php', '_de.php', '_fr.php'), '', $fl[1]);

		$sql = 'SELECT products.ID FROM products WHERE products.ID="'.$prod_id.'" AND Aproved=1';
		$rs->db_open($db_link, $DB_name, $sql);
		if($rs->EOF())
		{
			@unlink($filename);
		}
		$rs->db_close();
	}*/
	if(!file_exists($myFile)) die();

	//gera_produtos_filtros('pt');

	if(!file_exists($myFile)) die();
	fclose($fp);
	fclose($fh);

	if(isset($log_error) && $log_error != '') log_error($log_error);
	log_ok();

function log_error($txt)
{
	// if(copy($GLOBALS['file_path'], $GLOBALS['file_path_done']))
	// {
		// @unlink($GLOBALS['file_path']);
	// }
	if(copy(dirname( __FILE__ )."/../../../../files/importar/atualizar_dotbright.txt", dirname( __FILE__ )."/../../../../files/importar/atualizar_dotbright_".date('YmdHis').".txt"))
	{
		@unlink(dirname( __FILE__ )."/../../../../files/importar/atualizar_dotbright.txt");
	}
	$myFile = dirname( __FILE__ )."/../../../../files/importar/log_error_".date('YmdHis').".txt";
	$fh = fopen($myFile, 'w');
	fwrite($fh, $txt);
	fclose($fh);
}
function log_ok()
{

	/* if(copy($GLOBALS['file_path'], str_replace('.csv', date('YmdHis').'.csv', $GLOBALS['file_path_done'])))
	{
		@unlink($GLOBALS['file_path']);
	} */
	@unlink(dirname( __FILE__ )."/../../../../files/importar/atualizar_dotbright.txt");

	// $path = dirname( __FILE__ ) . '/../admin/products/filtros/run.php';
	// $exe_file = "php ".$path." > /dev/null &";
	// exec($exe_file);
	// $path = dirname( __FILE__ ) . '/../admin/products/filtros/geraprods.php';
	// $exe_file = "php ".$path." > /dev/null &";
	// exec($exe_file);
}
/* function backup_db()
{
	$table = 'products_objs';
	$res = ExecQuery($hConn,$DBName, "DROP TABLE IF EXISTS ".$table."_bk;");
	$res = ExecQuery($hConn,$DBName, "CREATE TABLE ".$table."_bk LIKE ".$table.";");
	$res = ExecQuery($hConn,$DBName, "INSERT ".$table."_bk SELECT * FROM ".$table.";");

	$table = 'products';
	$res = ExecQuery($hConn,$DBName, "DROP TABLE IF EXISTS ".$table."_bk;");
	$res = ExecQuery($hConn,$DBName, "CREATE TABLE ".$table."_bk LIKE ".$table.";");
	$res = ExecQuery($hConn,$DBName, "INSERT ".$table."_bk SELECT * FROM ".$table.";");

	$table = 'products_lang';
	$res = ExecQuery($hConn,$DBName, "DROP TABLE IF EXISTS ".$table."_bk;");
	$res = ExecQuery($hConn,$DBName, "CREATE TABLE ".$table."_bk LIKE ".$table.";");
	$res = ExecQuery($hConn,$DBName, "INSERT ".$table."_bk SELECT * FROM ".$table.";");

	$table = 'brands';
	$res = ExecQuery($hConn,$DBName, "DROP TABLE IF EXISTS ".$table."_bk;");
	$res = ExecQuery($hConn,$DBName, "CREATE TABLE ".$table."_bk LIKE ".$table.";");
	$res = ExecQuery($hConn,$DBName, "INSERT ".$table."_bk SELECT * FROM ".$table.";");

	$table = 'brands_lang';
	$res = ExecQuery($hConn,$DBName, "DROP TABLE IF EXISTS ".$table."_bk;");
	$res = ExecQuery($hConn,$DBName, "CREATE TABLE ".$table."_bk LIKE ".$table.";");
	$res = ExecQuery($hConn,$DBName, "INSERT ".$table."_bk SELECT * FROM ".$table.";");

} */
function devolvevalor($data, $campo, $tipo="")
{
	// global $names;
	$valor = $data[array_search($campo, $GLOBALS['names'])];
	if($tipo=="dt") $valor = convert_data($valor);
	elseif($tipo=="num")
	{
		$valor = str_replace("€","",$valor);
		$valor = convert_number($valor);
	}
	elseif($tipo == "perc") $valor = str_replace("%", "", $valor);

	$valor = rtrim($valor); // tira espaços no fim
	$valor = trim($valor,"'"); // tira a plica do início do texto

	if($tipo=="num" && $valor=="") $valor = 0;

	return $valor;
}
function detect_products_objs($ID_PROD=0, $obj_qtdmin=0, $obj_stock=0, $obj_pvp=0, $obj_val2=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;

	if($obj_stock==1)$obj_stock=2;

	echo "\n ID_PROD = $ID_PROD | obj_stock = $obj_stock ";

	$sql = 'SELECT obj_type_id FROM products_objs WHERE obj_type_id = "'.$ID_PROD.'" AND obj_val2="'.$obj_val2.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$sql = 'UPDATE products_objs SET obj_ativo=1, obj_pvp="'.$obj_pvp.'", obj_stock="'.$obj_stock.'", obj_qtdmin="'.$obj_qtdmin.'" WHERE obj_type_id="'.$ID_PROD.'"  AND obj_val2="'.$obj_val2.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO products_objs (obj_type, obj_type_id, obj_ativo, obj_pvp, obj_stock, obj_qtdmin, obj_val2) VALUES ("products","'.$ID_PROD.'", 1, "'.$obj_pvp.'", "'.$obj_stock.'", "'.$obj_qtdmin.'", "'.$obj_val2.'")';
		$res = Runquery($db_link, $DB_name, $sql);
		$obj_id = db_get_last_insert_id($db_link);

		for($a = 0; $a<count($GLOBALS['linguas_importa']); $a++)
		{
			$sql = 'INSERT INTO products_objs_lang (ID_PAI, Lang) VALUES ("'.$obj_id.'", "'.$GLOBALS['linguas_importa'][$a].'")';
			$res = Runquery($db_link, $DB_name, $sql);
		}
	}
	$rs->db_close();
}
function detect_famprod($ID_PROD=0, $ID_ORG=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;

	$sql = 'SELECT ID_PROD, ID_ORG FROM products_families_obj WHERE ID_PROD = "'.$ID_PROD.'" AND ID_ORG = "'.$ID_ORG.'" ';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{

	}
	else
	{
		$sql = 'INSERT INTO products_families_obj (ID_PROD, ID_ORG) VALUES ("'.$ID_PROD.'", "'.$ID_ORG.'")';
		$res = Runquery($db_link, $DB_name, $sql);


	}
	$rs->db_close(1);
}
function detect_family($XNGroupCode, $Titulos=array(), $FAMID=0, $Ref_Integracao2='')
{
	$ItemCode = trim($XNGroupCode);
	$fam_id = 0;
	if($ItemCode != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID, ID_ORG FROM products_families WHERE Ref_Integracao2="'.limpa_special($Ref_Integracao2).'"';

		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$fam_id = $rs->db_data['ID'];
			// echo $sql."\n";

			//if($FAMID > 0) $sql = 'UPDATE products_families SET visivel=1, ID_ORG="'.$FAMID.'", FLAG=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE Ref_Integracao2="'.limpa_special($Ref_Integracao2).'"';
			if($FAMID > 0) $sql = 'UPDATE products_families SET visivel=1, ID_ORG="'.$FAMID.'", FLAG=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';
			else $sql = 'UPDATE products_families SET visivel=1, FLAG=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';

			$res = Runquery($db_link, $DB_name, $sql);

			for($a=0; $a<count($Titulos); $a++)
			{
				detect_family_lang($fam_id, $Titulos[$a]['titulo'], $Titulos[$a]['lingua']);
			}
		}
		else
		{
			$sql = 'INSERT INTO products_families (ID_ORG, visivel, data_atualizacao, Ref_Integracao, Ref_Integracao2, FLAG) VALUES ("'.$FAMID.'", 1,"'.date('Y-m-d H:i:s').'", "'.limpa_special($Titulos[0]['titulo']).'", "'.limpa_special($Ref_Integracao2).'", 1)';
			$res = Runquery($db_link, $DB_name, $sql);
			$fam_id = db_get_last_insert_id($db_link);
			if($fam_id > 0)
			{
				for($a=0; $a<count($Titulos); $a++)
				{
					detect_family_lang($fam_id, $Titulos[$a]['titulo'], $Titulos[$a]['lingua']);
				}
			}
		}
		$rs->db_close(1);

		return array('fam_id' => $fam_id);
	}
	return array();
}

function detect_family_lang($fam_id, $XNGroupDesc, $lingua)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;

	$sql = 'SELECT ID FROM products_families_lang WHERE ID_PAI = "'.$fam_id.'" AND Lang="'.$lingua.'"';
	// echo $sql.'<br>';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$sql = 'UPDATE LOW_PRIORITY products_families_lang SET Cabecalho="'.addslashes($XNGroupDesc).'" WHERE ID_PAI="'.$fam_id .'" and Lang="'.$lingua.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'", "'.addslashes($XNGroupDesc).'", "'.$lingua.'")';
		// echo $sql.'<br>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close(1);
}

function detect_brand($brand='')
{
	$ItemCode = trim($brand);
	$marca_id = 0;
	if($ItemCode != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID FROM brands WHERE ref_integracao = "'.addslashes(limpa_special($ItemCode)).'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$marca_id = $rs->db_data['ID'];

			$sql = 'UPDATE brands SET data_atualizacao="'.date('Y-m-d H:i:s').'", FLAG=1 WHERE ref_integracao = "'.addslashes(limpa_special($ItemCode)).'"';
			$res = Runquery($db_link, $DB_name, $sql);

			detect_brand_lang($marca_id, $ItemCode, 'pt');
			detect_brand_lang($marca_id, $ItemCode, 'en');
		}
		else
		{
			$sql = 'INSERT INTO brands (visivel, ref_integracao, data_atualizacao, FLAG) VALUES (1, "'.addslashes(limpa_special($ItemCode)).'", "'.date('Y-m-d H:i:s').'", 1)';
			$res = Runquery($db_link, $DB_name, $sql);
			$marca_id = db_get_last_insert_id($db_link);
			if($marca_id > 0)
			{
				detect_brand_lang($marca_id, $ItemCode, 'pt');
				detect_brand_lang($marca_id, $ItemCode, 'en');
			}
		}
		$rs->db_close(1);

		return array('marca_id' => $marca_id);
	}
	return array();
}

function detect_brand_lang($marca_id, $ItemCode, $lingua)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;

	$sql = 'SELECT ID FROM brands_lang WHERE ID_PAI = "'.$marca_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$sql = 'UPDATE LOW_PRIORITY brands_lang SET Nome="'.addslashes($ItemCode).'" WHERE ID_PAI="'.$marca_id .'" and Lang="'.$lingua.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO brands_lang (ID_PAI, Nome, Lang) VALUES ("'.$marca_id.'", "'.addslashes($ItemCode).'", "'.$lingua.'")';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close(1);
}

function detect_product($prod_fam_id, $marca_id, $ItemCode, $EAN, $Titulos=array())
{

	$prod_id = 0;
	if($ItemCode != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		//$sql = 'SELECT ID, ID_ORG FROM products WHERE (Ref = "'.$ItemCode.'" AND fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'")';
		$sql = 'SELECT ID, ID_ORG FROM products WHERE (Ref = "'.$ItemCode.'")';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$prod_id = $rs->db_data['ID'];
			$prod_fam_id = $rs->db_data['ID_ORG'];

			// $sql = 'UPDATE products SET ID_ORG="'.$prod_fam_id.'", ID_Marca="'.$marca_id.'", Ref_Ean="'.$EAN.'", Carrinho=1, FLAG=1 WHERE Ref = "'.$ItemCode.'"';
			// $sql = 'UPDATE products SET ID_Marca="'.$marca_id.'", Ref_Ean="'.$EAN.'", Carrinho=1, Flag=1, Aproved=1, fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" WHERE Ref = "'.$ItemCode.'" AND fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'"';
			//$sql = 'UPDATE products SET ID_Marca="'.$marca_id.'", Ref_Ean="'.$EAN.'", Carrinho=1, Flag=1, fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" WHERE Ref = "'.$ItemCode.'" AND fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'"';
			$sql = 'UPDATE products SET ID_Marca="'.$marca_id.'", Ref_Ean="'.$EAN.'", Carrinho=1, Flag=1, fornecedor_importacao="'.FORNECEDOR_IMPORTACAO.'" WHERE Ref = "'.$ItemCode.'"';
			echo $sql."\n";
			$res = Runquery($db_link, $DB_name, $sql);

			for($a=0; $a<count($Titulos); $a++)
			{
				detect_product_lang($prod_id, $Titulos[$a]['titulo'], $Titulos[$a]['descricao'], $Titulos[$a]['lingua']);
			}
		}
		else
		{
			if($prod_fam_id > 0) $sql = 'INSERT INTO products(Ref, ID_ORG, ID_Marca, Data_criacao, Aproved, Flag, Ref_Ean, Carrinho, fornecedor_importacao) VALUES ("'.$ItemCode.'", "'.FAMILIA_TEMP.'", "'.$marca_id.'", "'.date('Y-m-d H:i:s').'", 1, 1, "'.$EAN.'",1,"'.FORNECEDOR_IMPORTACAO.'")';
			else $sql = 'INSERT INTO products(Ref, ID_ORG, ID_Marca, Data_criacao, Aproved, Flag, Ref_Ean, Carrinho, fornecedor_importacao) VALUES ("'.$ItemCode.'", "'.FAMILIA_TEMP.'", "'.$marca_id.'", "'.date('Y-m-d H:i:s').'", 0, 1, "'.$EAN.'",1,"'.FORNECEDOR_IMPORTACAO.'")';
			$prod_fam_id = FAMILIA_TEMP;

			echo $sql."\n";

			$res = Runquery($db_link, $DB_name, $sql);
			$prod_id = db_get_last_insert_id($db_link);
			if($prod_id > 0)
			{
				for($a=0; $a<count($Titulos); $a++)
				{
					detect_product_lang($prod_id, $Titulos[$a]['titulo'], $Titulos[$a]['descricao'], $Titulos[$a]['lingua']);
				}
			}
		}
		$rs->db_close(1);

		return array('prod_id' => $prod_id, 'prod_fam_id' => $prod_fam_id);
	}

	return array();
}

function detect_product_lang($prod_id, $ItemDesc, $ExtDesc, $lingua)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;

	$sql = 'SELECT ID FROM products_lang WHERE ID_PAI = "'.$prod_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$cond = array();

		if($ItemDesc!='') $cond[] = 'Cabecalho="'.addslashes($ItemDesc).'"';
		if($ExtDesc!='') $cond[] = 'Texto1=""';
		if($ExtDesc!='') $cond[] = 'Texto3="'.addslashes($ExtDesc).'"';


		if(!empty($cond))
		{
			$sql = 'UPDATE LOW_PRIORITY products_lang SET '.implode(',', $cond).' WHERE ID_PAI="'.$prod_id .'" and Lang="'.$lingua.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}
	}
	else
	{
		$cond = array();

		if($ItemDesc!=''){ $cond[] = 'Cabecalho';  $cond2[] = '"'.addslashes($ItemDesc).'"'; }
		if($ExtDesc!=''){ $cond[] = 'Texto1';  $cond2[] = '""'; }
		if($ExtDesc!=''){ $cond[] = 'Texto3';  $cond2[] = '"'.addslashes($ExtDesc).'"'; }

		$sql = 'INSERT INTO products_lang (ID_PAI, Lang'.(!empty($cond) ? ','.implode(',', $cond) : '').') VALUES ("'.$prod_id.'", "'.$lingua.'" '.(!empty($cond2) ? ','.implode(',', $cond2) : '').')';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close(1);
}


function getCategoriesRecursive($id_org)
{
	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;
	$arvore2 = array();
	$pai = $id_org;
	while($pai > 0)
	{
		$id_org=$pai;
		$sql="SELECT ID, ID_ORG FROM products_families WHERE ID=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai=$obj->db_data["ID_ORG"];
		$arvore2[]=$obj->db_data["ID"];
	}

	if(empty($arvore2)) $arvore2[] = $id_org;

	$output="";
	$t = (count($arvore2)-1);
	for($a=0;$a<count($arvore2);$a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();
	return $output;
}


function detect_filter_products($prod_id, $id_org=0, $marca_id=0, $category_id_import=0, $value_id_import=0)
{
	if($prod_id > 0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2 = new DataObj;

		$sql = 'SELECT prod_id FROM filters_products WHERE prod_id = "'.$prod_id.'"';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$fams = getCategoriesRecursive($id_org);
			if(isset($arr_fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $id_org;
			if(isset($arr_fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
			if(isset($arr_fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
			// if(isset($arr_fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

			$sql ='UPDATE filters_products SET filtro_area=2, filtros_rel2="'.$category_id_import.';'.$value_id_import.'", cat_id="'.$category_id_import.'",valor_id="'.$value_id_import.'",fam_id1="'.$fam_id1.'", fam_id2="'.$fam_id2.'", fam_id3="'.$fam_id3.'", marca_id="'.$marca_id.'" where prod_id = "'.$prod_id.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		else
		{
			$sql = '
						INSERT INTO filters_products
						(
							cat_id, valor_id, prod_id, fam_id1, fam_id2, fam_id3, marca_id, filtros_rel2, FLAG, filtro_active, filtro_area
						)
						VALUES
						(
							"'.$category_id_import.'", "'.$value_id_import.'", "'.$prod_id.'", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$marca_id.'", "'.$category_id_import.';'.$value_id_import.'", 1, 1, 2
						)
					';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		$rs2->db_close(1);
	}
}

function detect_filter_values($ordem2=0, $category_id_import=0, $value_id_import='', $lingua='pt', $filter='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;

	$sql = '
			SELECT
				value_id
			FROM
				filters_values
			WHERE
				category_id_import='.$category_id_import.'
				AND value_id_import_ref="'.limpa_special($value_id_import).'"
			';
	$rs2->db_open($db_link,$DB_name, $sql);
	if(!$rs2->EOF())
	{
		$value_id = $rs2->db_data['value_id'];
		$sql = 'UPDATE filters_values SET value_order="'.$ordem2.'", category_id=1, value_active=1, value_flag=1 WHERE category_id_import='.$category_id_import.' AND value_id_import_ref="'.limpa_special($value_id_import).'"';
		$res = Runquery($db_link, $DB_name, $sql);
		$sql = 'UPDATE filters_values_lang SET values_name="'.addslashes($filter).'", values_lang="'.$lingua.'" WHERE category_id_import='.$category_id_import.' AND value_id_import_ref="'.limpa_special($value_id_import).'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO filters_values(value_order, category_id, value_active, value_flag, category_id_import, value_id_import_ref) VALUES('.$ordem2.', 1, 1, 1, '.$category_id_import.', "'.limpa_special($value_id_import).'")';
		$res = Runquery($db_link, $DB_name, $sql);
		$value_id = db_get_last_insert_id($db_link);
		$sql = 'INSERT INTO filters_values_lang(values_id, values_name, values_lang, category_id_import, value_id_import_ref) VALUES( '.$value_id.', "'.addslashes($filter).'", "'.$lingua.'", '.$category_id_import.', "'.limpa_special($value_id_import).'")';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close(1);

	return $value_id;
}