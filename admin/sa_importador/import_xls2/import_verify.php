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

	error_reporting(0);
	// set_time_limit(0);
	// ini_set("max_execution_time","360000");
	// ini_set("memory_limit","128M");
	// ini_set("post_max_size","8M");
	// ini_set("upload_max_filesize","8M");

	$linguas_importa[] = 'pt';

	$familia_novas = 0; // TODAS AS FAMILIAS NOVAS VÃO PARA ESTAS

	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj; $rs2 = new DataObj; $rs3 = new DataObj; $rs4 = new DataObj;

	$sql = 'TRUNCATE TABLE products_import';
	$res = Runquery($db_link, $DB_name, $sql);

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


	foreach (glob($GLOBALS['site_shell_url']."files/importar/*.xlsx") as $filename)
	{
		$arr_loks[] = $filename;
	}


	if(count($arr_loks) > 0 && file_exists($arr_loks[0]))
	{

		$filename = $arr_loks[0];

		$lock_file = str_replace('.xlsx','', str_replace('.xlsx','_LOCK.xlsx', $filename)).'.txt';
		$lock_file_analise = str_replace('.xlsx','', str_replace('.xlsx','log_analise_', $filename)).'.txt';

		if(!file_exists($lock_file))
		{
			$file_path = $filename;
			$file_path_done = str_replace('importar', 'importado', $filename);

			$myFile = dirname( __FILE__ )."/../../../files/importar/atualizar_temp.txt";
			$fh = fopen($myFile, 'a+');
			fwrite($fh, $filename."\n");
			chmod($myFile, 0755);


			$objReader = PHPExcel_IOFactory::createReader('Excel2007');

			$inputFileType = 'Excel2007';
			$inputFileName = $file_path;
			$sheetIndex = 0;
			$myRow = 1; // LINHA COM OS CABECALHOS

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$sheetnames = $objReader->listWorksheetNames($inputFileName);
			$objReader->setLoadSheetsOnly($sheetnames[$sheetIndex]);
			$objPHPExcel = $objReader->load($inputFileName);
			// $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$sheetData = $objPHPExcel->getActiveSheet() ->rangeToArray('A' . $myRow . ':' .  $objPHPExcel->getActiveSheet()->getHighestColumn() . $myRow );

			$names = array();
			foreach($sheetData[0] as $field){$names[] = trim($field);}

			$tipos = array();
			for($a=0; $a<count($names); $a++)
			{
				if(substr_count(str_replace('–', '-', $names[$a]), 'Tipo - ')) $tipos[] = $names[$a];
				elseif(substr_count(str_replace('–', '-', $names[$a]), 'TIPO - ')) $tipos[] = $names[$a];
				elseif(substr_count(str_replace('–', '-', $names[$a]), 'tipo - ')) $tipos[] = $names[$a];
				// elseif(substr_count($names[$a], 'Tipo') && !substr_count($names[$a], 'Tipo - ')) $tipos[] = $names[$a];
			}


			if(empty($names)){ log_error('Não foi especificado o nome das colunas'); exit; }


			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$sheetnames = $objReader->listWorksheetNames($inputFileName);
			$objReader->setLoadSheetsOnly($sheetnames[$sheetIndex]);
			$objPHPExcel = $objReader->load($inputFileName);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, false);


			// print_r($sheetData);

			$prod = array(); $l=0;

			$linhas .= 'Marca[split]Categoria[split]Sub Categoria[split]Sub Categoria 2[split]Tipo[split]Código do produto[split]Produto[split]Descrição[split]Medidas[split]PVP Marca[split]Preço SITE com IVA[split]EAN[split]Imagem 1[split]Imagem 2[split]Imagem 3[split]Imagem 4'."\n";

			foreach($sheetData as $key => $data)
			{
				$Stock = 0; $Marca = ''; $Categoria = ''; $Sub_Categoria = ''; $Sub_Categoria2 = ''; $Tipo = ''; $Tipo_Caracteritica = ''; $Codigo_produto = ''; $Produto = ''; $Descricao = ''; $Medidas = ''; $Preco_IVA = ''; $EAN = ''; $Imagem_1 = ''; $Imagem_2 = ''; $Imagem_3 = ''; $Imagem_4 = '';
				if(isset($imagem)) reset($imagem);
				$imagem = array();

				if($key > 0)
				{
					if(in_array("Código do produto", $names)){ $Codigo_produto = devolvevalor($data,"Código do produto"); }
					// echo $Codigo_produto."\n";
					if(trim($Codigo_produto))
					{

						if(in_array("Produto", $names)){ $Produto = devolvevalor($data, "Produto"); } // NOME DO PRODUTO
						if(in_array("Descrição", $names)){ $Descricao = devolvevalor($data, "Descrição"); } // DESCRIÇÃO DO PRODUTO
						if(in_array("Preço SITE com IVA", $names)){ $Preco_IVA = devolvevalor($data, "Preço SITE com IVA", 'num', $Codigo_produto); } // PREÇO COM IVA DO PRODUTO
						if(in_array("Categoria", $names)){ $Categoria = devolvevalor($data, "Categoria"); } // FAMILIA 1º NIVEL
						if(in_array("Sub Categoria", $names)){ $Sub_Categoria = devolvevalor($data, "Sub Categoria"); }  // FAMILIA 2º NIVEL
						if(in_array("Sub Categoria 2", $names)){ $Sub_Categoria2 = devolvevalor($data, "Sub Categoria 2"); } // FAMILIA 3º NIVEL
						if(in_array("Marca", $names)){ $Marca = devolvevalor($data, "Marca"); }  // MARCA
						if(in_array("EAN", $names)){ $EAN = devolvevalor($data, "EAN"); }  // EAN
						if(in_array("Stock", $names)){ $Stock = devolvevalor($data, "Stock"); }  // EAN

						// SE FOR APENAS TIPO A CARACTERISTICA CHAMA-SE TIPO
						if(in_array("Tipo", $names)){ $Tipo = devolvevalor($data, "Tipo"); } // CARACTERISTICA TIPO

						// SE NO TIPO VIER COM IFEN  E TEXTO À FRENTE ex.: 'TIPO -', PODE TER VÁRIOS TIPOS DE CARACTERISTICAS
						$Tipo_s = '';
						for($z=0; $z<count($tipos);$z++)
						{
							$tipo_names = '';
							if(in_array($tipos[$z], $names)){ $tipo_names = devolvevalor($data, $tipos[$z]); }
							if($tipo_names != '') $Tipo_s .= str_replace('–', '-', $tipos[$z]).'[split]'.$tipo_names.'[split2]';
						}

						// CARACTERITICA DAS MEDIAS QUE AFETAM O PREÇO NA FICHA DO PRODUTO
						if(in_array("Medidas", $names)){ $Medidas = devolvevalor($data, "Medidas"); }

						// IMAGENS DO PRODUTO
						if(in_array("Imagem 1", $names)){ $Imagem_1 = devolvevalor($data, "Imagem 1"); }
						if(in_array("Imagem 2", $names)){ $Imagem_2 = devolvevalor($data, "Imagem 2"); }
						if(in_array("Imagem 3", $names)){ $Imagem_3 = devolvevalor($data, "Imagem 3"); }
						if(in_array("Imagem 4", $names)){ $Imagem_4 = devolvevalor($data, "Imagem 4"); }




						// print_r($prod[$Codigo_produto]['Tipo_']);
						// echo '<br>';



						products_temp_save_sync($Marca, $Categoria, $Sub_Categoria, $Sub_Categoria2, $Tipo, (isset($Tipo_s) ? $Tipo_s : ''), $Codigo_produto, $Produto, $Descricao, $Medidas, $Preco_IVA, $EAN, $Imagem_1, $Imagem_2, $Imagem_3, $Imagem_4, $Stock);


						// if($l < 11)
						// {
							// $linhas .= $Marca.'[split]'.$Categoria.'[split]'.$Sub_Categoria.'[split]'.$Sub_Categoria2.'[split]'.$Tipo.'[split]'.''.'[split]'.$Codigo_produto.'[split]'.$Produto.'[split]'.$Descricao.'[split]'.$Medidas.'[split]'.$Preco_IVA.'[split]'.$EAN.'[split]'.$Imagem_1.'[split]'.$Imagem_2.'[split]'.$Imagem_3.'[split]'.$Imagem_4."\n";
						// }

						$l++;

					}
				}
			}
			fclose($fh);
		}
	}

	log_ok($linhas, $lock_file_analise);

function log_error($txt)
{
	// if(copy($GLOBALS['file_path'], $GLOBALS['file_path_done'])) @unlink($GLOBALS['file_path']);
	@unlink(dirname( __FILE__ )."/../../../files/importar/atualizar_temp.txt");

}
function log_ok($txt, $lock_file_analise)
{
	if(copy($GLOBALS['file_path'], $GLOBALS['file_path_done'])) @unlink($GLOBALS['file_path']);
	@unlink(dirname( __FILE__ )."/../../../files/importar/atualizar_temp.txt");

	// $myFile = $lock_file_analise;
	// $fh = fopen($myFile, 'w');
	// fwrite($fh, $txt);
	// fclose($fh);
}

function products_temp_save_sync($Marca, $Categoria, $Sub_Categoria, $Sub_Categoria2, $Tipo, $Tipo_Caracteritica, $Codigo_produto, $Produto, $Descricao, $Medidas, $Preco_IVA, $EAN, $Imagem_1, $Imagem_2, $Imagem_3, $Imagem_4, $Stock)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);



	$sql = "
			INSERT INTO
				products_import
				(
					Marca,
					Categoria,
					Sub_Categoria,
					Sub_Categoria2,
					Tipo,
					Tipo_Caracteritica,
					Codigo_produto,
					Produto,
					Descricao,
					Medidas,
					Preco_IVA,
					EAN,
					Imagem_1,
					Imagem_2,
					Imagem_3,
					Imagem_4,
					Stock
				)
				VALUES
				(
					'".trim($Marca)."',
					'".trim($Categoria)."',
					'".trim($Sub_Categoria)."',
					'".trim($Sub_Categoria2)."',
					'".trim($Tipo)."',
					'".trim($Tipo_Caracteritica)."',
					'".trim($Codigo_produto)."',
					'".trim($Produto)."',
					'".trim($Descricao)."',
					'".trim($Medidas)."',
					'".trim($Preco_IVA)."',
					'".trim($EAN)."',
					'".trim($Imagem_1)."',
					'".trim($Imagem_2)."',
					'".trim($Imagem_3)."',
					'".trim($Imagem_4)."',
					'".trim($Stock)."'
				)
			";

	// $sql = 'Truncate products_import';
	$res = Runquery($db_link, $DB_name, $sql);
}


$urlEncodedWhiteSpaceChars   = '%81,%7F,%C5%8D,%8D,%8F,%C2%90,%C2,%90,%9D,%C2%A0,%A0,%C2%AD,%AD,%08,%09,%0A,%0D';
    $temp = explode(',', $urlEncodedWhiteSpaceChars); // turn them into a temp array so we can loop accross
    $email_address  = urlencode($row['EMAIL_ADDRESS']);
        foreach($temp as $v){
            $email_address  =  str_replace($v, '', $email_address);     // replace the current char with nuffink
        }
        $email_address = urldecode($email_address); // undo the url_encode
function devolvevalor($data, $campo, $tipo="", $ItemCode='')
{
	// global $names;

	$valor = $data[array_search($campo, $GLOBALS['names'])];
	$urlEncodedWhiteSpaceChars   = '%81,%7F,%C5%8D,%8D,%8F,%C2%90,%C2,%90,%9D,%C2%A0,%A0,%C2%AD,%AD,%08,%09,%0A,%0D';
	$temp = explode(',', $urlEncodedWhiteSpaceChars); // turn them into a temp array so we can loop accross
	foreach($temp as $v){$valor  =  str_replace($v, '', $valor);  }
	$valor = urldecode($valor); // undo the url_encode
	// $valor = trim(preg_replace('/\s+/',' ', $valor));

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
	$valor = ltrim($valor," "); // tira a plica do início do texto
	$valor = rtrim($valor," "); // tira a plica do início do texto
	$valor = strip_tags($valor); // tira a plica do início do texto


	if($tipo=='num' && $valor == '') $valor = 0;

	return $valor;
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

	$v = preg_replace('/[^a-zA-Z0-9-_.+*]/', '', $v);

	return $s;
}
