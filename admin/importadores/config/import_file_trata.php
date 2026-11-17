<?php

	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
	header('Content-Type: text/html; charset=utf-8');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	if($Supplier_ID==4)die();

	$path = dirname(__FILE__).'/../../../core/';
	//Incluir apenas as librarias necessarias
	require_once $path.'../conf/conf.php';
	require_once $path.'seguranca.php';
	require_once $path.'db_engine_pdo.php';
	require_once $path.'sessions_engine.php';
	require_once $path.'global.php';
	require_once $path.'admin_engine.php';
	require_once $path.'seo.php';
	require_once $path.'gd.php';
	require_once $path.'email.php';
	require_once $path.'produtos.php';
	require_once $path.'filtros.php';
	require_once $path.'return_filtros.php';

	//incluir as funcoes dos importadores
	require_once('settings_crons.inc.php');
	require_once('funcoes.inc.php');

	$path2 = dirname( __FILE__ ).'/../../../';
	include $path2.'admin/products/filtros/funcoes.inc.php';

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(E_ALL ^ E_DEPRECATED);
	error_reporting(0);
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Supplier_ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	//Vamos limpar os registos da tabela products_objs_history_day
	$sql='DELETE FROM products_objs_history_day WHERE data_i <= ( CURDATE() - INTERVAL 5 DAY ) ';
	$res = Runquery($db_link,$DB_name,$sql);

	function convToUtf8($str, $encoding_detection_types = "UTF-8,ASCII,ISO-8859-15")
	{
		if(!mb_check_encoding($str, 'UTF-8') OR !($str === mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32')))
		{
			$detect = mb_detect_encoding($str, $encoding_detection_types);
		    $str = mb_convert_encoding($str, 'UTF-8', $detect);
		}
		return $str;
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
		$Preco2_IVA = stripslashes($obj->db_data['Preco2_IVA']);
		$Preco3_IVA = stripslashes($obj->db_data['Preco3_IVA']);
		$EAN = stripslashes($obj->db_data['EAN']);
		$Stock = stripslashes($obj->db_data['Stock']);
		$Imagem1 = stripslashes($obj->db_data['Imagem1']);
		$Imagem2 = stripslashes($obj->db_data['Imagem2']);
		$Imagem3 = stripslashes($obj->db_data['Imagem3']);
		$Imagem4 = stripslashes($obj->db_data['Imagem4']);
		$Separador = stripslashes(trim($obj->db_data['Separador']));
		$Separador2 = (stripslashes($obj->db_data['Separador2']));
		$log_activation = $obj->db_data['log_activation'];
		$marcas_atraves_nome=$obj->db_data['marcas_atraves_nome'];
		$cria_marcas_novas=$obj->db_data['cria_marcas_novas'];
		$codigo_fam_default=$obj->db_data['codigo_fam_default'];
		$como_importar = $obj->db_data['conf1'];
		$acoes_familias = $obj->db_data['conf2'];
		$acoes_marcas = $obj->db_data['conf3'];
		$acoes_visivel = $obj->db_data['conf4'];
		$Separador_decimais = $obj->db_data['Separador_decimais'];
		$Preco_conf = $obj->db_data['Preco_conf'];
		$Preco_iva_valor = $obj->db_data['Preco_iva_valor'];
		$Margem = $obj->db_data['Margem'];
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
		$ficheiro_ftp1 = $obj->db_data['ficheiro_ftp1'];
		$ficheiro_ftp2 = $obj->db_data['ficheiro_ftp2'];
		$ficheiro_ftp3 = $obj->db_data['ficheiro_ftp3'];
		$ftp_caminho = rtrim(ltrim($obj->db_data['ftp_caminho'], '/'), '/');
		$ftp_caminho = ($ftp_caminho!='' ? '/'.$ftp_caminho.'/' : '/');
		$Peso = $obj->db_data['Peso'];
		$Descricao2 = $obj->db_data['Descricao2'];
		$Classe_energetica = $obj->db_data['Classe_energetica'];
		$Link_Classe_Energetica = $obj->db_data['Link_Classe_Energetica'];
		$Stock1 = $obj->db_data['Stock1']; //azul
		$Stock2 = $obj->db_data['Stock2']; //vermelho
		$Stock3 = $obj->db_data['Stock3']; //amarelo
		$Stock4 = $obj->db_data['Stock4']; //verde
		$codigo_importacao = ($obj->db_data['codigo_importacao'] == '' ? 'IMPNOVA' : $obj->db_data['codigo_importacao']);
		$Eliminar_conf = $obj->db_data['Eliminar_conf'];
		$fora_gama = $obj->db_data['fora_gama'];
		$fora_gama_codigo = $obj->db_data['fora_gama_codigo'];
		$price_conf  = $obj->db_data['price_conf'];
		$key_field = $obj->db_data['key_field'];
		$type_importacao = $obj->db_data['tipo_importacao'];

		$sql = 'INSERT INTO a_importador_stats (ID_ORG,DataStart) values ("'.$Supplier_ID.'","'.date('Y-m-d H:i:s').'")';
		$res = Runquery($db_link,$DB_name,$sql);
		$a_importador_stats_id=db_get_last_insert_id($db_link);
	}
	else
	{
		die("parou - erro a obter configuracoes");
	}
	$obj->db_close();

	echo "a começar a importar\n";

	// QUANDO É PARA ELIMINAR PRODUTOS CASO NAO VENHAM NO FICHEIRO
	if($codigo_importacao !='' && $Eliminar_conf >= 1)
	{
		$sql = 'UPDATE products SET Flag=0 WHERE codigo_importacao = "'.$codigo_importacao.'"';
		$res = Runquery($db_link,$DB_name,$sql);
	}
	if($type_importacao==0)//tipo ftp
	{
		define('SERVER',$ftp_servidor); //IP o Nome do Servidor
		define('PORT', ($ftp_porta == 0 || $ftp_porta == '' ? 21 : $ftp_porta)); //Porto
		define('USER',$ftp_user); //Nome de Usuário
		define('PASSWORD',$ftp_password); //Senha de acesso
		define('PASV', ($ftp_passivo == 1 ? true : false)); //Ativa modo passivo

	}

	$txt = "a iniciar a analise...\n";
	$myFile = dirname( __FILE__ )."/../../../files/importar/analisar_csv".$Supplier_ID.".txt";
	if(file_exists($myFile)) die('já está a importar, não posso ter 2 importações em simultaneo ...'.$myFile."\n");
	//TODOss send email begin import
	$email_address='';
	$sql = 'SELECT email_address FROM a_importador_configuracoes';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$email_address = $rs->db_data['email_address'];
	}
	$rs->db_close();
	if($email_address)
	{
		$mailsubject	=	"Importação - Início";
		$message		=	date('dmYHis').' - Início de nova importação';
		@send_email($email_address,$mailsubject,$message);
	}
	$fh = fopen($myFile, 'a+');
	fwrite($fh, "a comecar analise...\n");

	// FICHEIROS XLSX QUE SERÃO PARA IMPORTAR
	foreach (glob($GLOBALS['site_shell_url']."files/importar/importa_manual".$Supplier_ID.".xlsx") as $filenameXLSX)
	{
		//Vamos converter de xlsx para csv

		$csvFileName = 'importa_manual'.$Supplier_ID.'.csv';
		$fp2 = fopen($GLOBALS['site_shell_url']."files/importar/".$csvFileName, 'w');

		require(dirname( __FILE__ ).'/php-excel-reader/excel_reader2.php');
        require(dirname( __FILE__ ).'/SpreadsheetReader.php');
        $Reader = new SpreadsheetReader($img_path.$filenameXLSX);
        $Sheets = $Reader -> Sheets();
        // Vou buscar sempre a primeira folha
        $Reader -> ChangeSheet(0);
        foreach ($Reader as $Row => $linha)
		{
			$fim=0;
			$pos=0;
			$j=0;
			$dados=array();
            while(!$fim)
            {
                if(trim($linha[$pos]) != '')
                {
                    $dados[$j]=trim($linha[$pos]);
                    $pos++;
                    $j++;
                }
                else $fim=1;
            }
            //Agora vamos gravar a linha em csv
            fputcsv($fp2,$dados,'|','"');
            $Separador='|';
            $Separador2='"';
		}
		fclose($fp2);
	}
	
	// FICHEIROS CSV QUE SERÃO PARA IMPORTAR
	$arr_loks_manual=array();
	foreach (glob($GLOBALS['site_shell_url']."files/importar/importa_manual".$Supplier_ID.".csv") as $filename)
	{
		$arr_loks_manual[] = $filename;
		$arr_loks[] = $filename;
	}
	if(count($arr_loks_manual) > 0) // IMPORTAR FICHEIRO MANUAL
	{
		$ficheiro_caminho = 2;
	}
	else // IMPORTAR FICHEIROS AUTO
	{
		// VAMOS BUSCAR FICHEIRO POR FTP
		if($type_importacao==0)
		{
			$img_path = dirname( __FILE__ )."/../../../files/importar/";
			fwrite($fh, "a iniciar o download do ficheiro ftp...\n");
			$ftp = ConectarFTP();
			if($ftp['estado'] == 1)
			{
				if(PASV) ftp_pasv($ftp['id_ftp'], true);

				if($ficheiro_ftp!='')
				{
					ftp_chdir($ftp['id_ftp'],$ftp_caminho);
					$rawList = ftp_nlist($ftp['id_ftp'], '.');
					if(!empty($rawList))
					{
						foreach($rawList as $key => $server_file)
						{
							if(substr_count($ftp_caminho.$server_file,$ficheiro_ftp))
							{
								if(substr_count($server_file,'csv')>0)$new_name_file='auto'.$Supplier_ID.'.csv';
								if(substr_count($server_file,'CSV')>0)$new_name_file='auto'.$Supplier_ID.'.csv';
								if(substr_count($server_file,'xlsx')>0)
								{
									$new_name_file='auto'.$Supplier_ID.'.xlsx';
									fwrite($fh, "foi encontrado o ficheiro $server_file, comecou a fazer download para $new_name_file...\n");
									if($res=ftp_get2($ftp['id_ftp'],$img_path.$new_name_file,$ftp_caminho.$server_file, FTP_BINARY))
									{
										fwrite($fh, "download completo...\n");

										//Vamos converter de xlsx para csv
										$csvFileName = 'web'.$Supplier_ID.'.csv';
										$fp2 = fopen($img_path.$csvFileName, 'w');

										require(dirname( __FILE__ ).'/php-excel-reader/excel_reader2.php');
		                                require(dirname( __FILE__ ).'/SpreadsheetReader.php');
		                                $Reader = new SpreadsheetReader($img_path.$new_name_file);
		                                $Sheets = $Reader -> Sheets();
		                                // Vou buscar sempre a primeira folha
		                                $Reader -> ChangeSheet(0);
		                                foreach ($Reader as $Row => $linha)
                                		{
                                			$fim=0;
                                			$pos=0;
                                			$j=0;
                                			$dados=array();
	                                        while(!$fim)
	                                        {
	                                            if(trim($linha[$pos]) != '')
	                                            {
	                                                $dados[$j]=trim($linha[$pos]);
	                                                $pos++;
	                                                $j++;
	                                            }
	                                            else $fim=1;
	                                        }
	                                        //Agora vamos gravar a linha em csv
	                                        fputcsv($fp2,$dados,'|','"');
	                                        $Separador='|';
	                                        $Separador2='"';
                                		}
                                		fclose($fp2);

                                		$arr_loks[] = $img_path.$csvFileName;

									}
									else{fwrite($fh, "erro download falhado...$res\n");}
								}
								else
								{
									fwrite($fh, "foi encontrado o ficheiro $server_file, comecou a fazer download para $new_name_file...\n");
									if($res=ftp_get2($ftp['id_ftp'],$img_path.$new_name_file,$ftp_caminho.$server_file, FTP_ASCII))
									{
										fwrite($fh, "download completo...\n");
									}
									else{fwrite($fh, "erro download falhado...$res\n");}
								}
							}
						}
					}
					else
					{
						fwrite($fh, "sem ficheiros para download ftp...\n");
						fclose($fh);
						if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
						{
							unlink($myFile);
						}
						die("parou - sem fxs para download");
					}
				}
				else
				{
					if($ficheiro_ftp1!='')
					{
						ftp_close($ftp['id_ftp']);
						$ftp = ConectarFTP();
						if(PASV) ftp_pasv($ftp['id_ftp'], true);

						if(substr_count($ficheiro_ftp1,'/') > 0)
						{
							$folders=explode('/',$ficheiro_ftp1);
							$i=0;
							$ftp_caminho1='';
							while($i<count($folders)-1)
							{
								$ftp_caminho1.=$folders[$i].'/';
								$i++;
							}
							$ficheiro1=$folders[$i];
							ftp_chdir($ftp['id_ftp'],$ftp_caminho1);
						}
						else $ficheiro1=$ficheiro_ftp1;
						$rawList = ftp_nlist($ftp['id_ftp'], '.');
						if(!empty($rawList))
						{
							foreach($rawList as $key => $server_file1)
							{
								echo $ftp_caminho1.$server_file1."\n";
								if(substr_count($ftp_caminho1.$server_file1,$ficheiro1))
								{
									if(substr_count($server_file1,'csv')>0)$new_name_file1='auto1_'.$Supplier_ID.'.csv';
									if(substr_count($server_file1,'CSV')>0)$new_name_file1='auto1_'.$Supplier_ID.'.csv';
									fwrite($fh, "foi encontrado o ficheiro $server_file, comecou a fazer download para $new_name_file1 ...\n");
									if($res=ftp_get2($ftp['id_ftp'],$img_path.$new_name_file1,$server_file1, FTP_ASCII)){fwrite($fh, "download completo...\n");}
									else{fwrite($fh, "erro download falhado...$res\n");}
								}
							}
						}
						else "empty";
					}
					if($ficheiro_ftp2!='')
					{
						ftp_close($ftp['id_ftp']);
						$ftp = ConectarFTP();
						if(PASV) ftp_pasv($ftp['id_ftp'], true);

						if(substr_count($ficheiro_ftp2,'/') > 0)
						{
							$folders=explode('/',$ficheiro_ftp2);
							$i=0;
							$ftp_caminho2='';
							while($i<count($folders)-1)
							{
								$ftp_caminho2.=$folders[$i].'/';
								$i++;
							}
							$ficheiro2=$folders[$i];
							ftp_chdir($ftp['id_ftp'],$ftp_caminho2);
						}
						else $ficheiro2=$ficheiro_ftp2;
						$rawList = ftp_nlist($ftp['id_ftp'], '.');
						if(!empty($rawList))
						{
							foreach($rawList as $key => $server_file2)
							{
								if(substr_count($ftp_caminho2.$server_file2,$ficheiro2))
								{
									if(substr_count($server_file2,'csv')>0)$new_name_file2='auto2_'.$Supplier_ID.'.csv';
									if(substr_count($server_file2,'CSV')>0)$new_name_file2='auto2_'.$Supplier_ID.'.csv';
									fwrite($fh, "foi encontrado o ficheiro $server_file, comecou a fazer download para $new_name_file2 ...\n");
									if($res=ftp_get2($ftp['id_ftp'],$img_path.$new_name_file2,$server_file2, FTP_ASCII)){fwrite($fh, "download completo...\n");}
									else{fwrite($fh, "erro download falhado...$res\n");}
								}
							}
						}
					}
					if($ficheiro_ftp3!='')
					{
						ftp_close($ftp['id_ftp']);
						$ftp = ConectarFTP();
						if(PASV) ftp_pasv($ftp['id_ftp'], true);

						if(substr_count($ficheiro_ftp3,'/') > 0)
						{
							$folders=explode('/',$ficheiro_ftp3);
							$i=0;
							$ftp_caminho3='';
							while($i<count($folders)-1)
							{
								$ftp_caminho3.=$folders[$i].'/';
								$i++;
							}
							$ficheiro3=$folders[$i];
							ftp_chdir($ftp['id_ftp'],$ftp_caminho3);
						}
						else $ficheiro3=$ficheiro_ftp3;
						$rawList = ftp_nlist($ftp['id_ftp'], '.');
						if(!empty($rawList))
						{
							foreach($rawList as $key => $server_file3)
							{
								if(substr_count($ftp_caminho3.$server_file3,$ficheiro3))
								{
									if(substr_count($server_file3,'csv')>0)$new_name_file3='auto3_'.$Supplier_ID.'.csv';
									if(substr_count($server_file3,'CSV')>0)$new_name_file3='auto3_'.$Supplier_ID.'.csv';
									fwrite($fh, "foi encontrado o ficheiro $server_file, comecou a fazer download para $new_name_file3 ...\n");
									if($res=ftp_get2($ftp['id_ftp'],$img_path.$new_name_file3,$server_file3, FTP_ASCII)){fwrite($fh, "download completo...\n");}
									else{fwrite($fh, "erro download falhado...$res\n");}
								}
							}
						}
					}

					//Agora vamos criar um unico csv com todos
					if($new_name_file1 !='' || $new_name_file2 !=''  || $new_name_file3 !='' )
					{
						if($new_name_file1 !='')
						{
							$fh1 = fopen($GLOBALS['site_shell_url']."files/importar/auto1_".$Supplier_ID.".csv", 'r');

							if($Separador != '' && $Separador2 !='') while (($data = fgetcsv($fh1, '',$Separador, $Separador2)) !== FALSE) {$csv1[]=$data;}
							elseif($Separador!='') while (($data = fgetcsv($fh1, '',$Separador)) !== FALSE) {$csv1[]=$data;}

						}
						if($new_name_file2 !='')
						{
							$fh2 = fopen($GLOBALS['site_shell_url']."files/importar/auto2_".$Supplier_ID.".csv", 'r');

							if($Separador != '' && $Separador2 !='') while (($data = fgetcsv($fh2, '',$Separador, $Separador2)) !== FALSE) {$csv2[]=$data;}
							elseif($Separador!='') while (($data = fgetcsv($fh2, '',$Separador)) !== FALSE) {$csv2[]=$data;}

						}
						if($new_name_file3 !='')
						{
							$fh3 = fopen($GLOBALS['site_shell_url']."files/importar/auto3_".$Supplier_ID.".csv", 'r');
							if($Separador != '' && $Separador2 !='') while (($data = fgetcsv($fh3, '',$Separador, $Separador2)) !== FALSE) {$csv3[]=$data;}
							elseif($Separador!='') while (($data = fgetcsv($fh3, '',$Separador)) !== FALSE) {$csv3[]=$data;}
						}

						//Vamos procurar a coluna de campo chave:
						$found1='';
						$header1=$csv1[0];
						for($x=0;$x< count($header1);$x++)
						{
							if($key_field==0 && $header1[$x]==$Codigo_produto) {$found1 = $x;break;}
							if($key_field==1 && $header1[$x]==$EAN) {$found1 = $x;break;}
						}

						$found2='';
						$header2=$csv2[0];
						for($x=0;$x< count($header2);$x++)
						{
							if($key_field==0 && $header2[$x]==$Codigo_produto) {$found2 = $x;break;}
							if($key_field==1 && $header2[$x]==$EAN) {$found2 = $x;break;}
						}

						$found3='';
						$header3=$csv3[0];
						for($x=0;$x< count($header3);$x++)
						{
							if($key_field==0 && $header3[$x]==$Codigo_produto) {$found3 = $x;break;}
							if($key_field==1 && $header3[$x]==$EAN) {$found3 = $x;break;}
						}
						//Agora vamos criar novo array
						$keys=array();
						$novo=array();
						for($x=0;$x< count($csv1);$x++)
						{
							$keys[$x]=$key;
							$key=$csv1[$x][$found1];
							$novo[$key][0]=$csv1[$x];
						}
						for($x=0;$x< count($csv2);$x++)
						{
							$key=$csv2[$x][$found2];
							$novo[$key][1]=$csv2[$x];
						}
						for($x=0;$x< count($csv3);$x++)
						{
							$key=$csv3[$x][$found3];
							$novo[$key][2]=$csv3[$x];
						}

						// 3 section
				        $new_name_file='auto'.$Supplier_ID.'.csv';
				        $fp = fopen($GLOBALS['site_shell_url']."files/importar/".$new_name_file, 'w');//output file set here
				        if ( !$fp ) {
					        echo 'Erro File open failed : '.$GLOBALS['site_shell_url']."files/importar/".$new_name_file;
					      }

						for($x=0;$x< count($keys);$x++)
						{
							$key=$keys[$x];
							$array1=$novo[$key][0];
							$array2=$novo[$key][1];
							$array3=$novo[$key][2];
							$result = array_merge($array1,$array2,$array3);
							if(count($result)>0)
							{
								//fputcsv($fp,$result,$Separador);
								fputcsv($fp,$result,'|','"');
							}
						}
						$Separador='|';
						$Separador2='"';

				        fclose($fp);
				        fclose($fh1);
				        fclose($fh2);
				        fclose($fh3);
					}

				}

			}
			else
			{
				if($ftp['estado'] == -1) fwrite($fh, "nao conseguiu ligar ao servidor!\n");
				elseif($ftp['estado'] == 0) fwrite($fh, "nao conseguiu fazer login!\n");
				fclose($fh);
				if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
				{
					unlink($myFile);
				}
				die("parou - nao conseguio ligar ftp");
			}
		}
		elseif($type_importacao==1)//URL - Download através de url externo(ficheiro .csv)
		{
			$img_path = dirname( __FILE__ )."/../../../files/importar/";
			$file_to_import = 'web'.$Supplier_ID.'.csv';

			if(!file_exists($img_path.$file_to_import) && $Supplier_ID <> 4 && $Supplier_ID <> 6)
			{				
				fwrite($fh, "a fazer download do ficheiro a partir de URL $ficheiro_caminho !\n");
				$file = get_web_file($ficheiro_caminho);
				// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
				if($Supplier_ID == 5)
				{
					$encodedOutput = $file;				
					$encodedOutput=str_replace('Port?til','Portátil',$encodedOutput);
				}
				else $encodedOutput = convToUtf8($file);

				// VAMOS GUARDAR O NOVO FICHEIRO
				file_put_contents($img_path.$file_to_import,$encodedOutput);
			}
			if($Supplier_ID==4 || $Supplier_ID==6)
			{
				$img_path2 = dirname( __FILE__ )."/../../../files/importado/";
				//Nao posso descarregar o fx mais de que 1 vez por hora:
				if(file_exists($img_path2.$file_to_import))
				{
					$tempo = filemtime($img_path2.$file_to_import);
					// Verifica se foi modificado há mais de 1 hora
				    if ($tempo < (time() - 3600))
				    {
				        //echo "O ficheiro foi criado/modificado há mais de 1 hora.";
				        fwrite($fh, "a fazer download do ficheiro a partir de URL $ficheiro_caminho !\n");
						$file = get_web_file($ficheiro_caminho);

						$encodedOutput = convToUtf8($file);
						// VAMOS GUARDAR O NOVO FICHEIRO
						file_put_contents($img_path.$file_to_import,$encodedOutput);
				    }
				    else
				    {
						copia_segura_imp($img_path2.$file_to_import,$img_path.$file_to_import);
				    }
				}
			}
			

			if(!file_exists($img_path.$file_to_import))
			{
				fwrite($fh, "parou - nao copiou o fx $img_path"."$file_to_import");
				die("parou - nao copiou o fx $img_path.$file_to_import");
			}
			fwrite($fh, "ficheiro gravado com sucesso!\n");

			$arr_loks[] = $img_path.$file_to_import;
		}
		elseif($type_importacao==2)//URL - Download através de url externo(ficheiro .json)
		{
			$img_path = dirname( __FILE__ )."/../../../files/importar/";
			fwrite($fh, "a fazer download do ficheiro a partir de URL $ficheiro_caminho_json !\n");
			$file = get_web_file($ficheiro_caminho_json);
			// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
			$encodedOutput = convToUtf8($file);
			// VAMOS GUARDAR O NOVO FICHEIRO
			$file_to_import = 'web'.$Supplier_ID.'.json';

			file_put_contents($img_path.$file_to_import,$encodedOutput);
			if(!file_exists($img_path.$file_to_import))die("parou - nao copiou o fx $img_path.$file_to_import");

			fwrite($fh, "ficheiro gravado com sucesso em formato json!\n");
			fwrite($fh, "a converter fx para csv!\n");
			//Decode the JSON and convert it into an associative array.
			$jsonDecoded = json_decode($encodedOutput, true);
			//Give our CSV file a name.
			$csvFileName = 'web'.$Supplier_ID.'.csv';
			//Open file pointer.
			$fp2 = fopen($img_path.$csvFileName, 'w');
			//Loop through the associative array.
			$firstLineKeys = false;
			foreach($jsonDecoded as $line)
			{
			    //Write the row to the CSV file.
			    if (empty($firstLineKeys))
				{
					$firstLineKeys = array_keys($line);
					fputcsv($fp2,$firstLineKeys,'|','"');
					$firstLineKeys = array_flip($firstLineKeys);
				}
				// Using array_merge is important to maintain the order of keys acording to the first element
				fputcsv($fp2, array_merge($firstLineKeys,$line),'|','"');
			}
			//Finally, close the file pointer.
			fclose($fp2);

			$Separador='|';
			$Separador2='"';

			fwrite($fh, "a gravar fx convertido para csv!\n");
			$arr_loks[] = $img_path.$csvFileName;

			unlink($img_path.$file_to_import);
			$file_to_import=$csvFileName;
		}
		elseif($type_importacao==3)//URL - WEB API de um CSV
		{
			$img_path = dirname( __FILE__ )."/../../../files/importar/";
			fwrite($fh, "a iniciar o download do ficheiro via WEB API...\n");


			$timeout = 5;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$ficheiro_caminho_api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "$ficheiro_caminho_api_login:$ficheiro_caminho_api_pass");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
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
			if(!file_exists($img_path.$file_to_import))die("parou - nao copiou o fx $img_path.$file_to_import");
			echo "ficheiro gravado com sucesso!<br>";

			$arr_loks[] = $img_path.$file_to_import;
		}
		elseif($type_importacao==31)//URL - WEB API, URL A URL
		{
			fwrite($fh, "a iniciar o download do ficheiro via WEB API...\n");
			$img_path = dirname( __FILE__ )."/../../../files/importar/";

			//Vamos ler todos os dados de online e gravar em csv
			$sql='SELECT distinct(Ref_loja) FROM products_objs_history WHERE Supplier_ID = '.$Supplier_ID;
			$rs->db_open($db_link, $DB_name, $sql);
		    if (!$rs->EOF())
		    {
		    	$csvFileName = 'web'.$Supplier_ID.'.csv';
		    	//Open file pointer.
				$fp2 = fopen($img_path.$csvFileName, 'w');
		    	while (!$rs->EOF())
		    	{
			    	$ref_loja=$rs->db_data["Ref_loja"];

					$ficheiro_caminho_api=str_replace('{referencia}',$ref_loja);

					fwrite($fh, "a obter dados de $ficheiro_caminho_api\n");

					curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				    'method: GET',
				    'scheme: https',
				    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				    'accept-encoding: gzip, deflate, br',
				    'accept-language: en,pt-PT;q=0.9,pt;q=0.8,en-US;q=0.7,es;q=0.6',
				    'authorization: Basic NTMzNTpNSFIyMDE1QVBJ',
				    'sec-fetch-dest: document',
				    'sec-fetch-mode: navigate',
				    'sec-fetch-site: none',
				    'sec-fetch-user: ?1',
				    'upgrade-insecure-requests: 1',
				    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
					));
					curl_setopt($ch, CURLOPT_URL, $ficheiro_caminho_api);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_HTTPGET, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
					curl_setopt($ch, CURLOPT_TIMEOUT, 3);
					curl_setopt($ch, CURLOPT_USERPWD, "$ficheiro_caminho_api_login:$ficheiro_caminho_api_pass");
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					$data = curl_exec($ch);
					//fputcsv($fp2,$data, ";");
					fputcsv($fp2,$data,'|','"');
					$rs->db_next();
				}
				$Separador='|';
				$Separador2='"';
				//Finally, close the file pointer.
				fclose($fp2);
			}
			$rs->db_close();

			$arr_loks[] = $img_path.$csvFileName;
			fwrite($fh, "Ainda nao esta feito este importador...!\n");


			die("Ainda nao esta feito");

		}
		elseif($ficheiro_caminho  == 1 && $ficheiro_caminho != '')
		{
			fwrite($fh, "a gravar ficheiro enviado manualmente...\n");

			$img_path = dirname( __FILE__ )."/../../../files/importar/";
			if(copia_segura_imp($ficheiro_caminho,$img_path.'ficheirocsv'.$Supplier_ID.'.csv'))
			{
				// OK
			}
			else
			{
				fwrite($fh, "nao copiou o ficheiro!\n");
				fclose($fh);
				if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
				{
					unlink($myFile);
				}
				die("parou - nao copiou o fx");
			}
		}
	}
	// IMPORTAR NA LINGUAS
	$GLOBALS['linguas_importa'][0] = 'pt';
	// FICHEIROS CSV QUE SERÃO PARA IMPORTAR
	foreach (glob($GLOBALS['site_shell_url']."files/importar/auto".$Supplier_ID."*.csv") as $filename)
	{
		$arr_loks[] = $filename;
	}
	/*foreach (glob($GLOBALS['site_shell_url']."files/importar/auto".$Supplier_ID."*.CSV") as $filename)
	{
		$arr_loks[] = $filename;
	}*/
	if(count($arr_loks) == 0) die('nao foram encontrados ficheiros.');
	$file_path = $arr_loks[0];



	$file_path_done = str_replace('importar/', 'importado/',$arr_loks[0]);
	$file = file_get_contents($GLOBALS['site_shell_url'].'files/importar/'.basename($file_path));
	fwrite($fh, "a converter ficheiro para UTF8...!\n");
	// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
	$encodedOutput = convToUtf8($file);
	if(strlen($encodedOutput)<2 && strlen($file)>0){$encodedOutput=$file;fwrite($fh, "erro a converter ficheiro para UTF!\n");}//Por algum motivo a conversao nao foi bem sucedida
	else fwrite($fh, "ficheiro convertido!\n");
	// VAMOS GUARDAR O NOVO FICHEIRO


	if($Supplier_ID==4)
	{
		//FD
		//***********
		//Limpa lixo:		
		$encodedOutput=str_replace(', separator: |','',$encodedOutput);

		$encodedOutput=str_replace('"'."\n",'[bk]',$encodedOutput);
		$encodedOutput=str_replace("\n",'<br>',$encodedOutput);
		$encodedOutput=str_replace("\r",'<br>',$encodedOutput);
		$encodedOutput=str_replace('[bk]','"'."\n",$encodedOutput);
		
		$encodedOutput=str_replace(',"kod EAN",opis<br>',',"kod EAN",opis'."\n",$encodedOutput);
		//***********
		//echo "\n".$encodedOutput."\n";
	}

	$file_to_import = str_replace(array('.csv', '.CSV'), '_imp_'.$Supplier_ID.'_'.date('Ymd').'.csv',$file_path);
	file_put_contents($file_to_import,$encodedOutput);
	if(file_exists($file_to_import))
	{
		$file_path_old = $file_path;
		$file_path_old_done = $file_path_done;
		$file_path = $file_to_import;
		$file_path_done = str_replace('importar/', 'importado/',$file_to_import);
	}
	/*$total_produtos = 0;
	$file = new SplFileObject($file_path, 'r');
	$file->seek(PHP_INT_MAX);
	$total_produtos = $file->key();*/

	
	//***********
	$fp2 = fopen($file_path, "r");
	$fim=0;
	$total_produtos=0;
	while(!$fim)
	{
		//Ler os dados do CSV
		if($Separador != '' && $Separador2 !='') $data = fgetcsv($fp2, '',$Separador, $Separador2);
		elseif($Separador!='') $data = fgetcsv($fp2, '',$Separador);

		if($data != '')$total_produtos++;
		else $fim=1;
		//echo "<hr>";
	}
	fclose($fp2);
	//***********
	


	echo 'Total Produtos: '.$total_produtos."\n";
	$txt .= 'Total Produtos: '.$total_produtos."\n";


	$fp = fopen($file_path, "r");
	if($Separador == '')
	{
		fwrite($fh, "Em falta Separador!\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
		{
			unlink($myFile);
		}
		die("parou - Em falta Separador");
	}


	if($Separador != '' && $Separador2 !='') $data = fgetcsv($fp, '',$Separador, $Separador2);
	elseif($Separador!='') $data = fgetcsv($fp, '',$Separador);

	$data = (str_replace("\xEF\xBB\xBF",'',$data)); # strip BOM

	if($fp == null || ($data) === false)
	{
		fwrite($fh, "Ocorreu um erro de leitura do ficheiro: $file_path em que o Separador = $Separador  e  Separador2 = $Separador2 \n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
		{
			unlink($myFile);
		}
		die("parou - Ocorreu um erro de leitura!\n");
	}
	$names = array();
	foreach($data as $field) $names[] = trim(convToUtf8($field));	
	if(empty($names))
	{
		fwrite($fh, "Nao foi especificadas nome das colunas!\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
		{
			unlink($myFile);
		}
		die("parou - Nao foi especificadas nome das colunas!");
	}
	$linha = 1;
	$ll = '';
	$ll2 = '';
	$txt .= 'Colunas no csv: '.implode(' | ',$names)."\n";
	fwrite($fh,$txt."\n");
	// print_r($names);
	$ok = 1;
	// VAMOS VERIFICAR SE AS COLUNAS CONFIGURADAS EXISTEM NO FICHEIRO
	if(!in_array($Codigo_produto,$names))
	{
		$txt .= 'Coluna '.$Codigo_produto.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Produto !='' && !in_array($Produto,$names))
	{
		$txt .= '>'.in_array($Produto,$names).'>'.$Produto.'>'.implode(',',$names)."\n";

		$txt .= 'Coluna '.$Produto.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Descricao!='' && !in_array($Descricao,$names))
	{
		$txt .= 'Coluna '.$Descricao.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Marca !='' && !in_array($Marca,$names))
	{
		$txt .= 'Coluna '.$Marca.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($MarcaDesc !='' && !in_array($MarcaDesc,$names))
	{
		$txt .= 'Coluna '.$MarcaDesc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Categoria !='' && !in_array($Categoria,$names))
	{
		$txt .= 'Coluna '.$Categoria.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($CategoriaDesc !='' && !in_array($CategoriaDesc,$names))
	{
		$txt .= 'Coluna '.$CategoriaDesc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_Categoria !='' && !in_array($Sub_Categoria,$names))
	{
		$txt .= 'Coluna '.$Sub_Categoria.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_CategoriaDesc !='' && !in_array($Sub_CategoriaDesc,$names))
	{
		$txt .= 'Coluna '.$Sub_CategoriaDesc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_Categoria2 !='' && !in_array($Sub_Categoria2,$names))
	{
		$txt .= 'Coluna '.$Sub_Categoria2.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Sub_Categoria2Desc !='' && !in_array($Sub_Categoria2Desc,$names))
	{
		$txt .= 'Coluna '.$Sub_Categoria2Desc.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Preco_IVA !='' && !in_array($Preco_IVA,$names))
	{
		$txt .= 'Coluna '.$Preco_IVA.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Preco2_IVA !='' && !in_array($Preco2_IVA,$names))
	{
		$txt .= 'Coluna '.$Preco2_IVA.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Preco3_IVA !='' && !in_array($Preco3_IVA,$names))
	{
		$txt .= 'Coluna '.$Preco3_IVA.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($EAN !='' && !in_array($EAN,$names))
	{
		$txt .= 'Coluna '.$EAN.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Stock !='' && !in_array($Stock,$names))
	{
		$txt .= 'Coluna '.$Stock.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem1 !='' && !in_array($Imagem1,$names))
	{
		$txt .= 'Coluna '.$Imagem1.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem2 !='' && !in_array($Imagem2,$names))
	{
		$txt .= 'Coluna '.$Imagem2.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem3 !='' && !in_array($Imagem3,$names))
	{
		$txt .= 'Coluna '.$Imagem3.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Imagem4 !='' && !in_array($Imagem4,$names))
	{
		$txt .= 'Coluna '.$Imagem4.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Peso !='' && !in_array($Peso,$names))
	{
		$txt .= 'Coluna '.$Peso.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Descricao2 !='' && !in_array($Descricao2,$names))
	{
		$txt .= 'Coluna '.$Descricao2.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Classe_energetica !='' && !in_array($Classe_energetica,$names))
	{
		$txt .= 'Coluna '.$Classe_energetica.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($Link_Classe_Energetica !='' && !in_array($Link_Classe_Energetica,$names))
	{
		$txt .= 'Coluna '.$Link_Classe_Energetica.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($fora_gama !='' && !in_array($fora_gama,$names))
	{
		$txt .= 'Coluna '.$fora_gama.' nao foi encontrada'."\n";
		$ok = 0;
	}
	if($ok == 0)
	{
		fwrite($fh,$txt."\n");
		fclose($fh);
		if(copy($myFile, str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_err'.date('YmdHis').'.txt',$myFile)))
		{
			unlink($myFile);
		}
		die("parou - nao existe configuracao suficiente de campos ".$txt);
	}
	echo "a comecar importacao...\n";
	fwrite($fh, "a comecar importacao...\n");
	$total_produtos_ativos = array();











	$sql = 'UPDATE a_importador_stats set TotalProds = "'.$total_produtos.'" WHERE ID = '.$a_importador_stats_id;
	$res = Runquery($db_link,$DB_name,$sql);




	$fim=0;
	$total2=$total_produtos;
	while(!$fim)
	{
		//Ler os dados do CSV
		if($Separador != '' && $Separador2 !='') $data = fgetcsv($fp, '',$Separador, $Separador2);
		elseif($Separador!='') $data = fgetcsv($fp, '',$Separador);

		//Ja temos a primeira linha do CSV
		if($data != '')
		{
			//echo $total2." | ";
			echo $total2."\n | ";
			$ll = intval(($linha * 100 ) / $total_produtos);
			if($ll != $ll2)
			{
				$ll2 = $ll;
			}
			if(!file_exists($myFile)) die("parou - fx nao existe $myFile");//Esta condição serve para para a importacao quando via backoffice a queremos parar
			$CODIGO_PRODUTO = '';
			$PRECO_PRODUTO = '';
			$EAN_PRODUTO = '';
			$STOCK_PRODUTO = '';
			$NOME_PRODUTO = '';
			$RESUMO_PRODUTO = '';
			$CARACTERISTICAS_PRODUTO = '';
			$CLASS_ENERGITICA_PRODUTO = '';
			$LINK_CLASS_ENERGITICA_PRODUTO = '';
			$CODIGO_MARCA = '';
			$NOME_MARCA = '';
			$CODIGO_CATEGORIA = '';
			$NOME_CATEGORIA = '';
			$CODIGO_SUBCATEGORIA = '';
			$NOME_SUBCATEGORIA = '';
			$CODIGO_SUBCATEGORIA2 = '';
			$NOME_SUBCATEGORIA2 = '';
			$IMAGEM_PRODUTO1 = '';
			$IMAGEM_PRODUTO2 = '';
			$IMAGEM_PRODUTO3 = '';
			$IMAGEM_PRODUTO4 = '';
			$PESO_PRODUTO = '';
			$FORA_GAMA = '';
			if($Codigo_produto != '' && in_array($Codigo_produto,$names)){ $CODIGO_PRODUTO = devolvevalor($data,$Codigo_produto);} // REFERENCIA PRODUTO, OBRIGATORIO

			if($Preco_IVA !='' && in_array($Preco_IVA,$names)){ $PRECO_PRODUTO = devolvevalor($data,$Preco_IVA, 'num');} // PREÇO PRODUTO
			if($PRECO_PRODUTO>0)
			{
				if($Preco2_IVA !='' && in_array($Preco2_IVA,$names)){ $PRECO_PRODUTO += devolvevalor($data,$Preco2_IVA, 'num');} // PREÇO COMPLEMENTAR AO PRODUTO
				if($Preco3_IVA !='' && in_array($Preco3_IVA,$names)){ $PRECO_PRODUTO += devolvevalor($data,$Preco3_IVA, 'num');} // PREÇO COMPLEMENTAR AO PRODUTO
			}

			if($EAN !='' && in_array($EAN,$names)){


			$EAN_PRODUTO = devolvevalor($data,$EAN);
			if(trim($EAN_PRODUTO) != '' )$EAN_PRODUTO = str_pad($EAN_PRODUTO, 13, "0", STR_PAD_LEFT);

			} // EAN PRODUTO

			if($Stock !='' && in_array($Stock,$names))
			{
				$STOCK_PRODUTO = devolvevalor($data,$Stock);
				$STOCK_PRODUTO = devolve_stock_def($Stock1,$Stock2,$Stock3,$Stock4,$STOCK_PRODUTO);
			} // STOCK PRODUTO




			if($como_importar != 3 && $como_importar != 4)// para demorar menos tempo, so vamos ler os campos se for para atualizar tudo ou inserir produtos
			{
				if($Produto !='' && in_array($Produto,$names)){ $NOME_PRODUTO = devolvevalor($data,$Produto);} // TITULO PRODUTO
				if($Descricao !='' && in_array($Descricao,$names)){ $RESUMO_PRODUTO = devolvevalor($data,$Descricao);} // RESUMO PRODUTO
				if($Descricao2 !='' && in_array($Descricao2,$names)){ $CARACTERISTICAS_PRODUTO = devolvevalor($data,$Descricao2);} // CARACTERISTICAS PRODUTO
				if($Classe_energetica !='' && in_array($Classe_energetica,$names)){ $CLASS_ENERGITICA_PRODUTO = devolvevalor($data,$Classe_energetica);} // CARACTERISTICAS PRODUTO
				if($Link_Classe_Energetica !='' && in_array($Link_Classe_Energetica,$names)){ $LINK_CLASS_ENERGITICA_PRODUTO = devolvevalor($data,$Link_Classe_Energetica);} // CARACTERISTICAS PRODUTO
				if($Marca !='' && in_array($Marca,$names)){ $CODIGO_MARCA = devolvevalor($data,$Marca);} // CODIGO MARCA
				if($MarcaDesc !='' && in_array($MarcaDesc,$names)){ $NOME_MARCA = devolvevalor($data,$MarcaDesc);} // NOME MARCA
				if($Categoria !='' && in_array($Categoria,$names)){ $CODIGO_CATEGORIA = devolvevalor($data,$Categoria);} // CODIGO CATEGORIA - NIVEL 1
				if($CategoriaDesc !='' && in_array($CategoriaDesc,$names)){ $NOME_CATEGORIA = devolvevalor($data,$CategoriaDesc);} // NOME CATEGORIA
				if($Sub_Categoria !='' && in_array($Sub_Categoria,$names)){ $CODIGO_SUBCATEGORIA = devolvevalor($data,$Sub_Categoria);} // CODIGO SUBCATEGORIA - NIVEL 2
				if($Sub_CategoriaDesc !='' && in_array($Sub_CategoriaDesc,$names)){ $NOME_SUBCATEGORIA = devolvevalor($data,$Sub_CategoriaDesc);} // NOME SUBCATEGORIA
				if($Sub_Categoria2 !='' && in_array($Sub_Categoria2,$names)){ $CODIGO_SUBCATEGORIA2 = devolvevalor($data,$Sub_Categoria2);}  // CODIGO SUBCATEGORIA2 - NIVEL 3
				if($Sub_Categoria2Desc !='' && in_array($Sub_Categoria2Desc,$names)){ $NOME_SUBCATEGORIA2 = devolvevalor($data,$Sub_Categoria2Desc);} // NOME SUBCATEGORIA2
				

				if($Supplier_ID == 5)
				{
					if($Imagem1 !='' && in_array($Imagem1,$names))$IMAGEM_PRODUTO1 = devolvevalor($data,$Imagem1);
					$IMAGEM_PRODUTO1 = str_replace(',','|',$IMAGEM_PRODUTO1);
					//AS imagens vem separadas por , ou |
					if($substr_count($IMAGEM_PRODUTO1,'|')>0)
					{
						$imagens_array = explode('|',$IMAGEM_PRODUTO1);
						if($imagens_array[0]!='')$IMAGEM_PRODUTO1=$imagens_array[0];
						if($imagens_array[1]!='')$IMAGEM_PRODUTO2=$imagens_array[1];
						if($imagens_array[2]!='')$IMAGEM_PRODUTO3=$imagens_array[2];
						if($imagens_array[3]!='')$IMAGEM_PRODUTO4=$imagens_array[3];
					}

					if($IMAGEM_PRODUTO1) {$imagem[$x] = $IMAGEM_PRODUTO1;$x++;} // IMAGEM 1 PRODUTO
					if($IMAGEM_PRODUTO2) {$imagem[$x] = $IMAGEM_PRODUTO2;$x++;} // IMAGEM 2 PRODUTO
					if($IMAGEM_PRODUTO3) {$imagem[$x] = $IMAGEM_PRODUTO3;$x++;} // IMAGEM 3 PRODUTO
					if($IMAGEM_PRODUTO4) {$imagem[$x] = $IMAGEM_PRODUTO4;$x++;} // IMAGEM 4 PRODUTO

				}
				else
				{
					$imagem = array();
					$x=0;
					if($Imagem1 !='' && in_array($Imagem1,$names)){ $IMAGEM_PRODUTO1 = devolvevalor($data,$Imagem1); if($IMAGEM_PRODUTO1) $imagem[$x] = $IMAGEM_PRODUTO1;$x++;} // IMAGEM 1 PRODUTO
					if($Imagem2 !='' && in_array($Imagem2,$names)){ $IMAGEM_PRODUTO2 = devolvevalor($data,$Imagem2); if($IMAGEM_PRODUTO2) $imagem[$x] = $IMAGEM_PRODUTO2;$x++;} // IMAGEM 2 PRODUTO
					if($Imagem3 !='' && in_array($Imagem3,$names)){ $IMAGEM_PRODUTO3 = devolvevalor($data,$Imagem3); if($IMAGEM_PRODUTO3) $imagem[$x] = $IMAGEM_PRODUTO3;$x++;} // IMAGEM 3 PRODUTO
					if($Imagem4 !='' && in_array($Imagem4,$names)){ $IMAGEM_PRODUTO4 = devolvevalor($data,$Imagem4); if($IMAGEM_PRODUTO4) $imagem[$x] = $IMAGEM_PRODUTO4;$x++;} // IMAGEM 4 PRODUTO
				}





				if($Peso !='' && in_array($Peso,$names)){ $PESO_PRODUTO = devolvevalor($data,$Peso, 'num');} // PESO PRODUTO
				if($fora_gama !='' && in_array($fora_gama,$names)){ $FORA_GAMA = devolvevalor($data,$fora_gama);} // PESO PRODUTO
			}

			if(
				(trim($CODIGO_PRODUTO) != '' && ($Eliminar_conf == 0 || $Eliminar_conf == 1)) ||  /* PASSA SE EM 'Ações Ficheiro' ESTIVER 'Desativado(0)' ou 'Eliminar Produtos que não vinham no ficheiro (1)'*/
				(trim($CODIGO_PRODUTO) != '' && $Eliminar_conf == 2 && $FORA_GAMA != $fora_gama_codigo) /* PASSA SE EM 'Ações Ficheiro' ESTIVER 'Eliminar Produtos que não vinham no ficheiro e que estejam fora de gama (2)' E O CAMPO FORA DE GAMA NO FICHEIRO FOR IGUAL AO CAMPO EM 'Com Valor'*/
				)
			{
				echo "1111";
				if($como_importar == 5)
				{
					$prod_info = detect_product_EAN($CODIGO_PRODUTO,$EAN_PRODUTO);
				}

				if($como_importar == 5 && $prod_info['prod_id'] > 0)//Apenas atualizar imagens se ja existirem, mas os novos vamos criar
				{
					echo "prod = ".$prod_info['prod_id']." -> ";
					print_r($imagem);

					$original = '';
					$log_img = 'T='.count($imagem);
					for($z=0; $z<count($imagem);$z++)
					{
						if(
							!file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.jpg')
							&&
							!file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.png')
						)
						{
							echo "(i)";
							$url = ''; $original = $imagem[$z];
							if($Imagem_conf == 2) // URL EXTERNA FTP (DISCOTECA JUVENIL)
							{
								$log_img=' IMG URL: ';
								$img = '';
								$img = str_replace('ftp://discotecajuvenil.dyndns.org/', 'ftp://GadgetAlert:9NswSzXQ7Q@discotecajuvenil.dyndns.org/', $imagem[$z]);
								download_image1($img, dirname( __FILE__ ).'/../../files/importar/imagens/'.basename($imagem[$z]));
								if(file_exists(dirname( __FILE__ ).'/../../files/importar/imagens/'.basename($imagem[$z])))
								{
									$url = dirname( __FILE__ ).'/../../files/importar/imagens/'.basename($imagem[$z]);
								}
								else echo ' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
							}
							elseif($Imagem_conf == 0) // IMAGEM DE URL EXTERNO
							{
								$log_img=' IMG URL: ';
								$file = $prod_info['prod_id'].'_'.($z+1).'.jpg'; //file name to write to include location if needed

								$file_headers = @get_headers($imagem[$z]);
								$exists = true;
								if($file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0]=='HTTP/1.1 403 Forbidden') {$exists = false;}
								if($exists==true)
								{
									$current = file_get_contents($imagem[$z]); // read the created file
									if(file_put_contents($GLOBALS['site_shell_url'].'files/importar/imagens/'.$file,$current))
									{
										$log_img.=' OK ';
										$imagem[$z] = $file;
										if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($file)))
											$url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($file);
									}
									else $log_img.=' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
								}
								else
								{
									if($file_headers[0] == 'HTTP/1.1 404 Not Found')$log_img.=' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
									else $log_img.=' SEM PERMISSOES: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
								}

							}
							else
							{
								$log_img=' IMG: ';
								if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png';
								elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg')) $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg';
								elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif';
							}
							if($url != '' && file_exists($url))
							{
								$ext = pathinfo($url, PATHINFO_EXTENSION);
								$url_new = dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext;
								if($ext != '' && !file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext))
								{
									$log_img.=' CP OK ';
									@unlink(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext);
									copia_segura($url,$url_new);
									$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
									$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
									$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_info['prod_id'].'", "'.$ext.'", '.($z+1).', "'.addslashes($original).'")');
								}
								else $log_img.=' CP ERRO ';
							}
						}
					}

					//Se esta sem amrca mas existe, vou atualizar:
					if($CODIGO_MARCA != '' || $NOME_MARCA != '') // INSERE/ATUALIZA
					{
						$marca_id=0;

						$prod_marca = detect_brand($CODIGO_MARCA,$NOME_MARCA,$acoes_marcas,$marcas_atraves_nome,$cria_marcas_novas,$fh);
						$marca_id = $prod_marca['marca_id'];
						$marca_nome = $prod_marca['marca_nome'];

						echo $marca_nome;
						if($marca_id>0)
						{
							$sql = " UPDATE products SET ID_Marca = $marca_id WHERE ID = '".$prod_info['prod_id']."'";
							$res = Runquery($db_link,$DB_name,$sql);
						}
					}
				}
				elseif($como_importar == 4)//Apenas atualizar preço e stock
				{
					echo "(Vamos atualizar apenas os stocks e preços)";
					//$start = microtime(true);
					// Vamos atualizar apenas os stocks e preços
					$prod_info = detect_product($prod_fam_id,$marca_id,$CODIGO_PRODUTO,$NOME_PRODUTO,$RESUMO_PRODUTO,$CARACTERISTICAS_PRODUTO,$CLASS_ENERGITICA_PRODUTO,$LINK_CLASS_ENERGITICA_PRODUTO,$PESO_PRODUTO,$EAN_PRODUTO,$como_importar, ($Peso !='' ? 1 : 0),$codigo_importacao,$key_field,$fh);
					//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao detect_product: '.$time_elapsed_secs." segundos\n");

					//$start = microtime(true);
					// SO ENTRA SE NÃO FOR PARA ELIMINAR
					if($prod_info['prod_id'] > 0)
					{
						if($Margem != '')
						{
							$PRECO_PRODUTO = $PRECO_PRODUTO * (1+($Margem / 100));
						}

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
							update_products_precos($prod_info['prod_id'],$PRECO_PRODUTO, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0),$Supplier_ID,$price_conf);
						}
						else update_products_precos($prod_info['prod_id'], 0, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0),$Supplier_ID,$price_conf);

						fwrite($fh,$ll.'% - ('.$linha.' de '.$total_produtos.') - REF: '.$CODIGO_PRODUTO.', EAN; '.$EAN_PRODUTO.', Nome: '.$NOME_PRODUTO."(Prod: ".$prod_info['prod_id'].", Preço: $PRECO_PRODUTO, Stock: $STOCK_PRODUTO) (a)\n");
					}
					else fwrite($fh,$ll.'% - ('.$linha.' de '.$total_produtos.') - REF: '.$CODIGO_PRODUTO.', EAN; '.$EAN_PRODUTO.', Nome: '.$NOME_PRODUTO."(Sem familia/marca) (a)\n");

					//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao update_products_precos: '.$time_elapsed_secs." segundos\n");

				}
				else
				{
					$prod_fam_id = 0;
					$fam_id = 0;
					$subfam_id = 0;
					$subfam_id2 = 0;


					//$start = microtime(true);

					if($CODIGO_CATEGORIA != '' && $acoes_familias > 0) // INSERE/ATUALIZA
					{
						$prod_fam = detect_family($CODIGO_CATEGORIA,$NOME_CATEGORIA, 0,$acoes_familias); // NIVEL 1

						if($prod_fam['fam_id'] > 0)
						{
							$fam_id = $prod_fam['fam_id'];
							if($CODIGO_SUBCATEGORIA != '')
							{
								$prod_subfam = detect_family($CODIGO_SUBCATEGORIA,$NOME_SUBCATEGORIA,$fam_id,$acoes_familias); // NIVEL 2
								if($prod_fam['fam_id'] > 0)
								{
									$subfam_id = $prod_subfam['fam_id'];
									if($CODIGO_SUBCATEGORIA2 != '')
									{
										$prod_subfam2 = detect_family($CODIGO_SUBCATEGORIA2,$NOME_SUBCATEGORIA2,$subfam_id,$acoes_familias); // NIVEL 3
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
						// SE NO NIVEL 3 EXISTIR CODIGO ENTAO O PRODUTO FICA DENTRO
						if($CODIGO_SUBCATEGORIA2 !='' && $prod_fam_id == 0)
						{
							$prod_fam = detect_family($CODIGO_SUBCATEGORIA2, '', 0,$acoes_familias);
							$prod_fam_id = $prod_fam['fam_id'];
						}
						// SE NO NIVEL 2 EXISTIR CODIGO ENTAO O PRODUTO FICA DENTRO
						if($CODIGO_SUBCATEGORIA !='' && $prod_fam_id == 0)
						{
							$prod_fam = detect_family($CODIGO_SUBCATEGORIA, '', 0,$acoes_familias);
							$prod_fam_id = $prod_fam['fam_id'];
						}
						// SE NO NIVEL 1 EXISTIR CODIGO ENTAO O PRODUTO FICA DENTRO
						if($CODIGO_CATEGORIA !='' && $prod_fam_id == 0)
						{
							$prod_fam = detect_family($CODIGO_CATEGORIA, '', 0,$acoes_familias);
							$prod_fam_id = $prod_fam['fam_id'];
						}

					}

					//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao detect_family: '.$time_elapsed_secs." segundos\n");

					echo "(a)";

					if(!$prod_fam_id && $codigo_fam_default != '') $prod_fam_id=$codigo_fam_default;
					// MARCAS
					$marca_id = 0; $marca_nome = '';
					if($CODIGO_MARCA != '' || $NOME_MARCA != '') // INSERE/ATUALIZA
					{
						//$start = microtime(true);
						$prod_marca = detect_brand($CODIGO_MARCA,$NOME_MARCA,$acoes_marcas,$marcas_atraves_nome,$cria_marcas_novas,$fh);
						$marca_id = $prod_marca['marca_id'];
						$marca_nome = $prod_marca['marca_nome'];
						//fwrite($fh,$ll." CODIGO_MARCA = $CODIGO_MARCA | NOME_MARCA = $NOME_MARCA | marca_id = $marca_id | cria_marcas_novas = $cria_marcas_novas | marcas_atraves_nome = $marcas_atraves_nome | acoes_marcas = $acoes_marcas\n");
						//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao detect_brand: '.$time_elapsed_secs." segundos\n");
					}

					if($prod_fam_id > 0 && $marca_id > 0)
					{

						echo "\n";
						echo 'fam_id='.$prod_fam_id.'|CODIGO_MARCA='.$CODIGO_MARCA.'|';
						echo "CODIGO_PRODUTO = ".$CODIGO_PRODUTO;
						echo "EAN_PRODUTO = ".$EAN_PRODUTO;
						echo "key_field = ".$key_field;

						//$start = microtime(true);

						$prod_info = \detect_product($prod_fam_id,$marca_id,$CODIGO_PRODUTO,$NOME_PRODUTO,$RESUMO_PRODUTO,$CARACTERISTICAS_PRODUTO,$CLASS_ENERGITICA_PRODUTO,$LINK_CLASS_ENERGITICA_PRODUTO,$PESO_PRODUTO,$EAN_PRODUTO,$como_importar, ($Peso !='' ? 1 : 0),$codigo_importacao,$key_field,$fh);

						//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao detect_product: '.$time_elapsed_secs." segundos\n");

						echo "(b)";

						$start = microtime(true);

						// SO ENTRA SE NÃO FOR PARA ELIMINAR
						if($prod_info['prod_id'] > 0 && $como_importar != 3)
						{
							if($prod_fam_id > 0) detect_famprod($prod_info['prod_id'],$prod_fam_id);
							if($Margem != '')
							{
								$PRECO_PRODUTO = $PRECO_PRODUTO * (1+($Margem / 100));
							}
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
								echo "(c)";
								update_products_precos($prod_info['prod_id'],$PRECO_PRODUTO, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0),$Supplier_ID,$price_conf);
							}
							else update_products_precos($prod_info['prod_id'], 0, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0),$Supplier_ID,$price_conf);

							$time_elapsed_secs = microtime(true) - $start;//fwrite($fh,'funcao update_products_precos: '.$time_elapsed_secs." segundos\n");
							echo 'funcao update_products_precos: '.$time_elapsed_secs." segundos\n";



							if($como_importar != 4 && $como_importar != 5)//Apenas atualiza preços e stock
							{

								// INSERÇÃO FILTROS
								/*$sql = 'UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_info['prod_id'].'"';
								$res = Runquery($db_link,$DB_name,$sql);

								$sql = " UPDATE products_objs_combo SET obj_val = 0 WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' ";
								$res = Runquery($db_link,$DB_name,$sql);
								$cria_vazio = 0;
								*/




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
											$value_id_import_1 = detect_filter_values(0,$category_id_import_1,$tipos_fil, 'pt',$tipos_fil);
											if($value_id_import_1 > 0)
											{
												detect_filter_products($prod_info['prod_id'],$prod_fam_id,$marca_id,$category_id_import_1,$value_id_import_1, 0);
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
									if($Medidas!='') $value_id_import_1 = detect_filter_values(0,$category_id_import_1,$Medidas, 'pt',$Medidas);
									if($value_id_import_1 > 0 && $Medidas != '')
									{
										detect_filter_products($prod_info['prod_id'],$prod_fam_id,$marca_id,$category_id_import_1,$value_id_import_1, 1);
										$c_obj_id = 0;
										$sql = 'SELECT obj_id FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$prod_info['prod_id'].'" AND obj_combo="'.$category_id_import_1.'-'.$value_id_import_1.'"';
										$rs4->db_open($db_link,$DB_name,$sql);
										if(!$rs4->EOF())
										{
											$c_obj_id = $rs4->db_data['obj_id'];
										}
										$rs4->db_close();
										products_objs_filtros_save_sync($prod_info['prod_id'],$c_obj_id, 0,$PVP1,$Stock, 0,$category_id_import_1.'-'.$value_id_import_1,$Medidas);
										// filtros_ficha_produto_adm_sync($prod_id=0,$fam_id=0,$obj_preco = 0,$obj_stock = 0,$obj_ref = '')
										$cria_vazio = 1;
									}
								} */
								//$start = microtime(true);


								/*
								echo "(d)";
								// MARCA
								if($marca_id > 0 && $marca_nome != '')
								{
									$category_id_import_1 = 223; $value_id_import_1 = 0;
									$value_id_import_1 = detect_filter_values(0,$category_id_import_1,$marca_nome, 'pt',$marca_nome);
									if($value_id_import_1 > 0 && $marca_nome != '')
									{
										detect_filter_products($prod_info['prod_id'],$prod_fam_id,$marca_id,$category_id_import_1,$value_id_import_1, 0);
										$cria_vazio = 1;
									}
								}
								echo "(e)";
								$sql = "DELETE FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' AND obj_val=0";
								$res = Runquery($db_link,$DB_name,$sql);
								if($cria_vazio == 0) detect_filter_products($prod_info['prod_id'],$prod_fam_id,$marca_id, 0, 0, 0);
								$sql = 'DELETE FROM filters_products WHERE FLAG=0 and prod_id="'.$prod_info['prod_id'].'"';
								$res = Runquery($db_link,$DB_name,$sql);

								*/

								//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao FILTROS: '.$time_elapsed_secs." segundos\n");


								//$start = microtime(true);

								$original = '';

								//IMAGENS

								$log_img = 'T='.count($imagem);
								for($z=0; $z<count($imagem);$z++)
								{
									if(
										!file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.jpg')
										&&
										!file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.png')
									)
									{
										echo "(i)";
										$url = ''; $original = $imagem[$z];
										if($Imagem_conf == 2) // URL EXTERNA FTP (DISCOTECA JUVENIL)
										{
											$log_img=' IMG URL: ';
											$img = '';
											$img = str_replace('ftp://discotecajuvenil.dyndns.org/', 'ftp://GadgetAlert:9NswSzXQ7Q@discotecajuvenil.dyndns.org/', $imagem[$z]);
											download_image1($img, dirname( __FILE__ ).'/../../files/importar/imagens/'.basename($imagem[$z]));
											if(file_exists(dirname( __FILE__ ).'/../../files/importar/imagens/'.basename($imagem[$z])))
											{
												$url = dirname( __FILE__ ).'/../../files/importar/imagens/'.basename($imagem[$z]);
											}
											else echo ' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
										}
										elseif($Imagem_conf == 0) // IMAGEM DE URL EXTERNO
										{
											$log_img=' IMG URL: ';
											$file = $prod_info['prod_id'].'_'.($z+1).'.jpg'; //file name to write to include location if needed

											$file_headers = @get_headers($imagem[$z]);
											$exists = true;
											if($file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0]=='HTTP/1.1 403 Forbidden') {$exists = false;}
											if($exists==true)
											{
												$current = file_get_contents($imagem[$z]); // read the created file
												if(file_put_contents($GLOBALS['site_shell_url'].'files/importar/imagens/'.$file,$current))
												{
													$log_img.=' OK ';
													$imagem[$z] = $file;
													if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($file)))
														$url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($file);
												}
												else $log_img.=' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
											}
											else
											{
												if($file_headers[0] == 'HTTP/1.1 404 Not Found')$log_img.=' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
												else $log_img.=' SEM PERMISSOES: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
											}

										}
										else
										{
											$log_img=' IMG: ';
											if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png';
											elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg')) $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg';
											elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif';
										}
										if($url != '' && file_exists($url))
										{
											$ext = pathinfo($url, PATHINFO_EXTENSION);
											$url_new = dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext;
											if($ext != '' && !file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext))
											{
												$log_img.=' CP OK ';
												@unlink(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext);
												copia_segura($url,$url_new);
												$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
												$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
												$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_info['prod_id'].'", "'.$ext.'", '.($z+1).', "'.addslashes($original).'")');
											}
											else $log_img.=' CP ERRO ';
											/* elseif($ext != '')
											{
												copy($url,$url_new);
												$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
												$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
												$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_info['prod_id'].'", "'.$ext.'", '.($z+1).', "'.addslashes($original).'")');
												// echo 'INSERT INTO products_images (ID_ORG,img_ext,Ordem) VALUES ("'.$prod_info['prod_id'].'","'.$ext.'",'.($z+1).')'."\n";
											} */
										}
									}



								}

								//$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'IMAGENS: '.$time_elapsed_secs." segundos\n");

							}



							if($prod_fam_id > 0 && $marca_id > 0)
							{
								echo "(g)";
								//Agora já necessitamos de gerar filtros, sao gerados pelo index.php
								//gera_produtos($prod_info['prod_id'],$prod_fam_id,$marca_id, 1, 'pt');
								//fwrite($fh,$ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO." - importado e ativo no website\n");
								//fwrite($fh,$ll.'% ['.$linha.'/'.$total_produtos.'], Ref: '.$CODIGO_PRODUTO.', EAN; '.$EAN_PRODUTO.', Nome: '.$NOME_PRODUTO.', Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id'].", PVP: $PRECO_PRODUTO, STK: $STOCK_PRODUTO, ID MARCA: $marca_id) $log_img (b)\n");
								fwrite($fh,$ll.'% | Ref: '.$CODIGO_PRODUTO.' | EAN: '.$EAN_PRODUTO.' | Nome: '.substr($NOME_PRODUTO,25).' | Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id']." | PVP: $PRECO_PRODUTO | STK: $STOCK_PRODUTO | MARCA: $marca_id | FAM: $prod_fam_id) $log_img (b)\n");
								$total_produtos_ativos[] = $prod_info['prod_id'].' - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."\n";
							}
							else
							{
								//fwrite($fh,$ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(Produto Sem familia ou marca)\n");
								fwrite($fh,$ll.'% | Ref: '.$CODIGO_PRODUTO.' | EAN: '.$EAN_PRODUTO.' | Nome: '.substr($NOME_PRODUTO,25).' | Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id']." | PVP: $PRECO_PRODUTO | STK: $STOCK_PRODUTO | MARCA: -- | FAM: --) $log_img (b)\n");
								//fwrite($fh,$ll.'% ['.$linha.'/'.$total_produtos.'], Ref: '.$CODIGO_PRODUTO.', EAN; '.$EAN_PRODUTO.', Nome: '.$NOME_PRODUTO.', Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id'].", PVP: $PRECO_PRODUTO, STK: $STOCK_PRODUTO, ID MARCA: $marca_id) (Sem familia/marca) $log_img (b)\n");
							}
						}
					}
					else
					{
						echo "(h)";

						//$start = microtime(true);

						// Vamos atualizar apenas os stocks e preços
						$prod_info = detect_product($prod_fam_id,$marca_id,$CODIGO_PRODUTO,$NOME_PRODUTO,$RESUMO_PRODUTO,$CARACTERISTICAS_PRODUTO,$CLASS_ENERGITICA_PRODUTO,$LINK_CLASS_ENERGITICA_PRODUTO,$PESO_PRODUTO,$EAN_PRODUTO,$como_importar, ($Peso !='' ? 1 : 0),$codigo_importacao,$key_field,$fh);
						echo "(h2)";

						// SO ENTRA SE NÃO FOR PARA ELIMINAR
						if($prod_info['prod_id'] > 0 && $como_importar != 3)
						{
							if($Margem != '')
							{
								$PRECO_PRODUTO = $PRECO_PRODUTO * (1+($Margem / 100));
							}
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
								update_products_precos($prod_info['prod_id'],$PRECO_PRODUTO, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0),$Supplier_ID,$price_conf);
							}
							else update_products_precos($prod_info['prod_id'], 0, 0,$STOCK_PRODUTO,$CODIGO_PRODUTO,$EAN_PRODUTO, 0, ($Stock !='' ? 1 : 0),$Supplier_ID,$price_conf);



							//Mas apesar de nao ter familia, pode ter imagem
							$original = '';
							$log_img = 'T='.count($imagem);

							for($z=0; $z<count($imagem);$z++)
							{
								echo "(i)";
								$url = ''; $original = $imagem[$z];
								if($Imagem_conf == 0) // IMAGEM DE URL EXTERNO
								{
									$log_img=' IMG URL: ';
									$file = $prod_info['prod_id'].'_'.($z+1).'.jpg'; //file name to write to include location if needed

									$file_headers = @get_headers($imagem[$z]);
									$exists = true;
									if($file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0]=='HTTP/1.1 403 Forbidden') {$exists = false;}
									if($exists==true)
									{
										$current = file_get_contents($imagem[$z]); // read the created file
										if(file_put_contents($GLOBALS['site_shell_url'].'files/importar/imagens/'.$file,$current))
										{
											$log_img.=' OK ';
											$imagem[$z] = $file;
											if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($file)))
												$url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($file);
										}
										else $log_img.=' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
									}
									else
									{
										if($file_headers[0] == 'HTTP/1.1 404 Not Found')$log_img.=' ERRO DOWNLOAD de: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
										else $log_img.=' SEM PERMISSOES: '.$imagem[$z].' para '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;
									}
								}
								else
								{
									$log_img=' IMG: ';
									if(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.png';
									elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg')) $url = dirname( __FILE__ ).'/../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.jpg';
									elseif(file_exists(dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif')) $url = dirname( __FILE__ ).'/../../../files/importar/imagens/'.($Imagem_pasta!='' ? $Imagem_pasta.'/' : '').trim($imagem[$z]).'.gif';
								}
								if($url != '' && file_exists($url))
								{
									$ext = pathinfo($url, PATHINFO_EXTENSION);
									$url_new = dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext;
									if($ext != '' && !file_exists(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext))
									{
										$log_img.=' CP OK ';
										@unlink(dirname( __FILE__ ).'/../../../files/products/'.$prod_info['prod_id'].'_'.($z+1).'.'.$ext);
										copia_segura($url,$url_new);
										$res = Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.$ext.'" WHERE ID="'.$prod_info['prod_id'].'"');
										$res = Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem='.($z+1));
										$res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_info['prod_id'].'", "'.$ext.'", '.($z+1).', "'.addslashes($original).'")');
									}
									else $log_img.=' CP JÁ TEM ';
								}
							}

							//fwrite($fh,$ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(Prod: ".$prod_info['prod_id'].", Preço atualizado = $PRECO_PRODUTO | stock = $STOCK_PRODUTO)\n");
							//

							fwrite($fh,$ll.'% | Ref: '.$CODIGO_PRODUTO.' | EAN: '.$EAN_PRODUTO.' | Nome: '.substr($NOME_PRODUTO,25).' | Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id']." | PVP: $PRECO_PRODUTO | STK: $STOCK_PRODUTO | MARCA: $marca_id | FAM: $prod_fam_id) $log_img (b)\n");
							//fwrite($fh,$ll.'% ['.$linha.'/'.$total_produtos.'], Ref: '.$CODIGO_PRODUTO.', EAN; '.$EAN_PRODUTO.', Nome: '.$NOME_PRODUTO.', Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id'].", PVP: $PRECO_PRODUTO, STK: $STOCK_PRODUTO, ID MARCA: $marca_id) $log_img (c)\n");
						}
						else
						{
							fwrite($fh,$ll.'% | Ref: '.$CODIGO_PRODUTO.' | EAN: '.$EAN_PRODUTO.' | Nome: '.substr($NOME_PRODUTO,25).' | Marca: '.$NOME_MARCA.'(ID: '.$prod_info['prod_id']." | PVP: $PRECO_PRODUTO | STK: $STOCK_PRODUTO | MARCA: $marca_id | FAM: $prod_fam_id) $log_img (b)\n");
							//fwrite($fh,$ll.'% - ('.$linha.' de '.$total_produtos.') - REF: '.$CODIGO_PRODUTO.', EAN; '.$EAN_PRODUTO.', Nome: '.$NOME_PRODUTO."(Sem familia/marca - $NOME_MARCA) NÃO IMPORTOU $log_img (c)\n");
						}
						//else fwrite($fh,$ll.'% - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(Produto Sem familia ou marca)\n");
						////$time_elapsed_secs = microtime(true) - $start;fwrite($fh,'funcao H: '.$time_elapsed_secs." segundos\n");
					}
				}
				// if( $CODIGO_PRODUTO == '10409000') die("parou");
			}
			else
			{
				fwrite($fh,' BBBB '."\n");
				fwrite($fh,$ll.'% - ('.$linha.' de '.$total_produtos.') - '.$CODIGO_PRODUTO.': '.$NOME_PRODUTO."(FORA GAMA)  NÃO IMPORTOU \n");
			}

			$linha++;
		}
		else $fim=1;

		echo "(h2)";

		$total2--;
	}












	//Comentei esta parte do codigo porque o novo sistema de filtros nao necessita destes ficheiros
	/*fwrite($fh, "99% - a gerar filtros...\n");
	gera_produtos_filtros('pt');

	if($codigo_importacao !='' && $Eliminar_conf >= 1)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($db_link)
		{
			$sql = 'UPDATE products SET Aproved=0 WHERE Flag=0 AND codigo_importacao = "'.$codigo_importacao.'"';
			$res = Runquery($db_link,$DB_name,$sql);
		}
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
	}*/


//die();



	fwrite($fh, "100% - Importacao chegou ao fim\n");
	fwrite($fh, "\nTotal de produtos Ativos no website: ".count($total_produtos_ativos)."\n");
	for($a=0; $a<count($total_produtos_ativos); $a++)
	{
		fwrite($fh, "&nbsp;&nbsp; - ".$total_produtos_ativos[$a]."\n");
	}
	// fwrite($fh, "Total de produtos Ativos no website: ".count($total_produtos_ativos)."\n");
	fclose($fh);
	//TODOss send email end import
	if($email_address)
	{
		$mailsubject	=	"Importação - Fim";
		$message		=	date('dmYHis').' - Fim de importação'; //TODOss colocar date a hora
		@send_email($email_address,$mailsubject,$message);
	}
	// FICHEIRO DE LOG
	if($log_activation==1) // if logs are active
	{
		if(copy($myFile, str_replace('importar/', 'importado/', str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_ok'.date('YmdHis').'.txt',$myFile))))
		{
			unlink($myFile);
		}
	}
	else
	{
		unlink($myFile);
	}
	if($ficheiro_caminho == 2) // MANUAL
	{
		if(copy(dirname(__FILE__) .'/../../../files/importar/importa_manual'.$Supplier_ID.'.csv',  dirname(__FILE__) .'/../../../files/importado/importa_manual'.$Supplier_ID.'_'.date('YmdHis').'.csv'))
		{
			unlink(dirname(__FILE__) .'/../../../files/importar/importa_manual'.$Supplier_ID.'.csv');
		}
	}
	if(copy($file_path,$file_path_done)) unlink($file_path);
	if(copy($file_path_old,$file_path_old_done)) unlink($file_path_old);

	$sql = 'UPDATE a_importador_stats set DataEnd="'.date('Y-m-d H:i:s').'",TotalProdsAtivos = "'.count($total_produtos_ativos).'" WHERE ID = '.$a_importador_stats_id;
	$res = Runquery($db_link,$DB_name,$sql);
?>