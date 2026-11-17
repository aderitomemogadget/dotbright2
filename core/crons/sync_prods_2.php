<?php
	//Agora vamos criar e atualizar os novos produtos
	$importador=1;
	$path=dirname( __FILE__ ) . '/../../core/';
	require_once $path.'core_files.php';
	require_once $path.'../admin/products/funcoes.inc.php'; //Vamos carregar as funcoes do backoffice para criar ou alterar produtos
	require_once $path.'../admin/products/filtros/funcoes.inc.php'; //Vamos carregar as funcoes do backoffice para criar ou alterar produtos
	error_reporting(0);
	set_time_limit(0);
	header('Content-Type: text/html; charset=utf-8');
	ini_set("max_execution_time","6000000");
	ini_set("max_input_time","6000000");
	ini_set("memory_limit","-1");

	file_put_contents(dirname( __FILE__ ) . '/../../files/log_sync_prods_2.log', date('Y-m-d H:i:s'));

	$linguas_importa[]='pt';
	$Erro='';
	$Estado=1;

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$rs4=new DataObj;
	$rs5=new DataObj;

	$importar=0;
	$sql='SELECT
				REF,EAN,EAN2,Designacao,
				PVP1,PVP2,PVP3,PVP4,PVP5,PVP7,PVP8,STOCK_DATE,
				IVA,IVA_INFO,Stock,Stock_Arm,
				fam_id1,fam_id2,fam_id3,fam_id4,fam_id5,fam_desc1,fam_desc2,fam_desc3,fam_desc4,fam_desc5,fam_level,
				Marca,InformationField1,InformationField2,InformationField3,
				EQUIVALENTES,TaxableGroupID,ItemTax,ARMAZEM
			FROM
				products_sync
			WHERE
				(PVP1 > 0 OR PVP2 > 0 OR PVP3 > 0 OR PVP4 > 0) 
				OR (REF LIKE "%DIAGNOSTICO%" AND fam_id1=84)
			ORDER BY
				InformationField2 DESC';


	/*$sql='SELECT
                                REF,EAN,EAN2,Designacao,
                                PVP1,PVP2,PVP3,PVP4,PVP5,PVP7,PVP8,STOCK_DATE,
                                IVA,IVA_INFO,Stock,Stock_Arm,
                                fam_id1,fam_id2,fam_id3,fam_id4,fam_id5,fam_desc1,fam_desc2,fam_desc3,fam_desc4,fam_desc5,fam_level,
                                Marca,InformationField1,InformationField2,InformationField3,
                                EQUIVALENTES,TaxableGroupID,ItemTax
                        FROM
                                products_sync
                        WHERE
                                REF = "USAIPHONE15PROMAX512GBBLK" OR REF = "USAAPPLEWATCH10A3003JBLK"
                        ORDER BY
                                InformationField2 DESC';*/

	$rs->db_open($db_link,$DB_name,$sql);
	$total=$rs->db_rows;
	$resto=0;
	if(!$rs->EOF())
	{
		$res=Runquery($db_link,$DB_name,'UPDATE products_families SET FLAG=0 WHERE data_atualizacao != "0000-00-00 00:00:00" AND ID_Integracao > 0');
		$sql="UPDATE products SET Flag=0 WHERE ID_ORG='163'";
		$res=Runquery($db_link,$DB_name,$sql);

		$importar=1;
		$resto=$total;
		if($total < 5) die(); // SE FOR DETECTADO POUCOS PRODUTOS MORRE AQUI
		while(!$rs->EOF())
		{
			$REF=addslashes($rs->db_data['REF']);
			$EAN=$rs->db_data['EAN'];
			$EAN2=$rs->db_data['EAN2'];
			$STOCK_DATE=$rs->db_data['STOCK_DATE'];

			if($EAN2!='')$EAN=$EAN2;

			if(!ean_check($EAN))$EAN='';

			echo "\n";
			echo '['.$resto.': '.$REF.']';

			$IVA=$rs->db_data['IVA'];
			$IVA_INFO=$rs->db_data['IVA_INFO'];
			$prod_titulo=$rs->db_data['Designacao'];
			$TaxableGroupID=$rs->db_data['TaxableGroupID'];
			$ItemTax=$rs->db_data['ItemTax']; // JÁ CONTEM IVA
			$ARMAZEM=$rs->db_data['ARMAZEM']; // JÁ CONTEM IVA

			

			//Os precos vem sem iva, vou acrescentar:

			/*if($TaxableGroupID == 18) // TAXA COPIA PRIVADA
			{
				$PVP1=(($rs->db_data['PVP1'] * (1 + ($IVA / 100))) + $ItemTax); // PVP
				$PVP2=(($rs->db_data['PVP2'] * (1 + ($IVA / 100))) + $ItemTax); // REVENDA
				$PVP3=(($rs->db_data['PVP3'] * (1 + ($IVA / 100))) + $ItemTax); // PARCEIROS
				$PVP4=(($rs->db_data['PVP4'] * (1 + ($IVA / 100))) + $ItemTax); // PARCEIROS
				$PVP5=(($rs->db_data['PVP5'] * (1 + ($IVA / 100))) + $ItemTax); // PARCEIROS
				$PVP7=(($rs->db_data['PVP7'] * (1 + ($IVA / 100))) + $ItemTax); // PRECODOTBRIGHT
				$PVP8=(($rs->db_data['PVP8'] * (1 + ($IVA / 100))) + $ItemTax); // PRECOSITE
			}
			else
			{*/
				$PVP1=$rs->db_data['PVP1']; // PVP
				$PVP2=$rs->db_data['PVP2']; // REVENDA
				$PVP3=$rs->db_data['PVP3']; // PARCEIROS
				$PVP4=$rs->db_data['PVP4']; // PARCEIROS
				$PVP5=$rs->db_data['PVP5']; // PARCEIROS
				$PVP7=$rs->db_data['PVP7']; // PRECODOTBRIGHT
				$PVP8=$rs->db_data['PVP8']; // PRECOSITE
			//}
			
			$GRAU='';
			$OPERADORA='';
			$marca_id_alt1=0;
			$marca_id_alt2=0;
			$marca_id_alt3=0;
			$marca_id_alt4=0;
			$marca_id_alt5=0;
			$marca_id_alt6=0;
			$marca_id_alt7=0;
			$marca_id_alt8=0;
			$marca_id_alt9=0;
			$marca_id_alt10=0;
			$Marca_id=0;
			$active=1;

			$Stock_novo=$rs->db_data['Stock_Arm'];
			$fam_id1=$rs->db_data['fam_id1'];
			$fam_id2=$rs->db_data['fam_id2'];
			$fam_id3=$rs->db_data['fam_id3'];
			$fam_id4=$rs->db_data['fam_id4'];
			$fam_id5=$rs->db_data['fam_id5'];
			$fam_desc1=$rs->db_data['fam_desc1'];
			$fam_desc2=$rs->db_data['fam_desc2'];
			$fam_desc3=$rs->db_data['fam_desc3'];
			$fam_desc4=$rs->db_data['fam_desc4'];
			$fam_desc5=$rs->db_data['fam_desc5'];
			$fam_level=$rs->db_data['fam_level'];
			$EQUIVALENTES=$rs->db_data['EQUIVALENTES'];
			$GRAU=$rs->db_data['GRAU'];
			$OPERADORA=$rs->db_data['OPERADORA'];
			$Brand=$rs->db_data['Marca'];

			
			//--------------------------------- TROCA PREÇO
			if($PVP8 > 0 && $Stock_novo >0 )$PVP1=$PVP8;
			//---------------------------------
			if($PVP1 > 0 || (substr_count($REF,'DIAGNOSTICO') && $fam_id1 == 84)) $active=1; else $active=0;


			//-------------------------------------------
			$PVP1=$PVP1 * (1 + ($IVA / 100)); // PVP
			$PVP2=$PVP2 * (1 + ($IVA / 100)); // REVENDA
			$PVP3=$PVP3 * (1 + ($IVA / 100)); // PARCEIROS
			$PVP4=$PVP4 * (1 + ($IVA / 100)); // PARCEIROS
			$PVP5=$PVP5 * (1 + ($IVA / 100)); // PARCEIROS
			$PVP7=$PVP7 * (1 + ($IVA / 100)); // PRECODOTBRIGHT
			$PVP8=$PVP8 * (1 + ($IVA / 100)); // PRECOSITE

			if($TaxableGroupID == 18) // TAXA COPIA PRIVADA
			{
				$PVP1=($PVP1 + $ItemTax); // PVP
				$PVP2=($PVP2 + $ItemTax); // REVENDA
				$PVP3=($PVP3 + $ItemTax); // PARCEIROS
				$PVP4=($PVP4 + $ItemTax); // PARCEIROS
				$PVP5=($PVP5 + $ItemTax); // PARCEIROS
				$PVP7=($PVP7 + $ItemTax); // PRECODOTBRIGHT
				$PVP8=($PVP8 + $ItemTax); // PRECOSITE

			}














			// SE O ID DE INTEGRAÇÃO FOR 3 - ACESSORIOS ou 84 - REPARACOES ESTÃO SEMPRE ATIVOS NO WEBSITE
			if($fam_id1 == 3 || $fam_id1 == 84){$active=1;}


			$preco1=$PVP1;
			$stock=$Stock_novo;
			$qtd_minima_enc=1;
			$fam_id=0;
			$subfam_id=0;
			$subfam_id2=0;
			$subfam_id3=0;
			$subfam_id4=0;
			$familia_id=0;

			$fam_id=detect_family($fam_id1,$fam_desc1,0,$fam_id1);
			if($fam_id > 0 && $fam_id2 > 0)
			{
				$subfam_id=detect_family($fam_id2,$fam_desc2,$fam_id,$fam_id1.'_'.$fam_id2);
				if($subfam_id > 0 && $fam_id3 > 0)
				{
					$subfam_id2=detect_family($fam_id3,$fam_desc3,$subfam_id,$fam_id1.'_'.$fam_id2.'_'.$fam_id3);
					if($subfam_id2 > 0 && $fam_id4 > 0)
					{
						$subfam_id3=detect_family($fam_id4,$fam_desc4,$subfam_id2,$fam_id1.'_'.$fam_id2.'_'.$fam_id3.'_'.$fam_id4);
						if($subfam_id3 > 0 && $fam_id5 > 0)
						{
							$subfam_id4=detect_family($fam_id5,$fam_desc5,$subfam_id3,$fam_id1.'_'.$fam_id2.'_'.$fam_id3.'_'.$fam_id4.'_'.$fam_id5);
						}
					}
				}
			}

			if($subfam_id4 > 0) $familia_id=$subfam_id4;
			elseif($subfam_id3 > 0) $familia_id=$subfam_id3;
			elseif($subfam_id2 > 0) $familia_id=$subfam_id2; 
			elseif($subfam_id > 0) $familia_id=$subfam_id;
			else $familia_id=$fam_id;

			// MARCAS
			$marca_id=1; // SEM Marca
			if($Brand != '')
			{
				$marca_id=\detect_brand($Brand);
			}

			echo "fam_id1 = $fam_id1";


			$atualizar=1;
			if($fam_id1=='7')
			{
				$familia_id=163;


				//Semi novos, ou tem stock ou nao existe mais
				if($Stock_novo<=0)$atualizar=0;

				echo "Vou ver se tem outros IMEIS para este mesmo usado\n";
				//Aproved

				$sql='SELECT * FROM products_sync_graus WHERE REF="'.$REF.'" AND STOCK>0 AND IMEI <> "" GROUP BY GRAU,PVP1,IMEI';

				echo "$sql \n";


				//Se tiver mais do que um produto usado, vou ter de os criar.
				$rs3->db_open($db_link,$DB_name,$sql);
				if($rs3->db_rows > 1)
				{

					//echo $sql."\n";
					$sql='UPDATE products SET Variante=1 WHERE REF like "'.$REF.'_%"';
					$res=Runquery($db_link,$DB_name,$sql);

					while(!$rs3->EOF())
					{
						$bateria=$rs3->db_data['BATTERY'];
						$BOX1=$rs3->db_data['BOX1'];
						$BOX2=$rs3->db_data['BOX2'];
						$CABLE=$rs3->db_data['CABLE'];
						$OPERADORA=$rs3->db_data['OPERADORA'];
						$CHARGER=$rs3->db_data['CHARGER'];
						$HEADSET=$rs3->db_data['HEADSET'];
						$COVER=$rs3->db_data['COVER'];
						$STOCK_DATE2=$rs3->db_data['STOCK_DATE'];
						$GRAU=$rs3->db_data['GRAU'];
						$ARMAZEM=$rs3->db_data['ARMAZEM'];
						$IMEI=$rs3->db_data['IMEI'];
						$loja = "";
						if($ARMAZEM==1) $loja = ",localizacao_fisica='PORTO'";
						if($ARMAZEM==2) $loja = ",localizacao_fisica='MATOSINHOS'";

						if(trim($IMEI)!='')
						{
							$REF_NOVA=$REF.'_'.$IMEI;
							//Agora vou desativar o Artigo PAI e criar os novos.
							$sql="UPDATE products SET Flag=1,Aproved=0 WHERE REF='$REF'";
							$res=Runquery($db_link,$DB_name,$sql);
							//Agora vou criar as novas variantes deste artigo

							//echo "Agora vou desativar o Artigo PAI e criar os novos.\n";
							//echo $sql."\n";
							//echo "REF_NOVA = ".$REF_NOVA."\n";
							//echo "Agora vou criar o novo produto: detect_product($active,$familia_id,$marca_id,$REF_NOVA,$EAN,0,$prod_titulo,1) \n";

							$prod_info=\detect_product($active,$familia_id,$marca_id,$REF_NOVA,$EAN,0,$prod_titulo,1,$ARMAZEM);
							if(isset($prod_info['erro']))
							{
								$Erro .= '|ERRO: '.$REF;
								$Estado=0;
							}
							elseif($prod_info['prod_id'] > 0)
							{
								$sql='SELECT ID FROM products WHERE REF="'.$REF.'"';
								
								//echo "Agora vou obter o ID do Artigo PAI\n";
								//echo $sql."\n";

								$rs4->db_open($db_link,$DB_name,$sql);
								if(!$rs4->EOF())
								{
									$prod_id=$rs4->db_data['ID'];
								}
								$rs4->db_close();



								$sql="DELETE FROM products WHERE ID <> '".$prod_info['prod_id']."' AND REF='$REF_NOVA'";
								$res=Runquery($db_link,$DB_name,$sql);

								//echo "Agora vou buscar os detalhes do artigo para atualizar no novo\n";
								$sql='SELECT Texto1,Texto2,Texto3,Texto4 FROM products_lang WHERE ID_PAI="'.$prod_id.'"';
								//echo $sql."\n";
								$rs4->db_open($db_link,$DB_name,$sql);
								if(!$rs4->EOF())
								{
									$Texto1=$rs4->db_data['Texto1'];
									$Texto2=$rs4->db_data['Texto2'];
									$Texto3=$rs4->db_data['Texto3'];
									$Texto4=$rs4->db_data['Texto4'];
									$sql="UPDATE products_lang SET Texto1='$Texto1',Texto2='$Texto2',Texto3='$Texto3',Texto4='$Texto4' WHERE ID_PAI='".$prod_info['prod_id']."'";
									$res=Runquery($db_link,$DB_name,$sql);
								}
								$rs4->db_close();


								$sql='UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_info['prod_id'].'"';
								$res=Runquery($db_link,$DB_name,$sql);
								$sql="UPDATE products SET Flag=1,Variante=1,bateria='$bateria',BOX1='$BOX1',BOX2='$BOX2',CABLE='$CABLE',CHARGER='$CHARGER',HEADSET='$HEADSET',COVER='$COVER',estado='$GRAU'$loja WHERE ID='".$prod_info['prod_id']."'";
								$res=Runquery($db_link,$DB_name,$sql);
								$category_id_import_1=4;
								$value_id_import_1=0;
								if($GRAU!='')$value_id_import_1=detect_filter_values(0,$category_id_import_1,(trim($OPERADORA) != '' ? $GRAU.'-'.$OPERADORA : $GRAU),'pt',(trim($OPERADORA) != '' ? $GRAU.'-'.$OPERADORA : $GRAU));
								if($value_id_import_1 > 0 && $rs3->db_data['GRAU'] != '')
								{
									$IMEI=''; 
									$OPERADORA='';
									$PVP1_G=0;
									$STOCK_G=0;
									$c_obj_id=0;
									detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,1);
									$PVP1_G=$rs3->db_data['PVP1'];
									$STOCK_G=$rs3->db_data['STOCK'];
									$stock_graus += $STOCK_G;
									$IMEI=$rs3->db_data['IMEI'];
									$OPERADORA=$rs3->db_data['OPERADORA'];
									if($PVP1_G > 0) $PVP1=$PVP2=$PVP3=$PVP1_G;
									$sql='SELECT obj_id FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$prod_info['prod_id'].'" AND obj_combo="'.$category_id_import_1.'-'.$value_id_import_1.'"';
									$rs4->db_open($db_link,$DB_name,$sql);
									if(!$rs4->EOF())$c_obj_id=$rs4->db_data['obj_id'];
									$rs4->db_close();
									products_objs_filtros_save_sync($prod_info['prod_id'],$c_obj_id,0,$PVP1_G,$STOCK_G,0,$category_id_import_1.'-'.$value_id_import_1,$IMEI,$OPERADORA);


									if($cria_vazio == 0) detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,0,0,0);
									$sql='DELETE FROM filters_products WHERE FLAG=0 and prod_id="'.$prod_info['prod_id'].'"';
									$res=Runquery($db_link,$DB_name,$sql);
									if($stock_graus > 0) $stock=$stock_graus;
									// PVP
									detect_products_objs($prod_info['prod_id'],$PVP1,$qtd_minima_enc,$stock,$STOCK_DATE,$REF_NOVA,0,$IVA,$IVA_INFO,$fam_id1,$GRAU);
									// REVENDA
									detect_products_objs($prod_info['prod_id'],$PVP2,$qtd_minima_enc,$stock,$STOCK_DATE,$REF_NOVA,2,$IVA,$IVA_INFO,$fam_id1,$GRAU);
									// PARCEIROS
									detect_products_objs($prod_info['prod_id'],$PVP3,$qtd_minima_enc,$stock,$STOCK_DATE,$REF_NOVA,3,$IVA,$IVA_INFO,$fam_id1,$GRAU);
									// MERCADO
									detect_products_objs($prod_info['prod_id'],$PVP4,$qtd_minima_enc,$stock,$STOCK_DATE,$REF_NOVA,4,$IVA,$IVA_INFO,$fam_id1,$GRAU);
									// ON4TECH
									detect_products_objs($prod_info['prod_id'],$PVP5,$qtd_minima_enc,$stock,$STOCK_DATE,$REF_NOVA,5,$IVA,$IVA_INFO,$fam_id1,$GRAU);
									// DOTBRIGHT
									detect_products_objs($prod_info['prod_id'],$PVP7,$qtd_minima_enc,$stock,$STOCK_DATE,$REF_NOVA,6,$IVA,$IVA_INFO,$fam_id1,$GRAU);
								}

								//Agora vou copiar as imagens do produto PAI e as descrições
								$sql='SELECT * FROM products_images WHERE ID_ORG="'.$prod_id.'" ORDER BY Ordem,ID';
								$rs4->db_open($db_link,$DB_name,$sql);
								while(!$rs4->EOF())
								{
									$img_ext=$rs4->db_data['img_ext'];
									$Ordem=$rs4->db_data['Ordem'];								
									$URL_Source=addslashes((string)($rs4->db_data['URL_Source'] ?? ''));
									$imagem=dirname( __FILE__ ).'/../../files/products/'.$prod_id.'_'.$Ordem.'.'.$img_ext;
									if(file_exists($imagem))
									{
										$imagem_nova=dirname( __FILE__ ).'/../../files/products/'.$prod_info['prod_id'].'_'.$Ordem.'.'.$img_ext;
										if(!file_exists($imagem_nova))copy($imagem,$imagem_nova);

										$sql='SELECT * FROM products_images WHERE ID_ORG="'.$prod_info['prod_id'].'" AND Ordem="'.$Ordem.'"';
										$rs5->db_open($db_link,$DB_name,$sql);
										if($rs5->EOF())
										{
											//Ainda não existe
											$sql='INSERT INTO products_images (ID_ORG,Ordem,img_ext,URL_Source) values("'.$prod_info['prod_id'].'","'.$Ordem.'","'.$img_ext.'","'.$URL_Source.'")';
											$res=Runquery($db_link,$DB_name,$sql);
										}
										$rs5->db_close();
									}
									$rs4->db_next();
								}
								$rs4->db_close();
							}
						}
						$rs3->db_next();
					}
					//Vou colocar esta flag=0 para não inserir 2 vezes o mesmo artigo
					echo "Vou colocar esta flag=0 para não inserir 2 vezes o mesmo artigo";
					$atualizar=0;
				

					//die("*");
				}
				$rs3->db_close();

			}

			echo "atualizar=$atualizar | ";

			//---------------------------------- 
			if($atualizar == 1)
			{
				$prod_info=\detect_product($active,$familia_id,$marca_id,$REF,$EAN,$prod_id,$prod_titulo,0,$ARMAZEM);
				if(isset($prod_info['erro']))
				{
					echo "não encontrei o produto $REF ";

					$Erro .= '|ERRO: '.$REF;
					$Estado=0;
				}
				elseif($prod_info['prod_id'] > 0)
				{
					echo "o produto ja existe com o ID = ".$prod_info['prod_id'].", vou atualizar ";

					\detect_famprod($prod_info['prod_id'],$familia_id);

					// INSERE EQUIVALENTES PRODUTOS
					\detect_products_equivalentes($prod_info['prod_id'],$EQUIVALENTES);

					$sql='UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_info['prod_id'].'"';
					$res=Runquery($db_link,$DB_name,$sql);
					$cria_vazio=0;

					if($Brand!='') $InformationField1=$rs->db_data['InformationField1'].'|'.$Brand.'|';
					else $InformationField1=$rs->db_data['InformationField1'];
					if($InformationField1 != '')
					{
						$InformationField1_arr=explode('||',ltrim(rtrim($InformationField1,'|'),'|'));

						for($a=0; $a<count($InformationField1_arr); $a++)
						{
							// MARCA EXTRA
							$category_id_import_1=3; $value_id_import_1=0;
							if($InformationField1_arr[$a] != '') $value_id_import_1=detect_filter_values(0,$category_id_import_1,$InformationField1_arr[$a],'pt',$InformationField1_arr[$a]);
							if($value_id_import_1 > 0 && $InformationField1_arr[$a] != '') detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,0);
							$cria_vazio=1;
						}
					}
					$InformationField2=$rs->db_data['InformationField2'];
					if($InformationField2 != '')
					{
						$InformationField2_arr=explode('||',ltrim(rtrim($InformationField2,'|'),'|'));

						for($a=0; $a<count($InformationField2_arr); $a++)
						{
							$category_id_import_1=3; $value_id_import_1=0;
							if($InformationField2_arr[$a] != '') $value_id_import_1=detect_filter_values(0,$category_id_import_1,$InformationField2_arr[$a],'pt',$InformationField2_arr[$a]);
							if($value_id_import_1 > 0 && $InformationField2_arr[$a] != '') detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,0);
							$cria_vazio=1;
						}
					}
					$InformationField3=$rs->db_data['InformationField3'];
					if($InformationField3 != '')
					{
						$InformationField3_arr=explode('||',ltrim(rtrim($InformationField3,'|'),'|'));

						for($a=0; $a<count($InformationField3_arr); $a++)
						{
							$category_id_import_1=3; $value_id_import_1=0;
							if($InformationField3_arr[$a] != '') $value_id_import_1=detect_filter_values(0,$category_id_import_1,$InformationField3_arr[$a],'pt',$InformationField3_arr[$a]);
							if($value_id_import_1 > 0 && $InformationField3_arr[$a]!='') detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,0);
							$cria_vazio=1;
						}
					}
					$InformationField4=$rs->db_data['InformationField4'];
					if($InformationField4 != '')
					{
						$InformationField4_arr=explode('||',ltrim(rtrim($InformationField4,'|'),'|'));

						for($a=0; $a<count($InformationField4_arr); $a++)
						{
							$category_id_import_1=3; $value_id_import_1=0;
							if($InformationField4_arr[$a] != '') $value_id_import_1=detect_filter_values(0,$category_id_import_1,$InformationField4_arr[$a],'pt',$InformationField4_arr[$a]);
							if($value_id_import_1 > 0 && $InformationField4_arr[$a]!='') detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,0);
							$cria_vazio=1;
						}
					}
					$InformationField5=$rs->db_data['InformationField5'];
					if($InformationField5 != '')
					{
						$InformationField5_arr=explode('||',ltrim(rtrim($InformationField5,'|'),'|'));

						for($a=0; $a<count($InformationField5_arr); $a++)
						{
							$category_id_import_1=3; $value_id_import_1=0;
							if($InformationField5_arr[$a] != '') $value_id_import_1=detect_filter_values(0,$category_id_import_1,$InformationField5_arr[$a],'pt',$InformationField5_arr[$a]);
							if($value_id_import_1 > 0 && $InformationField5_arr[$a]!='') detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,0);
							$cria_vazio=1;
						}
					}
					$arr_grau=array();
					$sql="UPDATE products_objs_combo SET obj_val=0 WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."'";
					$res=Runquery($db_link,$DB_name,$sql);

					$stock_graus=0;				
					$sql='SELECT PVP1,IMEI,GRAU,PRECO_GRAU,OPERADORA,BATTERY,BOX1,BOX2,CABLE,CHARGER,HEADSET,COVER,STOCK_DATE,ARMAZEM,COUNT(REF) AS STOCK FROM products_sync_graus WHERE REF="'.$REF.'" GROUP BY GRAU,PVP1';
					$rs3->db_open($db_link,$DB_name,$sql);
					if(!$rs3->EOF())
					{
						while(!$rs3->EOF())
						{
							$bateria=$rs3->db_data['BATTERY'];
							$BOX1=$rs3->db_data['BOX1'];
							$BOX2=$rs3->db_data['BOX2'];
							$CABLE=$rs3->db_data['CABLE'];
							$CHARGER=$rs3->db_data['CHARGER'];
							$HEADSET=$rs3->db_data['HEADSET'];
							$COVER=$rs3->db_data['COVER'];
							$STOCK_DATE2=$rs3->db_data['STOCK_DATE'];
							$GRAU=$rs3->db_data['GRAU'];
							$ARMAZEM=$rs3->db_data['ARMAZEM'];

							$loja = "";
							if($ARMAZEM==1) $loja = ",localizacao_fisica='PORTO'";
							if($ARMAZEM==2) $loja = ",localizacao_fisica='MATOSINHOS'";

							$sql="UPDATE products SET bateria='$bateria',BOX1='$BOX1',BOX2='$BOX2',CABLE='$CABLE',CHARGER='$CHARGER',HEADSET='$HEADSET',COVER='$COVER',estado='$GRAU'$loja WHERE ID='".$prod_info['prod_id']."'";
							//echo $sql."\n";
							$res=Runquery($db_link,$DB_name,$sql);

							$category_id_import_1=4;
							$value_id_import_1=0;

							if($rs3->db_data['GRAU']!='') $value_id_import_1=detect_filter_values(0,$category_id_import_1,(trim($rs3->db_data['OPERADORA']) != '' ? $rs3->db_data['GRAU'].'-'.$rs3->db_data['OPERADORA'] : $rs3->db_data['GRAU']),'pt',(trim($rs3->db_data['OPERADORA']) != '' ? $rs3->db_data['GRAU'].'-'.$rs3->db_data['OPERADORA'] : $rs3->db_data['GRAU']));
							
							if($value_id_import_1 > 0 && $rs3->db_data['GRAU'] != '')
							{
								$IMEI=''; 
								$OPERADORA='';
								$PVP1_G=0;
								$STOCK_G=0;
								$c_obj_id=0;

								detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,$category_id_import_1,$value_id_import_1,1);
								$PVP1_G=$rs3->db_data['PVP1'];
								$STOCK_G=$rs3->db_data['STOCK'];
								$stock_graus += $STOCK_G;
								$IMEI=$rs3->db_data['IMEI'];
								$OPERADORA=$rs3->db_data['OPERADORA'];
								if($PVP1_G > 0) $PVP1=$PVP2=$PVP3=$PVP1_G;

								$sql='SELECT obj_id FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$prod_info['prod_id'].'" AND obj_combo="'.$category_id_import_1.'-'.$value_id_import_1.'"';
								$rs4->db_open($db_link,$DB_name,$sql);
								if(!$rs4->EOF())
								{
									$c_obj_id=$rs4->db_data['obj_id'];
								}
								$rs4->db_close();

								products_objs_filtros_save_sync($prod_info['prod_id'],$c_obj_id,0,$PVP1_G,$STOCK_G,0,$category_id_import_1.'-'.$value_id_import_1,$IMEI,$OPERADORA);
							}
							$cria_vazio=1;

							$rs3->db_next();
						}
					}
					$rs3->db_close();

					$sql="DELETE FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$prod_info['prod_id']."' AND obj_val=0";
					$res=Runquery($db_link,$DB_name,$sql);

					if($cria_vazio == 0) detect_filter_products($prod_info['prod_id'],$familia_id,$marca_id,0,0,0);

					$sql='DELETE FROM filters_products WHERE FLAG=0 and prod_id="'.$prod_info['prod_id'].'"';
					$res=Runquery($db_link,$DB_name,$sql);

					if($stock_graus > 0) $stock=$stock_graus;
					// PVP
					detect_products_objs($prod_info['prod_id'],$PVP1,$qtd_minima_enc,$stock,$STOCK_DATE,$REF,0,$IVA,$IVA_INFO,$fam_id1,$GRAU);
					// REVENDA
					detect_products_objs($prod_info['prod_id'],$PVP2,$qtd_minima_enc,$stock,$STOCK_DATE,$REF,2,$IVA,$IVA_INFO,$fam_id1,$GRAU);
					// PARCEIROS
					detect_products_objs($prod_info['prod_id'],$PVP3,$qtd_minima_enc,$stock,$STOCK_DATE,$REF,3,$IVA,$IVA_INFO,$fam_id1,$GRAU);
					// MERCADO
					detect_products_objs($prod_info['prod_id'],$PVP4,$qtd_minima_enc,$stock,$STOCK_DATE,$REF,4,$IVA,$IVA_INFO,$fam_id1,$GRAU);
					// ON4TECH
					detect_products_objs($prod_info['prod_id'],$PVP5,$qtd_minima_enc,$stock,$STOCK_DATE,$REF,5,$IVA,$IVA_INFO,$fam_id1,$GRAU);
					// DOTBRIGHT
					detect_products_objs($prod_info['prod_id'],$PVP7,$qtd_minima_enc,$stock,$STOCK_DATE,$REF,6,$IVA,$IVA_INFO,$fam_id1,$GRAU);
				}
				$rs3->db_close();
			}
			$resto--;
			$rs->db_next();
			$total--;
		}
	}
	$rs->db_close();

	//die();


	// ESCONDE REGISTOS QUE NÃO FORAM ATUALIZADOS
 	$res=Runquery($db_link,$DB_name,'UPDATE products_families SET visivel=0 WHERE FLAG=0 AND ID_Integracao > 0');

 	$sql="UPDATE products SET Aproved=0 WHERE Flag=0 AND ID_ORG='163'";
	$res=Runquery($db_link,$DB_name,$sql);

	$sql="DELETE FROM products WHERE Flag=0 AND ID_ORG='163' AND Variante='1'";
	$res=Runquery($db_link,$DB_name,$sql);

	if (getenv('DOTBRIGHT_SAGE_MANAGED') !== '1') {
		exec('/usr/bin/php '.escapeshellarg($path.'crons/acelerador_pesquisa.php').' > /dev/null 2>&1 &');
	}

	function combinations_sync($arrays,$i=0)
	{
		if (!isset($arrays[$i])) return array();
		if ($i == count($arrays) - 1) return $arrays[$i];

		// GET COMBINATIONS FROM SUBSEQUENT ARRAYS
		$tmp=combinations_sync($arrays,$i + 1);

		$result=array();

		// CONCAT EACH ARRAY FROM TMP WITH EACH ELEMENT FROM $arrays[$i]
		foreach ($arrays[$i] as $v)
		{
			foreach ($tmp as $t)
			{
				$result[]=is_array($t) ? array_merge(array($v),$t) : array($v,$t);
			}
		}
		return $result;
	}
	function filtros_ficha_produto_adm_sync($prod_id=0,$fam_id=0,$arr_grau=array()) //
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj2=new DataObj; $filtros=array();$return='';
		$sql2='
				SELECT
					fp.cat_id,fp.valor_id,fp.filtro_area
				FROM
					filters_products fp
				WHERE
					(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.')
					AND prod_id='.$prod_id.'
					AND fp.filtros_rel2 <> ""
					AND fp.filtro_active=1
					AND (fp.filtro_area=1 OR fp.filtro_area=2)
				GROUP BY
					fp.valor_id,fp.cat_id
				Order By
					fp.filtro_id asc
				';
		$obj2->db_open($db_link,$DB_name,$sql2);
		$a=0;
		while(!$obj2->EOF())
		{
			$filtros[$obj2->db_data['cat_id']][]=$obj2->db_data['valor_id'];

			$obj2->db_next();
		}
		$obj2->db_close();
		$arr=array();
		foreach($filtros as $key => $value)
		{
			for($a=0;$a<count($value); $a++)
			{
				if(!in_array($key,$arr))
				{
					$cat=buscacategoriaTipoeNomex_adm_sync($key);
					$nm_value=buscavalorfiltrosx_adm_sync($value[$a]);
					$arr[$key][]=$key.'spl'.$cat['category_name'].';'.$value[$a].'spl'.$nm_value;
				}
			}
		}

		$arrays=array_values(array_filter($arr));
		$combinations=combinations_sync($arrays);


		$obj_combo='';

		if(count($combinations) > 0)
		{
			for($a=0; $a<count($combinations); $a++)
			{
				$obj_combo='';

				if(is_array($combinations[$a]))
				{
					for($b=0; $b<count($combinations[$a]); $b++)
					{
						$n=explode(';',$combinations[$a][$b]);
						$bl_cat=explode('spl',$n[0]);
						$bl_val=explode('spl',$n[1]);
						$obj_combo .= $bl_cat[0].'-'.$bl_val[0].';';
					}
				}
				else
				{
					$n=explode(';',$combinations[$a]);
					$bl_cat=explode('spl',$n[0]);
					$bl_val=explode('spl',$n[1]);

					$obj_combo .= $bl_cat[0].'-'.$bl_val[0].';';
				}

				products_objs_filtros_sync($prod_id,rtrim($obj_combo,';'),$a,$arr_grau[$bl_cat[0].'-'.$bl_val[0]]);
			}
		}
	}
	function products_objs_filtros_sync($obj_id=0,$obj_combo='',$a=0,$arr_grau=array())
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$site_http_url;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$rs=new DataObj;
		$rs2=new DataObj;
		$sql='SELECT obj_id FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$obj_id.'" AND obj_combo="'.$obj_combo.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id_p=$rs->db_data['obj_id'];
			// $obj_ref=$rs->db_data['obj_ref'];
			// $obj_iva=number_format(((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']),4,'.','');
			// $obj_ivax=((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']);
			// $obj_preco=$rs->db_data['obj_pvp'];
			// $obj_precosiva=number_format(($rs->db_data['obj_pvp'] / (1 + ($obj_ivax / 100))),4,'.','');
			// $obj_stock=$rs->db_data['obj_stock'];
			// $obj_promo=$rs->db_data['obj_promo'];
			// $obj_promosiva=number_format(($rs->db_data['obj_promo'] / (1 + ($obj_ivax / 100))),4,'.','');
		}
		else
		{
			// $obj_iva=number_format(($GLOBALS['iva_default']),4,'.','');
		}
		$rs->db_close();

		/*
		// a
		$return .= $a.'<br>';

		// c_obj_id
		$return .= $obj_id_p.'<br>';

		// c_obj_iva
		$return .= $obj_iva.'<br>';

		// c_obj_preco
		$return .= $obj_preco.'<br>';

		// c_obj_stock
		$return .= $obj_stock.'<br>';

		// c_obj_promo
		$return .= $obj_promo.'<br>';

		// c_obj_combo
		$return .= $obj_combo.'<br>';


		// c_obj_ref
		$return .= $obj_ref.'<br>';
		$return .= '<br>----<br>'; */

		products_objs_filtros_save_sync($obj_id,$obj_id_p,$obj_iva,$arr_grau['PVP1'],$arr_grau['STOCK'],$obj_promo,$obj_combo, $arr_grau['IMEI'], $arr_grau['OPERADORA']);




		return $return;
	}
	function buscacategoriaTipoeNomex_adm_sync($ID)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;
		$sql="SELECT filters_categories.category_type,filters_categories_lang.category_name FROM filters_categories INNER JOIN filters_categories_lang ON filters_categories_lang.category_id=filters_categories.category_id  WHERE filters_categories.category_id='$ID'";
		// echo $sql;
		$dados->db_open($db_link,$DB_name,$sql);
		$dados->db_close();

		return array('category_type' => $dados->db_data["category_type"] ,'category_name' => $dados->db_data["category_name"]);
	}
	function buscavalorfiltrosx_adm_sync($ID)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;
		$sql="SELECT values_name from filters_values_lang where values_id='$ID' and values_lang='".$GLOBALS['site_lang']."'";
		$dados->db_open($db_link,$DB_name,$sql);
		return $dados->db_data["values_name"];
		$dados->db_close();
	}
	function products_objs_filtros_save_sync($ID_PROD=0,$c_obj_id='',$c_obj_iva='',$c_obj_preco='',$c_obj_stock='',$c_obj_promo='',$c_obj_combo='',$c_obj_ref='',$operadora='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$objv_id=$c_obj_id;
		$obj_iva=$c_obj_iva;
		$obj_preco=$c_obj_preco;
		$obj_stock=$c_obj_stock;
		$obj_promo=$c_obj_promo;
		$obj_combo=$c_obj_combo;
		$obj_ref=$c_obj_ref;


		$rs=new DataObj;
		$rs2=new DataObj;

		$sql="SELECT obj_id FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' AND obj_id='".$objv_id."' AND obj_combo='".$obj_combo."'";
		// echo $sql.'<br>';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id=$rs->db_data['obj_id'];
			$sql="
					UPDATE products_objs_combo
					SET
						obj_iva='".$obj_iva."',
						obj_pvp='".$obj_preco."',
						obj_stock='".$obj_stock."',
						obj_combo='".$obj_combo."',
						obj_ref='".$obj_ref."',
						obj_val=1,
						operadora='".$operadora."'
					WHERE
						obj_id='".$obj_id."'
						AND obj_type='products'
						AND obj_type_id='".$ID_PROD."'
					";
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql="
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
							obj_val,
							operadora
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
							1,
							'".$operadora."'
						)
					";
			$res=Runquery($db_link,$DB_name,$sql);
			$obj_id=db_get_last_insert_id($db_link);

		}

		$rs->db_close();



	}
	function detect_family($FAM_CODE,$FAM_NOME,$FAM_PAI=0,$CODE='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;

		//echo "$FAM_CODE,$FAM_NOME,$FAM_PAI,$CODE \n";
		$udate=date('Y-m-d H:i:s');
		$ItemCode=trim($FAM_CODE);
		$visivel=1;
		$fam_id=0;
		if($ItemCode != '')
		{
			if(substr_count($FAM_NOME,'NÃO VISIVEL'))$visivel=0;
			if(substr_count($FAM_NOME,'NAO VISIVEL'))$visivel=0;
			if(substr_count($FAM_NOME,'não visivel'))$visivel=0;
			if(substr_count($FAM_NOME,'nao visivel'))$visivel=0;
			if(substr_count($FAM_NOME,'Lixo'))$visivel=0;
			if(substr_count($FAM_NOME,'lixo'))$visivel=0;
			if(substr_count($FAM_NOME,'LIXEIRA'))$visivel=0;

			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			$sql='SELECT ID FROM products_families WHERE ID_Integracao="'.$ItemCode.'" AND Ref_Integracao="'.$CODE.'"';
			//echo $sql."\n";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$fam_id=$rs->db_data['ID'];

				if($visivel==0)
				{
					$sql='UPDATE products SET Aproved=0 WHERE ID_ORG="'.$fam_id.'" AND Flag=0 AND Sync=0 AND codigo_importacao=""';
					$res=Runquery($db_link,$DB_name,$sql);
				}
				

				if($FAM_PAI > 0) $sql='UPDATE products_families SET ID_ORG="'.$FAM_PAI.'",FLAG=1,visivel="'.$visivel.'",data_atualizacao="'.$udate.'",Ref_Integracao2="'.$CODE.'" WHERE ID_Integracao="'.$ItemCode.'" AND Ref_Integracao="'.$CODE.'"';
				else $sql='UPDATE products_families SET FLAG=1,visivel="'.$visivel.'",data_atualizacao="'.$udate.'",Ref_Integracao2="'.$CODE.'" WHERE ID_Integracao="'.$ItemCode.'" AND Ref_Integracao="'.$CODE.'"';

				//echo $sql."\n";

				$res=Runquery($db_link,$DB_name,$sql);
			}
			else
			{
				$sql='INSERT INTO products_families (ID_ORG,visivel,ID_integracao,data_atualizacao,Ref_Integracao,Ref_Integracao2,FLAG) VALUES ( "'.$FAM_PAI.'","'.$visivel.'","'.$ItemCode.'","'.$udate.'","'.$CODE.'","'.$CODE.'",1)';

				//echo $sql."\n";
				$res=Runquery($db_link,$DB_name,$sql);
				$fam_id=db_get_last_insert_id($db_link);
			}
			$rs->db_close(1);
			
			if($fam_id > 0)
			{
				detect_family_lang($fam_id,$FAM_NOME,'pt');
			}
		}
		return $fam_id;
	}
	function detect_family_lang($fam_id,$Cabecalho,$lingua)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2=new DataObj;

		$sql='SELECT ID FROM products_families_lang WHERE ID_PAI="'.$fam_id.'" AND Lang="'.$lingua.'"';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$sql='UPDATE products_families_lang SET Cabecalho="'.addslashes($Cabecalho).'" WHERE ID_PAI="'.$fam_id .'" and Lang="'.$lingua.'"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'","'.addslashes($Cabecalho).'","'.$lingua.'")';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$rs2->db_close();
	}
	// FUNÇÃO PARA INSERIR O PRODUTO
	function detect_product($active,$FAM_ID,$MARCA_ID,$ItemCode,$EAN,$ItemCodeID,$Cabecalho,$Variante=0,$ARMAZEM=0) 
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$udate=date('Y-m-d H:i:s');
		$prod_id=0;
		if($ItemCode != '')
		{
			$localizacao_fisica = '';
			if($ARMAZEM==1) $localizacao_fisica = 'PORTO';
			if($ARMAZEM==2) $localizacao_fisica = 'MATOSINHOS';


			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			$sql='SELECT ID,ID_ORG FROM products WHERE Ref="'.$ItemCode.'"';
			//echo $sql."\n";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$prod_id=$rs->db_data['ID'];
				$sql='UPDATE products SET Data_fim="'.$udate.'",Carrinho=1,Variante="'.$Variante.'",localizacao_fisica="'.$localizacao_fisica.'",ID_ORG="'.$FAM_ID.'",ID_Marca="'.$MARCA_ID.'",Aproved="'.$active.'",Flag="'.$active.'",EAN="'.$EAN.'" WHERE Ref="'.$ItemCode.'"';
echo $sql."\n";
				$res=Runquery($db_link,$DB_name,$sql);
			}
			else
			{
				$sql='INSERT INTO products(Ref,ID_ORG,ID_Marca,Data_criacao,Aproved,Flag,ID_Integracao,Carrinho,EAN,Variante,localizacao_fisica) VALUES ("'.$ItemCode.'","'.$FAM_ID.'","'.$MARCA_ID.'","'.$udate.'","'.$active.'","'.$active.'","'.$ItemCodeID.'",1,"'.$EAN.'","'.$Variante.'","'.$localizacao_fisica.'")';
				//echo $sql."\n";
				$res=Runquery($db_link,$DB_name,$sql);
				$prod_id=db_get_last_insert_id($db_link);
			}
			$rs->db_close();
			
			if($prod_id > 0)
			{
				detect_product_lang($prod_id,$Cabecalho,'pt');

				//**********************
				$sql='SELECT * FROM products_families_obj WHERE ID_PROD="'.$prod_id.'"';
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$sql='UPDATE products_families_obj SET ID_ORG="'.$FAM_ID.'" WHERE ID_PROD = "'.$prod_id.'"';
					$res=Runquery($db_link,$DB_name,$sql);
				}
				else
				{
					$sql='INSERT INTO products_families_obj(ID_ORG,ID_PROD) VALUES ("'.$FAM_ID.'","'.$prod_id.'")';
					$res=Runquery($db_link,$DB_name,$sql);
				}
				$rs->db_close();
			}


			if($res) return array('prod_id' => $prod_id);
			else return array('erro' => 'ERRO NA INSERÇÃO/ATUALIZAÇÃO DO PRODUTO');
		}

		return array('erro' => 'REFERÊNCIA DO PRODUTO VAZIA');
	}
	function detect_product_lang($prod_id,$Cabecalho,$lingua='pt')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2=new DataObj;

		$sql='SELECT ID FROM products_lang WHERE ID_PAI="'.$prod_id.'" AND Lang="'.$lingua.'"';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$sql='UPDATE products_lang SET Cabecalho="'.addslashes($Cabecalho).'" WHERE ID_PAI="'.$prod_id .'" and Lang="'.$lingua.'"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO products_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$prod_id.'","'.addslashes($Cabecalho).'","'.$lingua.'")';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$rs2->db_close();
	}
	// PRODUTO NA FAMILIA
	function detect_famprod($ID_PROD=0,$ID_ORG=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql='SELECT ID FROM products_families_obj WHERE ID_PROD="'.$ID_PROD.'" AND ID_ORG="'.$ID_ORG.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$sql='UPDATE products_families_obj SET ID_ORG="'.$ID_ORG.'" WHERE ID_PROD="'.$ID_PROD.'"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO products_families_obj (ID_PROD,ID_ORG) VALUES ("'.$ID_PROD.'","'.$ID_ORG.'")';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		if($ID_ORG > 0)
		{
			$sql='DELETE FROM products_families_obj WHERE ID_PROD="'.$ID_PROD.'" AND ID_ORG<>"'.$ID_ORG.'"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$rs->db_close();
	}
	// TABELAS DE EQUIVALENTES
	function detect_products_equivalentes($ID_PROD=0,$equivalentes=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$equivalentes_arr=explode('||',ltrim(rtrim($equivalentes,'|'),'|'));

		$sql='DELETE FROM produtos_opcoes WHERE ID_Prod='.$ID_PROD;
		$res=Runquery($db_link,$DB_name,$sql);

		for($a=0; $a<count($equivalentes_arr); $a++)
		{
			$sql='SELECT ID FROM products WHERE Ref="'.addslashes($equivalentes_arr[$a]).'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$sql='INSERT INTO produtos_opcoes (ID_Prod,ID_Prod2) VALUES ("'.$ID_PROD.'","'.$rs->db_data['ID'].'")';
				$res=Runquery($db_link,$DB_name,$sql);
			}
			$rs->db_close();
		}
	}
	// TABELAS DE PREÇOS
	function detect_products_objs($ID_PROD=0,$obj_pvp=0,$obj_qtdmin=0,$obj_stock=0,$obj_stockdate='',$obj_ref='',$tipo_pvp=0,$obj_iva=0,$obj_iva_txt='',$fam_id1=0,$GRAU='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql='SELECT obj_id FROM products_objs WHERE obj_type_id="'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id= $rs->db_data['obj_id'];

			$sql='UPDATE products_objs SET grau="'.$GRAU.'",obj_type="products",obj_pvp="'.$obj_pvp.'",obj_qtdmin="'.$obj_qtdmin.'",obj_stock="'.$obj_stock.'",obj_stockdate="'.$obj_stockdate.'",obj_iva="'.$obj_iva.'",obj_iva_txt="'.$obj_iva_txt.'" WHERE obj_type_id="'.$ID_PROD.'" AND obj_val2="'.$tipo_pvp.'"';
			$res=Runquery($db_link,$DB_name,$sql);

			$sql='UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang="pt"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO products_objs (grau,obj_type_id,obj_ativo,obj_pvp,obj_qtdmin,obj_stock,obj_stockdate,obj_type,obj_val2,obj_iva,obj_iva_txt) VALUES ("'.$GRAU.'","'.$ID_PROD.'",1,"'.$obj_pvp.'","'.$obj_qtdmin.'","'.$obj_stock.'","'.$obj_stockdate.'","products","'.$tipo_pvp.'","'.$obj_iva.'","'.$obj_iva_txt.'")';
			$res=Runquery($db_link,$DB_name,$sql);
			$obj_id=db_get_last_insert_id($db_link);

			$sql='INSERT INTO products_objs_lang (Cabecalho,ID_PAI,Lang) VALUES ("'.$obj_ref.'","'.$obj_id.'","pt")';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$rs->db_close();
	}
	function getCategoriesRecursive($id_org)
	{
		$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$obj=new DataObj;
		$arvore2=array();
		$pai=$id_org;
		while($pai > 0)
		{
			$id_org=$pai;
			$sql="SELECT ID,ID_ORG FROM products_families WHERE ID=$pai";
			$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
			$pai=$obj->db_data["ID_ORG"];
			$arvore2[]=$obj->db_data["ID"];
		}

		if(empty($arvore2)) $arvore2[]=$id_org;

		$output=array();
		$t=(count($arvore2)-1);
		for($a=0;$a<count($arvore2);$a++)
		{
			$output[$t]=$arvore2[$a];
			$t--;
		}
		ksort($output);
		$obj->db_close(1);
		return $output;
	}
	function detect_filter_products($prod_id,$id_org=0,$marca_id=0,$category_id_import=0,$value_id_import=0,$mostra='1')
	{
		if($prod_id > 0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs2=new DataObj;

			if($category_id_import==0 && $value_id_import==0) $sql='SELECT prod_id FROM filters_products WHERE prod_id="'.$prod_id.'"';
			else $sql='SELECT prod_id FROM filters_products WHERE prod_id="'.$prod_id.'" AND cat_id="'.$category_id_import.'" AND valor_id="'.$value_id_import.'"';

			$fams=getCategoriesRecursive($id_org);
			// print_r($fams);
			if(isset($fams[0])) $fam_id1=$fams[0]; else $fam_id1=$id_org;
			if(isset($fams[1])) $fam_id2=$fams[1]; else $fam_id2=0;
			if(isset($fams[2])) $fam_id3=$fams[2]; else $fam_id3=0;
			if(isset($fams[3])) $fam_id4=$fams[3]; else $fam_id4=0;

			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$sql ='UPDATE filters_products SET FLAG=1,filtro_area="'.$mostra.'",filtros_rel2="'.$category_id_import.'_'.$value_id_import.'", fam_id1="'.$fam_id1.'",fam_id2="'.$fam_id2.'",fam_id3="'.$fam_id3.'",marca_id="'.$marca_id.'" where prod_id="'.$prod_id.'" AND cat_id="'.$category_id_import.'" AND valor_id="'.$value_id_import.'"';
				$res=Runquery($db_link,$DB_name,$sql);
			}
			else
			{
				$sql='INSERT INTO filters_products(cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,fam_id4,marca_id,filtros_rel2,FLAG,filtro_active,filtro_area)
					  VALUES("'.$category_id_import.'","'.$value_id_import.'","'.$prod_id.'","'.$fam_id1.'","'.$fam_id2.'","'.$fam_id3.'","'.$fam_id4.'","'.$marca_id.'","'.$category_id_import.';'.$value_id_import.'",1,1,"'.$mostra.'")';
				$res=Runquery($db_link,$DB_name,$sql);
			}
			$rs2->db_close();


			$filtros_rel2='';
			$sql='SELECT * FROM filters_products WHERE prod_id="'.$prod_id.'" GROUP BY cat_id,valor_id';
			$rs2->db_open($db_link,$DB_name,$sql);
			while (!$rs2->EOF())
			{
				$filtros_rel2 .= $rs2->db_data['cat_id'].'_'.$rs2->db_data['valor_id'].';';
				$rs2->db_next();
			}
			$res = Runquery($db_link,$DB_name,'UPDATE filters_products SET filtros_rel2="'.$filtros_rel2.'" WHERE prod_id="'.$prod_id.'"');

			// echo $sql."\n";
			$rs2->db_close(1);
		}
	}
	function limpa_special_sync($s)
	{
	    $s=strip_tags($s);
		$s=str_replace('<br>','',$s);
		$s=trim($s,"\t\n\r\0\x0B");
		$v=array("(",")","³","°","²","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","?","#","=");
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
		if(mb_strlen($s,"UTF-8") > 100) $s=mb_substr($s,0,100,"UTF-8");
		return $s;
	}
	function detect_filter_values($ordem2=0,$category_id_import=0,$value_id_import='',$lingua='pt',$filter='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2=new DataObj;

		$value_id=0;

		// echo 'xx2:'.$category_id_import.'|'.$value_id_import."\n";

		if(trim($filter) != '')
		{
			// if($filter == 'I') $filter='INVERNO';
			// elseif($filter == 'V') $filter='VERÃO';
			// elseif($filter == 'P') $filter='PERMANENTE';

			$sql='
					SELECT
						value_id
					FROM
						filters_values
					WHERE
						value_id_import_ref="'.limpa_special_sync($value_id_import).'"
					';
				// echo $sql."\n";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$value_id=$rs2->db_data['value_id'];
				// $sql='UPDATE filters_values SET value_order="'.$ordem2.'",category_id='.$category_id_import.',values_name="'.addslashes($filter).'",value_active=1,value_flag=1 WHERE value_id_import_ref="'.limpa_special($value_id_import).'"';
				$sql='UPDATE filters_values SET category_id='.$category_id_import.',value_active=1,value_flag=1,data="'.date('Y-m-d H:i:s').'" WHERE value_id_import_ref="'.limpa_special_sync($value_id_import).'"';
				$res=Runquery($db_link,$DB_name,$sql);

				// for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				// {
					// $lingua=$GLOBALS['linguas_importa'][$a];
					// $sql='UPDATE filters_values_lang SET values_name="'.addslashes($filter).'" WHERE category_id_import='.$category_id_import.' AND value_id_import_ref="'.limpa_special($value_id_import).'" AND values_lang="'.$lingua.'"';
					// $res=Runquery($db_link,$DB_name,$sql);
				// }
			}
			else
			{
				$sql='INSERT INTO filters_values(value_order,category_id,value_active,value_flag,category_id_import,value_id_import_ref,data) VALUES('.$ordem2.','.$category_id_import.',1,1,'.$category_id_import.',"'.limpa_special_sync($value_id_import).'","'.date('Y-m-d H:i:s').'")';
				$res=Runquery($db_link,$DB_name,$sql);
				$value_id=db_get_last_insert_id($db_link);
				for($a=0; $a<count($GLOBALS['linguas_importa']); $a++)
				{
					$lingua=$GLOBALS['linguas_importa'][$a];
					$sql='INSERT INTO filters_values_lang(values_id,values_name,values_lang,category_id_import,value_id_import_ref) VALUES( '.$value_id.',"'.addslashes($filter).'","'.$lingua.'",'.$category_id_import.',"'.limpa_special_sync($value_id_import).'")';
					// echo $sql."\n";
					$res=Runquery($db_link,$DB_name,$sql);
				}
			}
			$rs2->db_close(1);
		}
		return $value_id;
	}
	// TABELAS DE FILTROS
	function detetect_product_filters($fam_id=0,$prod_id=0,$marca_id=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$fams=getCategoriesRecursive_sync($fam_id);

		if(isset($fams[0])) $fam_id1=$fams[0]; else $fam_id1=$fam_id;
		if(isset($fams[1])) $fam_id2=$fams[1]; else $fam_id2=0;
		if(isset($fams[2])) $fam_id3=$fams[2]; else $fam_id3=0;

		$rs3=new DataObj;
		$rs3->db_open($db_link,$DB_name,'SELECT prod_id FROM filters_products WHERE prod_id="'.$prod_id.'"');
		if($rs3->EOF())
		{

			$sql='
					INSERT INTO filters_products
					(
						cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,marca_id,filtros_rel2,FLAG,filtro_active
					)
					VALUES
					(
						"0","0","'.$prod_id.'","'.$fam_id1.'","'.$fam_id2.'","'.$fam_id3.'","'.$marca_id.'","",1,1
					)
				';

			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
		else
		{
			$sql ='UPDATE filters_products SET fam_id1="'.$fam_id1.'",fam_id2="'.$fam_id2.'",fam_id2="'.$fam_id3.'",marca_id="'.$marca_id.'" where prod_id="'.$prod_id.'"';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
		}
	}
	function getCategoriesRecursive_sync($id_org)
	{
		$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$obj=new DataObj;
		$arvore2=array();
		$pai=$id_org;
		while($pai > 0)
		{
			$id_org=$pai;
			$sql="SELECT ID,ID_ORG FROM products_families WHERE ID=$pai";
			$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
			$pai=$obj->db_data["ID_ORG"];
			$arvore2[]=$obj->db_data["ID"];
		}

		if(empty($arvore2)) $arvore2[]=$id_org;

		$output=array();
		$t=(count($arvore2)-1);
		for($a=0;$a<count($arvore2);$a++)
		{
			$output[$t]=$arvore2[$a];
			$t--;
		}
		ksort($output);
		$obj->db_close();
		return $output;
	}
	function detect_brand($brand='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$ItemCode=trim($brand);
		$marca_id=0;
		if($ItemCode != '')
		{
			$ItemCode=addslashes(limpa_special($ItemCode));
			$data_atualizacao=date('Y-m-d H:i:s');

			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			$sql='SELECT ID FROM brands WHERE ref_integracao="'.addslashes($ItemCode).'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$marca_id=$rs->db_data['ID'];

				$sql='UPDATE brands SET data_atualizacao="'.$data_atualizacao.'",FLAG=1 WHERE ref_integracao="'.$ItemCode.'"';
				$res=Runquery($db_link,$DB_name,$sql);
			}
			else
			{
				$sql='INSERT INTO brands (visivel,ref_integracao,data_atualizacao,FLAG) VALUES (1,"'.$ItemCode.'","'.$data_atualizacao.'",1)';
				$res=Runquery($db_link,$DB_name,$sql);
				$marca_id=db_get_last_insert_id($db_link);
			}
			$rs->db_close();
			if($marca_id > 0)
			{
				detect_brand_lang($marca_id,$ItemCode,'pt');
			}
		}
		return $marca_id;
	}
	function detect_brand_lang($marca_id,$ItemCode,$lingua)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2=new DataObj;

		$sql='SELECT ID FROM brands_lang WHERE ID_PAI="'.$marca_id.'" AND Lang="'.$lingua.'"';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$sql='UPDATE brands_lang SET Nome="'.$ItemCode.'" WHERE ID_PAI="'.$marca_id .'" and Lang="'.$lingua.'"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO brands_lang (ID_PAI,Nome,Lang) VALUES ("'.$marca_id.'","'.$ItemCode.'","'.$lingua.'")';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$rs2->db_close();
	}
	function ean_check($ean)
	{
		return true;


		
	    $ean=strrev($ean);
	    // Split number into checksum and number
	    $checksum=substr($ean,0,1);
	    $number=substr($ean,1);
	    $total=0;
	    for ($i=0,$max=strlen($number); $i < $max; $i++)
	    {
	        if (($i % 2) == 0) {$total += ($number[$i] * 3);} 
	        else {$total += $number[$i];}
	    }
	    $mod=($total % 10);
	    $calculated_checksum=(10 - $mod);
	    if ($calculated_checksum == $checksum) {return true;} 
	    else {return false;}
	}
?>
