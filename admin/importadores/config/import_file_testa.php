<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
	header('Content-Type:text/html;charset=utf-8');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	$path = dirname(__FILE__).'/../../../core/';
	include $path.'core_files.php';
	require_once('settings_crons.inc.php');
	require_once('funcoes.inc.php');

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_values_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('values_name2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_values_lang ADD values_name2 varchar(255) DEFAULT ""');}
	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_categories_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('category_name2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories_lang ADD category_name2 varchar(255) DEFAULT ""');}

	$path2 = dirname( __FILE__ ).'/../../../';
	include $path2.'admin/products/filtros/funcoes.inc.php';
	function convToUtf8($str, $encoding_detection_types = "UTF-8,ASCII,ISO-8859-15")
	{

		if(!mb_check_encoding($str, 'UTF-8') OR !($str === mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32')))
		{
			$detect = mb_detect_encoding($str, $encoding_detection_types);
		    $str = mb_convert_encoding($str, 'UTF-8', $detect);
		}
		return $str;

		//$str = mb_convert_encoding($str, "Windows-1252", "UTF-8");
		/*$detect = mb_detect_encoding($str, $encoding_detection_types);
		if($detect && $detect !="UTF-8" )
		{
			return  $str = mb_convert_encoding($str, "UTF-8", $detect);
		}
		else
		{
			return $str;
		}*/
	}
	$passa = 0;
	$camposObrigatorios = '';
	$contaTotalFicheiro = 0;
	$totalObrigatorios = 0;
	$camposIgnorados = array();
	// TABELA DE CONFIGURACOES
	$obj = getImportadorConfiguracoes_2($Supplier_ID);
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
		$log_activation = $obj->db_data['log_activation'];
		$marcas_atraves_nome=$obj->db_data['marcas_atraves_nome'];

		$codigo_fam_default=$obj->db_data['codigo_fam_default'];
		$como_importar = $obj->db_data['conf1'];
		$acoes_familias = $obj->db_data['conf2'];
		$acoes_marcas = $obj->db_data['conf3'];
		$acoes_visivel = $obj->db_data['conf4'];
		$Preco_conf = $obj->db_data['Preco_conf'];
		$Preco_iva_valor = $obj->db_data['Preco_iva_valor'];
		$Imagem_conf = $obj->db_data['Imagem_conf'];
		$Imagem_pasta = rtrim(ltrim($obj->db_data['Imagem_pasta'], '/'), '/');
		$ficheiro_caminho = $obj->db_data['ficheiro_caminho'];
		$ficheiro_caminho_json = $obj->db_data['ficheiro_caminho_json'];
		$ficheiro_caminho_api = $obj->db_data['ficheiro_caminho_api'];
		$ficheiro_caminho_api_login = $obj->db_data['ficheiro_caminho_api_login'];
		$ficheiro_caminho_api_pass = $obj->db_data['ficheiro_caminho_api_pass'];
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
		$price_conf  = $obj->db_data['price_conf'];
		$key_field = $obj->db_data['key_field'];

		$type_importacao = $obj->db_data['tipo_importacao'];


	}
	else
	{
		die("parou - erro a obter configuracoes");
	}
	$obj->db_close();
	// QUANDO É PARA ELIMINAR PRODUTOS CASO não VENHAM NO FICHEIRO
	if($codigo_importacao !='' && $Eliminar_conf >= 1)
	{
		$sql = 'UPDATE products SET Flag=0 WHERE codigo_importacao = "'.$codigo_importacao.'"';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	if($type_importacao==0)//tipo ftp
	{
		define('SERVER', $ftp_servidor); //IP o Nome do Servidor
		define('PORT', ($ftp_porta == 0 || $ftp_porta == '' ? 21 : $ftp_porta)); //Porto
		define('USER', $ftp_user); //Nome de Usuário
		define('PASSWORD', $ftp_password); //Senha de acesso
		define('PASV', ($ftp_passivo == 1 ? true : false)); //Ativa modo passivo
	}




	echo 'a iniciar a analise...'."<br>";

	// VAMOS BUSCAR FICHEIRO POR FTP
	if($type_importacao==0)
	{
		$img_path = dirname( __FILE__ )."/../../../files/importar/";
		echo "a iniciar o download do ficheiro ftp...<br>";
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
					echo $server_file."<br>";
					if(substr_count($ftp_caminho.$server_file, $ficheiro_ftp))
					{

						if(substr_count($server_file,'csv')>0)$new_name_file='auto'.$Supplier_ID.'.csv';
						if(substr_count($server_file,'CSV')>0)$new_name_file='auto'.$Supplier_ID.'.csv';
						echo "foi encontrado o ficheiro $server_file, comecou a fazer download para $new_name_file...<br>";


						if($res=ftp_get2($ftp['id_ftp'], $img_path.$new_name_file, $ftp_caminho.$server_file, FTP_ASCII))
						{
							echo "download completo...<br>";
						}
						else
						{
							echo "erro download falhado... de: $img_path.$new_name_file para:  $ftp_caminho.$server_file <br>";
						}
					}
				}
			}
			else
			{
				echo "sem ficheiros para download ftp...<br>";
				fclose($fh);
				if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt', $myFile)))
				{
					unlink($myFile);
				}
				die("parou - sem fxs para download");
			}
		}
		else
		{
			if($ftp['estado'] == -1) echo "nao conseguiu ligar ao servidor!<br>";
			elseif($ftp['estado'] == 0) echo "nao conseguiu fazer login!<br>";
			fclose($fh);
			if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt', $myFile)))
			{
				unlink($myFile);
			}
			die("parou - não conseguio ligar ftp");
		}
	}
	elseif($type_importacao==1)//URL - Download através de url externo(ficheiro .csv)
	{
		$img_path = dirname( __FILE__ )."/../../../files/importar/";
		echo "a fazer download do ficheiro a partir de URL $ficheiro_caminho !<br>";
		$file = get_web_file($ficheiro_caminho);
		// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
		$encodedOutput = convToUtf8($file);
		// VAMOS GUARDAR O NOVO FICHEIRO
		$file_to_import = 'web'.$Supplier_ID.'.csv';
		file_put_contents($img_path.$file_to_import,$encodedOutput);
		if(!file_exists($img_path.$file_to_import))die("parou - não copiou o fx $img_path.$file_to_import");
		echo "ficheiro gravado com sucesso!<br>";

		$arr_loks[] = $img_path.$file_to_import;
	}
	elseif($type_importacao==2)//URL - Download através de url externo(ficheiro .json)
	{
		$img_path = dirname( __FILE__ )."/../../../files/importar/";
		echo "a fazer download do ficheiro a partir de URL $ficheiro_caminho_json !<br>";
		$file = get_web_file($ficheiro_caminho_json);
		// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
		$encodedOutput = convToUtf8($file);
		// VAMOS GUARDAR O NOVO FICHEIRO
		$file_to_import = 'web'.$Supplier_ID.'.json';

		file_put_contents($img_path.$file_to_import,$encodedOutput);
		if(!file_exists($img_path.$file_to_import))die("parou - não copiou o fx $img_path.$file_to_import");

		echo "ficheiro gravado com sucesso em formato json!<br>";
		echo "a converter fx para csv!<br>";
		//Decode the JSON and convert it into an associative array.
		$jsonDecoded = json_decode($encodedOutput, true);
		//Give our CSV file a name.
		$csvFileName = 'web'.$Supplier_ID.'.csv';
		//Open file pointer.
		$fp2 = fopen($img_path.$csvFileName, 'w');
		//Loop through the associative array.
		$firstLineKeys = false;
		foreach($jsonDecoded as $line){
		    //Write the row to the CSV file.
		    if (empty($firstLineKeys))
			{
				$firstLineKeys = array_keys($line);
				fputcsv($fp2, $firstLineKeys, ";");
				$firstLineKeys = array_flip($firstLineKeys);
			}
			// Using array_merge is important to maintain the order of keys acording to the first element
			fputcsv($fp2, array_merge($firstLineKeys, $line), ";");
		}
		//Finally, close the file pointer.
		fclose($fp2);
		echo "a gravar fx convertido para csv!<br>";
		$arr_loks[] = $img_path.$csvFileName;

		unlink($img_path.$file_to_import);
		$file_to_import=$csvFileName;
	}
	elseif($type_importacao==3)//URL - WEB API
	{
		if(!$ficheiro_caminho_api_login)echo 'erro !! não existe url configurada';
		else
		{
			$timeout = 5;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ficheiro_caminho_api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "$ficheiro_caminho_api_login:$ficheiro_caminho_api_pass");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$data = curl_exec($ch);
			$info = curl_getinfo($ch);
			if (curl_errno($ch))
			{
		    	$error_msg = curl_error($ch);
		    	echo 'Erro !!! '.$error_msg;
			}
			else echo 'Ligação OK';
			curl_close($ch);

			$encodedOutput = convToUtf8($data);
			// VAMOS GUARDAR O NOVO FICHEIRO
			$file_to_import = 'web'.$Supplier_ID.'.csv';
			file_put_contents($img_path.$file_to_import,$encodedOutput);
			if(!file_exists($img_path.$file_to_import))die("parou - não copiou o fx $img_path.$file_to_import");
			echo "ficheiro gravado com sucesso!<br>";

			$arr_loks[] = $img_path.$file_to_import;


		}
	}
	elseif($ficheiro_caminho  == 1 && $ficheiro_caminho != '')
	{
		$img_path = dirname( __FILE__ )."/../../../files/importar/";
		if(copia_segura_imp($ficheiro_caminho, $img_path.'ficheirocsv'.$Supplier_ID.'.csv'))
		{
			// OK
		}
		else
		{
			echo "nao copiou o ficheiro!<br>";
			fclose($fh);
			if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt', $myFile)))
			{
				unlink($myFile);
			}
			die("parou - não copiou o fx");
		}
	}

	// IMPORTAR NA LINGUAS
	$linguas_importa[] = 'pt';
	// FICHEIROS CSV QUE SERÃO PARA IMPORTAR
	foreach (glob($GLOBALS['site_shell_url']."files/importar/auto".$Supplier_ID."*.csv") as $filename)
	{
		$arr_loks[] = $filename;
	}
	foreach (glob($GLOBALS['site_shell_url']."files/importar/auto".$Supplier_ID."*.CSV") as $filename)
	{
		$arr_loks[] = $filename;
	}
	if(count($arr_loks) <=0) die('nao foram encontrados ficheiros.');
	$file_path = $arr_loks[0];



	$file = file_get_contents($site_http_url.'files/importar/'.basename($file_path));
	echo "a converter ficheiro...!".$site_http_url.'files/importar/'.basename($file_path)."<br>";
	// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER

	$encodedOutput = convToUtf8($file);

	// VAMOS GUARDAR O NOVO FICHEIRO
	$file_to_import = str_replace(array('.csv', '.CSV'), '_imp_'.$Supplier_ID.'_'.date('Ymd').'.csv', $file_path);
	file_put_contents($file_to_import, $encodedOutput);
	echo "ficheiro convertido!<br>";

	$total_produtos = 0;
	$file = new SplFileObject($file_path, 'r');
	$file->seek(PHP_INT_MAX);
	$total_produtos = $file->key();
	$txt .= 'Total Produtos: '.$total_produtos."<br>\n";
	$fp = fopen($file_path, "r");
	if($Separador == '')
	{
		echo "Em falta Separador!<br>";
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die("parou - Em falta Separador");
	}
	if($Separador != '' && $Separador2 !='') $data = fgetcsv($fp, '', $Separador, ($Separador2!='' ? $Separador2 : null));
	elseif($Separador!='') $data = fgetcsv($fp, '', $Separador);
	if($fp == null || ($data) === false)
	{
		echo "Ocorreu um erro de leitura!<br>";
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die("parou - Ocorreu um erro de leitura!<br>");
	}





	$names = array();
	foreach($data as $field) $names[] = trim(convToUtf8($field));
	if(empty($names))
	{
		echo "Nao foi especificadas nome das colunas!<br>";
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt', $myFile)))
		{
			unlink($myFile);
		}
		die("parou - não foi especificadas nome das colunas!");
	}
	$linha = 1;
	$ll = '';
	$ll2 = '';
	$txt .= 'Colunas no csv: '.implode(' | ', $names)."<br>\n";
	fwrite($fh, $txt."<br>");
	// print_r($names);
	$ok = 1;
	// VAMOS VERIFICAR SE AS COLUNAS CONFIGURADAS EXISTEM NO FICHEIRO
	if(!in_array($Codigo_produto, $names))
	{
		$txt .= 'Coluna '.$Codigo_produto.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Produto !='' && !in_array($Produto, $names))
	{
		$txt .= 'Coluna '.$Produto.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Descricao!='' && !in_array($Descricao, $names))
	{
		$txt .= 'Coluna '.$Descricao.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Marca !='' && !in_array($Marca, $names))
	{
		$txt .= 'Coluna '.$Marca.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($MarcaDesc !='' && !in_array($MarcaDesc, $names))
	{
		$txt .= 'Coluna '.$MarcaDesc.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Categoria !='' && !in_array($Categoria, $names))
	{
		$txt .= 'Coluna '.$Categoria.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($CategoriaDesc !='' && !in_array($CategoriaDesc, $names))
	{
		$txt .= 'Coluna '.$CategoriaDesc.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Sub_Categoria !='' && !in_array($Sub_Categoria, $names))
	{
		$txt .= 'Coluna '.$Sub_Categoria.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Sub_CategoriaDesc !='' && !in_array($Sub_CategoriaDesc, $names))
	{
		$txt .= 'Coluna '.$Sub_CategoriaDesc.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Sub_Categoria2 !='' && !in_array($Sub_Categoria2, $names))
	{
		$txt .= 'Coluna '.$Sub_Categoria2.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Sub_Categoria2Desc !='' && !in_array($Sub_Categoria2Desc, $names))
	{
		$txt .= 'Coluna '.$Sub_Categoria2Desc.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Preco_IVA !='' && !in_array($Preco_IVA, $names))
	{
		$txt .= 'Coluna '.$Preco_IVA.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($EAN !='' && !in_array($EAN, $names))
	{
		$txt .= 'Coluna '.$EAN.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Stock !='' && !in_array($Stock, $names))
	{
		$txt .= 'Coluna '.$Stock.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Imagem1 !='' && !in_array($Imagem1, $names))
	{
		$txt .= 'Coluna '.$Imagem1.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Imagem2 !='' && !in_array($Imagem2, $names))
	{
		$txt .= 'Coluna '.$Imagem2.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Imagem3 !='' && !in_array($Imagem3, $names))
	{
		$txt .= 'Coluna '.$Imagem3.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Imagem4 !='' && !in_array($Imagem4, $names))
	{
		$txt .= 'Coluna '.$Imagem4.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Peso !='' && !in_array($Peso, $names))
	{
		$txt .= 'Coluna '.$Peso.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Descricao2 !='' && !in_array($Descricao2, $names))
	{
		$txt .= 'Coluna '.$Descricao2.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Classe_energetica !='' && !in_array($Classe_energetica, $names))
	{
		$txt .= 'Coluna '.$Classe_energetica.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($Link_Classe_Energetica !='' && !in_array($Link_Classe_Energetica, $names))
	{
		$txt .= 'Coluna '.$Link_Classe_Energetica.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($fora_gama !='' && !in_array($fora_gama, $names))
	{
		$txt .= 'Coluna '.$fora_gama.' não foi encontrada'."<br>\n";
		$ok = 0;
	}
	if($ok == 0)
	{
		echo $txt."<br>";
		die("Parou - não existe configuracao suficiente de campos");
	}
	echo "A comecar importação...\n";
	$total_produtos_ativos = array();


	$fim=0;
	$linha=1;
	echo '<textarea style="width:100%;height:100%;font-size:10px">';
	while(!$fim)
	{
		if($Separador != '' && $Separador2 !='') $data = fgetcsv($fp, '', $Separador, ($Separador2!='' ? $Separador2 : null));
		elseif($Separador!='') $data = fgetcsv($fp, '', $Separador);
		if($data != '')
		{
			var_dump($data);
		}
		$linha++;
		if($linha > 10) $fim=1;
	}
	echo '</textarea>';
	echo "100% - Pré-importacão chegou ao fim<br>";
	unlink($myFile);
	unlink($file_path);


?>