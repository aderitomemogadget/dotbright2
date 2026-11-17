<?php
function getImportadorConfiguracoes_2($Supplier_ID)
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;
	$sql2 = 'SELECT * FROM a_importador_configuracoes WHERE conf_id='.$Supplier_ID.'';
	$obj->db_open($db_link,$GLOBALS['DB_name'],$sql2);
	return $obj;
}
function limpa_special_import($s)
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
	$v=array("á","à","ã","â","ä");	$s=str_replace($v,'a',$s);
	$v=array("é","è","ê","ë");		$s=str_replace($v,'e',$s);
	$v=array("í","ì","î","ï");		$s=str_replace($v,'i',$s);
	$v=array("ó","ò","õ","ô","ö");	$s=str_replace($v,'o',$s);
	$v=array("ú","ù","û","ü");		$s=str_replace($v,'u',$s);
	$v=array("ñ");					$s=str_replace($v,'n',$s);
	$v=array("ç");					$s=str_replace($v,'c',$s);
	$v=array("Á","À","Ã","Â","Ä");	$s=str_replace($v,'A',$s);
	$v=array("É","È","Ê","Ë");		$s=str_replace($v,'E',$s);
	$v=array("Í","Ì","Î","Ï");		$s=str_replace($v,'I',$s);
	$v=array("Ó","Ò","Õ","Ô","Ö");	$s=str_replace($v,'O',$s);
	$v=array("Ú","Ù","Û","Ü");		$s=str_replace($v,'U',$s);
	$v=array("Ñ");					$s=str_replace($v,'N',$s);
	$v=array("Ç");					$s=str_replace($v,'C',$s);
	return $s;
}

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
	ftp_pasv($id_ftp, PASV); //Estabelece o modo de conexão
	return array('id_ftp' => $id_ftp, 'estado' => $return); //Devolve o manejador à função
}
function ftp_get2( $ftp,$local_file,$server_file,$tipo_ftp)
{
	return ftp_get($ftp,$local_file,$server_file,$tipo_ftp);
}
function copia_segura_imp($image,$dest)
{
	//Vamos verificar qual o tipo de ficheiro:
	$original_extension=pathinfo($dest, PATHINFO_EXTENSION);
	$dest2=str_replace('.'.$original_extension, '',$dest);
	// if($original_extension == 'jpg'){ return copy($image,$dest2.'.jpg');}
	// if($original_extension == 'JPG'){ return copy($image,$dest2.'.JPG');}
	// if($original_extension == 'png'){ return copy($image,$dest2.'.png');}
	// if($original_extension == 'PNG'){ return copy($image,$dest2.'.PNG');}
	// if($original_extension == 'gif'){ return copy($image,$dest2.'.gif');}
	// if($original_extension == 'GIF'){ return copy($image,$dest2.'.GIF');}
	// if($original_extension == 'pdf'){ return copy($image,$dest2.'.pdf');}
	// if($original_extension == 'PDF'){ return copy($image,$dest2.'.PDF');}
	if($original_extension == 'csv'){ return copy($image,$dest2.'.csv');}
	if($original_extension == 'CSV'){ return copy($image,$dest2.'.CSV');}
	// if($original_extension == 'txt'){ return copy($image,$dest2.'.txt');}
	// if($original_extension == 'TXT'){ return copy($image,$dest2.'.TXT');}
	// if($original_extension == 'xml'){ return copy($image,$dest2.'.xml');}
	// if($original_extension == 'XML'){ return copy($image,$dest2.'.XML');}
	// if($original_extension == 'xls'){ return copy($image,$dest2.'.xls');}
	// if($original_extension == 'xlsx'){ return copy($image,$dest2.'.xlsx');}
	// if($original_extension == 'XLS'){ return copy($image,$dest2.'.XLS');}
	// if($original_extension == 'XLSX'){ return copy($image,$dest2.'.XLSX');}
	return false;
}
function devolvevalor($data,$campo,$tipo='')
{
	$valor = $data[array_search($campo,$GLOBALS['names'])];
	if($tipo=='num')$valor = str_replace(' ','',$valor);

	$valor = trim($valor); // tira espaços no fim
	$valor = trim($valor,"'"); // tira a plica do início do texto

	if($tipo=='dt') $valor = convert_data($valor);
	elseif($tipo=='num')
	{
		$valor = str_replace('€','',$valor);
		if($GLOBALS['Separador_decimais']=='.')
		{
			$valor = str_replace(',','',$valor);
		}
		else
		{
			$valor = str_replace('.','',$valor);
			$valor = str_replace(',','.',$valor);
		}
		$valor = floatval($valor);
		if($valor == '') $valor = 0;
	}
	elseif($tipo == 'perc') $valor = str_replace('%','',$valor);
	return $valor;
}
function log_error($txt)
{
	echo $txt.'\n';
	$myFile = dirname( __FILE__ )."/../../../files/importar/log_error_".date('YmdHis').".txt";
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh,$txt);
	fclose($fh);
	// if(copy($GLOBALS['file_path'],$GLOBALS['file_path_done']))
	// {
		// @unlink($GLOBALS['file_path']);
	// }
	// @unlink(dirname( __FILE__ )."/../files/importar/atualizar.txt");
}

function devolve_stock_def($st1='',$st2='',$st3='',$st4='',$valor='')
{
	$valor=str_replace('"','',$valor);

	//Vamos devolver o valor correspondente

	if($st1 != '' && ($valor == $st1 || substr_count($st1,$valor)>0)) return 0;
	elseif($st2 != '' && ($valor == $st2 || substr_count($st2,$valor)>0)) return 1;
	elseif($st3 != '' && ($valor == $st3 || substr_count($st3,$valor)>0)) return 2;
	elseif($st4 != '' && ($valor == $st4 || substr_count($st4,$valor)>0)) return 3;
	else 
	{
		$valor = (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
		return intval($valor);
	}
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
function detect_products_objs($ID_PROD=0,$obj_pvp=0,$obj_qtdmin=0,$obj_stock=0,$obj_ref='',$obj_ean='',$tipo_pvp=0,$com_stock=0)
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;$rs2 = new DataObj;

	$sql = 'SELECT obj_id FROM products_objs WHERE obj_type_id = "'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$obj_id =  $rs->db_data['obj_id'];
		$fields = '';
		if($com_stock == 1) $fields .= ', obj_stock="'.$obj_stock.'"';
		// if($obj_ean != '') $fields .= ', Ref_Ean="'.$obj_ean.'"';
		$sql = 'UPDATE products_objs SET obj_type="products", obj_pvp="'.$obj_pvp.'", obj_qtdmin="'.$obj_qtdmin.'" '.$fields.' WHERE obj_type_id="'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		// echo $sql."\n";
		$sql = 'UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang = "pt"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
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
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		$obj_id = db_get_last_insert_id($db_link);
		$sql = 'INSERT INTO products_objs_lang (Cabecalho, ID_PAI, Lang) VALUES ("'.$obj_ref.'", "'.$obj_id.'", "pt")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	$rs->db_close();
}
function detect_famprod($ID_PROD=0,$ID_ORG=0)
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;$rs2 = new DataObj;
	$sql = 'SELECT ID FROM products_families_obj WHERE ID_PROD = "'.$ID_PROD.'" AND ID_ORG = "'.$ID_ORG.'" ';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		/*$sql = 'UPDATE products_families_obj set ID_ORG =  "'.$ID_ORG.'" WHERE  ID_PROD = "'.$ID_PROD.'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);*/
	}
	else
	{
		$sql = 'SELECT ID_PROD FROM products_families_obj WHERE ID_PROD = "'.$ID_PROD.'"';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			/*$sql = 'DELETE FROM products_families_obj WHERE ID_PROD = "'.$ID_PROD.'"';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);*/
		}
		else
		{
			$sql = 'INSERT INTO products_families_obj (ID_PROD, ID_ORG) VALUES ("'.$ID_PROD.'", "'.$ID_ORG.'")';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
		$rs2->db_close();

	}
	$rs->db_close();
}
function detect_family($codigo_importacao,$ItemDesc='',$FAMID=0,$acoes_familia=0)
{
	global $Supplier_ID;
	$fam_id=0;
	if($codigo_importacao != '')
	{
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$rs2=new DataObj;
		//$sql='SELECT ID FROM products_families WHERE codigo_importacao LIKE "%;'.limpa_special_sync($codigo_importacao).';%"';
		//$sql='SELECT ID_FAM as ID FROM a_importador_familias WHERE Codigo LIKE "%;'.limpa_special_sync($codigo_importacao).';%" AND conf_id = "'.$Supplier_ID.'"';
		$sql='SELECT ID_FAM as ID FROM a_importador_familias WHERE Codigo LIKE "%;'.($codigo_importacao).';%" AND conf_id = "'.$Supplier_ID.'"';

		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$fam_id=$rs->db_data['ID'];
			if($acoes_familia == 1 || $acoes_familia == 3)
			{
				if($FAMID > 0)$sql='UPDATE products_families SET ID_ORG="'.$FAMID.'", FLAG=1, visivel=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';
				else $sql='UPDATE products_families SET FLAG=1, visivel=1, data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';
				$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
				for($a=0;$a<count($GLOBALS['linguas_importa']); $a++)
				{
					if(trim($ItemDesc) != '') detect_family_lang($fam_id,$ItemDesc,$GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		elseif($acoes_familia == 1 || $acoes_familia == 2)
		{
			$sql='INSERT INTO products_families (ID_ORG, visivel, data_atualizacao, codigo_importacao, FLAG) VALUES ("'.$FAMID.'", 1,"'.date('Y-m-d H:i:s').'", ";'.limpa_special_sync($codigo_importacao).';", 1)';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			$fam_id=db_get_last_insert_id($db_link);
			if($fam_id > 0)
			{
				for($a=0;$a<count($GLOBALS['linguas_importa']);$a++)
				{
					if(trim($ItemDesc) != '')detect_family_lang($fam_id,$ItemDesc,$GLOBALS['linguas_importa'][$a]);
					else  detect_family_lang($fam_id,$codigo_importacao,$GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		$rs->db_close();
		return array('fam_id' => $fam_id);
	}
	return array();
}
function detect_family_lang($fam_id,$ItemDesc,$lingua='')
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2 = new DataObj;
	$sql = 'SELECT ID FROM products_families_lang WHERE ID_PAI = "'.$fam_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs2->EOF())
	{
		$sql = 'UPDATE LOW_PRIORITY products_families_lang SET Cabecalho="'.addslashes($ItemDesc).'" WHERE ID_PAI="'.$fam_id .'" and Lang="'.$lingua.'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	else
	{
		$sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'", "'.addslashes($ItemDesc).'", "'.$lingua.'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	$rs2->db_close();
}
function detect_brand($codigo_importacao='',$ItemDesc='',$acoes_marcas=0,$marcas_atraves_nome=0,$cria_marcas_novas=0,$fh='')
{
	global $Supplier_ID;
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;

	$marca_id=0;
	//Procura marca por codigo de importacao
	if($codigo_importacao != '')
	{
		//$sql = 'SELECT ID FROM brands WHERE codigo_importacao LIKE "%;'.limpa_special_sync($codigo_importacao).';%"';
		$sql='SELECT ID_FAM as ID FROM a_importador_brands WHERE Codigo LIKE "%;'.limpa_special_sync($codigo_importacao).';%" AND conf_id = "'.$Supplier_ID.'"';

		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$marca_id = $rs->db_data['ID'];
			if($acoes_marcas == 1 || $acoes_marcas == 2)
			{
				$sql = 'UPDATE brands SET data_atualizacao="'.date('Y-m-d H:i:s').'", FLAG=1, Visivel=1 WHERE ID = "'.$marca_id.'"';
				$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					if($ItemDesc != '') detect_brand_lang($marca_id,$ItemDesc,$GLOBALS['linguas_importa'][$a]);
				}
			}
		}
		$rs->db_close();
	}
	//Procura marca pelo nome
	if($marca_id <=0 && $marcas_atraves_nome == 1 && $ItemDesc != '')
	{
		$sql = 'SELECT ID_PAI FROM brands_lang WHERE Lower(Nome) = "'.strtolower($ItemDesc).'" AND Lang="'.$GLOBALS['linguas_importa'][0].'"';
//fwrite($fh,"$sql \n");
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF()) $marca_id = $rs->db_data['ID_PAI'];
		$rs->db_close();
	}

	//Cria marca
	if($marca_id <=0 && $cria_marcas_novas == 1 && $ItemDesc != '')
	{
		$sql = 'INSERT INTO brands (Visivel, codigo_importacao, data_atualizacao, FLAG) VALUES (1, ";'.limpa_special_sync($codigo_importacao).';", "'.date('Y-m-d H:i:s').'", 1)';
//fwrite($fh,"$sql \n");
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		$marca_id = db_get_last_insert_id($db_link);
		if($marca_id > 0)
		{
			for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
			{
				detect_brand_lang($marca_id,$ItemDesc,$GLOBALS['linguas_importa'][$a]);
			}
		}
	}
	$rs->db_close();


	//Se consegui vcriar ou obter a marca devolve array
	if($marca_id > 0)
	{
		return array('marca_id' => $marca_id, 'marca_nome' => $ItemDesc);
	}
	else return array('marca_id' => 0, 'marca_nome' => '');

	//return array();
}
function detect_brand_lang($marca_id,$ItemCode,$lingua)
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2 = new DataObj;
	$sql = 'SELECT ID FROM brands_lang WHERE ID_PAI = "'.$marca_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs2->EOF())
	{
		$sql = 'UPDATE LOW_PRIORITY brands_lang SET Nome="'.addslashes($ItemCode).'" WHERE ID_PAI="'.$marca_id .'" and Lang="'.$lingua.'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	else
	{
		$sql = 'INSERT INTO brands_lang (ID_PAI, Nome, Lang) VALUES ("'.$marca_id.'", "'.addslashes($ItemCode).'", "'.$lingua.'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	$rs2->db_close();
}
function detect_product($prod_fam_id=0,$marca_id=0,$ItemCode='',$ItemNome='',$ItemDesc='',$ItemDesc2='',$ItemClass='',$ItemClassLink='',$ItemPeso=0,$EAN_PRODUTO='',$tipo_importacao=0,$tem_peso=0,$codigo_importacao='',$key_field=0,$fh='')
{

	if($key_field == 0)
	{
		//Tem de ter referencia
		if(trim($ItemCode)=='')return;
	}
	else
	{
		//Tem de ter EAN
		if(trim($EAN_PRODUTO)=='')return;
	}
	if(trim($EAN_PRODUTO) != '')$EAN_PRODUTO = str_pad($EAN_PRODUTO, 13, "0", STR_PAD_LEFT);

	//if($fh !='')fwrite($fh,' tipo_importacao  '.$tipo_importacao);

	if($tipo_importacao == 3) // ELIMINAR PRODUTO
	{
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		if($key_field == 0)$sql='DELETE FROM products WHERE Ref="'.$ItemCode.'"';
		else $sql='DELETE FROM products WHERE Ref_Ean="'.$EAN_PRODUTO.'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	else
	{
		$cond='';
		$cond2='';
		$prod_id=0;
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$rs2=new DataObj;

		$start2 = microtime(true);
		if($key_field==0)$sql = 'SELECT ID,Sync,Ref_Ean FROM products WHERE Ref = "'.$ItemCode.'" limit 0,1';
		else $sql = 'SELECT ID,Sync,Ref_Ean FROM products WHERE Ref_Ean = "'.$EAN_PRODUTO.'" limit 0,1';

		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if($rs->EOF())
		{
			//Nao tem, mas pode ter pelo EAN
			if($key_field==0 && $EAN_PRODUTO != '')$sql = 'SELECT ID,Sync,Ref_Ean FROM products WHERE Ref_Ean = "'.$EAN_PRODUTO.'" limit 0,1';
			if($key_field==1)$sql = 'SELECT ID,Sync,Ref_Ean FROM products WHERE Ref = "'.$ItemCode.'" limit 0,1';
			
			$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		}

		if(!$rs->EOF())
		{
			//Produto ja existe mas se nao for para sincronizar deve retornar 0
			$sync=$rs->db_data['Sync'];
			$Ref_Ean_atual=$rs->db_data['Ref_Ean'];
			if($sync == 1)
			{
				$rs->db_close();
				return 0;
			}
			//
			//
			//
			//if($fh !=''){$time_elapsed_secs = microtime(true) - $start2;fwrite($fh,'query: '.$sql.' = '.$time_elapsed_secs." segundos\n");}
			$prod_id = $rs->db_data['ID'];
			//if($fh !='')fwrite($fh,' ja existe:  '.$prod_id);
			if($prod_id>0)
			{
				if($EAN_PRODUTO != '' && $Ref_Ean_atual=='')
				{
					//Nao tem ean mas veio na importacao
					$sql = 'UPDATE products SET Ref_Ean="'.$EAN_PRODUTO.'" WHERE ID = "'.$prod_id.'"';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}

				if($tipo_importacao == 4)//Apenas atualiza preços e stock
				{
					$rs->db_close();
					return array('prod_id' => $prod_id);
				}
				if($tipo_importacao == 0 || $tipo_importacao == 2)  // ATUALIZAR PRODUTO
				{
					$start2 = microtime(true);
					if($key_field==0 && $EAN_PRODUTO != '') $cond .= ', Ref_Ean="'.$EAN_PRODUTO.'"';
					if($ItemClass!='')
					{
						$cond .= ', u_catetiq="'.addslashes($ItemClass).'"';
						$cond .= ', u_coretiq="'.devolve_etiqueta_cor_def($ItemClass).'"';
					}
					if($ItemClassLink != '')
					{
						if(!file_exists(dirname(__FILE__).'/../../../files/products/EE/'.$prod_id.'_1.jpg'))
						{
							if(copia_segura($ItemClassLink, dirname(__FILE__).'/../../../files/products/EE/'.$prod_id.'_1.jpg'))
							{
								$cond .= ', u_imagetiq="'.addslashes($prod_id.'_1.jpg').'"';
							}
						}
					}
					if($prod_fam_id>0)
					{
						//Tem bug este update, se escolhermos familia predefinida fica tudo estragado
						//$sql = 'UPDATE products SET codigo_importacao="'.$codigo_importacao.'", ID_ORG="'.$prod_fam_id.'", ID_Marca="'.$marca_id.'", Flag=1, Aproved=1, Carrinho=1'.($tem_peso == 1 ? ', peso_cliente="'.$ItemPeso.'"' : '').$cond.' WHERE ID = "'.$prod_id.'"';
						$sql = 'UPDATE products SET codigo_importacao="'.$codigo_importacao.'", ID_Marca="'.$marca_id.'", Flag=1, Aproved=1, Carrinho=1'.($tem_peso == 1 ? ', peso_cliente="'.$ItemPeso.'"' : '').$cond.' WHERE ID = "'.$prod_id.'"';
						$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
						//products_families_save($prod_id,$prod_fam_id);
					}
					else
					{
						$sql = 'UPDATE products SET codigo_importacao="'.$codigo_importacao.'", ID_Marca="'.$marca_id.'", Flag=1, Aproved=1, Carrinho=1'.($tem_peso == 1 ? ', peso_cliente="'.$ItemPeso.'"' : '').$cond.' WHERE ID = "'.$prod_id.'"';
						$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
					}
					for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
					{
						detect_product_lang($prod_id,$ItemNome,$ItemDesc,$ItemDesc2,$GLOBALS['linguas_importa'][$a]);
					}

					//if($fh !=''){$time_elapsed_secs = microtime(true) - $start2;fwrite($fh,'detect_product_lang = '.$time_elapsed_secs." segundos\n");}
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
			if($prod_fam_id > 0) $sql = 'INSERT INTO products(codigo_importacao,Ref,ID_ORG,ID_Marca,Data_criacao,Aproved,Carrinho,Flag'.($tem_peso == 1 ? ', peso_cliente' : '').$cond .') VALUES ("'.$codigo_importacao.'","'.$ItemCode.'","'.$prod_fam_id.'","'.$marca_id.'","'.date('Y-m-d H:i:s').'",1,1,1'.($tem_peso == 1 ? ',"'.$ItemPeso.'"' : '').$cond2.')';
			else $sql = 'INSERT INTO products(codigo_importacao,Ref,ID_ORG,ID_Marca,Data_criacao,Aproved,Carrinho,Flag'.($tem_peso == 1 ? ', peso_cliente' : '').$cond.') VALUES ("'.$codigo_importacao.'","'.$ItemCode.'","'.$prod_fam_id.'","'.$marca_id.'","'.date('Y-m-d H:i:s').'",0,1,1'.($tem_peso == 1 ? ',"'.$ItemPeso.'"' : '').$cond2.')';
			//if($fh !='')fwrite($fh,' sql  '.$sql);
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
			$prod_id = db_get_last_insert_id($db_link);


			//if($fh !='')fwrite($fh,' criou:  '.$prod_id);

			if($prod_id > 0)
			{
				$start2 = microtime(true);

				products_families_save($prod_id,$prod_fam_id);
				$cond = '';
				if($ItemClassLink != '')
				{
					if(!file_exists(dirname(__FILE__).'/../../../files/products/EE/'.$prod_id.'_1.jpg'))
					{
						if(copia_segura($ItemClassLink, dirname(__FILE__).'/../../../files/products/EE/'.$prod_id.'_1.jpg'))
						{
							$cond .= ', u_imagetiq="'.addslashes($prod_id.'_1.jpg').'"';
						}
						//O campo chave e a ref ou o EAN
						$sql = 'UPDATE products SET codigo_importacao="'.$codigo_importacao.'"'.$cond.' WHERE ID = "'.$prod_id.'"';
						$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
					}
				}
				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					detect_product_lang($prod_id,$ItemNome,$ItemDesc,$ItemDesc2,$GLOBALS['linguas_importa'][$a]);
				}

				//if($fh !=''){$time_elapsed_secs = microtime(true) - $start2;fwrite($fh,'products_families_save + detect_product_lang = '.$time_elapsed_secs." segundos\n");}
			}
		}
		$rs->db_close();

		if($prod_id > 0)return array('prod_id' => $prod_id);

	}
	return array();
}
function products_families_save($id_prod=0,$id_org=0)
{
	$rs=new DataObj;
	$id_org=intval($id_org);
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT ID FROM products_families_obj WHERE ID_PROD="'.$id_prod.'"');
	if(!$rs->EOF()){$res=Runquery($db_link,$GLOBALS['DB_name'],'UPDATE products_families_obj SET ID_ORG="'.$id_org.'" WHERE ID_PROD="'.$id_prod.'"');}
	else $res=Runquery($db_link,$GLOBALS['DB_name'],'INSERT INTO products_families_obj(ID_PROD,ID_ORG) values ("'.$id_prod.'","'.$id_org.'")');
	$rs->db_close();
}
function detect_product_lang($prod_id,$ItemDesc,$ExtDesc,$ExtDesc2,$lingua)
{
	$rs2=new DataObj;
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$sql = 'SELECT ID,Cabecalho,Texto1,Texto3 FROM products_lang WHERE ID_PAI = "'.$prod_id.'" AND Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs2->EOF())
	{
		$cond='';

		if($ItemDesc!='' && $rs2->db_data["Cabecalho"]==''){if($cond != '')$cond.=',';$cond.='Cabecalho="'.addslashes($ItemDesc).'"';}
		if($ExtDesc!='' && $rs2->db_data["Texto1"]==''){if($cond != '')$cond.=',';$cond.='Texto1="'.addslashes($ExtDesc).'"';}
		if($ExtDesc2!='' && $rs2->db_data["Texto3"]==''){if($cond != '')$cond.=',';$cond.='Texto3="'.addslashes($ExtDesc2).'"';}

		/*if($ItemDesc!='')$cond.='Cabecalho="'.addslashes($ItemDesc).'"';
		if($ExtDesc!=''){if($cond != '')$cond.=',';$cond.='Texto1="'.addslashes($ExtDesc).'"';}
		if($ExtDesc2!=''){if($cond != '')$cond.=',';$cond.='Texto3="'.addslashes($ExtDesc2).'"';}*/
		if(!empty($cond))
		{
			$sql = 'UPDATE LOW_PRIORITY products_lang SET '.$cond.' WHERE ID_PAI="'.$prod_id.'" and Lang="'.$lingua.'"';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
	}
	else
	{
		$sql = 'INSERT INTO products_lang (ID_PAI,Lang,Cabecalho,Texto1,Texto3) VALUES ("'.$prod_id.'","'.$lingua.'","'.addslashes($ItemDesc).'","'.addslashes($ExtDesc).'","'.addslashes($ExtDesc2).'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
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
	if(is_array($output) && count($output)>1)ksort($output);
	$obj->db_close();
	return $output;
}
function detect_filter_categories($category_name='',$lingua='pt')
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2 = new DataObj; $rs3 = new DataObj;
	$sql = 'SELECT category_id FROM filters_categories WHERE value_id_import_ref="'.limpa_special_sync($category_name).'"';
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs2->EOF())
	{
		$category_id = $rs2->db_data['category_id'];
		$sql = 'UPDATE filters_categories SET category_active=1, category_flag=1 WHERE value_id_import_ref="'.limpa_special_sync($category_name).'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		$sql = 'SELECT category_id FROM filters_categories_lang WHERE value_id_import_ref="'.limpa_special_sync($category_name).'" AND category_lang="'.$lingua.'"';
		$rs3->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			$sql = 'UPDATE filters_categories_lang SET category_name="'.addslashes($category_name).'" WHERE value_id_import_ref="'.limpa_special_sync($category_name).'" AND category_lang="'.$lingua.'"';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
		else
		{
			$sql = 'INSERT INTO filters_categories_lang(category_id, category_name, category_lang, value_id_import_ref) VALUES( '.$category_id.', "'.addslashes($category_name).'", "'.$lingua.'", "'.limpa_special_sync($category_name).'")';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
	}
	else
	{
		$sql = 'INSERT INTO filters_categories(category_active, category_flag, value_id_import_ref) VALUES(1, 1, "'.limpa_special_sync($category_name).'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		$category_id = db_get_last_insert_id($db_link);
		$sql = 'INSERT INTO filters_categories_lang(category_id, category_name, category_lang, value_id_import_ref) VALUES( '.$category_id.', "'.addslashes($category_name).'", "'.$lingua.'", "'.limpa_special_sync($category_name).'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	$rs2->db_close(1);
	return $category_id;
}
function detect_filter_values($ordem2=0,$category_id_import=0,$value_id_import='',$lingua='pt',$filter='')
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2 = new DataObj;$rs3 = new DataObj;
	$value_id = 0;
	if(trim($filter) != '')
	{
		$sql = 'SELECT value_id FROM filters_values WHERE value_id_import_ref="'.limpa_special_sync($value_id_import).'"';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			$value_id = $rs2->db_data['value_id'];
			// $sql = 'UPDATE filters_values SET value_order="'.$ordem2.'", category_id='.$category_id_import.',values_name="'.addslashes($filter).'", value_active=1, value_flag=1 WHERE value_id_import_ref="'.limpa_special_sync($value_id_import).'"';
			$sql = 'UPDATE filters_values SET category_id='.$category_id_import.', value_active=1, value_flag=1, data="'.date('Y-m-d H:i:s').'" WHERE value_id_import_ref="'.limpa_special_sync($value_id_import).'"';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
			for($a = 0; $a<count($GLOBALS['linguas_importa']); $a++)
			{
				$lingua = $GLOBALS['linguas_importa'][$a];
				$sql = 'SELECT values_id FROM filters_values_lang
					WHERE
						category_id_import='.$category_id_import.'
						AND value_id_import_ref="'.limpa_special_sync($value_id_import).'"
						AND values_lang="'.$lingua.'"';
				$rs3->db_open($db_link,$GLOBALS['DB_name'],$sql);
				if(!$rs3->EOF())
				{
					$sql = 'UPDATE filters_values_lang SET values_name="'.addslashes($filter).'" WHERE category_id_import='.$category_id_import.' AND value_id_import_ref="'.limpa_special_sync($value_id_import).'" AND values_lang="'.$lingua.'"';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
				else
				{
					$sql = 'INSERT INTO filters_values_lang(values_id,values_name,values_lang,category_id_import,value_id_import_ref) VALUES( '.$value_id.', "'.addslashes($filter).'", "'.$lingua.'", '.$category_id_import.', "'.limpa_special_sync($value_id_import).'")';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
				$rs3->db_close();
			}
		}
		else
		{
			$sql = 'INSERT INTO filters_values(value_order,category_id,value_active,value_flag,category_id_import,value_id_import_ref,data) VALUES('.$ordem2.', '.$category_id_import.', 1, 1, '.$category_id_import.', "'.limpa_special_sync($value_id_import).'", "'.date('Y-m-d H:i:s').'")';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
			$value_id = db_get_last_insert_id($db_link);
			for($a = 0; $a<count($GLOBALS['linguas_importa']); $a++)
			{
				$lingua = $GLOBALS['linguas_importa'][$a];
				$sql = 'INSERT INTO filters_values_lang(values_id,values_name,values_lang,category_id_import,value_id_import_ref) VALUES( '.$value_id.', "'.addslashes($filter).'", "'.$lingua.'", '.$category_id_import.', "'.limpa_special_sync($value_id_import).'")';
				$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
			}
		}
		$rs2->db_close(1);
	}
	return $value_id;
}
function detect_filter_products($prod_id,$id_org=0,$marca_id=0,$category_id_import=0,$value_id_import=0,$mostra=0)
{
	if($prod_id > 0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs2 = new DataObj;
		if($category_id_import==0 && $value_id_import==0) $sql = 'SELECT prod_id FROM filters_products WHERE prod_id = "'.$prod_id.'"';
		else $sql = 'SELECT prod_id FROM filters_products WHERE prod_id = "'.$prod_id.'" AND cat_id="'.$category_id_import.'" AND valor_id="'.$value_id_import.'"';

		$fams = getCategoriesRecursive($id_org);
		// print_r($fams);
		if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $id_org;
		if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
		if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
		if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			$sql ='UPDATE filters_products SET FLAG=1, filtro_area="'.$mostra.'", filtros_rel2="'.$category_id_import.';'.$value_id_import.'",  fam_id1="'.$fam_id1.'", fam_id2="'.$fam_id2.'", fam_id3="'.$fam_id3.'", fam_id4="'.$fam_id4.'", marca_id="'.$marca_id.'" where prod_id = "'.$prod_id.'" AND cat_id="'.$category_id_import.'" AND valor_id="'.$value_id_import.'"';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
		else
		{
			$sql = 'INSERT INTO filters_products(cat_id, valor_id, prod_id, fam_id1, fam_id2, fam_id3, fam_id4, marca_id, filtros_rel2, FLAG, filtro_active, filtro_area)
			VALUES ("'.$category_id_import.'", "'.$value_id_import.'", "'.$prod_id.'", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$fam_id4.'", "'.$marca_id.'", "'.$category_id_import.';'.$value_id_import.'", 1, 1, "'.$mostra.'")';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
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
	$s = preg_replace('/[^a-zA-Z0-9-_.]/', '',$s);
	return $s;
}


function get_web_file($url='')
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	if (curl_errno($ch))
	{
    	$error_msg = curl_error($ch);
    	var_dump($error_msg);
	}
	curl_close($ch);
	return $data;
}


function download_image1($image_url, $image_file)
{
    $fp = fopen ($image_file, 'w+');
    $ch = curl_init($image_url);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
	curl_setopt($ch, CURLOPT_FTP_USE_EPSV, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    // curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
    curl_exec($ch);
    curl_close($ch);                              // closing curl handle
    fclose($fp);                                  // closing file handle
}
?>