<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = dirname( __FILE__ ) . '/../../../core/';
	require_once $path . 'core_files.php';
	require_once dirname( __FILE__ ).'/../../../admin_/products/funcoesfiltros.inc.php';
	require_once dirname( __FILE__ ).'/../../../admin_/products/filtros/funcoes.inc.php';
	require_once dirname(__FILE__) . '/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';


	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors',1);

	// error_reporting(0);
	set_time_limit(0);
	ini_set("max_execution_time","360000");
	ini_set("memory_limit","-1");
	ini_set("post_max_size","8M");
	ini_set("upload_max_filesize","8M");

	$linguas_importa[] = 'pt';

	$familia_novas = 0; // TODAS AS FAMILIAS NOVAS VÃO PARA ESTAS

	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj; $rs2 = new DataObj; $rs3 = new DataObj; $rs4 = new DataObj;

	$field_array = array();
	$fields=db_list_fields($DB_name,'products',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Ref_Ean', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_Ean varchar(255) DEFAULT ""');}

	// value_id_import_ref
	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_categories',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('value_id_import_ref', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories ADD value_id_import_ref varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_categories_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('value_id_import_ref', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories_lang ADD value_id_import_ref varchar(255) DEFAULT ""');}



	$imagem = array();
	$prod = array();


	// $tipos = array();
	// for($a=0; $a<count($names); $a++)
	// {
		// if(substr_count($names[$a], 'Tipo - ')) $tipos[] = $names[$a];
		// elseif(substr_count($names[$a], 'Tipo') && !substr_count($names[$a], 'Tipo - ')) $tipos[] = $names[$a];
	// }

	$sql = '
			SELECT
				import_id, Marca, Categoria, Sub_Categoria, Sub_Categoria2, Tipo, Tipo_Caracteritica, Codigo_produto,
				Produto, Descricao, Medidas, Preco_IVA, Stock, EAN, Imagem_1, Imagem_2, Imagem_3, Imagem_4
			FROM
				products_import
			';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{

			$Stock = $rs->db_data['Stock'];
			$Marca = $rs->db_data['Marca'];
			$Categoria = $rs->db_data['Categoria'];
			$Sub_Categoria = $rs->db_data['Sub_Categoria'];
			$Sub_Categoria2 = $rs->db_data['Sub_Categoria2'];
			$Tipo = $rs->db_data['Tipo'];
			$Tipo_Caracteritica_bd = $rs->db_data['Tipo_Caracteritica']; // TODO
			$ItemCode = $rs->db_data['Codigo_produto'];
			$Produto = $rs->db_data['Produto'];
			$Descricao = $rs->db_data['Descricao'];
			$Medidas = $rs->db_data['Medidas'];
			$Preco_IVA = $rs->db_data['Preco_IVA'];
			$EAN = $rs->db_data['EAN'];
			$Imagem_1 = $rs->db_data['Imagem_1'];
			$Imagem_2 = $rs->db_data['Imagem_2'];
			$Imagem_3 = $rs->db_data['Imagem_3'];
			$Imagem_4 = $rs->db_data['Imagem_4'];

			if(isset($imagem)) reset($imagem);	$imagem = array();
			if($Imagem_1 != '') $imagem[] = $Imagem_1;
			if($Imagem_2 != '') $imagem[] = $Imagem_2;
			if($Imagem_3 != '') $imagem[] = $Imagem_3;
			if($Imagem_4 != '') $imagem[] = $Imagem_4;


			$prod[$ItemCode]['Stock'] = $Stock;
			$prod[$ItemCode]['EAN'] = $EAN;
			// $prod[$ItemCode]['Tipo'] = $Tipo;
			// $prod[$ItemCode]['Tipo_Caracteritica'] = $Tipo_Caracteritica;

			$prod[$ItemCode]['Categoria'] = $Categoria;
			$prod[$ItemCode]['Sub_Categoria'] = $Sub_Categoria;
			$prod[$ItemCode]['Sub_Categoria2'] = $Sub_Categoria2;

			$prod[$ItemCode]['Produto'] = $Produto;
			$prod[$ItemCode]['Descricao'] = $Descricao;
			$prod[$ItemCode]['Marca'] = $Marca;
			$prod[$ItemCode]['PVP'][] = $Preco_IVA;
			$prod[$ItemCode]['Medidas'][] = array('Medidas' => $Medidas, 'PVP' => $Preco_IVA);
			$prod[$ItemCode]['Imagens'] = $imagem;


			if($Tipo_Caracteritica_bd != '')
			{

				$Tipo_Caracteritica = explode('[split2]', $Tipo_Caracteritica_bd);
				for($a=0; $a<count($Tipo_Caracteritica);$a++)
				{
					$t = explode('[split]', $Tipo_Caracteritica[$a]);

					if($t[0] != '' && $t[1] != '') $prod[$ItemCode]['Tipo_Caracteriticas'][] = array('Categoria' => $t[0], 'Filtro' => $t[1]);
				}
			}

			if($Tipo != '') $prod[$ItemCode]['Tipo_Caracteriticas'][] = array('Categoria' => 'Tipo', 'Filtro' => $Tipo);

			$rs->db_next();
		}
	}
	$rs->db_close();

	$ExtDesc_EN = '';
	if(count($prod) > 0)
	{
		$myFile = dirname( __FILE__ )."/../../../files/importar/atualizar.txt";
		$fh = fopen($myFile, 'a+');
		fwrite($fh, $filename."\n");
		chmod($myFile, 0755);

		foreach($prod as $ItemCode => $val)
		{
			if(trim($ItemCode))
			{
				echo "\n".$ItemCode;
				fwrite($fh, $ItemCode." | ");

				$Stock = $val['Stock'];
				$Produto = $val['Produto'];
				$Descricao = $val['Descricao'];
				$PVP1_desde = min($val['PVP']);

				$Categoria = $val['Categoria'];
				$Sub_Categoria = $val['Sub_Categoria'];
				$Sub_Categoria2 = $val['Sub_Categoria2'];
				// $Tipo_Colchao = $val['Tipo_Colchao'];
				$Brand = $val['Marca'];

				// Arrays
				$Medidas_ARR = $val['Medidas'];
				$TIPOS_ARR = $val['Tipo_Caracteriticas'];
				$imagem = $val['Imagens'];
				$Ean = $val['EAN'];


				// $myFile = dirname( __FILE__ )."/../../../files/importar/ssss2.txt";
				// $fh2 = fopen($myFile, 'w');
				// fwrite($fh2, print_r($TIPOS_ARR,true)."\n");
				// fclose($fh2);

				// exit;

				/* FAMILIAS */
				$fam_id = 0;
				$subfam_id = 0;
				$prod_fam_id = 0;


				// 3 niveis
				$prod_fam = detect_family($Categoria, $Categoria, $Categoria, 0, $Categoria);
				if($prod_fam['fam_id'] > 0)
				{
					$fam_id = $prod_fam['fam_id'];
					$prod_subfam = detect_family($Sub_Categoria, $Sub_Categoria, $Sub_Categoria, $fam_id, $Categoria.'_'.$Sub_Categoria);

					if($prod_subfam['fam_id'] > 0)
					{
						$subfam_id = $prod_subfam['fam_id'];

						$prod_subfam2 = detect_family($Sub_Categoria2, $Sub_Categoria2, $Sub_Categoria2, $subfam_id, $Categoria.'_'.$Sub_Categoria.'_'.$Sub_Categoria2);
						$subfam_id2 = $prod_subfam2['fam_id'];
					}
				}

				if($subfam_id2 > 0) $prod_fam_id = $subfam_id2; elseif($subfam_id > 0) $prod_fam_id = $subfam_id; else $prod_fam_id = $fam_id;


				$marca_id = 1;
				if($Brand != '')
				{
					$prod_marca = detect_brand($Brand);
					$marca_id = $prod_marca['marca_id'];
				}

				$prod_info = detect_product($prod_fam_id, $marca_id, $ItemCode, '', $Produto, $ExtDesc_EN, $Descricao, $Ean, '');
				if($prod_info['prod_id'] > 0)
				{
					detect_famprod($prod_info['prod_id'], $prod_fam_id);
					detect_products_objs($prod_info['prod_id'], $PVP1_desde, 0, $Stock, $ItemCode, 0);


					// INSERÇÃO FILTROS
					$sql = 'UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_info['prod_id'].'"';
					$res = Runquery($db_link, $DB_name, $sql);

					$sql = " UPDATE products_objs_combo SET obj_val = 0 WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' ";
					$res = Runquery($db_link, $DB_name, $sql);
					$cria_vazio = 0;


					// FILTROS LATERAIS
					for($a=0; $a<count($TIPOS_ARR);$a++)
					{
						$tipos_cat = ''; $tipos_cat = trim($TIPOS_ARR[$a]['Categoria']);
						$tipos_fil = ''; $tipos_fil = trim($TIPOS_ARR[$a]['Filtro']);

						// echo  $tipos_cat.' - '.$tipos_fil."\n";

						$category_id_import_1 = 0; $value_id_import_1 = 0;
						if($tipos_cat != '')
						{
							$category_id_import_1 = detect_filter_categories($tipos_cat, 'pt');
							// echo  $category_id_import_1.' - '.$tipos_fil."\n";
							if($category_id_import_1 > 0 && $tipos_fil != '')
							{
								// echo $category_id_import_1.', '.$tipos_fil."\n";
								$value_id_import_1 = detect_filter_values(0, $category_id_import_1, $tipos_fil, 'pt', $tipos_fil);

								if($value_id_import_1 > 0)
								{
									detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, $category_id_import_1, $value_id_import_1, 0);

									$cria_vazio = 1;
								}
							}
						}
					}

					// exit;
					// Opções de escolha na ficha produto
					for($a=0; $a<count($Medidas_ARR);$a++)
					{
						$Medidas = ''; $Medidas = trim(iconv("UTF-8","ISO-8859-1",$Medidas_ARR[$a]['Medidas'])," \t\n\r\0\x0B\xA0"); //trim($Medidas_ARR[$a]['Medidas']," \t\n\r\0\x0B\xA0");
						$PVP1 = ''; $PVP1 = $Medidas_ARR[$a]['PVP'];

						// echo $Medidas."\n";
						$category_id_import_1 = 141; $value_id_import_1 = 0;

						if($Medidas!='') $value_id_import_1 = detect_filter_values(0, $category_id_import_1, $Medidas, 'pt', $Medidas);
						if($value_id_import_1 > 0 && $Medidas != '')
						{
							detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, $category_id_import_1, $value_id_import_1, 1);

							$c_obj_id = 0;

							$sql = 'SELECT obj_id FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$prod_info['prod_id'].'" AND obj_combo="'.$category_id_import_1.'-'.$value_id_import_1.'"';
							$rs4->db_open($db_link,$DB_name,$sql);
							if(!$rs4->EOF())
							{
								$c_obj_id = $rs4->db_data['obj_id'];
							}
							$rs4->db_close();

							products_objs_filtros_save_sync($prod_info['prod_id'], $c_obj_id, 0, $PVP1, $Stock, 0, $category_id_import_1.'-'.$value_id_import_1, $Medidas);

							// filtros_ficha_produto_adm_sync($prod_id=0, $fam_id=0, $obj_preco = 0, $obj_stock = 0, $obj_ref = '')

							$cria_vazio = 1;
						}

					}

					// MARCA
					if($Brand != '')
					{
						$category_id_import_1 = 140; $value_id_import_1 = 0;
						$value_id_import_1 = detect_filter_values(0, $category_id_import_1, $Brand, 'pt', $Brand);
						if($value_id_import_1 > 0 && $Brand != '')
						{
							detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, $category_id_import_1, $value_id_import_1, 0);

							$cria_vazio = 1;
						}
					}


					// TIPO DE COLCHAO
					/* if(trim($Tipo_Colchao) != '')
					{
						$category_id_import_1 = 142; $value_id_import_1 = 0;

						if($Tipo_Colchao!='') $value_id_import_1 = detect_filter_values(0, $category_id_import_1, $Tipo_Colchao, 'pt', $Tipo_Colchao);
						if($value_id_import_1 > 0 && $Tipo_Colchao != '')
						{
							detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, $category_id_import_1, $value_id_import_1, 0);

							$cria_vazio = 1;
						}
					} */

					$sql = "DELETE FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' AND obj_val=0";
					$res = Runquery($db_link, $DB_name, $sql);

					if($cria_vazio == 0) detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, 0, 0, 0);

					$sql = 'DELETE FROM filters_products WHERE FLAG=0 and prod_id="'.$prod_info['prod_id'].'"';
					$res = Runquery($db_link, $DB_name, $sql);



					for($z=0; $z<count($imagem);$z++)
					{
						$url = '';

						if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.trim($imagem[$z]).'.png')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.trim($imagem[$z]).'.png';
						elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.trim($imagem[$z]).'.jpg')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.trim($imagem[$z]).'.jpg';
						elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.trim($imagem[$z]).'.gif')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.trim($imagem[$z]).'.gif';

						// ESTA NA PASTA
						if($url!='' && file_exists($url))
						{
							$ext = pathinfo($url, PATHINFO_EXTENSION);
							$url_new = dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext;

							if($ext!='' && file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext))
							{
								@unlink(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext);
								copy($url, $url_new);

								$res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
								$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
								$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",'.($z+1).')');
							}
							elseif($ext != '')
							{
								copy($url, $url_new);
								$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
								$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
								$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",'.($z+1).')');
							}
						}
						elseif($imagem[$z]!='' && filter_var(trim($imagem[$z]), FILTER_VALIDATE_URL))
						{
							$url = $file_path = trim($imagem[$z]);
							$ext = '';
							switch(@exif_imagetype($file_path))
							{
								case IMAGETYPE_JPEG:
									$ext = 'jpg';
									break;
								case IMAGETYPE_PNG:
									$ext = 'png';
									break;
								case IMAGETYPE_GIF:
									$ext = 'gif';
									break;
							}

							$url_new = dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext;

							if($ext!='' && file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext))
							{
								@unlink(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext);
								copy($url, $url_new);

								$res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
								$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
								$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",'.($z+1).')');
							}
							elseif($ext != '')
							{
								copy($url, $url_new);
								$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
								$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
								$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",'.($z+1).')');
							}
						}
					}

					if($prod_fam_id > 0 && $marca_id > 0)
					{
						gera_produtos($prod_info['prod_id'], $prod_fam_id, $marca_id, 1, 'pt');
					}

					if(!file_exists($myFile)) die();
				}
			}
		}

		echo "\n atualiza filtros... ";
		fwrite($fh, "<br> ATUALIZAR FILTROS...");
		gera_produtos_filtros('pt');
		fclose($fh);

		/* $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p*.json") as $filename)
		{
			$fl = explode('_p',$filename);
			$prod_id = str_replace(array('_en.json', '_pt.json', '_es.json', '_de.json', '_fr.json'), '', $fl[1]);

			$sql = 'SELECT products.ID FROM products WHERE products.ID="'.$prod_id.'" AND Aproved=0';
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF())
			{
				@unlink($filename);
			}
			$rs->db_close();
		} */
	}


	// if(isset($log_error) && $log_error != '') log_error($log_error);
	log_ok();

function log_error($txt)
{
	if(copy($GLOBALS['file_path'], $GLOBALS['file_path_done'])) @unlink($GLOBALS['file_path']);
	@unlink(dirname( __FILE__ )."/../../../files/importar/atualizar.txt");

	$myFile = dirname( __FILE__ )."/../../../files/importar/log_error_".date('YmdHis').".txt";
	$fh = fopen($myFile, 'w');
	fwrite($fh, $txt);
	fclose($fh);
}
function log_ok()
{
	// if(copy($GLOBALS['file_path'], $GLOBALS['file_path_done'])) @unlink($GLOBALS['file_path']);
	@unlink(dirname( __FILE__ )."/../../../files/importar/atualizar.txt");
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
function products_objs_filtros_save_sync($ID_PROD=0, $c_obj_id='', $c_obj_iva='', $c_obj_preco='', $c_obj_stock='', $c_obj_promo='', $c_obj_combo='', $c_obj_ref='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$objv_id = $c_obj_id;
	$obj_iva = $c_obj_iva;
	$obj_preco = $c_obj_preco;
	$obj_stock = $c_obj_stock;
	$obj_promo = $c_obj_promo;
	$obj_combo = $c_obj_combo;
	$obj_ref = $c_obj_ref;


	$rs = new DataObj; $rs2=new DataObj;

	$sql = "SELECT obj_id FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' AND obj_id='".$objv_id."' AND obj_combo='".$obj_combo."'";
	// echo $sql.'<br>';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$obj_id = $rs->db_data['obj_id'];
		$sql = "
				UPDATE products_objs_combo
				SET
					obj_iva='".$obj_iva."',
					obj_pvp='".$obj_preco."',
					obj_stock='".$obj_stock."',
					obj_combo = '".$obj_combo."',
					obj_ref = '".$obj_ref."',
					obj_val = 1
				WHERE
					obj_id='".$obj_id."'
					AND obj_type='products'
					AND obj_type_id='".$ID_PROD."'
				";
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = "
				INSERT INTO
					products_objs_combo
					(
						obj_type,
						obj_type_id,
						obj_iva,
						obj_pvp,
						obj_stock,
						obj_combo,
						obj_ref,
						obj_val
					)
					VALUES
					(
						'products',
						'".$ID_PROD."',
						'".$obj_iva."',
						'".$obj_preco."',
						'".$obj_stock."',
						'".$obj_combo."',
						'".$obj_ref."',
						1
					)
				";
		$res = Runquery($db_link, $DB_name, $sql);
		$obj_id = db_get_last_insert_id($db_link);

	}

	$rs->db_close();



}
function devolvevalor($data, $campo, $tipo="", $ItemCode='')
{
	// global $names;
	$valor = $data[array_search($campo, $GLOBALS['names'])];
	if($tipo=="dt") $valor = convert_data($valor);
	elseif($tipo=="num")
	{
		// if($ItemCode == 'DRE.C2.EXOTIC') echo 'xxx '.$valor.' xxx'."\n";

		$valor = str_replace(",","",$valor);
		$valor = str_replace("€","",$valor);
		$valor = str_replace(",",".",$valor);
		// if($ItemCode == 'DRE.C2.EXOTIC') echo 'xxx '.$valor.' xxx'."\n";
		$valor = floatval($valor);
	}
	elseif($tipo == 'perc') $valor = str_replace("%", '', $valor);

	$valor = trim($valor); // tira espaços no fim
	$valor = ltrim($valor,"'"); // tira a plica do início do texto
	// $valor = trim(preg_replace('/\s+/',' ', $valor));

	if($tipo=='num' && $valor == '') $valor = 0;

	return $valor;
}
function detect_products_objs($ID_PROD=0, $obj_pvp=0, $obj_qtdmin=0, $obj_stock=0, $obj_ref='', $tipo_pvp=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;

	// if($obj_stock<=0) $obj_stock = 1;

	$sql = 'SELECT obj_id FROM products_objs WHERE obj_type_id = "'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$obj_id =  $rs->db_data['obj_id'];

		$sql = 'UPDATE products_objs SET obj_type="products", obj_pvp="'.$obj_pvp.'", obj_qtdmin="'.$obj_qtdmin.'", obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
		// echo $sql."\n";
		$res = Runquery($db_link, $DB_name, $sql);

		$sql = 'UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang = "pt"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO products_objs (obj_type_id, obj_ativo, obj_pvp, obj_qtdmin, obj_stock, obj_type, obj_val2) VALUES ("'.$ID_PROD.'", 1, "'.$obj_pvp.'", "'.$obj_qtdmin.'", "'.$obj_stock.'", "products", "'.$tipo_pvp.'")';
		$res = Runquery($db_link, $DB_name, $sql);
		$obj_id = db_get_last_insert_id($db_link);
		$sql = 'INSERT INTO products_objs_lang (Cabecalho, ID_PAI, Lang) VALUES ("'.$obj_ref.'", "'.$obj_id.'", "pt")';
		$res = Runquery($db_link, $DB_name, $sql);

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
		$sql = 'UPDATE products_families_obj set ID_ORG =  "'.$ID_ORG.'" WHERE  ID_PROD = "'.$ID_PROD.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{

		$sql = 'SELECT ID_PROD FROM products_families_obj WHERE ID_PROD = "'.$ID_PROD.'"';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$sql = 'DELETE FROM products_families_obj WHERE ID_PROD = "'.$ID_PROD.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		$rs2->db_close();

		$sql = 'INSERT INTO products_families_obj (ID_PROD, ID_ORG) VALUES ("'.$ID_PROD.'", "'.$ID_ORG.'")';
		$res = Runquery($db_link, $DB_name, $sql);


	}
	$rs->db_close();
}
function detect_family($XNGroupCode, $XNGroupDesc_EN, $XNGroupDesc_PT, $FAMID=0, $Ref_Integracao2='')
{
	$ItemCode = trim($XNGroupCode);
	$fam_id = 0;
	if($ItemCode != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID, ID_ORG FROM products_families WHERE Ref_Integracao2="'.limpa_special_sync($Ref_Integracao2).'"';
		// echo $sql."\n";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$fam_id = $rs->db_data['ID'];

			if($FAMID > 0) $sql = 'UPDATE products_families SET ID_ORG="'.$FAMID.'", FLAG=1, visivel=1, data_atualizacao="'.date('Y-m-d H:i:s').'", Ref_Integracao2="'.limpa_special_sync($Ref_Integracao2).'" WHERE ID="'.$fam_id.'"';
			else $sql = 'UPDATE products_families SET FLAG=1, visivel=1, data_atualizacao="'.date('Y-m-d H:i:s').'", Ref_Integracao2="'.limpa_special_sync($Ref_Integracao2).'" WHERE ID="'.$fam_id.'"';

			$res = Runquery($db_link, $DB_name, $sql);

			if(trim($XNGroupDesc_PT) != '') detect_family_lang($fam_id, $XNGroupDesc_PT, 'pt');
			// if(trim($XNGroupDesc_EN) != '') detect_family_lang($fam_id, $XNGroupDesc_EN, 'en');
		}
		else
		{
			$sql = 'INSERT INTO products_families (ID_ORG, visivel, data_atualizacao, Ref_Integracao, Ref_Integracao2, FLAG) VALUES ("'.$FAMID.'", 1,"'.date('Y-m-d H:i:s').'", "'.limpa_special_sync($Ref_Integracao2).'", "'.limpa_special_sync($Ref_Integracao2).'", 1)';
			$res = Runquery($db_link, $DB_name, $sql);
			$fam_id = db_get_last_insert_id($db_link);
			if($fam_id > 0)
			{
				if(trim($XNGroupDesc_PT) != '') detect_family_lang($fam_id, $XNGroupDesc_PT, 'pt');
				// if(trim($XNGroupDesc_EN) != '') detect_family_lang($fam_id, $XNGroupDesc_EN, 'en');
			}
		}
		$rs->db_close();


	}
	return array('fam_id' => $fam_id);
}

function detect_family_lang($fam_id, $XNGroupDesc, $lingua)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;

	$sql = 'SELECT ID FROM products_families_lang WHERE ID_PAI = "'.$fam_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$sql = 'UPDATE LOW_PRIORITY products_families_lang SET Cabecalho="'.addslashes($XNGroupDesc).'" WHERE ID_PAI="'.$fam_id .'" and Lang="'.$lingua.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'", "'.addslashes($XNGroupDesc).'", "'.$lingua.'")';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close();
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

		$sql = 'SELECT ID FROM brands WHERE ref_integracao = "'.addslashes(limpa_special_sync($ItemCode)).'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$marca_id = $rs->db_data['ID'];

			$sql = 'UPDATE brands SET data_atualizacao="'.date('Y-m-d H:i:s').'", FLAG=1 WHERE ref_integracao = "'.addslashes(limpa_special_sync($ItemCode)).'"';
			$res = Runquery($db_link, $DB_name, $sql);

			detect_brand_lang($marca_id, $ItemCode, 'pt');
			// detect_brand_lang($marca_id, $ItemCode, 'en');
		}
		else
		{
			$sql = 'INSERT INTO brands (visivel, ref_integracao, data_atualizacao, FLAG) VALUES (1, "'.addslashes(limpa_special_sync($ItemCode)).'", "'.date('Y-m-d H:i:s').'", 1)';
			$res = Runquery($db_link, $DB_name, $sql);
			$marca_id = db_get_last_insert_id($db_link);
			if($marca_id > 0)
			{
				detect_brand_lang($marca_id, $ItemCode, 'pt');
				// detect_brand_lang($marca_id, $ItemCode, 'en');
			}
		}
		$rs->db_close();

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
	$rs2->db_close();
}

function detect_product($prod_fam_id, $marca_id, $ItemCode, $ItemDesc_EN, $ItemDesc_PT, $ExtDesc_EN, $ExtDesc_PT, $Ref_Ean, $WeightGR)
{

	$prod_id = 0;
	if($ItemCode != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID, ID_ORG FROM products WHERE Ref = "'.$ItemCode.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$prod_id = $rs->db_data['ID'];

			$sql = 'UPDATE products SET ID_ORG="'.$prod_fam_id.'", ID_Marca="'.$marca_id.'", FLAG=1, Aproved=1, Carrinho=1, Ref_Ean="'.$Ref_Ean.'", udtime="'.date('Y-m-d H:i:s').'" WHERE Ref = "'.$ItemCode.'"';
			$res = Runquery($db_link, $DB_name, $sql);

			detect_product_lang($prod_id, $ItemDesc_PT, $ExtDesc_PT, 'pt');
			// detect_product_lang($prod_id, $ItemDesc_EN, $ExtDesc_EN, 'en');
		}
		else
		{
			if($prod_fam_id > 0) $sql = 'INSERT INTO products(Ref, ID_ORG, ID_Marca, Data_criacao, Aproved, Carrinho, FLAG, Ref_Ean, idtime) VALUES ("'.$ItemCode.'", "'.$prod_fam_id.'", "'.$marca_id.'", "'.date('Y-m-d H:i:s').'", 1, 1, 1, "'.$Ref_Ean.'", "'.date('Y-m-d H:i:s').'")';
			else $sql = 'INSERT INTO products(Ref, ID_ORG, ID_Marca, Data_criacao, Aproved, Carrinho, FLAG, Ref_Ean, idtime) VALUES ("'.$ItemCode.'", "'.$prod_fam_id.'", "'.$marca_id.'", "'.date('Y-m-d H:i:s').'", 0, 1, 1,"'.$Ref_Ean.'", "'.date('Y-m-d H:i:s').'")';
			$res = Runquery($db_link, $DB_name, $sql);
			$prod_id = db_get_last_insert_id($db_link);
			if($prod_id > 0)
			{
				detect_product_lang($prod_id, $ItemDesc_PT, $ExtDesc_PT, 'pt');
				// detect_product_lang($prod_id, $ItemDesc_EN, $ExtDesc_EN, 'en');
			}
		}
		$rs->db_close();

		return array('prod_id' => $prod_id);
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
		if($ExtDesc!='') $cond[] = 'Texto1="'.addslashes($ExtDesc).'"';


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
		if($ExtDesc!=''){ $cond[] = 'Texto1';  $cond2[] = '"'.addslashes($ExtDesc).'"'; }

		$sql = 'INSERT INTO products_lang (ID_PAI, Lang'.(!empty($cond) ? ','.implode(',', $cond) : '').') VALUES ("'.$prod_id.'", "'.$lingua.'" '.(!empty($cond2) ? ','.implode(',', $cond2) : '').')';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close();
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

function detect_filter_categories($category_name='', $lingua='pt')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj; $rs3 = new DataObj;

	$sql = '
			SELECT
				category_id
			FROM
				filters_categories
			WHERE
				LOWER(value_id_import_ref)="'.strtolower(limpa_special_sync($category_name)).'"
			';
	$rs2->db_open($db_link,$DB_name, $sql);
	if(!$rs2->EOF())
	{
		$category_id = $rs2->db_data['category_id'];
		$sql = 'UPDATE filters_categories SET category_active=1, category_flag=1 WHERE LOWER(value_id_import_ref)="'.strtolower(limpa_special_sync($category_name)).'"';
		$res = Runquery($db_link, $DB_name, $sql);

		$sql = '
			SELECT
				category_id
			FROM
				filters_categories_lang
			WHERE
				LOWER(value_id_import_ref)="'.strtolower(limpa_special_sync($category_name)).'"
				AND category_lang="'.$lingua.'"
			';
			$rs3->db_open($db_link,$DB_name, $sql);
			if(!$rs2->EOF())
			{
				$sql = 'UPDATE filters_categories_lang SET category_name="'.addslashes($category_name).'" WHERE LOWER(value_id_import_ref)="'.strtolower(limpa_special_sync($category_name)).'" AND category_lang="'.$lingua.'"';
				$res = Runquery($db_link, $DB_name, $sql);
			}
			else
			{
				$sql = 'INSERT INTO filters_categories_lang(category_id, category_name, category_lang, value_id_import_ref) VALUES( '.$category_id.', "'.addslashes($category_name).'", "'.$lingua.'", "'.limpa_special_sync($category_name).'")';
				$res = Runquery($db_link, $DB_name, $sql);
			}
	}
	else
	{
		$sql = 'INSERT INTO filters_categories(category_active, category_flag, value_id_import_ref) VALUES(1, 1, "'.limpa_special_sync($category_name).'")';
		$res = Runquery($db_link, $DB_name, $sql);
		$category_id = db_get_last_insert_id($db_link);
		$sql = 'INSERT INTO filters_categories_lang(category_id, category_name, category_lang, value_id_import_ref) VALUES( '.$category_id.', "'.addslashes($category_name).'", "'.$lingua.'", "'.limpa_special_sync($category_name).'")';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close(1);

	return $category_id;
}

function detect_filter_values($ordem2=0, $category_id_import=0, $value_id_import='', $lingua='pt', $filter='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;$rs3 = new DataObj;

	$value_id = 0;

	if(trim($filter) != '')
	{

		$sql = '
				SELECT
					value_id
				FROM
					filters_values
				WHERE
					value_id_import_ref="'.limpa_special_sync($value_id_import).'"
				';
			// echo $sql."\n";
		$rs2->db_open($db_link,$DB_name, $sql);
		if(!$rs2->EOF())
		{
			$value_id = $rs2->db_data['value_id'];
			// $sql = 'UPDATE filters_values SET value_order="'.$ordem2.'", category_id='.$category_id_import.',values_name="'.addslashes($filter).'", value_active=1, value_flag=1 WHERE value_id_import_ref="'.limpa_special_sync($value_id_import).'"';
			$sql = 'UPDATE filters_values SET category_id='.$category_id_import.', value_active=1, value_flag=1, data="'.date('Y-m-d H:i:s').'" WHERE value_id_import_ref="'.limpa_special_sync($value_id_import).'"';
			$res = Runquery($db_link, $DB_name, $sql);

			for($a = 0; $a<count($GLOBALS['linguas_importa']); $a++)
			{
				$lingua = $GLOBALS['linguas_importa'][$a];

				$sql = '
					SELECT
						values_id
					FROM
						filters_values_lang
					WHERE
						category_id_import='.$category_id_import.'
						AND value_id_import_ref="'.limpa_special_sync($value_id_import).'"
						AND values_lang="'.$lingua.'"
				';
				$rs3->db_open($db_link,$DB_name, $sql);
				if(!$rs3->EOF())
				{
					$sql = 'UPDATE filters_values_lang SET values_name="'.addslashes($filter).'" WHERE category_id_import='.$category_id_import.' AND value_id_import_ref="'.limpa_special_sync($value_id_import).'" AND values_lang="'.$lingua.'"';
					$res = Runquery($db_link, $DB_name, $sql);
				}
				else
				{
					$sql = 'INSERT INTO filters_values_lang(values_id, values_name, values_lang, category_id_import, value_id_import_ref) VALUES( '.$value_id.', "'.addslashes($filter).'", "'.$lingua.'", '.$category_id_import.', "'.limpa_special_sync($value_id_import).'")';
					$res = Runquery($db_link, $DB_name, $sql);
				}
				$rs3->db_close();
			}
		}
		else
		{
			$sql = 'INSERT INTO filters_values(value_order, category_id, value_active, value_flag, category_id_import, value_id_import_ref, data) VALUES('.$ordem2.', '.$category_id_import.', 1, 1, '.$category_id_import.', "'.limpa_special_sync($value_id_import).'", "'.date('Y-m-d H:i:s').'")';
			$res = Runquery($db_link, $DB_name, $sql);
			$value_id = db_get_last_insert_id($db_link);
			for($a = 0; $a<count($GLOBALS['linguas_importa']); $a++)
			{
				$lingua = $GLOBALS['linguas_importa'][$a];
				$sql = 'INSERT INTO filters_values_lang(values_id, values_name, values_lang, category_id_import, value_id_import_ref) VALUES( '.$value_id.', "'.addslashes($filter).'", "'.$lingua.'", '.$category_id_import.', "'.limpa_special_sync($value_id_import).'")';
				// echo $sql."\n";
				$res = Runquery($db_link, $DB_name, $sql);
			}
		}
		$rs2->db_close(1);
	}
	return $value_id;
}
function detect_filter_products($prod_id, $id_org=0, $marca_id=0, $category_id_import=0, $value_id_import=0, $mostra=0)
{
	if($prod_id > 0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2 = new DataObj;

		if($category_id_import==0 && $value_id_import==0) $sql = 'SELECT prod_id FROM filters_products WHERE prod_id = "'.$prod_id.'"';
		else $sql = 'SELECT prod_id FROM filters_products WHERE prod_id = "'.$prod_id.'" AND cat_id="'.$category_id_import.'" AND valor_id="'.$value_id_import.'"';


		$fams = getCategoriesRecursive($id_org);
		// print_r($fams);
		if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $id_org;
		if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
		if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
		if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{

			$sql ='UPDATE filters_products SET FLAG=1, filtro_area="'.$mostra.'", filtros_rel2="'.$category_id_import.';'.$value_id_import.'",  fam_id1="'.$fam_id1.'", fam_id2="'.$fam_id2.'", fam_id3="'.$fam_id3.'", fam_id4="'.$fam_id4.'", marca_id="'.$marca_id.'" where prod_id = "'.$prod_id.'" AND cat_id="'.$category_id_import.'" AND valor_id="'.$value_id_import.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		else
		{
			$sql = '
						INSERT INTO filters_products
						(
							cat_id, valor_id, prod_id, fam_id1, fam_id2, fam_id3, fam_id4, marca_id, filtros_rel2, FLAG, filtro_active, filtro_area
						)
						VALUES
						(
							"'.$category_id_import.'", "'.$value_id_import.'", "'.$prod_id.'", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$fam_id4.'", "'.$marca_id.'", "'.$category_id_import.';'.$value_id_import.'", 1, 1, "'.$mostra.'"
						)
					';
			$res = Runquery($db_link, $DB_name, $sql);
		}

		// echo $sql."\n";
		$rs2->db_close(1);
	}
}

function limpa_special_sync($s)
{
    $s=strip_tags($s);
	$s=str_replace('<br>','',$s);
	$s=str_replace('<br/>','',$s);
	$s=str_replace('<br />','',$s);
	$s=trim($s,"\t\n\r\0\x0B");
	$v=array("(",")","³","°","²","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","#","=");
    $s=str_replace($v,'',$s);
	$v=array(" ","'","/","&",":",";","`","´","~","^","¨","_",",",".","|","\\","<",">","—","€","±","×","¿");
    $s=str_replace($v,'-',$s);
	$s=str_replace('-----','-',$s);
	$s=str_replace('----','-',$s);
	$s=str_replace('---','-',$s);
	$s=str_replace('--','-',$s);
	$v=array("á","à","ã","â","ä"); 	$s=str_replace($v,'a',$s);
	$v=array("é","è","ê","ë"); 		$s=str_replace($v,'e',$s);
	$v=array("í","ì","î","ï"); 		$s=str_replace($v,'i',$s);
	$v=array("ó","ò","õ","ô","ö"); 	$s=str_replace($v,'o',$s);
	$v=array("ú","ù","û","ü"); 		$s=str_replace($v,'u',$s);
	$v=array("ñ"); 					$s=str_replace($v,'n',$s);
	$v=array("ç"); 					$s=str_replace($v,'c',$s);
	$v=array("Á","À","Ã","Â","Ä"); 	$s=str_replace($v,'A',$s);
	$v=array("É","È","Ê","Ë"); 		$s=str_replace($v,'E',$s);
	$v=array("Í","Ì","Î","Ï"); 		$s=str_replace($v,'I',$s);
	$v=array("Ó","Ò","Õ","Ô","Ö"); 	$s=str_replace($v,'O',$s);
	$v=array("Ú","Ù","Û","Ü"); 		$s=str_replace($v,'U',$s);
	$v=array("Ñ"); 		$s=str_replace($v,'N',$s);
	$v=array("Ç"); 		$s=str_replace($v,'C',$s);

	$s = preg_replace('/[^a-zA-Z0-9-_.]/', '', $s);

	return $s;
}
