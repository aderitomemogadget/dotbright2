<?php

$path=dirname( __FILE__ ) . '/../../core/';
require_once $path . "core_files.php";
$path2=dirname( __FILE__ ) . '/../../';
include $path2 . 'admin/products/filtros/funcoes.inc.php';
if(!isset($_GET['log'])) $log=0;
error_reporting(E_ERROR);
ini_set('display_errors',1);

header('Content-Type: text/html; charset=utf-8');
ini_set("max_execution_time","60000");
ini_set("max_input_time","60000");
ini_set("memory_limit","-1");

//ler do settings o id de cliente no site da macorlux
global $id_revendedor;

$filename=$path2 . 'files/json_'.date('Ymd').'_'.$id_revendedor.'.txt';
$filename_importado=$path2 . 'files/importado/json_'.date('Ymd').'_'.$id_revendedor.'.txt';

if(stream_resolve_include_path($filename_importado)) exit; // CASO JÁ TENHA IMPORTADO HOJE MORRE AQUI
//Se ainda não existir,faz download do ficheiro da macorlux
echo "FASE 1\n";
if(!stream_resolve_include_path($filename))
{
    echo "1.1 A descarregar jason ".'http://www.macorlux.pt/img_upload/cache/json_'.date('Ymd').'_'.$id_revendedor.".txt\n";
    if($json=file_get_contents('http://www.macorlux.pt/img_upload/cache/json_'.date('Ymd').'_'.$id_revendedor.'.txt'))
    {
        file_put_contents($filename,$json);
    }
    else
    {
        echo file_get_contents('http://www.macorlux.pt/webservice_freeplace.php?user=memogadget&pass=domesticos&id_revendedor='.$id_revendedor.'&exec=1');
        exit;
    }
}
else
{
    echo "1.2 A importar o ficheiro $filename\n";
    $json=file_get_contents($filename);
}


echo $json;
$json=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json), true );
//$json=json_decode($json,true);
$cont=0;
$cont2=0;
$rs=new DataObj;
$rs2=new DataObj;
$rs3=new DataObj;
$rs4=new DataObj;
global $DB_host,$DB_user,$DB_pass,$DB_name,$prev,$online_edit;
$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

// vamos verificar se os campos existem todos na base de dados
$fields=db_list_fields($DB_name,'products_images',$db_link);
$columns=db_num_fields($fields);
for($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
if(!in_array('URL_Source',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE products_images ADD URL_Source Varchar(255) ');

$sql='INSERT INTO z_integracao_api(DATA_INI,DATA_FIM,FICHEIRO,IMPORTADO) VALUES("'.date('Y-m-d H:i:s').'","0000-00-00 00:00:00","'.$filename.'",0)';
$res=Runquery($db_link,$DB_name,$sql);

// VAMOS COLOCAR A FLAG DE TODOS A ZERO E SÓ FINAL VAMOS COLOCAR INATIVOS AQUELES CUJO A FLAG CONTINUAR A 0
$res=Runquery($db_link,$DB_name,'UPDATE filters_categories SET category_flag=0');
$res=Runquery($db_link,$DB_name,'UPDATE products SET Flag=0 WHERE ID_Integracao > 0 AND Sync=0');
$res=Runquery($db_link,$DB_name,'UPDATE filters_values SET value_flag=0 WHERE value_id_import > 0');
$res=Runquery($db_link,$DB_name,'UPDATE filters_products SET FLAG=0 WHERE data_atualizacao != "0000-00-00 00:00:00"');
$email_txt='';
global $iva_default;
$filtros_rel2='';
$xz=0;



foreach($json as $produtos)
{
    $t=count($produtos);
    foreach($produtos as $prod)
    {
        echo "$t|";$t--;
        //inicializa variaveis
        $preco_revenoff_marca=0;
        $preco_revenoff_fam=0;
        $preco_revenoff_prod=0;
        $prod_id=0;
        $outlet=0;
        $val_p2=0;
        $news_l=0;
        $val_promo=0;

        //db_closedb_conection($db_link);
        //$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $familia=$prod['Familia'];
        $ProdAss=$prod['ProdAss'];
        $subfamilia=$prod['SubFamilia'];
        $marca=$prod['Marca'];
        $ProdAss=$prod['ProdAss'];
        $products_quantity=$prod['Stock'];
        $products_name=addslashes(trim($prod['Nome']));
        $video = $prod['Video'];
        $corEE = $prod['CorEtiquetaEnergia'];

        //Verifica se já existe a referência na BD
        $rs->db_open($db_link,$DB_name,'SELECT ID,ID_ORG,ID_Integracao,Ref,preco_revenoff,Sync FROM products WHERE Ref="'.$prod['Referencia'].'"');
        if(!$rs->EOF())
        {
            $produto_id=$rs->db_data['ID'];
            $ID_ORG=$rs->db_data['ID_ORG'];
            $sync = $rs->db_data['Sync'];

            //JÁ EXISTE, VAMOS ATUALIZAR O PRODUTO
            $preco_revenoff_prod = $rs->db_data['preco_revenoff'];
            if($familia['ID'] > 0)
            {
                $rs2->db_open($db_link,$DB_name,'SELECT * FROM products_families WHERE ID_Integracao="'.$familia['ID'].'" ');
                if(!$rs2->EOF())
                {
                    //update familia
                    $fam_id=$rs2->db_data['ID'];
                    $preco_revenoff_fam=$rs2->db_data['preco_revenoff'];
                    $sql='UPDATE products_families_lang SET Cabecalho="'.$familia['Valor'].'" WHERE ID_PAI="'.$fam_id .'" and Lang="pt"';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                else
                {
                    //insert familia
                    $sql='INSERT INTO products_families (visivel,ID_integracao) VALUES (1,"'.$familia['ID'].'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $fam_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'","'.$familia['Valor'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                $fams_gen[]=$fam_id;
            }
            if($subfamilia['ID'] > 0)
            {
                $rs3->db_open($db_link,$DB_name,'SELECT * FROM products_families WHERE ID_Integracao="'.$subfamilia['ID'].'"');
                if(!$rs3->EOF())
                {
                    //UPDATE SUB FAM
                    $sub_fam_id=$rs3->db_data['ID'];
                    $preco_revenoff_fam=$rs3->db_data['preco_revenoff'];
                    $sql='UPDATE products_families_lang SET Cabecalho="'.$subfamilia['Valor'].'" WHERE ID_PAI="'.$sub_fam_id .'" and Lang="pt"';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                else
                {
                    //INSERT SUB FAM
                    $sql='INSERT INTO products_families (visivel,ID_ORG,ID_integracao) VALUES (1,"'.$fam_id.'","'.$subfamilia['ID'].'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $sub_fam_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$sub_fam_id.'","'.$subfamilia['Valor'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                $rs3->db_close();
                $fams_gen[] = $sub_fam_id;
            }
            $rs2->db_close();
            if($marca['ID'] > 0)
            {
                $rs2->db_open($db_link,$DB_name,'SELECT * FROM brands WHERE ID_Integracao="'.$marca['ID'].'"');
                if(!$rs2->EOF())
                {
                    //UPDATE Marca
                    $marca_id=$rs2->db_data['ID'];
                    $preco_revenoff_marca=$rs2->db_data['preco_revenoff'];
                    $sql='UPDATE brands_lang SET Nome="'.$marca['Valor'].'" WHERE ID_PAI="'.$marca_id .'" and Lang="pt"';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                else
                {
                    //Insert Marca
                    $sql='INSERT INTO brands (Visivel,ID_integracao) VALUES (1,"'.$marca['ID'].'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $marca_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO brands_lang (ID_PAI,Nome,Lang) VALUES ("'.$marca_id.'","'.$marca['Valor'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                $rs2->db_close();
                $marcas_gen[]=$marca_id;
            }
            if($sub_fam_id == '') $sub_fam_id=$fam_id;
            // UPDATE Produto

            if($ID_ORG!=$sub_fam_id) $ID_ORG=$sub_fam_id;
            if(substr($products_name,0,1) === '+') { $outlet=1;  $products_name=ltrim($products_name ,'+');}
            $products_description=addslashes(str_replace("|ln|","<br/>",$prod['Descricao']));
            if($ProdAss=='')
            {
                $obj_pvp_old=0;
                /*$sql_ref='SELECT obj_pvp FROM products_objs WHERE obj_type_id="'.$produto_id.'"';
                $rs4->db_open($db_link,$DB_name,$sql_ref);
                if(!$rs4->EOF()) $obj_pvp_old=floatval($rs4->db_data['obj_pvp']);
                $rs4->db_close();*/
                //VOLTA A INSERIR AS LINHAS DE PRODUCTS_OBJ E LANG
                if($preco_revenoff_prod==1) $val_p=floatval(aplicarIVA($prod['Preco_Original']));
                elseif($preco_revenoff_fam==1) $val_p=floatval(aplicarIVA($prod['Preco_Original']));
                elseif($preco_revenoff_marca==1) $val_p=floatval(aplicarIVA($prod['Preco_Original']));
                else $val_p=floatval($prod['Preco_Original']);
                /*$Data_ultimo_desconto='0000-00-00';*/
                $Desconto_percentagem=0;

                /*$perc_verfica=((($obj_pvp_old - $val_p ) / ($obj_pvp_old)) * 100);

                if($id_revendedor == 16305) // RPCMOVEIS
                {
                    $news_l = 1;
                    $val_promo = 0;
                }
                else
                {
                    if($val_p < $obj_pvp_old && $perc_verfica > 60)
                    {
                        $val_p2=$val_p;
                        $val_p=$obj_pvp_old;
                        $val_promo = 0;
                        $email_txt.='<br />Produto: '.$prod['Referencia'].' Preço Antigo: '.$val_p.' | Preço Novo: '.$val_p2.' - <a href="'.$GLOBALS['site_http_url'].'crons/webservicefreeplace_filtros.php?ref_upd='.$prod['Referencia'].'">Atualizar Preço</a>';
                    }
                    elseif($val_p < $obj_pvp_old && $val_p > 0 && $perc_verfica <= 60) //  && $perc_verfica <= 20
                    {
                        $news_l=1;
                        $val_promo=$val_p;
                        $val_p=$obj_pvp_old;
                        $Data_ultimo_desconto=date('Y-m-d');
                        $Desconto_percentagem=$perc_verfica;
                    }
                    else $news_l=1;
                }

                if($val_promo!=$val_p && $val_promo > 0) $promo=1; else $promo=0;
                if($val_promo > $val_p)
                {
                    //Se o valor da promocao for maior do que o valor anterior, temos de anular a promocao
                    $promo = 0;
                    $val_p=$val_promo;
                    $val_promo=0;
                }*/

            }

            if($sync == 0)
            {
                if($rs->db_data['ID_Integracao'] == 0)
                {
                    $sql='UPDATE products SET Carrinho=1,Aproved=1,Flag=1,ID_Integracao="'.$prod['ID'].'",Preco1="'.$prod['Preco'].'",MargemCli="'.aplicarMargem_ret($prod['Preco']).'",Preco2="'.ValorPagarMargens($prod['Preco']).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'", Video="'.$video.'", CorEE="'.$corEE.'" WHERE ID="'.$produto_id.'"';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                else
                {
                    if($ProdAss != '') $sql='UPDATE products SET Carrinho=1,Flag=1,Preco1="'.$prod['Preco'].'",MargemCli="'.aplicarMargem_ret($prod['Preco']).'",Preco2="'.ValorPagarMargens($prod['Preco']).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'", Video="'.$video.'", CorEE="'.$corEE.'" WHERE ID="'.$produto_id.'"';
                    else $sql = 'UPDATE products SET Carrinho=1,Aproved=1,Flag=1,Preco1="'.$prod['Preco'].'",MargemCli="'.aplicarMargem_ret($prod['Preco']).'",Preco2="'.ValorPagarMargens($prod['Preco']).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'", Video="'.$video.'", CorEE="'.$corEE.'" WHERE ID="'.$produto_id.'"';
                    $res = Runquery($db_link,$DB_name,$sql);
                }

                $sql = 'UPDATE products_lang SET Cabecalho="'.$products_name.'",Texto1="'.$products_description.'" WHERE ID_PAI="'.$produto_id.'" AND Lang="pt"';
                $res = Runquery($db_link,$DB_name,$sql);
                if($prod['Imagem']!='')
                {
                    echo "I1";

                    $url=$prod['Imagem'];
                    //echo "1";
                    $ext=explode('.',$url);
                    //echo "2";
                    $img=$site_shell_url.'files/products/'.$produto_id.'_1.'.end($ext);
                    //echo "3";
                    //Vamos verificar se a imagem já existe do nosso lado
                    if(!stream_resolve_include_path($img))
                    {
                        //echo "4";
                        echo "U1";
                        file_put_contents($img,file_get_contents($url));
                        $res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                        $res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=1');
                        $res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
                    }
                    else
                    {
                        //echo "5";
                        $sql='SELECT ID_ORG FROM products_images WHERE ID_ORG="'.$produto_id.'"';
                        $rs4->db_open($db_link,$DB_name,$sql);
                        if($rs4->EOF())
                        {
                            //Por algum motivo gravou a imagem mas não gravou em base de dados
                            $res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
                        }
                        $rs4->db_close();
                    }
                }

                // Etiqueta de Energia
                if($prod['EtiquetaEnergia'] != '')
                {
                    echo "I2";
                    $url=$prod['EtiquetaEnergia'];
                    $ext=explode('.',$url);
                    $ee=$site_shell_url.'files/products/EE/'.$produto_id.'_1.'.end($ext);
                    if(!is_dir($site_shell_url.'files/products/EE')) mkdir($site_shell_url.'files/products/EE');
                    //Vamos verificar se a imagem já existe do nosso lado
                    if(!stream_resolve_include_path($ee) && !stream_resolve_include_path($site_shell_url.'files/products/EE/'.$produto_id.'_1.lnk'))
                    {
                        echo "U2";
                        file_put_contents($ee,file_get_contents($url));
                        $res=Runquery($db_link,$DB_name,'UPDATE products SET ee_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                    }
                }else $res=Runquery($db_link,$DB_name,'UPDATE products SET ee_ext="" WHERE ID="'.$produto_id.'"');
                // Ficha União Europeia
                if($prod['FichaUE'] != '')
                {
                    echo "I3";
                    $url=$prod['FichaUE'];
                    $ext=explode('.',$url);
                    $ue=$site_shell_url.'files/products/UE/'.$produto_id.'_1.'.end($ext);
                    if(!is_dir($site_shell_url.'files/products/UE')) mkdir($site_shell_url.'files/products/UE');
                    //Vamos verificar se a imagem já existe do nosso lado
                    if(!stream_resolve_include_path($ue) && !stream_resolve_include_path($site_shell_url.'files/products/UE/'.$produto_id.'_1.lnk'))
                    {
                        echo "U3";
                        file_put_contents($ue,file_get_contents($url));
                        $res=Runquery($db_link,$DB_name,'UPDATE products SET ue_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                    }
                } else $res=Runquery($db_link,$DB_name,'UPDATE products SET ue_ext="" WHERE ID="'.$produto_id.'"');
                //Desenhos Técnicos
                if($prod['DesenhosTecnicos']!='')
                {
                    echo "I4";
                    $url=$prod['DesenhosTecnicos'];
                    $ext=explode('.',$url);
                    $dt=$site_shell_url.'files/products/DT/'.$produto_id.'_1.'.end($ext);
                    if(!is_dir($site_shell_url.'files/products/DT')) mkdir($site_shell_url.'files/products/DT');
                    //Vamos verificar se a imagem já existe do nosso lado
                    if(!stream_resolve_include_path($dt) && !stream_resolve_include_path($site_shell_url.'files/products/DT/'.$produto_id.'_1.lnk'))
                    {
                        echo "U4";
                        file_put_contents($dt,file_get_contents($url));
                        $res=Runquery($db_link,$DB_name,'UPDATE products SET dt_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                    }
                }else $res=Runquery($db_link,$DB_name,'UPDATE products SET dt_ext="" WHERE ID="'.$produto_id.'"');

				if($ProdAss == '')
				{
					if($id_revendedor == 16305)
					{
						$sql = 'SELECT obj_type_id FROM products_objs WHERE	obj_type_id="'.$produto_id.'"';
						$rs->db_open($db_link, $DB_name, $sql);
						if(!$rs->EOF())
						{
							$sql = 'UPDATE products_objs SET ';
							$sql .= 'obj_stock="'.$products_quantity.'", ';
							$sql .= 'obj_iva="'.$iva_default.'", ';
							$sql .= 'obj_pvp="'.$val_p.'", ';
							$sql .= 'obj_ativo=1, ';
							$sql .= 'obj_val=1, ';
							$sql .= 'obj_destaque="'.$prod['Destaque'].'", ';
							$sql .= 'obj_novo="'.$prod['Novidade'].'", ';
							$sql .= 'obj_novo2="'.$news_l.'", ';
							$sql .= 'obj_outlet="'.$outlet.'", ';
							$sql .= 'obj_qtdmin="'.$prod['QtdMinEnc'].'",';
							$sql .= 'obj_ecotaxas="'.addslashes($prod['Ecotaxas']).'" ';
							$sql .= ' WHERE obj_type_id="'.$produto_id.'"';
							$res = Runquery($db_link, $DB_name, $sql);

							if($produto_id == 12768) echo $sql."\n";

						}
						else
						{
							$sql = 'DELETE FROM products_objs_lang WHERE ID_PAI IN (SELECT obj_id FROM products_objs WHERE obj_type_id="'.$produto_id.'")';
							$sql2 = 'DELETE FROM products_objs WHERE obj_type_id="'.$produto_id .'"';
							$res = Runquery($db_link,$DB_name, $sql.';'.$sql2);
							$sql = 'INSERT INTO products_objs (obj_type_id,obj_type,obj_stock,obj_iva,obj_pvp,obj_pvp2,obj_promo,obj_promo_ativo,obj_ativo,obj_val,obj_destaque,obj_novo,obj_novo2,obj_outlet,obj_qtdmin,obj_ecotaxas) VALUES
							("'.$produto_id.'","products","'.$products_quantity.'","'.$iva_default.'","'.$val_p.'","'.$val_p2.'","'.$val_promo.'","'.$promo.'",1,1,"'.$prod['Destaque'].'","'.$prod['Novidade'].'","'.$news_l.'","'.$outlet.'","'.$prod['QtdMinEnc'].'","'.addslashes($prod['Ecotaxas']).'")';
							$res = Runquery($db_link,$DB_name,$sql);
							$obj_id = db_get_last_insert_id($db_link);
							$sql = 'INSERT INTO products_objs_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'", "'.$prod['Referencia'].'", "pt")';
							$res = Runquery($db_link,$DB_name,$sql);
						}
						$rs->db_close();
					}
					else
					{
						$sql = 'DELETE FROM products_objs_lang WHERE ID_PAI IN (SELECT obj_id FROM products_objs WHERE obj_type_id="'.$produto_id.'")';
						$sql2 = 'DELETE FROM products_objs WHERE obj_type_id="'.$produto_id .'"';
						$res = Runquery($db_link,$DB_name, $sql.';'.$sql2);
						$sql = 'INSERT INTO products_objs (obj_type_id,obj_type,obj_stock,obj_iva,obj_pvp,obj_pvp2,obj_promo,obj_promo_ativo,obj_ativo,obj_val,obj_destaque,obj_novo,obj_novo2,obj_outlet,obj_qtdmin,obj_ecotaxas) VALUES
						("'.$produto_id.'","products","'.$products_quantity.'","'.$iva_default.'","'.$val_p.'","'.$val_p2.'","'.$val_promo.'","'.$promo.'",1,1,"'.$prod['Destaque'].'","'.$prod['Novidade'].'","'.$news_l.'","'.$outlet.'","'.$prod['QtdMinEnc'].'","'.addslashes($prod['Ecotaxas']).'")';
						$res = Runquery($db_link,$DB_name,$sql);
						$obj_id = db_get_last_insert_id($db_link);
						$sql = 'INSERT INTO products_objs_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'", "'.$prod['Referencia'].'", "pt")';
						$res = Runquery($db_link,$DB_name,$sql);
					}
				}

               /*  if($ProdAss=='')
                {
                    //echo "a1";
                    //LIMPA LINHAS DO PRODUCTS_OBJ E AS LANG
                    $sql='DELETE FROM products_objs_lang WHERE ID_PAI IN (SELECT obj_id FROM products_objs WHERE obj_type_id="'.$produto_id.'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $sql='DELETE FROM products_objs WHERE obj_type_id="'.$produto_id .'"';
                    $res=Runquery($db_link,$DB_name,$sql);
                    // obj_outlet
                    $sql='INSERT INTO products_objs (obj_type_id,obj_type,obj_stock,obj_iva,obj_pvp,obj_pvp2,obj_promo,obj_promo_ativo,obj_ativo,obj_val,obj_destaque,obj_novo,obj_novo2,obj_outlet,obj_qtdmin,obj_ecotaxas) VALUES ("'.$produto_id.'","products","'.$products_quantity.'","'.$iva_default.'","'.$val_p.'","'.$val_p2.'","'.$val_promo.'","'.$promo.'",1,1,"'.$prod['Destaque'].'","'.$prod['Novidade'].'","'.$news_l.'","'.$outlet.'","'.$prod['QtdMinEnc'].'","'.addslashes($prod['Ecotaxas']).'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $obj_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO products_objs_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'","'.$prod['Referencia'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                } */
                $Sugestoes=$prod['Sugestoes'];
                if(is_array($Sugestoes) && !empty($Sugestoes))
                {
                    for ($i=0; $i < count($Sugestoes); $i++)
                    {
                        $rs2->db_open($db_link,$DB_name,'SELECT * FROM products WHERE ID_Integracao="'.$Sugestoes[$i].'"');
                        if(!$rs2->EOF())
                        {
                            $prod_id2=$rs2->db_data['ID'];
                            $rs3->db_open($db_link,$DB_name,'SELECT * FROM produtos_sugestoes WHERE ID_Prod="'.$produto_id.'" AND ID_Prod2="'.$prod_id2.'"');
                            if($rs3->EOF())
                            {
                                $res=Runquery($db_link,$DB_name,'INSERT INTO produtos_sugestoes (ID_Prod,ID_Prod2) VALUES ("'.$produto_id.'","'.$prod_id2.'")');
                            }
                            $rs3->db_close();
                        }
                    }
                }
                //echo "a2";
                $rs2->db_open($db_link,$DB_name,'SELECT * FROM products_destaques WHERE ID_Prod="'.$produto_id.'"');
                if(!$rs2->EOF())
                {
                    if($prod['Destaque']==0)$res=Runquery($db_link,$DB_name,'DELETE FROM products_destaques WHERE ID_Prod="'.$produto_id.'"');
                }
                else
                {
                    if($prod['Destaque']==1)$res=Runquery($db_link,$DB_name,'INSERT INTO products_destaques (ID_Prod,Ordem) VALUES ("'.$produto_id.'",0)');
                }
                $rs2->db_close();
            }
            else
            {
                if($prod['Imagem']!='')
                {
                    echo "I5";
                    $url = $prod['Imagem'];
                    $ext = explode('.',$url);
                    $img = $site_shell_url.'files/products/'.$produto_id.'_1.'.end($ext);
                    //Vamos verificar se a imagem já existe do nosso lado
                    if(!stream_resolve_include_path($img))
                    {
                        echo "U5";
                        file_put_contents($img,file_get_contents($url));
                        $res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                        $res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=1');
                        $res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
                    }
                    else
                    {
                        $sql='SELECT ID_ORG FROM products_images WHERE ID_ORG="'.$produto_id.'"';
                        $rs4->db_open($db_link,$DB_name,$sql);
                        if($rs4->EOF())
                        {
                            //Por algum motivo gravou a imagem mas não gravou em base de dados
                            $res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
                        }
                        $rs4->db_close();
                    }
                }
            }

            $filtros_rel2='';
            if(isset($prod['Caracteristicas']) && !empty($prod['Caracteristicas']))
            {
                //echo "a3";
//              if($t <= 8863) print_r($prod['Caracteristicas']);

                if(is_array($prod['Caracteristicas']) && !empty($prod['Caracteristicas']))
                {
                    $c=0;
                    foreach($prod['Caracteristicas'] as $filtros)
                    {
                        //if($t <= 8863) print_r($filtros);

                        $IDCategoria=$filtros['IDCategoria'];
                        $Categoria=$filtros['Categoria'];
                        $IDValor=$filtros['IDValor'];
                        $Valor=$filtros['Valor'];
                        //if($t <= 8863)echo'*';
                        $filters_data=filtros_atualiza($produto_id,$IDCategoria,$Categoria,$IDValor,$Valor);
                        //if($t <= 8863)echo'+';
                        $filtros_rel2.=$filters_data['category_id'].'_'.$filters_data['value_id'].';';
                        //if($t <= 8863)echo'-';
                        //if($t <= 8863)echo $ID_ORG;
                        $fams=niveis_fams_f($ID_ORG);
                        //if($t <= 8863)echo'&';
                        filtros_produtos_atualiza($filters_data['category_id'],$filters_data['value_id'],$produto_id,$fams[0],$fams[1],$marca_id,$filtros_rel2);
                        //if($t <= 8863)echo'%';
                    }
                }
            }

            $res=Runquery($db_link,$DB_name,'UPDATE filters_products SET filtros_rel2="'.$filtros_rel2.'" WHERE prod_id="'.$produto_id.'"');
            if($log) echo ($cont+$cont2).': Update Produto: '.$prod['Referencia'].'<br>';
            $cont++;
            // flush();
        }
        else
        {
            //NÃO EXISTE, VAMOS INSERIR O PRODUTO
            if($familia['ID'] > 0)
            {
                $rs2->db_open($db_link,$DB_name,'SELECT * FROM products_families WHERE ID_Integracao="'.$familia['ID'].'" ');
                if(!$rs2->EOF())
                {
                    //update familia
                    $fam_id=$rs2->db_data['ID'];
                    $preco_revenoff_fam=$rs2->db_data['preco_revenoff'];
                    $sql='UPDATE products_families_lang SET Cabecalho="'.$familia['Valor'].'" WHERE ID_PAI="'.$fam_id .'" and Lang="pt"';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                else
                {
                    //insert familia
                    $sql='INSERT INTO products_families (visivel,ID_integracao) VALUES (1,"'.$familia['ID'].'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $fam_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'","'.$familia['Valor'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                $fams_gen[]=$fam_id;
            }
            if($subfamilia['ID'] > 0)
            {
                $sql='SELECT * FROM products_families WHERE ID_Integracao="'.$subfamilia['ID'].'"';
                $rs3->db_open($db_link,$DB_name,$sql);
                if(!$rs3->EOF())
                {
                    //UPDATE SUB FAM
                    $sub_fam_id=$rs3->db_data['ID'];
                    $preco_revenoff_fam=$rs3->db_data['preco_revenoff'];
                    $sql='UPDATE products_families_lang SET Cabecalho="'.$subfamilia['Valor'].'" WHERE ID_PAI="'.$sub_fam_id .'" and Lang="pt"';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                else
                {
                    //INSERT SUB FAM
                    $sql='INSERT INTO products_families (visivel,ID_ORG,ID_integracao) VALUES (1,"'.$fam_id.'","'.$subfamilia['ID'].'")';
                    $res=Runquery($db_link,$DB_name,$sql);
                    $sub_fam_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$sub_fam_id.'","'.$subfamilia['Valor'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                $rs3->db_close();
                $fams_gen[]=$sub_fam_id;
            }
            $rs2->db_close();
            if($marca['ID'] > 0)
            {
                $rs2->db_open($db_link,$DB_name,'SELECT * FROM brands WHERE ID_Integracao="'.$marca['ID'].'"');
                if(!$rs2->EOF())
                {
                    //UPDATE Marca
                    $marca_id=$rs2->db_data['ID'];
                    $preco_revenoff_marca=$rs2->db_data['preco_revenoff'];
                    $res=Runquery($db_link,$DB_name,'UPDATE brands_lang SET Nome="'.$marca['Valor'].'" WHERE ID_PAI="'.$marca_id .'" and Lang="pt"');
                }
                else
                {
                    //Insert Marca
                    $res=Runquery($db_link,$DB_name,'INSERT INTO brands (Visivel,ID_integracao) VALUES (1,"'.$marca['ID'].'")');
                    $marca_id=db_get_last_insert_id($db_link);
                    $sql='INSERT INTO brands_lang (ID_PAI,Nome,Lang) VALUES ("'.$marca_id.'","'.$marca['Valor'].'","pt")';
                    $res=Runquery($db_link,$DB_name,$sql);
                }
                $rs2->db_close();
                $marcas_gen[]=$marca_id;
            }
            if($sub_fam_id == '') $sub_fam_id=$fam_id;
            if($ProdAss!='') $sql='INSERT INTO products (Flag,ID_ORG,Ref,Ref_Ass,ID_Marca,Destaque,Aproved,Aproved_ass,ID_Integracao,Preco1,MargemCli,Preco2,Data_criacao,Data_ultimo_desconto,Desconto_percentagem,Importancia,TXPV,TXPV_Data,Carrinho,Video,CorEE) VALUES (1,"'.$sub_fam_id.'","'.$prod['Referencia'].'","'.$ProdAss.'","'.$marca_id.'",1,0,1,"'.$prod['ID'].'","'.$prod['Preco'].'","'.aplicarMargem_ret($prod['Preco']).'","'.ValorPagarMargens($prod['Preco']).'","'.date("Y-m-d").'","0000-00-00",0,0,0,"0000-00-00",1,"'.$video.'","'.$corEE.'")';
            else $sql='INSERT INTO products (Flag,ID_ORG,Ref,ID_Marca,Destaque,Aproved,Aproved_ass,ID_Integracao,Preco1,MargemCli,Preco2,Data_criacao,Data_ultimo_desconto,Desconto_percentagem,Importancia,TXPV,TXPV_Data,Carrinho,Video,CorEE) VALUES (1,"'.$sub_fam_id.'","'.$prod['Referencia'].'","'.$marca_id.'",1,1,0,"'.$prod['ID'].'","'.$prod['Preco'].'","'.aplicarMargem_ret($prod['Preco']).'","'.ValorPagarMargens($prod['Preco']).'","'.date("Y-m-d").'","0000-00-00",0,0,0,"0000-00-00",1,"'.$video.'","'.$corEE.'")';

            $res=Runquery($db_link,$DB_name,$sql);
            $produto_id=db_get_last_insert_id($db_link);

            //Insere familia ao produto
            $res=Runquery($db_link,$DB_name,'INSERT INTO products_families_obj (ID_PROD,ID_ORG) VALUES ("'.$produto_id.'","'.$sub_fam_id.'")');
            $products_name=addslashes(trim($prod['Nome']));

            if(substr($products_name,0,1) === '+'){$outlet=1;$products_name=ltrim($products_name ,'+');}
            $products_description=addslashes(str_replace("|ln|","<br/>",$prod['Descricao']));
            $sql='INSERT INTO products_lang (ID_PAI,Cabecalho,Texto1,Lang) VALUES ("'.$produto_id.'","'.$products_name.'","'.$products_description.'","pt")';
            $res=Runquery($db_link,$DB_name,$sql);

            if($prod['Imagem']!='')
            {
                echo "I6";
                $url=$prod['Imagem'];
                $ext=explode('.',$url);
                $img=$site_shell_url.'files/products/'.$produto_id.'_1.'.end($ext);
                //Vamos verificar se a imagem já existe do nosso lado
                if(!stream_resolve_include_path($img))
                {
                    echo "U6";
                    file_put_contents($img,file_get_contents($url));
                    $res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                    $res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=1');
                    $res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
                }
                else
                {
                    $sql='SELECT ID_ORG FROM products_images WHERE ID_ORG="'.$produto_id.'"';
                    $rs4->db_open($db_link,$DB_name,$sql);
                    if($rs4->EOF())
                    {
                        //Por algum motivo gravou a imagem mas não gravou em base de dados
                        $res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
                    }
                    $rs4->db_close();
                }
            }
            // Etiqueta de Energia
            if($prod['EtiquetaEnergia'] != '')
            {
                echo "I7";
                $url=$prod['EtiquetaEnergia'];
                $ext=explode('.',$url);
                $ee=$site_shell_url.'files/products/EE/'.$produto_id.'_1.'.end($ext);
                if(!is_dir($site_shell_url.'files/products/EE')) mkdir($site_shell_url.'files/products/EE');
                //Vamos verificar se a imagem já existe do nosso lado
                if(!stream_resolve_include_path($ee) && !stream_resolve_include_path($site_shell_url.'files/products/EE/'.$produto_id.'_1.lnk'))
                {
                    echo "U7";
                    file_put_contents($ee,file_get_contents($url));
                    $res=Runquery($db_link,$DB_name,'UPDATE products SET ee_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                }
            }else $res=Runquery($db_link,$DB_name,'UPDATE products SET ee_ext="" WHERE ID="'.$produto_id.'"');
            // Ficha União Europeia
            if($prod['FichaUE'] != '')
            {
                echo "I8";
                $url=$prod['FichaUE'];
                $ext=explode('.',$url);
                $ue=$site_shell_url.'files/products/UE/'.$produto_id.'_1.'.end($ext);
                if(!is_dir($site_shell_url.'files/products/UE')) mkdir($site_shell_url.'files/products/UE');
                //Vamos verificar se a imagem já existe do nosso lado
                if(!stream_resolve_include_path($ue) && !stream_resolve_include_path($site_shell_url.'files/products/UE/'.$produto_id.'_1.lnk'))
                {
                    echo "U8";
                    file_put_contents($ue,file_get_contents($url));
                    $res=Runquery($db_link,$DB_name,'UPDATE products SET ue_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                }
            } else $res=Runquery($db_link,$DB_name,'UPDATE products SET ue_ext="" WHERE ID="'.$produto_id.'"');
            //Desenhos Técnicos
            if($prod['DesenhosTecnicos']!='')
            {
                echo "I9";
                $url=$prod['DesenhosTecnicos'];
                $ext=explode('.',$url);
                $dt=$site_shell_url.'files/products/DT/'.$produto_id.'_1.'.end($ext);
                if(!is_dir($site_shell_url.'files/products/DT')) mkdir($site_shell_url.'files/products/DT');
                //Vamos verificar se a imagem já existe do nosso lado
                if(!stream_resolve_include_path($dt) && !stream_resolve_include_path($site_shell_url.'files/products/DT/'.$produto_id.'_1.lnk'))
                {
                    echo "U9";
                    file_put_contents($dt,file_get_contents($url));
                    $res=Runquery($db_link,$DB_name,'UPDATE products SET dt_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
                }
            }else $res=Runquery($db_link,$DB_name,'UPDATE products SET dt_ext="" WHERE ID="'.$produto_id.'"');


            if($ProdAss=='')
            {
                $obj_pvp_old=0;
                /*$rs4->db_open($db_link,$DB_name,'SELECT obj_pvp FROM products_objs WHERE obj_type_id="'.$produto_id.'"');
                if(!$rs4->EOF())$obj_pvp_old=floatval($rs4->db_data['obj_pvp']);
                $rs4->db_close();*/
                $products_quantity=$prod['Stock'];
                if($preco_revenoff_prod==1) $val_p=floatval(aplicarIVA($prod['Preco_Original']));
                elseif($preco_revenoff_fam==1) $val_p=floatval(aplicarIVA($prod['Preco_Original']));
                elseif($preco_revenoff_marca==1) $val_p=floatval(aplicarIVA($prod['Preco_Original']));
                else $val_p=floatval($prod['Preco_Original']);

                /*$perc_verfica=((($obj_pvp_old - $val_p ) / ($obj_pvp_old)) * 100);

                if($id_revendedor == 16305) // RPCMOVEIS
                {
                    $news_l = 1;
                    $val_promo = 0;
                }
                else
                {
                    if($val_p < $obj_pvp_old && $perc_verfica > 60)
                    {
                        $val_p2=$val_p;
                        $val_p=$obj_pvp_old;
                        $val_promo=0;
                        $email_txt.='<br />Produto: '.$prod['Referencia'].' Preço Antigo: '.$val_p.' | Preço Novo: '.$val_p2.' - <a href="'.$GLOBALS['site_http_url'].'crons/webservicefreeplace_filtros.php?ref_upd='.$prod['Referencia'].'">Atualizar Preço</a>';
                    }
                    elseif($val_p < $obj_pvp_old && $perc_verfica <= 60 && $val_p > 0)
                    {
                        $news_l=1;
                        $val_promo=$val_p;
                        $val_p=$obj_pvp_old;
                        $Data_ultimo_desconto=date('Y-m-d');
                        $Desconto_percentagem=$perc_verfica;
                    }
                    else
                    {
                        $news_l=1;
                    }

                    if($val_promo!=$val_p && $val_promo > 0) $promo=1; else $promo=0;
                    if($val_promo > $val_p)
                    {
                        //Se o valor da promocao for maior do que o valor anterior, temos de anular a promocao
                        $promo = 0;
                        $val_p=$val_promo;
                        $val_promo=0;
                    }
                }*/


                $sql='INSERT INTO products_objs (obj_type_id,obj_type,obj_stock,obj_iva,obj_pvp,obj_pvp2,obj_promo,obj_promo_ativo,obj_ativo,obj_val,obj_destaque,obj_novo,obj_novo2,obj_outlet,obj_qtdmin,obj_ecotaxas) VALUES ("'.$produto_id.'","products","'.$products_quantity.'","'.$iva_default.'","'.$val_p.'","'.$val_p2.'","'.$val_promo.'","'.$promo.'",1,1,"'.$prod['Destaque'].'","'.$prod['Novidade'].'","'.$news_l.'","'.$outlet.'","'.$prod['QtdMinEnc'].'","'.addslashes($prod['Ecotaxas']).'")';
				if($produto_id == 12768) echo $sql;
                $res=Runquery($db_link,$DB_name,$sql);
                $obj_id=db_get_last_insert_id($db_link);
                $sql='INSERT INTO products_objs_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'","'.$prod['Referencia'].'","pt")';
                $res=Runquery($db_link,$DB_name,$sql);
            }

            $rs2->db_open($db_link,$DB_name,'SELECT * FROM products_destaques WHERE ID_Prod="'.$produto_id.'"');
            if(!$rs2->EOF())
            {
                if($prod['Destaque']==0)$res=Runquery($db_link,$DB_name,'DELETE FROM products_destaques WHERE ID_Prod="'.$produto_id.'"');
            }
            else
            {
                if($prod['Destaque']==1)$res=Runquery($db_link,$DB_name,'INSERT INTO products_destaques (ID_Prod,Ordem) VALUES ("'.$produto_id.'",0)');
            }
            $rs2->db_close();
            $Sugestoes=$prod['Sugestoes'];
            if(is_array($Sugestoes) && !empty($Sugestoes))
            {
                for ($i=0; $i < count($Sugestoes); $i++)
                {
                    $rs2->db_open($db_link,$DB_name,'SELECT * FROM products WHERE ID_Integracao="'.$Sugestoes[$i].'"');
                    if(!$rs2->EOF())
                    {
                        $prod_id2=$rs2->db_data['ID'];
                        $rs3->db_open($db_link,$DB_name,'SELECT * FROM produtos_sugestoes WHERE ID_Prod="'.$produto_id.'" AND ID_Prod2="'.$prod_id2.'"');
                        if($rs3->EOF())
                        {
                            $res=Runquery($db_link,$DB_name,'INSERT INTO produtos_sugestoes (ID_Prod,ID_Prod2) VALUES ("'.$produto_id.'","'.$prod_id2.'")');
                        }
                        $rs3->db_close();
                    }
                }
            }
            $filtros_rel2='';
            if(isset($prod['Caracteristicas']) && !empty($prod['Caracteristicas']))
            {
                foreach($prod['Caracteristicas'] as $filtros)
                {
                    $IDCategoria=$filtros['IDCategoria'];
                    $Categoria=$filtros['Categoria'];
                    $IDValor=$filtros['IDValor'];
                    $Valor=$filtros['Valor'];
                    $filters_data=filtros_atualiza($produto_id,$IDCategoria,$Categoria,$IDValor,$Valor);
                    $filtros_rel2.=$filters_data['category_id'].'_'.$filters_data['value_id'].';';
                    $fams=niveis_fams_f($sub_fam_id);
                    filtros_produtos_atualiza($filters_data['category_id'],$filters_data['value_id'],$produto_id,$fams[0],$fams[1],$marca_id,$filtros_rel2);
                }
            }
            $res=Runquery($db_link,$DB_name,'UPDATE filters_products SET filtros_rel2="'.$filtros_rel2.'" WHERE prod_id="'.$produto_id.'"');
            $cont2++;
            if($log) echo ($cont+$cont2).': Inseriu Produto: '.$prod['Referencia'].'<br>';
        }
        $rs->db_close();

        if($t != 8863 && $produto_id > 0 && $sub_fam_id > 0 && $marca_id > 0) gera_produtos($produto_id,$sub_fam_id,$marca_id,1);
    }

    // SÓ NO FIM GERAMOS OS FILTROS para marcas / familias
    gera_produtos_filtros($fams_gen,$marcas_gen);
    $res=Runquery($db_link,$DB_name,'UPDATE filters_categories SET category_active=0 WHERE category_flag=0');
    $res=Runquery($db_link,$DB_name,'UPDATE filters_values SET value_active=0 WHERE value_flag=0');
    $res=Runquery($db_link,$DB_name,'UPDATE filters_products SET filtro_active=0 WHERE FLAG=0');

    // ESCONDE PRODUTOS QUE NÃO ESTEJAM NESTE FICHEIRO
    $sql_esconde='UPDATE products SET Aproved=1 WHERE Flag=1 and Aproved_ass=0 AND ID_Integracao > 0 AND Sync = 0';
    $res=Runquery($db_link,$DB_name,$sql_esconde);

    $sql_esconde='UPDATE products SET Aproved=0 WHERE Flag=0 AND ID_Integracao > 0 AND Sync = 0';
    $res=Runquery($db_link,$DB_name,$sql_esconde);

    $sql_esconde='DELETE FROM filters_products WHERE filtro_active=0 AND data_atualizacao != "0000-00-00 00:00:00"';
    $res=Runquery($db_link,$DB_name,$sql_esconde);


    // SCRIPT QUE REMOVE FICHEIROS QUE NÃO SE ENCONTRAM ATIVOS
    $total_p = 0; $del = 0;
    foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p*.php") as $filename)
    {
        $total_p++;
        $fl = explode('_p',$filename);
        $prod_id = str_replace('.php', '', $fl[1]);

        $sql = 'SELECT ID FROM products WHERE ID="'.$prod_id.'" AND Aproved=1';
        $rs->db_open($db_link, $DB_name, $sql);
        if($rs->EOF())
        {
            @unlink($filename); $del++;
        }
        $rs->db_close();
    }

    echo 'Total Prods: ' . $total_p.'<br />';
    echo 'Removeu: ' . $del.'<br />';


    echo 'Fez '.$cont2.' INSERTS e '.$cont.' UPDATES Total de '.($cont+$cont2).'<br>';

    // VAMOS LIMPAR TODOS OS FICHEIRO NA PASTA IMPORTADO
    foreach (glob($GLOBALS['site_shell_url']."files/importado/*.txt") as $filename_imp)
    {
        @unlink($filename_imp);
    }
	foreach (glob($GLOBALS['site_shell_url']."files/kk*.txt") as $filename)
	{
		@unlink($filename);
	}
	foreach (glob($GLOBALS['site_shell_url']."files/json_*.txt") as $filename)
	{
		@unlink($filename);
	}

    $sql='UPDATE z_integracao_api SET DATA_FIM="'.date('Y-m-d H:i:s').'",IMPORTADO=1 WHERE FICHEIRO="'.$filename.'"';
    $res=Runquery($db_link,$DB_name,$sql);
    if(@copy($filename,$filename_importado)) @unlink($filename);

    //ELIMINAR CACHE DOS DESTAQUES
    /*exec("php ".$GLOBALS['site_shell_url']."crons/gera_homepage.php > /dev/null"); //Atualiza TXPV
    if(stream_resolve_include_path($GLOBALS['site_shell_url'].'files/produtosdestaque_PT.php')) @unlink($GLOBALS['site_shell_url'].'files/produtosdestaque_PT.php');*/

    //Correr os crons:
    /*

     php /home/www/netkuatro.com/public_html/kuantokusta.php

    */

    /*
    if($email_txt)
    {
        $email_txt2='SITE ATUALIZADO: '.$site_title;
        $email_txt2.=' - <a href="'.$site_http_url.'">'.$site_http_url.'</a><br /><br />Vamos trocar todos os preços pelos novos caso estes sejam inferiores ao preço existente,menos que sejam 0: <a href="'.$GLOBALS['site_http_url'].'crons/webservicefreeplace_filtros.php?filtr_upd=2">Atualizar Todos os Preços</a>';
        $email_txt2.=' - <br /><br />Caso tenha atualizado os produtos 1 a 1,no fim de atualizar os valores clique no seguinte url para atualizar a cache: <a href="'.$GLOBALS['site_http_url'].'crons/webservicefreeplace_filtros.php?filtr_upd=1">Atualizar Cache</a>';
        $email_txt2.='<br /><br />Os seguintes produtos tem preços inferiores:<br />'.$email_txt;
        @send_email('ricardomaia@memogadget.com','Existem produtos que tem preços inferiores',$email_txt2);
        @send_email('aderito@memogadget.com','Existem produtos que tem preços inferiores',$email_txt2);
    }
    */
}
function niveis_fams_f($id_org=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;$pai=$id_org;
    while($pai > 0)
    {
        $sql='SELECT ID,ID_ORG FROM products_families WHERE ID="'.$pai.'"';
        $rs->db_open($db_link,$DB_name,$sql);
        $pai=$rs->db_data['ID_ORG'];
        $pai2=$rs->db_data['ID'];
        if(array($nivel) && in_array($pai,$nivel))
        {
            $pai=0;
            echo "ERRO: A familia $pai2 está filha dela propria<br>";
        }
        else
        {
            $nivel[]=$pai2;
        }
        //if($id_org == 16) print_r($nivel);
    }
    for($a=(count($nivel)-1); $a>=0; $a--)
    {
        $nivel2[]=$nivel[$a];
    }
    return $nivel2;
}
function aplicarIVA($preco_revendedor)
{
    //global $iva_default;
    //$iva=1+(floatval($iva_default)/100);
    //$preco_revendedor /= $iva; //PRECO S/IVA
    return $preco_revendedor;
}
function aplica_margens_valor($valor=0)
{
    // +7% nos produtos de valor entre 198.01€ e 298€
    // +6% nos produtos entre 298.01€ e 498 €
    // +4% entre 498.01€ e 998€
    // +5%  de 998.01€ a 1498€
    // +4% e 3% acima de qualquer  produto  de valor superior a 1498.01€.

    if($valor >= 1498.01){$valor *= 1.03;}
    elseif($valor >= 998.01 && $valor < 1498) $valor *= 1.05;
    elseif($valor >= 498.01 && $valor < 998) $valor *= 1.04;
    elseif($valor >= 298.01 && $valor < 498) $valor *= 1.06;
    elseif($valor >= 198.01 && $valor < 298) $valor *= 1.07;
    else $valor *= 1.08;

    return $valor;
}
function aplicarMargem($preco_revendedor)
{
    global $iva_default;
    if($preco_revendedor >= 1000)
    {
        $preco_revendedor *= 1.005;
    }
    else
    {
        $preco_revendedor *= 1.01;
    }

    $iva=1+(floatval($iva_default)/100);
    // $preco_revendedor *= 1.02; //Margem cliente
    $preco_revendedor *= $iva; //PRECO C/IVA
    return $preco_revendedor;
}

// Margem da memogadget
function aplicarMargem_ret($preco_revendedor)
{
    if($preco_revendedor >= 1000)
    {
        $preco_revendedor = 1.005;
    }
    else
    {
        $preco_revendedor = 1.01;
    }
    return $preco_revendedor;
}
function ValorPagarMargens($valor)
{
    // 1 - valor + margem memogadget------------------
    $valor_memo=$valor * aplicarMargem_ret($valor);
    // 2 - valor + margem cliente + margem memogadget------------------
    // MARGEM CLIENTE
    // $valor *= 1.02;
    // MARGEM MEMOGADGET
    $valor *= aplicarMargem_ret($valor);
    // valor a pagar
    if($valor==$valor_memo) $valor_pagar=($valor_memo * (aplicarMargem_ret($valor)-1));
    else $valor_pagar=($valor - $valor_memo);
    return $valor_pagar;
}
?>