<?php
function ApagarProduto($ID=0)
{
	if($ID<1) return;

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	
	$sql="DELETE FROM products WHERE ID='$ID'";
	$res=Runquery($db_link,$DB_name,$sql);
	if (!$res)  echo db_error($db_link).$sql."<br>";

	$sql="DELETE FROM products_lang WHERE ID_PAI='$ID'";
	$res=Runquery($db_link,$DB_name,$sql);
	if (!$res)  echo db_error($db_link).$sql."<br>";


	$sql="DELETE FROM products_objs WHERE obj_type_id='$ID'";
	$res=Runquery($db_link,$DB_name,$sql);
	if (!$res)  echo db_error($db_link).$sql."<br>";


	$sql="DELETE FROM products_families_obj WHERE ID_PROD='$ID'";
	$res=Runquery($db_link,$DB_name,$sql);
	if (!$res)  echo db_error($db_link).$sql."<br>";


	del_produtc_info($ID);
}
function combinations($arrays,$i=0)
{
    if (!isset($arrays[$i])) return array();
    if ($i == count($arrays) - 1) return $arrays[$i];
	// GET COMBINATIONS FROM SUBSEQUENT ARRAYS
    $tmp=combinations($arrays,$i + 1);
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
function show_seo()
{
	global $browser,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width="5px" align="left"></td>
		<td class="titulo2" align="center"><?php echo write_lang('key_campos_extra','CAMPOS EXTRA');?></td>
		<td width="5px" align="right"></td>
	</tr>
    <tr>
		<td width="5px" align="left"></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_mostrar_esconder','Mostrar/Esconder');?>" onClick="javascript:ShowSEO()"></td>
		<td width="5px" align="right"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<?
}
function filtros_ficha_produto_adm($prod_id=0,$fam_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2=new DataObj;
	$filtros=array();
	$return='';
	$sql='
			SELECT
				fp.cat_id,fp.valor_id,fp.filtro_area
			FROM
				filters_products fp
			WHERE
				(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.')
				AND prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
				AND (fp.filtro_area=1 OR fp.filtro_area=2)
			GROUP BY
				fp.valor_id,fp.cat_id
			Order By
				fp.filtro_id asc
			';
	$obj2->db_open($db_link,$DB_name,$sql);
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
				$cat=buscacategoriaTipoeNomex_adm($key);
				$nm_value=buscavalorfiltrosx_adm($value[$a]);
				$arr[$key][]=$key.'spl'.$cat['category_name'].';'.$value[$a].'spl'.$nm_value;
			}
		}
	}

	$arrays=array_values(array_filter($arr));
	$combinations=combinations($arrays);

	$obj_combo='';
	if(count($combinations) > 0)
	{
		$return .= '<table border="1" cellpadding=5>';
		$return .= '<tr>';
		$a=0;
		if(is_array($combinations[$a]))
		{
			$cor2='';
			for($b=0; $b<count($combinations[$a]); $b++)
			{
				$return .= '<td height="25" align="center" style="background:'.$cor2.'">&nbsp;</td>';
			}
		}
		else
		{
			$return .= '<td align="center" style="font-size:10px;text-align:center"><b>&nbsp;</b></td>';
		}


		$return .= '
		<td align="center" style="font-size:10px;text-align:center"><b>REF</b></td>
		<td align="center" style="font-size:10px;text-align:center"><b>IVA</b><span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small; vertical-align:super;">1</span></td>
		<td align="center" style="font-size:10px;text-align:center"><b>PVP c/IVA</b></td>
		<td align="center" style="font-size:10px;text-align:center"><b>PVP s/IVA</b></td>
		<td align="center" style="font-size:10px;text-align:center"><b>Stock</b></td>
		<td align="center" style="font-size:10px;text-align:center"><b>Qtd min</b></td>
		<td align="center" style="font-size:10px;text-align:center"><b>PVP Promo c/IVA</b></td>
		<td align="center" style="font-size:10px;text-align:center"><b>PVP Promo s/IVA</b></td>
		<td>&nbsp;</td>';

		$return .= '</tr>';


		for($a=0; $a<count($combinations); $a++)
		{
			$obj_combo='';
			if($cor=='#F6F6F6') {$cor="#def2fa";}
			else				{$cor="#F6F6F6";}
			$return .= '<tr bgcolor="'.$cor.'">';
			if(is_array($combinations[$a]))
			{
				$cor2='';
				for($b=0; $b<count($combinations[$a]); $b++)
				{
					if($cor2=='#CCFF99') {$cor2="#CCCC99";}
					else				{$cor2="#CCFF99";}
					$n=explode(';',$combinations[$a][$b]);
					$bl_cat=explode('spl',$n[0]);
					$bl_val=explode('spl',$n[1]);
					$return .= '<td height="25" align="center" style="background:'.$cor2.'">'.$bl_val[1].'</td>';
					$obj_combo .= $bl_cat[0].'-'.$bl_val[0].';';
				}
			}
			else
			{
				if($cor2=='#CCFF99') {$cor2="#CCCC99";}
				else				{$cor2="#CCFF99";}
				$n=explode(';',$combinations[$a]);
				$bl_cat=explode('spl',$n[0]);
				$bl_val=explode('spl',$n[1]);
				$return .= '<td height="25" align="center" style="background:'.$cor2.'">'.$bl_cat[1].': '.$bl_val[1].'</td>';
				$obj_combo .= $bl_cat[0].'-'.$bl_val[0].';';
			}
			$return .= products_objs_filtros($prod_id,rtrim($obj_combo,';'),$a);
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
	$sql="SELECT filters_categories.category_type,filters_categories_lang.category_name FROM filters_categories INNER JOIN filters_categories_lang ON filters_categories_lang.category_id=filters_categories.category_id  WHERE filters_categories.category_id='$ID'";
	$dados->db_open($db_link,$DB_name,$sql);
	$category_type=$dados->db_data["category_type"];
	$category_name=$dados->db_data["category_name"];
	$dados->db_close();
	return array('category_type' => $category_type,'category_name' => $category_name);
}
function buscavalorfiltrosx_adm($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$sql="SELECT values_name from filters_values_lang where values_id='$ID' and values_lang='".$GLOBALS['admin_lang']."'";
	$dados->db_open($db_link,$DB_name,$sql);
	$values_name=$dados->db_data["values_name"];
	$dados->db_close();
	return $values_name;
}
// COPIA PARA OUTRO SITE --------------------------------------------------------------------------
function copia_produto($prod_id=0)
{
	if(isset($_POST['copiar_produto']) && isset($_POST['marca_remote']) && isset($_POST['m_familia_remote']) && $_POST['copiar_produto'] == 1 && $_POST['marca_remote'] > 0 && $_POST['m_familia_remote'] > 0)
	{
		$fam_id=$_POST['m_familia_remote'];
		$marca_id=$_POST['marca_remote'];

		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql='SELECT prod_id FROM products_copia WHERE prod_id="'.$prod_id.'"';
		$obj->db_open($db_link,$DB_name,$sql);
		if(!$obj->EOF())
		{
			$sql='UPDATE products_copia SET fam_id="'.$fam_id.'",marca_id="'.$marca_id.'" WHERE prod_id="'.$prod_id.'"';
			Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO products_copia(fam_id,marca_id,prod_id) VALUES ("'.$fam_id.'","'.$marca_id.'","'.$prod_id.'")';
			Runquery($db_link,$DB_name,$sql);
		}
		$obj->db_close();
	}
}
function gera_lista_marcas2_remote($sel_nm,$tabela1,$tabela1_lang,$id_marca=0)
{
    global $DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote);
	$obj=new DataObj;
	$sql="Select $tabela1.*,$tabela1_lang.Nome from $tabela1 inner join $tabela1_lang on $tabela1_lang.ID_PAI=$tabela1.ID WHERE $tabela1_lang.Lang='$admin_lang' order by $tabela1_lang.Nome Asc";
	$hRes=$obj->db_open($db_link,$DB_name_remote,$sql);
    echo '<select name="'.$sel_nm.'_remote" id="'.$sel_nm.'_remote" class="form-control5">';
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
	$db_link=db_opendb_conection($DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name_remote,$sql);
    echo '<select name="m_familia_remote" id="m_familia_remote" class="form-control4">';
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
	$db_link=db_opendb_conection($DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote);
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
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql2='SELECT
				fc.category_id,fcl.category_name
			FROM
				filters_categories AS fc
				INNER JOIN filters_categories_lang AS fcl ON fc.category_id=fcl.category_id
				WHERE
					fcl.category_lang="'.$admin_lang.'"
				ORDER BY
					fcl.category_name ASC';
	$obj2=new DataObj;
	$obj2->db_open($db_link,$DB_name,$sql2);
	$data=array();
	$temp=array();
	$valor_antigo=0;
	while(!$obj2->EOF())
	{
		$temp=array();
		$temp[]=$obj2->db_data['category_id'];
		$temp[]=$obj2->db_data['category_name'];
		$data[]=$temp;
		$obj2->db_next();
	}
	$obj2->db_close();
	return $data;
}
function getFiltrosProd($ID_Fam=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2=new DataObj;
	$sql2='SELECT
				fp.cat_id,fp.valor_id,fp.filtro_area,fp.img_ext
			FROM
				filters_products fp
			WHERE
				prod_id='.$_GET['ID'].'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
			GROUP BY fp.valor_id,fp.cat_id
			Order By fp.filtro_id asc';

	$obj2->db_open($db_link,$DB_name,$sql2);
	$a=0;
	while(!$obj2->EOF())
	{
		$a++;
		$temp[$a]=array('cat_id' => $obj2->db_data['cat_id'],'valor_id' => $obj2->db_data['valor_id'],'filtro_area' => $obj2->db_data['filtro_area'],'img_ext' => $obj2->db_data['img_ext']);
		$obj2->db_next();
	}
	$obj2->db_close();
	return $temp;
}
function produto_personalizacao2($prod_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$new_personalizacao_ordem=$_POST['new_personalizacao_ordem'];
	$new_personalizacao_titulo=$_POST['new_personalizacao_titulo'];
	$new_personalizacao_outros=$_POST['new_personalizacao_outros'];
	for($a=0; $a<count($new_personalizacao_ordem); $a++)
	{
		if(trim($new_personalizacao_titulo[$a]) != '')
		{
			$ins='INSERT INTO personalizacao (bloco_visivel,bloco_ordem) VALUES (1,"'.$new_personalizacao_ordem[$a].'")';
			Runquery($db_link,$DB_name,$ins);
			$bloco_id_new=db_get_last_insert_id($db_link);
			if($bloco_id_new > 0)
			{
				$ins='INSERT INTO personalizacao_lang (bloco_id,bloco_titulo,bloco_lang) VALUES ("'.$bloco_id_new.'","'.addslashes($new_personalizacao_titulo[$a]).'","pt")';
				Runquery($db_link,$DB_name,$ins);
				$ins='INSERT INTO products_personalizacao2 (bloco_id,prod_id,ordem,FLAG,Outros,Outros_on) VALUES ("'.$bloco_id_new.'","'.$prod_id.'","'.$new_personalizacao_ordem[$a].'",1,"'.addslashes($new_personalizacao_outros[$a]).'",1)';
				Runquery($db_link,$DB_name,$ins);
			}
		}
	}
	$bloco_id=$_POST['personalizacao_id'];
	$personalizacao_outros=$_POST['personalizacao_outros'];
	$personalizacao_ordem=$_POST['personalizacao_ordem'];
	$obj=new DataObj;
	for($a=0; $a<count($bloco_id); $a++)
	{
		$bloco_id_p=$bloco_id[$a];
		$personalizacao_outros_p=$personalizacao_outros[$a];
		$personalizacao_ordem_p=$personalizacao_ordem[$a];
		$personalizacao_p=(isset($_POST['personalizacao_'.$bloco_id_p]) ? 1 : 0);
		$sql2='
				SELECT
					prod_id
				FROM
					products_personalizacao2
				WHERE
					prod_id="'.$prod_id.'"
					AND bloco_id="'.$bloco_id_p.'"';
		$obj->db_open($db_link,$DB_name,$sql2);
		if(!$obj->EOF())
		{
			$upd='UPDATE products_personalizacao2 SET Outros="'.addslashes($personalizacao_outros_p).'",Outros_on="'.$personalizacao_p.'", ordem="'.$personalizacao_ordem_p.'",FLAG=1 WHERE prod_id="'.$prod_id.'" AND bloco_id="'.$bloco_id_p.'"';
			Runquery($db_link,$DB_name,$upd);
		}
		else
		{
			$ins='INSERT INTO products_personalizacao2 (bloco_id,prod_id,ordem,FLAG,Outros,Outros_on) VALUES ("'.$bloco_id_p.'","'.$prod_id.'","'.$personalizacao_ordem_p.'",1,"'.addslashes($personalizacao_outros_p).'","'.$personalizacao_p.'")';
			Runquery($db_link,$DB_name,$ins);
		}
		$obj->db_close();
	}
}
function produto_personalizacao($prod_id=0)
{
	$personalizacao=$_POST['personalizacao'];
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$upd='UPDATE products_personalizacao SET FLAG=0 WHERE prod_id="'.$prod_id.'"';
	Runquery($db_link,$DB_name,$upd);
	for($a=0; $a<count($personalizacao); $a++)
	{
		$personalizacao_on=explode('_',$personalizacao[$a]);
		$ordem=0;  $ordem=$_POST['ordem_'.$personalizacao_on[0]];
		$obrigatorio='';  $obrigatorio=explode('_',$_POST['obrigatorio_'.$personalizacao_on[0]]);
		$aparece='';  $aparece=explode('_',$_POST['aparece_'.$personalizacao_on[0]]);
		$personalizacao_ordem =0; $personalizacao_ordem=$_POST['personalizacao_ordem_'.$personalizacao_on[0].'_'.$personalizacao_on[1]];
		$sql2='SELECT
					prod_id
				FROM
					products_personalizacao
				WHERE
					prod_id="'.$prod_id.'"
					AND bloco_id="'.$personalizacao_on[0].'"
					AND bloco_id_filho="'.$personalizacao_on[1].'"';
		$obj->db_open($db_link,$DB_name,$sql2);
		if(!$obj->EOF())
		{
			$upd='UPDATE products_personalizacao SET aparece_com="0",aparece_elelement_id="0",obrigatorio_com="0",obrigatorio_element_id="0",ordem="0",ordemp="'.$personalizacao_ordem.'",bloco_id="'.$personalizacao_on[0].'",bloco_id_filho="'.$personalizacao_on[1].'",FLAG=1 WHERE prod_id="'.$prod_id.'" AND bloco_id="'.$personalizacao_on[0].'" AND bloco_id_filho="'.$personalizacao_on[1].'"';
			Runquery($db_link,$DB_name,$upd);
		}
		else
		{
			$ins='INSERT INTO products_personalizacao (bloco_id,bloco_id_filho,prod_id,obrigatorio_com,obrigatorio_element_id,aparece_com,aparece_elelement_id,ordem,ordemp,FLAG) VALUES ("'.$personalizacao_on[0].'","'.$personalizacao_on[1].'","'.$prod_id.'","'.$obrigatorio[1].'","'.$obrigatorio[0].'","'.$aparece[1].'","'.$aparece[0].'","'.$ordem.'","'.$personalizacao_ordem.'",1)';
			Runquery($db_link,$DB_name,$ins);
		}
		$obj->db_close();
		if($personalizacao_on[0] > 0 && ($personalizacao_on[1] == 0 || $personalizacao_on[1] == '' || !isset($personalizacao_on[1])))
		{
			$personalizacao_outros=$_POST['personalizacao_outros_'.$personalizacao_on[0]];
			if(isset($personalizacao_outros) && $personalizacao_outros)
			{
				$sql2='SELECT
						prod_id
					FROM
						products_personalizacao
					WHERE
						prod_id="'.$prod_id.'"
						AND bloco_id="'.$personalizacao_on[0].'"
						AND bloco_id_filho="0"
						AND Outros<>""
						AND Outros_on='.$personalizacao_on[0];
				$obj->db_open($db_link,$DB_name,$sql2);
				if(!$obj->EOF())
				{
					$upd='UPDATE products_personalizacao SET Outros="'.addslashes($personalizacao_outros).'",aparece_com="0",aparece_elelement_id="0",obrigatorio_com="0",obrigatorio_element_id="0",ordem="0",bloco_id="'.$personalizacao_on[0].'",bloco_id_filho="0",FLAG=1,Outros_on='.$personalizacao_on[0].' WHERE prod_id="'.$prod_id.'" AND bloco_id="'.$personalizacao_on[0].'" AND bloco_id_filho="0" AND Outros<>"" AND Outros_on='.$personalizacao_on[0].'';
					Runquery($db_link,$DB_name,$upd);
				}
				else
				{
					$ins='INSERT INTO products_personalizacao (bloco_id,bloco_id_filho,prod_id,obrigatorio_com,obrigatorio_element_id,aparece_com,aparece_elelement_id,ordem,FLAG,Outros,Outros_on) VALUES ("'.$personalizacao_on[0].'","0","'.$prod_id.'","0","0","0","0","0",1,"'.addslashes($personalizacao_outros).'",1)';
					Runquery($db_link,$DB_name,$ins);
				}
				$obj->db_close();
			}
		}
	}
	$del='DELETE FROM products_personalizacao WHERE prod_id="'.$prod_id.'" AND FLAG=0';
	Runquery($db_link,$DB_name,$del);
}
function get_personalizacao($prod_id=0,$bloco_id=0,$bloco_id2=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$ordem=0;
	$on=0;
	$sql2='SELECT
				ordem,ordemp,bloco_id,bloco_id_filho,obrigatorio_com,aparece_com
			FROM
				products_personalizacao2
			WHERE
				prod_id="'.$prod_id.'"
				AND bloco_id="'.$bloco_id.'"
				AND bloco_id_filho="'.$bloco_id2.'"';
	$obj->db_open($db_link,$DB_name,$sql2);
	if(!$obj->EOF())
	{
		$ordem=$obj->db_data['ordem'];
		$ordemp=$obj->db_data['ordemp'];
		$bloco_id=$obj->db_data['bloco_id'];
		$bloco_id_filho=$obj->db_data['bloco_id_filho'];
		$obrigatorio_com=$obj->db_data['obrigatorio_com'];
		$aparece_com=$obj->db_data['aparece_com'];
		$on=1;
	}
	$obj->db_close(1);
	if($on == 1) return array('ordem' => $ordem,'ordemp' => $ordemp,'bloco_id' => $bloco_id,'bloco_id_filho' => $bloco_id_filho,'obrigatorio_com' => $obrigatorio_com,'aparece_com' => $aparece_com);
	else return array();
}
function get_personalizacao_outro($prod_id=0,$bloco_id=0,$bloco_id2=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$ordem=0;
	$on=0;
	$sql2='SELECT
				ordem,ordemp,bloco_id,bloco_id_filho,obrigatorio_com,aparece_com,Outros
			FROM
				products_personalizacao
			WHERE
				prod_id="'.$prod_id.'"
				AND bloco_id="'.$bloco_id.'"
				AND bloco_id_filho="0"
				AND Outros <> ""';
	$obj->db_open($db_link,$DB_name,$sql2);
	if(!$obj->EOF())
	{
		$Outro=$obj->db_data['Outros'];
		$ordemp=$obj->db_data['ordemp'];
		$on=1;
	}
	$obj->db_close(1);
	if($on == 1) return array('Outro' => $Outro,'ordemp' => $ordemp);
	else return array();
}
function get_personalizacao_prod($prod_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;$obj2=new DataObj;
	$ordem=0;$on=0;
	$sql2='SELECT
				pp.bloco_id,pp.bloco_id_filho
			FROM
				products_personalizacao pp
			WHERE
				prod_id="'.$prod_id.'"';
	$obj->db_open($db_link,$DB_name,$sql2);
	$return[]=array('id' => '','nome' => 'Não é obrigatório');
	$return[]=array('id' => '0','nome' => 'Obrigatório');
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$bloco_id=$obj->db_data['bloco_id'];
			$bloco_id_filho=$obj->db_data['bloco_id_filho'];
			$bloco_titulo_1=''; $bloco_tipo_1=0;
			$obj2->db_open($db_link,$DB_name,'SELECT bloco_tipo,bloco_titulo FROM personalizacao INNER JOIN personalizacao_lang ON personalizacao.bloco_id=personalizacao_lang.bloco_id  WHERE bloco_lang="pt" AND personalizacao.bloco_id="'.$bloco_id.'"');
			if(!$obj2->EOF())
			{
				$bloco_titulo_1=$obj2->db_data['bloco_titulo'];
				$bloco_tipo_1 	= $obj2->db_data['bloco_tipo'];
			}
			$obj2->db_close();
			$bloco_titulo_2='';
			if($bloco_id_filho > 0)
			{
				$obj2->db_open($db_link,$DB_name,'SELECT bloco_titulo FROM personalizacao_lang WHERE bloco_lang="pt" AND bloco_id="'.$bloco_id_filho.'"');
				if(!$obj2->EOF()) $bloco_titulo_2=$obj2->db_data['bloco_titulo'];
				$obj2->db_close();
			}
			if($bloco_titulo_2 != '') $return[]=array('id' => $bloco_id_filho,'id_pai' => $bloco_id,'nome' => $bloco_titulo_1.': '.$bloco_titulo_2);
			elseif($bloco_tipo_1 != 2 && $bloco_tipo_1 != 3 && $bloco_tipo_1 != 4) $return[]=array('id' => $bloco_id,'id_pai' => $bloco_id,'nome' => $bloco_titulo_1);
			$obj->db_next();
		}
	}
	$obj->db_close(1);
	return $return;
}
function get_personalizacao_prod2($prod_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;$obj2=new DataObj;
	$ordem=0;
	$on=0;
	$sql2='SELECT
				pp.bloco_id,pp.bloco_id_filho
			FROM
				products_personalizacao pp
			WHERE
				prod_id="'.$prod_id.'"';
	$obj->db_open($db_link,$DB_name,$sql2);
	$return[]=array('id' => '','nome' => 'Não faz nada');
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$bloco_id=$obj->db_data['bloco_id'];
			$bloco_id_filho=$obj->db_data['bloco_id_filho'];
			$bloco_titulo_1=''; $bloco_tipo_1=0;
			$obj2->db_open($db_link,$DB_name,'SELECT bloco_tipo,bloco_titulo FROM personalizacao INNER JOIN personalizacao_lang ON personalizacao.bloco_id=personalizacao_lang.bloco_id  WHERE bloco_lang="pt" AND personalizacao.bloco_id="'.$bloco_id.'"');
			if(!$obj2->EOF())
			{
				$bloco_titulo_1=$obj2->db_data['bloco_titulo'];
				$bloco_tipo_1 	= $obj2->db_data['bloco_tipo'];
			}
			$obj2->db_close();
			$bloco_titulo_2='';
			if($bloco_id_filho > 0)
			{
				$obj2->db_open($db_link,$DB_name,'SELECT bloco_titulo FROM personalizacao_lang WHERE bloco_lang="pt" AND bloco_id="'.$bloco_id_filho.'"');
				if(!$obj2->EOF()) $bloco_titulo_2=$obj2->db_data['bloco_titulo'];
				$obj2->db_close();
			}
			if($bloco_titulo_2 != '') $return[]=array('id' => $bloco_id_filho,'id_pai' => $bloco_id,'nome' => $bloco_titulo_1.': '.$bloco_titulo_2);
			elseif($bloco_tipo_1 != 2 && $bloco_tipo_1 != 3 && $bloco_tipo_1 != 4) $return[]=array('id' => $bloco_id,'id_pai' => $bloco_id,'nome' => $bloco_titulo_1);
			$obj->db_next();
		}
	}
	$obj->db_close(1);
	return $return;
}
function InsertNewProd($id_org,$ordem,$cabecalho,$ref,$cod_ean,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1,$tem_prod_destaque_homepage,$preco_custo_custom,$store_icons,$bateria,$estado,$localizacao_fisica)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$dominio,$caminho,$userx,$passwordx,$site_shell_url,$admin_id;
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
	$tem_prod_destaque_homepage=$tem_prod_destaque_homepage;
	$ref=str2SQLstr($ref);
	$cod_ean=str2SQLstr($cod_ean);
	$tipo=str2SQLstr($tipo);
	$stock_txt1=str2SQLstr($stock_txt);
	$strSearchWords=str2SQLstr($strSearchWords);
	$bonus=$_POST['pontos'].$_POST['tpoints'];

	$bateria=str2SQLstr($bateria);
	$estado=str2SQLstr($estado);
	$localizacao_fisica=str2SQLstr($localizacao_fisica);

	if(isset($_POST['carrinho'])) $carrinho=$_POST['carrinho'];	else $carrinho=0;

	if(isset($_POST['portes_gratis'])) $portes_gratis=$_POST['portes_gratis'];
	else $portes_gratis=0;

	$ee_ext["name"] = $_FILES['ee_ext']['name'];
	$ee_ext["temp"] = $_FILES['ee_ext']['tmp_name'];
	$Apagar_ee_ext = $_POST['Apagar_ee_ext'];
	$tem_ee_ext = $_POST['tem_ee_ext'];

	$ue_ext["name"] = $_FILES['ue_ext']['name'];
	$ue_ext["temp"] = $_FILES['ue_ext']['tmp_name'];
	$Apagar_ue_ext = $_POST['Apagar_ue_ext'];
	$tem_ue_ext = $_POST['tem_ue_ext'];

	$dt_ext["name"] = $_FILES['dt_ext']['name'];
	$dt_ext["temp"] = $_FILES['dt_ext']['tmp_name'];
	$Apagar_dt_ext = $_POST['Apagar_dt_ext'];
	$tem_dt_ext = $_POST['tem_dt_ext'];


	$destaque_ext["name"] = $_FILES['destaque_ext']['name'];
	$destaque_ext["temp"] = $_FILES['destaque_ext']['tmp_name'];
	$Apagar_destaque_ext = $_POST['Apagar_destaque_ext'];
	$tem_destaque_ext = $_POST['tem_destaque_ext'];

	$destaque2_ext["name"] = $_FILES['destaque2_ext']['name'];
	$destaque2_ext["temp"] = $_FILES['destaque2_ext']['tmp_name'];
	$Apagar_destaque2_ext = $_POST['Apagar_destaque2_ext'];
	$tem_destaque2_ext = $_POST['tem_destaque2_ext'];

	$destaque3_ext["name"] = $_FILES['destaque3_ext']['name'];
	$destaque3_ext["temp"] = $_FILES['destaque3_ext']['tmp_name'];
	$Apagar_destaque3_ext = $_POST['Apagar_destaque3_ext'];
	$tem_destaque3_ext = $_POST['tem_destaque3_ext'];

	$destaque4_ext["name"] = $_FILES['destaque4_ext']['name'];
	$destaque4_ext["temp"] = $_FILES['destaque4_ext']['tmp_name'];
	$Apagar_destaque4_ext = $_POST['Apagar_destaque4_ext'];
	$tem_destaque4_ext = $_POST['tem_destaque4_ext'];


	$Video=$_POST['Video'];
	$corEE=$_POST['corEE'];
	$home_destaque=$_POST['home_destaque'];

	$u_catetiq=$_POST['u_catetiq'];
	$u_coretiq=$_POST['u_coretiq'];

	$altera_img1=0;if($ee_ext["name"]){$altera_img1=1;$pos=strrpos($ee_ext["name"],".");$img1_ext=substr($ee_ext["name"],$pos+1,strlen($ee_ext["name"])+1-$pos);}
	$altera_img2=0;if($ue_ext["name"]){$altera_img2=1;$pos=strrpos($ue_ext["name"],".");$img2_ext=substr($ue_ext["name"],$pos+1,strlen($ue_ext["name"])+1-$pos);}
	$altera_img3=0;if($dt_ext["name"]){$altera_img3=1;$pos=strrpos($dt_ext["name"],".");$img3_ext=substr($dt_ext["name"],$pos+1,strlen($dt_ext["name"])+1-$pos);}
	$altera_img4=0;if($destaque_ext["name"]){$altera_img4=1;$pos=strrpos($destaque_ext["name"],".");$img4_ext=substr($destaque_ext["name"],$pos+1,strlen($destaque_ext["name"])+1-$pos);}
	$altera_img5=0;if($destaque2_ext["name"]){$altera_img5=1;$pos=strrpos($destaque2_ext["name"],".");$img5_ext=substr($destaque2_ext["name"],$pos+1,strlen($destaque2_ext["name"])+1-$pos);}
	$altera_img6=0;if($destaque3_ext["name"]){$altera_img6=1;$pos=strrpos($destaque3_ext["name"],".");$img6_ext=substr($destaque3_ext["name"],$pos+1,strlen($destaque3_ext["name"])+1-$pos);}
	$altera_img7=0;if($destaque4_ext["name"]){$altera_img7=1;$pos=strrpos($destaque4_ext["name"],".");$img7_ext=substr($destaque4_ext["name"],$pos+1,strlen($destaque4_ext["name"])+1-$pos);}

	

	if(isset($_POST['estados_produto'])) $estados_produto=$_POST['estados_produto']; else $estados_produto=0;
	if(isset($_POST['encomenda_sem_stock'])) $encomenda_sem_stock=$_POST['encomenda_sem_stock']; else $encomenda_sem_stock=0;
	if(isset($_POST['produto_standard'])) $produto_standard=$_POST['produto_standard']; else $produto_standard=0;
	if(isset($_POST['qnt_esp'])) $qnt_esp=$_POST['qnt_esp']; else $qnt_esp=0;
	if(isset($_POST['c_submissao_ficheiro'])) $c_submissao_ficheiro=$_POST['c_submissao_ficheiro']; else $c_submissao_ficheiro=0;
	if(isset($_POST['largura'])) $largura=$_POST['largura']; else $largura=0;
	if(isset($_POST['altura'])) $altura=$_POST['altura']; else $altura=0;
	if(isset($_POST['profundidade'])) $profundidade=$_POST['profundidade']; else $profundidade=0;

	if(isset($_POST['servicos'])) $servicos=implode(';',$_POST['servicos']); else $servicos='';
	if(isset($_POST['entregas'])) $entregas=implode(';',$_POST['entregas']); else $entregas='';

	if(isset($_POST['margem_fam'])) $margem_fam=$_POST['margem_fam']; else $margem_fam=0;



	$personalizacao_on='';
	if(isset($_POST['personalizacao']))
	{
		$personalizacao=$_POST['personalizacao'];
		for($a=0; $a<count($personalizacao); $a++)
		{
			$personalizacao_on .= $personalizacao[$a].';';
		}
	}
	$descontopreco=$_POST['descontopreco'];
	$sql="
		INSERT INTO products
			(
				ID_ORG,
				Ordem,
				Ref,
				Ref_Ean,
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
				estados_produto,
				encomenda_sem_stock,
				produto_standard,
				servicos,
				entregas,
				qnt_esp,
				c_submissao_ficheiro,
				largura,
				altura,
				profundidade,
				tem_prod_destaque_homepage,
				Video,
				corEE,
				ee_ext,
				ue_ext,
				dt_ext,
				destaque_ext,
				destaque2_ext,
				destaque3_ext,
				destaque4_ext,
				preco_custo_custom,
				idtime,
				ID_Admin,
				margem_fam,
				store_icons, 
				bateria,
				estado,
				localizacao_fisica
			)
			VALUES
			(
				'$id_org','$ordem',$ref,$cod_ean,$preco1,$preco2,$precopromo1,$precopromo2,$preco_prec,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$carrinho,$tipo,$strSearchWords,
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
				'".$estados_produto."',
				'".$encomenda_sem_stock."',
				'".$produto_standard."',
				'".$servicos."',
				'".$Entregas."',
				'".$qnt_esp."',
				'$c_submissao_ficheiro',
				'".$largura."',
				'".$altura."',
				'".$profundidade."',
				'".$tem_prod_destaque_homepage."',
				'$Video',
				'$corEE',
				'$img1_ext',
				'$img2_ext',
				'$img3_ext',
				'$img4_ext',
				'$img5_ext',
				'$img6_ext',
				'$img7_ext',
				'".$preco_custo_custom."',
				'".date("Y-m-d H:m:s")."',
				'".$admin_id."',
				'".$margem_fam."',
				'".$store_icons."',
				".$bateria.",
				".$estado.",
				".$localizacao_fisica."
			)
		";
    $res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);

		if($altera_img1)copia_segura($ee_ext["temp"],$img_path.'EE/'.$id."_1.".$img1_ext);
		if($altera_img2)copia_segura($ue_ext["temp"],$img_path.'UE/'.$id."_1.".$img2_ext);
		if($altera_img3)copia_segura($dt_ext["temp"],$img_path.'DT/'.$id."_1.".$img3_ext);
		if($altera_img4)copia_segura($destaque_ext["temp"],$img_path.$id."_d.".$img4_ext);
		if($altera_img5)copia_segura($destaque2_ext["temp"],$img_path.$id."_d2.".$img5_ext);
		if($altera_img6)copia_segura($destaque3_ext["temp"],$img_path.$id."_d3.".$img6_ext);
		if($altera_img7)copia_segura($destaque4_ext["temp"],$img_path.$id."_d4.".$img7_ext);


		if(isset($GLOBALS['CAMPOS_EXTRA']) && count($GLOBALS['CAMPOS_EXTRA']) > 0)
		{
			$i=0;
			while($i<count($GLOBALS['CAMPOS_EXTRA']))
			{
				$campo = $GLOBALS['CAMPOS_EXTRA'][$i]['campo'];
				$valor=addslashes($_POST[$campo]);

				$sql = 'UPDATE products SET '.$GLOBALS['CAMPOS_EXTRA'][$i]['campo'].'="'.$valor.'" WHERE ID='.$new_id;
				$res=Runquery($db_link,$DB_name,$sql);
				$i++;
			}
		}

		products_images_save($new_id,$Img_peq,$tem_foto,$Apagar_img);
		products_objs_save($new_id);
		produto_personalizacao2($new_id);
		products_families_save($new_id,$id_org);
		$fams=encontraFamiliasRecursiva_x($id_org);
		if(isset($fams[0])) $fam_id1=$fams[0]; else $fam_id1=$id_org;
		if(isset($fams[1])) $fam_id2=$fams[1]; else $fam_id2=0;
		if(isset($fams[2])) $fam_id3=$fams[2]; else $fam_id3=0;
		if(isset($fams[3])) $fam_id4=$fams[3]; else $fam_id4=0;

		$sql ='INSERT INTO filters_products(
				cat_id,
				valor_id,
				prod_id,
				fam_id1,
				fam_id2,
				fam_id3,
				fam_id4,
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
				"'.$fam_id4.'",
				"'.$marca.'",
				"",
				1,
				1
			)
		';

		InsertProdLinguas($new_id,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$texto4,$cabecalho_texto5,$texto5,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1);
	}
	else
	{
		$error = db_error($db_link);
		error_log('[products] Unable to insert product: '.$error);
		echo $error.$sql;
	}
	db_closedb_conection($db_link);
	return array('res' => $res,'prod_id' => $new_id);
}
function InsertProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$texto4,$cabecalho_texto5,$texto5,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho1=str2SQLstr($cabecalho);
	$URL1=str2SQLstr($URL);
	$subtitulo=$_POST['subtitulo'];
	if(isset($_POST['tempo_entrega'])) $tempo_entrega=$_POST['tempo_entrega']; else $tempo_entrega='';
	$sql="insert into products_lang (ID_PAI,Cabecalho,Subtitulo,URL,Texto1,Texto2,Texto3,Tamanho,Cor,Lang,Texto4,Cabecalho_Texto5,Texto5,tempo_entrega,SEO_title,SEO_description,SEO_keywords,SEO_alt,SEO_h1) values ($id_pai,$cabecalho1,'$subtitulo',$URL1,'$texto1','$texto2','$texto3','$tamanho','$cor','$admin_lang','$texto4','$cabecalho_texto5','$texto5','$tempo_entrega','$SEO_title','$SEO_description','$SEO_keywords','$SEO_alt','$SEO_h1')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link).$sql;
    return($res);
}
function ChangeProd($id,$id_org,$ordem,$cabecalho,$ref,$cod_ean,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$Apagar_img,$tem_foto,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1,$tem_prod_destaque_homepage,$preco_custo_custom,$store_icons,$bateria,$estado,$localizacao_fisica)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_shell_url,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	/****************************************/
	/* Garantir que so um produto esteja destacado em homepage ao atualizar */
	if( $tem_prod_destaque_homepage)
	{
		$sql = 'update products set tem_prod_destaque_homepage=0';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	/****************************************/
	if(!$m_lang) $m_lang=$admin_lang;
	$altera=0;
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select ee_ext,ue_ext,dt_ext,destaque_ext,destaque2_ext,destaque3_ext,destaque4_ext from products WHERE ID="'.$id.'"');
	if(!$rs->EOF())
	{
		$altera=1;
		$img1_ext=$rs->db_data["ee_ext"];
		$img2_ext=$rs->db_data["ue_ext"];
		$img3_ext=$rs->db_data["dt_ext"];
		$img4_ext=$rs->db_data["destaque_ext"];
		$img5_ext=$rs->db_data["destaque2_ext"];
		$img6_ext=$rs->db_data["destaque3_ext"];
		$img7_ext=$rs->db_data["destaque4_ext"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewProd($id_org,$ordem,$cabecalho,$ref,$cod_ean,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1,$tem_prod_destaque_homepage,$preco_custo_custom,$store_icons,$bateria,$estado,$localizacao_fisica);
	else
	{
		$ee_ext["name"] = $_FILES['ee_ext']['name'];
		$ee_ext["temp"] = $_FILES['ee_ext']['tmp_name'];
		$Apagar_ee_ext = $_POST['Apagar_ee_ext'];
		$tem_ee_ext = $_POST['tem_ee_ext'];

		$ue_ext["name"] = $_FILES['ue_ext']['name'];
		$ue_ext["temp"] = $_FILES['ue_ext']['tmp_name'];
		$Apagar_ue_ext = $_POST['Apagar_ue_ext'];
		$tem_ue_ext = $_POST['tem_ue_ext'];

		$dt_ext["name"] = $_FILES['dt_ext']['name'];
		$dt_ext["temp"] = $_FILES['dt_ext']['tmp_name'];
		$Apagar_dt_ext = $_POST['Apagar_dt_ext'];
		$tem_dt_ext = $_POST['tem_dt_ext'];

		$destaque_ext["name"] = $_FILES['destaque_ext']['name'];
		$destaque_ext["temp"] = $_FILES['destaque_ext']['tmp_name'];
		$Apagar_destaque_ext = $_POST['Apagar_destaque_ext'];
		$tem_destaque_ext = $_POST['tem_destaque_ext'];

		$destaque2_ext["name"] = $_FILES['destaque2_ext']['name'];
		$destaque2_ext["temp"] = $_FILES['destaque2_ext']['tmp_name'];
		$Apagar_destaque2_ext = $_POST['Apagar_destaque2_ext'];
		$tem_destaque2_ext = $_POST['tem_destaque2_ext'];

		$destaque3_ext["name"] = $_FILES['destaque3_ext']['name'];
		$destaque3_ext["temp"] = $_FILES['destaque3_ext']['tmp_name'];
		$Apagar_destaque3_ext = $_POST['Apagar_destaque3_ext'];
		$tem_destaque3_ext = $_POST['tem_destaque3_ext'];

		$destaque4_ext["name"] = $_FILES['destaque4_ext']['name'];
		$destaque4_ext["temp"] = $_FILES['destaque4_ext']['tmp_name'];
		$Apagar_destaque4_ext = $_POST['Apagar_destaque4_ext'];
		$tem_destaque4_ext = $_POST['tem_destaque4_ext'];

		$Video=$_POST['Video'];
		$corEE=$_POST['corEE'];

		$u_catetiq=$_POST['u_catetiq'];
		$u_coretiq=$_POST['u_coretiq'];

		$altera_img1=0;if($ee_ext["name"]){$altera_img1=1;$pos=strrpos($ee_ext["name"],".");$img1_ext=substr($ee_ext["name"],$pos+1,strlen($ee_ext["name"])+1-$pos);}
		$altera_img2=0;if($ue_ext["name"]){$altera_img2=1;$pos=strrpos($ue_ext["name"],".");$img2_ext=substr($ue_ext["name"],$pos+1,strlen($ue_ext["name"])+1-$pos);}
		$altera_img3=0;if($dt_ext["name"]){$altera_img3=1;$pos=strrpos($dt_ext["name"],".");$img3_ext=substr($dt_ext["name"],$pos+1,strlen($dt_ext["name"])+1-$pos);}
		$altera_img4=0;if($destaque_ext["name"]){$altera_img4=1;$pos=strrpos($destaque_ext["name"],".");$img4_ext=substr($destaque_ext["name"],$pos+1,strlen($destaque_ext["name"])+1-$pos);}
		$altera_img5=0;if($destaque2_ext["name"]){$altera_img5=1;$pos=strrpos($destaque2_ext["name"],".");$img5_ext=substr($destaque2_ext["name"],$pos+1,strlen($destaque2_ext["name"])+1-$pos);}
		$altera_img6=0;if($destaque3_ext["name"]){$altera_img6=1;$pos=strrpos($destaque3_ext["name"],".");$img6_ext=substr($destaque3_ext["name"],$pos+1,strlen($destaque3_ext["name"])+1-$pos);}
		$altera_img7=0;if($destaque4_ext["name"]){$altera_img7=1;$pos=strrpos($destaque4_ext["name"],".");$img7_ext=substr($destaque4_ext["name"],$pos+1,strlen($destaque4_ext["name"])+1-$pos);}

		
		if($altera_img1)copia_segura($ee_ext["temp"],$img_path.'EE/'.$id."_1.".$img1_ext);
		if($altera_img2)copia_segura($ue_ext["temp"],$img_path.'UE/'.$id."_1.".$img2_ext);
		if($altera_img3)copia_segura($dt_ext["temp"],$img_path.'DT/'.$id."_1.".$img3_ext);
		//IMAGEM NUMERO 1
		if($tem_ee_ext && $Apagar_ee_ext){$ficheiro1=$id."_1.".$img1_ext;unlink($img_path.'EE/'.$ficheiro1);if(!$altera_img)$img1_ext='';}
		if($altera_img1)copia_segura($ee_ext["temp"],$img_path.'EE/'.$id."_1.".$img1_ext);
		//IMAGEM NUMERO 2
		if($tem_ue_ext && $Apagar_ue_ext){$ficheiro2=$id."_1.".$img2_ext;unlink($img_path.'UE/'.$ficheiro2);if(!$altera_img2)$img2_ext='';}
		if($altera_img2)copia_segura($ue_ext["temp"],$img_path.'UE/'.$id."_1.".$img2_ext);
		//IMAGEM NUMERO 3
		if($tem_dt_ext && $Apagar_dt_ext){$ficheiro3=$id."_1.".$img3_ext;unlink($img_path.'DT/'.$ficheiro3);if(!$altera_img3)$img3_ext='';}
		if($altera_img3)copia_segura($dt_ext["temp"],$img_path.'DT/'.$id."_1.".$img3_ext);
		//IMAGEM NUMERO 4
		if($tem_destaque_ext && $Apagar_destaque_ext){$ficheiro4=$id."_d.".$img4_ext;unlink($img_path.$ficheiro4);if(!$altera_img4)$img4_ext='';}
		if($altera_img4)copia_segura($destaque_ext["temp"],$img_path.$id."_d.".$img4_ext);

		if($tem_destaque2_ext && $Apagar_destaque2_ext){$ficheiro5=$id."_d2.".$img5_ext;unlink($img_path.$ficheiro5);if(!$altera_img5)$img5_ext='';}
		if($altera_img5)copia_segura($destaque2_ext["temp"],$img_path.$id."_d2.".$img5_ext);
		if($tem_destaque3_ext && $Apagar_destaque3_ext){$ficheiro6=$id."_d3.".$img6_ext;unlink($img_path.$ficheiro6);if(!$altera_img6)$img6_ext='';}
		if($altera_img6)copia_segura($destaque3_ext["temp"],$img_path.$id."_d3.".$img6_ext);
		if($tem_destaque4_ext && $Apagar_destaque4_ext){$ficheiro7=$id."_d4.".$img7_ext;unlink($img_path.$ficheiro7);if(!$altera_img7)$img7_ext='';}
		if($altera_img7)copia_segura($destaque4_ext["temp"],$img_path.$id."_d4.".$img7_ext);


		$id_org1=str2SQLstr($id_org);
		$ordem1=str2SQLstr($ordem);
		$cabecalho1=str2SQLstr($cabecalho);
		$ref1=str2SQLstr($ref);
		$cod_ean=str2SQLstr($cod_ean);
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
		$tem_prod_destaque_homepage=str2SQLstr($tem_prod_destaque_homepage);
		$tipo=str2SQLstr($tipo);
		$stock_txt1=str2SQLstr($stock_txt);
		$strSearchWords=str2SQLstr($strSearchWords);
		$bonus=$_POST['pontos'].$_POST['tpoints'];

		$bateria=str2SQLstr($bateria);
		$estado=str2SQLstr($estado);
		$localizacao_fisica=str2SQLstr($localizacao_fisica);

		if(isset($_POST['carrinho'])) $carrinho=$_POST['carrinho'];	else $carrinho=0;
		if(isset($_POST['estados_produto'])) $estados_produto=$_POST['estados_produto']; else $estados_produto=0;
		if(isset($_POST['encomenda_sem_stock'])) $encomenda_sem_stock=$_POST['encomenda_sem_stock']; else $encomenda_sem_stock=0;
		if(isset($_POST['produto_standard'])) $produto_standard=$_POST['produto_standard']; else $produto_standard=0;
		if(isset($_POST['qnt_esp'])) $qnt_esp=$_POST['qnt_esp']; else $qnt_esp=0;
		if(isset($_POST['c_submissao_ficheiro'])) $c_submissao_ficheiro=$_POST['c_submissao_ficheiro']; else $c_submissao_ficheiro=0;
		if(isset($_POST['largura'])) $largura=$_POST['largura']; else $largura=0;
		if(isset($_POST['altura'])) $altura=$_POST['altura']; else $altura=0;
		if(isset($_POST['profundidade'])) $profundidade=$_POST['profundidade']; else $profundidade=0;
		if(isset($_POST['servicos'])) $servicos=implode(';',$_POST['servicos']); else $servicos='';
		if(isset($_POST['entregas'])) $entregas=implode(';',$_POST['entregas']); else $entregas='';

		if(isset($_POST['margem_fam'])) $margem_fam=$_POST['margem_fam']; else $margem_fam=0;

		$personalizacao_on='';
		if(isset($_POST['personalizacao']))
		{
			$personalizacao=$_POST['personalizacao'];
			for($a=0; $a<count($personalizacao); $a++)
			{
				$personalizacao_on .= $personalizacao[$a].';';
			}
		}
		$sql="UPDATE products
				SET
					ID_ORG=$id_org1,
					Ordem=$ordem1,
					Ref=$ref1,
					Ref_Ean=$cod_ean,
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
					tem_prod_destaque_homepage=$tem_prod_destaque_homepage,
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
					estados_produto='".$estados_produto."',
					encomenda_sem_stock='".$encomenda_sem_stock."',
					ID_Admin='".$admin_id."',
					udtime='".date("Y-m-d H:m:s")."',
					Video='$Video',
					corEE='$corEE',
					ee_ext='$img1_ext',
					ue_ext='$img2_ext',
					dt_ext='$img3_ext',
					u_catetiq='$u_catetiq',
					u_coretiq='$u_coretiq',
					destaque_ext='$img4_ext',
					destaque2_ext='$img5_ext',
					destaque3_ext='$img6_ext',
					destaque4_ext='$img7_ext',
					largura='$largura',
					altura='$altura',
					profundidade='$profundidade',
					produto_standard='".$produto_standard."',
					servicos='".$servicos."',
					entregas='".$entregas."',
					qnt_esp='".$qnt_esp."',
					c_submissao_ficheiro='".$c_submissao_ficheiro."',
					largura='".$largura."',
					altura='".$altura."',
					profundidade='".$profundidade."',
					preco_custo_custom='".$preco_custo_custom."',
					margem_fam='".$margem_fam."',
					store_icons='".$store_icons."',
					bateria=".$bateria.",
					estado=".$estado.",
					localizacao_fisica=".$localizacao_fisica."
				WHERE
					ID=$id";

					// echo '<pre>';
					// echo $sql;
					// echo '</pre>';
					// die();
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			if($aproved==0)
			{
				//Atualiza a cache dos artigos
				del_produtc_info($id,'',1,0,$admin_lang,1);
			}
			
			if(isset($GLOBALS['CAMPOS_EXTRA']) && count($GLOBALS['CAMPOS_EXTRA']) > 0)
			{
				$i=0;
				while($i<count($GLOBALS['CAMPOS_EXTRA']))
				{
					$campo = $GLOBALS['CAMPOS_EXTRA'][$i]['campo'];
					$valor=addslashes($_POST[$campo]);

					$sql = 'UPDATE products SET '.$GLOBALS['CAMPOS_EXTRA'][$i]['campo'].'="'.$valor.'" WHERE ID='.$id;
					$res=Runquery($db_link,$DB_name,$sql);
					$i++;
				}
			}
			products_images_save($id,$Img_peq,$tem_foto,$Apagar_img);
			products_objs_save($id);
			produto_personalizacao2($id);
			products_families_save($id,$id_org);
			$fams=encontraFamiliasRecursiva_x($id_org);
			if(isset($fams[0])) $fam_id1=$fams[0]; else $fam_id1=$id_org;
			if(isset($fams[1])) $fam_id2=$fams[1]; else $fam_id2=0;
			if(isset($fams[2])) $fam_id3=$fams[2]; else $fam_id3=0;
			if(isset($fams[3])) $fam_id4=$fams[3]; else $fam_id4=0;
			$rs3=new DataObj;
			$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
			if($rs3->EOF())
			{
				$sql='
						INSERT INTO filters_products
						(
							cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,fam_id4,marca_id,filtros_rel2,FLAG,filtro_active
						)
						VALUES
						(
							"0","0","'.$id.'","'.$fam_id1.'","'.$fam_id2.'","'.$fam_id3.'","'.$fam_id4.'","'.$marca.'","",1,1
						)
					';
				$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			}
			else
			{
				$sql ='UPDATE filters_products SET fam_id1="'.$fam_id1.'",fam_id2="'.$fam_id2.'",fam_id3="'.$fam_id3.'",fam_id4="'.$fam_id4.'",marca_id="'.$marca.'" where prod_id="'.$id.'"';
				$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			}
			$rs3->db_close();
		}
		db_closedb_conection($db_link);
		$res =ChangeProdLinguas($id,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$cabecalho_texto5,$texto5,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1);
		// exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
  }
}
function ChangeProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$cabecalho_texto5,$texto5,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang) $m_lang=$admin_lang;
	if($id_org== $id) $id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"SELECT ID,SEO_img,ICON_img FROM products_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())
	{
		$altera=1;
		$id_video=$rs->db_data["ID"];
		$SEO_img=$rs->db_data["SEO_img"];
		$ICON_img=$rs->db_data["ICON_img"];
	}
	$rs->db_close();
	if(!$altera)return InsertProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$cabecalho_texto5,$texto5,$SEO_title,$SEO_description,$SEO_keywords,$SEO_alt,$SEO_h1);
	else
	{
		$altera_download=0; if($Download_peq["name"]){ $altera_download=1; $pos=strrpos($Download_peq["name"],"."); $tamanho=substr($Download_peq["name"],$pos+1,strlen($Download_peq["name"])+1-$pos); }
		if(!$tem_download && $Apagar_download)
		{
			$ficheiro=$id_video."_download.".$tamanho;
			if(file_exists($img_path_download.$ficheiro)) unlink($img_path_download.$ficheiro);
			if(!$altera_download) $tamanho='';
		}
		if($altera_download) copia_segura($Download_peq["temp"],$img_path_download . $id_video . "_download." . $tamanho);
		$cabecalho1=str2SQLstr($cabecalho);
		$URL1=str2SQLstr($URL);
		$subtitulo=addslashes($_POST['subtitulo']);
		if(isset($_POST['tempo_entrega'])) $tempo_entrega=$_POST['tempo_entrega']; else $tempo_entrega='';

		$SEO_img_file["name"] = $_FILES['SEO_img']['name'];
		$SEO_img_file["temp"] = $_FILES['SEO_img']['tmp_name'];
		$ICON_img_file["name"] = $_FILES['ICON_img']['name'];
		$ICON_img_file["temp"] = $_FILES['ICON_img']['tmp_name'];

		$altera_img=0;if($SEO_img_file["name"]){$altera_img=1;$pos=strrpos($SEO_img_file["name"],".");$img_ext=substr($SEO_img_file["name"],$pos+1,strlen($SEO_img_file["name"])+1-$pos);}
		$altera_img2=0;if($ICON_img_file["name"]){$altera_img2=1;$pos=strrpos($ICON_img_file["name"],".");$img_ext2=substr($ICON_img_file["name"],$pos+1,strlen($ICON_img_file["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_SEO_img && $Apagar_SEO_img)
		{
			if(file_exists('../../files/products/'.$id_pai."_seo_".$m_lang.".".$img_ext))unlink('../../files/products/'.$id_pai."_seo_".$m_lang.".".$img_ext);
			if(!$altera_img)$img_ext='';
		}
		if($altera_img)
		{
			copia_segura($SEO_img_file["temp"],'../../files/products/'.$id_pai."_seo_".$m_lang.".".$img_ext);
		}
		//IMAGEM ICON
		if($tem_ICON_img && $Apagar_ICON_img)
		{
			if(file_exists('../../files/products/'.$id_pai."_ICON_".$m_lang.".".$img_ext2))unlink('../../files/products/'.$id_pai."_ICON_".$m_lang.".".$img_ext2);
			if(!$altera_img2)$img_ext2='';
		}
		if($altera_img2)
		{
			copia_segura($ICON_img_file["temp"],'../../files/products/'.$id_pai."_ICON_".$m_lang.".".$img_ext2);
		}

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
					tempo_entrega='$tempo_entrega',
					SEO_title = '$SEO_title',
					SEO_description = '$SEO_description',
					SEO_keywords = '$SEO_keywords',
					SEO_alt = '$SEO_alt',
					SEO_h1 = '$SEO_h1',
					SEO_img='$img_ext',
					ICON_img='$img_ext2'
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
	$res=Runquery($db_link,$DB_name,"DELETE FROM products WHERE ID=$id");
	if($res)
	{
		$sql2='DELETE FROM products_lang WHERE ID_PAI='.$id;
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2='DELETE FROM products_families_obj WHERE ID_PROD="'.$id.'"';
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
			if(file_exists($img_path.$ficheiro_original))unlink($img_path.$ficheiro_original);
			if(file_exists($img_path.$ficheiro_cart))unlink($img_path.$ficheiro_cart);
			if(file_exists($img_path.$ficheiro_med))unlink($img_path.$ficheiro_med);
			if(file_exists($img_path.$ficheiro_new))unlink($img_path.$ficheiro_new);
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
		$rs->db_open($db_link,$DB_name,'SELECT obj_id FROM products_objs WHERE obj_type_id="'.$id.'"');
		while(!$rs->EOF())
		{
			$obj_id=intval($rs->db_data['obj_id']);
			$sql2='DELETE FROM products_objs_lang WHERE ID_PAI="'.$obj_id.'"';
			$res2=Runquery($db_link,$DB_name,$sql2);
			$rs->db_next();
		}
		$rs->db_close();
		$sql2='DELETE FROM products_objs WHERE obj_type_id="'.$obj_id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
		$sql2='DELETE FROM filters_products WHERE prod_id="'.$id.'"';
		$res2=Runquery($db_link,$DB_name,$sql2);
	}
	else echo db_error($db_link).$sql;
	//exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function InsertNewFam($id_org=0,$ordem,$cabecalho,$cabecalho2,$URL,$texto1,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$preco_revenoff)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$new_id=0;
	$altera_img =0; if($Img_peq1["name"]){ $altera_img =1; $pos=strrpos($Img_peq1["name"],"."); $img_ext= substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
	$altera_img2=0; if($Img_peq2["name"]){ $altera_img2=1; $pos=strrpos($Img_peq2["name"],"."); $img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
	$altera_img3=0; if($Img_peq3["name"]){ $altera_img3=1; $pos=strrpos($Img_peq3["name"],"."); $img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos);}
	$ordem1=str2SQLstr($ordem);
	$margemcli=str2SQLstr($margemcli);
	$margemrev=str2SQLstr($margemrev);
	$cor=str2SQLstr($cor);
	$visivel=str2SQLstr($visivel);
	$destaque=str2SQLstr($destaque);
	$Portes=$_POST['Portes'];
	if(!$id_org)$id_org=0;
	if(!isset($_POST['m_media']))$ID_Media=0;
	else $ID_Media=$_POST['m_media'];

	$Margem_formula=$_POST['Margem_formula'];

	$sql="insert into products_families (Portes,ID_ORG,Ordem,img_ext,img2_ext,img3_ext,MargemCli,MargemRev,Margem_formula,cor,visivel,Destaque,cat1,cat2,cat3,Download,ID_Media,preco_revenoff) values ('$Portes','$id_org',$ordem1,'$img_ext','$img2_ext','$img3_ext',$margemcli,$margemrev,'$Margem_formula',$cor,$visivel,$destaque,'$cat1','$cat2','$cat3','$download','$ID_Media','$preco_revenoff')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$ext1);
		if($altera_img2) copia_segura($Img_peq2["temp"],$img_path.$new_id."_2.".$ext2);
		if($altera_img3) copia_segura($Img_peq3["temp"],$img_path.$new_id."_3.".$ext3);
		InsertNewFamLinguas($new_id,$cabecalho,$cabecalho2,$URL,$texto);
	}
	else echo db_error($db_link).$sql;
	db_closedb_conection($db_link);
	//exec('php '.$GLOBALS['site_shell_url'].'core/crons/gera_sitemap.php > /dev/null &');
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
	$prod_txt1=addslashes($_POST['prod_txt1']);
	$sql="insert into products_families_lang(ID_PAI,Cabecalho,Cabecalho2,URL,Texto,prod_txt1,Lang) values ($id_pai,$cabecalho1,$cabecalho2,$URL1,'$texto1','$prod_txt1','$admin_lang')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link).$sql;
    return($res);
}
function products_objs($obj_id=0,$sync=0,$preco_custo_custom=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$site_http_url,$tem_promocoes,$tem_promocoes2,$Suppliers_nomes;
	$obj_id_p = $obj_desc = $obj_iva = $obj_preco = $obj_precosiva = $obj_stock = [];
	$obj_promo = $obj_promosiva = $obj_promo_ativo = $obj_outlet = $obj_destaque = [];
	$obj_travelsize = $obj_novo = $obj_ativo = $obj_qtdmin = $obj_dia = [];
	$obj_destaque_data = $obj_novo_data = $obj_outlet_data = $obj_promo_data = $obj_dia_data = [];
	$obj_Supplier_ID = $Promo_data_ini = $Promo_data_fim = $Promo_descricao = $outlet_box = [];
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$sql="SELECT * FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$obj_id."' AND obj_type_id > 0 and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs.obj_id ASC";
	$rs->db_open($db_link,$DB_name,$sql);

	//VAMOS COLOCAR NA LINHA DE PREÇO OS VALORES TODOS
	$prod_precos=get_produtc_price($_GET['ID']);
	if($prod_precos['PVP']>0)
	{
		$pvp=$prod_precos['PVP'];
		if($sync)
		{
			$pcusto=$preco_custo_custom;
			$pcusto2=$pcusto;
			$fornecedor_custom = strtoupper(PROJETO);
		}
		else
		{
			// P. custo ----------------------
			//$sql="SELECT obj_pvp,obj_Supplier_ID FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$_GET['ID']."' AND obj_type_id > 0 and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs.obj_id ASC";
			//$rs2->db_open($db_link,$DB_name,$sql);
			//if(!$rs2->EOF())
			//{
				$pcusto2=$rs->db_data["obj_pvp"];
				$pcusto=$pcusto2/(1+($GLOBALS['iva_default']/100));
				//$pcusto=$pcusto2/1.23;
				//$pcustoiva=$rs->db_data["obj_iva"];
				//$pcustosiva=($pcusto/(1+($pcustoiva/100)));
				$supplier_id=$rs->db_data['obj_Supplier_ID'];
				$fornecedor_custom = $Suppliers_nomes[$supplier_id] ?? '';
			//}
			//$rs2->db_close();
		}
		$preco_site_s_iva = $pvp/(1+($GLOBALS['iva_default']/100));
		if($pcusto)
		{
			$dif_preco = $preco_site_s_iva-$pcusto2;
			$prec_percent = $preco_site_s_iva*100;
			$margem = number_format(($preco_site_s_iva-$pcusto),2,',','.').' &euro;';
			$margem_perc = number_format((($preco_site_s_iva/$pcusto)-1)*100,2,',','.').' %';
			$preco_site_s_iva = number_format($preco_site_s_iva,2,',','.');
			$pcusto = number_format($pcusto,2,',','.');
		}
		// echo 'PVP Site: '.number_format($pvp,2,',','.').' &euro;';
		// echo '<br>PREÇO SITE S/IVA: '.number_format($preco_site_s_iva,2,',','.').' &euro;';
		// // echo '<br>PVP s/IVA: '.number_format($pvpsiva,2,',','.').' &euro;';
		// echo '<br>P. Custo: '.number_format($pcusto,2,',','.').' &euro;';
		// echo '<br>P. Custo S/IVA: '.number_format($pcusto2,2,',','.').' &euro;';
		// echo '<br>Fornecedor: '.$fornecedor_custom;
		// if($margem) echo '<br>Margem: '.number_format($margem,2,',','.').' &euro; ('.number_format($margem_perc,2).'%)';
		// else echo '<br>Margem: NÃO É POSSÍVEL CALCULAR';
	}
	//FIM
	//

	while(!$rs->EOF())
	{
		$t=$rs->db_data['obj_val2'];
		if($t==NULL)$t=0;
		$obj_id_p[$t]=$rs->db_data['obj_id'];
		$obj_desc[$t]=$rs->db_data['Cabecalho'];
		$obj_iva[$t]=number_format(((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']),4,'.','');
		$obj_ivax=((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']);
		$obj_preco[$t]=$rs->db_data['obj_pvp'];
		$obj_precosiva[$t]=number_format(($rs->db_data['obj_pvp'] / (1 + ($obj_ivax / 100))),4,'.','');
		$obj_stock[$t]=$rs->db_data['obj_stock'];
		$obj_promo[$t]=$rs->db_data['obj_promo'];
		$obj_promosiva[$t]=number_format(($rs->db_data['obj_promo'] / (1 + ($obj_ivax / 100))),4,'.','');
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
		$obj_Supplier_ID[$t]=$rs->db_data['obj_Supplier_ID'];
		$Promo_data_ini[$t]=$rs->db_data['Promo_data_ini'];
		$Promo_data_fim[$t]=$rs->db_data['Promo_data_fim'];
		$Promo_descricao[$t]=$rs->db_data['Promo_descricao'];

		$outlet_box[$t]=$rs->db_data['outlet_box'];

		$rs->db_next();
	}
	$rs->db_close();

	$return ='<table id="IDTabelaPrecos" border="0">';
	$return .= '<tr>
				<td align="center" width="140px" style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted">&nbsp;</td>
				<td align="center" width="40px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>IVA</b><span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small;vertical-align:super">1</span></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Margem €</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Margem %</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Preço site</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Preço Custo c/iva</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Preço Custo s/iva</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Stock</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Stock Final (stock - reserva)</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Preço Promo c/IVA</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Preço Promo s/IVA</b></td>
				<td align="center" width="70px"  style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px;border-right:#c0c0c0 1px dotted"><b>Qtd Min</b></td>
				<td align="center" width="100px" style="background-color:#f2f3f4;font-size:10px;text-align:center;padding:5px"><b>Fornecedor</b></td>
				<td>&nbsp;</td>
			</tr>';
	// $novo_prod=0;
	if(count($obj_desc)<=0)
	{
		// $sync=1;
		$obj_id_p[]=0;
		$obj_desc[]='';
		$obj_iva[]=number_format($GLOBALS['iva_default'],4,'.','');
		$obj_ivax=$GLOBALS['iva_default'];
		$obj_preco[]=0;
		$obj_precosiva[]=0;
		$obj_stock[]=0;
		$obj_promo[]=0;
		$obj_promosiva[]=0;
		$obj_promo_ativo[]=0;
		$obj_outlet[]=0;
		$obj_destaque[]=0;
		$obj_travelsize[]=0;
		$obj_novo[]=0;
		$obj_ativo[]=0;
		$obj_qtdmin[]=0;
		$obj_destaque_data[]='';
		$obj_novo_data[]='';
		$obj_outlet_data[]='';
		$obj_promo_data[]='';
		$obj_dia[]='';
		$obj_dia_data[]='';
		$obj_Supplier_ID[]='';
	}
	// SE NÃO EXISTIR SICRONIZAÇÃO CONFIGURADA NO SETTINGS,OS CAMPOS ESTÃO SEMPRE DISPONIEIS
	if(isset($GLOBALS['prod_conf']) && $GLOBALS['prod_conf']['tem_sincro'] == 0) $sync=1;

	$guarda_id=0;
	$count=0;
	$a=0;

	if($sync)
	{
		$obj_Supplier_ID[$a] = '13';
		$Suppliers_nomes[$obj_Supplier_ID[$a]] = $GLOBALS['site_title'];
		$stock_reserva = reserva_stock($_GET['ID']);
		$stock_final = $obj_stock[0] - $stock_reserva;

	}
	else
	{
		$stock_final = 'STOCK FORNECEDOR';
	}

	$return .= '<tr id="rem_'.$a.'" class="precos_line">';
	$return .= '<td style="width:80px;background-color:#CCC" align="center">NORMAL</td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_iva[]" id="obj_iva'.$a.'" style="WIDTH:70px" maxlength="8" value="'.$obj_iva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:70px;background-color:#CCC; color:green" align="center">'.$margem.'</td>';
	$return .= '<td style="width:70px;background-color:#CCC; color:green" align="center">'.$margem_perc.'</td>';
	$return .= '<td style="width:80px;background-color:#CCC; color:green" align="center">'.$pvp.' €</td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_precosiva[]" id="obj_precosiva'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_precosiva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:70px" maxlength="255" value="'.$obj_stock[$a].'"  /></td>';
	$return .= '<td style="width:80px;background-color:#CCC; color:green" align="center">'.$stock_final.'</td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input type="hidden" value="'.$a.'" name="obj_val2[]" id="obj_val2'.$a.'" style="WIDTH:80px" /><input type="hidden" value="'.$obj_id_p[$a].'" name="obj_id[]" id="obj_id'.$a.'" style="WIDTH:130px; " /><input class="form-control" type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_promosiva[]" id="obj_promosiva'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promosiva[$a].'"  onkeypress="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_qtdmin[]" id="obj_qtdmin'.$a.'" style="WIDTH:70px;" maxlength="255" value="'.number_format($obj_qtdmin[$a],0).'" onkeypress="handleKeyPress(this)" /></td>';
	$return .= '<td style="width:100px;background-color:#CCC" align="center"><input disabled class="form-control" type="text" name="obj_Supplier_ID[]" id="obj_Supplier_ID'.$a.'" style="WIDTH:80px;height:40px" maxlength="255" value="'.$fornecedor_custom.'" onkeypress="handleKeyPress(this)" /></td>';
	$return .= '<td>';

	//Verificar se existem historico de preços
	//$sql="SELECT ID FROM products_objs_history_day WHERE ID_ORG='".$obj_id_p[$t]."' ORDER BY ID DESC";
	$sql="SELECT ID FROM products_objs_history WHERE ID_ORG='".$obj_id_p[$t]."' ORDER BY ID DESC";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$return .= '<a href="javascript:;" onclick="load_prod(\'#prod_'.$obj_id.'\',\'precos_detalhe.php?ID='.$obj_id_p[$t].'&ID2='.$obj_id.'\')"> » mostrar histórico de preços </a>';
	}
	else $return .= '&nbsp; » Não tem histórico de preços';
	$rs2->db_close();
	$return .= '&nbsp;</td></tr>';

	$sql="SELECT ID,Descricao FROM grupos_clientes";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$a=$rs->db_data['ID'];
			$Descricao=$rs->db_data['Descricao'];
			$return .= '<tr id="rem_'.$a.'" class="precos_line">';
			$return .= '<td style="width:80px;background-color:#CCC" align="center">'.$Descricao.'</td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_iva[]" id="obj_iva'.$a.'" style="WIDTH:70px" maxlength="8" value="'.$obj_iva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="background-color:#CCC">&nbsp;</td>';
			$return .= '<td style="background-color:#CCC">&nbsp;</td>';
			$return .= '<td style="background-color:#CCC" >&nbsp;</td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_precosiva[]" id="obj_precosiva'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_precosiva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:70px" maxlength="255" value="'.$obj_stock[$a].'"  /></td>';
			$return .= '<td style="background-color:#CCC">&nbsp;</td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input type="hidden" value="'.$a.'" name="obj_val2[]" id="obj_val2'.$a.'" style="WIDTH:80px; " /><input type="hidden" value="'.$obj_id_p[$a].'" name="obj_id[]" id="obj_id'.$a.'" style="WIDTH:130px; " /><input class="form-control" type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_promosiva[]" id="obj_promosiva'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promosiva[$a].'"  onkeypress="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_qtdmin[]" id="obj_qtdmin'.$a.'" style="WIDTH:70px; " maxlength="255" value="'.number_format($obj_qtdmin[$a],0).'" onkeypress="handleKeyPress(this)" /></td>';
			$return .= '<td style="background-color:#CCC">&nbsp;</td>';
			$return .= '</tr>';
			$rs->db_next();
		}
	}
	$rs->db_close();
	$return .= '</table><br>';
	$return .= '<div class="col-lg-12 prod" id="prod_'.$obj_id.'" style="opacity: 0; display: none; transition: NaNpx 0s ease 0s"></div>';

	$a=0;
	$return .= '<table id="IDTabelaPrecos" border="0"><tr>';
	// $return .= '<td align="center" width="80px" height="25" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Ocasião</b></td>';
	$return .= '<td align="center" width="80px" height="25" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Destaque</b></td>';
	
	if($GLOBALS['key_oportunidades'])$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>'.$GLOBALS['key_oportunidades'].'</b></td>';
	else $return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b><input type="text" name="outlet_box" id="outlet_box'.$a.'" style="width:86px !important;font-size:10px;text-align:center;font-weight:bold;padding-left:0;height:17px" maxlength="255" value="'.($outlet_box[$a] == '' ? 'Outlet' : $outlet_box[$a]).'"></b></td>';
	
	$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Promoção<span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small;vertical-align:super">2</span></b></td>';
	$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Novo</b></td>';
	$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center"><b>Mais Vendido</b></td>';
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
	$return .= '</tr>';

	if($tem_promocoes2)
	{
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br><input class="box dropdate" type="text" name="obj_destaque_data[]" id="obj_destaque_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_destaque_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br> <input class="box dropdate" type="text" name="obj_outlet_data[]" id="obj_outlet_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_outlet_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br> <input class="box dropdate" type="text" name="obj_promo_data[]" id="obj_promo_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_promo_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>Acaba em<br><input class="box dropdate" type="text" name="obj_novo_data[]" id="obj_novo_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_novo_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>No dia<br><input class="box dropdate" type="text" name="obj_dia_data[]" id="obj_dia_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_dia_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>No dia<br><input class="box dropdate" type="text" name="obj_Supplier_ID[]" id="obj_Supplier_ID'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_Supplier_ID[$a].'" /></td>';

	}

	//Modulo Promoções
	if($tem_promocoes)
	{
		$return .= '</tr>';
		/**
		$return .= '<tr class="precos_line">';
		$return .= '<td colspan="11">&nbsp;';
		$return .= '</td>';
			$return .= '</tr>';

		$return .= '<tr class="precos_line">';
		$return .= '<td colspan="11">';
		$return .= '<table>';
			$return .= '<tr>';
				$return .= '<td>Associar promoção&nbsp;&nbsp;</td>';
				$return .= '<td><input type="hidden" name="promo_id[]" id="promo_id" value="'.$obj_promo_id[$a].'"></td>';

				$return .= '<td>&nbsp;
				<a href="promocoes.php?field=promo_id&field2=promo_desc" class="preto fancybox2">    &#128279;  Procurar
				</a>&nbsp;
				</td>';

				//
				$rsp=new DataObj;
	            $sqlp="Select * from sales_promotions
              	where promo_id=".$obj_promo_id[$a];
              	$rsp->db_open($db_link,$DB_name,$sqlp);
              	$promo_close = 0;
              	if(!$rsp->EOF())
    			{
    				$promo_id = $rsp->db_data["promo_id"];
          			$DESIGNACAO = $rsp->db_data["DESIGNACAO"];
          			$promo_close = 1;
    			}
    			$rsp->db_close();
				//
				$return .= '<td>';
				$return .= '&nbsp;<span id="promo_desc"><a href="../sales_promotions/pagina.php?promo_id='.$promo_id.'&Dump='.uniqid().'" target="_blank">'.($DESIGNACAO).'</a></span>';
				$return .= '</td>';

				$return .= '<td>';
				if($promo_close){
					$return .= '&nbsp;&nbsp;<span style="font-size:20px;cursor:pointer;" id="prom-close" onclick="$(\'#promo_id\').val(\'\');$(\'#promo_desc\').html(\'\');$(\'#prom-close\').html(\'\')">&#x2716;</span>';
				}

				$return .= '</td>';

			$return .= '</tr>';
			$return .= '</table>';
		$return .= '</td>';
		$return .= '</tr>';
		/**/
		$return .= '<tr class="precos_line">';
		$return .= '<td align="right" colspan="11"><br><br>

		<table border="0" cellspacing="2" cellpadding="2" style="background-color:#f2f3f4">
		<tr>
			<td align="center" style="font-weight:bold;padding:5px">Data Ini Promo</td>
			<td align="center" style="font-weight:bold;padding:5px">Data Fim Promo</td>
			<td align="center" style="font-weight:bold;padding:5px">Descrição da promoção</td>
		</tr>
		<tr>
		<td style="vertical-align:top;padding:5px"><input value="'.$Promo_data_ini[$a].'" type="date" data-date-format="YYYY-MM-DD" name="Promo_data_ini[]" id="Promo_data_ini'.$a.'" style="WIDTH:100px; HEIGHT:23px;text-align:left" /></td>
		<td style="vertical-align:top;padding:5px"><input value="'.$Promo_data_fim[$a].'" type="date" data-date-format="YYYY-MM-DD" name="Promo_data_fim[]" id="Promo_data_fim'.$a.'" style="WIDTH:100px; HEIGHT:23px;text-align:left" /></td>
		<td style="vertical-align:top;padding:5px"><textarea name="Promo_descricao[]" id="Promo_descricao'.$a.'" style="WIDTH:450px; HEIGHT:80px;text-align:left">'.htmlentities_memo(stripslashes($Promo_descricao[$a])).'</textarea></td>
		</tr>
		</table>


		</td>';
		//<input class="form-control" type="date" name="f_data1" id="f_data1" value="" style="width:100px">

		$return .= '</tr>';
	}

	$return .= '</table>';
	$return .= '<br>
			<div style="line-height:8px;background-color:#deefc9;padding:10px;border:1px solid #c2e5c2">
			&nbsp;&nbsp;<span style="color:#348fe2;font-weight:bold;font-size:x-small; vertical-align:super;">(1) - O campo IVA serve somente para efeitos de calculo no carrinho</span></br>
			&nbsp;&nbsp;<span style="color:#348fe2;font-weight:bold;font-size:x-small; vertical-align:super;">(2) - É obrigatório que os produtos em promoção contenham a data de inicio e fim e a descrição da campanha</span></br>
			</div>
			</br></br>';
	return $return;
}
function products_objs_standard($obj_id=0,$sync=0,$preco_custo_custom=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$site_http_url,$tem_promocoes,$tem_promocoes2,$Suppliers_nomes;
	$obj_id_p = $obj_desc = $obj_iva = $obj_preco = $obj_precosiva = $obj_stock = [];
	$obj_promo = $obj_promosiva = $obj_promo_ativo = $obj_outlet = $obj_destaque = [];
	$obj_travelsize = $obj_novo = $obj_ativo = $obj_qtdmin = $obj_dia = [];
	$obj_destaque_data = $obj_novo_data = $obj_outlet_data = $obj_promo_data = $obj_dia_data = [];
	$obj_Supplier_ID = $Promo_data_ini = $Promo_data_fim = $Promo_descricao = $outlet_box = [];
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$sql="SELECT * FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$obj_id."' AND obj_type_id > 0 and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs.obj_id ASC";
	$rs->db_open($db_link,$DB_name,$sql);


	//VAMOS COLOCAR NA LINHA DE PREÇO OS VALORES TODOS
	$prod_precos=get_produtc_price($_GET['ID']);
	// echo '+++'.$prod_precos['PVP'];
	
	// if($prods['prod_precos']['PVP'] > 0)
	// echo 'ola'.$preco_custo_custom;
	if($prod_precos['PVP']>0)
	{
		// $pvp=$prods['prod_precos']['PVP'];
		$pvp=$prod_precos['PVP'];
		// $txiva=$prods['prod_precos']['IVA'];
		// $pvpsiva=($pvp/(1+($txiva/100)));

		if($sync)
		{
			$pcusto=$preco_custo_custom;
			$pcusto2=$pcusto;
			$fornecedor_custom = strtoupper(PROJETO);
		}
		else
		{
			// P. custo ----------------------
			/*
			$sql="SELECT * FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$_GET['ID']."' AND obj_type_id > 0 and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs.obj_id ASC";
			$rs2->db_open($db_link,$DB_name,$sql);*/
			/*if(!$rs2->EOF())
			{*/
				$pcusto2=$rs->db_data["obj_pvp"];
				$pcusto=$pcusto2/1.23;
				// $pcustoiva=$rs->db_data["obj_iva"];
				// $pcustosiva=($pcusto/(1+($pcustoiva/100)));
				// var_dump($rs->db_data);
				$supplier_id=$rs->db_data['obj_Supplier_ID'];
				$fornecedor_custom = $Suppliers_nomes[$supplier_id] ?? '';
			/*}
			$rs2->db_close();*/
		}
		// echo $fornecedor_custom;
		// $pcusto=$pcusto-83;
		$preco_site_s_iva = $pvp/1.23;
		if($pcusto)
		{
			$dif_preco = $preco_site_s_iva-$pcusto2;
			$prec_percent = $preco_site_s_iva*100;
			$margem = number_format(($preco_site_s_iva-$pcusto),2,',','.').' &euro;';
			$margem_perc = number_format((($preco_site_s_iva/$pcusto)-1)*100,2,',','.').' %';
			$preco_site_s_iva = number_format($preco_site_s_iva,2,',','.');
			$pcusto = number_format($pcusto,2,',','.');
		}
		// echo 'PVP Site: '.number_format($pvp,2,',','.').' &euro;';
		// echo '<br>PREÇO SITE S/IVA: '.number_format($preco_site_s_iva,2,',','.').' &euro;';
		// // echo '<br>PVP s/IVA: '.number_format($pvpsiva,2,',','.').' &euro;';
		// echo '<br>P. Custo: '.number_format($pcusto,2,',','.').' &euro;';
		// echo '<br>P. Custo S/IVA: '.number_format($pcusto2,2,',','.').' &euro;';
		// echo '<br>Fornecedor: '.$fornecedor_custom;
		// if($margem) echo '<br>Margem: '.number_format($margem,2,',','.').' &euro; ('.number_format($margem_perc,2).'%)';
		// else echo '<br>Margem: NÃO É POSSÍVEL CALCULAR';
	}
	//FIM
	//
	//
	while(!$rs->EOF())
	{
		$t=$rs->db_data['obj_val2'];
		$obj_id_p[$t]=$rs->db_data['obj_id'];
		$obj_desc[$t]=$rs->db_data['Cabecalho'];
		$obj_iva[$t]=number_format(((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']),4,'.','');
		$obj_ivax=((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']);
		$obj_preco[$t]=$rs->db_data['obj_pvp'];
		$obj_precosiva[$t]=number_format(($rs->db_data['obj_pvp'] / (1 + ($obj_ivax / 100))),4,'.','');
		$obj_stock[$t]=$rs->db_data['obj_stock'];
		$obj_promo[$t]=$rs->db_data['obj_promo'];
		$obj_promosiva[$t]=number_format(($rs->db_data['obj_promo'] / (1 + ($obj_ivax / 100))),4,'.','');
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
		$obj_Supplier_ID[$t]=$rs->db_data['obj_Supplier_ID'];

		$outlet_box[$t]=$rs->db_data['outlet_box'];

		$Promo_data_ini[$t]=$rs->db_data['Promo_data_ini'];
		$Promo_data_fim[$t]=$rs->db_data['Promo_data_fim'];
		$Promo_descricao[$t]=$rs->db_data['Promo_descricao'];
		$rs->db_next();
	}
	$rs->db_close();

	$return ='<table id="IDTabelaPrecos" border="0">';
	$return .= '<tr>
				<td align="center" width="140px" style="font-size:10px;text-align:center">&nbsp;</td>
				<td align="center" width="40px" style="font-size:10px;text-align:center"><b>IVA</b><span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small;vertical-align:super">1</span></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Margem €</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Margem %</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Preço site</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Preço Custo c/iva</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Preço Custo s/iva</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Stock</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Preço Promo c/IVA</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Preço Promo s/IVA</b></td>
				<td align="center" width="70px" style="font-size:10px;text-align:center"><b>Qtd Min</b></td>
				<td>&nbsp;</td>
			</tr>';
	// $novo_prod=0;
	if(count($obj_desc)<=0)
	{
		// $sync=1;
		$obj_id_p[]=0;
		$obj_desc[]='';
		$obj_iva[]=number_format($GLOBALS['iva_default'],4,'.','');
		$obj_ivax=$GLOBALS['iva_default'];
		$obj_preco[]=0;
		$obj_precosiva[]=0;
		$obj_stock[]=0;
		$obj_promo[]=0;
		$obj_promosiva[]=0;
		$obj_promo_ativo[]=0;
		$obj_outlet[]=0;
		$obj_destaque[]=0;
		$obj_travelsize[]=0;
		$obj_novo[]=0;
		$obj_ativo[]=0;
		$obj_qtdmin[]=0;
		$obj_destaque_data[]='';
		$obj_novo_data[]='';
		$obj_outlet_data[]='';
		$obj_promo_data[]='';
		$outlet_box[] = '';
		$obj_dia[]='';
		$obj_dia_data[]='';
		$obj_Supplier_ID[]='';
	}
	// SE NÃO EXISTIR SICRONIZAÇÃO CONFIGURADA NO SETTINGS,OS CAMPOS ESTÃO SEMPRE DISPONIEIS
	if(isset($GLOBALS['prod_conf']) && $GLOBALS['prod_conf']['tem_sincro'] == 0) $sync=1;

	$guarda_id=0;
	$count=0;
	$a=0;

	if($sync)
	{
		$obj_Supplier_ID[$a] = '13';
		$Suppliers_nomes[$obj_Supplier_ID[$a]] = $GLOBALS['site_title'];
		$stock_reserva = reserva_stock($_GET['ID']);
		$stock_final = $obj_stock[0] - $stock_reserva;

	}
	else
	{
		$stock_final = 'STOCK FORNECEDOR';
	}

	$return .= '<tr id="rem_'.$a.'" class="precos_line">';
	$return .= '<td style="width:80px;background-color:#CCC" align="center">NORMAL</td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_iva[]" id="obj_iva'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_iva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC;color:green" align="center">'.$margem.'</td>';
	$return .= '<td style="width:80px;background-color:#CCC;color:green" align="center">'.$margem_perc.'</td>';
	$return .= '<td style="width:80px;background-color:#CCC;color:green" align="center">'.$pvp.' €</td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_precosiva[]" id="obj_precosiva'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_precosiva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:80px" maxlength="255" value="'.$obj_stock[$a].'"  /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input type="hidden" value="'.$a.'" name="obj_val2[]" id="obj_val2'.$a.'" style="WIDTH:80px" /><input type="hidden" value="'.$obj_id_p[$a].'" name="obj_id[]" id="obj_id'.$a.'" style="WIDTH:130px; " /><input class="form-control" type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_promosiva[]" id="obj_promosiva'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promosiva[$a].'"  onkeypress="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')" /></td>';
	$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_qtdmin[]" id="obj_qtdmin'.$a.'" style="WIDTH:70px;" maxlength="255" value="'.number_format($obj_qtdmin[$a],0).'" onkeypress="handleKeyPress(this)" /></td>';
	$return .= '</tr>';

	$sql="SELECT ID,Descricao FROM grupos_clientes";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$a=$rs->db_data['ID'];
			$Descricao=$rs->db_data['Descricao'];
			$return .= '<tr id="rem_'.$a.'" class="precos_line">';
			$return .= '<td style="width:80px;background-color:#CCC" align="center">'.$Descricao.'</td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_iva[]" id="obj_iva'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_iva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'0\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'1\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_precosiva[]" id="obj_precosiva'.$a.'"  style="WIDTH:80px" maxlength="255" value="'.$obj_precosiva[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" onkeyup="handleKeyPress(\''.$a.'\',\'2\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:80px" maxlength="255" value="'.$obj_stock[$a].'"  /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input type="hidden" value="'.$a.'" name="obj_val2[]" id="obj_val2'.$a.'" style="WIDTH:80px; " /><input type="hidden" value="'.$obj_id_p[$a].'" name="obj_id[]" id="obj_id'.$a.'" style="WIDTH:130px; " /><input class="form-control" type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'3\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:80px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_promosiva[]" id="obj_promosiva'.$a.'" style="WIDTH:80px; " maxlength="255" value="'.$obj_promosiva[$a].'"  onkeypress="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')"  onkeyup="handleKeyPress(\''.$a.'\',\'4\',\''.$obj_iva[$a].'\')" /></td>';
			$return .= '<td style="width:70px;background-color:#CCC" align="center"><input class="form-control" type="text" name="obj_qtdmin[]" id="obj_qtdmin'.$a.'" style="WIDTH:70px" maxlength="255" value="'.number_format($obj_qtdmin[$a],0).'" onkeypress="handleKeyPress(this)" /></td>';
			$return .= '<td>&nbsp;</td></tr>';
			$rs->db_next();
		}
	}
	$rs->db_close();
	$return .= '</table><br>';
	$return .= '<div class="col-lg-12 prod" id="prod_'.$obj_id.'" style="opacity: 0; display: none; transition: NaNpx 0s ease 0s"></div>';

	$a=0;
	$return .= '<table id="IDTabelaPrecos" border="0"><tr>';
	// $return .= '<td align="center" width="80px" height="25" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Ocasião</b></td>';
	$return .= '<td align="center" width="80px" height="25" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Destaque</b></td>';
	if($GLOBALS['key_oportunidades'])$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>'.$GLOBALS['key_oportunidades'].'</b></td>';
	else $return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Outlet</b></td>';
	$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Promoção<span style="color:#FF0000;font-weight:bold;font-size:15px;font-size:xx-small;vertical-align:super">2</span></b></td>';
	$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center;border-right:#c0c0c0 1px dotted"><b>Novo</b></td>';
	$return .= '<td align="center" width="80px" style="font-size:10px;text-align:center"><b>Mais Vendido</b></td>';
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
	$return .= '</tr>';

	if($tem_promocoes2)
	{
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br><input class="box dropdate" type="text" name="obj_destaque_data[]" id="obj_destaque_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_destaque_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br> <input class="box dropdate" type="text" name="obj_outlet_data[]" id="obj_outlet_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_outlet_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC;border-right:#FFF 1px dotted" align="center"><br>Acaba em<br> <input class="box dropdate" type="text" name="obj_promo_data[]" id="obj_promo_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_promo_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>Acaba em<br><input class="box dropdate" type="text" name="obj_novo_data[]" id="obj_novo_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_novo_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>No dia<br><input class="box dropdate" type="text" name="obj_dia_data[]" id="obj_dia_data'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_dia_data[$a].'" /></td>';
		$return .= '<td style="background-color:#CCC" align="center"><br>No dia<br><input class="box dropdate" type="text" name="obj_Supplier_ID[]" id="obj_Supplier_ID'.$a.'" style="width:64px !important;font-size:10px" maxlength="255" value="'.$obj_Supplier_ID[$a].'" /></td>';

	}

	//Modulo Promoções

	if($tem_promocoes)
	{
		$return .= '</tr>';
		/**
		$return .= '<tr class="precos_line">';
		$return .= '<td colspan="11">&nbsp;';
		$return .= '</td>';
			$return .= '</tr>';

		$return .= '<tr class="precos_line">';
		$return .= '<td colspan="11">';
		$return .= '<table>';
			$return .= '<tr>';
				$return .= '<td>Associar promoção&nbsp;&nbsp;</td>';
				$return .= '<td><input type="hidden" name="promo_id[]" id="promo_id" value="'.$obj_promo_id[$a].'"></td>';

				$return .= '<td>&nbsp;
				<a href="promocoes.php?field=promo_id&field2=promo_desc" class="preto fancybox2">    &#128279;  Procurar
				</a>&nbsp;
				</td>';

				//
				$rsp=new DataObj;
	            $sqlp="Select * from sales_promotions
              	where promo_id=".$obj_promo_id[$a];
              	$rsp->db_open($db_link,$DB_name,$sqlp);
              	$promo_close = 0;
              	if(!$rsp->EOF())
    			{
    				$promo_id = $rsp->db_data["promo_id"];
          			$DESIGNACAO = $rsp->db_data["DESIGNACAO"];
          			$promo_close = 1;
    			}
    			$rsp->db_close();
				//
				$return .= '<td>';
				$return .= '&nbsp;<span id="promo_desc"><a href="../sales_promotions/pagina.php?promo_id='.$promo_id.'&Dump='.uniqid().'" target="_blank">'.($DESIGNACAO).'</a></span>';
				$return .= '</td>';

				$return .= '<td>';
				if($promo_close){
					$return .= '&nbsp;&nbsp;<span style="font-size:20px;cursor:pointer;" id="prom-close" onclick="$(\'#promo_id\').val(\'\');$(\'#promo_desc\').html(\'\');$(\'#prom-close\').html(\'\')">&#x2716;</span>';
				}

				$return .= '</td>';

			$return .= '</tr>';
			$return .= '</table>';
		$return .= '</td>';
		$return .= '</tr>';
		/**/
		$return .= '<tr class="precos_line">';
		$return .= '<td align="right" colspan="11"><br><br>

		<table border="0" cellspacing="2" cellpadding="2" style="background-color:#f2f3f4">
		<tr>
			<td align="center" style="font-weight:bold">Data Ini Promo</td>
			<td align="center" style="font-weight:bold">Data Fim Promo</td>
			<td align="center" style="font-weight:bold">Descrição da promoção</td>
		</tr>
		<tr>
		<td style="vertical-align:top"><input class="form-control" type="date" value="'.$Promo_data_ini[$a].'" data-date-format="YYYY-MM-DD"  name="Promo_data_ini[]" id="Promo_data_ini'.$a.'" style="WIDTH:100px; HEIGHT:23px;text-align:left" /></td>
		<td style="vertical-align:top"><input class="form-control"  value="'.$Promo_data_fim[$a].'" type="date" data-date-format="YYYY-MM-DD" name="Promo_data_fim[]" id="Promo_data_fim'.$a.'" style="WIDTH:100px; HEIGHT:23px;text-align:left" /></td>
		<td style="vertical-align:top"><textarea name="Promo_descricao[]" id="Promo_descricao'.$a.'" style="WIDTH:450px; HEIGHT:80px;text-align:left">'.htmlentities_memo(stripslashes($Promo_descricao[$a])).'</textarea></td>
		</tr>
		</table>



		</td>';

		$return .= '</tr>';
	}

	$return .= '</table>';
	$return .= '
			<br />
			<div style="line-height:8px;background-color:#deefc9;padding:10px;border:1px solid #c2e5c2">
			&nbsp;&nbsp;<span style="color:#348fe2;font-weight:bold;font-size:x-small; vertical-align:super;">(1) - O campo IVA serve somente para efeitos de calculo no carrinho</span></br>
			&nbsp;&nbsp;<span style="color:#348fe2;font-weight:bold;font-size:x-small; vertical-align:super;">(2) - É obrigatório que os produtos em promoção contenham a data de inicio e fim e a descrição da campanha</span></br>
			</div>
			</br></br>';
	return $return;
}
function products_objs_filtros($obj_id=0,$obj_combo='',$a=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$field_array=array();
	$fields=db_list_fields($DB_name,'products_objs_combo',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0; $i < $columns; $i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('obj_ref',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs_combo ADD obj_ref VARCHAR(255)'); }

	$rs=new DataObj;
	$sql='SELECT obj_id,obj_iva,obj_pvp,obj_stock,obj_promo,obj_ref,obj_qtdmin FROM products_objs_combo WHERE obj_type="products" AND obj_type_id="'.$obj_id.'" AND obj_combo="'.$obj_combo.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$obj_id_p=$rs->db_data['obj_id'];
		$obj_ref=$rs->db_data['obj_ref'];
		$obj_iva=number_format(((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']),4,'.','');
		$obj_ivax=((isset($rs->db_data['obj_iva']) && $rs->db_data['obj_iva'] > 0) ? $rs->db_data['obj_iva'] : $GLOBALS['iva_default']);
		$obj_preco=$rs->db_data['obj_pvp'];
		$obj_precosiva=number_format(($rs->db_data['obj_pvp'] / (1 + ($obj_ivax / 100))),4,'.','');
		$obj_stock=$rs->db_data['obj_stock'];
		$obj_qtdmin=$rs->db_data['obj_qtdmin'];
		$obj_promo=$rs->db_data['obj_promo'];
		$obj_promosiva=number_format(($rs->db_data['obj_promo'] / (1 + ($obj_ivax / 100))),4,'.','');
	}
	else
	{
		$obj_iva=number_format(($GLOBALS['iva_default']),4,'.','');
	}
	$rs->db_close();
	/*$return ='<table id="IDTabelaPrecos" style="margin:0px;" border="0">';
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
	$return .= '<tr>';*/

	$off='';if($obj_preco<=0)$off=' input_off ';
	$return .= '<td align="center"><input class="form-control" type="text" name="c_obj_ref[]" id="c_obj_ref'.$a.'"  style="WIDTH:100px" maxlength="255" value="'.$obj_ref.'" /></td>';
	$return .= '<td align="center"><input type="hidden" value="'.$obj_combo.'" name="c_obj_combo[]" id="c_obj_combo'.$a.'" style="WIDTH:130px;" /><input type="hidden" value="'.$obj_id_p.'" name="c_obj_id[]" id="c_obj_id'.$a.'" style="WIDTH:130px;" /><input class="form-control" type="text" name="c_obj_iva[]" id="c_obj_iva'.$a.'"  style="WIDTH:50px" maxlength="255" value="'.$obj_iva.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'0\',\''.$obj_iva.'\')" onkeyup="handleKeyPress_combo(\''.$a.'\',\'0\',\''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="form-control '.$off.'" type="text" name="c_obj_preco[]" id="c_obj_preco'.$a.'"  style="WIDTH:90px" maxlength="255" value="'.$obj_preco.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'1\',\''.$obj_iva.'\')"  onkeyup="handleKeyPress_combo(\''.$a.'\',\'1\',\''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="form-control" type="text" name="c_obj_precosiva[]" id="c_obj_precosiva'.$a.'"  style="WIDTH:90px" maxlength="255" value="'.$obj_precosiva.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'2\',\''.$obj_iva.'\')" onkeyup="handleKeyPress_combo(\''.$a.'\',\'2\',\''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="form-control" type="text" name="c_obj_stock[]" id="c_obj_stock'.$a.'" style="WIDTH:70px" maxlength="255" value="'.$obj_stock.'" /></td>';
	$return .= '<td align="center"><input class="form-control" type="text" name="c_obj_qtdmin[]" id="c_obj_qtdmin'.$a.'" style="WIDTH:70px" maxlength="255" value="'.$obj_qtdmin.'" /></td>';
	$return .= '<td align="center"><input class="form-control" type="text" name="c_obj_promo[]" id="c_obj_promo'.$a.'" style="WIDTH:90px; " maxlength="255" value="'.$obj_promo.'" onkeypress="handleKeyPress_combo(\''.$a.'\',\'3\',\''.$obj_iva.'\')"  onkeyup="handleKeyPress_combo(\''.$a.'\',\'3\',\''.$obj_iva.'\')" /></td>';
	$return .= '<td align="center"><input class="form-control" type="text" name="c_obj_promosiva[]" id="c_obj_promosiva'.$a.'" style="WIDTH:90px; " maxlength="255" value="'.$obj_promosiva.'"  onkeypress="handleKeyPress_combo(\''.$a.'\',\'4\',\''.$obj_iva.'\')"  onkeyup="handleKeyPress_combo(\''.$a.'\',\'4\',\''.$obj_iva.'\')" /></td>';
	$return .= '<td><img onclick="get_precos(\''.$a.'\');" style="cursor:pointer" title="copiar valores da linha principal" src="avancada/refresh.png" /></td></tr>';
	//$return .= '</table>';
	return $return;
}

function products_acabamentos_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$acabamento=$_POST['acabamento'];
	$objeto=$_POST['objeto'];
	$ac_ativo=$_POST['ac_ativo'];
	$ac_ordem=$_POST['ac_ordem'];

	$rs=new DataObj;
	$sql="UPDATE products_acabamentos_save SET flag=0 WHERE prod_id='".$ID_PROD."'";
	$res=Runquery($db_link,$DB_name,$sql);
	for($a=0; $a<count($acabamento); $a++)
	{
		if($acabamento[$a] > 0)
		{
			$sql="SELECT save_id FROM products_acabamentos_save WHERE prod_id='".$ID_PROD."' AND acabamento_id='".$acabamento[$a]."' AND objeto_id='".$objeto[$a]."'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$save_id=$rs->db_data['save_id'];
				$sql="
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
				$res=Runquery($db_link,$DB_name,$sql);
			}
			else
			{
				$sql="
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
				$res=Runquery($db_link,$DB_name,$sql);
				$obj_id=db_get_last_insert_id($db_link);
			}
			$rs->db_close();
		}
	}
	$sql="DELETE FROM products_acabamentos_save WHERE prod_id='".$ID_PROD."' AND flag=0";
	$res=Runquery($db_link,$DB_name,$sql);
}
function products_objs_filtros_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$objv_id=$_POST['c_obj_id'] ?? array();
	$obj_iva=$_POST['c_obj_iva'] ?? array();
	$obj_preco=$_POST['c_obj_preco'] ?? array();
	$obj_stock=$_POST['c_obj_stock'] ?? array();
	$obj_qtdmin=$_POST['c_obj_qtdmin'] ?? array();
	$obj_promo=$_POST['c_obj_promo'] ?? array();
	$obj_p_ativo=$_POST['c_obj_p_ativo'] ?? array();
	$obj_combo=$_POST['c_obj_combo'] ?? array();
	$obj_ref=$_POST['c_obj_ref'] ?? array();
	$rs=new DataObj;

	$sql="UPDATE products_objs_combo SET obj_val=0 WHERE obj_type='products'AND obj_type_id='".$ID_PROD."'";
	$res=Runquery($db_link,$DB_name,$sql);
	for($a=0; $a<count($objv_id); $a++)
	{
		$sql="SELECT obj_id FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' AND obj_id='".$objv_id[$a]."' AND obj_combo='".$obj_combo[$a]."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id=$rs->db_data['obj_id'];
			$sql="UPDATE products_objs_combo
					SET
						obj_iva='".$obj_iva[$a]."',
						obj_pvp='".$obj_preco[$a]."',
						obj_stock='".$obj_stock[$a]."',
						obj_qtdmin='".$obj_qtdmin[$a]."',
						obj_promo='".$obj_promo[$a]."',
						obj_promo_ativo='".$obj_p_ativo[$a]."',
						obj_combo='".$obj_combo[$a]."',
						obj_combo2=';".$obj_combo[$a].";',
						obj_ref='".$obj_ref[$a]."',
						obj_val=1
					WHERE
						obj_id='".$obj_id."'
						AND obj_type='products'
						AND obj_type_id='".$ID_PROD."'";
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql="INSERT INTO
						products_objs_combo
						(
							obj_type,
							obj_type_id,
							obj_iva,
							obj_pvp,
							obj_stock,
							obj_qtdmin,
							obj_promo,
							obj_combo,
							obj_combo2,
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
							'".$obj_qtdmin[$a]."',
							'".$obj_promo[$a]."',
							'".$obj_combo[$a]."',
							';".$obj_combo[$a].";',
							'".$obj_ref[$a]."',
							1
						)
					";
			$res=Runquery($db_link,$DB_name,$sql);
			$obj_id=db_get_last_insert_id($db_link);
		}
		$rs->db_close();
	}
	$sql="DELETE FROM products_objs_combo WHERE obj_type='products'AND obj_type_id='".$ID_PROD."'AND obj_val=0 ";
	$res=Runquery($db_link,$DB_name,$sql);
}
function products_objs_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$objv_id=$_POST['obj_id'];
	$obj_val2=$_POST['obj_val2'];
	$obj_iva=$_POST['obj_iva'];
	$obj_preco=$_POST['obj_preco'];
	$obj_stock=$_POST['obj_stock'];
	$obj_qtdmin=$_POST['obj_qtdmin'];
	$obj_promo=$_POST['obj_promo'];
	$obj_p_ativo=$_POST['obj_p_ativo'];
	$obj_ativo=$_POST['obj_ativo'];
	$obj_desc=$_POST['ref'];
	$obj_destaque=$_POST['obj_destaque'];
	$obj_outlet=$_POST['obj_outlet'];
	$obj_travelsize=$_POST['obj_travelsize'];
	$obj_novo=$_POST['obj_novo'];
	$obj_destaque_data=$_POST['obj_destaque_data'];
	$obj_novo_data=$_POST['obj_novo_data'];
	$obj_outlet_data=$_POST['obj_outlet_data'];
	$obj_promo_data=$_POST['obj_promo_data'];
	$outlet_box=$_POST['outlet_box'];
	$obj_dia=$_POST['obj_dia'];
	$obj_dia_data=$_POST['obj_dia_data'];
	$promo_id=$_POST['promo_id'];
	$Promo_data_ini = $_POST['Promo_data_ini'];
	$Promo_data_fim = $_POST['Promo_data_fim'];
	$Promo_descricao = $_POST['Promo_descricao'];
	$rs=new DataObj;
	$rs2=new DataObj;
	$destaque=0;
	$promocao=0;

	if(count($obj_val2) > 0)
	{
		for($a=0; $a<count($obj_val2); $a++)
		{
			$sql="SELECT obj_id FROM products_objs WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' and obj_val2='".$obj_val2[$a]."'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$obj_id=$rs->db_data['obj_id'];
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
						outlet_box='".$outlet_box."',
						obj_dia='".$obj_dia[$a]."',
						obj_dia_data='".$obj_dia_data[$a]."',
						Promo_descricao='".$Promo_descricao[$a]."',
						Promo_data_ini='".$Promo_data_ini[$a]."',
						Promo_data_fim='".$Promo_data_fim[$a]."'
					WHERE
						obj_id='".$obj_id."' AND obj_type='products' AND obj_type_id='".$ID_PROD."'";
				$res=Runquery($db_link,$DB_name,$sql);
				
				// echo '<pre>';
				 //echo $a.' _ '.$sql.'<br>';
				// echo '</pre>';
				// die();
				//Mas como podemos ter mais do que 1 preço,temos de alterar os campos comuns a todas as linhas de preço:
				/**/
					$sql="UPDATE products_objs
					SET
						obj_promo='".$obj_promo[0]."',
						obj_promo_ativo='".$obj_p_ativo[0]."',
						obj_ativo='".$obj_ativo[0]."',
						obj_destaque='".$obj_destaque[0]."',
						obj_outlet='".$obj_outlet[0]."',
						obj_travelsize='".$obj_travelsize[0]."',
						obj_novo='".$obj_novo[0]."',
						obj_destaque_data='".$obj_destaque_data[0]."',
						obj_novo_data='".$obj_novo_data[0]."',
						obj_outlet_data='".$obj_outlet_data[0]."',
						obj_promo_data='".$obj_promo_data[0]."',
						outlet_box='".$outlet_box."',
						obj_dia='".$obj_dia[0]."',
						obj_dia_data='".$obj_dia_data[0]."',
						Promo_descricao='".$Promo_descricao[0]."',
						Promo_data_ini='".$Promo_data_ini[0]."',
						Promo_data_fim='".$Promo_data_fim[0]."'
					WHERE
						obj_type='products' AND obj_type_id='".$ID_PROD."'";
				$res=Runquery($db_link,$DB_name,$sql);

				// echo '<pre>';
				// echo $a.' _ '.$sql.'<br>';
				// echo '</pre>';
				// die();
				$sql="
					INSERT INTO
						products_objs_logs
						(
							admin_id,
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
							obj_val2,
							outlet_box,
							Promo_descricao,
							Promo_data_ini,
							Promo_data_fim,
							i_date
						)
						VALUES
						(
							'".$admin_id."',
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
							'".$obj_val2[$a]."',
							'".$outlet_box."',
							'".$Promo_descricao[$a]."',
							'".$Promo_data_ini[$a]."',
							'".$Promo_data_fim[$a]."',
							'".date('Y-m-d H:i:s')."'
						)
					";
					$res=Runquery($db_link,$DB_name,$sql);

					// echo '<pre>';
					// echo $a.' _ '.$sql.'<br>';
					// echo '</pre>';
					// die();
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
							obj_val2,
							outlet_box,
							Promo_descricao,
							Promo_data_ini,
							Promo_data_fim
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
							'".$obj_val2[$a]."',
							'".$outlet_box."',
							'".$Promo_descricao[$a]."',
							'".$Promo_data_ini[$a]."',
							'".$Promo_data_fim[$a]."'
						)
					";
				$res=Runquery($db_link,$DB_name,$sql);
				$obj_id=db_get_last_insert_id($db_link);
				$sql="
					INSERT INTO
						products_objs_logs
						(
							admin_id,
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
							obj_val2,
							outlet_box,
							Promo_descricao,
							Promo_data_ini,
							Promo_data_fim,
							i_date
						)
						VALUES
						(
							'".$admin_id."',
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
							'".$obj_val2[$a]."',
							'".$outlet_box."',
							'".$Promo_descricao[$a]."',
							'".$Promo_data_ini[$a]."',
							'".$Promo_data_fim[$a]."',
							'".date('Y-m-d H:i:s')."'
						)
					";
					$res=Runquery($db_link,$DB_name,$sql);

			}
			$rs->db_close();
			if($obj_id > 0)
			{
				$sql="SELECT ID FROM products_objs_lang WHERE ID_PAI='".$obj_id ."' AND Lang='".$GLOBALS['admin_lang']."'";
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
				$rs->db_close();
			}
		}
	}
	else
	{
		$sql="SELECT obj_id FROM products_objs WHERE obj_type='products' AND obj_type_id='".$ID_PROD."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id=$rs->db_data['obj_id'];
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
						obj_val2,
						outlet_box,
						Promo_descricao,
						Promo_data_ini,
						Promo_data_fim
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
						'0',
						'".$outlet_box."',
						'".$Promo_descricao[$a]."',
						'".$Promo_data_ini[$a]."',
						'".$Promo_data_fim[$a]."'
					)
				";
			$res=Runquery($db_link,$DB_name,$sql);
			$obj_id=db_get_last_insert_id($db_link);



			$sql="
					INSERT INTO
						products_objs_logs
						(
							admin_id,
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
							obj_val2,
							Promo_descricao,
							Promo_data_ini,
							Promo_data_fim,
							i_date
						)
						VALUES
						(
							'".$admin_id."',
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
							'".$obj_val2[$a]."',
							'".$Promo_descricao[$a]."',
							'".$Promo_data_ini[$a]."',
							'".$Promo_data_fim[$a]."',
							'".date('Y-m-d H:i:s')."'
						)
					";
					$res=Runquery($db_link,$DB_name,$sql);

		}
		$rs->db_close();
		// if($destaque==0 && $obj_destaque[$a]==1) $destaque=1;
		// if($outlet==0 && $obj_outlet[$a]==1) $outlet=1;
		// if($promocao==0 && $obj_p_ativo[$a]==1) $promocao=1;
		if($obj_id > 0)
		{
			$sql="SELECT ID FROM products_objs_lang WHERE ID_PAI='".$obj_id ."' AND Lang='".$GLOBALS['admin_lang']."'";
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
		/**
		gera_json_destaques(1);
		gera_json_home_promocoes(1);
		gera_json_maisvendidos(1);
		gera_json_home_novidades(1);
		/**/
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_destaque.txt';
		if(file_exists($f_original))unlink($f_original);
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_promocao.txt';
		if(file_exists($f_original))unlink($f_original);
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_maisvendido.txt';
		if(file_exists($f_original))unlink($f_original);
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_novidades.txt';
		if(file_exists($f_original))unlink($f_original);
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_random.txt';
		if(file_exists($f_original))unlink($f_original);
		/**/

		/*$res = get_produtc_info($ID_PROD);
		$repor = $GLOBALS['site_shell_url'].'files/filtros/json_'.$res['fam_id'].'_'.$GLOBALS['admin_lang'].'.js';
		if(file_exists($repor))unlink($repor);*/

	/*

	 $sql="SELECT * FROM products_promocoes WHERE ID_Prod='".$ID_PROD ."'";
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
	}
	//*/
}
function show_easy_create()
{
	global $browser,$ID;
	if(($browser=="Firefox" || $browser=="Mozilla") && $ID)
	{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center">EASY CREATE</td>
	</tr>
	<tr>
		<td class=preto align="center">
			<table width="100%" border=0 cellspacing=0 cellpadding=0 height="100px" bgcolor="#FFFFFF">
			<tr>
				<td align="center"><? include "easycreate.php"; ?></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
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
		<td class="titulo2" align="center">CROSS/UP SELLING</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Produtos relacionados" onclick="javascript:relacionados()"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">CLIENTES ALERTA STOCK</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Notificar clientes" onclick="javascript:notificarClientes(<?php echo $_GET['ID'];?>)"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_drop_tools()
{
	return;



	global $browser,$ID;
	if(($browser=="Firefox" || $browser=="Mozilla") && $ID)
	{?>
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
        <td class="titulo2" align="center">EASY DROP</td>
    </tr>
    <tr>
        <td class=preto align="center">
            <table width="100%" border=0 cellspacing=0 cellpadding=0 height="100px" bgcolor="#FFFFFF">
            <tr>
                <td align="center"><? include "easydrop.php"; ?></td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
	<?
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
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PRODUTOS</h4>
        </div>
    </div>
    <form action="products_families.php" method="post">

	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
    <tr>
    	<td width=29 align="center"><input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
	    <td align="right" class="branco">
	    	<div style="float: left;line-height: 30px;padding-right: 10px">
	    		<? echo write_lang('key_pesquisar_por_nome','Pesquisar por Nome')?>:
	    	</div>
	    	<div style="float: left;line-height: 30px;padding-right: 10px">
	    		<input class="form-control" style="width:100px;" type="text" name="pesquisa" id="pesquisa" value="<?php echo $pesquisa?>">
	    	</div>
	    	<div style="float: left;line-height: 30px;padding-right: 10px">
            	<? echo write_lang('key_or','ou')?> <? echo write_lang('key_por_ref','por referência')?>:
            </div>
            <div style="float: left;line-height: 30px;padding-right: 10px">
            	<input class="form-control" style="width: 100px;" type="text" name="pesquisa2" id="pesquisa2" value="<?php echo $pesquisa2?>">
            </div>
	    	<div style="float: left;line-height: 30px;padding-right: 10px">
            	<? echo write_lang('key_or','ou')?> <? echo write_lang('key_por_id','por ID')?>:
            </div>
        	<div style="float: left;line-height: 30px;padding-right: 10px">
            	<input class="form-control" style="width: 100px;" type="text" name="pesquisa3" id="pesquisa3" value="<?php echo $pesquisa3?>">
            </div>
            <div style="float: left;line-height: 30px">
            	<input  class="btn btn-primary" type="submit" name="Pesquisar" value="<? echo write_lang('key_pesquisar','Pesquisar')?>">
        	</div>
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
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
        <td align="right"><input type="button" onclick="javascript:window.open('list.php?edit=1','_self')" value="<? echo write_lang('key_ativar_edicao_na_pagina','Activar Edição na Página')?>" class="btn btn-primary" style="width:150px">&nbsp;</td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_page_bottom_tools2($tipo=0)
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
		<td width="125px"><select name="flag2" id="flag2" class="form-control">
				<option value="" selected="selected"></option>
				<option value="3"><?php echo write_lang('key_mover_para_a_familia','Mover para a familia');?></option>
				<option value="5">Mover para as familias escolhidas</option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familias(0,"products_families","products_families_lang",$tipo);?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Listagem avançada" onclick="javascript:window.open('avancada.php?dp=<?php echo uniqid('');?>','_self')" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_ver_em_forma_de_arvore','Ver em Forma de Árvore')?>" onclick="javascript:window.open('treecatalog.php?dp=<?php echo uniqid('');?>','_self')" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_ver_em_lista','Ver em Lista')?>" onclick="javascript:window.open('list.php?dp=<?php echo uniqid('');?>','_self')" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_nova','Criar Nova')?> <? if($ID) echo 'SUB-';?><? echo write_lang('key_categoria','Category')?>" onclick="javascript:window.open('products_families.php?ID=0&ID_ORG=<? echo $ID?>&dp=<? echo uniqid('');?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_produto','Criar Novo Produto')?>" onclick="javascript:window.open('products.php?ID=0&ID_ORG=<?php echo $ID;?>&from_prods=<?php echo $from_prods?>&to_prods=<?php echo $to_prods?>&total_prods=<?php echo $total_prods?>','_self')"></td>
	</tr>
<?php  /*	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Export Price Bench" onclick="javascript:window.open('price_bench.php','_new')"></td>
	</tr>
   <tr>
		<td class="titulo2" align="center">LISTAGENS PRODUTOS</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «descrição»" onclick="javascript:window.open('lista_produtos_semdescricao.php','_new')"></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «como usar»" onclick="javascript:window.open('lista_produtos_semcomousar.php','_new')"></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «saiba mais»" onclick="javascript:window.open('lista_produtos_semsaibamais.php','_new')"></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «todos descritivos»" onclick="javascript:window.open('lista_produtos_semtodosdescritivos.php','_new')"></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «foto»" onclick="javascript:window.open('lista_produtos_semimagem.php','_new')"></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «marca»" onclick="javascript:window.open('lista_produtos_semmarca.php','_new')"></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Sem «peso»" onclick="javascript:window.open('lista_produtos_sempeso.php','_new')"></td>
	</tr> */?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_fast_tools_destaques()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo 'Adicionar produto';?>" onclick="javascript:window.open('add_product_destaque.php','add_product_destaque','height=600,width=800')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_fast_tools_promo()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo 'Adicionar produto';?>" onclick="javascript:window.open('add_product_promocao.php','add_product_promocao','height=600,width=800')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_fast_tools_extra()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo 'Adicionar produto';?>" onclick="javascript:window.open('add_product_extra.php','add_product_extra','height=600,width=800')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $edit;
	?>
    <table class="listagem" width="100%" border=0 cellspacing=0 cellpadding="2">
    <?php
    $rs=GetRecords_Recursos_1(0,$ID_ORG,0);
    $count=0;
    while(!$rs->EOF())
    {
		$count++;
		$codigo=$rs->db_data["ID"];
		$ordem=$rs->db_data["Ordem"];
		$desconto=number_format($rs->db_data["Desconto"],2);
		$Visivel=$rs->db_data["visivel"];
		$cabecalho=stripslashes($rs->db_data["Cabecalho"]);
		$cabecalho2=stripslashes($rs->db_data["Cabecalho2"]);
		$Protected=$rs->db_data["Protected"];

		$cor_url='class="preto"';
		if($Visivel==0)$cor_url='style="color:#ccc"';

		if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}

		if($cabecalho2)$cabecalho=$cabecalho2;
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb"></td><? }?>

            <td width="29" align="center"><a <?=$cor_url?> href="products_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>

            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a <?=$cor_url?> href="products_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
            <? if($desconto > 0){?> <td width="80" class="desconto<?php echo $n?>"><?php echo $desconto?> %</td> <? }else{?> <td width="30" class="desconto<?php echo $n?>">&nbsp;</td> <td width="4" align="right">&nbsp;</td><?php }?>
            <td align="center" width="30"><img title="<? if($Visivel) echo write_lang('key_pagina_visivel','Página Visivel'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
			<? if($Protected==0){?><td width="30" align="center"><a class="italico" href="products_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=0&Nivel=0&task=duplicar"><img title="<? echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
			<td width="30" align="center"><a class="italico" href="products_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=0&Nivel=0&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td><? }?>
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
				P.ID,P.img_ext,P.Destaque,PL.Cabecalho,PL.Texto1
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
		$sql2='SELECT Ordem from products_promocoes where ID_Prod='.$codigo;
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
		if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr id="<?php echo $codigo?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" <?php echo $checked?>></td><? }?>
            <td width="29" align="center"><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
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
				P.ID,P.img_ext,P.Destaque,PL.Cabecalho,PL.Texto1
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
		$sql2='SELECT Ordem from products_destaques where ID_Prod='.$codigo;
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
		if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr id="<?php echo $codigo?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" <?php echo $checked?>></td><? }?>
            <td width="29" align="center"><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>
            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
            <td width="30" align="center" class=preto><input type="text" name="field_<?php echo $codigo?>" style="width:35px;" value="<?php echo $ordem?>"></td>
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
	$sql="Select ID from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia" id="m_familia" class="form-control4" style="width:300px">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select Cabecalho from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
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
function gera_lista_familias_produto($id_org=0,$tabela1,$tabela1_lang)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select ID from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia'.$id_org.'" id="m_familia'.$id_org.'" class="form-control4" style="width:300px">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select Cabecalho from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
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
	$sql="Select ID from $tabela1 where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    while(!$obj->EOF())
    {
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select Cabecalho from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
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
	$sql="Select ID,Nome from $tabela1";
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
	if($id_org==0) return 0;
	else
	{
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$condicao="where ID=$id_org";
		$sql="Select ID,ID_ORG from ".$tabela1." $condicao order by Ordem Desc";
		$hRes=$obj->db_open($db_link,$DB_name,$sql);
		if(!$obj->EOF())
		{
			$sql="Select Cabecalho from ".$tabela1."_lang where ID_PAI=$id_org and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);
			$C=htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"]));
			$obj2->db_close();
			$id_org=$obj->db_data["ID_ORG"];
			$obj->db_close();
			$result=show_products_menu_path($id_org,$m_pagina,$tabela1,$number_per_page,$checkshowimages);
			if($id_org > 0) echo " / ";
			echo '<a href="'.$m_pagina.'?ID='.$id_org2.'&ID_ORG='.$id_org2.'&total_prods='.$number_per_page.'&checkshowimages='.$checkshowimages.'">'.$C.'</a>';
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
    echo '<select name="'.$sel_nm.'" id="'.$sel_nm.'" class="form-control4" style="width:300px">';

	if($id_marca==0) echo '<option value="1" selected>- nenhuma -</option>';
	else echo '<option value="1">- nenhuma -</option>';

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
	$porpagina=20;
	$tem_pesquisa=0;
	$condicao="WHERE products_lang.Lang='".$admin_lang."' ";
	if($pesquisa1 || $pesquisa2 || $pesquisa3 || $pesquisa4)
	{
		if($pesquisa1) $condicao.= " and products.ID='".$pesquisa1."' ";
		if($pesquisa2) $condicao.= " and products_objs_lang.Cabecalho like '%".$pesquisa2."%' ";
		if($pesquisa3) $condicao.= " and products_lang.Cabecalho like '%".$pesquisa3."%' ";
		if($pesquisa4) $condicao.= " and products_families_obj.ID_ORG='".$pesquisa4."' ";
		$tem_pesquisa=1;
	}
	elseif($id!=0)
	{
		$condicao="where products.ID=$id and products_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao="$condicao and products_families_obj.ID_ORG=$id_org and products_lang.Lang='".$admin_lang."'";
		else $condicao="$condicao and products_families_obj.ID_ORG='0'";
	}
	else
	{
		$condicao="where products.ID > 0  and products_lang.Lang='".$admin_lang."'";
		if($id_org!=0) $condicao=$condicao." and products_families_obj.ID_ORG=$id_org";
		else $condicao="$condicao";
	}
	if($tem_pesquisa)
	{
	    if($pesquisa4)
	    {
	    	$sql1="Select products.ID from products inner join products_lang on products_lang.ID_PAI=products.ID inner join products_families_obj on products.ID=products_families_obj.ID_PROD inner join products_objs on products.ID=products_objs.obj_type_id inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $condicao GROUP BY products.ID ORDER BY CAST(products_objs_lang.Cabecalho AS SIGNED) asc $key_limite ";
	    	$sql="Select $campos,products.ID,products_objs_lang.Cabecalho as Ref2 from products inner join products_lang on products_lang.ID_PAI=products.ID inner join products_families_obj on products.ID=products_families_obj.ID_PROD inner join products_objs on products.ID=products_objs.obj_type_id inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $condicao GROUP BY products.ID ORDER BY CAST(products_objs_lang.Cabecalho AS SIGNED) asc $key_limite ";
		}
		else
		{
			$sql1="Select products.ID from products inner join products_lang on products_lang.ID_PAI=products.ID inner join products_families_obj on products.ID=products_families_obj.ID_PROD inner join products_objs on products.ID=products_objs.obj_type_id inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $condicao GROUP BY products.ID ORDER BY CAST(products_objs_lang.Cabecalho AS SIGNED) asc $key_limite ";
			$sql="Select $campos,products.ID,products_objs_lang.Cabecalho as Ref2 from products inner join products_lang on products_lang.ID_PAI=products.ID inner join products_families_obj on products.ID=products_families_obj.ID_PROD inner join products_objs on products.ID=products_objs.obj_type_id inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $condicao GROUP BY products.ID ORDER BY CAST(products_objs_lang.Cabecalho AS SIGNED) asc $key_limite ";
		}
	}
	else
	{
		$sql1="Select products.ID from products inner join products_families_obj on products.ID=products_families_obj.ID_PROD WHERE products_families_obj.ID_ORG=$id_org $key_limite ";
		$sql="Select $campos,products.ID from products inner join products_families_obj on products.ID=products_families_obj.ID_PROD WHERE products_families_obj.ID_ORG=$id_org $key_limite ";
	}

	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql1);
	$total=$rs->db_rows;
	$rs->db_close();


	$n_pags=ceil($total / $porpagina); // n de paginas
	if(!isset($_GET['pagina'])) $pagina='1';
	else $pagina=$_GET['pagina'];
	$inicio=$pagina - 1;
	$inicio=$inicio * $porpagina;
	$sql2=$sql." LIMIT $inicio,$porpagina";
	// echo $sql2;
	$obj->db_open($db_link,$DB_name,$sql2);


	

	if($n_pags > 1)
	{
		$return='<div class="btn-group">';
		 $p_seo='products_families.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'';
		if($pagina > 6)
		{
			if(substr_count($p_seo,'?')) $ash='&pagina=1';	else $ash='?pagina=1';
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= '<button type="button" class="btn btn-white" '.$pgn.'>1...</button>';
		}
		for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			if(substr_count($p_seo,'?')) $ash='&pagina='.$pi;	else $ash='?pagina='.$pi;
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<button type="button" class="btn btn-white active" '.$pgn.'>'.sprintf(($pi)).'</button>' : '<button type="button" class="btn btn-white" '.$pgn.'>'.sprintf($pi).'</button>' :'';
		}
		if($n_pags!=$pagina)
		{
			if(substr_count($p_seo,'?')) $ash='&pagina='.$n_pags;	else $ash='?pagina='.$n_pags;
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= '<button type="button" class="btn btn-white" '.$pgn.'>'.sprintf($n_pags).'</button>';
		}
		$return .= '</div>';
	}





	/*if($n_pags > 1)
	{
		$return='<div class="clear" style="height:10px"></div><table class="new_quadros" border=0 cellspacing=0 cellpadding=0 style="margin-left:auto;margin-right:auto;"><tr>';
		 $p_seo='products_families.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'';
		if($pagina > 6)
		{
			if(substr_count($p_seo,'?')) $ash='&pagina=1';	else $ash='?pagina=1';
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= '<td width="2px"></td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597">1</td><td width="2px">...</td>';
		}
		for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			if(substr_count($p_seo,'?')) $ash='&pagina='.$pi;	else $ash='?pagina='.$pi;
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px"></td><td width="20" height="15" bgcolor="#006d9f" align="center" style="font-size:10px;color:#FFF;padding-top:2px;border:#939597 1px solid" >'.sprintf(($pi)).'</td>' : '<td width="2px"></td><td width="20" height="15" bgcolor="#edf4f9" onmouseover="this.style.background=\'#006d9f\';this.style.color=\'#FFFFFF\'" onmouseout="this.style.background=\'#edf4f9\';this.style.color=\'#939597\'" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;margin:2px;color:#939597" >'.sprintf($pi).'</td>' :'';
		}
		if($n_pags!=$pagina)
		{
			if(substr_count($p_seo,'?')) $ash='&pagina='.$n_pags;	else $ash='?pagina='.$n_pags;
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= '<td width="2px"></td><td width="2px">...</td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597" >'.sprintf($n_pags).'</td>';
		}
		$return .= '</tr></table>';
	}*/
	return array('obj' => $obj,'return' => $return);
}
function BuscarNivelId($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select Nivel from products_families where ID=$id");
	$nivel=$obj->db_data["Nivel"];
	$obj->db_close();
	return($nivel);
}
function MaxNivel($db_link)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select max(Nivel) as maximo from products_families");
	$nivel=$obj->db_data["maximo"];
	$obj->db_close();
	return ($nivel);
}
function BuscarImg($id,$db_link)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select img_ext from products_families where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	$obj->db_close();
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
		$rs->db_close();
	}
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
		else echo db_error($db_link).$sql;
	}
	//exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
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
	$rs->db_open($db_link,$DB_name,"Select img_ext,img2_ext,img3_ext from products_families WHERE ID=$id");
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
		if($tem_foto1 && $Apagar_img1)
		{
			$ficheiro1=$id."_1.".$img_ext;
			if(file_exists($img_path.$ficheiro1))unlink($img_path.$ficheiro1);
			if(!$altera_img)$img_ext='';
		}
		if($altera_img)
		{
			copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);
			//putWatermarkImage($img_path,$id."_1.".$img_ext);
		}
		//IMAGEM NUMERO 2
		if($tem_foto2 && $Apagar_img2)
		{
			$ficheiro2=$id."_2.".$img2_ext;
			if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);
			if(!$altera_img2)$img2_ext='';
		}
		if($altera_img2){
			copia_segura($Img_peq2["temp"],$img_path.$id."_2.".$img2_ext);
			//putWatermarkImage($img_path,$id."_2.".$img_ext);
		}
		//IMAGEM NUMERO 3
		if($tem_foto3 && $Apagar_img3)
		{
			$ficheiro3=$id."_3.".$img3_ext;
			if(file_exists($img_path.$ficheiro3))unlink($img_path.$ficheiro3);
			if(!$altera_img3)$img3_ext='';
		}
		if($altera_img3)
		{
			copia_segura($Img_peq3["temp"],$img_path.$id."_3.".$img3_ext);
			//putWatermarkImage($img_path,$id."_3.".$img_ext);
		}
		$ordem1=str2SQLstr($ordem);
		$cabecalho1=str2SQLstr($cabecalho);
		$texto1=str2SQLstr($texto);
		$URL1=str2SQLstr($URL);
		$margemcli=str2SQLstr($margemcli);
		$margemrev=str2SQLstr($margemrev);
		$cor=str2SQLstr($cor);
		$visivel=str2SQLstr($visivel);
		$destaque=str2SQLstr($destaque);
		$Desconto=$_POST['Desconto'];
		$Margem=$_POST['Margem'];
		$DescontoTipo=$_POST['DescontoTipo'];
		$MargemTipo=$_POST['MargemTipo'];
		$Portes=$_POST['Portes'];
		$bonus=$_POST['pontos'].$_POST['tpoints'];
		if(!isset($_POST['m_media']))$ID_Media=0;
		else $ID_Media=$_POST['m_media'];


		$Margem_formula=$_POST['Margem_formula'];

		$sql="
				UPDATE
					products_families
				SET
					Portes='$Portes',
					ID_Media='$ID_Media',ID_ORG='$id_org',Ordem=$ordem1,img_ext='$img_ext',img2_ext='$img2_ext',img3_ext='$img3_ext',MargemCli=$margemcli,MargemRev=$margemrev,cor=$cor,visivel=$visivel,Destaque=$destaque,cat1='$cat1',cat2='$cat2',cat3='$cat3',
					Download='$download',
					Desconto='".$Desconto."',
					DescontoTipo='".$DescontoTipo."',
					Margem='".$Desconto."',
					Margem_formula='".$Margem_formula."',
					MargemTipo='".$MargemTipo."',
					preco_revenoff='".$preco_revenoff."',
					points='".$bonus."'
				WHERE
					ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		// APLICAR DESCONTO
		// echo '$DescontoTipo: '.$id.','.$Desconto.','.$DescontoTipo.'<br />';
		if($DescontoTipo > 0) aplica_lista_familias($id,$Desconto,$DescontoTipo);
		if($tipo_check==1)
		{
			$sql="Update products Set Tipo=$tipo where ID_ORG=$id";
			$res=Runquery($db_link,$DB_name,$sql);
		}
		db_closedb_conection($db_link);
		ChangeFamLinguas($id,$cabecalho,$cabecalho2,$URL,$texto);
		//exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
	}
}
function aplica_lista_familias($id_org=0,$Desconto=0,$DescontoTipo=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj; $obj2=new DataObj;
	$sql="SELECT ID FROM products_families WHERE ID_ORG='$id_org'";
	$obj->db_open($db_link,$DB_name,$sql);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$sql='UPDATE products_families SET Desconto="'.$Desconto.'", DescontoTipo="'.$DescontoTipo.'"WHERE ID="'.$obj->db_data["ID"].'"';
			$res=Runquery($db_link,$DB_name,$sql);
			if($obj->db_data["ID"] > 0) aplica_promos_produtos($obj->db_data["ID"],$Desconto,$DescontoTipo);
			if($DescontoTipo > 0) aplica_lista_familias($obj->db_data["ID"],$Desconto,$DescontoTipo);
			$obj->db_next();
		}
	}
	else
	{
		if($id_org > 0) aplica_promos_produtos($id_org,$Desconto,$DescontoTipo);
	}
    $obj->db_close();
}
/*function aplica_lista_margem_familias($id_org=0,$Margem=0,$MargemTipo=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj; $obj2=new DataObj;
	$sql="SELECT ID FROM products_families WHERE ID_ORG='$id_org'";
	$obj->db_open($db_link,$DB_name,$sql);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$sql='UPDATE products_families SET Margem="'.$Margem.'", MargemTipo="'.$MargemTipo.'"WHERE ID="'.$obj->db_data["ID"].'"';
			$res=Runquery($db_link,$DB_name,$sql);
			if($obj->db_data["ID"] > 0) aplica_margens_produtos($obj->db_data["ID"],$Margem,$MargemTipo);
			if($MargemTipo > 0) aplica_lista_margem_familias($obj->db_data["ID"],$Margem,$MargemTipo);
			$obj->db_next();
		}
	}
	else
	{
		if($id_org > 0) aplica_promos_produtos($id_org,$Desconto,$DescontoTipo);
	}
    $obj->db_close();
}*/
function aplica_promos_produtos($ID=0,$Desconto=0,$DescontoTipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2=new DataObj;
	$sql='SELECT ID_PROD as ID FROM products_families_obj where ID_ORG="'.$ID.'"';
	$obj2->db_open($db_link,$DB_name,$sql);
	if(!$obj2->EOF())
	{
		while(!$obj2->EOF())
		{
			if($Desconto==0 && $DescontoTipo==1) // SE O DESCONTO FOR 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM INATIVA
			{
				$sqlp='UPDATE products_objs SET obj_promo=0, obj_promo_ativo=0 WHERE obj_type_id="'.$obj2->db_data["ID"].'"';
			}
			elseif($Desconto==0 && $DescontoTipo==2) // A TODOS PRODUTOS SEM PROMOCAO
			{
				$sqlp='UPDATE products_objs SET obj_promo=0, obj_promo_ativo=0 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo=0 AND obj_promo_ativo=0 ';
			}
			elseif($Desconto==0 && $DescontoTipo==3) // A TODOS PRODUTOS COM PROMOCAO
			{
				$sqlp='UPDATE products_objs SET obj_promo=0, obj_promo_ativo=0 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo > 0 AND obj_promo_ativo=0 ';
			}
			elseif($Desconto > 0 && $DescontoTipo==1) // SE O DESCONTO FOR MAIOR QUE 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM ATIVA
			{
				$sqlp='UPDATE products_objs SET obj_promo=obj_pvp - (obj_pvp * ('.$Desconto.' / 100)), obj_promo_ativo=1 WHERE obj_type_id="'.$obj2->db_data["ID"].'"';
			}
			elseif($Desconto > 0 && $DescontoTipo==2)
			{
				$sqlp='UPDATE products_objs SET obj_promo=obj_pvp - (obj_pvp * ('.$Desconto.' / 100)), obj_promo_ativo=1 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo=0 AND obj_promo_ativo=0 ';
			}
			elseif($Desconto > 0 && $DescontoTipo==3)
			{
				$sqlp='UPDATE products_objs SET obj_promo=obj_pvp - (obj_pvp * ('.$Desconto.' / 100)), obj_promo_ativo=1 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo > 0 AND obj_promo_ativo=0 ';
			}
			if($sqlp)
			{
				$res=Runquery($db_link,$DB_name,$sqlp);
			}
			$obj2->db_next();
		}
	}
	$obj2->db_close();
}
function aplica_margens_produtos($ID=0,$Margem=0,$DescontoTipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2=new DataObj;
	$sql='SELECT ID_PROD as ID FROM products_families_obj where ID_ORG="'.$ID.'"';

	$obj2->db_open($db_link,$DB_name,$sql);
	if(!$obj2->EOF())
	{
		while(!$obj2->EOF())
		{
			if($Desconto==0 && $DescontoTipo==1) // SE O DESCONTO FOR 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM INATIVA
			{
				$sqlp='UPDATE products_objs SET obj_promo=0, obj_promo_ativo=0 WHERE obj_type_id="'.$obj2->db_data["ID"].'"';
			}
			elseif($Desconto==0 && $DescontoTipo==2) // A TODOS PRODUTOS SEM PROMOCAO
			{
				$sqlp='UPDATE products_objs SET obj_promo=0, obj_promo_ativo=0 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo=0 AND obj_promo_ativo=0 '; }
			elseif($Desconto==0 && $DescontoTipo==3) // A TODOS PRODUTOS COM PROMOCAO
			{
				$sqlp='UPDATE products_objs SET obj_promo=0, obj_promo_ativo=0 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo > 0 AND obj_promo_ativo=0 ';
			}
			elseif($Desconto > 0 && $DescontoTipo==1) // SE O DESCONTO FOR MAIOR QUE 0 VAMOS APLICAR A TODOS OS PRODUTOS E TEMOS QUE COLOCAR A PROMOCAO COM ATIVA
			{
				$sqlp='UPDATE products_objs SET obj_promo=obj_pvp - (obj_pvp * ('.$Desconto.' / 100)), obj_promo_ativo=1 WHERE obj_type_id="'.$obj2->db_data["ID"].'"';
			}
			elseif($Desconto > 0 && $DescontoTipo==2)
			{
				$sqlp='UPDATE products_objs SET obj_promo=obj_pvp - (obj_pvp * ('.$Desconto.' / 100)), obj_promo_ativo=1 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo=0 AND obj_promo_ativo=0 ';
			}
			elseif($Desconto > 0 && $DescontoTipo==3)
			{
				$sqlp='UPDATE products_objs SET obj_promo=obj_pvp - (obj_pvp * ('.$Desconto.' / 100)), obj_promo_ativo=1 WHERE obj_type_id="'.$obj2->db_data["ID"].'"AND obj_promo > 0 AND obj_promo_ativo=0 ';
			}
			if($sqlp)
			{
				$res=Runquery($db_link,$DB_name,$sqlp);
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
	$altera=0;$rs->db_open($db_link,$DB_name,"Select ID,SEO_img,ICON_img from products_families_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())
	{
		$altera=1;
		$id_video=$rs->db_data["ID"];
		$img_ext=$rs->db_data["SEO_img"];
		$img_ext2=$rs->db_data["ICON_img"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewFamLinguas($id_pai,$cabecalho,$cabecalho2,$URL,$texto);
	else
	{
		$cabecalho1=str2SQLstr($cabecalho);
		$cabecalho2=str2SQLstr($cabecalho2);
		$texto1=$texto;
		$URL1=str2SQLstr($URL);

		$SEO_img_file["name"] = $_FILES['SEO_img']['name'];
		$SEO_img_file["temp"] = $_FILES['SEO_img']['tmp_name'];
		$Apagar_SEO_img = $_POST['Apagar_SEO_img'];
		$tem_SEO_img = $_POST['tem_SEO_img'];
		$altera_img=0;if($SEO_img_file["name"]){$altera_img=1;$pos=strrpos($SEO_img_file["name"],".");$img_ext=substr($SEO_img_file["name"],$pos+1,strlen($SEO_img_file["name"])+1-$pos);}
		if($tem_SEO_img && $Apagar_SEO_img)
		{
			if(file_exists('../../files/products_families/'.$id_pai."_seo_".$m_lang.".".$img_ext))unlink('../../files/products_families/'.$id_pai."_seo_".$m_lang.".".$img_ext);
			if(!$altera_img)$img_ext='';
		}
		if($altera_img){copia_segura($SEO_img_file["temp"],'../../files/products_families/'.$id_pai."_seo_".$m_lang.".".$img_ext);}

		//icon
		$ICON_img_file["name"] = $_FILES['ICON_img']['name'];
		$ICON_img_file["temp"] = $_FILES['ICON_img']['tmp_name'];
		$Apagar_ICON_img = $_POST['Apagar_ICON_img'];
		$tem_ICON_img = $_POST['tem_ICON_img'];
		$altera_img2=0;if($ICON_img_file["name"]){$altera_img2=1;$pos=strrpos($ICON_img_file["name"],".");$img_ext2=substr($ICON_img_file["name"],$pos+1,strlen($ICON_img_file["name"])+1-$pos);}
		if($tem_ICON_img && $Apagar_ICON_img==1)
		{
			if(file_exists('../../files/products_families/'.$id_pai."_ICON_".$m_lang.".".$img_ext2))unlink('../../files/products_families/'.$id_pai."_ICON_".$m_lang.".".$img_ext2);
			if(!$altera_img2)$img_ext2='';
		}
		if($altera_img2){copia_segura($ICON_img_file["temp"],'../../files/products_families/'.$id_pai."_ICON_".$m_lang.".".$img_ext2);}

		$prod_txt1=addslashes($_POST['prod_txt1']);

		$sql="Update products_families_lang Set Cabecalho=$cabecalho1,Cabecalho2=$cabecalho2,URL=$URL1,Texto='$texto1',prod_txt1='$prod_txt1',SEO_img='$img_ext',ICON_img='$img_ext2' where ID_PAI=$id_pai and Lang='$admin_lang'";
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
		else
		{
			$pos=strrpos($Img_peq2["name"],".");
			$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
			$img2_ext=str2SQLstr($ext2);
			copia_segura($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
            redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_".$num."_2.".$ext2);// redimensiona para o tamanho default
			redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_".$num."_1.".$ext2);// gera o tamanho pequeno
		}
	}
	else  //TINHA IMAGEM
	{
		if($Apagar_img2==1)
		{
			if($num==4){$ficheiro2=$id."_1.".$ext2; if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);}
			if($num==4){$ficheiro2=$id."_2.".$ext2; if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);}
			$ficheiro2=$id."_".$num.".".$ext2; if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_1.".$ext2; if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_2.".$ext2; if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_0.".$ext2; if(file_exists($img_path.$ficheiro2))unlink($img_path.$ficheiro2);
			//NAO SUBSTITUI A IMAGEM
			if(!$altera_img2){$img2_ext='';$img2_ext=str2SQLstr($img2_ext);}
			else
			{
				$pos=strrpos($Img_peq2["name"],".");
				$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
				$img2_ext=str2SQLstr($ext2);
				copia_segura($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
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
				copia_segura($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
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
	if(strlen($Str_pesquisar)<=4)
	{
		$and .= ' pol.Cabecalho like "%'.$Str_pesquisar.'%" ';
		$and .= ' OR pl.Cabecalho like "%'.$Str_pesquisar.'%" ';
	}
	else
	{
		$stringbooleanmode=searchIntoBooleanMode(" ","+",strtolower($Str_pesquisar));
		//nome produto
		$and .= ' MATCH(pol.Cabecalho,pl.Cabecalho) AGAINST("'.$stringbooleanmode.'" IN BOOLEAN MODE) ';
	}

	$sql='SELECT  DISTINCT p.ID as PID,p.ID_ORG,pl.Cabecalho,pol.Cabecalho as Ref
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
				// echo $sql;
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
	$obj->db_close();
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
					// echo "<br>".$sql;
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
	if(!$obj->EOF())
	{
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
function products_families_save($id_prod,$id_org)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT * FROM products_families_obj WHERE ID_PROD='".$id_prod."'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$sql="UPDATE products_families_obj SET ID_ORG='".intval($id_org)."' WHERE ID_PROD='".$id_prod."'";
		$res=Runquery($db_link,$DB_name,$sql);
	}
	else
	{
		$sql="INSERT INTO products_families_obj(ID_PROD,ID_ORG) values ('".$id_prod."','".intval($id_org)."')";
		$res=Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();
}

function getImagensProduto($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$arr_imagens=array();
	$pos=0;
	$sql="SELECT ID,img_ext,ID_ORG,Ordem FROM products_images WHERE ID_ORG='".$ID."' group by Ordem order by Ordem ASC";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
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
	$names=(isset($Img_peq['name']) && is_array($Img_peq['name'])) ? $Img_peq['name'] : array();
	$tmp_names=(isset($Img_peq['tmp_name']) && is_array($Img_peq['tmp_name'])) ? $Img_peq['tmp_name'] : array();
	$tem_foto=is_array($tem_foto) ? $tem_foto : array();
	$Apagar_img=is_array($Apagar_img) ? $Apagar_img : array();
	$slots=array_unique(array_merge(array_keys($names),array_keys($tem_foto),array_keys($Apagar_img)));
	sort($slots,SORT_NUMERIC);

	foreach($slots as $i)
	{
		$name=trim((string)($names[$i] ?? ''));
		$tmp_name=(string)($tmp_names[$i] ?? '');
		if($name!=='' && $tmp_name!=='' && is_file($tmp_name))
		{
			$img_ext=strtolower((string)pathinfo($name,PATHINFO_EXTENSION));
			if(!in_array($img_ext,array('jpg','jpeg','png','gif','webp'),true))
			{
				error_log('[products_images_save] Extensão de imagem recusada: '.$img_ext);
				continue;
			}
			$image_info=@getimagesize($tmp_name);
			if($image_info===false)
			{
				error_log('[products_images_save] Ficheiro recusado por não ser uma imagem válida: produto '.(int)$ID.', posição '.(int)$i);
				continue;
			}

			$sql="SELECT img_ext FROM products_images WHERE ID_ORG='".(int)$ID."' AND Ordem='".(int)$i."' ORDER BY Ordem";
			$rs->db_open($db_link,$DB_name,$sql);
			$old_ext=$rs->EOF() ? '' : (string)$rs->db_data['img_ext'];
			$rs->db_close();

			$target=$img_path.(int)$ID.'_'.(int)$i.'.'.$img_ext;
			$temporary=$target.'.uploading.'.uniqid('',true).'.'.$img_ext;
			// Não usar copia_segura() para o ficheiro temporário: essa função
			// remove todas as ocorrências da extensão no caminho e corrompe nomes
			// do tipo "produto.jpg.uploading.<id>.jpg".
			if(!@copy($tmp_name,$temporary) || !is_file($temporary))
			{
				error_log('[products_images_save] Não foi possível copiar a imagem do produto '.(int)$ID.', posição '.(int)$i);
				@unlink($temporary);
				continue;
			}
			if(!@rename($temporary,$target))
			{
				error_log('[products_images_save] Não foi possível publicar a imagem do produto '.(int)$ID.', posição '.(int)$i);
				@unlink($temporary);
				continue;
			}

			if($old_ext!=='')
				$sql='UPDATE products_images SET img_ext="'.$img_ext.'", URL_Source="" WHERE ID_ORG="'.(int)$ID.'" AND Ordem="'.(int)$i.'"';
			else
				$sql='INSERT INTO products_images(ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.(int)$ID.'","'.$img_ext.'","'.(int)$i.'","")';
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res)
			{
				@unlink($target);
				continue;
			}
			if($old_ext!=='' && $old_ext!==$img_ext)
				@unlink($img_path.(int)$ID.'_'.(int)$i.'.'.$old_ext);
		}
		elseif(($tem_foto[$i] ?? 0)==1 && ($Apagar_img[$i] ?? 0)==1)
		{
			$sql="SELECT img_ext FROM products_images WHERE ID_ORG='".(int)$ID."' AND Ordem='".(int)$i."' ORDER BY Ordem";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$old_ext=(string)$rs->db_data['img_ext'];
				$sql='DELETE FROM products_images WHERE ID_ORG="'.(int)$ID.'" AND Ordem="'.(int)$i.'"';
				if(Runquery($db_link,$DB_name,$sql))
					@unlink($img_path.(int)$ID.'_'.(int)$i.'.'.$old_ext);
			}
			$rs->db_close();
		}
	}
	$sql="SELECT img_ext FROM products_images WHERE ID_ORG='".$ID."' ORDER BY Ordem Limit 1";
	$rs->db_open($db_link,$DB_name,$sql);
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
	define('N_PAGINAS',10);
	$rs=new DataObj;
	$rs2=new DataObj;
	$sql="
			SELECT
				products.*,products.Ref as Referencia,products_lang.Cabecalho,products_objs.obj_stock
			FROM
				products
				inner join products_families_obj on products_families_obj.ID_ORG=products.ID
				inner join products_objs on products_objs.obj_type_id=products.ID
				inner join products_lang on products_lang.ID_PAI=products.ID
				where
					1=1
					AND products_families_obj.ID_ORG=0
				";
    if(isset($_GET['p']) && $_GET['p']!='') $sql .= " AND products_lang.Cabecalho like '%".$_GET['p']."%'";
    $sql .= " Group by products.ID";

	$rs->db_open($db_link,$DB_name,$sql);
	$total=$rs->db_rows;
	//$rs->db_close();


	$n_pags=ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina=1;
	else $pagina=$_GET['pagina'];
	$inicio=$pagina - 1;
	$inicio=$inicio * N_PAGINAS;
	$sql2=$sql.' Limit '.$inicio.','.N_PAGINAS;

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
		<form name="form2" method="post"><br>
		<table width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
        <tr>
            <td>
            	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
                <tr>
                    <td width="5px">&nbsp;</td>
                    <td class="branco">Produtos sem categoria</td>
                </tr>
                </table>
                <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                <tr>
                    <td width="100" height="10px" class="branco" align="center">Pesquisa</td>
                    <td class="branco" width="300"><input class="form-control4" type="text" name="pesq_list" id="pesq_list" style="width:300px" value="<?php echo $_GET['p'];?>" /></td>
                    <td width="100" align="center"><input class="btn btn-primary" type="button" value="pesquisa" onclick="window.open('list.php?d=&pagina=1&p='+$('#pesq_list').val(),'_top');" /></td>
                    <td>&nbsp;</td>
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
					$PrecoCusto=$rs->db_data["PrecoCusto"];
					$preco2=$rs->db_data["Preco2"];
					$preco_prec=$rs->db_data["Preco_prec"];
					$precopromo1=$rs->db_data["PrecoPromo1"];
					$C=$rs->db_data["Cabecalho"];
					$ID=$rs->db_data["ID_ORG"];
					$fornecedor_importacao='';
					$fornecedor_importacao=$rs->db_data["fornecedor_importacao"];
					$obj_stock=$rs->db_data["obj_stock"];
					//********* linguas ***//
					$contain='';
					if($contain) $C2=$C.' - ['.$contain.']';
					else $C2=$C;

					if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
					else				{$cor="#FFFFFF";$class="r2";$n="2";}
					?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
					<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
						<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" checked="checked" /></td>
						<!-- <td width="29" align="center"><?php //show_familias_save($codigo,0)?></td> -->
						<td width="14"></td>
						<td width="200" align="left"><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&Nivel=<?php echo $Nivel+1;?>"><?php echo $codigo?>&nbsp;-&nbsp;<?php echo $ref?></a></td>
						<td align="left"><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&Nivel=<?php echo $Nivel+1;?>"><? if($C2) echo htmlentities_b($C2); else echo "[Edit]"?> </a></td>
	                    <td width="50" align="center" bgcolor="<?php echo $cor;?>"><?php echo $obj_stock; ?></td>
	                    <td width="100" align="right" bgcolor="<?php echo $cor;?>"><?php echo $fornecedor_importacao; ?></td>
	                    <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
						<td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products.php?ID=<?php echo $rs->db_data["ID"]?>&task=apagar"><img title="<?php echo write_lang('key_apagar','Apagar')?>" src="del2.png" border="0" /></a></td>
						<td width="4px" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
					</tr>
					</table><?
					$rs->db_next();
					$count++;
				}
				show_page_bottom_tools3();
				if($n_pags > 1)
				{
					if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages='pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&bloco_visivel='.urlencode($_GET['bloco_visivel']);
					else $url_pages='list.php?d=&p='.$_GET['p'];
					$return='<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
					if($pagina > 6)
					{
						$pgn='onclick=window.open("'.$url_pages.'&pagina=1","_self")';
						$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
					}
					for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
					{
						$pgn='onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
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
function show_familias_save($id_start=0,$ID_ORG=0,$on=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select products_families.ID,Cabecalho from products_families INNER JOIN products_families_lang ON products_families_lang.ID_PAI=products_families.ID where ID_ORG='.$ID_ORG.' AND products_families.ID!=14 and Lang="pt" order by Cabecalho');
	if($ID_ORG == 0) echo '<select name="m_familia_'.$id_start.'" style="width:150px">';
	if($ID_ORG == 0) echo '<option value="0" selected>- nenhuma -</option>';
	while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		// if($id_org==$rs->db_data['ID']) echo ' selected ';
	    // $rs2->db_open($db_link,$DB_name,'Select Cabecalho from products_families_lang where ID_PAI="'.$rs->db_data['ID'].'" and Lang="pt"');
		echo '>';
		if($on > 0)
		{
			$sp='';
			for($a=0; $a<$on; $a++){$sp .= '- ';}
			echo $sp.$rs->db_data['Cabecalho'].'</option>';
		}
		else echo $rs->db_data['Cabecalho'].'</option>';
		show_familias_save($id_start,$rs->db_data['ID'],($on+2));
        $rs->db_next();
    }
    $rs->db_close();
}
function show_products_without_categorie_list()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$img_path,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	define('N_PAGINAS',10);
	$rs=new DataObj;$rs2=new DataObj;
	$sql="
			SELECT
				P.ID,P.Ref,PL.Cabecalho,PL.Texto1,PL.Texto3,PO.obj_pvp,PO.obj_val2,PI.img_ext,PP.prod_id AS PERSONALIZACAO
			FROM
				products P
				INNER JOIN products_lang PL on PL.ID_PAI=P.ID
				INNER JOIN products_objs PO on PO.obj_type_id=P.ID
				LEFT JOIN products_images PI on PI.ID_ORG=P.ID
				LEFT JOIN products_personalizacao2 PP on PP.prod_id=P.ID
			WHERE
				PO.obj_val2=0
				AND
				(
					PI.img_ext is NULL
					OR (PO.obj_pvp=0)
					OR PL.Texto1=''
					OR PP.prod_id is NULL
				)
			GROUP BY P.ID
			ORDER BY P.ID

			";
	$rs->db_open($db_link,$DB_name,$sql);
	$total=$rs->db_rows;
	// echo $total;
	$rs->db_close();
	$n_pags=ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina=1;
	else $pagina=$_GET['pagina'];
	$inicio=$pagina - 1;
	$inicio=$inicio * N_PAGINAS;
	$sql2=$sql.' Limit '.$inicio.','.N_PAGINAS;
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
		<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="0" align="center">
        <tr>
            <td>
            	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                <tr>
                    <td width="5px">&nbsp;</td>
                    <td class="branco">Produtos sem (PREÇO DE CLIENTE FINAL ou FOTO ou DESCRIÇÃO ou ESPECIFICAÇÕES)</td>
                </tr>
                </table>
                <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                <tr>
                    <td class="branco" height="30px">&nbsp;</td>
                    <td class="branco" align="center">ID</td>
                    <td class="branco" height="10px" align="center"></td>
                    <td class="branco" height="10px">&nbsp;</td>
                    <td class="branco" align="left">REF.</td>
                    <td class="branco">&nbsp;</td>
                    <td class="branco">&nbsp;</td>
					<td class="branco" align="left">NOME</td>
                    <td class="branco">&nbsp;</td>
                    <td class="branco">&nbsp;</td>
					<td class="branco" align="left">EM FALTA</td>
                </tr>
				<?
				$count=0;
				while(!$rs->EOF())
				{
					// P.ID,P.Ref,PL.Cabecalho,PL.Texto1,PL.Texto3,PO.obj_pvp,PO.obj_val2,PI.img_ext
					$em_falta='';
					$ID=$rs->db_data["ID"];
					$Ref=$rs->db_data["Ref"];
					$Cabecalho=$rs->db_data["Cabecalho"];
					$Texto1=strip_html_tags($rs->db_data["Texto1"]); // Descrição
					if($Texto1=='') $em_falta .= 'DESCRIÇÃO | ';
					// $Texto3=$rs->db_data["Texto3"];
					$obj_pvp=$rs->db_data["obj_pvp"];
					if($obj_pvp == 0) $em_falta .= 'PREÇO | ';
					$PERSONALIZACAO=$rs->db_data["PERSONALIZACAO"];
					if($PERSONALIZACAO == '') $em_falta .= 'ESPECIFICAÇÕES | ';
					$img_ext=$rs->db_data["img_ext"];
					if($img_ext == '') $em_falta .= 'IMAGEM';
					$fornecedor='sfdsdf';
					if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
					else				{$cor="#FFFFFF";$class="r2";$n="2";}
				?>
					<tr style="background:<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
					<td width="29" align="center"><a class="preto" href="products.php?ID=<?php echo $ID;?>"> <img src="../images/page.png" /></a></td>
					<td width="50" class="preto"  align="center"><a class="preto" href="products.php?ID=<?php echo $ID;?>"> <?php echo $ID?></a></td>
					<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
					<td width="4" align="right">&nbsp;</td>
					<td width="130" class="preto" align="left"><a class="preto" href="products.php?ID=<?php echo $ID;?>"> <?php echo $Ref?></a></td>
					<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
					<td width="4" align="right">&nbsp;</td>
					<td><a class="preto" href="products.php?ID=<?php echo $ID;?>"> <? if($Cabecalho) echo htmlentities_b($Cabecalho); else echo "[SEM TÍTULO]"?> </a></td>
					<td width="4px" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
					<td width="4" align="right">&nbsp;</td>
					<td width="230" class="preto" align="left" style="font-size:10px;font-weight:bold"><?php echo rtrim($em_falta,' | ')?></td>
					</tr>
					<?
					$rs->db_next();
					$count++;
				}
				?></table><?php

				 // show_page_bottom_tools3();

				if($n_pags > 1)
				{
					if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages='pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&bloco_visivel='.urlencode($_GET['bloco_visivel']);
					else $url_pages='list.php?d=';
					$return='<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
					if($pagina > 6)
					{
						$pgn='onclick=window.open("'.$url_pages.'&pagina=1","_self")';
						$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
					}
					for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
					{
						$pgn='onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
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
				<option value="5">Mover para as familias escolhidas</option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familias(0,"products_families","products_families_lang",$tipo);?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
if (!function_exists('gera_lista_familias_cat'))
{
	function gera_lista_familias_cat($id_org=0,$tabela1,$tabela1_lang)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql="Select ID from $tabela1 where ID_ORG='0' order by Ordem Asc";
		$hRes=$obj->db_open($db_link,$DB_name,$sql);
		echo '<select name="s_familia">';
		if($id_org==0) echo '<option value="0" selected>- todas -</option>';
		else echo '<option value="0">- todas -</option>';
		while(!$obj->EOF())
		{
				echo '<option value="'.$obj->db_data["ID"].'"';
				if($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
				$sql="Select Cabecalho from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
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
	if(($tipo > 0 || $tipo==0) && $tipo!='') $tp=' and Tipo='.$tipo;
	else $tp='';
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select ID from '.$T.' where ID_ORG="0" '.$tp.' order by Ordem Asc');
    echo '<select name="m_familia2" style="width:150px" class="form-control4">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$rs->EOF())
    {
	    echo '<option value="'.$rs->db_data['ID'].'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select Cabecalho from '.$Tlang.' where ID_PAI='.$rs->db_data['ID'].' and Lang="'.$admin_lang.'"');
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
	$rs->db_open($db_link,$DB_name,'Select ID from '.$T.' where ID_ORG="'.$id_start.'" order by Ordem Asc');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if($id_org==$rs->db_data['ID']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select Cabecalho from '.$Tlang.' where ID_PAI="'.$rs->db_data['ID'].'" and Lang="'.$admin_lang.'"');
		echo '>';
		$i=0;while($i<$c){echo ' ';$i++;}
		echo $rs2->db_data['Cabecalho'].'</option>';
	    $rs2->db_close();
	    show_familiasb($rs->db_data['ID'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}
function show_translate_tools()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	global $ID,$ID_ORG;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">TRANSLATE TOOLS</td>
	</tr><?
	$sql="Select ID,Sigla from linguas";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Sigla=$rs->db_data["Sigla"];
		?>
		<tr>
			<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Traduzir para <?=$Sigla?>" onclick="javascript:window.open('products_families.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&translate_to=<?=$Sigla?>&d=<?=uniqid("");?>','_self')"></td>
		</tr><?
		$rs->db_next();
	}
	$rs->db_close();
	?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }

function reserva_stock($id_prod=0)
{
	if(!$id_prod)return;

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select sum(Qtd) as T from reserva_stocks WHERE ID_Produto = $id_prod";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$T=0;
	if(!$rs->EOF())
	{
		$T=$rs->db_data["T"];
	}
	$rs->db_close();
	return $T;
}
?>
