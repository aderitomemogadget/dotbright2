<?php

function mostra_estilos($estilo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$users2=explode(';',$users);
	global $admin_id;
	$where='WHERE Tipo=1 ORDER BY Nome ASC';
	$rs->db_open($db_link,$DB_name,'Select Nome from modulos_config '.$where);
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['Nome'].'"';
		if($rs->db_data['Nome']==$estilo) echo ' selected ';
		echo '>';
		echo str_replace('.', '. ', str_replace('#', '# ', $rs->db_data['Nome'])).'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
}
function gera_lista_grupos_users($users)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$users2=explode(';',$users);
	global $admin_id;
	$where='WHERE Grupo_ID=5';
	$rs->db_open($db_link,$DB_name,'Select ID,Nome from logins '.$where);
	echo '<select name="listagrupos[]" id="listagrupos" multiple="multiple"  class="chzn-select" style="width:350px">';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if(in_array($rs->db_data['ID'],$users2)) echo ' selected ';
		echo '>';
		echo $rs->db_data['Nome'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}
function show_menupath($id_org,$table="pages",$pagina="pagina.php",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT ID,ID_ORG FROM $table WHERE ID=$id_org";

		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["ID"];
		$id_org1=$rs->db_data["ID_ORG"];

		$rs->db_close();
		$sql="SELECT titulo FROM $table"."_lang WHERE ID_PAI=$codigo";

		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["titulo"]);
		$C1=$C!="" ? $C : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" / <a href=\"$pagina"."?ID=$codigo&do=".date('YmdHis')."\" class=\"branco\">$C1</a>";
	}
	return $str;
}

function gera_lista_path($id_org=0,$T=null,$Tlang=null,$tipo='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT * FROM modulos WHERE ID_ORG="0" ORDER BY ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="'.TBID.'_ORG" style="width:350px" class="chzn-select">';
	if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || $id_org==0)
	{
		if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
		else echo '<option value="0">- nenhuma -</option>';
	}
	while(!$rs->EOF())
    {
		$users=explode(';',$rs->db_data["users"]);
		if($rs->db_data["users"]=='' || $rs->db_data["users"]=='0')
		{
			$ID_principal=BLOCOS_PATH_PRINCIPAL_B($rs->db_data["ID_ORG"]);
			$users=GET_BLOCO_USERS($ID_principal);
		}
		if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && in_array($GLOBALS['admin_id'],$users)))
		{
			if($_GET['ID']!=$rs->db_data['ID'])
			{
				$novo_id=$rs->db_data['ID'];
				//Para prevenirmos que uma pagina fique filha dela mesma:
				if($novo_id == $_GET["ID"]) $novo_id=$id_org;
				echo '<option value="'.$novo_id.'"';
				if($id_org==$rs->db_data["ID"]) echo ' selected ';
				$rs2=new DataObj;
				$rs2->db_open($db_link,$DB_name,'Select * from modulos_lang where ID_PAI='.$rs->db_data['ID'].' and lang="'.$GLOBALS['admin_lang'].'"');
				echo '>'.stripslashes($rs2->db_data['titulo']).'</option>';
				$rs2->db_close();
			}
		}
	    gera_lista_path2($rs->db_data['ID'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from modulos where ID_ORG="'.$id_start.'" ORDER BY ordem ASC');
    while(!$rs->EOF())
    {
		$users=explode(';',$rs->db_data["users"]);
		if($rs->db_data["users"]=='' || $rs->db_data["users"]=='0')
		{
			$ID_principal=BLOCOS_PATH_PRINCIPAL_B($rs->db_data["ID_ORG"]);
			$users=GET_BLOCO_USERS($ID_principal);
		}
		if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && in_array($GLOBALS['admin_id'],$users)))
		{
			if($_GET['ID']!=$rs->db_data['ID'])
			{
				echo '<option value="'.$rs->db_data['ID'].'"';
				if($id_org==$rs->db_data['ID']) echo ' selected ';
				$hRes=$rs2->db_open($db_link,$DB_name,'Select * from modulos_lang where ID_PAI="'.$rs->db_data['ID'].'" AND lang="'.$GLOBALS['admin_lang'].'"');
				echo '>';
				$i=0;while($i<$c){echo '- ';$i++;}
				echo stripslashes($rs2->db_data['titulo']).'</option>';
				$rs2->db_close();
			}
		}
	    gera_lista_path2($rs->db_data['ID'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}
function BLOCOS_PATH_PRINCIPAL_B($id_org=0,$on=0)
{
	if($on==1) $id_org=intval($_GET['ID']);
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs1=new DataObj;
	if($id_org!=0)
	{
		$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT ID,ID_ORG,img_ext FROM modulos WHERE ID="'.$id_org.'"');
		$codigo=$rs->db_data["ID"];
		$id_org1=$rs->db_data["ID_ORG"];
		$img_ext=$rs->db_data["img_ext"];
		$rs->db_close();

		$rs1->db_open($db_link,$GLOBALS['DB_name'],'SELECT titulo FROM modulos_lang WHERE ID="'.$codigo.'"');
		$C=stripslashes($rs1->db_data["titulo"]);
		$C1=$C!='' ? $C : '';
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=BLOCOS_PATH_PRINCIPAL_B($id_org1);
		if($id_org1==0) $str.=$codigo;
	}
	return $str;
}
function getBrands($arr_brd)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$result=array();
	$sql='SELECT b.ID,b.img_ext,bo.* FROM brands b INNER JOIN brands_lang bo ON bo.ID_PAI=b.ID ORDER BY b.Ordem ASC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	while(!$rs->EOF())
	{
		if(!in_array($rs->db_data['ID'],$arr_brd))
		{
			$brand_id=$rs->db_data['ID'];
			$brands=get_produtc_brand($brand_id);
			$brands['brand_id']=$brand_id;
			$result[]=$brands;
		}
		$rs->db_next();
	}
	$rs->db_close();
	return $result;
}
function gera_lista_grupos($arr_familias,$arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
    echo '<select id="users" name="users[]" class="box chzn-select" multiple="multiple" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'],$arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}
function gera_lista_brands_cat($arr_familias,$arr_brd,$t=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($t==1)
	{
		echo '<div style="width:100%; display: table; border: 1px solid #000;overflow:auto">';

		//echo '<div style="display:table;table-layout:fixed;width:100%">';

		for($a=0; $a<count($arr_familias); $a++)
		{
			//Como queremos que este modulo seja compativel com as versoes anteriores, é necessário manter o seguinte codigo:
        	//Os proximos tipos de elementos vão ser substituidos por novos
        	$hide=0;
        	if($a==2) $hide=1;
        	if($a==3) $hide=1;
        	if($a==6) $hide=1;
        	if($a==11)$hide=1;
        	if($a==12)$hide=1;
        	if($a==13)$hide=1;
        	if($a==14)$hide=1;
        	if($a==17)$hide=1;
        	if($a==19)$hide=1;
        	if($a==20)$hide=1;
        	if($a==22)$hide=1;
        	//**********************************************
        	if(!$hide)
        	{
				echo '<div style="width:100px;height:100px;border: 1px solid #000;display: table-cell;">';
				echo '<img style="width:100px;height:50px" src="img/'.$a.'.png">';
				echo '<div style="width:100px;height:20px;font-size:8px;text-align:center">'.$arr_familias[$a]['marca_nome'].'</div>';
				echo '<div style="width:100px;height:10px;font-size:8px;text-align:center">';
				echo '<input value="'.$arr_familias[$a]['brand_id'].'" type="radio" name="listademarcas[]"';
				if(in_array($arr_familias[$a]['brand_id'],$arr_brd)) echo ' checked ';
				if($a==0 && !$arr_brd) echo ' checked ';
				echo '>';
				echo '</div>';
				echo '</div>';
			}
		}
		echo '</div>';
		echo '</div>';
	}
	else
	{
	    echo '<select id="listademarcas" name="listademarcas[]" class="box chzn-select" style="font-size:13px;width:100%">';
	    for($a=0; $a<count($arr_familias); $a++)
		{
			echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
			if(in_array($arr_familias[$a]['brand_id'],$arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
			else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
	    }
	    echo '</select>';
    }
}
function gera_lista_brands_list($arr_familias,$arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
    echo '<select id="listademarcasx" name="listademarcasx" class="chzn-select" style="font-size:13px;width:150px">';
	echo '<option value="">TODOS</option>';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'],$arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}
function gera_brands_name($marca_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT bo.Nome FROM brands_lang bo WHERE ID_PAI="'.$marca_id.'"';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF()) $brand_nome=$rs->db_data['Nome'];
	$rs->db_close();
	return $brand_nome;
}
function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$modulo_id=(isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar=(isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao=(isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS=$GLOBALS['CMPS_MODULO_DADOS'];
	if($modulo_acao=='insere')
	{
		$sql='INSERT INTO '.TBMODULO;
		$sql.='(';
		$i=0; foreach($CMPS_MODULO_DADOS as $campos){if($campos != TBID){if($i > 0){ $sql.=',';} $sql.=$campos; $i++;} } // ARRAY DOS CAMPOS
		$sql.=')VALUES(';
		$i=0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if($i>0){$sql.=',';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro=submete_ficheiro($campos,'');
					$sql.='"'.$nome_ficheiro.'"';
				}
				elseif($campos=='categorias')
				{
					$cats=$_POST['listademarcas'];

					$flds='';
					if(is_array($cats) && !empty($cats))
					{
						foreach($cats as $key)
						{
							if($key > 0 && $key!='') $flds.=$key.';';
						}
					}
					$sql.='"'.$flds.'"';
				}
				elseif($campos=='users')
				{
					$cats=$_POST['listagrupos'];
					$flds='';
					if(is_array($cats) && !empty($cats))
					{
						foreach($cats as $key)
						{
							if($key > 0 && $key!='') $flds.=$key.';';
						}
					}
					$sql.='"'.$flds.'"';
				}
				else
				{
					$sql.='"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql.='
				)
				';
	}
	elseif($modulo_acao=='update')
	{
		$sql='UPDATE '.TBMODULO.' SET ';
		$i=0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro=submete_ficheiro($campos,'');
					if($i > 0 && $nome_ficheiro){$sql.=',';}
					if($nome_ficheiro) $sql.=$campos.'="'.$nome_ficheiro.'"';
				}
				elseif($campos=='categorias')
				{
					$cats=$_POST['listademarcas'];
					$flds='';
					if(is_array($cats) && !empty($cats))
					{
						foreach($cats as $key)
						{
							if($key > 0 && $key!='') $flds.=$key.';';
						}

					}
					if($i > 0){$sql.=',';}
					$sql.= $campos.'="'.$flds.'"';
				}
				elseif($campos=='users')
				{
					$cats=$_POST['listagrupos'];

					$flds='';
					if(is_array($cats) && !empty($cats))
					{
						foreach($cats as $key)
						{
							if($key > 0 && $key!='') $flds.=$key.';';
						}

					}
					if($i > 0){$sql.=',';}
					$sql.= $campos.'="'.$flds.'"';
				}
				else
				{
					if($i > 0){$sql.=',';}
					$sql.=$campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql.=' WHERE '.TBID.'='.$_POST[TBID];
	}
	//echo '<br>'.$sql;
	$res=Runquery($db_link,$DB_name,$sql);
	if($modulo_acao=='insere') $id_acao=db_get_last_insert_id($db_link);
	else $id_acao=$modulo_id;
	if($res) submeteDadosLinguas($id_acao);
	return array('resultado' => $res,'acao' => $modulo_acao,'id_acao' => $id_acao);
}
function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$modulo_duplicar=(isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs=new DataObj;

	$rs->db_open($db_link,$DB_name,'SELECT '.TBID.' FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$modulo_id.' AND lang="'.$GLOBALS['admin_lang'].'"');
	if(!$rs->EOF()) $modulo_acao='update'; else $modulo_acao='insere';
	$rs->db_close();

	$CMPS_MODULO_DADOS=$GLOBALS['CMPS_MODULO_DADOS_LANG'];
	if($modulo_acao=='insere')
	{
		$sql='INSERT INTO '.TBMODULO_LANG.' (';
		$i=0;foreach($CMPS_MODULO_DADOS as $campos){if($campos != 'ID'){if($i > 0){$sql.=',';}$sql.=$campos;$i++;}}
		$sql.=')VALUES(';
		$i=0;
		foreach($CMPS_MODULO_DADOS as $campos)
		{
			if($campos != 'ID')
			{
				if($i>0){$sql.=',';}
				if($campos=='ID' || $campos=='ID_PAI')$sql.='"'.$modulo_id.'"';
				elseif($campos=='titulo' && $modulo_duplicar == 1)$sql.='"'.addslashes($_POST[$campos]).'"';
				else $sql.='"'.addslashes($_POST[$campos]).'"';
				$i++;
			}
		}
		$sql.=')';
	}
	elseif($modulo_acao=='update')
	{
		$sql='UPDATE '.TBMODULO_LANG.' SET ';
		$i=0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID && $campos != 'lang' && $campos != 'ID')
			{
				if($campos=='ID' || $campos=='ID_PAI')$sql.=$campos.'="'.$modulo_id.'"';
				else
				{
					if($i > 0){$sql.=',';}
					$sql.=$campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		//$sql.=' WHERE '.TBID.'='.$_POST[TBID].' AND lang="'.$GLOBALS['admin_lang'].'"';
		$sql.=' WHERE ID_PAI='.$modulo_id.' AND lang="'.$GLOBALS['admin_lang'].'"';
	}
	//echo '<br>'.$sql;
	$res=Runquery($db_link,$DB_name,$sql);
}
function strip_html_tags_blocos($string)
{
    $regExp="<"."p"."[^>]*>";
    $string=str_replace("</p>",'\n',$string);
    $string=preg_replace ("( ".$regExp." )","<p>",$string);
    $regExp="<"."P"."[^>]*>";
    $string=str_replace("</P>",'\n',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."div"."[^>]*>";
    $string=str_replace("</div>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."DIV"."[^>]*>";
    $string=str_replace("</DIV>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."span"."[^>]*>";
    $string=str_replace("</span>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."SPAN"."[^>]*>";
    $string=str_replace("</SPAN>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."font"."[^>]*>";
    $string=str_replace("</font>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."FONT"."[^>]*>";
    $string=str_replace("</FONT>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."table"."[^>]*>";
    $string=str_replace("</table>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."TABLE"."[^>]*>";
    $string=str_replace("</TABLE>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."tbody"."[^>]*>";
    $string=str_replace("</tbody>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."TBODY"."[^>]*>";
    $string=str_replace("</TBODY>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."COL"."[^>]*>";
    $string=str_replace("</COL>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."col"."[^>]*>";
    $string=str_replace("</col>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."colgroup"."[^>]*>";
    $string=str_replace("</colgroup>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."COLGROUP"."[^>]*>";
    $string=str_replace("</COLGROUP>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."td"."[^>]*>";
    $string=str_replace("</td>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."TD"."[^>]*>";
    $string=str_replace("</TD>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."tr"."[^>]*>";
    $string=str_replace("</tr>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."TR"."[^>]*>";
    $string=str_replace("</TR>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."STRONG"."[^>]*>";
    $string=str_replace("</STRONG>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."strong"."[^>]*>";
    $string=str_replace("</strong>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."h1"."[^>]*>";
    $string=str_replace("</h1>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."H1"."[^>]*>";
    $string=str_replace("</H1>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."h2"."[^>]*>";
    $string=str_replace("</h2>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."H2"."[^>]*>";
    $string=str_replace("</H2>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."h3"."[^>]*>";
    $string=str_replace("</h3>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."H3"."[^>]*>";
    $string=str_replace("</H3>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."li"."[^>]*>";
    $string=str_replace("</li>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."LI"."[^>]*>";
    $string=str_replace("</LI>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."ul"."[^>]*>";
    $string=str_replace("</ul>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."UL"."[^>]*>";
    $string=str_replace("</UL>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."ol"."[^>]*>";
    $string=str_replace("</ol>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."OL"."[^>]*>";
    $string=str_replace("</OL>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."img"."[^>]*>";
    $string=str_replace("</img>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."IMG"."[^>]*>";
    $string=str_replace("</IMG>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."obj"."[^>]*>";
    $string=str_replace("</obj>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $regExp="<"."OBJ"."[^>]*>";
    $string=str_replace("</OBJ>",'',$string);
    $string=preg_replace("(".$regExp." )",'',$string);
    $string=str_replace("<o:p>",'',$string);
    $string=str_replace("</o:p>",'',$string);
    $string=str_replace('<?xml:namespace prefix=o ns="urn:schemas-microsoft-com:office:office" />','',$string);
    $string=str_replace('<P><SPAN>&nbsp;</SPAN></P>','',$string);
    $string=str_replace('<SPAN>&nbsp;</SPAN>','',$string);
    $string=str_replace('<SPAN></SPAN>','',$string);
    $string=str_replace('</P>','\n',$string);
    $string=str_replace('<P>',"\n",$string);
    $string=str_replace('</p>','\n',$string);
    $string=str_replace('<p>',"\n",$string);
    $string=str_replace('<FONT>','',$string);
    $string=str_replace('</FONT>','',$string);
    $string=str_replace('<STRONG>','',$string);
    $string=str_replace('</STRONG>','',$string);
    $string=str_replace('<strong>','',$string);
    $string=str_replace('</strong>','',$string);
    $string=str_replace('<B>','',$string);
    $string=str_replace('</B>','',$string);
    $string=str_replace('<b>','',$string);
    $string=str_replace('</b>','',$string);
    $string=str_replace('<div>','',$string);
    $string=str_replace('</div>','',$string);
    $string=str_replace('<DIV>','',$string);
    $string=str_replace('</DIV>','',$string);
    $string=str_replace("'",'',$string);
    $string=str_replace("<br>",'\n',$string);
    $string=str_replace("<BR>",'\n',$string);
    $string=str_replace("\r",'',$string);
    $string=str_replace("/"," ",$string);
    $string=str_replace('&nbsp;'," ",$string);
    $string=str_replace("<div>&nbsp;</div>"," ",$string);
    $string=removeUnsafeAttributesAndGivenTags($string);
	return $string;
}

function submeteimagens($modulo_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	// if($modulo_duplicar == 0)
	// {
		$CMPS_MODULO=$GLOBALS['CMPS_MODULO_IMGS'];
		$query='';
		foreach($_POST['MODULO_IMG_ID'] as $field_num2) // Para cada imagem submetida
		{
			$codis=explode(';',$field_num2);
			$bloco_img_ID_ORG=$codis[0];
			$bloco_img_id=$codis[1];
			if($modulo_duplicar == 1) $modulo_acao='insere'; else $modulo_acao=($bloco_img_id > 0) ? 'update' : 'insere';

			if($modulo_acao=='insere')
			{
				$sql='INSERT INTO '.TBIMAGENS;
				$sql.='(';
				$i=0; foreach($CMPS_MODULO as $campos){if($campos != TBIMGID){if($i>0){$sql.=',';} $sql.=$campos; $i++;} } // ARRAY DOS CAMPOS
				$sql.=')VALUES(';
				$i=0;

				foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{
						if($i>0){$sql.=',';}
						if(substr_count($campos.$bloco_img_id,'_ext'))$sql.='"'.submete_ficheiro($campos.$bloco_img_id,'').'"';
						elseif(substr_count($campos,'bloco_img_ID_ORG'))$sql.='"'.$modulo_id.'"';
						else $sql.='"'.addslashes($_POST[$campos.$bloco_img_id]).'"';
						$i++;
					}
				}
				$sql.=')';
			}
			elseif($modulo_acao=='update')
			{
				if($_POST['Apagar_img'.$bloco_img_id]==1 || $_POST['Apagar_elemento'.$bloco_img_id]==1) // Apagar imagem
				{
					apaga_ficheiro($bloco_img_id); // Apaga imagem
					if(isset($_POST['Apagar_elemento'.$bloco_img_id]) && $_POST['Apagar_elemento'.$bloco_img_id]==1) $sql='DELETE FROM  '.TBIMAGENS.' WHERE '.TBIMGID.'='.$bloco_img_id;
				}
				elseif($_POST['Apagar_img_capa'.$bloco_img_id]==1 || $_POST['Apagar_elemento'.$bloco_img_id]==1) // Apagar imagem
				{
					apaga_capa($bloco_img_id); // Apaga imagem
					if(isset($_POST['Apagar_elemento'.$bloco_img_id]) && $_POST['Apagar_elemento'.$bloco_img_id]==1) $sql='DELETE FROM  '.TBIMAGENS.' WHERE '.TBIMGID.'='.$bloco_img_id;
					// if(isset($_POST['Apagar_elemento'.$bloco_img_id]) && $_POST['Apagar_elemento'.$bloco_img_id]==1) $sql='DELETE FROM  '.TBIMAGENS.' WHERE '.TBIMGID.'='.$bloco_img_id;
				}
				else
				{
					$sql='UPDATE '.TBIMAGENS.' SET ';
					$i=0;
					foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS
					{
						if($campos != TBIMGID)
						{
							if(substr_count($campos,'_ext'))
							{
								//Vamos validar se é para remover o fx ou para alterar
								$nome_ficheiro=submete_ficheiro($campos.$bloco_img_id,'');
								if($i>0 && $nome_ficheiro){$sql.=',';}
								if($nome_ficheiro) $sql.=$campos.'="'.$nome_ficheiro.'"';
							}
							elseif(substr_count($campos,'bloco_img_ID_ORG'))
							{
								if($i>0){$sql.=',';}
								$sql.=$campos.'="'.$modulo_id.'"';
							}
							else
							{
								if($i>0){$sql.=',';}
								$sql.=$campos.'="'.addslashes($_POST[$campos.$bloco_img_id]).'"';
							}
							$i++;
						}
					}
					$sql.=' WHERE '.TBIMGID.'='.$bloco_img_id;
				}
			}
			if($sql)
			{
				$res=Runquery($db_link,$DB_name,$sql);
				if($modulo_acao=='insere') $id_acao=db_get_last_insert_id($db_link);
				else $id_acao=$bloco_img_id;
				if($res) submeteimagensLinguas($id_acao,$bloco_img_id);
			}
		}
		//Agora vamos ver se existe algum novo elemento para inserir:
		$bloco_img_id='';

		$sql='INSERT INTO '.TBIMAGENS;
		$sql.='(';
		$i=0; foreach($CMPS_MODULO as $campos){if($campos != TBIMGID){if($i>0){$sql.=',';} $sql.=$campos; $i++;} } // ARRAY DOS CAMPOS
		$sql.=')VALUES(';
		$i=0;

		$novo_campo=0;
		if($_POST['bloco_img_titulo'] || $_POST['bloco_img_txt'] || $_FILES['bloco_img_ext']['tmp_name'] )$novo_campo=1;


		foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBIMGID)
			{
				if($i>0){$sql.=',';}
				if(substr_count($campos.$bloco_img_id,'_ext'))$sql.='"'.submete_ficheiro($campos.$bloco_img_id,'').'"';
				elseif(substr_count($campos,'bloco_img_ID_ORG'))$sql.='"'.$modulo_id.'"';
				else $sql.='"'.addslashes($_POST[$campos.$bloco_img_id]).'"';
				$i++;
			}
		}
		$sql.=')';
		if($novo_campo==1)
		{
			$res=Runquery($db_link,$DB_name,$sql);
			$id_acao=db_get_last_insert_id($db_link);
			if($id_acao) submeteimagensLinguas($id_acao,$bloco_img_id);
		}
	// }
}
function submeteimagensLinguas($modulo_id,$bloco_img_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$modulo_duplicar=(isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs=new DataObj;

	$rs->db_open($db_link,$DB_name,'SELECT bloco_img_lang_id FROM '.TBIMAGENS_LANG.' WHERE bloco_img_ID_PAI='.$modulo_id.' AND lang="'.$GLOBALS['admin_lang'].'"');
	if(!$rs->EOF()) $modulo_acao='update'; else $modulo_acao='insere';
	$rs->db_close();

	$CMPS_MODULO_IMGS_LANG=$GLOBALS['CMPS_MODULO_IMGS_LANG'];
	if($modulo_acao=='insere')
	{
		$sql='INSERT INTO '.TBIMAGENS_LANG.' (';
		$i=0;foreach($CMPS_MODULO_IMGS_LANG as $campos){if($campos != 'bloco_img_lang_id'){if($i > 0){$sql.=',';}$sql.=$campos;$i++;}}
		$sql.=')VALUES(';
		$i=0;
		foreach($CMPS_MODULO_IMGS_LANG as $campos)
		{
			if($campos != 'bloco_img_lang_id')
			{
				if($i>0){$sql.=',';}
				if($campos=='bloco_img_ID_PAI')$sql.='"'.$modulo_id.'"';
				elseif($campos=='lang')$sql.='"'.$GLOBALS['admin_lang'].'"';
				else $sql.='"'.addslashes($_POST[$campos.$bloco_img_id]).'"';
				$i++;
			}
		}
		$sql.=')';
	}
	elseif($modulo_acao=='update')
	{
		$sql='UPDATE '.TBIMAGENS_LANG.' SET ';
		$i=0;
		foreach($CMPS_MODULO_IMGS_LANG as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != 'lang' && $campos != 'bloco_img_lang_id' && $campos != 'bloco_img_ID_PAI')
			{
				if($i > 0){$sql.=',';}
				$sql.=$campos.'="'.addslashes($_POST[$campos.$bloco_img_id]).'"';
				$i++;
			}
		}
		$sql.='WHERE bloco_img_ID_PAI='.$modulo_id.' AND lang="'.$GLOBALS['admin_lang'].'"';
	}


	$res=Runquery($db_link,$DB_name,$sql);
}
function apaga_ficheiro($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT * FROM '.TBIMAGENS.' WHERE bloco_img_ID="'.$ID.'" ORDER BY bloco_img_ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$bloco_img_ext=$rs->db_data['bloco_img_ext'];
		$img_path='../../files/documentos/';
		if(file_exists($img_path.$bloco_img_ext))unlink($img_path.$bloco_img_ext);
	}
	$rs->db_close();
}
function apaga_capa($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT * FROM '.TBIMAGENS.' WHERE bloco_img_ID="'.$ID.'" ORDER BY bloco_img_ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$bloco_pdf_ext=$rs->db_data['bloco_pdf_ext'];
		$img_path='../../files/documentos/';
		if(file_exists($img_path.$bloco_pdf_ext))unlink($img_path.$bloco_pdf_ext);
	}
	$rs->db_close();
}
function submete_ficheiro($file_name,$nome_ficheiro_old)
{
	$img_path='../../files/documentos/';

	$campo_Apagar_img = str_replace('bloco_img_ext','Apagar_img',$file_name); $campo_Apagar_img = str_replace('bloco_img2_ext','Apagar_img',$campo_Apagar_img);
	$campo_Apagar_elemento = str_replace('bloco_img_ext','Apagar_elemento',$file_name);

	if($_POST[$campo_Apagar_img]==1 || $_POST[$campo_Apagar_elemento]==1 )
	{
		$nome_ficheiro='-1';
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))unlink($img_path.$nome_ficheiro_old);
	}
	else
	{
		$Img_peq["name"]=$_FILES[$file_name]['name'];
		$Img_peq["temp"]=$_FILES[$file_name]['tmp_name'];
		$nome_ficheiro='';
		$nome_ficheiro=$nome_ficheiro_old;
		if($Img_peq["name"]!="")
		{
			$pos=strrpos($Img_peq['name'],".");
			$img_ext=substr($Img_peq['name'],$pos+1,strlen($Img_peq['name'])+1-$pos);
			if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
			{
				unlink($img_path.$nome_ficheiro_old);
			}
			$token=date('YmdHis').bin2hex(uniqid());
			$nome_ficheiro=$token.".".$img_ext;
			echo copia_segura($Img_peq["temp"],$img_path.$nome_ficheiro);
		}
	}
	return $nome_ficheiro;
}
function submete_capa($file_name,$nome_ficheiro_old)
{
	$img_path='../../files/documentos/';
	$campo_Apagar_img = str_replace('bloco_img_capa_ext','Apagar_img_capa',$file_name);
	$campo_Apagar_elemento = str_replace('bloco_img_ext','Apagar_elemento',$file_name);

	if($_POST[$campo_Apagar_img]==1 || $_POST[$campo_Apagar_elemento]==1 )
	{
		$nome_ficheiro='-1';
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))unlink($img_path.$nome_ficheiro_old);
	}
	else
	{
		$Img_peq["name"]=$_FILES[$file_name]['name'];
		$Img_peq["temp"]=$_FILES[$file_name]['tmp_name'];
		$nome_ficheiro='';
		$nome_ficheiro=$nome_ficheiro_old;
		if($Img_peq["name"]!="")
		{
			$pos=strrpos($Img_peq['name'],".");
			$img_ext=substr($Img_peq['name'],$pos+1,strlen($Img_peq['name'])+1-$pos);
			if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
			{
				unlink($img_path.$nome_ficheiro_old);
			}
			$token=date('YmdHis').bin2hex(uniqid());
			$nome_ficheiro=$token.".".$img_ext;
			echo copia_segura($Img_peq["temp"],$img_path.$nome_ficheiro);
		}
	}
	return $nome_ficheiro;
}


/*
function submeteimagens($modulo_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	// if($modulo_duplicar == 0)
	// {
		$CMPS_MODULO=$GLOBALS['CMPS_MODULO_IMGS'];
		$query='';
		foreach($_POST['MODULO_IMG_ID'] as $field_num2) // Para cada imagem submetida
		{
			$codis=explode(';',$field_num2);
			$bloco_img_ID_ORG=$codis[0];
			$bloco_img_id=$codis[1];
			if($modulo_duplicar == 1) $modulo_acao='insere'; else $modulo_acao=($bloco_img_id > 0) ? 'update' : 'insere';

			if($modulo_acao=='insere')
			{
				$sql='INSERT INTO '.TBIMAGENS;
				$sql.='(';
				$i=0; foreach($CMPS_MODULO as $campos){if($campos != TBIMGID){if($i>0){$sql.=',';} $sql.=$campos; $i++;} } // ARRAY DOS CAMPOS
				$sql.=')VALUES(';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{
						if($i>0){$sql.=',';}
						if(substr_count($campos.$bloco_img_id,'_ext'))$sql.='"'.submete_ficheiro($campos.$bloco_img_id,'').'"';
						elseif(substr_count($campos,'bloco_img_ID_ORG'))$sql.='"'.$modulo_id.'"';
						else $sql.='"'.addslashes($_POST[$campos.$bloco_img_id]).'"';
						$i++;
					}
				}
				$sql.=')';
			}
			elseif($modulo_acao=='update')
			{
				if($_POST['Apagar_img'.$bloco_img_id]==1 || $_POST['Apagar_elemento'.$bloco_img_id]==1) // Apagar imagem
				{
					apaga_ficheiro($bloco_img_id); // Apaga imagem
					if(isset($_POST['Apagar_elemento'.$bloco_img_id]) && $_POST['Apagar_elemento'.$bloco_img_id]==1) $sql='DELETE FROM  '.TBIMAGENS.' WHERE '.TBIMGID.'='.$bloco_img_id;
					// if(isset($_POST['Apagar_elemento'.$bloco_img_id]) && $_POST['Apagar_elemento'.$bloco_img_id]==1) $sql='DELETE FROM  '.TBIMAGENS.' WHERE '.TBIMGID.'='.$bloco_img_id;
				}
				else
				{
					$sql='UPDATE '.TBIMAGENS.' SET ';
					$i=0;
					foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS
					{
						if($campos != TBIMGID)
						{
							if(substr_count($campos,'_ext'))
							{
								//Vamos validar se é para remover o fx ou para alterar
								$nome_ficheiro=submete_ficheiro($campos.$bloco_img_id,'');
								if($i>0 && $nome_ficheiro){$sql.=',';}
								if($nome_ficheiro) $sql.=$campos.'="'.$nome_ficheiro.'"';
							}
							elseif(substr_count($campos,'bloco_img_ID_ORG'))
							{
								if($i>0){$sql.=',';}
								$sql.=$campos.'="'.$modulo_id.'"';
							}
							else
							{
								if($i>0){$sql.=',';}
								$sql.=$campos.'="'.addslashes($_POST[$campos.$bloco_img_id]).'"';
							}
							$i++;
						}
					}
					$sql.=' WHERE '.TBIMGID.'='.$bloco_img_id;
				}
			}
			if($sql)
			{
				$res=Runquery($db_link,$DB_name,$sql);
				if($modulo_acao=='insere') $id_acao=db_get_last_insert_id($db_link);
				else $id_acao=$bloco_img_id;
				if($res) submeteimagensLinguas($id_acao,$bloco_img_id);
			}
		}
		//Agora vamos ver se existe algum novo elemento para inserir:
		$bloco_img_id='';

		$sql='INSERT INTO '.TBIMAGENS;
		$sql.='(';
		$i=0; foreach($CMPS_MODULO as $campos){if($campos != TBIMGID){if($i>0){$sql.=',';} $sql.=$campos; $i++;} } // ARRAY DOS CAMPOS
		$sql.=')VALUES(';
		$i=0;

		$novo_campo=0;
		if($_POST['bloco_img_titulo'] || $_POST['bloco_img_txt'] || $_FILES['bloco_img_ext']['tmp_name'] )$novo_campo=1;


		foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBIMGID)
			{
				if($i>0){$sql.=',';}
				if(substr_count($campos.$bloco_img_id,'_ext'))$sql.='"'.submete_ficheiro($campos.$bloco_img_id,'').'"';
				elseif(substr_count($campos,'bloco_img_ID_ORG'))$sql.='"'.$modulo_id.'"';
				else $sql.='"'.addslashes($_POST[$campos.$bloco_img_id]).'"';
				$i++;
			}
		}
		$sql.=')';
		if($novo_campo==1)
		{
			$res=Runquery($db_link,$DB_name,$sql);
			$id_acao=db_get_last_insert_id($db_link);
			if($id_acao) submeteimagensLinguas($id_acao,$bloco_img_id);
		}
	// }
}
function submeteimagensLinguas($modulo_id,$bloco_img_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$modulo_duplicar=(isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs=new DataObj;

	$rs->db_open($db_link,$DB_name,'SELECT bloco_img_lang_id FROM '.TBIMAGENS_LANG.' WHERE bloco_img_ID_PAI='.$modulo_id.' AND lang="'.$GLOBALS['admin_lang'].'"');
	if(!$rs->EOF()) $modulo_acao='update'; else $modulo_acao='insere';
	$rs->db_close();

	$CMPS_MODULO_IMGS_LANG=$GLOBALS['CMPS_MODULO_IMGS_LANG'];
	if($modulo_acao=='insere')
	{
		$sql='INSERT INTO '.TBIMAGENS_LANG.' (';
		$i=0;foreach($CMPS_MODULO_IMGS_LANG as $campos){if($campos != 'bloco_img_lang_id'){if($i > 0){$sql.=',';}$sql.=$campos;$i++;}}
		$sql.=')VALUES(';
		$i=0;
		foreach($CMPS_MODULO_IMGS_LANG as $campos)
		{
			if($campos != 'bloco_img_lang_id')
			{
				if($i>0){$sql.=',';}
				if($campos=='bloco_img_ID_PAI')$sql.='"'.$modulo_id.'"';
				elseif($campos=='lang')$sql.='"'.$GLOBALS['admin_lang'].'"';
				else $sql.='"'.addslashes($_POST[$campos.$bloco_img_id]).'"';
				$i++;
			}
		}
		$sql.=')';
	}
	elseif($modulo_acao=='update')
	{
		$sql='UPDATE '.TBIMAGENS_LANG.' SET ';
		$i=0;
		foreach($CMPS_MODULO_IMGS_LANG as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != 'lang' && $campos != 'bloco_img_lang_id' && $campos != 'bloco_img_ID_PAI')
			{
				if($i > 0){$sql.=',';}
				$sql.=$campos.'="'.addslashes($_POST[$campos.$bloco_img_id]).'"';
				$i++;
			}
		}
		$sql.='WHERE bloco_img_ID_PAI='.$modulo_id.' AND lang="'.$GLOBALS['admin_lang'].'"';
	}

	$res=Runquery($db_link,$DB_name,$sql);
}
function apaga_ficheiro($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT * FROM '.TBIMAGENS.' WHERE bloco_img_ID="'.$ID.'" ORDER BY bloco_img_ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$bloco_img_ext=$rs->db_data['bloco_img_ext'];
		$img_path='../../files/documentos/';
		if(file_exists($img_path.$bloco_img_ext))unlink($img_path.$bloco_img_ext);
	}
	$rs->db_close();
}
function submete_ficheiro($file_name,$nome_ficheiro_old)
{
	$img_path='../../files/documentos/';
	$campo_Apagar_img = str_replace('bloco_img_ext','Apagar_img',$file_name);
	$campo_Apagar_elemento = str_replace('bloco_img_ext','Apagar_elemento',$file_name);

	if($_POST[$campo_Apagar_img]==1 || $_POST[$campo_Apagar_elemento]==1 )
	{
		$nome_ficheiro='-1';
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))unlink($img_path.$nome_ficheiro_old);
	}
	else
	{
		$Img_peq["name"]=$_FILES[$file_name]['name'];
		$Img_peq["temp"]=$_FILES[$file_name]['tmp_name'];
		$nome_ficheiro='';
		$nome_ficheiro=$nome_ficheiro_old;
		if($Img_peq["name"]!="")
		{
			$pos=strrpos($Img_peq['name'],".");
			$img_ext=substr($Img_peq['name'],$pos+1,strlen($Img_peq['name'])+1-$pos);
			if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
			{
				unlink($img_path.$nome_ficheiro_old);
			}
			$token=date('YmdHis').bin2hex(uniqid());
			$nome_ficheiro=$token.".".$img_ext;
			echo copia_segura($Img_peq["temp"],$img_path.$nome_ficheiro);
		}
	}
	return $nome_ficheiro;
}

*/
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T,$ID_ORG;
	?>
	<div class="clearboth"></div><br>

    <table class="p1" width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td class="t1" valign="middle" align="center"><?

				echo '<input class="btn btn-primary" type="submit" name="Submit" value=" '.$task_button.' ">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value=" <? echo write_lang('key_cancelar','Cancelar')?> " onMouseUp="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-primary" type="button" name="Apagar" value=" <? echo write_lang('key_apagar','Apagar')?> " onMouseUp="window.open('pagina_trata.php?ID=<? echo $ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value=" <? echo write_lang('key_cancelar','Cancelar')?> " onMouseUp="window.open('list.php','_self')" />
		</td>
		<td width="5px"></td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{
	global $ID,$admin_id,$BOTOES_L,$limitado;
    if(!$limitado)
    {?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr><td class="titulo2" align="center" height="20">FERRAMENTAS</td></tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		$a=1;
		foreach($BOTOES_L as $key => $value)
		{
			?><tr><td class="preto" align="center" height="30"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo $key?>" onclick='<?php if($value['confirm'] != ''){?>if(confirm("<?php echo $value['confirm']?>")){window.open("<?php echo $value['url']; ?>","<?php echo $value['target']; ?>");}<?php }else{ ?>window.open("<?php echo $value['url'];?>", "<?php echo $value['target']; ?>");<?php }?>' /></td></tr><?
			$a++;
		}
	}
	?>
	<tr><td class="preto" align="center" height="40"><input class="btn btn-primary" type="button" style="width:160px" value="Remover Ficheiros" onclick="if(confirm('Tem a certeza que pretende apagar todos os ficheiros que não estão em uso?')){window.open('removerimagens.php','_self')}" /></td></tr>
	<?php if(isset($_GET['ID'])){?><tr><td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $GLOBALS['task_button'];?> " onclick="$('#mod_form').submit();"></td></tr><?php }?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px"><?
	}
}
function show_page_tools($filter=0)
{
	global $tipos;?>
	<script>function checkFields(){return true;}</script>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">COMPONENTES</h4>
        </div>
    </div>
    <?
}
function show_page_bottom_tools()
{?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" class="p1" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="150" height="35" align="right" class="t1">Para os Selecionados:&nbsp;</td>
	<td height="35" width="275">
		<select name="ACCOES" id="ACCOES" class="select1">
			<option value="">-</option>
			<? if(isset($GLOBALS['BOTOES_B']) && !empty($GLOBALS['BOTOES_B'])){foreach($GLOBALS['BOTOES_B'] as $value => $key)echo '<option value="'.$key.'">'.$value.'</option>';}?>
		</select>
	</td>
	<td height="35" align="left" style="font-weight:bold">&nbsp;<input type="button" class="btn btn-primary" onclick="$('#lista_paginas').submit();" value=" APLICAR "></td>
</tr>
</table>
<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function acoes_selecionados()
{
	if(isset($_POST['ACCOES']))
	{
		$Accao=$_POST['ACCOES'];
		if($Accao)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			if($Accao==1) // REORDENAR REGISTO
			{
				foreach($_POST as $varName=> $value)
				{
					$str_id=substr($varName,strpos($varName,"_") + 1);
					if($str_id > 0 && substr_count($varName,"chk_") > 0)
					{
						$var='field_'.$str_id;
						$dv=$_POST[$var];
						$res=Runquery($db_link,$DB_name,'UPDATE '.TBMODULO.' SET ordem='.$dv.' WHERE '.TBID.'='.$str_id);
						if(!$res) echo db_error($db_link).$sql.'<br>';
					}
				}
			}
			elseif($Accao==2) // DESATIVAR REGISTOS
			{
				foreach($_POST as $varName=> $value)
				{
					$str_id=substr($varName,strpos($varName,"_") + 1);
					if($str_id > 0 && substr_count($varName,"chk_") > 0)
					{
						$res=Runquery($db_link,$DB_name,'UPDATE '.TBMODULO.' SET visivel=0 WHERE '.TBID.'='.$str_id);
						if(!$res) echo db_error($db_link).$sql.'<br>';
					}
				}
			}
			elseif($Accao==3) // ATIVAR REGISTOS
			{
				foreach($_POST as $varName=> $value)
				{
					$str_id=substr($varName,strpos($varName,"_") + 1);
					if($str_id > 0 && substr_count($varName,"chk_") > 0)
					{
						$res=Runquery($db_link,$DB_name,'UPDATE '.TBMODULO.' SET visivel=1 WHERE '.TBID.'='.$str_id);
						if(!$res) echo db_error($db_link).$sql.'<br>';
					}
				}
			}
			elseif($Accao==4) // REMOVER REGISTOs
			{
				foreach($_POST as $varName=> $value)
				{
					$str_id=substr($varName,strpos($varName,"_") + 1);
					if($str_id > 0 && substr_count($varName,"chk_") > 0)
					{
						$res=Runquery($db_link,$DB_name,'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id);
						$res=Runquery($db_link,$DB_name,'DELETE FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$str_id);
						$res=Runquery($db_link,$DB_name,'DELETE FROM '.TBIMAGENS.' WHERE bloco_img_ID_PAI='.$str_id);
					}
				}
			}
		}
	}
	else
	{
		if(isset($_GET['ACCOES']))
		{
			$Accao=$_GET['ACCOES'];
			if($Accao)
			{
				if($Accao==4) // REMOVER REGISTO
				{
					$str_id=$_GET['ID'];
					if($str_id > 0)
					{
						global $DB_host,$DB_user,$DB_pass,$DB_name;
						$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
						$res=Runquery($db_link,$DB_name,'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id);
						$res=Runquery($db_link,$DB_name,'DELETE FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$str_id);
						$res=Runquery($db_link,$DB_name,'DELETE FROM '.TBIMAGENS.' WHERE bloco_img_ID_PAI='.$str_id);
					}
				}
			}
		}
	}
}
function GET_BLOCO_USERS($ID=0)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$return='';
	$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT bl.users FROM modulos bl WHERE bl.ID="'.$ID.'"');
	if(!$rs->EOF())$return=explode(';',$rs->db_data['users']);
	$rs->db_close(1);
	return $return;
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$pesq='';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq.=' AND bl.titulo like "%'.$_GET['DESIGNACAO'].'%"';
		if(isset($_GET['DATA_PEDIDO']) && $_GET['DATA_PEDIDO']!='' && $_GET['DATA_PEDIDO']!='0000-00-00') $pesq.=' AND b.data like "%'.$_GET['DATA_PEDIDO'].'%"';
		if(isset($_GET['visivel']) && $_GET['visivel']!='') $pesq.=' AND b.visivel="'.$_GET['visivel'].'"';
		if(isset($_GET['ATIVIDADE']) && $_GET['ATIVIDADE']!='') $pesq.=' AND b.categorias="'.$_GET['ATIVIDADE'].';"';
	}
	$sql='SELECT b.'.TBID.',b.users,b.ID_ORG,b.ordem,b.visivel,b.data,b.categorias,bl.titulo FROM '.TBMODULO.' b INNER JOIN '.TBMODULO_LANG.' bl on bl.ID_PAI=b.'.TBID.'
			WHERE bl.Lang="'.$admin_lang.'" AND ID_ORG='.$pai.' '.$pesq.'
			ORDER BY b.ordem ASC,b.categorias ASC,b.'.TBID.' ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	$total=$rs->db_rows;
	$rs->db_close();
	$n_pags=ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina=1;
	else $pagina=$_GET['pagina'];
	$inicio=$pagina - 1;
	$inicio=$inicio * N_PAGINAS;
	$sql2=$sql.' Limit '.$inicio.','.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>
		<table width="100%" border=0 cellspacing=0 cellpadding=0 class="titulo_janela">
		<tr>
			<td width="20" height="35" align="center">&nbsp;</td>
			<td width="50" valign="middle" align="left"><b>ID</b></td>
			<td valign="middle" align="left"><b>Designação</b></td>
			<td width="100" valign="middle" align="left"><b>Tipo</b></td>
			<td width="100" valign="middle" align="left"><b>Data</b></td>
			<td width="100" valign="middle"><b>Estado</b></td>
			<td width="10">&nbsp;</td>
			<td width="10">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<? if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && substr_count($_SERVER['SCRIPT_NAME'],'pagina.php'))){?>
		<tr bgcolor="#161716" height="45px">
			<td width="35px" height="35" align="center">
			<?
				if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['permissoes'] > 0 && $GLOBALS['dados']['Grupo_ID']==5 && substr_count($_SERVER['SCRIPT_FILENAME'],'pagina.php')))
				{
				?><label class="label_check2" for="chk_all" style="margin: 0 auto;padding:0;display:inline-block"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label><?
				}?>
			</td>
			<td valign="middle" width="50" align="left"></td>
			<td valign="middle" align="left"><input type="text" style="width:96%" name="DESIGNACAO" id="DESIGNACAO" class="form-control" value="<? if(isset($_GET['DESIGNACAO'])) echo $_GET['DESIGNACAO']?>" /></td>
			<td valign="middle" width="300" align="left"><? gera_lista_brands_cat($GLOBALS['arr_familias'], $GLOBALS['categorias']);?></td>
			<td valign="middle" width="100" align="center"><input type="date" type="text" style="width:80px !important" name="DATA_PEDIDO" id="DATA_PEDIDO" class="form-control" value="<?php if(isset($_GET['DATA_PEDIDO'])) echo $_GET['DATA_PEDIDO']?>" /></td>
			<td width="50" valign="middle" align="center"><select style="width:80px;font-size:11px;padding-top:4px" name="visivel" id="visivel" class="form-control"><option value="" <? if(!isset($_GET['visivel']) && !$_GET['visivel']) echo 'selected="selected"';?>>TODOS</option><option value="1" <? if(isset($_GET['visivel']) && $_GET['visivel']=='1') echo 'selected="selected"';?>>Visivel</option><option value="2" <? if(isset($_GET['visivel']) && $_GET['visivel']=='2') echo 'selected="selected"';?>>Invisivel</option></select></td>
			<td valign="middle" align="left" colspan="2"><img src="img/search.png" title="Pesquisar" border="0" onclick="pesquisa('<? echo ((isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'pagina.php?'.TBID.'='.$_GET[TBID] : '')?>'); return false;" style="cursor:pointer" /></td>
			<td></td>
		</tr>
		<?
		}
		while(!$rs->EOF())
		{
			$count++;
			$IDb=$rs->db_data[TBID];
			$id_org=$rs->db_data['ID_ORG'];
			$visivel=$rs->db_data["visivel"];
			$visivel_img=(isset($rs->db_data["visivel"]) && $rs->db_data["visivel"]==1) ? 'ok.png' : 'hidden.png';
			$titulo=$rs->db_data["titulo"];
			$ordem=$rs->db_data["ordem"];
			$data=$rs->db_data["data"];
			$categorias=rtrim(ltrim($rs->db_data["categorias"],';'),';');

			$users=explode(';',$rs->db_data["users"]);
			if($rs->db_data["users"]=='' || $rs->db_data["users"]=='0')
			{
				$ID_principal=BLOCOS_PATH_PRINCIPAL_B($id_org);
				$users=GET_BLOCO_USERS($ID_principal);
			}
			if($visivel==1)
						{
				if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#FFFFFF";$class="r2";$n="2";}
			}
			else
			{
				if($count % 2 != 0)	{$cor="#dbdada";$class="r1";$n="1";}
				else				{$cor="#e8e8e8";$class="r2";$n="2";}
			}
			if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && in_array($GLOBALS['admin_id'],$users)))
			{
				?>
				<tr bgcolor="<? echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<? echo $cor;?>'">
				<td align="center"><?
				if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['permissoes'] > 0 && $GLOBALS['dados']['Grupo_ID']==5 && substr_count($_SERVER['SCRIPT_FILENAME'],'pagina.php')))
				{
				?><label class="label_check2" for="chk_<? echo $IDb?>" style="margin: 0 auto;padding:0;display:inline-block"><input type="checkbox" name="chk_<? echo $IDb?>" id="chk_<? echo $IDb?>" value="<?php echo $IDb?>" class="cb" /></label>
				<?php }?>
				</td>
				<td valign="middle" align="left"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<? echo TBID?>=<? echo $IDb;?>&Dump=<? echo uniqid("");?>"><?=$IDb?></a></td>
				<td valign="middle" align="left"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<? echo TBID?>=<? echo $IDb;?>&Dump=<? echo uniqid("");?>"><? if($titulo){echo htmlentities_b(stripslashes($titulo));} else echo "[Editar Página]";?></a></td>
				<td valign="middle" align="left"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<? echo TBID?>=<? echo $IDb;?>&Dump=<? echo uniqid("");?>"><? echo $GLOBALS['arr_familias'][$categorias-1]['marca_nome'];?></a></td>
				<td valign="middle" align="center"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<? echo TBID?>=<? echo $IDb;?>&Dump=<? echo uniqid("");?>"><? echo $data;?></a></td>


				<td valign="middle" align="center"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<? echo TBID?>=<? echo $IDb;?>&Dump=<? echo uniqid("");?>"><img src="img/<? echo $visivel_img?>"/></a></td>

				<?
				if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['permissoes'] > 0 && $GLOBALS['dados']['Grupo_ID']==5 && substr_count($_SERVER['SCRIPT_FILENAME'],'pagina.php')))
				{
				?>
				<td align="center" width="30px" ><a title="Duplicar" class="lista_links" href="pagina.php?<? echo TBID?>=<? echo $IDb;?>&duplicar=1&Dump=<? echo uniqid("");?>"><img src="img/duplicate.png" border="0" /></a></td>
				<td align="center" width="30px" ><a title="Remover" class="lista_links" onclick="if(confirm('Tem a certeza que pretente remover este registo?')) return true; else return false;" href="list.php?<? echo TBID?>=<? echo $IDb;?>&ACCOES=4&Dump=<? echo uniqid("");?>"><img src="img/delete.png" border="0" /></a></td>
				<? }else{?>
				<td align="center" width="30px" height="32">&nbsp;</td>
				<td align="center" width="30px" height="32">&nbsp;</td>
				<? }?>
				<td align="center" width="30px" class="ordem<?=$count?>" style="text-align:left"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
				</tr>
				<?
			}
			$rs->db_next();
		}
		?>
		</table>
		<?
		if($n_pags > 1)
		{
			if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages='pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&visivel='.urlencode($_GET['visivel']);
			else $url_pages='list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&visivel='.urlencode($_GET['visivel']);
			$return='<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn='onclick=window.open("'.$url_pages.'&pagina=1","_self")';
				$return.='<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn='onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
				$return.=($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'","_self")';
				$return.='<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return.='<td>&nbsp;</td></tr></table>';
			echo $return;
		}
		return 1;
	}
	elseif($pai==0)
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <? echo MODULO?></td>
		</tr>
		</table>
	<?
	}
	$rs->db_close();
}
function show_easy_create()
{
	global $browser,$ID;
	if(($browser=="Firefox" || $browser=="Mozilla") && $ID)
	{?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
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
<? }
}

function show_preview($ID)
{
	global $site_http_url;
	?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td align="center"><iframe id="media" class="autoHeight"  src="<?=$site_http_url?>preview.php?ID=<?=$ID?>"  width="890" height="400" style="overflow:hidden" frameborder=0 SCROLLING="auto"><p>Your browser does not support iframes.</p></iframe></td>
	</tr>
    </table><?
}
?>