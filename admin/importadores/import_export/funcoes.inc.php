<?php
function cleanField($field){
	return rtrim(ltrim($field));
}
function checkEncode($field)
{
	return mb_convert_encoding($field,'UTF-8');
}
function isnumeric($v)
{
	return is_numeric(str_replace(',','.',str_replace('.','',$v)));
}
function tonumeric($v)
{
	return str_replace(',','.',str_replace('.','',$v));
}
function showMessage($msg,$tipo=0)
{
	if($tipo==1) return '<span style="color:#F00">'.$msg.'</span>';
	if($tipo==2) return '<span style="color:#339933">'.$msg.'</span>';
	else return '<span>'.$msg.'</span>';
}
function insereProdutos($prods)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$rs4=new DataObj;
	for ($i=0; $i < count($prods); $i++)
	{
		$rs->db_open($db_link,$DB_name,'SELECT ID from products WHERE Ref="'.$prods[$i][IMPORT_POS_REF].'"');
		if($rs->EOF())
		{
			echo '<br><br>PRODUTO NÃO EXISTE --> "'.$prods[$i][IMPORT_POS_REF].'"<br>';
			$Peso=0;
			$Categoria=0;
			$Familia=0;

			echo '|- Peso --> "'.$prods[$i][IMPORT_POS_PESO].'"<br>';
			$Peso=$prods[$i][IMPORT_POS_PESO];
			$sql='INSERT INTO products(Ref,Aproved,Peso) VALUES ("'.$prods[$i][IMPORT_POS_REF].'",0,"'.$Peso.'")';
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				$prod_id=db_get_last_insert_id($db_link);
				echo '|- NOME --> "'.$prods[$i][IMPORT_POS_NOME].'"<br>';
				$sql='INSERT INTO products_lang(ID_PAI,Cabecalho,Lang) VALUES ("'.$prod_id.'","'.$prods[$i][IMPORT_POS_NOME].'","pt")';
				$res=Runquery($db_link,$DB_name,$sql);
				if($res)echo showMessage('|- INSERIDO o Nome<br>',2);
				else echo showMessage('Erro a inserir o Nome do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);

				echo '|- OBTER FAMILIA --> "'.$prods[$i][IMPORT_POS_FAMILIA].'"<br>';
//				$rs3->db_open($db_link,$DB_name,'SELECT ID_PAI from products_families_lang WHERE Cabecalho="'.$prods[$i][IMPORT_POS_FAMILIA].'" and Lang="pt"');
				$rs3->db_open($db_link,$DB_name,'SELECT ID as ID_PAI from products_families WHERE ID="'.$prods[$i][IMPORT_POS_FAMILIA].'"');
				if(!$rs3->EOF())
				{
					$Familia=$rs3->db_data['ID_PAI'];
					echo showMessage('|- FAMILIA IDº "'.$Familia.'"<br>',2);
					$res=Runquery($db_link,$DB_name,'INSERT INTO products_families_obj(ID_PROD,ID_ORG) VALUES ("'.$prod_id.'","'.$Familia.'")');
					if($res) echo showMessage('|- INSERIDA A FAMILIA DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a familia do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				}
				else echo showMessage('|- FAMILIA --> "'.$prods[$i][IMPORT_POS_FAMILIA].'" Não encontrada<br>',1);
				$rs3->db_close();

				$res=Runquery($db_link,$DB_name,'INSERT INTO products_objs(obj_type,obj_type_id,obj_ativo) VALUES ("products","'.$prod_id.'",1)');
				if($res)
				{
					$obj_id=db_get_last_insert_id($db_link);
					echo showMessage('|- INSERIDA A LINHA BASE OBJ DO PRODUTO <br>',2);
					$res=Runquery($db_link,$DB_name,'INSERT INTO products_objs_lang(ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'","'.$prods[$i][IMPORT_POS_REF].'","pt")');
					if($res) echo showMessage('|- INSERIDA A LINHA BASE OBJ LANG DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a linha base obj lang do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);

					/*
					$sql = 'INSERT INTO filters_products(cat_id,valor_id,prod_id,fam_id1,fam_id2,marca_id,filtros_rel2,FLAG,filtro_active) VALUES ("0","0","'.$prod_id.'","'.$Categoria.'","'.$Familia.'","'.$ID_Marca.'","",1,1)';
					$res=Runquery($db_link,$DB_name,$sql);
					if($res)echo showMessage('|- INSERIDA A LINHA BASE DE FILTROS DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a linha base de FILTROS do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
					*/
				}
				else echo showMessage('Erro a inserir a linha base obj do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
			}
			else
			{
				echo showMessage('Erro a inserir o produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				continue;
			}
		}
		else
		{
			$prod_id=$rs->db_data['ID'];
			echo '<br><br>PRODUTO JÁ EXISTE --> "'.$prods[$i][IMPORT_POS_REF].'" -- ID: "'.$prod_id.'"<br>';
		}
		//Agora que ja temos o produto inserido vamos fazer algumas atualizacoes aos seus dados
		$rs2->db_open($db_link,$DB_name,'SELECT obj_id,obj_type_id from products_objs WHERE obj_type_id="'.$rs->db_data['ID'].'"');
		$obj_id=$rs2->db_data['obj_id'];
		$rs2->db_close();

		$ID_Marca=0;
		$Peso=0;
		$Categoria=0;
		$Familia=0;


		if(isset($_POST['imp_nome']))
		{
			echo '|- ATUALIZAR NOME PRODUTO PARA "'.$prods[$i][IMPORT_POS_NOME].'"<br>';
			$rs2->db_open($db_link,$DB_name,'SELECT * from products_lang WHERE ID_PAI="'.$prod_id.'" and Lang="pt"');
			if(!$rs2->EOF())
			{
				$res=Runquery($db_link,$DB_name,'UPDATE products_lang SET Cabecalho="'.$prods[$i][IMPORT_POS_NOME].'" WHERE ID_PAI="'.$prod_id.'" AND Lang="pt"');
				if($res)echo showMessage('|- ATUALIZADO o Nome do produto para "'.$prods[$i][IMPORT_POS_NOME].'"<br>',2);
				else echo showMessage('Erro a ATUALIZAR o Nome do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
			}
			else
			{
				$res=Runquery($db_link,$DB_name,'INSERT INTO products_lang(ID_PAI,Cabecalho,Lang) VALUES ("'.$prod_id.'","'.$prods[$i][IMPORT_POS_NOME].'","pt")');
				if($res)echo showMessage('|- INSERIDO o Nome do produto <br>',2);
				else echo showMessage('Erro a inserir o Nome do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
			}
			$rs2->db_close();
		}
		if(isset($_POST['imp_peso']))
		{
			echo '|- ATUALIZAR PESO DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PESO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Peso="'.$prods[$i][IMPORT_POS_PESO].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o peso do produto para "'.$prods[$i][IMPORT_POS_PESO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o peso do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
		}
		if(isset($_POST['imp_carrinho']))
		{
			echo '|- ATUALIZAR FLAG CARRINHO DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_CARRINHO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET destaque="'.$prods[$i][IMPORT_POS_CARRINHO].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADA a flag carrinho do produto para "'.$prods[$i][IMPORT_POS_CARRINHO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR a flag carrinho do produto --> "'.$prods[$i][IMPORT_POS_CARRINHO].'"<br>',1);
		}
		if(isset($_POST['imp_ordem']))
		{
			echo '|- ATUALIZAR ORDEM DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_ORDEM].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Ordem="'.$prods[$i][IMPORT_POS_ORDEM].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO a ordem do produto para "'.$prods[$i][IMPORT_POS_ORDEM].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR a ordem do produto --> "'.$prods[$i][IMPORT_POS_ORDEM].'"<br>',1);
		}
		if(isset($_POST['imp_sinonimos']))
		{
			echo '|- ATUALIZAR SINONIMOS DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_SINONIMOS].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Hottags="'.$prods[$i][IMPORT_POS_SINONIMOS].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADOS os sinonimos do produto para "'.$prods[$i][IMPORT_POS_SINONIMOS].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR os sinonimos do produto --> "'.$prods[$i][IMPORT_POS_SINONIMOS].'"<br>',1);
		}
		if(isset($_POST['imp_medidas']))
		{
			echo '|- ATUALIZAR AS MEDIDAS DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Medidas="'.$prods[$i][IMPORT_POS_MEDIDAS].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADOS as medidas do produto para "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR as medidas do produto --> "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',1);
		}
		if(isset($_POST['imp_medidas']))
		{
			echo '|- ATUALIZAR AS MEDIDAS DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET destaque="'.$prods[$i][IMPORT_POS_MEDIDAS].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADOS as medidas do produto para "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR as medidas do produto --> "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',1);
		}
		if(isset($_POST['imp_destaque']))
		{
			echo '|- ATUALIZAR A OPCAO DESTAQUE DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_DESTAQUE].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Home="'.$prods[$i][IMPORT_POS_DESTAQUE].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o destaque do produto para "'.$prods[$i][IMPORT_POS_DESTAQUE].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o destaque do produto --> "'.$prods[$i][IMPORT_POS_DESTAQUE].'"<br>',1);
		}

		if(isset($_POST['imp_modelo']))
		{
			echo '|- ATUALIZAR O MODELO DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_MODELO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs_lang SET Cabecalho="'.$prods[$i][IMPORT_POS_MODELO].'" WHERE ID_PAI="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o modelo do produto para "'.$prods[$i][IMPORT_POS_MODELO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o modelo do produto --> "'.$prods[$i][IMPORT_POS_MODELO].'"<br>',1);
		}
		if(isset($_POST['imp_pvp']))
		{
			echo '|- ATUALIZAR O PVP DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PVP].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_pvp="'.$prods[$i][IMPORT_POS_PVP].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o pvp do produto para "'.$prods[$i][IMPORT_POS_PVP].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o pvp do produto --> "'.$prods[$i][IMPORT_POS_PVP].'"<br>',1);
		}
		if(isset($_POST['imp_pvp_promo']))
		{
			echo '|- ATUALIZAR O pvp promo DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PVP_PROMO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_promo="'.$prods[$i][IMPORT_POS_PVP_PROMO].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o pvp promo do produto para "'.$prods[$i][IMPORT_POS_PVP_PROMO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o pvp promo do produto --> "'.$prods[$i][IMPORT_POS_PVP_PROMO].'"<br>',1);
		}
		if(isset($_POST['imp_promo']))
		{
			echo '|- ATUALIZAR O campo promo DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PROMO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_promo_ativo="'.$prods[$i][IMPORT_POS_PROMO].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o campo promo do produto para "'.$prods[$i][IMPORT_POS_PROMO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o campo promo do produto --> "'.$prods[$i][IMPORT_POS_PROMO].'"<br>',1);
		}
		if(isset($_POST['imp_stock']))
		{
			echo '|- ATUALIZAR O STOCK DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_STOCK].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_stock="'.$prods[$i][IMPORT_POS_STOCK].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o stock do produto para "'.$prods[$i][IMPORT_POS_STOCK].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o stock do produto --> "'.$prods[$i][IMPORT_POS_STOCK].'"<br>',1);
		}

		if(isset($_POST['imp_visivel']))
		{
			echo '|- ATUALIZAR A OPCAO VISIVEL DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_VISIVEL].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Aproved="'.$prods[$i][IMPORT_POS_VISIVEL].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO a opcao visivel do produto para "'.$prods[$i][IMPORT_POS_VISIVEL].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR a opcao visivel  do produto --> "'.$prods[$i][IMPORT_POS_VISIVEL].'"<br>',1);
		}
		if(isset($_POST['imp_familia']))
		{
			echo '|- ATUALIZAR FAMILIA DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_FAMILIA].'"<br>';
			$rs2->db_open($db_link,$DB_name,'SELECT ID as ID_PAI from products_families WHERE ID="'.$prods[$i][IMPORT_POS_FAMILIA].'"');
			//$rs2->db_open($db_link,$DB_name,'SELECT ID_PAI from products_families_lang WHERE Cabecalho="'.$prods[$i][IMPORT_POS_FAMILIA].'" and Lang="pt"');
			if(!$rs2->EOF())
			{
				$Familia=$rs2->db_data['ID_PAI'];
				echo showMessage('|- FAMILIA IDº "'.$Familia.'"<br>',2);
				$rs4->db_open($db_link,$DB_name,'SELECT ID_ORG from products_families_obj where ID_PROD="'.$prod_id.'"');
				if(!$rs4->EOF())
				{
					$res=Runquery($db_link,$DB_name,'UPDATE products_families_obj SET ID_ORG="'.$Familia.'" WHERE ID_PROD="'.$prod_id.'"');
					if($res) echo showMessage('|- ATUALIZADA A FAMILIA DO PRODUTO  para "'.$prods[$i][IMPORT_POS_FAMILIA].'"<br>',2);
					else echo showMessage('Erro a atualizar a familia do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				}
				else
				{
					$res=Runquery($db_link,$DB_name,'INSERT INTO products_families_obj(ID_PROD,ID_ORG) VALUES ("'.$prod_id.'","'.$Familia.'")');
					if($res)echo showMessage('|- INSERIDA A FAMILIA DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a familia do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				}
				$rs4->db_close();
			}
			else echo showMessage('|- FAMILIA --> "'.$prods[$i][IMPORT_POS_FAMILIA].'" Não encontrada<br>',1);
			$rs3->db_close();
		}
		$rs->db_close();
	}
}
function show_fast_tools()
{
	global $ID_ORG,$ID;?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
    <tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Ficheiro Exemplo" onclick="javascript:window.open('exemplo.csv','_new')"></td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../../../images/bsh.png" width="200px" height="30px">
<? }



function formatEAN($ean)
{
    $ean = str_pad($ean,13,'0',STR_PAD_LEFT);
    return $ean;
}
function checkNumberFormat($ean)
{
    $return = 1;
    $ean_expression = '/^[0-9]{13}$/m'; // aceita números com exactamente 13 dígitos
    if (!preg_match_all($ean_expression,$ean,$matches,PREG_SET_ORDER,0)) $return = 0;
    return $return;
}
function formatText($text)
{
    $return = $text;
    $return = utf8_encode($return);
    $return = addslashes($return);
    $return = str_replace('|','<br>',$return);
    return $return;
}
function getFilterID($ID_Filtro,$Valor_Filtro)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs = new DataObj;
    $return = '';
    $sql_select = '
            SELECT
                fv.value_id
            FROM
                filters_categories AS fc,
                filters_values AS fv,
                filters_values_lang AS fvl
            WHERE
                '.$ID_Filtro.' = fc.category_id
                AND
                fc.category_id = fv.category_id
                AND
                fv.value_id = fvl.values_id
                AND
                fvl.values_name = "'.$Valor_Filtro.'"';

	$rs->db_open($db_link,$DB_name,$sql_select);
    if (!$rs->EOF()) $return = $rs->db_data['value_id'];
    return $return;
}
function products_families_obj_save($id_prod,$id_org)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs = new DataObj;
    $res = '';
    $sql = "DELETE FROM products_families_obj WHERE ID_PROD='".$id_prod."'";
    $res = Runquery($db_link,$DB_name,$sql);
    if ($res)
    {
        $nId_org = count($id_org);
        for ($i = 0;$i < $nId_org;$i++)
        {
            $res = '';
            $sql = "INSERT INTO products_families_obj (ID_PROD,ID_ORG) values ('".$id_prod."','".intval($id_org[$i])."')";
            $res = Runquery($db_link,$DB_name,$sql);
            if (!$res) break;
        }
    }
    return $res;
}
function products_objs_save($ID_PROD = 0,$Cabecalho)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs = new DataObj;
    $rs2 = new DataObj;
    $res = '';
    $sql = "SELECT obj_id FROM products_objs WHERE obj_type='products' AND obj_type_id='".$ID_PROD."'";
    $rs->db_open($db_link,$DB_name,$sql);
    if (!$rs->EOF())
    {
        $obj_id = $rs->db_data['obj_id'];
    }
    else
    {
        $sql = "
            INSERT INTO
                products_objs
                (
                    obj_type,
                    obj_type_id,
                    obj_store,
                    obj_val2
                )
                VALUES
                (
                    'products',
                    '".$ID_PROD."',
                    '0',
                    '0'
                )
            ";
        $res = Runquery($db_link,$DB_name,$sql);
        if ($res) $obj_id = db_get_last_insert_id($db_link);
    }
    $rs->db_close();
    $res = '';
    if ($obj_id > 0)
    {
        $sql = "SELECT * FROM products_objs_lang WHERE ID_PAI='".$obj_id."' AND Lang='".$GLOBALS['admin_lang']."'";
        $rs->db_open($db_link,$DB_name,$sql);
        if (!$rs->EOF())
        {
            $obj_id2 = $rs->db_data['ID'];
            $sql = "UPDATE products_objs_lang SET Cabecalho='".$Cabecalho."' WHERE ID='".$obj_id2."' AND ID_PAI='".$obj_id."' AND Lang='".$GLOBALS['admin_lang']."'";
            $res = Runquery($db_link,$DB_name,$sql);
        }
        else
        {
            $sql = "INSERT INTO products_objs_lang(Cabecalho,ID_PAI,Lang) VALUES('".$obj_desc."','".$obj_id."','".$GLOBALS['admin_lang']."')";
            $res = Runquery($db_link,$DB_name,$sql);
        }
    }
    return $res;
}
function ScanFilesDir()
{
    global $site_shell_url;
    $path = $site_shell_url."files/images_upload/";
    $dir_handle = opendir($path);
    while ($file = readdir($dir_handle))
    {
        $src = $path.$file;
        if (is_file($src) && (pathinfo($src,PATHINFO_EXTENSION) == 'png' || pathinfo($src,PATHINFO_EXTENSION) == 'PNG' || pathinfo($src,PATHINFO_EXTENSION) == 'jpg' || pathinfo($file,PATHINFO_EXTENSION) == 'JPG'))
        {
            $files[] = $src;
        }
    }
    return $files;
}
function insertImages($ID,$Ref)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    global $site_shell_url;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $path_origin = $site_shell_url."files/images_upload/";
    $path_dest = $site_shell_url."files/products/";
    $error = 1;
    $counter = 1;
    $img_ext = array();
    $img_ord = array();
    $dir_handle = opendir($path_origin);
    while ($file = readdir($dir_handle))
    {
        $image_url_origin = $path_origin.$file;
        $ext = '';
        $ext = pathinfo($image_url_origin,PATHINFO_EXTENSION);
        $file_name = pathinfo($image_url_origin,PATHINFO_FILENAME);
        if (is_file($image_url_origin))
        {
            $order = '1';
            $hasUnderscore = 0;
            $isNumber = false;
            $hasUnderscore = substr_count($file_name,'_');
            if ($hasUnderscore > 0)
            {
                $isNumber = is_numeric(substr($file_name,strrpos($file_name,"_") + 1,strlen($file_name)));
                if ($isNumber)
                {
                    $order = substr($file_name,strrpos($file_name,"_") + 1,strlen($file_name)) + 1;
                    $file_name = substr($file_name,0,strrpos($file_name,"_"));
                }
            }
            $image_url_dest = $path_dest.$ID."_".$order.".".pathinfo($image_url_origin,PATHINFO_EXTENSION);
            $image_url_dest_prod = $path_dest.$ID."_".$order."_prod.".pathinfo($image_url_origin,PATHINFO_EXTENSION);
            $image_url_dest_small = $path_dest.$ID."_".$order."_small.".pathinfo($image_url_origin,PATHINFO_EXTENSION);
            $image_url_dest_thum = $path_dest.$ID."_".$order."_thum.".pathinfo($image_url_origin,PATHINFO_EXTENSION);
            if ($file_name == $Ref)
            {
                $error = 0;
                if (!copia_segura($image_url_origin,$image_url_dest)) $error = 1;
                if (!copia_segura($image_url_origin,$image_url_dest_prod)) $error = 1;
                if (!copia_segura($image_url_origin,$image_url_dest_small)) $error = 1;
                if (!copia_segura($image_url_origin,$image_url_dest_thum)) $error = 1;
                $img_ext[] = $ext;
                $img_ord[] = $order;
                $image_url_delete[] = $image_url_origin;
                $counter++;
            }
        }
    }
    closedir($dir_handle);
    if (!$error)
    {
        $sql = "DELETE FROM products_images WHERE ID_ORG = ".$ID;
        $res = Runquery($db_link,$DB_name,$sql);
        $i = 0;
        while ($i < count($img_ext))
        {
            $sql = "INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES (".$ID.",'".$img_ext[$i]."','".$img_ord[$i]."','')";
            $res = '';
            $res = Runquery($db_link,$DB_name,$sql);
            $i++;
        }
    }
}







function atualiza_dados_produtos($titulos,$data,&$total_produtos_update,&$total_produtos_insert,&$erros)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs1 = new DataObj;

    $Cabecalho = '';
    $ID_Marca = '';
    $Ref = '';
    $Texto1 = '';
    $ID_Filtro1 = '';
    $Valor_Filtro1 = '';
    $ID_Filtro2 = '';
    $Valor_Filtro2 = '';
    $EAN = '';
    $SKU = '';
    $ID_ORG = '';
    $Texto3 = '';
    $ID = '';
    $res = '';
    $res2 = '';
    $res3 = '';
    $res5 = '';
    $res6 = '';
    if ($linha != 1)
    {
        $Cabecalho = formatText($data[0]);
        $ID_Marca = intval($data[1]);
        $Ref = $data[2];
        $Ref_PAI = $data[3];
        $Texto1 = formatText($data[4]);
        $EAN = formatEAN($data[5]);
	    $SKU = $data[6];
        $ID_ORG = $data[9];
        $Texto3 = formatText($data[10]);
//echo $EAN.' | ';
        $sql = 'SELECT products.ID FROM products WHERE products.ref_ean = '.$EAN;
        
        $rs1->db_open($db_link,$DB_name,$sql);
        // PRODUTO EXISTE - UPDATE
        if (!$rs1->EOF())
        {
            $ID = $rs1->db_data['ID'];

            //TODO
            if (checkNumberFormat($EAN))
            {
            	/*$sql_update_produto = "UPDATE
                                        products
                                    SET
                                        ref_sku     = '".$SKU."',
                                        ID_Marca    = '".$ID_Marca."',
                                        Ref         = '".$Ref."',
                                        udtime      = '".date('Y-m-d H:m:s')."',
                                        ID_ORG      = '".$ID_ORG."'
                                    WHERE ID        = '".$ID."'";*/

				$and='';
				if($Ref_PAI)$and.=',Ref_Pai="'.$Ref_PAI.'"';

                $sql = "UPDATE products SET Carrinho = '1', ID_Marca = '".$ID_Marca."',udtime='".date('Y-m-d H:m:s')."'".$and." WHERE ID = '".$ID."'";
                
                $res = Runquery($db_link,$DB_name,$sql);

                //descss
                $sql_update_produto_lang = "
                                    UPDATE
                                        products_lang
                                    SET
                                        Cabecalho   = '".$Cabecalho."',
                                        Texto1      = '".$Texto1."'
                                    WHERE ID_PAI    = '".$ID."'";

                //descss
                $res2 = Runquery($db_link,$DB_name,$sql_update_produto_lang);

                $families = explode(';',$ID_ORG);
                $res3 = products_families_obj_save($ID,$families);
                if ($res3) $res3 = products_objs_save($ID,$Cabecalho);
                insertImages($ID,$Ref);

            }
            if (!$res && !$res2 && !$res3)
            {
                $erros = 1;
                echo '<div style="color:#F00">'.date('d-m-Y H:m:s').' linha #'.$linha.' - ERRO #3: o produto com o EAN '.$EAN.' não actualizou! '.$sql.'</div><br>';
            }
            else
            {
            	//Agora vamos criar os filtros mas primeiro temos de remover todos os valores
				$sql = "DELETE FROM filters_products where prod_id='".$ID."'";
				$res = Runquery($db_link,$DB_name,$sql);

            	$i=11;
            	$fim=0;

            	while(!$fim)
            	{
            		if(!$titulos[$i])$fim=1;
            		else
            		{

            			if(formatText($data[$i]))
            			{
            				//Para cada familia vamos criar os filtros:

            				$ID_ORG_TMP = explode(';',$ID_ORG);
            				$j=0;
            				while($j<count($ID_ORG_TMP))
            				{

            					//echo $ID. '-' .$ID_ORG_TMP[$j]. '-' .$i. '-' .$titulos. '-' .var_dump($data). '<br>';

								atualiza_filtro($ID,$ID_ORG_TMP[$j],$i,$titulos,$data);
								$j++;
            				}
            			}
            		}
            		$i++;
            	}


                //echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' linha #'.$linha.' - SUCESSO #2: o produto com o EAN '.$EAN.' foi actualizado.</div><br>';
                $total_produtos_update++;
            }
        }
        //PRODUTO NÃO EXISTE NA LOJA - INSERT
        else
        {
            if (checkNumberFormat($EAN))
            {
            	//Para cada familia vamos criar os filtros:
				$ID_ORG_TMP = explode(';',$ID_ORG);


                $sql = 'INSERT INTO products(ref_ean,ID_Marca,Ref,ID_ORG,Aproved,Carrinho,Data_criacao) VALUES ("'.$EAN.'","'.$ID_Marca.'" ,"'.$Ref.'","'.$ID_ORG_TMP[0].'","1","1","'.date('Y-m-d').'")';
                $res = Runquery($db_link,$DB_name,$sql);
                if ($res)
                {
                    $ID = db_get_last_insert_id($db_link);
                    if ($ID > 0)
                    {

                        $sql = 'INSERT INTO products_lang (ID_PAI,Cabecalho,Texto1,Texto3,Lang) VALUES ("'.$ID.'","'.$Cabecalho.'","'.$Texto1.'","1'.$Texto3.'2","'.$GLOBALS['admin_lang'].'")';
                        $res2 = Runquery($db_link,$DB_name,$sql);
                        $families = explode(';',$ID_ORG);
                        $res3 = products_families_obj_save($ID,$families);
                        if ($res3) $res3 = products_objs_save($ID,$Cabecalho);
                        insertImages($ID,$Ref);
                    }
                }
            }
            else $sql_insert_product = ' EAN inválido! ';

            if (!$res && !$res2 && !$res3)
            {
                $erros = 1;
                echo '<div style="color:#F00">'.date('d-m-Y H:m:s').' linha #'.$linha.' - ERRO #4: o produto com o EAN '.$EAN.' não foi inserido!</div><br>';
            }
            else
            {
				//Agora vamos criar os filtros mas primeiro temos de remover todos os valores
				$sql = "DELETE FROM filters_products where prod_id='".$ID."'";
				$res = Runquery($db_link,$DB_name,$sql);

            	$i=11;
            	$fim=0;

            	while(!$fim)
            	{
            		if(!$titulos[$i])$fim=1;
            		else
            		{
            			if(formatText($data[$i]))
            			{
            				//Para cada familia vamos criar os filtros:
            				$ID_ORG_TMP = explode(';',$ID_ORG);
            				$j=0;
            				while($j<count($ID_ORG_TMP))
            				{

            					//echo $ID. '-' .$ID_ORG_TMP[$j]. '-' .$i. '-' .$titulos. '-' .var_dump($data). '<br>';
								atualiza_filtro($ID,$ID_ORG_TMP[$j],$i,$titulos,$data);
								$j++;
            				}
            			}
            		}
            		$i++;
            	}

				$i=11;
            	$fim=0;

            	while(!$fim)
            	{
            		if(!$titulos[$i])$fim=1;
            		else
            		{
            			if(formatText($data[$i]))
            			{
            				//Para cada familia vamos criar os filtros:
            				$ID_ORG_TMP = explode(';',$ID_ORG);
            				$j=0;
            				while($j<count($ID_ORG_TMP))
            				{

            					//echo $ID. '-' .$ID_ORG_TMP[$j]. '-' .$i. '-' .$titulos. '-' .var_dump($data). '<br>';
								atualiza_filtro($ID,$ID_ORG_TMP[$j],$i,$titulos,$data);
								$j++;
            				}
            			}
            		}
            		$i++;
            	}

                $total_produtos_insert++;
            }

        }
        $rs1->db_close();
    }
    $linha++;
}

function atualiza_filtro($ID,$ID_ORG,$i,$titulos,$data)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs1 = new DataObj;

    $nome_filtro_pt=trim($titulos[$i]);
	$Valor_Filtro=trim($data[$i]);

	$sql = 'SELECT * FROM `filters_categories_lang` WHERE category_lang = "pt" and category_name like "'.$nome_filtro_pt.'"';
	if($ID==1649) echo "<br>".$sql; //TODOss
	//echo $sql."<br>";
	$rs1->db_open($db_link,$DB_name,$sql);
    if (!$rs1->EOF())
    {
    	$ID_Filtro=$rs1->db_data['category_id'];
    }
    else
    {
    	//Ainda nao existe o filtro, por isso temos de o criar
    	$sql = 'INSERT INTO `filters_categories` (category_type,category_order,category_active,category_flag,category_id_import,category_active2) values(2,0,1,0,0,1)';
    	$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$ID_Filtro=db_get_last_insert_id($db_link);
			$sql = 'INSERT INTO `filters_categories_lang` (category_id,category_name,category_lang) values('.$ID_Filtro.',"'.$nome_filtro_pt.'","pt")';
    		$res=Runquery($db_link,$DB_name,$sql);
    		if(!$res)echo db_error($db_link).$sql;
		}
    }


    //Agora vamos ver se ja existem valores inseridos para os filtros
    $sql = 'SELECT * FROM `filters_values_lang` WHERE values_lang = "pt" and values_name like "'.$Valor_Filtro.'"';
   	$rs1->db_open($db_link,$DB_name,$sql);
    if (!$rs1->EOF())
    {
    	$ID_Filtro_Valor=$rs1->db_data['values_id'];
    }
    else
    {
    	//Ainda nao existe o valor do filtro, por isso temos de o criar
    	$sql = 'INSERT INTO `filters_values` (category_id,value_active) values('.$ID_Filtro.',1)';
    	$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$ID_Filtro_Valor=db_get_last_insert_id($db_link);
			$sql = 'INSERT INTO `filters_values_lang` (values_id,values_name,values_lang) values('.$ID_Filtro_Valor.',"'.$Valor_Filtro.'","pt")';
    		$res=Runquery($db_link,$DB_name,$sql);
    		if(!$res)echo db_error($db_link).$sql;
		}
    }

    //Agora vamos associar este  filtro ao artigo
    $res5 = insertFilters($ID,$ID_ORG,$ID_Filtro,$Valor_Filtro);
}

function insertFilters($ID,$ID_ORG,$ID_Filtro,$Valor_Filtro)
{
	if(!$ID_ORG) return;
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $res = '';
    $ID_valor_filtro = getFilterID($ID_Filtro,$Valor_Filtro);
	if($ID_valor_filtro>0)
    {
	    //FILTROS
	    $fams = BuscarArvoreFams_F($ID_ORG);
	    if (isset($fams[0])) $fam_id1 = $fams[0];
	    else $fam_id1 = 0;
	    if (isset($fams[1])) $fam_id2 = $fams[1];
	    else $fam_id2 = 0;
	    if (isset($fams[2])) $fam_id3 = $fams[2];
	    else $fam_id3 = 0;

	    $sql = "INSERT INTO filters_products (cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,marca_id,filtros_rel2,FLAG,data_atualizacao,filtro_active,filtro_area) VALUES ('".$ID_Filtro."','".$ID_valor_filtro."','".$ID."','".$fam_id1."','".$fam_id2."','".$fam_id3."','".$ID_Marca."','".$ID_Filtro.'_'.$ID_valor_filtro."',1,'".date('Y-m-d H:m:s')."',1,2)";
	    $res = Runquery($db_link,$DB_name,$sql);
	    if(!$res)echo db_error($db_link).$sql;
    }
    return $res;
}
?>