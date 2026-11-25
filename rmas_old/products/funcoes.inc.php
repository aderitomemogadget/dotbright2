<?php
function combinations($arrays, $i = 0) 
{
    if (!isset($arrays[$i])) return array();    
    if ($i == count($arrays) - 1) return $arrays[$i];
    
	// GET COMBINATIONS FROM SUBSEQUENT ARRAYS
    $tmp = combinations($arrays, $i + 1);

    $result = array();

    // CONCAT EACH ARRAY FROM TMP WITH EACH ELEMENT FROM $arrays[$i]
    foreach ($arrays[$i] as $v) 
	{
        foreach ($tmp as $t) 
		{
            $result[] = is_array($t) ? array_merge(array($v), $t) : array($v, $t);
        }
    }
    return $result;
}

function filtros_ficha_produto_adm($prod_id=0, $fam_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2 = new DataObj; $filtros = array();$return = '';
	$sql2 = '
			SELECT 
				fp.cat_id, fp.valor_id, fp.filtro_area 
			FROM 
				filters_products fp
			WHERE 
				(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.')
				AND prod_id='.$prod_id.' 
				AND fp.filtros_rel2 <> "" 
				AND fp.filtro_active = 1
				AND (fp.filtro_area=1 OR fp.filtro_area=2)
			GROUP BY 
				fp.valor_id, fp.cat_id
			Order By 
				fp.filtro_id asc
			';
	$obj2->db_open($db_link,$DB_name,$sql2);
	$a = 0;
	while(!$obj2->EOF())
	{		
		$filtros[$obj2->db_data['cat_id']][] = $obj2->db_data['valor_id'];

		$obj2->db_next();
	}
	$obj2->db_close();
	$arr = array();
	foreach($filtros as $key => $value)
	{
		for($a=0;$a<count($value); $a++)
		{
			if(!in_array($key, $arr))
			{ 
				$cat = buscacategoriaTipoeNomex_adm($key);
				$nm_value = buscavalorfiltrosx_adm($value[$a]);
				$arr[$key][] = $key.'spl'.$cat['category_name'].';'.$value[$a].'spl'.$nm_value;
			}
		}		
	}	
	
		
	$arrays = array_values(array_filter($arr));
	$combinations = combinations($arrays);
	
	
	$obj_combo = '';
	
	if(count($combinations) > 0)
	{
		$return .= '<table style="margin:0px;" border="0" style="margin:0 auto">';
		for($a=0; $a<count($combinations); $a++)
		{
			$obj_combo = '';
			
			if($cor=='#F6F6F6') {$cor="#def2fa";}
			else				{$cor="#F6F6F6";}
			
			$return .= '<tr bgcolor="'.$cor.'">';
			
			if(is_array($combinations[$a]))
			{
				for($b=0; $b<count($combinations[$a]); $b++)
				{
					if($cor2=='#CCFF99') {$cor2="#CCCC99";}
					else				{$cor2="#CCFF99";}
					
					$n = explode(';', $combinations[$a][$b]);
					$bl_cat = explode('spl', $n[0]);
					$bl_val = explode('spl', $n[1]);
					
					$return .= '<td height="25" align="center" style="background:'.$cor2.'">'.$bl_cat[1].': '.$bl_val[1].'</td>';			
					$obj_combo .= $bl_cat[0].'-'.$bl_val[0].';';
				}
			}
			else
			{
				if($cor2=='#CCFF99') {$cor2="#CCCC99";}
				else				{$cor2="#CCFF99";}
				
				$n = explode(';', $combinations[$a]);
				$bl_cat = explode('spl', $n[0]);
				$bl_val = explode('spl', $n[1]);
				
				$return .= '<td height="25" align="center" style="background:'.$cor2.'">'.$bl_cat[1].': '.$bl_val[1].'</td>';			
				$obj_combo .= $bl_cat[0].'-'.$bl_val[0].';';
			}
			
			$return .= '</tr>';
			$return .= '<tr bgcolor="'.$cor.'">';
			$return .= '<td colspan="50" align="center">';
			$return .= products_objs_filtros($prod_id, rtrim($obj_combo, ';'), $a);
			$return .= '</td>';		
			$return .= '</tr>';		
			
			$return .= '<tr >';
			$return .= '<td colspan="50" align="center">&nbsp;</td>';		
			$return .= '</tr>';
		}
		$return .= '</table>';
		}
	return $return;
}
function buscacategoriaTipoeNomex_adm($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;	
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$sql="SELECT filters_categories.category_type, filters_categories_lang.category_name FROM filters_categories INNER JOIN filters_categories_lang ON filters_categories_lang.category_id=filters_categories.category_id  WHERE filters_categories.category_id='$ID'";
	// echo $sql;
	$dados->db_open($db_link,$DB_name,$sql);
	$dados->db_close();
	
	return array('category_type' => $dados->db_data["category_type"] , 'category_name' => $dados->db_data["category_name"]);	
}
function buscavalorfiltrosx_adm($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;	
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$sql="SELECT values_name from filters_values_lang where values_id='$ID' and values_lang='".$GLOBALS['site_lang']."'";
	$dados->db_open($db_link,$DB_name,$sql);
	return $dados->db_data["values_name"];
	$dados->db_close();
}
// COPIA PARA OUTRO SITE --------------------------------------------------------------------------
function copia_produto($prod_id=0)
{
	echo $_POST['copiar_produto'].' - '.$_POST['marca_remote'].' - '.$_POST['m_familia_remote'];
	if(isset($_POST['copiar_produto']) && isset($_POST['marca_remote']) && isset($_POST['m_familia_remote']) && $_POST['copiar_produto'] == 1 && $_POST['marca_remote'] > 0 && $_POST['m_familia_remote'] > 0)
	{	
	
		$fam_id = $_POST['m_familia_remote'];
		$marca_id = $_POST['marca_remote'];
		
		
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj = new DataObj;
		$sql2 = '
				SELECT 
					prod_id
				FROM 
					products_copia
				WHERE 
					prod_id = "'.$prod_id.'"
				';
				// echo $sql2;
		$obj->db_open($db_link,$DB_name,$sql2);

		if(!$obj->EOF())
		{			
			$upd = 'UPDATE products_copia SET fam_id = "'.$fam_id.'", marca_id = "'.$marca_id.'" WHERE prod_id = "'.$prod_id.'"';
			Runquery($db_link, $DB_name, $upd); 
		}
		else
		{
			$ins = 'INSERT INTO products_copia (fam_id, marca_id, prod_id) VALUES ("'.$fam_id.'", "'.$marca_id.'", "'.$prod_id.'")';
			Runquery($db_link, $DB_name, $ins); 			
		}
		
		$obj->db_close();
	}

}

function gera_lista_marcas2_remote($sel_nm,$tabela1,$tabela1_lang,$id_marca=0)
{
    global $DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host_remote,$DB_user_remote,$DB_pass_remote, $DB_name_remote);
	$obj=new DataObj;
	$sql="Select $tabela1.*,$tabela1_lang.Nome from $tabela1 inner join $tabela1_lang on $tabela1_lang.ID_PAI=$tabela1.ID WHERE $tabela1_lang.Lang='$admin_lang' order by $tabela1_lang.Nome Asc";
	$hRes=$obj->db_open($db_link,$DB_name_remote,$sql);
    echo '<select name="'.$sel_nm.'_remote" id="'.$sel_nm.'_remote" class="chzn-select5">';
	if($id_marca==0) echo '<option value="0" selected>Marca</option>';
	else echo '<option value="0">Marca</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_marca==$obj->db_data["ID"] ) echo ' selected="selected" '; 
		echo '>'. htmlentities_memo(stripslashes($obj->db_data["Nome"])).'</option>';
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}

function gera_lista_familias_remote($id_org=0,$tabela1,$tabela1_lang)
{
    global $DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote,$admin_lang,$ID;
$db_link=db_opendb_conection($DB_host_remote,$DB_user_remote,$DB_pass_remote, $DB_name_remote);
	$obj = new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name_remote,$sql);
    echo '<select name="m_familia_remote" id="m_familia_remote" class="chzn-select4" >';
	if($id_org==0) echo '<option value="0" selected>Categoria</option>';
	else echo '<option value="0">Categoria</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name_remote,$sql);   
		echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';			
		$obj2->db_close();
	    gera_lista_subfamilias_remote($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang);	    
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function gera_lista_subfamilias_remote($id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=5)
{
    global $DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host_remote,$DB_user_remote,$DB_pass_remote, $DB_name_remote);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name_remote,$sql);	
    while(!$obj->EOF())
    {
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name_remote,$sql);   
		echo '>';
		$i=0;while($i<$contador){echo "- ";$i++;}
		echo  htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';		
	    $obj2->db_close();
	    gera_lista_subfamilias_remote($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+5);	    
        $obj->db_next();
    }
    $obj->db_close();
}

// -------------------------------------------------------------------------------------------------------------------------


function getFiltros1()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql2 = '
			SELECT 
				fc.category_id,fcl.category_name 
			FROM 
				filters_categories AS fc 
				INNER JOIN filters_categories_lang AS fcl ON fc.category_id=fcl.category_id 
				WHERE 
					fcl.category_lang="'.$site_lang.'" 
				ORDER BY 
					fcl.category_name ASC
			';
	$obj2=new DataObj;
	$obj2->db_open($db_link,$DB_name,$sql2);
	$data = array();
	$temp = array();
	$valor_antigo = 0;
	while(!$obj2->EOF())
	{
		
		$temp = array();
		$temp[] = $obj2->db_data['category_id'];
		$temp[] = $obj2->db_data['category_name'];
		$data[] = $temp;
		$obj2->db_next();
	}
	$obj2->db_close();
	return $data;
}
function getFiltrosProd($ID_Fam = 0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2 = new DataObj;
	$sql2 = '
			SELECT 
				fp.cat_id, fp.valor_id, fp.filtro_area 
			FROM 
				filters_products fp
			WHERE 
				(fp.fam_id1='.$ID_Fam.' OR fp.fam_id2='.$ID_Fam.' OR fp.fam_id3='.$ID_Fam.')
				AND prod_id='.$_GET['ID'].' 
				AND fp.filtros_rel2 <> "" 
				AND fp.filtro_active = 1
			GROUP BY fp.valor_id, fp.cat_id
			Order By fp.filtro_id asc
			';
	// echo $sql2;
	$obj2->db_open($db_link,$DB_name,$sql2);
	$a = 0;
	while(!$obj2->EOF())
	{									
		$a++;
		
		$temp[$a] = array('cat_id' => $obj2->db_data['cat_id'], 'valor_id' => $obj2->db_data['valor_id'], 'filtro_area' => $obj2->db_data['filtro_area']);

		$obj2->db_next();
	}
	$obj2->db_close();
	return $temp;
}
	
function InsertNewProd($id_org,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$dominio,$caminho,$userx,$passwordx,$site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho1=str2SQLstr($cabecalho);
	$preco_prec=str2SQLstr($preco_prec);
	$preco1=str2SQLstr($preco1);
	$preco2=str2SQLstr($preco2);
	$precopromo1=str2SQLstr($precopromo1);
	$precopromo2=str2SQLstr($precopromo2);
	$destaque=str2SQLstr($destaque);
	$novidade=str2SQLstr($novidade);
	$promocao=str2SQLstr($promocao);
	$home=str2SQLstr($home);
	$margem=str2SQLstr($margem);
	$margem_activa=str2SQLstr($margem_activa);
	$saldo=str2SQLstr($saldo);
	$aproved=str2SQLstr($aproved);
	$ref=str2SQLstr($ref);
	$tipo=str2SQLstr($tipo);
	$stock_txt1=str2SQLstr($stock_txt);
	$strSearchWords=str2SQLstr($strSearchWords);
	$bonus = $_POST['pontos'].$_POST['tpoints'];
	
	if(isset($_POST['carrinho'])) $carrinho=$_POST['carrinho'];	else $carrinho=0;
	if(isset($_POST['estados_produto'])) $estados_produto=$_POST['estados_produto']; else $estados_produto=0;
	
	$personalizacao_on = '';
	if(isset($_POST['personalizacao']))
	{ 
		$personalizacao = $_POST['personalizacao'];
		for($a=0; $a<count($personalizacao); $a++)
		{
			$personalizacao_on .= $personalizacao[$a].';';
		}
	}
		
	$descontopreco = $_POST['descontopreco'];
	
	$sql="
		INSERT INTO products 
			(
				Ordem,
				Ref,
				Preco1,
				Preco2,
				PrecoPromo1,
				PrecoPromo2,
				Preco_prec,
				Margem,
				MargemActiva,
				Destaque,
				Novidade,
				Promo,
				Home,
				Saldo,
				Aproved,
				Carrinho,
				Tipo,
				Hottags,
				ID_Marca,
				Peso,
				usado,
				StockTxt,
				Medidas,
				PrecoCusto,
				Qtd,
				Download,
				decumentario,
				Sync,
				preco_revenoff,
				peso_cliente,
				descontopreco,
				points,
				personalizacao,
				estados_produto
			) 
			VALUES 
			(
				'$ordem',$ref,$preco1,$preco2,$precopromo1,$precopromo2,$preco_prec,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$carrinho,$tipo,$strSearchWords,
				'$marca',
				'$peso',
				'$usado',
				$stock_txt1,
				'$medidas',
				'$precocusto',
				'$stock',
				'$download',
				'$decumentario',
				'$sync',
				'$preco_revenoff',
				'".$_POST['peso_cliente']."',
				'".$_POST['descontopreco']."',
				'".$bonus."',
				'".$personalizacao_on."',
				'".$estados_produto."'
			)
		";
		// echo $sql;
    $res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);
		// $res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS products_m');//limpar a tabela de ram
		
		products_images_save($new_id,$Img_peq,$tem_foto,$Apagar_img);
		products_objs_save($new_id);
		
		products_families_save($new_id,$id_org);

		$arr_fams = encontraFamiliasRecursiva_x();
		if(isset($arr_fams[0])) $fam_id1 = $arr_fams[0]; else $fam_id1 = 0;
		if(isset($arr_fams[1])) $fam_id2 = $arr_fams[1]; else $fam_id2 = 0;
		if(isset($arr_fams[2])) $fam_id3 = $arr_fams[2]; else $fam_id3 = 0;

		
		if($ID_Fam > 0)
		{
		
			$sql ='INSERT INTO filters_products(
					cat_id,
					valor_id,
					prod_id,
					fam_id1,
					fam_id2,
					fam_id3,
					marca_id,
					filtros_rel2,
					FLAG,
					filtro_active
				)
				VALUES
				(
					"0",
					"0",
					"'.$new_id.'",
					"'.$fam_id1.'",
					"'.$fam_id2.'",
					"'.$fam_id3.'",
					"'.$marca.'",
					"",
					1,
					1
				)
			';
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);		
		}
		else
		{
			$sql ='INSERT INTO filters_products(
					cat_id,
					valor_id,
					prod_id,
					fam_id1,
					fam_id2,
					fam_id3,
					marca_id,
					filtros_rel2,
					FLAG,
					filtro_active
				)
				VALUES
				(
					"0",
					"0",
					"'.$new_id.'",
					"'.$fam_id1.'",
					"'.$fam_id2.'",
					"'.$fam_id3.'",
					"'.$marca.'",
					"",
					1,
					1
				)
			';
		}

		InsertProdLinguas($new_id,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$texto4,$cabecalho_texto5,$texto5);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	
	return array('res' => $res, 'prod_id' => $new_id);
}
function InsertProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$texto4,$cabecalho_texto5,$texto5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho1=str2SQLstr($cabecalho);
	$URL1=str2SQLstr($URL);
	$subtitulo=$_POST['subtitulo'];
	if(isset($_POST['tempo_entrega'])) $tempo_entrega = $_POST['tempo_entrega']; else $tempo_entrega = '';
	$sql="insert into products_lang (ID_PAI,Cabecalho,Subtitulo,URL,Texto1,Texto2,Texto3,Tamanho,Cor,Lang,Texto4,Cabecalho_Texto5,Texto5,tempo_entrega) values ($id_pai,$cabecalho1,'$subtitulo',$URL1,'$texto1','$texto2','$texto3','$tamanho','$cor','$admin_lang','$texto4','$cabecalho_texto5','$texto5','$tempo_entrega')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
    return($res);
}
function ChangeProd($id,$id_org,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$Apagar_img,$tem_foto,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	
	if(!$m_lang) $m_lang=$admin_lang;
	$altera=0;
	
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from products WHERE ID="'.$id.'"');
	if(!$rs->EOF())
	{
		$altera=1;
	}
	$rs->db_close();

	if(!$altera)return InsertNewProd($id_org,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff);
	else
	{	
		$id_org1=str2SQLstr($id_org);
		$ordem1=str2SQLstr($ordem);
		$cabecalho1=str2SQLstr($cabecalho);
		$ref1=str2SQLstr($ref);
    	$preco_prec=str2SQLstr($preco_prec);
		$preco11=str2SQLstr($preco1);
		$preco21=str2SQLstr($preco2);
		$precopromo11=str2SQLstr($precopromo1);
		$precopromo21=str2SQLstr($precopromo2);
		$margem1=str2SQLstr($margem);
		$margem_activa1=str2SQLstr($margem_activa);
		$destaque1=str2SQLstr($destaque);
		$novidade1=str2SQLstr($novidade);
		$promocao1=str2SQLstr($promocao);
		$home1=str2SQLstr($home);
		$saldo=str2SQLstr($saldo);
		$aproved=str2SQLstr($aproved);
		$tipo=str2SQLstr($tipo);
		$stock_txt1=str2SQLstr($stock_txt);
		$strSearchWords=str2SQLstr($strSearchWords);
		$bonus = $_POST['pontos'].$_POST['tpoints'];
		
		if(isset($_POST['carrinho'])) $carrinho=$_POST['carrinho'];	else $carrinho=0;
		if(isset($_POST['estados_produto'])) $estados_produto=$_POST['estados_produto']; else $estados_produto=0;
		
		$personalizacao_on = '';
		if(isset($_POST['personalizacao']))
		{ 
			$personalizacao = $_POST['personalizacao'];
			for($a=0; $a<count($personalizacao); $a++)
			{
				$personalizacao_on .= $personalizacao[$a].';';
			}
		}
		
		$sql="
				UPDATE products 
				SET 
					Ordem=$ordem1,
					Ref=$ref1,
					Preco1=$preco11,
					Preco2=$preco21,
					PrecoPromo1=$precopromo11,
					PrecoPromo2=$precopromo21,
					Preco_prec=$preco_prec,
					Margem=$margem1,
					MargemActiva=$margem_activa1,
					StockTxt=$stock_txt1,
					Destaque=$destaque1,
					Novidade=$novidade1,
					Promo=$promocao1,
					Home=$home1,
					Saldo=$saldo,
					Aproved=$aproved,
					Carrinho=$carrinho,
					Tipo=$tipo,
					Hottags=$strSearchWords,
					ID_Marca='$marca',
					Peso='$peso',
					usado='$usado',
					Medidas='$medidas',
					PrecoCusto='$precocusto',
					Qtd='$stock',
					Download='$download',
					decumentario='$decumentario',
					Sync='$sync',
					preco_revenoff='$preco_revenoff',
					peso_cliente='".$_POST['peso_cliente']."',
					descontopreco='".$_POST['descontopreco']."',
					points='".$bonus."',
					personalizacao='".$personalizacao_on."',
					estados_produto='".$estados_produto."'
				WHERE 
					ID=$id
			";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			// $res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS products_m');//limpar a tabela de ram
			products_images_save($id,$Img_peq,$tem_foto,$Apagar_img);
			products_objs_save($id);
			
			products_families_save($id,$id_org);			
			
			$fams = BuscarArvoreFams_F($id_org);
			if(isset($arr_fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = 0;
			if(isset($arr_fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
			if(isset($arr_fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;

			$rs3 = new DataObj;
			$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
			if($rs3->EOF())
			{			
		
				$sql = '
						INSERT INTO filters_products
						(
							cat_id, valor_id, prod_id, fam_id1, fam_id2, fam_id3, marca_id, filtros_rel2, FLAG, filtro_active
						)
						VALUES
						(
							"0", "0", "'.$id.'", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$marca.'", "", 1, 1
						)
					';
		
				$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);				
			}
			else
			{
				$sql ='UPDATE filters_products SET fam_id1="'.$fam_id1.'", fam_id2="'.$fam_id2.'", fam_id2="'.$fam_id3.'", marca_id="'.$marca.'" where prod_id = "'.$id.'"';
				$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);				
			}
		}
		db_closedb_conection($db_link);		
		$res =ChangeProdLinguas($id,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$cabecalho_texto5,$texto5);
		exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
  }
}
function ChangeProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$cabecalho_texto5,$texto5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang) $m_lang=$admin_lang;
	if($id_org== $id) $id_org=0;	
	$altera=0;
	$rs->db_open($db_link,$DB_name,"SELECT * FROM products_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())
	{
		$altera=1; $id_video=$rs->db_data["ID"];
	}
	$rs->db_close();
	
	if(!$altera)return InsertProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$cabecalho_texto5,$texto5);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$altera_download=0; if($Download_peq["name"]){ $altera_download=1; $pos=strrpos($Download_peq["name"],"."); $tamanho=substr($Download_peq["name"],$pos+1,strlen($Download_peq["name"])+1-$pos); }		
		if(!$tem_download && $Apagar_download){$ficheiro=$id_video."_download.".$tamanho; unlink($img_path_download.$ficheiro); if(!$altera_download) $tamanho='';}		
		if($altera_download) copy($Download_peq["temp"],$img_path_download . $id_video . "_download." . $tamanho);	
		
		$obj=new DataObj;
		$cabecalho1=str2SQLstr($cabecalho);
		$URL1=str2SQLstr($URL);

		$subtitulo=addslashes($_POST['subtitulo']);
		
		if(isset($_POST['tempo_entrega'])) $tempo_entrega = $_POST['tempo_entrega']; else $tempo_entrega = '';
		
		$sql="UPDATE products_lang 
				SET 
					Subtitulo='$subtitulo', 
					Cabecalho=$cabecalho1, 
					URL=$URL1, 
					Texto1='$texto1', 
					Texto2='$texto2', 
					Texto3='$texto3', 
					Tamanho='$tamanho',
					Cor='$cor',
					Texto4='$texto4',
					Cabecalho_Texto5='$cabecalho_texto5',
					Texto5='$texto5',
					tempo_entrega='$tempo_entrega'
				WHERE 
					ID_PAI=$id_pai 
					AND 
					Lang='$admin_lang'
			";

		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function DeleteProd($id,$img_path)
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select ID_Prod from products WHERE ID="'.$id.'"');
	if(!$rs->EOF())
	{
		$marca=$rs->db_data['ID_Prod'];
	}
	$rs->db_close();
	$rs->db_open($db_link,$DB_name,'Select ID_ORG from products_families_obj WHERE ID_PROD="'.$id.'"');
	if(!$rs->EOF())
	{
		$id_org=$rs->db_data['ID_ORG'];
	}
	$rs->db_close();

	$res=Runquery($db_link,$DB_name,"DELETE FROM products WHERE ID=$id");
	if($res)
	{
		$sql2="DELETE FROM products_lang WHERE ID_PAI=".$id;
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2="DELETE FROM products_families_obj WHERE ID_PROD='".$id."'";
		$res2=Runquery($db_link,$DB_name,$sql2);
		$rs->db_open($db_link,$DB_name,'SELECT * FROM products_images WHERE ID_ORG="'.$id.'"');
		while(!$rs->EOF())
		{
			$img_ext=$rs->db_data['img_ext'];
			$ordem=$rs->db_data['Ordem']; 
			$ficheiro_original=$id."_".$ordem.".".$img_ext;
			$ficheiro_cart=$id."_".$ordem."_cart.".$img_ext;
			$ficheiro_med=$id."_".$ordem."_med.".$img_ext;
			$ficheiro_new=$id."_".$ordem."_new.".$img_ext;
			unlink($img_path.$ficheiro_original);
			unlink($img_path.$ficheiro_cart);
			unlink($img_path.$ficheiro_med);
			unlink($img_path.$ficheiro_new);
			
			$rs->db_next();
		}
		$rs->db_close();
		$sql2='DELETE FROM products_images WHERE ID_ORG="'.$id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2='DELETE FROM products_testemunhos WHERE ID_PROD="'.$id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2='DELETE FROM products_promocoes WHERE ID_Prod="'.$id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2='DELETE FROM products_destaques WHERE ID_Prod="'.$id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		$rs->db_open($db_link,$DB_name,'SELECT * FROM products_objs WHERE obj_type_id="'.$id.'"');
		while(!$rs->EOF())
		{
			$obj_id=$rs->db_data['obj_id'];
			$sql2='DELETE FROM products_objs_lang WHERE ID_PAI="'.$obj_id.'"';
			$res2=Runquery($db_link,$DB_name,$sql2);
			$rs->db_next();
		}
		$rs->db_close();
		$sql2='DELETE FROM products_objs WHERE obj_type_id="'.$obj_id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2='DELETE FROM filters_products WHERE prod_id="'.$id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		
		$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS products_m');//limpar a tabela de ram
		$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS products_objs_m');//limpar a tabela de ram
	}
	else echo db_error($db_link);
	
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');

	// passthru('(php '.$GLOBALS['site_shell_url'].'rmas/filters/cache/do_familias.php  > /dev/null &)&&(php '.$GLOBALS['site_shell_url'].'rmas/filters/cache/do_marcas.php  > /dev/null &)');	

	// $exe_file = $GLOBALS['site_shell_url'] . "crons/gera_produtospromocao.php";	
	// exec('php '.$exe_file.' > /dev/null &');

	// $exe_file = $GLOBALS['site_shell_url'] . "crons/gera_produtosdestaque.php";	
	// exec('php '.$exe_file.' > /dev/null &');
	
	
	return($res);
}
function InsertNewFam($id_org=0,$ordem,$cabecalho,$cabecalho2,$URL,$texto1,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$preco_revenoff)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$new_id = 0;
	$altera_img  = 0; if($Img_peq1["name"]){ $altera_img  = 1;  $pos=strrpos($Img_peq1["name"],"."); $img_ext =  substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);  }
	$altera_img2 = 0; if($Img_peq2["name"]){ $altera_img2 = 1; $pos=strrpos($Img_peq2["name"],".");  $img2_ext = substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos); }
	$altera_img3 = 0; if($Img_peq3["name"]){ $altera_img3 = 1; $pos=strrpos($Img_peq3["name"],".");  $img3_ext = substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos); }
	$ordem1=str2SQLstr($ordem);
	$margemcli=str2SQLstr($margemcli);
	$margemrev=str2SQLstr($margemrev);
	$cor=str2SQLstr($cor);
	$visivel=str2SQLstr($visivel);
	$destaque=str2SQLstr($destaque);	
	$Portes=$_POST['Portes'];	
	if(!$id_org)$id_org=0;
	if(!isset($_POST['m_media']))$ID_Media=0;
	else $ID_Media = $_POST['m_media'];
	$sql="insert into products_families (Portes,ID_ORG,Ordem,img_ext,img2_ext,img3_ext,MargemCli,MargemRev,cor,visivel,Destaque,cat1,cat2,cat3,Download,ID_Media,preco_revenoff) values ('$Portes','$id_org',$ordem1,'$img_ext','$img2_ext','$img3_ext',$margemcli,$margemrev,$cor,$visivel,$destaque,'$cat1','$cat2','$cat3','$download','$ID_Media','$preco_revenoff')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copy($Img_peq1["temp"],$img_path.$new_id."_1.".$ext1);
		if($altera_img2) copy($Img_peq2["temp"],$img_path.$new_id."_2.".$ext2);
		if($altera_img3) copy($Img_peq3["temp"],$img_path.$new_id."_3.".$ext3);
			
		InsertNewFamLinguas($new_id,$cabecalho,$cabecalho2,$URL,$texto);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	
	exec('php '.$GLOBALS['site_shell_url'].'core/crons/gera_sitemap.php > /dev/null &');
	
	return($res);
}
function InsertNewFamLinguas($id_pai,$cabecalho,$cabecalho2,$URL,$texto)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho1=str2SQLstr($cabecalho);
	$cabecalho2=str2SQLstr($cabecalho2);
	$texto1=$texto;
	$URL1=str2SQLstr($URL);
	$sql="insert into products_families_lang (ID_PAI,Cabecalho,Cabecalho2,URL,Texto,Lang) values ($id_pai,$cabecalho1,$cabecalho2,$URL1,'$texto1','$admin_lang')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
    return($res);
}
function products_objs($obj_id=0,$sync=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$sql="SELECT * FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$obj_id."' AND obj_type_id > 0 and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs.obj_id ASC";
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		
		$t = $rs->db_data['obj_val2'];
		
		$obj_id_p[$t]=$rs->db_data['obj_id'];
		$obj_desc[$t]=$rs->db_data['Cabecalho'];
		
		$obj_iva[$t] = number_format(((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']), 4, '.','');
		$obj_ivax = ((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']);
		$obj_preco[$t]=$rs->db_data['obj_pvp'];
		$obj_precosiva[$t] = number_format(($rs->db_data['obj_pvp'] / (1 + ($obj_ivax / 100))), 4, '.','');		
		
		$obj_stock[$t]=$rs->db_data['obj_stock'];
		
		$obj_promo[$t]=$rs->db_data['obj_promo'];		
		$obj_promosiva[$t] = number_format(($rs->db_data['obj_promo'] / (1 + ($obj_ivax / 100))), 4, '.','');	
		
		$obj_promo_ativo[$t]=$rs->db_data['obj_promo_ativo'];
		$obj_outlet[$t]=$rs->db_data['obj_outlet'];
		$obj_destaque[$t]=$rs->db_data['obj_destaque'];
		$obj_travelsize[$t]=$rs->db_data['obj_travelsize'];
		$obj_novo[$t]=$rs->db_data['obj_novo'];
		$obj_ativo[$t]=$rs->db_data['obj_ativo'];
		$obj_qtdmin[$t]=$rs->db_data['obj_qtdmin'];
		$obj_destaque_data[$t]=$rs->db_data['obj_destaque_data'];
		$obj_novo_data[$t]=$rs->db_data['obj_novo_data'];
		$obj_outlet_data[$t]=$rs->db_data['obj_outlet_data'];
		$obj_promo_data[$t]=$rs->db_data['obj_promo_data'];
		
		$obj_dia[$t]=$rs->db_data['obj_dia'];
		$obj_dia_data[$t]=$rs->db_data['obj_dia_data'];
		
		$rs->db_next();
	}
	$rs->db_close();
	$return  = '<table id="IDTabelaPrecos" style="margin:0 auto;" border="0">';
	$return .= '	
			<tr>
			<td align="center" width="140px" style="font-size:10px;text-align:center">&nbsp;</td>
			<td align="center" width="40px" style="font-size:10px;text-align:center"><b>IVA</b><span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small; vertical-align:super;">1</span></td>
			<td align="center" width="70px" style="font-size:10px;text-align:center"><b>PVP c/IVA</b></td>
			<td align="center" width="70px" style="font-size:10px;text-align:center"><b>PVP s/IVA</b></td>
			<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Stock</b></td>			
			<td align="center" width="70px" style="font-size:10px;text-align:center"><b>PVP Promo c/IVA</b></td>
			<td align="center" width="70px" style="font-size:10px;text-align:center"><b>PVP Promo s/IVA</b></td>
			<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Qtd Min</b></td>
			<td>&nbsp;</td>
			</tr>
			';
	
	// $novo_prod = 0;
	if(count($obj_desc)<=0)
	{
		// $sync = 1;
		$obj_id_p[] = 0;
		$obj_desc[] = '';
		
		$obj_iva[] = number_format($GLOBALS['iva_default'], 4, '.','');
		$obj_ivax = $GLOBALS['iva_default'];
		$obj_preco[] = 0;
		$obj_precosiva[] = 0;
		
		$obj_stock[] = 0;
		
		$obj_promo[] = 0;
		$obj_promosiva[] = 0;
		
		$obj_promo_ativo[] = 0;
		$obj_outlet[] = 0;
		$obj_destaque[] = 0;
		$obj_travelsize[] = 0;
		$obj_novo[] = 0;
		$obj_ativo[] = 0;
		$obj_qtdmin[] = 0;
		$obj_destaque_data[] = '';
		$obj_novo_data[] = '';
		$obj_outlet_data[] = '';
		$obj_promo_data[] = '';
		$obj_dia[] = '';
		$obj_dia_data[] = '';
	
	}
	
	// SE NÃO EXISTIR SICRONIZAÇÃO CONFIGURADA NO SETTINGS, OS CAMPOS ESTÃO SEMPRE DISPONIEIS
	if(isset($GLOBALS['prod_conf']) && $GLOBALS['prod_conf']['tem_sincro'] == 0) $sync = 1;
	
	
	$guarda_id=0;
	$count=0;
	$a=0;
	// for($a=0; $a<count($obj_desc); $a++)
	// {
		$return .= '<tr id="rem_'.$a.'" class="precos_line">';
		$return .= '<td style="width:70px;background-color:#CCC" align="center">NORMAL</td>';
		$return .= '<td style="width:40px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_iva[]" id="obj_iva'.$a.'"  style="WIDTH:50px" maxlength="255" value="'.$obj_iva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'0\', \''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'0\', \''.$obj_iva[$a].'\')" /></td>';
		$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:60px" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'1\', \''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'1\', \''.$obj_iva[$a].'\')" /></td>';
		$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_precosiva[]" id="obj_precosiva'.$a.'"  style="WIDTH:60px" maxlength="255" value="'.$obj_precosiva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'2\', \''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'2\', \''.$obj_iva[$a].'\')" /></td>';
		$return .= '<td style="width:50px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:50px" maxlength="255" value="'.$obj_stock[$a].'"  /></td>';
		$return .= '<td style="width:70px;background-color:#CCC" align="center"><input type="hidden" value="'.$a.'" name="obj_val2[]" id="obj_val2'.$a.'" style="WIDTH:130px; " /><input type="hidden" value="'.$obj_id_p[$a].'" name="obj_id[]" id="obj_id'.$a.'" style="WIDTH:130px; " /><input class="box" type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:60px; " maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'3\', \''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'3\', \''.$obj_iva[$a].'\')" /></td>';
		$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_promosiva[]" id="obj_promosiva'.$a.'" style="WIDTH:60px; " maxlength="255" value="'.$obj_promosiva[$a].'"  onkeypress="handleKeyPress(\''.$a.'\',\'4\', \''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'4\', \''.$obj_iva[$a].'\')" /></td>';
		$return .= '<td style="width:50px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_qtdmin[]" id="obj_qtdmin'.$a.'" style="WIDTH:40px; " maxlength="255" value="'.number_format($obj_qtdmin[$a],0).'" onkeypress="handleKeyPress(this)" /></td>';
		$return .= '<td>&nbsp;</td></tr>';
		// $return .= '</table><br>';
		
		
		
		
	$sql="SELECT ID, Descricao FROM grupos_clientes";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		// $return  .= '<br><table id="IDTabelaPrecos" style="margin:0 auto;" border="0">';
			
		
		while(!$rs->EOF())
		{
			$a = $rs->db_data['ID'];
			$Descricao = $rs->db_data['Descricao'];
			$return .= '<tr id="rem_'.$a.'" class="precos_line">';
			$return .= '<td style="width:70px;background-color:#CCC" align="center">'.$Descricao.'</td>';
			$return .= '<td style="width:40px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_iva[]" id="obj_iva'.$a.'"  style="WIDTH:50px" maxlength="255" value="'.$obj_iva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'0\', \''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'0\', \''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:60px" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'1\', \''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'1\', \''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_precosiva[]" id="obj_precosiva'.$a.'"  style="WIDTH:60px" maxlength="255" value="'.$obj_precosiva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'2\', \''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'2\', \''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:50px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:50px" maxlength="255" value="'.$obj_stock[$a].'"  /></td>';		
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input type="hidden" value="'.$a.'" name="obj_val2[]" id="obj_val2'.$a.'" style="WIDTH:130px; " /><input type="hidden" value="'.$obj_id_p[$a].'" name="obj_id[]" id="obj_id'.$a.'" style="WIDTH:130px; " /><input class="box" type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:60px; " maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'3\', \''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'3\', \''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_promosiva[]" id="obj_promosiva'.$a.'" style="WIDTH:60px; " maxlength="255" value="'.$obj_promosiva[$a].'"  onkeypress="handleKeyPress(\''.$a.'\',\'4\', \''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'4\', \''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:50px;background-color:#CCC" align="center"><input class="box" type="text" name="obj_qtdmin[]" id="obj_qtdmin'.$a.'" style="WIDTH:40px; " maxlength="255" value="'.number_format($obj_qtdmin[$a],0).'" onkeypress="handleKeyPress(this)" /></td>';
			$return .= '<td>&nbsp;</td></tr>';
			
			$rs->db_next();
		}
	
	}
	$rs->db_close();
		$return .= '</table><br>';		
	$a=0;	
		$return .= '<table id="IDTabelaPrecos" style="margin:0 auto;" border="0"><tr>';	
		// $return .= '<td align="center" width="80px" height="25" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Ocasião</b></td>';
		$return .= '<td align="center" width="20px" height="25" style="font-size:10px;text-align:center" rowspan="10"><br><br><br><span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small; vertical-align:super;">2</span></td>';
		$return .= '<td align="center" width="80px" height="25" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Destaque</b></td>';
		$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Outlet</b></td>';
		$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Promoção</b></td>';
		$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Novo</b></td>';
		$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center"><b>Produto do Dia</b></td>';
		// $return .= '<td align="center" style="font-size:10px;text-align:center"><b>Ativo</b></td>';
		
		$return .= '</tr>';
		$return .= '<tr id="rem_'.$a.'" class="precos_line">';
		
		
		$return .= '<td style="width:100px;background-color:#CCC;border-right:#FFF 1px dotted" align="center" ><input value="'.$obj_destaque[$a].'" type="hidden" name="obj_destaque[]" id="obj_destaque'.$a.'"><input onclick="document.form.obj_destaque'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
		if($obj_destaque[$a]) $return .= ' checked="checked" ';
		$return .= '></td>';
		
		$return .= '<td style="width:100px;background-color:#CCC;border-right:#FFF 1px dotted" align="center"><input value="'.$obj_outlet[$a].'" type="hidden" name="obj_outlet[]" id="obj_outlet'.$a.'"><input onclick="document.form.obj_outlet'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
		if($obj_outlet[$a]) $return .= ' checked="checked" ';
		$return .= '></td>';
		
		$return .= '<td style="width:100px;background-color:#CCC;border-right:#FFF 1px dotted" align="center"><input value="'.$obj_promo_ativo[$a].'" type="hidden" name="obj_p_ativo[]" id="obj_p_ativo'.$a.'"><input onclick="document.form.obj_p_ativo'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
		if($obj_promo_ativo[$a]) $return .= ' checked="checked" ';
		$return .= '></td>';
		
		$return .= '<td style="width:100px;background-color:#CCC" align="center">';
		$return .= '<input value="'.$obj_novo[$a].'" type="hidden" name="obj_novo[]" id="obj_novo'.$a.'"><input onclick="document.form.obj_novo'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
		if($obj_novo[$a]) $return .= ' checked="checked" ';
		$return .= '><input value="1" type="hidden" name="obj_ativo[]" id="obj_ativo'.$a.'"></td>';
		
		$return .= '<td style="width:100px;background-color:#CCC" align="center">';
		$return .= '<input value="'.$obj_dia[$a].'" type="hidden" name="obj_dia[]" id="obj_dia'.$a.'"><input onclick="document.form.obj_dia'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
		if($obj_dia[$a]) $return .= ' checked="checked" ';
		$return .= '></td>';
		
		$return .= '</tr><tr>';
		
		// $return .= '<td style="width:50px;background-color:#CCC;border-right:#FFF 1px dotted" align="center">Acaba <input class="box dropdate" type="text" name="obj_ocasiao_data[]" id="obj_ocasiao_data'.$a.'" style="width:80px !important;font-size:10px" maxlength="255" value="'.$obj_ocasiao_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br><input class="box dropdate" type="text" name="obj_destaque_data[]" id="obj_destaque_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_destaque_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br> <input class="box dropdate" type="text" name="obj_outlet_data[]" id="obj_outlet_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_outlet_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br> <input class="box dropdate" type="text" name="obj_promo_data[]" id="obj_promo_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_promo_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>Acaba em<br><input class="box dropdate" type="text" name="obj_novo_data[]" id="obj_novo_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_novo_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>No dia<br><input class="box dropdate" type="text" name="obj_dia_data[]" id="obj_dia_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_dia_data[$a].'" /></td>';
		$return .= '</tr>';

		// $count++;
	// }	
	
	$return .= '</table>';
		
	$return .= '
			<br /><br />
			<div style="line-height:8px">
			&nbsp;&nbsp;<span style="color:#FF0000;font-weight:bold;font-size:x-small; vertical-align:super;">(1) - O campo IVA serve somente para efeitos de calculo no carrinho</span></br>
			&nbsp;&nbsp;<span style="color:#FF0000;font-weight:bold;font-size:x-small; vertical-align:super;">(2) - Os campos de data podem não estar ativos no projeto</span></br>
			
			</div>
			</br></br>';

	return $return;
}


function products_objs_filtros($obj_id=0, $obj_combo='', $a=0)
{	
	// echo $obj_combo;
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$site_http_url;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	
	
	$field_array = array();
	$fields = db_list_fields($DB_name, 'products_objs_combo', $db_link);
	$columns = db_num_fields($fields);
	for ($i=0; $i < $columns; $i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('obj_ref', $field_array)){$res = Runquery($db_link, $DB_name, 'ALTER TABLE products_objs_combo ADD obj_ref VARCHAR(255)'); }
	
	
	$rs = new DataObj;$rs2=new DataObj;
	$sql = 'SELECT obj_id, obj_iva, obj_pvp, obj_stock, obj_promo, obj_ref FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$obj_id.'" AND obj_combo="'.$obj_combo.'"';
	$rs->db_open($db_link,$DB_name,$sql); 
	if(!$rs->EOF())
	{		
		$obj_id_p = $rs->db_data['obj_id'];		
		$obj_ref = $rs->db_data['obj_ref'];		
		$obj_iva = number_format(((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']), 4, '.','');
		$obj_ivax = ((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']);
		$obj_preco = $rs->db_data['obj_pvp'];
		$obj_precosiva = number_format(($rs->db_data['obj_pvp'] / (1 + ($obj_ivax / 100))), 4, '.','');
		$obj_stock = $rs->db_data['obj_stock'];
		$obj_promo = $rs->db_data['obj_promo'];
		$obj_promosiva = number_format(($rs->db_data['obj_promo'] / (1 + ($obj_ivax / 100))), 4, '.','');
	}
	else
	{
		$obj_iva = number_format(($GLOBALS['iva_default']), 4, '.','');
	}
	$rs->db_close();

	$return  = '<table id="IDTabelaPrecos" style="margin:0px;" border="0">';	
	$return .= '<tr>	
	<td align="center" style="font-size:10px;text-align:center"><b>REF</b></td>
	<td align="center" style="font-size:10px;text-align:center"><b>IVA</b><span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small; vertical-align:super;">1</span></td>
	<td align="center" style="font-size:10px;text-align:center"><b>PVP c/IVA</b></td>
	<td align="center" style="font-size:10px;text-align:center"><b>PVP s/IVA</b></td>
	<td align="center" style="font-size:10px;text-align:center"><b>Stock</b></td>
	<td align="center" style="font-size:10px;text-align:center"><b>PVP Promo c/IVA</b></td>
	<td align="center" style="font-size:10px;text-align:center"><b>PVP Promo s/IVA</b></td>	
	<td>&nbsp;</td>
	</tr>';	
	$return .= '<tr>';	
	$return .= '<td align="center"><input class="box" type="text" name="c_obj_ref[]" id="c_obj_ref'.$a.'"  style="WIDTH:100px" maxlength="255" value="'.$obj_ref.'" /></td>';
	$return .= '<td align="center"><input type="hidden" value="'.$obj_combo.'" name="c_obj_combo[]" id="c_obj_combo'.$a.'" style="WIDTH:130px;" /><input type="hidden" value="'.$obj_id_p.'" name="c_obj_id[]" id="c_obj_id'.$a.'" style="WIDTH:130px;" /><input class="box" type="text" name="c_obj_iva[]" id="c_obj_iva'.$a.'"  style="WIDTH:50px" maxlength="255" value="'.$obj_iva.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'0\', \''.$obj_iva.'\')" onkeyup="handleKeyPress_combo(\''.$a.'\',\'0\', \''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="box" type="text" name="c_obj_preco[]" id="c_obj_preco'.$a.'"  style="WIDTH:90px" maxlength="255" value="'.$obj_preco.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'1\', \''.$obj_iva.'\')"  onkeyup="handleKeyPress_combo(\''.$a.'\',\'1\', \''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="box" type="text" name="c_obj_precosiva[]" id="c_obj_precosiva'.$a.'"  style="WIDTH:90px" maxlength="255" value="'.$obj_precosiva.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'2\', \''.$obj_iva.'\')" onkeyup="handleKeyPress_combo(\''.$a.'\',\'2\', \''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="box" type="text" name="c_obj_stock[]" id="c_obj_stock'.$a.'" style="WIDTH:70px" maxlength="255" value="'.$obj_stock.'" /></td>';
	$return .= '<td align="center"><input class="box" type="text" name="c_obj_promo[]" id="c_obj_promo'.$a.'" style="WIDTH:90px; " maxlength="255" value="'.$obj_promo.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'3\', \''.$obj_iva.'\')"  onkeyup="handleKeyPress_combo(\''.$a.'\',\'3\', \''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="box" type="text" name="c_obj_promosiva[]" id="c_obj_promosiva'.$a.'" style="WIDTH:90px; " maxlength="255" value="'.$obj_promosiva.'"  onkeypress="handleKeyPress_combo(\''.$a.'\',\'4\', \''.$obj_iva.'\')"  onkeyup="handleKeyPress_combo(\''.$a.'\',\'4\', \''.$obj_iva.'\')" /></td>';
	$return .= '<td><img onclick="get_precos(\''.$a.'\');" style="cursor:pointer" title="copiar valores da linha principal" src="avancada/refresh.png" /></td></tr>';
	$return .= '</table>';
	
	return $return;
}


function products_acabamentos_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	
	$acabamento = $_POST['acabamento'];	
	$objeto = $_POST['objeto'];	
	$ac_ativo = $_POST['ac_ativo'];
	$ac_ordem = $_POST['ac_ordem'];
	
	
	$rs = new DataObj; $rs2=new DataObj;
	
	$sql = "
			UPDATE products_acabamentos_save 
			SET 
				 flag = 0
			WHERE 
				prod_id='".$ID_PROD."'
			";
			$res = Runquery($db_link, $DB_name, $sql);

	for($a=0; $a<count($acabamento); $a++)
	{
		if($acabamento[$a] > 0)
		{
			$sql = "SELECT save_id FROM products_acabamentos_save WHERE prod_id='".$ID_PROD."' AND acabamento_id='".$acabamento[$a]."' AND objeto_id='".$objeto[$a]."'";
			// echo $sql.'<br>';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$save_id = $rs->db_data['save_id'];
				$sql = "
						UPDATE products_acabamentos_save 
						SET 
							ativo='".$ac_ativo[$a]."',
							ordem='".$ac_ordem[$a]."',
							flag=1
						WHERE 
							prod_id='".$ID_PROD."' 
							AND acabamento_id='".$acabamento[$a]."'
							AND objeto_id='".$objeto[$a]."'
						";
				$res = Runquery($db_link, $DB_name, $sql);
			}
			else
			{
				$sql = "
						INSERT INTO 
							products_acabamentos_save 
							( 
								acabamento_id,
								objeto_id,
								prod_id,
								ativo,
								ordem, 
								flag
							)
							VALUES
							(
								'".$acabamento[$a]."',
								'".$objeto[$a]."',
								'".$ID_PROD."',			
								'".$ac_ativo[$a]."',
								'".$ac_ordem[$a]."',
								1
							)
						";
				$res = Runquery($db_link, $DB_name, $sql);
				$obj_id = db_get_last_insert_id($db_link);
				
			}
			
			// echo $sql.';<br>';
			$rs->db_close();	
		}
	}

		$sql = "
			DELETE FROM 
				products_acabamentos_save 
			WHERE 
				prod_id='".$ID_PROD."'
				AND flag=0
			";
			$res = Runquery($db_link, $DB_name, $sql);
}

function products_objs_filtros_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	
	$objv_id = $_POST['c_obj_id'];
	$obj_iva = $_POST['c_obj_iva'];
	$obj_preco = $_POST['c_obj_preco'];
	$obj_stock = $_POST['c_obj_stock'];
	$obj_promo = $_POST['c_obj_promo'];
	$obj_combo = $_POST['c_obj_combo'];
	$obj_ref = $_POST['c_obj_ref'];
	$rs = new DataObj; $rs2=new DataObj;
	
	$sql = "
			UPDATE products_objs_combo 
			SET 
				 obj_val = 0
			WHERE 
				obj_type='products' 
				AND obj_type_id='".$ID_PROD."'
			";
			$res = Runquery($db_link, $DB_name, $sql);

	for($a=0; $a<count($objv_id); $a++)
	{
		$sql = "SELECT obj_id FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' AND obj_id='".$objv_id[$a]."' AND obj_combo='".$obj_combo[$a]."'";
		// echo $sql.'<br>';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id = $rs->db_data['obj_id'];
			$sql = "
					UPDATE products_objs_combo 
					SET 
						obj_iva='".$obj_iva[$a]."',
						obj_pvp='".$obj_preco[$a]."',
						obj_stock='".$obj_stock[$a]."',
						obj_promo='".$obj_promo[$a]."',
						obj_promo_ativo='".$obj_p_ativo[$a]."',
						obj_combo = '".$obj_combo[$a]."',
						obj_ref = '".$obj_ref[$a]."',
						obj_val = 1
					WHERE 
						obj_id='".$obj_id."' 
						AND obj_type='products' 
						AND obj_type_id='".$ID_PROD."'
					";
			$res = Runquery($db_link, $DB_name, $sql);
		}
		else
		{
			$sql = "
					INSERT INTO 
						products_objs_combo 
						( 
							obj_type,
							obj_type_id,
							obj_iva, 
							obj_pvp, 
							obj_stock,
							obj_promo,
							obj_combo,
							obj_ref,
							obj_val
						)
						VALUES
						(
							'products',
							'".$ID_PROD."',			
							'".$obj_iva[$a]."',
							'".$obj_preco[$a]."',
							'".$obj_stock[$a]."',
							'".$obj_promo[$a]."',
							'".$obj_combo[$a]."',
							'".$obj_ref[$a]."',
							1
						)
					";
			$res = Runquery($db_link, $DB_name, $sql);
			$obj_id = db_get_last_insert_id($db_link);
			
		}
		
		// echo $sql.';<br>';
		$rs->db_close();				
	}

		$sql = "
			DELETE FROM 
				products_objs_combo 
			WHERE 
				obj_type='products' 
				AND obj_type_id='".$ID_PROD."'
				AND obj_val=0
			";
			$res = Runquery($db_link, $DB_name, $sql);
}
function products_objs_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$objv_id=$_POST['obj_id'];
	$obj_val2=$_POST['obj_val2'];
	$obj_iva=$_POST['obj_iva'];
	$obj_preco=$_POST['obj_preco'];
	$obj_stock=$_POST['obj_stock'];
	$obj_promo=$_POST['obj_promo'];
	$obj_p_ativo=$_POST['obj_p_ativo'];
	$obj_ativo=$_POST['obj_ativo'];
	$obj_desc=$_POST['ref'];
	$obj_destaque=$_POST['obj_destaque'];
	$obj_outlet=$_POST['obj_outlet'];
	$obj_travelsize=$_POST['obj_travelsize'];
	$obj_novo=$_POST['obj_novo'];
	$obj_qtdmin=$_POST['obj_qtdmin'];
	$obj_destaque_data=$_POST['obj_destaque_data'];
	$obj_novo_data=$_POST['obj_novo_data'];
	$obj_outlet_data=$_POST['obj_outlet_data'];
	$obj_promo_data=$_POST['obj_promo_data'];
	$obj_dia=$_POST['obj_dia'];
	$obj_dia_data=$_POST['obj_dia_data'];
	$rs=new DataObj;$rs2=new DataObj;

	$destaque=0;
	$promocao=0;
	
	// echo 'count: '.count($obj_val2).'<br>';

	if(count($obj_val2) > 0)
	{
		for($a=0; $a<count($obj_val2); $a++)
		{
			$sql="SELECT obj_id FROM products_objs WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' and obj_val2='".$obj_val2[$a]."'";
			// echo $sql.'<br>';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$obj_id = $rs->db_data['obj_id'];
				$sql="UPDATE products_objs 
					SET 
						obj_iva='".$obj_iva[$a]."',
						obj_pvp='".$obj_preco[$a]."',
						obj_stock='".$obj_stock[$a]."',
						obj_promo='".$obj_promo[$a]."',
						obj_promo_ativo='".$obj_p_ativo[$a]."',
						obj_ativo='".$obj_ativo[$a]."',
						obj_destaque='".$obj_destaque[$a]."',
						obj_outlet='".$obj_outlet[$a]."',
						obj_travelsize='".$obj_travelsize[$a]."',
						obj_novo='".$obj_novo[$a]."',
						obj_qtdmin='".$obj_qtdmin[$a]."',
						obj_destaque_data='".$obj_destaque_data[$a]."',
						obj_novo_data='".$obj_novo_data[$a]."',
						obj_outlet_data='".$obj_outlet_data[$a]."',
						obj_promo_data='".$obj_promo_data[$a]."',
						obj_dia='".$obj_dia[$a]."',
						obj_dia_data='".$obj_dia_data[$a]."'						
					WHERE 
						obj_id='".$obj_id."' AND obj_type='products' AND obj_type_id='".$ID_PROD."'";
						// echo $sql;
				$res=Runquery($db_link,$DB_name,$sql);
				
			}
			else
			{
				$sql="
					INSERT INTO 
						products_objs 
						( 
							obj_type,
							obj_type_id,
							obj_iva, 
							obj_pvp, 
							obj_stock,
							obj_promo,
							obj_promo_ativo,
							obj_ativo,
							obj_destaque,
							obj_outlet,
							obj_travelsize,
							obj_novo,
							obj_qtdmin,
							obj_destaque_data,
							obj_novo_data,
							obj_outlet_data,
							obj_promo_data,
							obj_dia_data,
							obj_dia,
							obj_val2
						)
						VALUES
						(
							'products',
							'".$ID_PROD."',			
							'".$obj_iva[$a]."',
							'".$obj_preco[$a]."',
							'".$obj_stock[$a]."',
							'".$obj_promo[$a]."',
							'".$obj_p_ativo[$a]."',
							'".$obj_ativo[$a]."',
							'".$obj_destaque[$a]."',
							'".$obj_outlet[$a]."',
							'".$obj_travelsize[$a]."',
							'".$obj_novo[$a]."',
							'".$obj_qtdmin[$a]."',
							'".$obj_destaque_data[$a]."',
							'".$obj_novo_data[$a]."',
							'".$obj_outlet_data[$a]."',
							'".$obj_promo_data[$a]."',
							'".$obj_dia_data[$a]."',
							'".$obj_dia[$a]."',
							'".$obj_val2[$a]."'
						)
					";
				$res=Runquery($db_link,$DB_name,$sql);
				$obj_id=db_get_last_insert_id($db_link);
				
			}
			$rs->db_close();
// echo $sql.'<br>';
			// if($destaque==0 && $obj_destaque[$a]==1) $destaque=1;
			// if($outlet==0 && $obj_outlet[$a]==1) $outlet=1;
			// if($promocao==0 && $obj_p_ativo[$a]==1) $promocao=1;

			if($obj_id > 0)
			{
				$sql="SELECT * FROM products_objs_lang WHERE ID_PAI='".$obj_id ."' AND Lang='".$GLOBALS['admin_lang']."'";
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$obj_id2=$rs->db_data['ID'];
					$sql="UPDATE products_objs_lang SET Cabecalho='".$obj_desc."' WHERE ID='".$obj_id2."' AND ID_PAI='".$obj_id."' AND Lang='".$GLOBALS['admin_lang']."'";
					$res=Runquery($db_link,$DB_name,$sql);	
				}
				else
				{
					$sql="INSERT INTO products_objs_lang(Cabecalho,ID_PAI,Lang) VALUES('".$obj_desc."','".$obj_id."','".$GLOBALS['admin_lang']."')";
					$res=Runquery($db_link,$DB_name,$sql);
				}	
			}		
		}
	}
	else
	{
		$sql = "SELECT obj_id FROM products_objs WHERE obj_type='products' AND obj_type_id='".$ID_PROD."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id = $rs->db_data['obj_id'];		
		}
		else
		{
			$sql="
				INSERT INTO 
					products_objs 
					( 
						obj_type,
						obj_type_id,
						obj_iva, 
						obj_pvp, 
						obj_stock,
						obj_promo,
						obj_promo_ativo,
						obj_ativo,
						obj_destaque,
						obj_outlet,
						obj_travelsize,
						obj_novo,
						obj_qtdmin,
						obj_destaque_data,
						obj_novo_data,
						obj_outlet_data,
						obj_promo_data,
						obj_dia_data,
						obj_dia,
						obj_val2
					)
					VALUES
					(
						'products',
						'".$ID_PROD."',			
						'".$obj_iva[$a]."',
						'".$obj_preco[$a]."',
						'".$obj_stock[$a]."',
						'".$obj_promo[$a]."',
						'".$obj_p_ativo[$a]."',
						'".$obj_ativo[$a]."',
						'".$obj_destaque[$a]."',
						'".$obj_outlet[$a]."',
						'".$obj_travelsize[$a]."',
						'".$obj_novo[$a]."',
						'".$obj_qtdmin[$a]."',
						'".$obj_destaque_data[$a]."',
						'".$obj_novo_data[$a]."',
						'".$obj_outlet_data[$a]."',
						'".$obj_promo_data[$a]."',
						'".$obj_dia_data[$a]."',
						'".$obj_dia[$a]."',
						'0'
					)
				";
			$res = Runquery($db_link,$DB_name,$sql);
			$obj_id = db_get_last_insert_id($db_link);			
		}
		$rs->db_close();
		
		// if($destaque==0 && $obj_destaque[$a]==1) $destaque=1;
		// if($outlet==0 && $obj_outlet[$a]==1) $outlet=1;
		// if($promocao==0 && $obj_p_ativo[$a]==1) $promocao=1;

		if($obj_id > 0)
		{
			$sql="SELECT * FROM products_objs_lang WHERE ID_PAI='".$obj_id ."' AND Lang='".$GLOBALS['admin_lang']."'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$obj_id2=$rs->db_data['ID'];
				$sql="UPDATE products_objs_lang SET Cabecalho='".$obj_desc."' WHERE ID='".$obj_id2."' AND ID_PAI='".$obj_id."' AND Lang='".$GLOBALS['admin_lang']."'";
				$res=Runquery($db_link,$DB_name,$sql);	
			}
			else
			{
				$sql="INSERT INTO products_objs_lang(Cabecalho,ID_PAI,Lang) VALUES('".$obj_desc."','".$obj_id."','".$GLOBALS['admin_lang']."')";
				$res=Runquery($db_link,$DB_name,$sql);
			}	
		}
	}

	/* $sql="SELECT * FROM products_promocoes WHERE ID_Prod='".$ID_PROD ."'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		if($promocao==0){
			//produto ja nao esta em promocao
			$sql="DELETE from products_promocoes WHERE ID_Prod='".$ID_PROD ."'";
			$res=Runquery($db_link,$DB_name,$sql);
		}
	}
	else
	{
		if($promocao==1){
			//adicionar produto em promocao
			$sql="INSERT INTO products_promocoes(ID_Prod,Ordem) VALUES ('".$ID_PROD ."',0)";
			$res=Runquery($db_link,$DB_name,$sql);
		}
	}	

	$sql="SELECT * FROM products_destaques WHERE ID_Prod='".$ID_PROD ."'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		if($destaque==0){
			//produto ja nao esta em destaque
			$sql="DELETE from products_destaques WHERE ID_Prod='".$ID_PROD ."'";
			$res=Runquery($db_link,$DB_name,$sql);
		}
	}
	else
	{
		if($destaque==1){
			//adicionar produto em destaque
			$sql="INSERT INTO products_destaques(ID_Prod,Ordem) VALUES ('".$ID_PROD ."',0)";
			$res=Runquery($db_link,$DB_name,$sql);
		}
	}	 */
}

function show_easy_create()
{
	global $browser,$ID;
	if(($browser=="Firefox" || $browser=="Mozilla") && $ID)
	{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">EASY CREATE</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center">
			<table width="100%" border=0 cellspacing=0 cellpadding=0 height="100px" bgcolor="#FFFFFF">
			<tr>
				<td align="center"><? include "easycreate.php"; ?></td>				
			</tr>
			</table>
		</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }}
function show_product_tools()
{
	global $ID_ORG,$ID;
	
	echo '<script>
	function relacionados()
	{
		var x=(window.innerWidth / 2) - (600 / 2);
		var y=(window.innerHeight / 2) - (400 / 2);
		
		newwindow=window.open("products_related.php?id_produto='.$ID.'","janela_'.$ID.'","top="+y+",left="+x+",width=600,height=400,toolbar=0,status=0,scrollbars=1,location=0,menubar=0,directories=0,resizable=1","_self");
		if (window.focus) {newwindow.focus()}
		if (!newwindow.closed) {newwindow.focus()}
	}
	function notificarClientes(idprod){
		$.ajax({type: "POST",url:"stocknotifier.php",data: { idprod: idprod }
      }).done(function(resultado) {
        alert(resultado);
      });
	}
	</script>
	';
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">CROSS/UP SELLING</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Produtos relacionados" onclick="javascript:relacionados()"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">CLIENTES ALERTA STOCK</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Notificar clientes" onclick="javascript:notificarClientes(<?php echo $_GET['ID'];?>)"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_drop_tools()
{
	global $browser,$ID;
	if(($browser=="Firefox" || $browser=="Mozilla") && $ID)
	{?>
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
        <td width="5px" align="left"><img src="../images/s2.jpg"></td>
        <td class="titulo2" align="center">EASY DROP</td>
        <td width="5px" align="right"><img src="../images/s2.jpg"></td>
    </tr>
    <tr> 
        <td width="5px" align="left"><img src="../images/s2.jpg"></td>
        <td class=preto align="center">
            <table width="100%" border=0 cellspacing=0 cellpadding=0 height="100px" bgcolor="#FFFFFF">
            <tr>
                <td align="center"><? include "easydrop.php"; ?></td>				
            </tr>
            </table>
        </td>
        <td width="5px" align="right"><img src="../images/s2.jpg"></td>
    </tr>
    </table>
	<img src="../images/bsh.png" width="200px" height="30px"><?
	}
}
function show_page_tools_select()
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
    	<td width="10px"></td>
    	<td align="left"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
    </tr>
    </table>
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
    <form action="products_families.php" method="post">			
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
    	<td width=29 align="center"><input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
	    <td align="right" class="branco"><? echo write_lang('key_pesquisar_por_nome','Pesquisar por Nome')?>: <input style="width:100px;" type="text" name="pesquisa" id="pesquisa" value="<?php echo $pesquisa?>"> 
                <? echo write_lang('key_or','ou')?> <? echo write_lang('key_por_ref','por referência')?>: 
                <input style="width: 100px;" type="text" name="pesquisa2" id="pesquisa2" value="<?php echo $pesquisa2?>">
                <input type="submit" name="Pesquisar" value="<? echo write_lang('key_pesquisar','Pesquisar')?>" style="width:70px">
		</td>
    	<td width="5"></td>
    </tr>
    </table>
    </form>
<? }
function show_page_bottom_tools()
{
	global $edit;?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value='' selected="selected"></option>
				<option value="2"><? echo write_lang('key_reordenar','Reordenar')?></option>
				<option value="3">Ativar</option>
				<option value="4">Desativar</option>
                <? if($edit){?><option value="3"><? echo write_lang('key_atualizar_nomes','Atualizar Nomes')?></option><? }?>
			</select>
		</td>
        <td width="5px"></td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="botao" style="width:120px;height:20px"></td>
        <td align="right"><input type="button" onclick="javascript:window.open('list.php?edit=1','_self')" value="<? echo write_lang('key_ativar_edicao_na_pagina','Activar Edição na Página')?>" class="botao" style="width:150px;height:20px">&nbsp;</td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_page_bottom_tools2($tipo=0)
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
		<td width="125px"><select name="flag2" id="flag2" class="select1">
				<option value="" selected="selected"></option>
				<option value="3"><?php echo write_lang('key_mover_para_a_familia','Mover para a familia');?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familias(0,"products_families","products_families_lang",$tipo);?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
<?
}
function show_fast_tools()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo write_lang('key_ver_em_forma_de_arvore','Ver em Forma de Árvore')?>" onclick="javascript:window.open('treecatalog.php?Dump=<?php echo uniqid("");?>','_self')" /></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo write_lang('key_ver_em_lista','Ver em Lista')?>" onclick="javascript:window.open('list.php?Dump=<?php echo uniqid("");?>','_self')" /></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo write_lang('key_criar_nova','Criar Nova')?> <? if($ID) echo 'SUB-';?><? echo write_lang('key_categoria','Category')?>" onclick="javascript:window.open('products_families.php?ID=0&ID_ORG=<? echo $ID?>&Dump=<? echo uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo write_lang('key_criar_novo_produto','Criar Novo Produto')?>" onclick="javascript:window.open('products.php?ID=0&ID_ORG=<?php echo $ID;?>&from_prods=<?php echo $from_prods?>&to_prods=<?php echo $to_prods?>&total_prods=<?php echo $total_prods?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
<?php  /*   <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">LISTAGENS PRODUTOS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «descrição»" onclick="javascript:window.open('lista_produtos_semdescricao.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «como usar»" onclick="javascript:window.open('lista_produtos_semcomousar.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «saiba mais»" onclick="javascript:window.open('lista_produtos_semsaibamais.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «todos descritivos»" onclick="javascript:window.open('lista_produtos_semtodosdescritivos.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «foto»" onclick="javascript:window.open('lista_produtos_semimagem.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «marca»" onclick="javascript:window.open('lista_produtos_semmarca.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Sem «peso»" onclick="javascript:window.open('lista_produtos_sempeso.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr> */?>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_fast_tools_destaques()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo 'Adicionar produto';?>" onclick="javascript:window.open('add_product_destaque.php','add_product_destaque','height=600,width=800')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_fast_tools_promo()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo 'Adicionar produto';?>" onclick="javascript:window.open('add_product_promocao.php','add_product_promocao','height=600,width=800')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_fast_tools_extra()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<? echo 'Adicionar produto';?>" onclick="javascript:window.open('add_product_extra.php','add_product_extra','height=600,width=800')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $edit;
	?>
    <table class="listagem" width="100%" border=0 cellspacing=0 cellpadding="2">
    <?
    $rs=GetRecords_Recursos_1(0,$ID_ORG,0);
    $count=0;
    while(!$rs->EOF())
    {
		$count++;
		$codigo=$rs->db_data["ID"];
		$ordem=$rs->db_data["Ordem"];
		$desconto=number_format($rs->db_data["Desconto"],2);
		$Visivel=$rs->db_data["visivel"];
		$cabecalho=$rs->db_data["Cabecalho"];
		$Protected=$rs->db_data["Protected"];
		if($count % 2 !=0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb"></td><? }?>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
            <td width="29" align="center"><a class="preto" href="products_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td valign="middle" width="35"><img title="<? if($Visivel) echo write_lang('key_pagina_visivel','Página Visivel'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a class="preto" href="products_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
            <? if($desconto > 0){?> <td width="80" class="desconto<?php echo $n?>"><?php echo $desconto?> %</td> <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td><? }else{?> <td width="20" class="desconto<?php echo $n?>">&nbsp;</td> <td width="4" align="right">&nbsp;</td><?php }?>
			<? if($Protected==0){?><td width="20" align="right"><a class="italico" href="products_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=0&Nivel=0&task=duplicar"><img title="<? echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
			<td width="20" align="right"><a class="italico" href="products_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=0&Nivel=0&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td><? }?>
            <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
           <? if($Protected==0){?> <td class="ordem<?php echo $n?>"><input class="ordem" type=text name="chks_<?php echo $codigo?>" value="<?php echo $ordem?>"></td><? }?>
         </tr>
        </table>
        <?
        $rs->db_next();	
    }
    $rs->db_close(1);
}
function show_content_list_promocoes($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $edit;
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	
    $sql='SELECT 
				P.ID, P.img_ext, P.Destaque, PL.Cabecalho, PL.Texto1
			FROM 
				products AS P
				INNER JOIN products_lang AS PL
				ON PL.ID_PAI=P.ID
				INNER JOIN products_families_obj as PFO 
				ON P.ID=PFO.ID_PROD
				INNER JOIN products_families AS PF
				ON PFO.ID_ORG=PF.ID
				INNER JOIN products_families_lang AS PFL
				ON PFL.ID_PAI=PF.ID
				INNER JOIN brands AS BR 
				ON P.ID_Marca=BR.ID
				INNER JOIN brands_lang AS BRL 
				ON P.ID_Marca=BRL.ID_PAI
				INNER JOIN products_objs as PO
				ON PO.obj_type_id=P.ID
				INNER JOIN products_objs_lang as POL
				ON POL.ID_PAI=PO.obj_id
			WHERE 
				P.Aproved=1
				AND
				PO.obj_promo_ativo=1
				AND
				PL.Lang="'.$admin_lang.'"
				AND
				POL.Lang="'.$admin_lang.'"
			Order by
				P.ID ASC';
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
    $count=0;
    while(!$rs->EOF())
    {
		$count++;
		$codigo=$rs->db_data["ID"];

		$sql2='SELECT * from products_promocoes where ID_Prod='.$codigo;
		$hRes=$rs2->db_open($db_link,$DB_name,$sql2);
	    $count=0;
	    if(!$rs2->EOF())
	    {
	    	$checked=' checked="checked" ';
	    	$ordem=$rs2->db_data["Ordem"];
	    }else{
	    	$checked='';
	    	$ordem=0;
	    }
	    $rs2->db_close();

		
		$cabecalho=$rs->db_data["Cabecalho"];
		if($count % 2 !=0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr id="<?php echo $codigo?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" <?php echo $checked?>></td><? }?>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
            <td width="29" align="center"><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
            <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
            <td width="20" align="right" class=preto><input type="text" name="field_<?php echo $codigo?>" style="width:35px;" value="<?php echo $ordem?>"></td>
            <td width="4"></td>
         </tr>
        </table>
        <?
        $rs->db_next();	
    }
    $rs->db_close(1);
}
function show_content_list_destaques($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $edit;
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	
    $sql='SELECT 
				P.ID, P.img_ext, P.Destaque, PL.Cabecalho, PL.Texto1
			FROM 
				products AS P
				INNER JOIN products_lang AS PL
				ON PL.ID_PAI=P.ID
				INNER JOIN products_families_obj as PFO 
				ON P.ID=PFO.ID_PROD
				INNER JOIN products_families AS PF
				ON PFO.ID_ORG=PF.ID
				INNER JOIN products_families_lang AS PFL
				ON PFL.ID_PAI=PF.ID
				INNER JOIN brands AS BR 
				ON P.ID_Marca=BR.ID
				INNER JOIN brands_lang AS BRL 
				ON P.ID_Marca=BRL.ID_PAI
				INNER JOIN products_objs as PO
				ON PO.obj_type_id=P.ID
				INNER JOIN products_objs_lang as POL
				ON POL.ID_PAI=PO.obj_id
			WHERE 
				P.Aproved=1
				AND
				PO.obj_destaque=1
				AND
				PL.Lang="'.$admin_lang.'"
				AND
				POL.Lang="'.$admin_lang.'"
			Order by
				P.ID ASC';
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
    $count=0;
    while(!$rs->EOF())
    {
		$count++;
		$codigo=$rs->db_data["ID"];
		$sql2='SELECT * from products_destaques where ID_Prod='.$codigo;
		$hRes=$rs2->db_open($db_link,$DB_name,$sql2);
	    $count=0;
		if(!$rs2->EOF())
	    {
	    	$checked=' checked="checked" ';
	    	$ordem=$rs2->db_data["Ordem"];
	    }
		else
		{
	    	$checked='';
	    	$ordem=0;
	    }
	    $rs2->db_close();
		$cabecalho=$rs->db_data["Cabecalho"];
		if($count % 2 !=0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr id="<?php echo $codigo?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" <?php echo $checked?>></td><? }?>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
            <td width="29" align="center"><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
            <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
            <td width="20" align="right" class=preto><input type="text" name="field_<?php echo $codigo?>" style="width:35px;" value="<?php echo $ordem?>"></td>
            <td width="4"></td>
         </tr>
        </table>
        <?
        $rs->db_next();	
    }
    $rs->db_close(1);
}
function gera_lista_familias($id_org=0,$tabela1,$tabela1_lang)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia" id="m_familia" class="chzn-select4" >';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);   
		echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';			
		$obj2->db_close();
	    gera_lista_subfamilias($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang);	    
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function gera_lista_subfamilias($id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=5)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);	
    while(!$obj->EOF())
    {
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);   
		echo '>';
		$i=0;while($i<$contador){echo "- ";$i++;}
		echo  htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';		
	    $obj2->db_close();
	    gera_lista_subfamilias($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+5);	    
        $obj->db_next();
    }
    $obj->db_close();
}
function gera_lista_medias($id_org=0,$tabela1)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_media" >';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';  
		echo '>'. htmlentities_memo(stripslashes($obj->db_data["Nome"])).'</option>';	
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}

function show_products_menu_path($id_org=0,$m_pagina='',$tabela1='',$number_per_page=15,$checkshowimages=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$id_org2=$id_org;
	if($id_org==0) return "";
	else
	{		
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$condicao="where ID=$id_org";
		$sql="Select * from ".$tabela1." $condicao order by Ordem Desc";
		$hRes=$obj->db_open($db_link,$DB_name,$sql);
		if(!$obj->EOF())
		{
			$sql="Select * from ".$tabela1."_lang where ID_PAI=$id_org and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);
			$C=htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"]));
			$obj2->db_close();
			$id_org=$obj->db_data["ID_ORG"];
			$obj->db_close();
			$result=show_products_menu_path($id_org,$m_pagina,$tabela1,$number_per_page,$checkshowimages);
			if($id_org > 0) echo " - ";
			echo '<a class=new_caminho href="'.$m_pagina.'?ID='.$id_org2.'&ID_ORG='.$id_org2.'&total_prods='.$number_per_page.'&checkshowimages='.$checkshowimages.'">'.$C.'</a>';
			return ($result+1);
		}
		else
		{
			$obj->db_close();
			return 0;
		}
	}
}
function gera_lista_marcas2($sel_nm,$tabela1,$tabela1_lang,$id_marca=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select $tabela1.*,$tabela1_lang.Nome from $tabela1 inner join $tabela1_lang on $tabela1_lang.ID_PAI=$tabela1.ID WHERE $tabela1_lang.Lang='$admin_lang' order by $tabela1_lang.Nome Asc";
	$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="'.$sel_nm.'" id="'.$sel_nm.'" class="chzn-select5">';
	if($obj->EOF())
	{
	if($id_marca==0) echo '<option value="1" selected>- nenhuma -</option>';
	else echo '<option value="1">- nenhuma -</option>';
	}
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_marca==$obj->db_data["ID"] ) echo ' selected="selected" '; 
		echo '>'. htmlentities_memo(stripslashes($obj->db_data["Nome"])).'</option>';
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function GetRecords_Recursos($id=0,$id_org=0,$nivel=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where ID=$id";
		if($id_org!=0)	$condicao="$condicao and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
	else
	{
		$condicao="where ID > 0";
		if($id_org!=0)	$condicao=$condicao." and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
    $sql="Select $campos from products_families $condicao";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}
function GetRecords_Recursos_1($id=0,$id_org=0,$nivel=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where products_families.ID=$id and products_families_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao="$condicao and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
	else
	{
		$condicao="where products_families.ID > 0 and products_families_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao=$condicao." and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
    $sql="Select $campos,products_families.ID from products_families inner join products_families_lang on products_families_lang.ID_PAI=products_families.ID $condicao order by products_families.Ordem asc,products_families_lang.Cabecalho asc ";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}
function GetRecords_Recursos_1_1($id=0,$id_org=0,$nivel=0,$campos="*",$limite_from,$limite_to,$total_prods,$pesquisa1='',$pesquisa2='',$pesquisa3='',$pesquisa4='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;$rs=new DataObj;
	
	$porpagina = 30;	
	
	$condicao = "WHERE products_lang.Lang = '".$admin_lang."' ";
	if($pesquisa1 || $pesquisa2 || $pesquisa3 || $pesquisa4)
	{
		if($pesquisa1) $condicao.= " and products.ID='".$pesquisa1."' ";
		if($pesquisa2) $condicao.= " and products_objs_lang.Cabecalho like '%".$pesquisa2."%' ";
		if($pesquisa3) $condicao.= " and products_lang.Cabecalho like '%".$pesquisa3."%' ";
		if($pesquisa4) $condicao.= " and products_families_obj.ID_ORG='".$pesquisa4."' ";
	}
	elseif($id!=0)
	{
		$condicao="where products.ID=$id and products_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao="$condicao and products_families_obj.ID_ORG=$id_org and products_lang.Lang='".$admin_lang."'";
		else $condicao="$condicao and products_families_obj.ID_ORG='0'";
	}
	else
	{
		// if($limite_to!=0) $key_limite=" LIMIT $limite_from ,$total_prods";
		// else $key_limite='';
		$condicao="where products.ID > 0  and products_lang.Lang='".$admin_lang."'";
		if($id_org!=0) $condicao=$condicao." and products_families_obj.ID_ORG=$id_org";
		else $condicao="$condicao";
	}
    if($pesquisa4)$sql="Select $campos,products.ID,products_objs_lang.Cabecalho as Ref from products inner join products_lang on products_lang.ID_PAI=products.ID inner join products_families_obj on products.ID=products_families_obj.ID_PROD inner join products_objs on products.ID=products_objs.obj_type_id inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $condicao GROUP BY products.ID ORDER BY CAST(products_objs_lang.Cabecalho AS SIGNED) asc $key_limite ";
	else $sql="Select $campos,products.ID,products_objs_lang.Cabecalho as Ref from products inner join products_lang on products_lang.ID_PAI=products.ID inner join products_families_obj on products.ID=products_families_obj.ID_PROD inner join products_objs on products.ID=products_objs.obj_type_id inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $condicao GROUP BY products.ID ORDER BY CAST(products_objs_lang.Cabecalho AS SIGNED) asc $key_limite ";
	
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$total=$rs->db_rows;
	echo $rs->db_rows;
	$rs->db_close();
		
	$n_pags = ceil($total / $porpagina); // n de paginas
	if(!isset($_GET['pagina'])) $pagina = '1';
	else $pagina = $_GET['pagina'];
	$inicio = $pagina - 1;
	$inicio = $inicio * $porpagina;
	$sql2=$sql." LIMIT $inicio, $porpagina";
	// echo $sql2;
	$obj->db_open($db_link, $DB_name, $sql2);
	
	// echo $obj->db_rows;
	if($n_pags > 1)
	{       
		$return = '<div class="clear" style="height:10px"></div><table class="new_quadros" border=0 cellspacing=0 cellpadding=0 style="margin-left:auto;margin-right:auto;"><tr>';
		
		 $p_seo = 'products_families.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'';
		
		if($pagina > 6)
		{	
			if(substr_count($p_seo, '?')) $ash = '&pagina=1';	else $ash = '?pagina=1';
			$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")'; 
			$return .= '<td width="2px"></td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597">1</td><td width="2px">...</td>';
		}
		for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			
			if(substr_count($p_seo, '?')) $ash = '&pagina='.$pi;	else $ash = '?pagina='.$pi;
			$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")'; 
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px"></td><td width="20" height="15" bgcolor="#006d9f" align="center" style="font-size:10px;color:#FFF;padding-top:2px;border:#939597 1px solid" >'.sprintf(($pi)).'</td>' : '<td width="2px"></td><td width="20" height="15" bgcolor="#edf4f9" onmouseover="this.style.background=\'#006d9f\';this.style.color=\'#FFFFFF\'" onmouseout="this.style.background=\'#edf4f9\';this.style.color=\'#939597\'" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;margin:2px;color:#939597" >'.sprintf($pi).'</td>' :'';
		}
		if($n_pags!=$pagina)
		{
			
			if(substr_count($p_seo, '?')) $ash = '&pagina='.$n_pags;	else $ash = '?pagina='.$n_pags;
			$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")'; 
			$return .= '<td width="2px"></td><td width="2px">...</td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597" >'.sprintf($n_pags).'</td>';
		}
		$return .= '</tr></table>';
	}
	
	return array('obj' => $obj, 'return' => $return);
}

function BuscarNivelId($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select Nivel from products_families where ID=$id");
	$nivel=$obj->db_data["Nivel"];
	return($nivel);
}
function MaxNivel($db_link)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select max(Nivel) as maximo from products_families");
	$nivel=$obj->db_data["maximo"];
	return ($nivel);	
}
function BuscarImg($id,$db_link)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select * from products_families where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	return ($ext1);
}
function DeleteFam($id,$img_path)
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$nivel=BuscarNivelId($id);
	$nivel=$nivel+1;
	$vector_filhos[0]=$id;
	$count=0;
	$MaxNivel=MaxNivel($db_link);
	for($i=$nivel;$i<=$MaxNivel;$i++)
	{
		$rs=GetRecords_Recursos(0,$vector_filhos[$count],$i);
		while(!$rs->EOF())//se tiver filhos percorre-os
		{
			$count=$count+1;
			$codigo=htmlentities_b($rs->db_data["ID"]);
			$vector_filhos[$count]=$codigo;
			$rs->db_next();			
		}
		$rs->db_close(1);
	}
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	for($i=0;$i<count($vector_filhos);$i++)
	{
		$img=BuscarImg($vector_filhos[$i],$db_link);		
		$del="delete from products_families where ID=".$vector_filhos[$i];
		$res=Runquery($db_link,$DB_name,$del);
		if($res)
		{
			$sql2="delete from products_families_lang where ID_PAI=".$vector_filhos[$i];
			$res2=Runquery($db_link,$DB_name,$sql2);
		}
		else echo db_error($db_link);
	}
	
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}

function ChangeFam($id,$id_org,$ordem,$cabecalho,$cabecalho2,$URL,$texto,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$Img_peq2,$Apagar_img2,$tem_foto2,$Img_peq3,$Apagar_img3,$tem_foto3,$margemcli,$margemrev,$cor,$visivel,$destaque,$tipo_check,$tipo,$cat1,$cat2,$cat3,$download,$preco_revenoff)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;
	
	$rs->db_open($db_link,$DB_name,"Select * from products_families WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;		
		$img_ext=$rs->db_data["img_ext"];
		$img2_ext=$rs->db_data["img2_ext"];
		$img3_ext=$rs->db_data["img3_ext"];	
	}
	$rs->db_close();	
	if(!$altera) return InsertNewFam($id_org,$data,$cabecalho,$cabecalho2,$URL,$texto,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$preco_revenoff);
	else
	{		
		$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		$altera_img2=0;if($Img_peq2["name"]){$altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
		$altera_img3=0;if($Img_peq3["name"]){$altera_img3=1;$pos=strrpos($Img_peq3["name"],".");$img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img){
			copy($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);
			//putWatermarkImage($img_path,$id."_1.".$img_ext);
		}
		//IMAGEM NUMERO 2
		if($tem_foto2 && $Apagar_img2){$ficheiro2=$id."_2.".$img2_ext;unlink($img_path.$ficheiro2);if(!$altera_img2)$img2_ext='';}
		if($altera_img2){
			copy($Img_peq2["temp"],$img_path.$id."_2.".$img2_ext);
			//putWatermarkImage($img_path,$id."_2.".$img_ext);
		}
		//IMAGEM NUMERO 3
		if($tem_foto3 && $Apagar_img3){$ficheiro3=$id."_3.".$img3_ext;unlink($img_path.$ficheiro3);if(!$altera_img3)$img3_ext='';}
		if($altera_img3){
			copy($Img_peq3["temp"],$img_path.$id."_3.".$img3_ext);
			//putWatermarkImage($img_path,$id."_3.".$img_ext);
		}
		
		$ordem1=str2SQLstr($ordem);
		$cabecalho1=str2SQLstr($cabecalho);
		// $cabecalho2=str2SQLstr($cabecalho2);
		$texto1=str2SQLstr($texto);
		$URL1=str2SQLstr($URL);
		$margemcli=str2SQLstr($margemcli);
		$margemrev=str2SQLstr($margemrev);
		$cor=str2SQLstr($cor);
		$visivel=str2SQLstr($visivel);
		$destaque=str2SQLstr($destaque);
		$Desconto = $_POST['Desconto'];
		$Margem = $_POST['Margem'];
		$DescontoTipo = $_POST['DescontoTipo'];
		$MargemTipo = $_POST['MargemTipo'];
		$Portes=$_POST['Portes'];	
		$bonus = $_POST['pontos'].$_POST['tpoints'];
		
		if(!isset($_POST['m_media']))$ID_Media=0;
		else $ID_Media = $_POST['m_media'];
		$sql = "	
				UPDATE 
					products_families 
				SET 
					Portes='$Portes',
					ID_Media='$ID_Media', ID_ORG='$id_org', Ordem=$ordem1,img_ext='$img_ext',img2_ext='$img2_ext',img3_ext='$img3_ext',MargemCli=$margemcli,MargemRev=$margemrev,cor=$cor,visivel=$visivel,Destaque=$destaque,cat1='$cat1',cat2='$cat2',cat3='$cat3',
					Download='$download',
					Desconto='".$Desconto."',
					DescontoTipo='".$DescontoTipo."',
					Margem='".$Desconto."',
					MargemTipo='".$DescontoTipo."',
					preco_revenoff='".$preco_revenoff."',
					points='".$bonus."'
				WHERE 
					ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		// APLICAR DESCONTO
		// echo '$DescontoTipo: '.$id.', '.$Desconto.', '.$DescontoTipo.'<br />';
		if($DescontoTipo > 0) aplica_lista_familias($id, $Desconto, $DescontoTipo);
		
		if($tipo_check==1)
		{
			$sql="Update products Set Tipo=$tipo where ID_ORG=$id";	
			$res=Runquery($db_link,$DB_name,$sql);
		}
		db_closedb_conection($db_link);
		ChangeFamLinguas($id,$cabecalho,$cabecalho2,$URL,$texto);
		
		exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
	}
}

function aplica_lista_familias($id_org=0, $Desconto=0, $DescontoTipo=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj; $obj2 = new DataObj;
	$sql = "SELECT ID FROM products_families WHERE ID_ORG='$id_org'";
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$sql = '
					UPDATE 
						products_families 
					SET 
						Desconto="'.$Desconto.'",
						DescontoTipo="'.$DescontoTipo.'"
					WHERE 
						ID="'.$obj->db_data["ID"].'"
					';
			// echo $sql.'<br />';
			$res = Runquery($db_link, $DB_name, $sql);		
		
			if($obj->db_data["ID"] > 0) aplica_promos_produtos($obj->db_data["ID"], $Desconto, $DescontoTipo);
			
			// Pecorrer todas subfamilias
			if($DescontoTipo > 0) aplica_lista_familias($obj->db_data["ID"], $Desconto, $DescontoTipo);
			
			$obj->db_next();
		}
	}
	else
	{
		if($id_org > 0) aplica_promos_produtos($id_org, $Desconto, $DescontoTipo);
	}
    $obj->db_close();
}
function aplica_lista_margem_familias($id_org=0, $Margem=0, $MargemTipo=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj; $obj2 = new DataObj;
	$sql = "SELECT ID FROM products_families WHERE ID_ORG='$id_org'";
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$sql = '
					UPDATE 
						products_families 
					SET 
						Margem="'.$Margem.'",
						MargemTipo="'.$MargemTipo.'"
					WHERE 
						ID="'.$obj->db_data["ID"].'"
					';
			// echo $sql.'<br />';
			$res = Runquery($db_link, $DB_name, $sql);		
		
			if($obj->db_data["ID"] > 0) aplica_margens_produtos($obj->db_data["ID"], $Margem, $MargemTipo);
			
			// Pecorrer todas subfamilias
			if($MargemTipo > 0) aplica_lista_margem_familias($obj->db_data["ID"], $Margem, $MargemTipo);
			
			$obj->db_next();
		}
	}
	else
	{
		if($id_org > 0) aplica_promos_produtos($id_org, $Desconto, $DescontoTipo);
	}
    $obj->db_close();
}
function aplica_promos_produtos($ID=0, $Desconto=0, $DescontoTipo=0)
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2=new DataObj;
	
	$sql = 'SELECT ID_PROD as ID FROM products_families_obj where ID_ORG="'.$ID.'"';
	// echo $sql.'<br />';
	$obj2->db_open($db_link,$DB_name,$sql);
	if(!$obj2->EOF())			
	{
		while(!$obj2->EOF())
		{
			if($Desconto==0 && $DescontoTipo==1) // SE O DESCONTO FOR 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM INATIVA
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = 0,
							obj_promo_ativo = 0
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
						';
			}
			elseif($Desconto==0 && $DescontoTipo==2) // A TODOS PRODUTOS SEM PROMOCAO
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = 0,
							obj_promo_ativo = 0
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo = 0
							AND
							obj_promo_ativo = 0
						';				
			}
			elseif($Desconto==0 && $DescontoTipo==3) // A TODOS PRODUTOS COM PROMOCAO
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = 0,
							obj_promo_ativo = 0
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo > 0
							AND
							obj_promo_ativo = 0
						';				
			}
			elseif($Desconto > 0 && $DescontoTipo==1) // SE O DESCONTO FOR MAIOR QUE 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM ATIVA
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = obj_pvp - (obj_pvp * ('.$Desconto.' / 100)),
							obj_promo_ativo = 1
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
						';
			}
			elseif($Desconto > 0 && $DescontoTipo==2)
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = obj_pvp - (obj_pvp * ('.$Desconto.' / 100)),
							obj_promo_ativo = 1
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo = 0
							AND
							obj_promo_ativo = 0
						';				
			}
			elseif($Desconto > 0 && $DescontoTipo==3)
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = obj_pvp - (obj_pvp * ('.$Desconto.' / 100)),
							obj_promo_ativo = 1
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo > 0
							AND
							obj_promo_ativo = 0
						';				
			}				

			if($sqlp)
			{ 
				// echo $sqlp.'<br />';
				$res = Runquery($db_link, $DB_name, $sqlp);
			}
			
			$obj2->db_next();
		}
	}
	
	$obj2->db_close();
}
function aplica_margens_produtos($ID=0, $Margem=0, $DescontoTipo=0)
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2=new DataObj;
	
	$sql = 'SELECT ID_PROD as ID FROM products_families_obj where ID_ORG="'.$ID.'"';
	// echo $sql.'<br />';
	$obj2->db_open($db_link,$DB_name,$sql);
	if(!$obj2->EOF())			
	{
		while(!$obj2->EOF())
		{
			if($Desconto==0 && $DescontoTipo==1) // SE O DESCONTO FOR 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM INATIVA
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = 0,
							obj_promo_ativo = 0
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
						';
			}
			elseif($Desconto==0 && $DescontoTipo==2) // A TODOS PRODUTOS SEM PROMOCAO
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = 0,
							obj_promo_ativo = 0
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo = 0
							AND
							obj_promo_ativo = 0
						';				
			}
			elseif($Desconto==0 && $DescontoTipo==3) // A TODOS PRODUTOS COM PROMOCAO
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = 0,
							obj_promo_ativo = 0
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo > 0
							AND
							obj_promo_ativo = 0
						';				
			}
			elseif($Desconto > 0 && $DescontoTipo==1) // SE O DESCONTO FOR MAIOR QUE 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM ATIVA
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = obj_pvp - (obj_pvp * ('.$Desconto.' / 100)),
							obj_promo_ativo = 1
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
						';
			}
			elseif($Desconto > 0 && $DescontoTipo==2)
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = obj_pvp - (obj_pvp * ('.$Desconto.' / 100)),
							obj_promo_ativo = 1
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo = 0
							AND
							obj_promo_ativo = 0
						';				
			}
			elseif($Desconto > 0 && $DescontoTipo==3)
			{
				$sqlp = '
						UPDATE 
							products_objs
						SET 
							obj_promo = obj_pvp - (obj_pvp * ('.$Desconto.' / 100)),
							obj_promo_ativo = 1
						WHERE 
							obj_type_id="'.$obj2->db_data["ID"].'"
							AND
							obj_promo > 0
							AND
							obj_promo_ativo = 0
						';				
			}				

			if($sqlp)
			{ 
				// echo $sqlp.'<br />';
				$res = Runquery($db_link, $DB_name, $sqlp);
			}
			
			$obj2->db_next();
		}
	}
	
	$obj2->db_close();
}
function ChangeFamLinguas($id_pai,$cabecalho,$cabecalho2,$URL,$texto)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;	
	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from products_families_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();	
	if(!$altera)return InsertNewFamLinguas($id_pai,$cabecalho,$cabecalho2,$URL,$texto);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$cabecalho1=str2SQLstr($cabecalho);
		$cabecalho2=str2SQLstr($cabecalho2);
		$texto1=$texto;
		$URL1=str2SQLstr($URL);
		$sql="Update products_families_lang Set Cabecalho=$cabecalho1,Cabecalho2=$cabecalho2,URL=$URL1,Texto='$texto1' where ID_PAI=$id_pai and Lang='$admin_lang'";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);		
		return($res);
	}
}
function altera_imagem($id,$ext2,$tem_foto2,$altera_img2,$img_path,$Img_peq2,$num=4,$Apagar_img2)
{
  if($tem_foto2==0)
  {
		if(!$altera_img2){$img2_ext='';$img2_ext=str2SQLstr($img2_ext);}
		else{$pos=strrpos($Img_peq2["name"],".");
			$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
			$img2_ext=str2SQLstr($ext2);
			copy($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
            redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_".$num."_2.".$ext2);// redimensiona para o tamanho default		
			redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_".$num."_1.".$ext2);// gera o tamanho pequeno
		}
	}
	else  //TINHA IMAGEM
	{
		if($Apagar_img2==1)
		{
			if($num==4){$ficheiro2=$id."_1.".$ext2; unlink($img_path.$ficheiro2);}
			if($num==4){$ficheiro2=$id."_2.".$ext2; unlink($img_path.$ficheiro2);}
			$ficheiro2=$id."_".$num.".".$ext2; unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_1.".$ext2; unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_2.".$ext2; unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_0.".$ext2; unlink($img_path.$ficheiro2);
			
			//NAO SUBSTITUI A IMAGEM
			if(!$altera_img2){$img2_ext='';$img2_ext=str2SQLstr($img2_ext);}
			else
			{
				$pos=strrpos($Img_peq2["name"],".");
				$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
				$img2_ext=str2SQLstr($ext2);
				copy($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original		
                redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_2.".$ext2);// redimensiona para o tamanho default	
				redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_1.".$ext2);// gera o tamanho pequeno
			}
		}
		else
		{
			if($altera_img2)
			{
				$pos=strrpos($Img_peq2["name"],".");
				$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
				$img2_ext=str2SQLstr($ext2);
				copy($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
                redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_".$num."_2.".$ext2);// redimensiona para o tamanho default	
				redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_".$num."_1.".$ext2);// gera o tamanho pequeno
			}
			else $img2_ext=str2SQLstr($ext2);
		}
	}
	return $img2_ext;
}
function strtolower2($str)
{
	$str =html_entity_decode($str);

	$valores=array("(",")","3","°","2","+","1","%","«","»","?","a","o","®","©","AfÂ§");	$str=str_replace($valores,'',$str);
	$valores=array(" ","'","/","&",":",";","`","´","~","^","¨","_","+",",",".","+","|","\\");	$str=str_replace($valores,'-',$str);
	$str=str_replace('--','-',$str);
	$str=str_replace('-',' ',$str);
	
	$valores=array("á","a","a","â","ä"); 	$str=str_replace($valores,'a',$str);
	$valores=array("é","e","e","ë"); 		$str=str_replace($valores,'e',$str);
	$valores=array("í","i","î","i"); 		$str=str_replace($valores,'i',$str);
	$valores=array("ó","o","o","ô","ö"); 	$str=str_replace($valores,'o',$str);
	$valores=array("ú","u","u","ü"); 		$str=str_replace($valores,'u',$str);
	$valores=array("n"); 		$str=str_replace($valores,'n',$str);
	$valores=array("ç"); 		$str=str_replace($valores,'c',$str);
	
	$valores=array("Á","A","A","Â","Ä"); 	$str=str_replace($valores,'A',$str);
	$valores=array("É","E","E","Ë"); 		$str=str_replace($valores,'E',$str);
	$valores=array("Í","I","Î","I"); 		$str=str_replace($valores,'I',$str);
	$valores=array("Ó","O","O","Ô","Ö"); 	$str=str_replace($valores,'O',$str);
	$valores=array("Ú","U","U","Ü"); 		$str=str_replace($valores,'U',$str);
	$valores=array("N"); 		$str=str_replace($valores,'N',$str);
	$valores=array("Ç"); 		$str=str_replace($valores,'C',$str);
	return ltrim(rtrim(strtolower($str)));
}
function Pesquisa_produtos_backoffice($Str_pesquisar,$cat=0,$t_cat="products")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$sugest,$count,$total_palavras,$num,$produtos,$Str_pesquisar2,$ID_Fam,$admin_lang;
	$rs=new DataObj;$obj=new DataObj;$obj2=new DataObj;

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(strlen($Str_pesquisar)<=4){
		$and .= ' pol.Cabecalho like "%'.$Str_pesquisar.'%" ';
		$and .= ' OR pl.Cabecalho like "%'.$Str_pesquisar.'%" ';
	}else{
		$stringbooleanmode=searchIntoBooleanMode(" ","+",strtolower($Str_pesquisar));

		//nome produto
		$and .= ' MATCH(pol.Cabecalho,pl.Cabecalho) AGAINST("'.$stringbooleanmode.'" IN BOOLEAN MODE) ';
	}
	
	
	
	$sql='SELECT p.ID as PID,p.ID_ORG,pl.Cabecalho,pol.Cabecalho as Ref
				FROM 
					products p 
					INNER JOIN 
						products_lang pl 
						ON pl.ID_PAI=p.ID 
					INNER JOIN 
						products_objs po ON po.obj_type_id=p.ID 
					INNER JOIN 
						products_objs_lang pol ON pol.ID_PAI=po.obj_id 
				WHERE 
				( '.$and.' ) 
				AND p.Aproved=1 AND pl.Lang="'.$admin_lang.'" AND pol.Lang="'.$admin_lang.'"';
	$hRes=$obj->db_open( $db_link,$DB_name,$sql);
	$count=0;
	if(!$obj->EOF()) 
	{
		$found=1;
		while (!$obj->EOF())
		{		
															
				$nomeb=str_replace('"','',$obj->db_data["Cabecalho"]);
				$nomeb=str_replace("'","",$nomeb);
				
				
				$sugest[$num][0]=$obj->db_data["PID"];
				$sugest[$num][4]=$obj->db_data["ID_ORG"];
				$sugest[$num][3]=$cat;
				$sugest[$num][2]=$nomeb;
				$num++;
				$count++;

			$obj->db_next();
		}
	}
    return $found;
}

function gera_resultados($m_nome)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
	global $sugest,$count,$total_palavras,$num,$peso,$cfg_esquema,$Str_pesquisar2;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$maior_num_palavras=0;	
	class clsPesquisa
	{
		var $db_link,$hRes,$sDB,$db_data,$db_data2,$sSQLQuery,$iPos,$db_rows;
		function inicializa()
		{
			$this->iPos=0;
			$this->db_rows=0;
		}
		function db_close($nada)
		{
			// Nada
		}
		function db_next()
		{
			if(!$this->EOF())
			{
				$this->iPos++;
				$this->db_data["Aproved"]=$this->db_data2[$this->iPos]["Aproved"];
				$this->db_data["ID"]=$this->db_data2[$this->iPos]["ID"];
				$this->db_data["img_ext"]=$this->db_data2[$this->iPos]["img_ext"];
				$this->db_data["Ref"]=$this->db_data2[$this->iPos]["Ref"];
				$this->db_data["Preco1"]=$this->db_data2[$this->iPos]["Preco1"];
				$this->db_data["Preco2"]=$this->db_data2[$this->iPos]["Preco2"];
				$this->db_data["PrecoCusto"]=$this->db_data2[$this->iPos]["PrecoCusto"];
				$this->db_data["ID_ORG"]=$this->db_data2[$this->iPos]["ID_ORG"];
				$this->db_data["Importado"]=$this->db_data2[$this->iPos]["Importado"];
				$this->db_data["id_fornecedor"]=$this->db_data2[$this->iPos]["id_fornecedor"];
			}
		}
		function Previous()
		{
			
			if ($this->iPos==0)	$this->iPos--;
			else
			{
				$this->iPos--;
				$this->db_data["Aproved"]=$this->db_data2[$this->iPos]["Aproved"];
				$this->db_data["ID"]=$this->db_data2[$this->iPos]["ID"];
				$this->db_data["img_ext"]=$this->db_data2[$this->iPos]["img_ext"];
				$this->db_data["Ref"]=$this->db_data2[$this->iPos]["Ref"];
				$this->db_data["Preco1"]=$this->db_data2[$this->iPos]["Preco1"];
				$this->db_data["Preco2"]=$this->db_data2[$this->iPos]["Preco2"];
				$this->db_data["PrecoCusto"]=$this->db_data2[$this->iPos]["PrecoCusto"];
				$this->db_data["ID_ORG"]=$this->db_data2[$this->iPos]["ID_ORG"];
				$this->db_data["Importado"]=$this->db_data2[$this->iPos]["Importado"];
				$this->db_data["id_fornecedor"]=$this->db_data2[$this->iPos]["id_fornecedor"];
			}
		}
		function EOF()
		{
			if ($this->iPos==$this->db_rows)	return 1;
			else							return 0;
		}
		function BOF()
		{
			if ($this->iPos<0)	return 1;
			else				return 0;
		}
		function avanca()
		{
			// Actualizar vector temporario
			$this->db_data2[$this->db_rows]["Aproved"]=$this->db_data["Aproved"];
			$this->db_data2[$this->db_rows]["ID"]=$this->db_data["ID"];
			$this->db_data2[$this->db_rows]["img_ext"]=$this->db_data["img_ext"];
			$this->db_data2[$this->db_rows]["Ref"]=$this->db_data["Ref"];
			$this->db_data2[$this->db_rows]["Preco1"]=$this->db_data["Preco1"];
			$this->db_data2[$this->db_rows]["Preco2"]=$this->db_data["Preco2"];
			$this->db_data2[$this->db_rows]["PrecoCusto"]=$this->db_data["PrecoCusto"];
			$this->db_data2[$this->db_rows]["ID_ORG"]=$this->db_data["ID_ORG"];
			$this->db_data2[$this->db_rows]["Importado"]=$this->db_data["Importado"];	
			$this->db_data2[$this->db_rows]["id_fornecedor"]=$this->db_data["id_fornecedor"];	
			$this->db_rows++;
		}
		function first()
		{
			$this->iPos=0;
			$this->db_data["Aproved"]=$this->db_data2[$this->iPos]["Aproved"];
			$this->db_data["ID"]=$this->db_data2[$this->iPos]["ID"];
			$this->db_data["img_ext"]=$this->db_data2[$this->iPos]["img_ext"];
			$this->db_data["Ref"]=$this->db_data2[$this->iPos]["Ref"];
			$this->db_data["Preco1"]=$this->db_data2[$this->iPos]["Preco1"];
			$this->db_data["Preco2"]=$this->db_data2[$this->iPos]["Preco2"];
			$this->db_data["PrecoCusto"]=$this->db_data2[$this->iPos]["PrecoCusto"];
			$this->db_data["ID_ORG"]=$this->db_data2[$this->iPos]["ID_ORG"];
			$this->db_data["Importado"]=$this->db_data2[$this->iPos]["Importado"];
			$this->db_data["id_fornecedor"]=$this->db_data2[$this->iPos]["id_fornecedor"];
		}
	}
	$resultados=new clsPesquisa;
	$resultados->inicializa();
	$m_nome =str_replace(' gb','gb',$m_nome);	$m_nome =str_replace(' GB','GB',$m_nome);
	$m_nome =str_replace(' Gb','GB',$m_nome);	$m_nome =str_replace(' tb','tb',$m_nome);
	$m_nome =str_replace(' TB','TB',$m_nome);	$m_nome =str_replace(' Tb','TB',$m_nome);		
	$m_nome =str_replace(' mb','mb',$m_nome);	$m_nome =str_replace(' MB','MB',$m_nome);
	$m_nome =str_replace(' Mb','MB',$m_nome);	$m_nome =str_replace('USB 2.0','USB2.0',$m_nome);
	$m_nome2=$m_nome;
	$m_nome=strtolower2($m_nome);
	$m_str1 =explode(" ",$m_nome);
	$num_palavras =count($m_str1);
	$count2=0;
	while($count2<$num_palavras)
	{
		$count2++;
		$total_palavras[$count2]=0;
	}
	$count2=0;
	while($count2<$num)
	{
		$i=0;
		$conta=0;
		$x_str=$sugest[$count2][5];
		if($log4) echo "x_str=".$x_str."<br>";
		while($i < count($m_str1)) 
		{
			$t=substr_count($x_str,$m_str1[$i]);
			if($num_palavras > 1){if($t > 0) $conta+=1;}
			else				 {$conta+=$t;}
			$i++;
		}
		if($conta > $maior_num_palavras) $maior_num_palavras=$conta;		
		$sugest[$count2][4]=$conta;
		$numy=$conta;
		$total_palavras[$numy]++;
		$sugest2[$numy][$total_palavras[$numy]]=$count2;
		$count2++;
	}
	$count2=0;$count3=0;
	$fim=0;
	$j=0;
	$num_palavras=$maior_num_palavras;
	$niveis=$num_palavras;	
	while($num_palavras >=0)
	{
		$i=0;
		while($i <=$total_palavras[$num_palavras] && !$fim)
		{
			$chave=$sugest2[$num_palavras][$i];
			// Se estivermos no nivel maximo vamos mostrar tudo
			if($total > 0 && $niveis>2)
			{
				if($num_palavras < $niveis) $j++;
				if($j>30) $fim=1;
			}
			$ID_do_produto=$sugest[$chave][0];
			$Str_name=$sugest[$chave][2];
			$ID_da_Familia=$sugest[$chave][4];			
			$cat=$sugest[$chave][3];
			$Pagina="produtos.php?ID=";
			$t_name2="1";
			$t_name=$t_name2;
			$obj2=new DataObj;
			
			if($ID_do_produto)
			{			
			        global $m_tab; $m_tab='';		
					$sql="Select p.*,pol.Cabecalho as Ref FROM products p  INNER JOIN products_objs po ON po.obj_type_id=p.ID  INNER JOIN  products_objs_lang pol ON pol.ID_PAI=po.obj_id where p.ID=".$ID_do_produto;			
					$hRes=$obj2->db_open( $db_link,$DB_name,$sql);
					if(!$obj2->EOF()) 
					{
						$ID_da_Familia=$obj2->db_data["ID_ORG"];
						$x_ref=$obj2->db_data["Ref"];
						$img_ext=$obj2->db_data["img_ext"];
						$resultados->db_data["Aproved"]=$obj2->db_data["Aproved"];
						$resultados->db_data["ID"]=$obj2->db_data["ID"];
						$resultados->db_data["img_ext"]=$obj2->db_data["img_ext"];
						$resultados->db_data["Ref"]=$obj2->db_data["Ref"];
						$resultados->db_data["Preco1"]=$obj2->db_data["Preco1"];
						$resultados->db_data["Preco2"]=$obj2->db_data["Preco2"];
						$resultados->db_data["PrecoCusto"]=$obj2->db_data["PrecoCusto"];
						$resultados->db_data["ID_ORG"]=$obj2->db_data["ID_ORG"];
						$resultados->db_data["Importado"]=$obj2->db_data["Importado"];
						$resultados->db_data["id_fornecedor"]=$obj2->db_data["id_fornecedor"];
						$resultados->avanca();
					}
					$obj2->db_close();
					$count3++;
			}
			$count2++;
			$i++;
		}
		$num_palavras--;
	}
	$resultados->first();
	return $resultados;
}
function Pesquisa_backoffice($pesquisa)
{
	global $count,$num,$produtos;
	$sucesso=0;
	if($pesquisa)
	{
		if(trim($pesquisa)!='' && strlen($pesquisa)>1)
		{
			$count=0;
			$num=0;
			$produtos[1][]='';
			$pesquisa=trim($pesquisa);
			$sucesso =Pesquisa_produtos_backoffice($pesquisa,1,"products",$cat);
			return gera_resultados($pesquisa);
		}
	}
}


function getListaFamiliasProduto($ID) {
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;

	$sql='SELECT PF.ID,PFL.Cabecalho FROM  products_families PF INNER JOIN products_families_lang as PFL on PFL.ID_PAI=PF.ID INNER JOIN products_families_obj as PFO on PFO.ID_ORG=PF.ID WHERE PFO.ID_PROD='.$ID.' AND PFL.Lang="'.$admin_lang.'" order by PFL.Cabecalho';
	
	$obj->db_open($db_link,$DB_name,$sql);
	
	$result=array();
	$pos=0;
	if(!$obj->EOF()) {
		
		while (!$obj->EOF())
		{	
			$result[$pos]['ID']=$obj->db_data['ID'];
			$result[$pos]['Cabecalho']=$obj->db_data['Cabecalho'];
			$pos++;
			$obj->db_next();	
		}
	}
	$obj->db_close();

	return $result;
}
function gera_lista_subfamilias_produto($ID,$id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=1)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;//,$ID,$ID_Fam
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql='SELECT PF.ID FROM products_families PF WHERE PF.ID_ORG="'.$id_start.'" and PF.ID NOT IN (SELECT ID_ORG from products_families_obj where ID_PROD='.$ID.') order by PF.Ordem Asc';
	$hRes=$obj->db_open($db_link,$DB_name,$sql);	
    while(!$obj->EOF())
    {
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);   
		echo '>';
		$i=0;while($i<$contador){echo ">";$i++;}
		echo " ".$obj2->db_data["Cabecalho"].'</option>';		
	    $obj2->db_close();
	    gera_lista_subfamilias_produto($ID,$obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+1);	    
        $obj->db_next();
    }
    $obj->db_close();
}
function products_families_save($id_prod,$id_org){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT * FROM products_families_obj WHERE ID_PROD='".$id_prod."'";
	//echo $sql;
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF()){
		$sql="UPDATE products_families_obj SET ID_ORG='".intval($id_org)."' WHERE ID_PROD='".$id_prod."'";
		//echo $sql;
		$res=Runquery($db_link,$DB_name,$sql);
	}else{
		$sql="INSERT INTO products_families_obj(ID_PROD,ID_ORG) values ('".$id_prod."','".intval($id_org)."')";
		//echo $sql;
		$res=Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();
}



function getImagensProduto($ID){
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$arr_imagens=array();
	$pos=0;
	$sql="SELECT * FROM products_images WHERE ID_ORG='".$ID."' order by Ordem";
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF()){
		while(!$rs->EOF()){
			$arr_imagens[$pos]['ID']=$rs->db_data['ID'];
			$arr_imagens[$pos]['img_ext']=$rs->db_data['img_ext'];
			$arr_imagens[$pos]['ID_ORG']=$rs->db_data['ID_ORG'];
			$arr_imagens[$pos]['Ordem']=$rs->db_data['Ordem'];
			$pos++;
			$rs->db_next();
		}
	}
	$rs->db_close();
	return $arr_imagens;
}
function products_images_save($ID,$Img_peq,$tem_foto='',$Apagar_img='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$img_path;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	//percorrer as imagens recebidas
	for ($i=1; $i<=count($Img_peq["name"]); $i++)
	{ 

		if($Img_peq["name"][$i]!='')
		{ //contem imagem nova
			//obter a extensao
			$sql="SELECT * FROM products_images WHERE ID_ORG='".$ID."' and Ordem='".$i."' order by Ordem";
			// echo $sql.'<br>';
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF())
			{
				//já tem imagem
				$pos=strrpos($Img_peq["name"][$i],".");
				$img_ext=substr($Img_peq["name"][$i],$pos+1,strlen($Img_peq["name"][$i])+1-$pos);

				$ficheiro=$ID."_".$i.".".$rs->db_data['img_ext'];
				unlink($img_path.$ficheiro);

				$sql='UPDATE products_images SET img_ext="'.$img_ext.'" WHERE ID_ORG="'.$ID.'"  and Ordem="'.$i.'"';	
				$res=Runquery($db_link,$DB_name,$sql);

				

				copy($Img_peq["tmp_name"][$i],$img_path.$ID."_".$i.".".$img_ext);
			}
			else
			{
				//nova imagem
				$pos=strrpos($Img_peq["name"][$i],".");
				$img_ext=substr($Img_peq["name"][$i],$pos+1,strlen($Img_peq["name"][$i])+1-$pos);

				$sql='INSERT INTO products_images(ID_ORG,img_ext,Ordem) VALUES ("'.$ID.'","'.$img_ext.'","'.$i.'")';	
				// echo $sql.'<br>';
				$res=Runquery($db_link,$DB_name,$sql);
					
				copy($Img_peq["tmp_name"][$i],$img_path.$ID."_".$i.".".$img_ext);
			}
			$rs->db_close();
		}
		else
		{	
			//não tem

			

			if(is_array($tem_foto) && is_array($Apagar_img))
			{
				if(isset($Apagar_img[$i]))
				{
					if($tem_foto[$i]==1 && $Apagar_img[$i]==1)
					{
						//se tem foto e  pretende eliminar
						$sql="SELECT * FROM products_images WHERE ID_ORG='".$ID."'  and Ordem='".$i."' order by Ordem";
						$rs->db_open($db_link, $DB_name, $sql);
						if(!$rs->EOF())
						{
							$ficheiro = $ID."_".$i.".".$rs->db_data['img_ext'];
							unlink($img_path.$ficheiro);

							$sql='DELETE from `products_images` WHERE ID_ORG="'.$ID.'"  and Ordem="'.$i.'"';	
							$res=Runquery($db_link,$DB_name,$sql);						
						}
						$rs->db_close();
					}
				}
			}
		}				
	}
	
	$sql = "SELECT img_ext FROM products_images WHERE ID_ORG='".$ID."' ORDER BY Ordem Limit 1";
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$sql='UPDATE products SET img_ext="'.$rs->db_data['img_ext'].'" WHERE ID="'.$ID.'"';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	else
	{
		$sql='UPDATE products SET img_ext="" WHERE ID="'.$ID.'"';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();
}
function show_products_without_categorie()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$img_path,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	define('N_PAGINAS', 20);
	
	$rs=new DataObj;$rs2=new DataObj;
	$sql=" 
			SELECT 
				products.*,products_objs_lang.Cabecalho as Referencia 
			FROM 
				products 
				inner join products_objs on products_objs.obj_type_id=products.ID 
				inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id 
				where  Not exists (select * from products_families_obj where products.ID=products_families_obj.ID_PROD) Group by products.ID";
	$rs->db_open($db_link, $DB_name, $sql);		
	$total = $rs->db_rows;
	// echo $total;
	$rs->db_close();
	
	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];
	
	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;							
	
	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	
	if(!$rs->EOF())
	{
		?>
		<script>
		function makeSelection2(selObj)
		{
			if(selObj.value==1)	for(i=0; i<document.form2.elements.length; i++)	document.form2.elements[i].checked=true;
			if(selObj.value==2)	for(i=0; i<document.form2.elements.length; i++)	document.form2.elements[i].checked=false;
		}	
		</script>
		<form name="form2" method="post">
		<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
        <tr>
            <td>
            	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                <tr> 
                    <td width="5px">&nbsp;</td>
                    <td class="branco">Produtos sem categoria</td>
                </tr>
                </table>
                <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                <tr> 
                    <td width="29px" height="10px">&nbsp;</td>
                    <td width="4px" height="10px">&nbsp;</td>
                    <td width="29px" height="10px">&nbsp;</td>
                    <td width="4px" height="10px">&nbsp;</td>
                    <td width="50px" height="10px">&nbsp;</td>
                    <td width="1pc">&nbsp;</td>
                    <td class="branco">Nome</td>
                    <td width="26px">&nbsp;</td>
                    <td width="4px">&nbsp;</td>
                    <td width="30px">&nbsp;</td>
                </tr>
                </table>
				<?
				$count=0;	
				while(!$rs->EOF())
				{
					$visivel=$rs->db_data["Aproved"];
					$codigo=$rs->db_data["ID"];
					$img_ext=$rs->db_data["img_ext"];
					$ordem=$rs->db_data["Ordem"];
					$ref=$rs->db_data["Referencia"];
					$preco1=$rs->db_data["Preco1"];
					$preco2=$rs->db_data["Preco2"];
					$precoCusto=$rs->db_data["Preco_prec"];
					$ID=$rs->db_data["ID_ORG"];
					$importado=$rs->db_data["Importado"];
					$id_fornecedor=$rs->db_data["id_fornecedor"];
					$tipo=$rs->db_data["Tipo"];
					$configurador=$rs->db_data["Configurador"];	
					//********* linguas ***//
					$hRes=$rs2->db_open($db_link,$DB_name, "Select * from products_lang where ID_PAI=$codigo and lang='$admin_lang'");
					$C=$rs2->db_data["Cabecalho"];
					$T=$rs2->db_data["Texto1"];
					$rs2->db_close();
				
					if($count % 2 !=0)	{$cor="#def2fa";$class="r1";$n="1";}
					else				{$cor="#F6F6F6";$class="r2";$n="2";}
					?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
					<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
					<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" /></td>
					<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
					<td width="29" align="center"><img src="../images/page.png" /></td>
					<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>                        
					<td width=50 class="preto"><?php echo $fornecedor?></td>											                                               
					<td><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&Nivel=<?php echo $Nivel+1;?>">[<?php echo $codigo?>]&nbsp;- <?php echo $ref?> - <? if($C) echo htmlentities_b($C); else echo "[Edit]"?> </a></td>
					<td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=duplicar"><img title="Duplicar" src="duplicate.png" border="0" /></a></td>
					<td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=apagar"><img title="<?php echo write_lang('key_apagar','Apagar')?>" src="del2.png" border="0" /></a></td>
					<td width="4px" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
					<td width="30px" class="ordem<?php echo $n?>"><input class="ordem" type=text name="field_<?php echo $codigo?>" value="<?php echo $ordem?>"></td>
					<td width="1px"><? if($fotos && $img_ext && file_exists("../../files/products/".$rs->db_data["ID"]."_1.".$rs->db_data["img_ext"])){ echo "<img width=100 height=100 src=\"".$site_http_url."files/products/".$rs->db_data["ID"]."_1.".$rs->db_data["img_ext"]."\" border=0 align=absmiddle />";} ?>&nbsp;</td>
					</tr>
					</table><?
					$rs->db_next();	
					$count++;
				}
				
				
				 // show_page_bottom_tools3();
				
				
				if($n_pags > 1)
				{       
					
					if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages = 'pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&bloco_visivel='.urlencode($_GET['bloco_visivel']);
					else $url_pages = 'list.php?d=';
					
					$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
					if($pagina > 6)
					{
						$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';				
						$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
					}
					for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
					{
						$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")'; 
						$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
					}
					if($n_pags!=$pagina)
					{
						$pgn='onclick=window.open("'.$url_pages.'&pagina='.$n_pags.'","_self")'; 
						$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
					}
					$return .= '<td>&nbsp;</td></tr></table>';
					
					echo $return;
				}
				?>
            </td>
        </tr>
        </table>
        </form><?
    }
    $rs->db_close();
}
function show_page_bottom_tools3($tipo=0)
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
		<td width="125px"><select name="flag2" id="flag2" class="select1">
				<option value="" selected="selected"></option>
				<option value="3"><?php echo write_lang('key_mover_para_a_familia','Mover para a familia');?></option>
				<option value="4">Mover para familia e Ativar</option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familias(0,"products_families","products_families_lang",$tipo);?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
<?
}

if (!function_exists('gera_lista_familias_cat')) 
{
	function gera_lista_familias_cat($id_org=0,$tabela1,$tabela1_lang)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql="Select * from $tabela1 where ID_ORG='0' order by Ordem Asc";
		$hRes=$obj->db_open($db_link,$DB_name,$sql);
		echo '<select name="s_familia">';
		if($id_org==0) echo '<option value="0" selected>- todas -</option>';
		else echo '<option value="0">- todas -</option>';
		while(!$obj->EOF())
		{
				echo '<option value="'.$obj->db_data["ID"].'"';
				if($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
				$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
				$obj2=new DataObj;
				$hRes=$obj2->db_open($db_link,$DB_name,$sql);   
				echo '>'.$obj2->db_data["Cabecalho"].'</option>';			
				$obj2->db_close();

			gera_lista_subfamilias($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang);	    
			$obj->db_next();
		}
		$obj->db_close();
		echo '</select>';
	}
}

function show_familias($id_org=0,$T,$Tlang,$tipo='')
{		
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	if(($tipo > 0 || $tipo==0) && $tipo!='') $tp = ' and Tipo='.$tipo;
	else $tp='';
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from '.$T.' where ID_ORG="0" '.$tp.' order by Ordem Asc');
    echo '<select name="m_familia2" style="width:150px">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$rs->EOF())
    {
	    echo '<option value="'.$rs->db_data['ID'].'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from '.$Tlang.' where ID_PAI='.$rs->db_data['ID'].' and Lang="'.$admin_lang.'"');   
		echo '>'.$rs2->db_data['Cabecalho'].'</option>';
	    $rs2->db_close();
	    show_familiasb($rs->db_data['ID'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function show_familiasb($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from '.$T.' where ID_ORG="'.$id_start.'" order by Ordem Asc');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if($id_org==$rs->db_data['ID']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from '.$Tlang.' where ID_PAI="'.$rs->db_data['ID'].'" and Lang="'.$admin_lang.'"');
		echo '>';
		$i=0;while($i<$c){echo '&nbsp;';$i++;}
		echo $rs2->db_data['Cabecalho'].'</option>';
	    $rs2->db_close();
	    show_familiasb($rs->db_data['ID'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();	
}
?>