<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
	header('Content-Type: text/html; charset=utf-8');

	// if(!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = $site_http_url;
	$path = dirname(__FILE__) . '/../../core/';
	include $path . 'core_files.php';


	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(0);

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// define('codigo_importacao', 'imp');

	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_values_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('values_name2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_values_lang ADD values_name2 varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_categories_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('category_name2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories_lang ADD category_name2 varchar(255) DEFAULT ""');}

	include dirname(__FILE__) .'/funcoes.inc.php';
	// include dirname(__FILE__) .'/ftp.php';

	$path2 = dirname( __FILE__ ) . '/../../';
	include $path2 . 'admin/products/filtros/funcoes.inc.php';

	function convToUtf8($str, $encoding_detection_types = "UTF-8,ASCII,ISO-8859-15")
	{
		$detect = mb_detect_encoding($str, $encoding_detection_types);
		if($detect && $detect !="UTF-8" )
		{
			return  $str = mb_convert_encoding($str, "UTF-8", $detect);
		}
		else
		{
			return $str;
		}
	}

	$passa = 0;
	$camposObrigatorios = '';
	$contaTotalFicheiro = 0;
	$totalObrigatorios = 0;
	$camposIgnorados = array();

	// TABELA DE CONFIGURACOES
	$obj = getImportadorConfiguracoes();
	if(!$obj->EOF())
	{
		$Marca = stripslashes($obj->db_data['Marca']);
		$MarcaDesc = stripslashes($obj->db_data['MarcaDesc']);

		$Categoria = stripslashes($obj->db_data['Categoria']);
		$CategoriaDesc = stripslashes($obj->db_data['CategoriaDesc']);

		$Sub_Categoria = stripslashes($obj->db_data['Sub_Categoria']);
		$Sub_CategoriaDesc = stripslashes($obj->db_data['Sub_CategoriaDesc']);

		$Sub_Categoria2 = stripslashes($obj->db_data['Sub_Categoria2']);
		$Sub_Categoria2Desc = stripslashes($obj->db_data['Sub_Categoria2Desc']);

		$Codigo_produto = stripslashes($obj->db_data['Codigo_produto']);
		$Produto = stripslashes($obj->db_data['Produto']);
		$Descricao = stripslashes($obj->db_data['Descricao']);
		$Preco_IVA = stripslashes($obj->db_data['Preco_IVA']);
		$EAN = stripslashes($obj->db_data['EAN']);
		$Stock = stripslashes($obj->db_data['Stock']);
		$Imagem1 = stripslashes($obj->db_data['Imagem1']);
		$Imagem2 = stripslashes($obj->db_data['Imagem2']);
		$Imagem3 = stripslashes($obj->db_data['Imagem3']);
		$Imagem4 = stripslashes($obj->db_data['Imagem4']);
		$Separador = stripslashes($obj->db_data['Separador']);
		$Separador2 = htmlentities(stripslashes($obj->db_data['Separador2']));

		$como_importar = $obj->db_data['conf1'];

		$acoes_familias = $obj->db_data['conf2'];
		$acoes_marcas = $obj->db_data['conf3'];

		$Preco_conf = $obj->db_data['Preco_conf'];
		$Preco_iva_valor = $obj->db_data['Preco_iva_valor'];

		$Imagem_conf = $obj->db_data['Imagem_conf'];
		$Imagem_pasta = rtrim(ltrim($obj->db_data['Imagem_pasta'], '/'), '/');

		$ficheiro_caminho = $obj->db_data['ficheiro_caminho'];

		// POR FTP
		$ftp_servidor = $obj->db_data['ftp_servidor'];
		$ftp_porta = $obj->db_data['ftp_porta'];
		$ftp_user = $obj->db_data['ftp_user'];
		$ftp_password = $obj->db_data['ftp_password'];
		$ftp_passivo = $obj->db_data['ftp_passivo'];
		$ficheiro_ftp = $obj->db_data['ficheiro_ftp'];

		$ftp_caminho = rtrim(ltrim($obj->db_data['ftp_caminho'], '/'), '/');
		$ftp_caminho = ($ftp_caminho!='' ? '/'.$ftp_caminho.'/' : '/');

		$Peso = $obj->db_data['Peso'];

		$Descricao2 = $obj->db_data['Descricao2'];
		$Classe_energetica = $obj->db_data['Classe_energetica'];
		$Link_Classe_Energetica = $obj->db_data['Link_Classe_Energetica'];

		$Stock1 = $obj->db_data['Stock1'];
		$Stock2 = $obj->db_data['Stock2'];
		$Stock3 = $obj->db_data['Stock3'];
		$Stock4 = $obj->db_data['Stock4'];

		$codigo_importacao = ($obj->db_data['codigo_importacao'] == '' ? 'IMPNOVA' : $obj->db_data['codigo_importacao']);
		$Eliminar_conf = $obj->db_data['Eliminar_conf'];

		$fora_gama = $obj->db_data['fora_gama'];
		$fora_gama_codigo = $obj->db_data['fora_gama_codigo'];
	}
	else
	{
		die();
	}
	$obj->db_close();

	// QUANDO É PARA ELIMINAR PRODUTOS CASO NAO VENHAM NO FICHEIRO
	if($codigo_importacao !='' && $Eliminar_conf >= 1)
	{
		$sql = 'UPDATE products SET Flag=0 WHERE codigo_importacao = "'.$codigo_importacao.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}

	define('SERVER', $ftp_servidor); //IP o Nome do Servidor
	define('PORT', ($ftp_porta == 0 || $ftp_porta == '' ? 21 : $ftp_porta)); //Porto
	define('USER', $ftp_user); //Nome de Usuário
	define('PASSWORD', $ftp_password); //Senha de acesso
	define('PASV', ($ftp_passivo == 1 ? true : false)); //Ativa modo passivo

	$txt = $file_path."\n";
	$txt .= 'a iniciar a analise...'."\n";

	$myFile = dirname( __FILE__ )."/../../files/importar/analisar_csv.txt";
	if(file_exists($myFile)) die('a importar...'."\n");

	$fh = fopen($myFile, 'a+');
	fwrite($fh, "a comecar analise...\n");

	// FICHEIROS CSV QUE SERÃO PARA IMPORTAR
	foreach (glob($GLOBALS['site_shell_url']."files/importar/importa_manual.csv") as $filename)
	{
		$arr_loks_manual[] = $filename;
	}

	if(count($arr_loks_manual) > 0) // IMPORTAR FICHEIRO MANUAL
	{
		$ficheiro_caminho = 2;
	}
	else // IMPORTAR FICHEIROS AUTO
	{
		// VAMOS BUSCAR FICHEIRO POR FTP
		if($ficheiro_caminho  == 0)
		{
			$img_path = dirname( __FILE__ )."/../../files/importar/";

			fwrite($fh, "a buscar ficheiro ftp...\n");

			$ftp = ConectarFTP();
			if($ftp['estado'] == 1)
			{
				if(PASV) ftp_pasv($ftp['id_ftp'], true);
				ftp_chdir($ftp['id_ftp'], $ftp_caminho);

				$rawList = ftp_nlist($ftp['id_ftp'], '.');

				if(!empty($rawList))
				{
					foreach($rawList as $key => $server_file)
					{
						if(substr_count($ftp_caminho.$server_file, $ficheiro_ftp))
						{
							fwrite($fh, "foi encontrado o ficheiro, comecou a fazer download...\n");
							if(ftp_get2($ftp['id_ftp'], $img_path.$server_file, $ftp_caminho.$server_file, FTP_ASCII))
							{
								fwrite($fh, "download completo...\n");
							}
						}
					}
				}
				else
				{
					fwrite($fh, "sem ficheiros para download ftp...\n");
					fclose($fh);
					if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
					{
						unlink($myFile);
					}
					die();
				}
			}
			else
			{
				if($ftp['estado'] == -1) fwrite($fh, "nao conseguiu ligar ao servidor!\n");
				elseif($ftp['estado'] == 0) fwrite($fh, "nao conseguiu fazer login!\n");
				fclose($fh);

				if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
				{
					unlink($myFile);
				}
				die();
			}
		}
		elseif($ficheiro_caminho  == 1 && $ficheiro_caminho != '')
		{
			$img_path = dirname( __FILE__ )."/../../files/importar/";
			if(copia_segura_imp($ficheiro_caminho, $img_path.'ficheirocsv.csv'))
			{
				// OK
			}
			else
			{
				fwrite($fh, "nao copiou o ficheiro!\n");
				fclose($fh);
				if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
				{
					unlink($myFile);
				}
				die();
			}
		}
	}

	// IMPORTAR NA LINGUAS
	$linguas_importa[] = 'pt';

	// FICHEIROS CSV QUE SERÃO PARA IMPORTAR
	foreach (glob($GLOBALS['site_shell_url']."files/importar/*.csv") as $filename)
	{
		$arr_loks[] = $filename;
	}
	foreach (glob($GLOBALS['site_shell_url']."files/importar/*.CSV") as $filename)
	{
		$arr_loks[] = $filename;
	}

	if(count($arr_loks) <=0) die('nao foram encontrados ficheiros.');

	$file_path = $arr_loks[0];
	$file_path_done = str_replace('importar/', 'importado/', $arr_loks[0]);

	$file = file_get_contents($site_http_url.'files/importar/'.basename($file_path));

	fwrite($fh, "a converter ficheiro...!\n");

	// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
	$encodedOutput = convToUtf8($file);

	// VAMOS GUARDAR O NOVO FICHEIRO
	$file_to_import = str_replace(array('.csv', '.CSV'), '_imp_'.date('Ymd').'.csv', $file_path);
	file_put_contents($file_to_import, $encodedOutput);
	fwrite($fh, "ficheiro convertido!\n");
	if(file_exists($file_to_import))
	{
		$file_path_old = $file_path;
		$file_path_old_done = $file_path_done;

		$file_path = $file_to_import;
		$file_path_done = str_replace('importar/', 'importado/', $file_to_import);
	}

	$total_produtos = 0;
	$file = new SplFileObject($file_path, 'r');
	$file->seek(PHP_INT_MAX);
	$total_produtos = $file->key();

	$txt .= 'Total Produtos: '.$total_produtos."\n";

	$fp = fopen($file_path, "r");

	if($Separador == '')
	{
		fwrite($fh, "Em falta Separador!\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die();
	}

	if($Separador != '' && $Separador2 !='') $data = fgetcsv($fp, '', $Separador, ($Separador2!='' ? $Separador2 : null));
	elseif($Separador!='') $data = fgetcsv($fp, '', $Separador);

	if($fp == null || ($data) === false)
	{
		fwrite($fh, "Ocurreu um erro de leitura!\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die();
	}
	$names = array();
	foreach($data as $field) $names[] = trim($field);

	if(empty($names))
	{
		fwrite($fh, "Nao foi especificadas nome das colunas!\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die();
	}

	$linha = 1;
	$ll = '';
	$ll2 = '';

	$txt .= 'Colunas no csv: '.implode(' | ', $names)."\n";
	fwrite($fh, $txt."\n");
	// print_r($names);

	$ok = 1;
	// VAMOS VERIFICAR SE AS COLUNAS CONFIGURADAS EXISTEM NO FICHEIRO
	if(!in_array($Codigo_produto, $names))
	{
		$txt .= 'Coluna '.$Codigo_produto.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Produto !='' && !in_array($Produto, $names))
	{
		$txt .= 'Coluna '.$Produto.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Descricao!='' && !in_array($Descricao, $names))
	{
		$txt .= 'Coluna '.$Descricao.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Marca !='' && !in_array($Marca, $names))
	{
		$txt .= 'Coluna '.$Marca.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($MarcaDesc !='' && !in_array($MarcaDesc, $names))
	{
		$txt .= 'Coluna '.$MarcaDesc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Categoria !='' && !in_array($Categoria, $names))
	{
		$txt .= 'Coluna '.$Categoria.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($CategoriaDesc !='' && !in_array($CategoriaDesc, $names))
	{
		$txt .= 'Coluna '.$CategoriaDesc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_Categoria !='' && !in_array($Sub_Categoria, $names))
	{
		$txt .= 'Coluna '.$Sub_Categoria.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_CategoriaDesc !='' && !in_array($Sub_CategoriaDesc, $names))
	{
		$txt .= 'Coluna '.$Sub_CategoriaDesc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_Categoria2 !='' && !in_array($Sub_Categoria2, $names))
	{
		$txt .= 'Coluna '.$Sub_Categoria2.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_Categoria2Desc !='' && !in_array($Sub_Categoria2Desc, $names))
	{
		$txt .= 'Coluna '.$Sub_Categoria2Desc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Preco_IVA !='' && !in_array($Preco_IVA, $names))
	{
		$txt .= 'Coluna '.$Preco_IVA.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($EAN !='' && !in_array($EAN, $names))
	{
		$txt .= 'Coluna '.$EAN.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Stock !='' && !in_array($Stock, $names))
	{
		$txt .= 'Coluna '.$Stock.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem1 !='' && !in_array($Imagem1, $names))
	{
		$txt .= 'Coluna '.$Imagem1.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem2 !='' && !in_array($Imagem2, $names))
	{
		$txt .= 'Coluna '.$Imagem2.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem3 !='' && !in_array($Imagem3, $names))
	{
		$txt .= 'Coluna '.$Imagem3.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem4 !='' && !in_array($Imagem4, $names))
	{
		$txt .= 'Coluna '.$Imagem4.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Peso !='' && !in_array($Peso, $names))
	{
		$txt .= 'Coluna '.$Peso.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Descricao2 !='' && !in_array($Descricao2, $names))
	{
		$txt .= 'Coluna '.$Descricao2.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Classe_energetica !='' && !in_array($Classe_energetica, $names))
	{
		$txt .= 'Coluna '.$Classe_energetica.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Link_Classe_Energetica !='' && !in_array($Link_Classe_Energetica, $names))
	{
		$txt .= 'Coluna '.$Link_Classe_Energetica.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($fora_gama !='' && !in_array($fora_gama, $names))
	{
		$txt .= 'Coluna '.$fora_gama.' nao foi encontrada'."\n";
		$ok = 0;
	}

	if($ok == 0)
	{
		fwrite($fh, $txt."\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv.txt', 'analisar_csv_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die();
	}

	fwrite($fh, "a comecar importacao...\n");
	$total_produtos_ativos = array();
	while($data = fgetcsv($fp, '', $Separador, $Separador2))
	{
		$ll = intval(($linha * 100 ) / $total_produtos);
		if($ll != $ll2)
		{
			// fwrite($fh, $ll."%\n");
			$ll2 = $ll;

			echo $ll." | ";
		}

		if(!file_exists($myFile)) die();

		$CODIGO_PRODUTO = ''; $NOME_PRODUTO = '';
		$RESUMO_PRODUTO = ''; $CARACTERISTICAS_PRODUTO = ''; $CLASS_ENERGITICA_PRODUTO = ''; $LINK_CLASS_ENERGITICA_PRODUTO = '';
		$CODIGO_MARCA = ''; $NOME_MARCA = '';
		$CODIGO_CATEGORIA = ''; $NOME_CATEGORIA = '';
		$CODIGO_SUBCATEGORIA = ''; $NOME_SUBCATEGORIA = '';
		$CODIGO_SUBCATEGORIA2 = ''; $NOME_SUBCATEGORIA2 = '';

		$PRECO_PRODUTO = ''; $EAN_PRODUTO = ''; $STOCK_PRODUTO = '';
		$IMAGEM_PRODUTO1 = ''; $IMAGEM_PRODUTO2 = ''; $IMAGEM_PRODUTO3 = ''; $IMAGEM_PRODUTO4 = '';
		$PESO_PRODUTO = '';
		$FORA_GAMA = '';

		if($Codigo_produto != '' && in_array($Codigo_produto, $names)){ $CODIGO_PRODUTO = devolvevalor($data, $Codigo_produto); } // REFERENCIA PRODUTO, OBRIGATORIO

		if($Produto !='' && in_array($Produto, $names)){ $NOME_PRODUTO = devolvevalor($data, $Produto); } // TITULO PRODUTO
		if($Descricao !='' && in_array($Descricao, $names)){ $RESUMO_PRODUTO = devolvevalor($data, $Descricao); } // RESUMO PRODUTO
		if($Descricao2 !='' && in_array($Descricao2, $names)){ $CARACTERISTICAS_PRODUTO = devolvevalor($data, $Descricao2); } // CARACTERISTICAS PRODUTO

		if($Classe_energetica !='' && in_array($Classe_energetica, $names)){ $CLASS_ENERGITICA_PRODUTO = devolvevalor($data, $Classe_energetica); } // CARACTERISTICAS PRODUTO
		if($Link_Classe_Energetica !='' && in_array($Link_Classe_Energetica, $names)){ $LINK_CLASS_ENERGITICA_PRODUTO = devolvevalor($data, $Link_Classe_Energetica); } // CARACTERISTICAS PRODUTO

		if($Marca !='' && in_array($Marca, $names)){ $CODIGO_MARCA = devolvevalor($data, $Marca); } // CODIGO MARCA
		if($MarcaDesc !='' && in_array($MarcaDesc, $names)){ $NOME_MARCA = devolvevalor($data, $MarcaDesc); } // NOME MARCA

		if($Categoria !='' && in_array($Categoria, $names)){ $CODIGO_CATEGORIA = devolvevalor($data, $Categoria); } // CODIGO CATEGORIA - NIVEL 1
		if($CategoriaDesc !='' && in_array($CategoriaDesc, $names)){ $NOME_CATEGORIA = devolvevalor($data, $CategoriaDesc); } // NOME CATEGORIA

		if($Sub_Categoria !='' && in_array($Sub_Categoria, $names)){ $CODIGO_SUBCATEGORIA = devolvevalor($data, $Sub_Categoria); } // CODIGO SUBCATEGORIA - NIVEL 2
		if($Sub_CategoriaDesc !='' && in_array($Sub_CategoriaDesc, $names)){ $NOME_SUBCATEGORIA = devolvevalor($data, $Sub_CategoriaDesc); } // NOME SUBCATEGORIA

		if($Sub_Categoria2 !='' && in_array($Sub_Categoria2, $names)){ $CODIGO_SUBCATEGORIA2 = devolvevalor($data, $Sub_Categoria2); }  // CODIGO SUBCATEGORIA2 - NIVEL 3
		if($Sub_Categoria2Desc !='' && in_array($Sub_Categoria2Desc, $names)){ $NOME_SUBCATEGORIA2 = devolvevalor($data, $Sub_Categoria2Desc); } // NOME SUBCATEGORIA2

		if($Preco_IVA !='' && in_array($Preco_IVA, $names)){ $PRECO_PRODUTO = devolvevalor($data, $Preco_IVA, 'num'); } // PREÇO PRODUTO

		if($EAN !='' && in_array($EAN, $names)){ $EAN_PRODUTO = devolvevalor($data, $EAN); } // EAN PRODUTO

		if($Stock !='' && in_array($Stock, $names)){ $STOCK_PRODUTO = devolvevalor($data, $Stock); $STOCK_PRODUTO = devolve_stock_def($Stock1, $Stock2, $Stock3, $Stock4, $STOCK_PRODUTO);} // STOCK PRODUTO


		$imagem = array();
		if($Imagem1 !='' && in_array($Imagem1, $names)){ $IMAGEM_PRODUTO1 = devolvevalor($data, $Imagem1); if($IMAGEM_PRODUTO1) $imagem[] = $IMAGEM_PRODUTO1; } // IMAGEM 1 PRODUTO
		if($Imagem2 !='' && in_array($Imagem2, $names)){ $IMAGEM_PRODUTO2 = devolvevalor($data, $Imagem2); if($IMAGEM_PRODUTO2) $imagem[] = $IMAGEM_PRODUTO2; } // IMAGEM 2 PRODUTO
		if($Imagem3 !='' && in_array($Imagem3, $names)){ $IMAGEM_PRODUTO3 = devolvevalor($data, $Imagem3); if($IMAGEM_PRODUTO3) $imagem[] = $IMAGEM_PRODUTO3; } // IMAGEM 3 PRODUTO
		if($Imagem4 !='' && in_array($Imagem4, $names)){ $IMAGEM_PRODUTO4 = devolvevalor($data, $Imagem4); if($IMAGEM_PRODUTO4) $imagem[] = $IMAGEM_PRODUTO4; } // IMAGEM 4 PRODUTO

		if($Peso !='' && in_array($Peso, $names)){ $PESO_PRODUTO = devolvevalor($data, $Peso, 'num'); } // PESO PRODUTO

		if($fora_gama !='' && in_array($fora_gama, $names)){ $FORA_GAMA = devolvevalor($data, $fora_gama); } // PESO PRODUTO

		if(
			(trim($CODIGO_PRODUTO) != '' && ($Eliminar_conf == 0 || $Eliminar_conf == 1)) ||  /* PASSA SE EM 'Ações Ficheiro' ESTIVER 'Desativado(0)' ou 'Eliminar Produtos que não vinham no ficheiro (1)'*/
			(trim($CODIGO_PRODUTO) != '' && $Eliminar_conf == 2 && $FORA_GAMA != $fora_gama_codigo) /* PASSA SE EM 'Ações Ficheiro' ESTIVER 'Eliminar Produtos que não vinham no ficheiro e que estejam fora de gama (2)' E O CAMPO FORA DE GAMA NO FICHEIRO FOR IGUAL AO CAMPO EM 'Com Valor'*/
			)
		{
			$prod_fam_id = 0; $fam_id = 0; $subfam_id = 0; $subfam_id2 = 0;

			if($CODIGO_CATEGORIA != '' && $acoes_familias > 0) // INSERE/ATUALIZA
			{
				$prod_fam = detect_family($CODIGO_CATEGORIA, $NOME_CATEGORIA, 0, $acoes_familias); // NIVEL 1
				if($prod_fam['fam_id'] > 0)
				{
					$fam_id = $prod_fam['fam_id'];

					if($CODIGO_SUBCATEGORIA != '')
					{
						$prod_subfam = detect_family($CODIGO_SUBCATEGORIA, $NOME_SUBCATEGORIA, $fam_id, $acoes_familias); // NIVEL 2
						if($prod_fam['fam_id'] > 0)
						{
							$subfam_id = $prod_subfam['fam_id'];

							if($CODIGO_SUBCATEGORIA2 != '')
							{
								$prod_subfam2 = detect_family($CODIGO_SUBCATEGORIA2, $NOME_SUBCATEGORIA2, $subfam_id, $acoes_familias); // NIVEL 3
								$subfam_id2 = $prod_subfam2['fam_id'];
							}
						}
					}
				}

				if($subfam_id2 > 0) $prod_fam_id = $subfam_id2; elseif($subfam_id > 0) $prod_fam_id = $subfam_id; else $prod_fam_id = $fam_id;
			}
			elseif($acoes_familias == 0) // APENAS VAMOS BUSCAR O ID DA FAMILIA
			{
				// echo $CODIGO_SUBCATEGORIA2 .'|'.$CODIGO_SUBCATEGORIA .'|'.$CODIGO_CATEGORIA.'|'.$prod_fam_id."\n";

				// SE NO NIVEL 3 EXISTIR COSDIGO ENTAO O PRODUTO FICA DENTRO
				if($CODIGO_SUBCATEGORIA2 !='' && $prod_fam_id == 0)
				{
					$prod_fam = detect_family($CODIGO_SUBCATEGORIA2, '', 0, $acoes_familias);
					$prod_fam_id = $prod_fam['fam_id'];
				}
				// SE NO NIVEL 2 EXISTIR COSDIGO ENTAO O PRODUTO FICA DENTRO
				if($CODIGO_SUBCATEGORIA !='' && $prod_fam_id == 0)
				{
					$prod_fam = detect_family($CODIGO_SUBCATEGORIA, '', 0, $acoes_familias);
					$prod_fam_id = $prod_fam['fam_id'];
				}
				// SE NO NIVEL 1 EXISTIR COSDIGO ENTAO O PRODUTO FICA DENTRO
				if($CODIGO_CATEGORIA !='' && $prod_fam_id == 0)
				{
					$prod_fam = detect_family($CODIGO_CATEGORIA, '', 0, $acoes_familias);
					$prod_fam_id = $prod_fam['fam_id'];
				}
			}

			// MARCAS
			$marca_id = 0; $marca_nome = '';
			if($CODIGO_MARCA != '') // INSERE/ATUALIZA
			{
				$prod_marca = detect_brand($CODIGO_MARCA, $NOME_MARCA, $acoes_marcas);
				$marca_id = $prod_marca['marca_id'];
				$marca_nome = $prod_marca['marca_nome'];
			}

			if($prod_fam_id > 0 && $marca_id > 0)
			{
				// echo $prod_fam_id.', '.$marca_id."\n";
				$prod_info = detect_product($prod_fam_id, $marca_id, $CODIGO_PRODUTO, $NOME_PRODUTO, $RESUMO_PRODUTO, $CARACTERISTICAS_PRODUTO, $CLASS_ENERGITICA_PRODUTO, $LINK_CLASS_ENERGITICA_PRODUTO, $PESO_PRODUTO, $EAN_PRODUTO, $como_importar, ($Peso !='' ? 1 : 0), $codigo_importacao, $PRECO_PRODUTO);

				// SO ENTRA SE NÃO FOR PARA ELIMINAR
				if($prod_info['prod_id'] > 0 && $como_importar != 3)
				{
					if($prod_fam_id > 0) detect_famprod($prod_info['prod_id'], $prod_fam_id);

					if($Preco_IVA != '')
					{
						if($Preco_conf == 1 && $Preco_iva_valor > 0) // O PREÇO VEM SEM IVA E O CAMPO DO IVA FOI CONFIGURADO
						{
							$PRECO_PRODUTO = $PRECO_PRODUTO * (1+($Preco_iva_valor / 100));
						}
						elseif($Preco_conf == 1 && $Preco_iva_valor == 0) // O PREÇO VEM SEM IVA E O CAMPO DO IVA NÂO FOI CONFIGURADO VOU COLOCAR O IVA DEFAULT
						{
							$PRECO_PRODUTO = $PRECO_PRODUTO * (1+($GLOBALS['iva_default'] / 100));
						}

						detect_products_objs($prod_info['prod_id'], $PRECO_PRODUTO, 0, $STOCK_PRODUTO, $CODIGO_PRODUTO, $EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0));
					}
					else detect_products_objs($prod_info['prod_id'], 0, 0, $STOCK_PRODUTO, $CODIGO_PRODUTO, $EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0));

					// detect_products_objs($ID_PROD=0, $obj_pvp=0, $obj_qtdmin=0, $obj_stock=0, $obj_ref='', $obj_ean='', $tipo_pvp=0, $com_stock=0)
						// echo  $CODIGO_PRODUTO.'|'.$STOCK_PRODUTO."\n";
						// die();

					// INSERÇÃO FILTROS
					$sql = 'UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_info['prod_id'].'"';
					$res = Runquery($db_link, $DB_name, $sql);
					// echo 'zzzzzzzzzzzzzzzzz<br>';
					$sql = " UPDATE products_objs_combo SET obj_val = 0 WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' ";
					$res = Runquery($db_link, $DB_name, $sql);
					$cria_vazio = 0;

					// FILTROS LATERAIS
					/* for($a=0; $a<count($TIPOS_ARR);$a++)
					{
						$tipos_cat = ''; $tipos_cat = str_replace('–', '-', trim($TIPOS_ARR[$a]['Categoria']));
						$tipos_fil = ''; $tipos_fil = str_replace('–', '-', trim($TIPOS_ARR[$a]['Filtro']));

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
					} */

					// exit;
					// Opções de escolha na ficha produto
					/* for($a=0; $a<count($Medidas_ARR);$a++)
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

					} */

					// MARCA
					if($marca_id > 0 && $marca_nome != '')
					{
						$category_id_import_1 = 140; $value_id_import_1 = 0;
						$value_id_import_1 = detect_filter_values(0, $category_id_import_1, $marca_nome, 'pt', $marca_nome);
						if($value_id_import_1 > 0 && $marca_nome != '')
						{
							detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, $category_id_import_1, $value_id_import_1, 0);

							$cria_vazio = 1;
						}
					}

					$sql = "DELETE FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' AND obj_val=0";
					$res = Runquery($db_link, $DB_name, $sql);

					if($cria_vazio == 0) detect_filter_products($prod_info['prod_id'], $prod_fam_id, $marca_id, 0, 0, 0);

					$sql = 'DELETE FROM filters_products WHERE FLAG=0 and prod_id="'.$prod_info['prod_id'].'"';
					$res = Runquery($db_link, $DB_name, $sql);


					$original = '';
					for($z=0; $z<count($imagem);$z++)
					{
						$url = ''; $original = $imagem[$z];

						if($Imagem_conf == 0) // IMAGEM DE URL EXTERNO
						{
							$file = $prod_info['prod_id'].'_'.($z+1).'.jpg'; //file name to write to include location if needed

							$current = file_get_contents($imagem[$z]); // read the created file

							// echo $GLOBALS['site_shell_url'].'files/importar/imagens/'.$file.' | '.$imagem[$z]."\n";
							if(file_put_contents($GLOBALS['site_shell_url'].'files/importar/imagens/'.$file, $current))
							{
								$imagem[$z] = $file;

								if(file_exists(dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z])))  $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]);
							}
						}
						else
						{
							if(file_exists(dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png')) $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png';
							elseif(file_exists(dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg')) $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg';
							elseif(file_exists(dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif')) $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif';
						}

						if($url != '' && file_exists($url))
						{
							$ext = pathinfo($url, PATHINFO_EXTENSION);
							$url_new = dirname( __FILE__ ).'/../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext;

							if($ext != '' && !file_exists(dirname( __FILE__ ).'/../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext))
							{
								@unlink(dirname( __FILE__ ).'/../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext);
								copia_segura($url, $url_new);

								$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
								$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
								$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_info['prod_id'].'", "'.$ext.'", '.($z+1).', "'.addslashes($original).'")');

							}
							/* elseif($ext != '')
							{
								copy($url, $url_new);
								$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
								$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
								$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_info['prod_id'].'", "'.$ext.'", '.($z+1).', "'.addslashes($original).'")');

								// echo 'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",'.($z+1).')'."\n";
							} */
						}
					}

					if($prod_fam_id > 0 && $marca_id > 0)
					{
						gera_produtos($prod_info['prod_id'], $prod_fam_id, $marca_id, 1, 'pt');
						fwrite($fh, $ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO." - importado e ativo no website\n");
						$total_produtos_ativos[] = $prod_info['prod_id'].' - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."\n";
					}
					else
					{
						fwrite($fh, $ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(Produto Sem familia ou marca)\n");
					}
				}
			}
			else
			{
				fwrite($fh, $ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(Produto Sem familia ou marca)\n");
			}
			// if( $CODIGO_PRODUTO == '10409000') die();
		}
		else
		{
			fwrite($fh, $ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(FORA GAMA)\n");
		}

		$linha++;
	}


	fwrite($fh, "99% - a gerar filtros...\n");
	gera_produtos_filtros('pt');


	if($codigo_importacao !='' && $Eliminar_conf >= 1)
	{
		global $DB_host, $DB_user, $DB_pass, $DB_name;
		$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
		if($db_link)
		{
			$sql = 'UPDATE products SET Aproved=0 WHERE Flag=0 AND codigo_importacao = "'.$codigo_importacao.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}

		global $DB_host, $DB_user, $DB_pass, $DB_name;
		$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

		$rs = new DataObj;
		$sql = 'SELECT ID FROM products WHERE Aproved=0 AND codigo_importacao = "'.$codigo_importacao.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$rs->db_data['ID']."_*.json") as $filename)
				{
					@unlink($filename);
				}

				$rs->db_next();
			}
		}
		$rs->db_close();
	}

	fwrite($fh, "100% - Importacao chegou ao fim\n");

	fwrite($fh, "\nTotal de produtos Ativos no website: ".count($total_produtos_ativos)."\n");

	for($a=0; $a<count($total_produtos_ativos); $a++)
	{
		fwrite($fh, "&nbsp;&nbsp; - ".$total_produtos_ativos[$a]."\n");
	}
	// fwrite($fh, "Total de produtos Ativos no website: ".count($total_produtos_ativos)."\n");


	fclose($fh);

	// FICHEIRO DE LOG
	if(copy($myFile, str_replace('importar/', 'importado/', str_replace('analisar_csv.txt', 'analisar_csv_ok'.date('YmdHis').'.txt', $myFile))))
	{
		unlink($myFile);
	}

	if($ficheiro_caminho == 2) // MANUAL
	{
		if(copy(dirname(__FILE__) .'/../../files/importar/importa_manual.csv',  dirname(__FILE__) .'/../../files/importado/importa_manual_'.date('YmdHis').'.csv'))
		{
			unlink(dirname(__FILE__) .'/../../files/importar/importa_manual.csv');
		}
	}

	if(copy($file_path, $file_path_done)) unlink($file_path);
	if(copy($file_path_old, $file_path_old_done)) unlink($file_path_old);


function ConectarFTP()
{
	// PERMITE SE CONECTAR AO SERVIDOR FTP
	$return = 0;
	$id_ftp = ftp_connect(SERVER, PORT); //or die("Não consegui ligacao com ".SERVER);

	if(!$id_ftp) $return = -1;

	// TRY TO LOGIN
	if (@ftp_login($id_ftp, USER, PASSWORD))
	{
		$return = 1;
	}
	else
	{
		$return = 0;
	}

	if(PASV == true) ftp_pasv($id_ftp, MODO); //Estabelece o modo de conexão

	return array('id_ftp' => $id_ftp, 'estado' => $return); //Devolve o manejador à função
}
function ftp_get2( $ftp, $local_file, $server_file, $tipo_ftp)
{
	return ftp_get($ftp, $local_file, $server_file, $tipo_ftp);
}
function copia_segura_imp($image, $dest)
{
	//Vamos verificar qual o tipo de ficheiro:
	$original_extension=pathinfo($dest, PATHINFO_EXTENSION);
	$dest2=str_replace('.'.$original_extension, '', $dest);
	// if($original_extension == 'jpg'){ return copy($image, $dest2.'.jpg'); }
	// if($original_extension == 'JPG'){ return copy($image, $dest2.'.JPG'); }
	// if($original_extension == 'png'){ return copy($image, $dest2.'.png'); }
	// if($original_extension == 'PNG'){ return copy($image, $dest2.'.PNG'); }
	// if($original_extension == 'gif'){ return copy($image, $dest2.'.gif'); }
	// if($original_extension == 'GIF'){ return copy($image, $dest2.'.GIF'); }
	// if($original_extension == 'pdf'){ return copy($image, $dest2.'.pdf'); }
	// if($original_extension == 'PDF'){ return copy($image, $dest2.'.PDF'); }
	// if($original_extension == 'doc'){ return copy($image, $dest2.'.doc'); }
	// if($original_extension == 'DOC'){ return copy($image, $dest2.'.DOC'); }
	// if($original_extension == 'docx'){ return copy($image, $dest2.'.docx'); }
	// if($original_extension == 'DOCX'){ return copy($image, $dest2.'.DOCX'); }
	if($original_extension == 'csv'){ return copy($image, $dest2.'.csv'); }
	if($original_extension == 'CSV'){ return copy($image, $dest2.'.CSV'); }
	// if($original_extension == 'txt'){ return copy($image, $dest2.'.txt'); }
	// if($original_extension == 'TXT'){ return copy($image, $dest2.'.TXT'); }
	// if($original_extension == 'xml'){ return copy($image, $dest2.'.xml'); }
	// if($original_extension == 'XML'){ return copy($image, $dest2.'.XML'); }
	// if($original_extension == 'xls'){ return copy($image, $dest2.'.xls'); }
	// if($original_extension == 'xlsx'){ return copy($image, $dest2.'.xlsx'); }
	// if($original_extension == 'XLS'){ return copy($image, $dest2.'.XLS'); }
	// if($original_extension == 'XLSX'){ return copy($image, $dest2.'.XLSX'); }
	return false;
}
function devolvevalor($data, $campo, $tipo="")
{
	// global $names;
	$valor = $data[array_search($campo, $GLOBALS['names'])];
	if($tipo=="dt") $valor = convert_data($valor);
	elseif($tipo=="num")
	{
		$valor = str_replace("€","",$valor);
		$valor = str_replace(".","",$valor);
		$valor = str_replace(",",".",$valor);
		// if($ItemCode == 'DRE.C2.EXOTIC') echo 'xxx '.$valor.' xxx'."\n";
		$valor = floatval($valor);
		// $valor = convert_number($valor);
	}
	elseif($tipo == "perc") $valor = str_replace("%", "", $valor);

	$valor = rtrim($valor); // tira espaços no fim
	$valor = trim($valor,"'"); // tira a plica do início do texto

	if($tipo=="num" && $valor == '') $valor = 0;

	return $valor;
}
function log_error($txt)
{
	echo $txt.'\n';
	$myFile = dirname( __FILE__ )."/../../files/importar/log_error_".date('YmdHis').".txt";
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $txt);
	fclose($fh);

	// if(copy($GLOBALS['file_path'], $GLOBALS['file_path_done']))
	// {
		// @unlink($GLOBALS['file_path']);
	// }
	// @unlink(dirname( __FILE__ )."/../files/importar/atualizar.txt");
}


function devolve_stock_def($st1, $st2, $st3, $st4, $valor)
{
	if($st1 != '' && $valor == $st1) return 0;
	elseif($st2 != '' && $valor == $st2) return 1;
	elseif($st3 != '' && $valor == $st3) return 2;
	elseif($st4 != '' && $valor >= $st4) return 3;
	else return 0;
}

function devolve_etiqueta_cor_def($st1)
{
	if($st1 == 'A+++') return '#00A54F';
	elseif($st1 == 'A++') return '#4CB849';
	elseif($st1 == 'A+') return '#BDD632';
	elseif($st1 == 'A') return '#FFF102';
	elseif($st1 == 'B') return '#4CB849';
	elseif($st1 == 'C') return '#F36E21';
	elseif($st1 == 'D') return '#EE1D23';
	elseif($st1 == 'E') return '#FDB815';
	elseif($st1 == 'F') return '#EE1D23';
	else return '';

}
function detect_products_objs($ID_PROD=0, $obj_pvp=0, $obj_qtdmin=0, $obj_stock=0, $obj_ref='', $obj_ean='', $tipo_pvp=0, $com_stock=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;


	$sql = 'SELECT obj_id FROM products_objs WHERE obj_type_id = "'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$obj_id =  $rs->db_data['obj_id'];

		$fields = '';
		if($com_stock == 1) $fields .= ', obj_stock="'.$obj_stock.'"';
		// if($obj_ean != '') $fields .= ', Ref_Ean="'.$obj_ean.'"';

		$sql = 'UPDATE products_objs SET obj_type="products", obj_pvp="'.$obj_pvp.'", obj_qtdmin="'.$obj_qtdmin.'" '.$fields.' WHERE obj_type_id="'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
		$res = Runquery($db_link, $DB_name, $sql);
		// echo $sql."\n";
		$sql = 'UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang = "pt"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$fields_c = '';
		$fields_v = '';
		if($com_stock == 1)
		{
			$fields_c .= ', obj_stock';
			$fields_v .= ', "'.$obj_stock.'"';
		}
		// if($obj_ean != '')
		// {
			// $fields_c .= ', Ref_Ean';
			// $fields_v .= ', "'.$obj_ean.'"';
		// }

		$sql = 'INSERT INTO products_objs (obj_type_id, obj_ativo, obj_pvp, obj_qtdmin, obj_type, obj_val2'.$fields_c.') VALUES ("'.$ID_PROD.'", 1, "'.$obj_pvp.'", "'.$obj_qtdmin.'", "products", "'.$tipo_pvp.'"'.$fields_v.')';
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
function detect_family($codigo_importacao, $ItemDesc='', $FAMID = 0, $acoes_familia = 0)
{
	$fam_id = 0;
	if($codigo_importacao != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID, ID_ORG FROM products_families WHERE codigo_importacao LIKE "%;'.limpa_special_sync($codigo_importacao).';%"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$fam_id = $rs->db_data['ID'];

			if($acoes_familia == 1 || $acoes_familia == 3)
			{
				if($FAMID > 0) $sql = 'UPDATE products_families SET ID_ORG="'.$FAMID.'", FLAG=1, visivel=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';
				else $sql = 'UPDATE products_families SET FLAG=1, visivel=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';

				$res = Runquery($db_link, $DB_name, $sql);

				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					if(trim($ItemDesc) != '') detect_family_lang($fam_id, $ItemDesc, $GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		elseif($acoes_familia == 1 || $acoes_familia == 2)
		{
			$sql = 'INSERT INTO products_families (ID_ORG, visivel, data_atualizacao, codigo_importacao, FLAG) VALUES ("'.$FAMID.'", 1,"'.date('Y-m-d H:i:s').'", ";'.limpa_special_sync($codigo_importacao).';", 1)';
			$res = Runquery($db_link, $DB_name, $sql);
			$fam_id = db_get_last_insert_id($db_link);
			if($fam_id > 0)
			{
				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					if(trim($ItemDesc) != '') detect_family_lang($fam_id, $ItemDesc, $GLOBALS['linguas_importa'][$a]);
					else  detect_family_lang($fam_id, $codigo_importacao, $GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		$rs->db_close();

		return array('fam_id' => $fam_id);
	}
	return array();
}

function detect_family_lang($fam_id, $ItemDesc, $lingua)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;

	$sql = 'SELECT ID FROM products_families_lang WHERE ID_PAI = "'.$fam_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$sql = 'UPDATE LOW_PRIORITY products_families_lang SET Cabecalho="'.addslashes($ItemDesc).'" WHERE ID_PAI="'.$fam_id .'" and Lang="'.$lingua.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	else
	{
		$sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'", "'.addslashes($ItemDesc).'", "'.$lingua.'")';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	$rs2->db_close();
}

function detect_brand($codigo_importacao='', $ItemDesc = '', $acoes_marcas = 0)
{
	$marca_id = 0;
	if($codigo_importacao != '')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID FROM brands WHERE codigo_importacao LIKE "%;'.limpa_special_sync($codigo_importacao).';%"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$marca_id = $rs->db_data['ID'];

			if($acoes_marcas == 1 || $acoes_marcas == 2)
			{
				$sql = 'UPDATE brands SET data_atualizacao="'.date('Y-m-d H:i:s').'", FLAG=1 WHERE ID = "'.$marca_id.'"';
				$res = Runquery($db_link, $DB_name, $sql);

				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					if($ItemDesc != '') detect_brand_lang($marca_id, $ItemDesc, $GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		elseif($acoes_marcas == 1 || $acoes_marcas == 2)
		{
			$sql = 'INSERT INTO brands (visivel, codigo_importacao, data_atualizacao, FLAG) VALUES (1, ";'.limpa_special_sync($codigo_importacao).';", "'.date('Y-m-d H:i:s').'", 1)';
			$res = Runquery($db_link, $DB_name, $sql);
			$marca_id = db_get_last_insert_id($db_link);
			if($marca_id > 0)
			{
				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					if($ItemDesc != '') detect_brand_lang($marca_id, $ItemDesc, $GLOBALS['linguas_importa'][$a]);
					else detect_brand_lang($marca_id, $codigo_importacao, $GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		$rs->db_close();

		$marca_nome = '';
		if($marca_id > 0)
		{
			$sql = 'SELECT Nome FROM brands_lang WHERE ID_PAI = "'.$marca_id.'" AND Lang="'.$GLOBALS['linguas_importa'][0].'"';
			$rs2->db_open($db_link, $DB_name, $sql);
			if(!$rs2->EOF()) $marca_nome = $rs2->db_data['Nome'];
			$rs->db_close();

			$sql = 'UPDATE brands SET visivel=1 WHERE ID = "'.$marca_id.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}

		return array('marca_id' => $marca_id, 'marca_nome' => $marca_nome);
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

// $prod_info = detect_product($prod_fam_id, $marca_id, $CODIGO_PRODUTO, $NOME_PRODUTO, $RESUMO_PRODUTO, $CARACTERISTICAS_PRODUTO, $CLASS_ENERGITICA_PRODUTO, $LINK_CLASS_ENERGITICA_PRODUTO, $PESO_PRODUTO, $EAN_PRODUTO, $como_importar, ($Peso !='' ? 1 : 0));

function detect_product($prod_fam_id=0, $marca_id=0, $ItemCode='', $ItemNome='', $ItemDesc='', $ItemDesc2='', $ItemClass='', $ItemClassLink='', $ItemPeso=0, $EAN_PRODUTO='', $tipo_importacao = 0, $tem_peso = 0, $codigo_importacao='', $preco = 0)
{
	$prod_id = 0; $cond = ''; $cond2 = '';
	if($ItemCode != '' && $tipo_importacao != 3)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;

		$sql = 'SELECT ID FROM products WHERE Ref = "'.$ItemCode.'" AND codigo_importacao="'.$codigo_importacao.'" ORDER BY ID ASC';
		$rs->db_open($db_link, $DB_name, $sql);
		$total = $rs->db_rows;
		if($total > 1)
		{
			$sql = 'DELETE FROM products WHERE ID = "'.$rs->db_data['ID'].'" AND codigo_importacao="'.$codigo_importacao.'"';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		$rs->db_close();

		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;
		// Preco1
		$sql = 'SELECT ID, ID_ORG FROM products WHERE Ref = "'.$ItemCode.'"';
		$rs->db_open($db_link, $DB_name, $sql);

		if(!$rs->EOF())
		{
			$prod_id = $rs->db_data['ID'];

			if($tipo_importacao == 0 || $tipo_importacao == 2)  // ATUALIZAR PRODUTO
			{
				if($EAN_PRODUTO != '') $cond .= ', Ref_Ean="'.$EAN_PRODUTO.'"';

				if($ItemClass!='')
				{
					$cond .= ', u_catetiq="'.addslashes($ItemClass).'"';
					$cond .= ', u_coretiq="'.devolve_etiqueta_cor_def($ItemClass).'"';
				}
				if($ItemClassLink != '')
				{
					if(!file_exists(dirname(__FILE__) . '/../../files/products/EE/'.$prod_id.'_1.jpg'))
					{
						if(copia_segura($ItemClassLink, dirname(__FILE__) . '/../../files/products/EE/'.$prod_id.'_1.jpg'))
						{
							$cond .= ', u_imagetiq="'.addslashes($prod_id.'_1.jpg').'"';
						}
					}
				}
				// if($obj_ean != '') $fields .= ', Ref_Ean="'.$obj_ean.'"';

				$sql = 'UPDATE products SET codigo_importacao="'.$codigo_importacao.'", ID_ORG="'.$prod_fam_id.'", ID_Marca="'.$marca_id.'", Flag=1, Aproved=1, Carrinho=1'.($tem_peso == 1 ? ', peso_cliente="'.$ItemPeso.'"' : '').$cond.' WHERE Ref = "'.$ItemCode.'"';
				$res = Runquery($db_link, $DB_name, $sql);

				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					detect_product_lang($prod_id, $ItemNome, $ItemDesc, $ItemDesc2, $GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		elseif($tipo_importacao == 0 || $tipo_importacao == 1)  // INSERIR PRODUTO
		{
			if($ItemClass!='')
			{
				$cond  .= ', u_catetiq';
				$cond  .= ', u_coretiq';
			}
			if($ItemClass!='')
			{
				$cond2 .= ', "'.addslashes($ItemClass).'"';
				$cond2 .= ', "'.devolve_etiqueta_cor_def($ItemClass).'"';
			}

			if($EAN_PRODUTO != '')
			{
				$cond .= ', Ref_Ean';
				$cond2 .= ', "'.$EAN_PRODUTO.'"';
			}

			if($prod_fam_id > 0) $sql = 'INSERT INTO products(codigo_importacao, Ref, ID_ORG, ID_Marca, Data_criacao, Aproved, Carrinho, Flag'.($tem_peso == 1 ? ', peso_cliente' : '').$cond .') VALUES ("'.$codigo_importacao.'", "'.$ItemCode.'", "'.$prod_fam_id.'", "'.$marca_id.'", "'.date('Y-m-d H:i:s').'", 1, 1, 1'.($tem_peso == 1 ? ', "'.$ItemPeso.'"' : '').$cond2.')';
			else $sql = 'INSERT INTO products(codigo_importacao, Ref, ID_ORG, ID_Marca, Data_criacao, Aproved, Carrinho, Flag'.($tem_peso == 1 ? ', peso_cliente' : '').$cond.') VALUES ("'.$codigo_importacao.'", "'.$ItemCode.'", "'.$prod_fam_id.'", "'.$marca_id.'", "'.date('Y-m-d H:i:s').'", 0, 1, 1'.($tem_peso == 1 ? ', "'.$ItemPeso.'"' : '').$cond2.')';
			$res = Runquery($db_link, $DB_name, $sql);
			$prod_id = db_get_last_insert_id($db_link);
			if($prod_id > 0)
			{
				$cond = '';
				if($ItemClassLink != '')
				{
					if(!file_exists(dirname(__FILE__) . '/../../files/products/EE/'.$prod_id.'_1.jpg'))
					{
						if(copia_segura($ItemClassLink, dirname(__FILE__) . '/../../files/products/EE/'.$prod_id.'_1.jpg'))
						{
							$cond .= ', u_imagetiq="'.addslashes($prod_id.'_1.jpg').'"';
						}

						$sql = 'UPDATE products SET codigo_importacao="'.$codigo_importacao.'"'.$cond.' WHERE Ref = "'.$ItemCode.'"';
						$res = Runquery($db_link, $DB_name, $sql);
					}
				}

				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					detect_product_lang($prod_id, $ItemNome, $ItemDesc, $ItemDesc2, $GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		$rs->db_close();

		return array('prod_id' => $prod_id);
	}
	elseif($tipo_importacao == 3) // ELIMINAR PRODUTO
	{
		global $DB_host, $DB_user, $DB_pass, $DB_name;
		$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

		$sql = 'DELETE FROM products WHERE Ref = "'.$ItemCode.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}

	return array();
}

function detect_product_lang($prod_id, $ItemDesc, $ExtDesc, $ExtDesc2, $lingua)
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
		if($ExtDesc2!='') $cond[] = 'Texto3="'.addslashes($ExtDesc2).'"';

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
		if($ExtDesc2!=''){ $cond[] = 'Texto3';  $cond2[] = '"'.addslashes($ExtDesc2).'"'; }

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
				value_id_import_ref="'.limpa_special_sync($category_name).'"
			';
	$rs2->db_open($db_link,$DB_name, $sql);
	if(!$rs2->EOF())
	{
		$category_id = $rs2->db_data['category_id'];
		$sql = 'UPDATE filters_categories SET category_active=1, category_flag=1 WHERE value_id_import_ref="'.limpa_special_sync($category_name).'"';
		$res = Runquery($db_link, $DB_name, $sql);

		$sql = '
			SELECT
				category_id
			FROM
				filters_categories_lang
			WHERE
				value_id_import_ref="'.limpa_special_sync($category_name).'"
				AND category_lang="'.$lingua.'"
			';
			$rs3->db_open($db_link,$DB_name, $sql);
			if(!$rs2->EOF())
			{
				$sql = 'UPDATE filters_categories_lang SET category_name="'.addslashes($category_name).'" WHERE value_id_import_ref="'.limpa_special_sync($category_name).'" AND category_lang="'.$lingua.'"';
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
