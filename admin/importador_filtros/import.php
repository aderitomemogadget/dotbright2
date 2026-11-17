<?php
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');
    require_once '../products/filtros/funcoes.inc.php';

    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(0);


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
                    <td colspan="2" style="padding:0 10px;font-size: 16px;color: blue" align="left">

                        <table style="background-color:#FFF" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody><tr><td colspan="2" height="25" align="center"><b>ESTRUTURA DO FICHEIRO</b></td></tr>
                    <tr>
                        <td colspan="2" style="padding:0 10px;" align="center">
                            <table width="100%" cellspacing="0" cellpadding="5" border="1">
                                <tbody><tr>
                                    <td style="font-weight: bold; padding: 5px;" align="center">REFERÊNCIA</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">DESIGNAÇÃO</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">STOCK</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">QTD MIN</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">PVP</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">DIAS ENTREGA</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">FILTRO 1</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">FILTRO 2</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">FILTRO ...</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">x...x</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center">Obrigatório</td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center">Obrigatório</td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center">Obrigatório</td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center"></td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center"></td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center"></td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center"></td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center"></td>
                                    <td style="font-weight: bold; color: rgb(255, 0, 0); padding: 5px;" align="center"></td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr><td colspan="2" height="25" align="center"><br><b>Exemplo de Ficheiro<br>(Cabeçalho + Linhas)</b><br><br></td></tr>
                    <tr>
                        <td colspan="2" style="padding:0 10px;" align="center">

                            <table width="100%" cellspacing="0" cellpadding="5" border="1">
                                <tbody><tr>
                                    <td style="font-weight: bold; padding: 5px;" align="center">REFERÊNCIA</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">DESIGNAÇÃO</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">STOCK</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">QTD MIN</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">PVP</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">DIAS ENTREGA</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">FILTRO 1</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">FILTRO 2</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">FILTRO ...</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 5px;" align="center">SP028</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">Sponser Carbo Loader</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">86</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">1</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">22.3</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">3</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">1200 g</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">Limão Laranja</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; padding: 5px;" align="center">SP028</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">Sponser Carbo Loader</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">86</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">1</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">22.3</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">3</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">1200 g</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center">Limão Laranja</td>
                                    <td style="font-weight: bold; padding: 5px;" align="center"></td>
                                </tr>
                            </tbody></table>



                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px" width="120" align="left"><input type="file" name="import_file"></td>
                        <td style="padding:10px" valign="top" align="left"><input type="submit" class="botao" value="Importar XLSX"></td>
                    </tr>
                </tbody></table>

                            <?php
                            $ficheiro=$_GET['f'];
                            $ficheiro=str_replace('.xlsx','',$ficheiro);
                            $ficheiro=$site_shell_url.'files/tmp/'.$ficheiro.'.xlsx';
                            if($op==1)
                            {
                                require('php-excel-reader/excel_reader2.php');
                                require('SpreadsheetReader.php');
                                $Reader = new SpreadsheetReader($ficheiro);
                                $Sheets = $Reader -> Sheets();
                                // Vou buscar sempre a primeira folha
                                $Reader -> ChangeSheet(0);

                                global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url,$site_http_url;
                                $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                                $rs = new DataObj; $rs2 = new DataObj; $rs3 = new DataObj; $rs4 = new DataObj;
                                echo "## IMPORTAÇÃO INICIADA ##\n";
                                echo "\n\n";
                                $i=0;
                                $Existe=0;
                                $N_Existe=0;
                                $variante_id = 0;
                                $variante_nome = '';
                                $ID_ORG = 0;
                                $ID_Marca = 0;
                                $last_ref='';

                                $sql = "UPDATE products set Flag=0";
                                $res=Runquery($db_link,$DB_name,$sql);


                                foreach ($Reader as $Row => $linha)
                                {
                                    echo "<hr>";
                                    if($i==0)
                                    {
                                        $j=0;
                                        $pos=6;
                                        $fim=0;
                                        $filtro_cabecalhos=array();
                                        while(!$fim)
                                        {
                                            if(trim($linha[$pos]) != '')
                                            {
                                                $filtro_cabecalhos[$j]=trim($linha[$pos]);
                                                $pos++;
                                                $j++;
                                            }
                                            else $fim=1;
                                        }
                                    }
                                    if($i>0)
                                    {
                                        $Ref                     = $linha[0];
                                        $Designacao              = $linha[1];
                                        $Qtd                     = $linha[2];
                                        $PVP                     = $linha[3];
                                        $PVP2                    = $linha[4];
                                        $Dias                    = $linha[5];

                                        //Limpar as referencias a primeira vez
                                        $sql = "SELECT ID,Aproved FROM products WHERE Ref = '$Ref'";
                                        $rs->db_open($db_link,$DB_name,$sql);
                                        if(!$rs->EOF())
                                        {
                                        	echo "> Encontrou ";
                                            $prod_id = $rs->db_data['ID'];
                                            $aproved = $rs->db_data['Aproved'];
                                            $Existe++;


                                            $sql = "UPDATE products set Flag=1 WHERE ID = $prod_id";
                                            $res=Runquery($db_link,$DB_name,$sql);

                                        }
                                        else echo "> Não Encontrou";
                                        $rs->db_close();


                                        if($Ref != $last_ref)
                                        {
                                        	$last_ref=$Ref;
                                        	$sql = "DELETE FROM filters_products WHERE prod_id = $prod_id";
                                            $res=Runquery($db_link,$DB_name,$sql);
                                        }
                                        //
                                        //Depois começam o filtros
                                        $fim=0;
                                        $pos=6;
                                        $j=0;
                                        $filtro=array();
                                        while(!$fim)
                                        {
                                            /*if(trim($linha[$pos]) != '')
                                            {*/
                                                $filtro[$j]=trim($linha[$pos]);
                                                $pos++;
                                                $j++;
                                            /*}
                                            else 
                                            {
                                                $fim=1;
                                            }*/

                                            if($pos>9)$fim=1;


                                        }
                                        echo "Ref: $Ref <br>";
                                        if($prod_id>0)
                                        {
                                            atualiza_artigo_filtro($prod_id,$Qtd,$Qtd2,$PVP,$PVP2,$Dias,$filtro_cabecalhos,$filtro);
                                        }
                                    }
                                    $i++;
                                }
                                echo "Existem = $Existe \n";
                                echo "Não Existem = $N_Existe \n";
                                echo "\n\n";
                                echo "## IMPORTAÇÃO TERMINADA ##\n";

                                //$sql = "DELETE FROM products WHERE Flag=0";
                                //$res=Runquery($db_link,$DB_name,$sql);
                            }

                            if($op==2)
                            {
                                unlink($ficheiro);
                            }

                            ?>
                            <table border="1" cellspacing="0" cellpadding="5" style="border-color:#ccc;margin-top:50px">
                            <tr>
                                <td align="left" style="font-weight:bold; background-color: #FFF; color: black; font-size: 16px"><?php
                                // FICHEIROS XLSX QUE SERÃO PARA IMPORTAR
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
function get_category_id($nome,$lingua='')
{
    global $site_http_url,$DB_host,$DB_user,$DB_pass,$DB_name;

    $sql = 'SELECT filters_categories.category_id,category_name FROM filters_categories INNER JOIN filters_categories_lang ON filters_categories_lang.category_id=filters_categories.category_id
    WHERE category_lang="'.$lingua.'" AND category_name = "'.$nome.'"';
    $rs=new DataObj;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $category_id=$rs->db_data["category_id"];
    }
    $rs->db_close();
    return $category_id;
}

function get_filtro_id($filtro_nome,$cat_id,$criar=0,$versao_en='',$ordem=0)
{
    global $site_http_url,$DB_host,$DB_user,$DB_pass,$DB_name;
    $sql = 'SELECT filters_values.value_id,filters_values.value_id_import_ref FROM filters_values INNER JOIN filters_values_lang ON filters_values_lang.values_id=filters_values.value_id WHERE category_id="'.$cat_id.'" AND values_name = "'.$filtro_nome.'"';
    $rs=new DataObj;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $filtro_id=$rs->db_data["value_id"];
        $value_id_import_ref=$rs->db_data["value_id_import_ref"];
        if($value_id_import_ref=='')
        {
            $sql = "UPDATE filters_values set value_order=$ordem, value_id_import_ref = '$filtro_nome' WHERE value_id='$filtro_id'";
            $res=Runquery($db_link,$DB_name,$sql);
        }

        if($versao_en != '')
        {
            $sql = 'SELECT * FROM filters_values_lang WHERE values_id="'.$filtro_id.'" AND values_lang="en"';
            $rs2=new DataObj;
            $rs2->db_open($db_link,$DB_name,$sql);
            if(!$rs2->EOF())
            {
                //Ja existe
                if($criar==1)
                {
                    $sql = "UPDATE filters_values_lang set value_id_import_ref = '$versao_en' WHERE values_id='$filtro_id' AND values_lang='en'";
                    $res=Runquery($db_link,$DB_name,$sql);
                }
            }
            else
            {
                $sql = "INSERT INTO filters_values_lang(values_id,values_name,value_id_import_ref,values_lang) values ('$filtro_id','$versao_en','$versao_en','en') ";
                $res=Runquery($db_link,$DB_name,$sql);
            }
        }
    }
    else
    {
        if($criar==1)
        {
            $sql = "INSERT INTO filters_values(value_order,category_id,value_active,data,value_id_import_ref) values ('$ordem','$cat_id','1','".date("Y-m-d H:m:i")."','$filtro_nome') ";
            $res=Runquery($db_link,$DB_name,$sql);
            $filtro_id=db_get_last_insert_id($db_link);
            $sql = "INSERT INTO filters_values_lang(values_id,values_name,value_id_import_ref,values_lang) values ('$filtro_id','$filtro_nome','$filtro_nome','pt') ";
            $res=Runquery($db_link,$DB_name,$sql);
            $sql = "INSERT INTO filters_values_lang(values_id,values_name,value_id_import_ref,values_lang) values ('$filtro_id','$versao_en','$versao_en','en') ";
            $res=Runquery($db_link,$DB_name,$sql);
        }
    }
    $rs->db_close();
    return $filtro_id;
}

function atualiza_artigo_filtro($prod_id,$Qtd,$Qtd2,$PVP,$PVP2,$Dias,$filtro_cabecalhos,$filtros)
{
	global $site_http_url,$DB_host,$DB_user,$DB_pass,$DB_name;
    $rs=new DataObj;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $ID_ORG=0;
    $sql = 'SELECT ID_ORG FROM products_families_obj WHERE ID_PROD = "'.$prod_id.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF()){$ID_ORG=$rs->db_data["ID_ORG"];}
    $rs->db_close();
    if($ID_ORG==0)die("erro");


var_dump($filtro_cabecalhos);
echo '<hr>';
var_dump($filtros);


    //primeiro vamos obter os ids de cada cabecalho
    $i=0;
    $filtro_cabecalhos_ids=array();
    while($i<count($filtro_cabecalhos))
    {
        $cabecalho_id=get_category_id($filtro_cabecalhos[$i],'pt');

        if($cabecalho_id>0)$filtro_cabecalhos_ids[$i]=$cabecalho_id;
        else die("ERRO, Não foi encontrada a categoria '".$filtro_cabecalhos[$i]."', reveja pf. os nomes inseridos no excel na linha 1,");

        $i++;
    }




    $filtro_valores_ids=array();
    $i=0;
    while($i<count($filtros))
    {
        $valor=$filtros[$i];
        $versao_en=$valor;
        $cat_id=$filtro_cabecalhos_ids[$i];
        $criar=1;
        $ordem=$i;

        if($valor!='')
        {
            $valor_id=get_filtro_id($valor,$cat_id,$criar,$versao_en,$ordem);
            $filtro_valores_ids[$i]=$valor_id;
        }
        $i++;
    }





    //Agora vou atualizar a ficha de artigo
    $i=0;
    $codes='';
    while($i<count($filtros))
    {
        $valor=$filtros[$i];
        if($valor!='')
        {
            $codes.=$filtro_cabecalhos_ids[$i].'-'.$filtro_valores_ids[$i].';';

            //Vamos verificar se ja temos filtros criados
            //
            $cat_id=$filtro_cabecalhos_ids[$i];
            $valor_id=$filtro_valores_ids[$i];

    		$sql = "SELECT * FROM filters_products WHERE prod_id = $prod_id AND cat_id = '$cat_id' and valor_id='$valor_id'";
    		$rs->db_open($db_link,$DB_name,$sql);
    	    if(!$rs->EOF()){}
    	    else
    	    {
    	    	if($valor_id>0)
    	    	{
    		    	$fam_id1=$ID_ORG;
    		    	$marca_id=81;
    		    	$filtros_rel=$cat_id.'_'.$valor_id.';';
    		    	$sql='INSERT INTO filters_products (cat_id,valor_id,prod_id,fam_id1,marca_id,filtro_active,filtro_area,filtros_rel2) values("'.$cat_id.'","'.$valor_id.'","'.$prod_id.'","'.$fam_id1.'","'.$marca_id.'","1","1","'.$filtros_rel.'")';
    		    	$res=Runquery($db_link,$DB_name,$sql);
    	    	}
    	    }
        	$rs->db_close();
        }
        $i++;
    }


    //Agora vamos atualizar as combinacoes de preço:
    $sql = "SELECT * FROM products_objs_combo WHERE obj_type_id = $prod_id and obj_combo2=';$codes'";
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $obj_id=$rs->db_data["obj_id"];
        $sql='UPDATE products_objs_combo SET obj_ativo=1,obj_pvp="'.$PVP.'",obj_promo="'.$PVP2.'",obj_stock="'.$Qtd.'",obj_qtdmin="'.$Qtd2.'",obj_iva="23" WHERE obj_id = '.$obj_id;
        $res=Runquery($db_link,$DB_name,$sql);
    }
    else
    {
        $codes2 = substr($codes, 0, -1);
        $sql='INSERT INTO products_objs_combo (obj_type_id,obj_pvp,obj_promo,obj_iva,obj_stock,obj_qtdmin,obj_type,obj_val,obj_combo,obj_combo2,obj_ativo) values ("'.$prod_id.'","'.$PVP.'","'.$PVP2.'","23","'.$Qtd2.'","'.$Qtd2.'","products","1","'.$codes2.'",";'.$codes.'","1")';
        $res=Runquery($db_link,$DB_name,$sql);
    }
    $rs->db_close();


}

?>