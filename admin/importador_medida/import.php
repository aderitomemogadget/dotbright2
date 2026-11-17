<?php
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');
    require_once '../products/filtros/funcoes.inc.php';

    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(0);


    set_time_limit(180); // Time limit in seconds
    ini_set("max_execution_time", 180);
    ini_set('memory_limit', '-1'); 



?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de filtros de produtos (preços)</h3></td></tr>
            </table>
            <form name="form1" method="post" action="import_trata.php" enctype="multipart/form-data">
                <table width="100%" border="0" cellpadding="5" cellspacing="0" style="background-color:#FFF">
                <tr>
                    <td align="left" width="120" style="padding:10px"><input type="file" name="import_file"></td>
                    <td align="left" valign="top" style="padding:10px"><input type="submit" class="botao" value="ENVIAR FICHEIRO XLSX" /></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:0 10px;font-size: 16px;color: blue" align="left">

                            <?php
                            $ficheiro=$_GET['f'];
                            $ficheiro=str_replace('.xlsx','',$ficheiro);
                            $ficheiro=$site_shell_url.'files/tmp/'.$ficheiro.'.xlsx';
                            if($op==1)
                            {
                                require_once __DIR__.'/simplexlsx-master/src/SimpleXLSX.php';

                                $rs = new DataObj;
                                $rs2 = new DataObj;
                                $rs3 = new DataObj;
                                $rs4 = new DataObj;
                                global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url,$site_http_url;
                                $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

                                $sql = 'DELETE FROM products_families_lang where Cabecalho=""';
                                $res=Runquery($db_link,$DB_name,$sql);

                                //----------------------------------------------------------------------
                                $sql="SELECT ID FROM products_families ORDER BY ID";
                                $rs->db_open($db_link,$DB_name,$sql);
                                while(!$rs->EOF())
                                {
                                    $IDb = $rs->db_data['ID'];
                                    if($IDb>0)
                                    {
                                        $sql="SELECT ID FROM products_families_lang WHERE ID_PAI = $IDb";
                                        $rs2->db_open($db_link,$DB_name,$sql);
                                        if(!$rs2->EOF())
                                        {
                                            //existe
                                        }
                                        else
                                        {
                                            $sql = "DELETE FROM products_families where ID = $IDb";
                                            $res=Runquery($db_link,$DB_name,$sql);
                                        }
                                        $rs2->db_close();
                                    }
                                    $rs->db_next();
                                }
                                $rs->db_close();

                                //----------------------------------------------------------------------

                                if ($xlsx = Shuchkin\SimpleXLSX::parse($ficheiro))
                                {
                                    $sheet=0;
                                    while($sheet<1)
                                    {
                                        foreach( $xlsx->rows($sheet) as $linha  )
                                        {
                                            echo "<br>";

                                            $Ref='';
                                            $Designacao='';
                                            $PVP='';
                                            $STOCK='';
                                            $Familia='';
                                            $SubFamiliaNome='';
                                            $IMG='';
                                            $FamiliaCod='';
                                            $SubFamiliaCod='';
                                            $Medida='';
                                            $Unidade='';
                                            $PVPPromo='';
                                            $PromoData='';
                                            $Marca='precosbaixos';

                                            $Ref                     = $linha[0];
                                            $Designacao              = $linha[1];
                                            $PVP                     = $linha[10];
                                            $IVA                     = $linha[11];
                                            $STOCK                   = $linha[12];
                                            $PVPPromo                = $linha[13];
                                            $PromoData               = $linha[14];
                                            $PromoDataFim            = $linha[15];
                                            $IMG                     = $linha[16];

                                            $Familia                 = $linha[2];
                                            $FamiliaCod              = $linha[3];
                                            $SubFamiliaNome          = $linha[5];

                                            //$SubFamiliaCod           = $linha[9];
                                            //$Medida                  = $linha[10];
                                            //$Unidade                 = $linha[11];

                                            if(!$PVP)$PVP=0;
                                            if(!$STOCK)$STOCK=1;

                                            $Texto_longo='';

                                            if($Peso>0)$Texto_longo.='<br><b>Peso:</b> '.$Peso;
                                            if($Comprimento>0)$Texto_longo.='<br><b>Comprimento:</b> '.$Comprimento;
                                            if($Largura>0)$Texto_longo.='<br><b>Largura:</b> '.$Largura;
                                            if($Altura>0)$Texto_longo.='<br><b>Altura:</b> '.$Altura;

                                            $Texto_longo=addslashes($Texto_longo);

                                            //echo "$FamiliaCod|";

                                            //Vamos obter o ID da familia
                                            if($FamiliaCod!='')
                                            {
                                                $sql = "SELECT ID FROM products_families WHERE Codigo_importacao = '$FamiliaCod'";
                                                //echo $sql."<br>";
                                                $rs->db_open($db_link,$DB_name,$sql);
                                                if(!$rs->EOF())$ID_FAMILIA = $rs->db_data['ID'];
                                                $rs->db_close();
                                            }
                                            if($ID_FAMILIA)
                                            {
                                                if($Familia == $SubFamiliaNome)
                                                {
                                                    //E para gravar os artigos na familia
                                                    $ID_SUBFAMILIA = $ID_FAMILIA;
                                                }
                                                else
                                                {
                                                    if($SubFamiliaNome!='')
                                                    {
                                                        $sql = "SELECT ID_PAI FROM products_families INNER JOIN products_families_lang ON products_families_lang.ID_PAI=products_families.ID WHERE ID_ORG='$ID_FAMILIA' AND Cabecalho = '$SubFamiliaNome'";
                                                        $rs->db_open($db_link,$DB_name,$sql);
                                                        if(!$rs->EOF())
                                                        {
                                                            $ID_SUBFAMILIA = $rs->db_data['ID_PAI'];
                                                            $sql="UPDATE products_families SET visivel='1' WHERE ID='$ID_SUBFAMILIA'";
                                                            $res=Runquery($db_link,$DB_name,$sql);

                                                            //Ja existe, mas pode não ter ficado bem grava
                                                            $sql = "SELECT ID FROM products_families_lang WHERE Lang='pt' AND ID_PAI=$ID_SUBFAMILIA";
                                                            $rs2->db_open($db_link,$DB_name,$sql);
                                                            if(!$rs2->EOF())
                                                            {
                                                                //Existe
                                                            }
                                                            else
                                                            {
                                                                $sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) values("'.$ID_SUBFAMILIA.'","'.$SubFamiliaNome.'","pt")';
                                                                //echo $sql."<br>";
                                                                $res=Runquery($db_link,$DB_name,$sql);
                                                            }
                                                            $rs2->db_close();

                                                        }
                                                        else
                                                        {
                                                            //Nao existe 
                                                            $sql = 'INSERT INTO products_families (ID_ORG,visivel) values("'.$ID_FAMILIA.'","1")';
                                                            $res=Runquery($db_link,$DB_name,$sql);
                                                            $ID_SUBFAMILIA=db_get_last_insert_id($db_link);
                                                            $sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) values("'.$ID_SUBFAMILIA.'","'.$SubFamiliaNome.'","pt")';
                                                            $res=Runquery($db_link,$DB_name,$sql);
                                                        }
                                                        $rs->db_close();
                                                    }
                                                }
                                            }

                                            if($ID_SUBFAMILIA)
                                            {
                                                $ID_ORG=$ID_SUBFAMILIA;

                                                //----------------------- AGORA PRODUTOS
                                                $PVP=str_replace(',','.',$PVP);
                                                //$PVP=$PVP/1.23;

                                                //Limpar as referencias a primeira vez
                                                echo $Ref;
                                                $sql = "SELECT ID,Aproved FROM products WHERE Ref = '$Ref'";
                                                $rs->db_open($db_link,$DB_name,$sql);
                                                if(!$rs->EOF())
                                                {
                                                    echo "> Encontrou ";
                                                    $sql = "SELECT * FROM brands_lang WHERE Nome = '$Marca' and Lang='pt'";
                                                    $rs2->db_open($db_link,$DB_name,$sql);
                                                    if(!$rs2->EOF())
                                                    {
                                                        $ID_Marca = $rs2->db_data['ID_PAI'];
                                                    }
                                                    else
                                                    {
                                                        $sql = "INSERT INTO brands (Visivel) values ('1')";
                                                        $res=Runquery($db_link,$DB_name,$sql);
                                                        $ID_Marca=db_get_last_insert_id($db_link);
                                                        $sql = "INSERT INTO brands_lang (ID_PAI,Nome,Lang) values ('$ID_Marca','$Marca','pt')";
                                                        $res=Runquery($db_link,$DB_name,$sql);
                                                    }
                                                    $rs2->db_close();


                                                    $prod_id = $rs->db_data['ID'];
                                                    $aproved = $rs->db_data['Aproved'];
                                                    $sql="UPDATE products SET ID_Marca='$ID_Marca' WHERE ID='$prod_id'";
                                                    $res=Runquery($db_link,$DB_name,$sql);
                                                    $sql="UPDATE products_objs SET obj_stock='$STOCK',obj_pvp='$PVP',obj_iva='$IVA' WHERE obj_type_id='$prod_id'";
                                                    $res=Runquery($db_link,$DB_name,$sql);
                                                    $sql = "UPDATE products_lang set Cabecalho='$Designacao',Texto1='$Texto_longo' WHERE ID_PAI = '$prod_id' AND Lang='pt'";
                                                    $res=Runquery($db_link,$DB_name,$sql);
                                                    $sql = "UPDATE products set ID_ORG=$ID_ORG WHERE ID = '$prod_id'";
                                                    $res=Runquery($db_link,$DB_name,$sql);

                                                    //$sql = "UPDATE products_families_obj set ID_ORG=$ID_ORG WHERE ID_Prod = '$prod_id'";
                                                    $sql = "REPLACE INTO products_families_obj (ID_ORG,ID_Prod) values ('$ID_ORG','$prod_id') ";
                                                    $res=Runquery($db_link,$DB_name,$sql);


                                                    //outras tabelas
                                                    $sql = "SELECT * FROM filters_products WHERE prod_id = '$prod_id'";
                                                    $rs2->db_open($db_link,$DB_name,$sql);
                                                    if(!$rs2->EOF())
                                                    {
                                                        //existe
                                                    }
                                                    else
                                                    {
                                                        $res = Runquery($db_link,$DB_name,'INSERT INTO filters_products (prod_id,filtro_active) VALUES ("'.$prod_id.'","1")');                                                        
                                                    }
                                                    $rs2->db_close();


                                                    //$res = Runquery($db_link,$DB_name,'INSERT INTO products_objs_lang (ID_PAI,Lang) VALUES ("'.$bloco_obj_id.'","pt")');






                                                    $Existe++;
                                                }
                                                else
                                                {
                                                    $N_Existe++;
                                                    echo "> Não Encontrou";
                                                    $sql = "SELECT * FROM brands_lang WHERE Nome = '$Marca' and Lang='pt'";
                                                    $rs2->db_open($db_link,$DB_name,$sql);
                                                    if(!$rs2->EOF())
                                                    {
                                                        $ID_Marca = $rs2->db_data['ID_PAI'];
                                                    }
                                                    else
                                                    {
                                                        $sql = "INSERT INTO brands (Visivel) values ('1')";
                                                        $res=Runquery($db_link,$DB_name,$sql);
                                                        $ID_Marca=db_get_last_insert_id($db_link);
                                                        $sql = "INSERT INTO brands_lang (ID_PAI,Nome,Lang) values ('$ID_Marca','$Marca','pt')";
                                                        $res=Runquery($db_link,$DB_name,$sql);
                                                    }
                                                    $rs2->db_close();
                                                    $sql = "INSERT INTO products (ID_ORG,Ref,Aproved,Ref_Ean,ID_Marca,produto_standard,Flag,Carrinho) values ('$ID_ORG','$Ref','1','$EAN','$ID_Marca','1','1','1')";
                                                    $res=Runquery($db_link,$DB_name,$sql);
                                                    $prod_id=db_get_last_insert_id($db_link);
                                                    $sql = "INSERT INTO products_lang (ID_PAI,Cabecalho,Texto1,Lang) values ('$prod_id','$Designacao','$Texto_longo','pt')";
                                                    $res=Runquery($db_link,$DB_name,$sql);

                                                    $sql = "INSERT INTO products_objs (obj_type_id,obj_iva,obj_stock,obj_pvp,obj_iva,obj_promo) values ('$prod_id','23','$STOCK','$PVP','$IVA','0')";
                                                    $res=Runquery($db_link,$DB_name,$sql);
                                                    $bloco_obj_id=db_get_last_insert_id($db_link);

                                                    $sql = "INSERT INTO products_families_obj (ID_PROD,ID_ORG) values ('$prod_id','$ID_ORG')";
                                                    $res=Runquery($db_link,$DB_name,$sql);

                                                    //outras tabelas
                                                    $res = Runquery($db_link,$DB_name,'INSERT INTO filters_products (prod_id,filtro_active) VALUES ("'.$prod_id.'","1")');
                                                    $res = Runquery($db_link,$DB_name,'INSERT INTO products_objs_lang (ID_PAI,Lang) VALUES ("'.$bloco_obj_id.'","pt")');

                                                }
                                                $rs->db_close();
                                            }
                                            //---------------------------------------------


                                            if($prod_id)
                                            {
                                                $sql="SELECT ID_Prod FROM acelerador_pesquisa WHERE ID_Prod='$prod_id'";
                                                $hRes=$rs2->db_open($db_link,$DB_name,$sql);
                                                if(!$rs2->EOF())
                                                {
                                                    $sql = "UPDATE acelerador_pesquisa set Ref='$ref',EAN='$EAN',Texto='$texto',Flag=1,Cabecalho='$Designacao',Preco_Site='$preco_site',fam_id1='$ID_FAMILIA',fam_id2='$ID_SUBFAMILIA',marca_id='$ID_Marca',Filtros='' WHERE ID_Prod=".$prod_id;
                                                    echo "U ";
                                                }
                                                else
                                                {
                                                    $sql = "INSERT INTO acelerador_pesquisa (Ref,EAN,Texto,ID_Prod,Flag,Cabecalho,Preco_Site,fam_id1, fam_id2, marca_id,Filtros) values ('$ref','$EAN','$texto','$prod_id',1,'$Designacao','$preco_site','$ID_FAMILIA','$ID_SUBFAMILIA','$ID_Marca','')";
                                                    echo "I ";
                                                }
                                                $rs2->db_close();
                                            }

                                            //Agora as imagens                                            
                                            if($prod_id >0 && $IMG!='')
                                            {
                                                $z=1;
                                                $file = $prod_id.'_'.$z.'.jpg';
                                                $url='';

                                                $current = file_get_contents($IMG); // read the created file


                                                if(file_put_contents($GLOBALS['site_shell_url'].'files/importar/imagens/'.$file,$current))
                                                {
                                                    if(file_exists($GLOBALS['site_shell_url'].'files/importar/imagens/'.trim($file)))
                                                    {
                                                        $url = $GLOBALS['site_shell_url'].'files/importar/imagens/'.trim($file);
                                                    }
                                                }
                                                else echo ' ERROR SAVING IMAGE '.$GLOBALS['site_shell_url'].'files/importar/imagens/'.$file;

                                                
                                                if($url != '' && file_exists($url))
                                                {
                                                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                                                    $url_new = $GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_'.$z.'.'.$ext;
                                                    if($ext != '' && !file_exists($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_'.$z.'.'.$ext))
                                                    {
                                                        copia_segura($url,$url_new);
                                                        $res = Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$prod_id.'", "'.$ext.'", '.$z.', "")');
                                                    }
                                                }
                                            }

                                            // https://codebeautify.org/base64-to-image-converter
                                        }
                                        $sheet++;
                                    }

                                } else {
                                    echo Shuchkin\SimpleXLSX::parseError();
                                }

                                

                                echo "Existem = $Existe \n";
                                echo "Não Existem = $N_Existe \n";
                                echo "\n\n";
                                echo "## IMPORTAÇÃO TERMINADA ##\n";

                                /*foreach (glob("/home/motormaq/public_html/files/filtros/*.*") as $filename)
                                {
                                    unlink($filename);
                                }*/


                            }

                            if($op==2)
                            {
                                unlink($ficheiro);
                            }

                            ?>
                            <table border="1" cellspacing="0" cellpadding="5" style="border-color:#ccc;margin-top:50px">
                            <tr>
                                <td align="left" style="font-weight:bold; background-color: #FFF; color: black; font-size: 16px"><?php
                                // FICHEIROS CSV QUE SERÃO PARA IMPORTAR
                                foreach (glob($GLOBALS['site_shell_url']."files/tmp/*.xlsx") as $filename)
                                {
                                    $filename=str_replace($GLOBALS['site_shell_url']."files/tmp/",'',$filename);
                                    $arr_loks[] = $filename;
                                    echo '<a style="font-size:16px" href="'.$GLOBALS['site_http_url'].'files/tmp/'.$filename.'" target="_new">'.$filename.'</a><br><br>';
                                    echo '<a style="font-size:16px" href="import.php?op=1&f='.$filename.'"> » Atualizar site </a><br><br>';
                                    echo '<a style="font-size:16px" href="import.php?op=2&f='.$filename.'"> » Remover ficheiro </a><br><br>';
                                    echo '<br>';
                                }
                                ?>
                                </td>
                            </tr>
                            </table>

                            <hr/>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>

<?php require_once('../layout/bottom.inc.php');

?>