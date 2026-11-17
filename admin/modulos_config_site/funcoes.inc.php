<?php

function intervalos_stock($tipo, $valor=0)
{
	echo '<select name="stock_'.$tipo.'_aparece" id="stock_'.$tipo.'_aparece" class="form-control" style="width:140px">';
	echo '<option value="0" '.($valor == 0 ? 'selected="selected"' : '').'>Menor ou Igual</option>';
	echo '<option value="1" '.($valor == 1 ? 'selected="selected"' : '').'>=</option>';
	echo '<option value="2" '.($valor == 2 ? 'selected="selected"' : '').'>>=</option>';
    echo '</select>';
}

function gera_lista_familias($id_org)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from products_families where ID_ORG='0' AND visivel=1 order by Ordem Asc";
	$obj->db_open($db_link,$DB_name,$sql);


	// $blocos_fams = explode(';',$id_org);


	echo '<select name="familia_id" id="familia_id" class="form-control">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($obj->db_data['ID']==$id_org) echo ' selected="selected" ';
		$sql="Select * from products_families_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$obj2->db_open($db_link,$DB_name,$sql);
		echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';
		$obj2->db_close();
	    gera_lista_subfamilias($obj->db_data["ID"],$id_org);
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function gera_lista_subfamilias($id_start=0, $id_org=array(), $contador=5)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from products_families where ID_ORG='$id_start' AND visivel=1 order by Ordem Asc";
	$obj->db_open($db_link,$DB_name,$sql);

	$blocos_fams = explode(';',$id_org);

    while(!$obj->EOF())
    {
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($obj->db_data['ID']==$id_org) echo ' selected="selected" ';
		$sql="Select * from products_families_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);
		echo '>';
		$i=0;while($i<$contador){echo "- ";$i++;}
		echo  htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';
	    $obj2->db_close();
	    gera_lista_subfamilias($obj->db_data["ID"],$id_org,$contador+5);
        $obj->db_next();
    }
    $obj->db_close();
}

function gera_lista_grupos_users($blocos_users)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$blocos_users2 = explode(';',$blocos_users);

	global $admin_id;
	$where = 'WHERE Grupo_ID=5';
	$rs->db_open($db_link,$DB_name,'Select ID, Nome from logins '.$where);
	echo '<select name="listagrupos[]" id="listagrupos" multiple="multiple"  class="form-control">';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if(in_array($rs->db_data['ID'],$blocos_users2)) echo ' selected ';
		echo '>';
		echo $rs->db_data['Nome'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}

function GET_BLOCOS_ATIVIDADES($tabela='blocos')
{



	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;$rs2 = new DataObj; $return = '';
	$sql = '
			SELECT
				b.bloco_id, b.bloco_ext, b.bloco_data, bl.bloco_titulo, bl.bloco_resumo, bl.bloco_texto
			FROM
				'.$tabela.' b
				INNER JOIN '.$tabela.'_lang bl ON bl.bloco_id = b.bloco_id
			WHERE
				bl.bloco_lang = "'.$GLOBALS['admin_lang'].'"
				AND b.bloco_id_pai=0
			ORDER BY
				b.bloco_ordem ASC, b.bloco_data DESC
			';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{
		$a = 6;
		$a2 = 0;

		$return .= '<div class="dashboard">';
		$return .= '<ul>';

		while(!$rs->EOF())
		{

			$bloco_id = $rs->db_data['bloco_id'];
			$bloco_open = $rs->db_data['bloco_open'];
			$bloco_titulo = $rs->db_data['bloco_titulo'];
			$bloco_data = $rs->db_data['bloco_data'];
			$bloco_resumo = $rs->db_data['bloco_resumo'];
			$bloco_texto = $rs->db_data['bloco_texto'];
			$bloco_ext = $rs->db_data['bloco_ext'];

			$img_file_med = '';
			if($tabela!='blocos')
			{
				$img_file = 'files/documentos/'.$bloco_ext;
				$ext = pathinfo($img_file, PATHINFO_EXTENSION);
				$img_file_med = str_replace('.'.$ext, '_dash.'.$ext, $img_file);

				if(file_exists($GLOBALS['site_shell_url'].$img_file)) $original = date("Y-m-d H:i:s",filemtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
				if(file_exists($GLOBALS['site_shell_url'].$img_file_med)) $miniatura = date("Y-m-d H:i:s",filemtime($GLOBALS['site_shell_url'].$img_file_med)); //data/hora em que foi modificada/criada

				if((!file_exists($GLOBALS['site_shell_url'].$img_file_med) || $miniatura<$original) && file_exists($GLOBALS['site_shell_url'].$img_file))
				{
					if(resize_image($GLOBALS['site_shell_url'].$img_file,100,100,true, $GLOBALS['site_shell_url'].$img_file_med)){/* myimagefilledpolygon($GLOBALS['site_shell_url'].$img_file_med, $GLOBALS['site_shell_url'].$img_file_med, '#e31d1a'); */}
				}
			}


			if(file_exists($GLOBALS['site_shell_url'].$img_file_med) && $ext!=''){$pad = '2px;background:url(../layout/logo_mini_sw.png) no-repeat center;';$height='38px;';}
			else{ $img_file_med = 'layout/logo_mini.png'; $pad = '10px'; $height='30px;';}


			if($a==$a2)
			{
				$return .= '</ul></div><div class="dashboard"><ul>';
				$a2 = 0;
			}

			if($bloco_open > 0) $URL_Link = $GLOBALS['site_http_url'].'bl-'.$bloco_open.'-'. str_replace(' ','',limpa_special($bloco_titulo)).'-'.$GLOBALS['site_lang'];
			else $URL_Link = $GLOBALS['site_http_url'].'bl-'.$bloco_id.'-'. str_replace(' ','',limpa_special($bloco_titulo)).'-'.$GLOBALS['site_lang'];
			$URL_Link = str_replace($GLOBALS['site_http_url'],'',$URL_Link);

			$return .= '<li style="cursor:pointer" onclick="window.open(\'sa_'.$tabela.'/pagina.php?bloco_id='.$bloco_id.'\',\'_self\')">
			<div style="height:100px;padding-top:'.$pad.'"><img src="'.$GLOBALS['site_http_url'].$img_file_med.'" border="0" /></div>';
			$return .= '<div style="height:'.$height.';display: table-cell;vertical-align:middle;text-align: center;width:150px">';
			$return .= ''.$bloco_titulo.'</div>';
			$return .= '<div class="clear"></div>';
			$return .= '</li>';

			$a2++;
			$rs->db_next();
		}
		$return .= '</ul></div>';
	}
	$rs->db_close(1);

	return $return;
}
function show_menupath($id_org,$table="pages",$pagina="pagina.php",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT bloco_id, bloco_id_pai FROM $table WHERE bloco_id=$id_org";
		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["bloco_id"];
		$id_org1=$rs->db_data["bloco_id_pai"];
		$rs->db_close();
		$sql="SELECT bloco_titulo FROM $table"."_lang WHERE bloco_id=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["bloco_titulo"]);
		$C1=$C!="" ? $C : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" - <a href=\"$pagina"."?bloco_id=$codigo&do=".date('YmdHis')."\" class=\"brancoh\">$C1</a>";
	}
	return $str;
}

function gera_lista_path($id_org=0,$T,$Tlang,$tipo='')
{

}
function gera_lista_path2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}

function gera_lista_path_open($id_org=0,$T,$Tlang,$tipo='')
{
  /*  global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="'.$_GET['bloco_id'].'" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="bloco_open" style="width:350px" class="form-control">';
	if($id_org==0) echo '<option value="0" selected>- Esta -</option>';
	else echo '<option value="0">- Esta -</option>';
    while(!$rs->EOF())
    {
	    if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';

		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
		gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';*/
}
function gera_lista_path_open2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	/*global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
       if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';

		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();	       */
}

function getBrands($arr_brd)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$result=array();

	$sql = '
			SELECT
				b.ID,
				b.img_ext,
				bo.*
			FROM
				brands b
				INNER JOIN brands_lang bo ON bo.ID_PAI=b.ID
			ORDER BY
				b.Ordem ASC
				';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	while(!$rs->EOF())
	{
		if(!in_array($rs->db_data['ID'], $arr_brd))
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
function gera_lista_grupos($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;



    echo '<select id="blocos_users" name="blocos_users[]" class="box form-control" multiple="multiple" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}
function gera_lista_brands_cat($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

    echo '<select id="listademarcas" name="listademarcas[]" class="box form-control" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}

function gera_lista_brands_list($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

    echo '<select id="listademarcasx" name="listademarcasx" class="form-control" style="font-size:13px;width:150px">';
	echo '<option value="">TODOS</option>';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}

function gera_brands_name($marca_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

   $sql = '
			SELECT
				bo.Nome
			FROM
				brands_lang bo
			WHERE
				ID_PAI="'.$marca_id.'"
				';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	if(!$rs->EOF()) $brand_nome = $rs->db_data['Nome'];
	$rs->db_close();

	return $brand_nome;
}
function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}

				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= ')';
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO.' SET ';
		$i = 0;

		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if(substr_count($campos,'_ext'))
				{

					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}

				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= ' WHERE '.TBID.'='.$_POST[TBID];

	}

	/*
	die();*/
//INSERT INTO modulos_config_site(revendedor_id, img_ext, img2_ext, img3_ext, img4_ext, img5_ext, pais_id_default, exec_caminho, forcar_https, FromName, form_recipient, Default_SMTP, Default_SMTP_login, Default_SMTP_pass, Default_SMTP_Email, Default_SMTP_Porta, Default_notifications_email, TAG_EMPRESA, TAG_DESIGSOCIAL, TAG_COPYRIGHT, SEO_FB, SEO_PHONE, SEO_DESCRIPTION, iva_default, offline, offline_txt, offline_cor, Seta_scrollup, emails_encomenda, stock_azul_aparece, stock_vermelho_aparece, stock_amarelo_aparece, stock_verde_aparece, stock_azul_qtd, stock_vermelho_qtd, stock_amarelo_qtd, stock_verde_qtd, stock_azul, stock_vermelho, stock_amarelo, stock_verde, stock_azul_ativo, stock_vermelho_ativo, stock_amarelo_ativo, stock_verde_ativo, facebook_id, facebook_secret, facebook_ativo, google_id, google_secret, google_ativo, twitter_id, twitter_secret, twitter_ativo, dblocalhost, dbusername, dbpassword, dbname, title, url, url_email, addthis_id, addthis_ativo, icon_bc, texto_bc, cookie_txt_barra, cookie_txt_modal, cookie_posicao, cred_min, onoff_valor_min)VALUES("", "logo_top_memogadgetcms5eb54aa7cf64320200508130351.png", "", "", "", "", "165", "php", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "23.0000", "", "", "", "", "", "0", "", "0", "0", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "home", "", "", "0", "", "")

	$res = Runquery($db_link, $DB_name, $sql);
	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}

function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs = new DataObj;
	$sql='SELECT '.TBID.' FROM '.TBMODULO_LANG.' WHERE '.TBID.'='.$modulo_id.' AND bloco_lang="'.$GLOBALS['admin_lang'].'"';
	// echo $sql.'<br />';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF()) $modulo_acao = 'update'; else $modulo_acao = 'insere';
	$rs->db_close();

	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS_LANG'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO_LANG;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != 'bloco_lang_id'){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != 'bloco_lang_id')
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				elseif($campos=='bloco_id')
				{
					$sql .= '"'.$modulo_id.'"';
				}
				elseif($campos=='bloco_titulo' && $modulo_duplicar == 1)
				{
					$sql .= '"'.addslashes($_POST[$campos].'(duplicado)').'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= '
				)
				';
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO_LANG.' SET ';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID && $campos != 'bloco_lang' && $campos != 'bloco_lang_id')
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				elseif($campos=='bloco_id')
				{
					$sql .= $campos.'="'.$modulo_id.'"';
				}
				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= 'WHERE '.TBID.'='.$_POST[TBID].' AND bloco_lang="'.$GLOBALS['admin_lang'].'"';
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
}

function submeteimagens($bloco_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	if($modulo_duplicar == 0)
	{
		$modulo_id = $bloco_id;
		$CMPS_MODULO = $GLOBALS['CMPS_MODULO_IMGS'];

		// echo '<pre style="color:#FFF">';
		$query = '';

		foreach($_POST['RENOVACAO_ID'] as $field_num2)
		{
			$codis = explode(';', $field_num2);
			$field_num = $codis[0];
			$field_id = $codis[1];

			$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

			if($modulo_acao=='insere')
			{
				$sql = 'INSERT INTO '.TBIMAGENS;
				$sql .= '(';
				$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBIMGID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
				$sql .= ')VALUES(';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{
						if($i>0){$sql .= ', ';}

						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							$sql .= '"'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel'))
						{
							$sql .= '"'.$field_num.'"';
						}
						elseif(substr_count($campos,'bloco_id'))
						{
							$sql .= '"'.$modulo_id.'"';
						}
						else
						{
							$sql .= '"'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ')';
			}
			elseif($modulo_acao=='update')
			{
				$sql = 'UPDATE '.TBIMAGENS.' SET ';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{
						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							if($i>0 && $nome_ficheiro){$sql .= ', ';}
							if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel'))
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$field_num.'"';
						}
						elseif($campos=='bloco_id')
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$modulo_id.'"';
						}
						else
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ' WHERE '.TBIMGID.'='.$field_id;
			}
			$res = Runquery($db_link, $DB_name, $sql);
			// echo $sql.'<br />';
		}
	}
	// echo '</pre>';
}
function submeteimagens2($bloco_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	if($modulo_duplicar == 0)
	{
		$modulo_id = $bloco_id;
		$CMPS_MODULO = $GLOBALS['CMPS_MODULO_IMGS2'];

		// echo '<pre style="color:#FFF">';
		$query = '';

		foreach($_POST['RENOVACAO_ID2'] as $field_num2)
		{
			$codis = explode(';', $field_num2);
			$field_num = $codis[0];
			$field_numx = explode('_', $codis[0]);
			$field_id = $codis[1];

			$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

			if($modulo_acao=='insere')
			{
				$sql = 'INSERT INTO '.TBIMAGENS2;
				$sql .= '(';
				$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBIMGID2){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
				$sql .= ')VALUES(';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != TBIMGID2)
					{
						if($i>0){$sql .= ', ';}

						// echo $campos.$field_num.'<br>';
						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							$sql .= '"'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel') && !substr_count($campos,'_rel2'))
						{
							$sql .= '"'.$field_numx[0].'"';
						}
						elseif(substr_count($campos,'_rel2'))
						{
							$sql .= '"'.$field_numx[1].'"';
						}
						elseif(substr_count($campos,'bloco_id'))
						{
							$sql .= '"'.$modulo_id.'"';
						}
						else
						{
							$sql .= '"'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ')';
			}
			elseif($modulo_acao=='update')
			{
				$sql = 'UPDATE '.TBIMAGENS2.' SET ';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
				{
					if($campos != TBIMGID2)
					{
						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							if($i>0 && $nome_ficheiro){$sql .= ', ';}
							if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel') && !substr_count($campos,'_rel2'))
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$field_numx[0].'"';
						}
						elseif(substr_count($campos,'_rel2'))
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$field_numx[1].'"';
						}
						elseif($campos=='bloco_id')
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$modulo_id.'"';
						}
						else
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ' WHERE '.TBIMGID2.'='.$field_id;
			}
			$res = Runquery($db_link, $DB_name, $sql);
			// echo $sql.'<br />';
		}
	}
	// echo '</pre>';
}
function submeteAverbamentos($id_marca)
{
	// echo '<pre style="color:#FFF">';
	tabs_submete_av('ENDERECO_ID');
	tabs_submete_av('NOME_ID');
	tabs_submete_av('TRANSMISSAO_ID');
	tabs_submete_av('FUSAO_ID');
	tabs_submete_av('LICENSA_ID');
	// echo '</pre>';
}

function submeteOposicoes($id_marca)
{
	echo '<pre style="color:#FFF">';
	tabs_submete_op('OPOSICAO_ID');
	tabs_submete_op('RESPOSTA_ID');
	echo '</pre>';
}

function tabs_submete_op($tab)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_OPOSICAO'];

	foreach($_POST[$tab] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBOPOSICOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBOPOSICAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID)
				{
					if($i>0){$sql .= ', ';}

					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBOPOSICOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBOPOSICAOID.'='.$field_id.' AND objeto_bloco="'.$field_bloco.'"';
		}
		// echo $sql.'<br />';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteReclamacoes($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECLAMACAO'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECLAMACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECLAMACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECLAMACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRECLAMACOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRECLAMACAOID.'='.$field_id;
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteRecursos($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECURSOS'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECURSO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECURSOS;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECURSOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRECURSOS.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRECURSOID.'='.$field_id;
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submete_ficheiro($file_name)
{
	$img_path = '../../files/'.FOLDER.'/';

	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	$nome_ficheiro = '';

	if($Img_peq["name"]!="")
	{

		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], $pos+1, strlen($Img_peq['name'])+1-$pos);


		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;
		$sql='SELECT '.$file_name.' FROM '.TBMODULO.' WHERE '.$file_name.'<> ""';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF()) $nome_ficheiro_old = $rs->db_data[$file_name];
		$rs->db_close();

		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
		{
			unlink($img_path.$nome_ficheiro_old);
		}

		if($file_name == 'img_ext') $token = 'logo_top_'.uniqid(PROJETO).date('YmdHis');
		elseif($file_name == 'img2_ext') $token = 'favicon_'.uniqid(PROJETO).date('YmdHis');
		elseif($file_name == 'img3_ext') $token = 'logotipo_popups_'.uniqid(PROJETO).date('YmdHis');
		elseif($file_name == 'img4_ext') $token = 'logotipo_email_'.uniqid(PROJETO).date('YmdHis');
		elseif($file_name == 'img5_ext') $token = 'logotipo_mobile'.uniqid(PROJETO).date('YmdHis');
		else $token = uniqid(PROJETO).date('YmdHis');

		$nome_ficheiro = $token.".".$img_ext;
		if(copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro))
		{
			if($file_name == 'img2_ext')
			{
				gerar_favicons($img_path, $nome_ficheiro);
				save_manifest();
			}
		}
	}

	return $nome_ficheiro;
}

function save_manifest()
{
	$file_xml = '{
	    "name": "'.SITE_TITLE.'",
	    "short_name": "'.SITE_TITLE.'",
	    "display": "standalone",
	    "background_color": "'.$GLOBALS['theme_color'].'",
	    "description": "'.$GLOBALS['SEO_DESCRIPTION'].'",
	    "icons": [
	        {
	            "src": "/files/icons/android-icon-36x36_v_'.filemtime('../../files/icons/android-icon-36x36.png').'.png",
	            "sizes": "36x36",
	            "type": "image/png"
	        },
	        {
	            "src": "/files/icons/android-icon-48x48_v_'.filemtime('../../files/icons/android-icon-48x48.png').'.png",
	            "sizes": "48x48",
	            "type": "image/png"
	        },
	        {
	            "src": "/files/icons/android-icon-72x72_v_'.filemtime('../../files/icons/android-icon-72x72.png').'.png",
	            "sizes": "72x72",
	            "type": "image/png"
	        },
	        {
	            "src": "/files/icons/android-icon-96x96_v_'.filemtime('../../files/icons/android-icon-96x96.png').'.png",
	            "sizes": "96x96",
	            "type": "image/png"
	        },
	        {
	            "src": "/files/icons/android-icon-144x144_v_'.filemtime('../../files/icons/android-icon-144x144.png').'.png",
	            "sizes": "144x144",
	            "type": "image/png"
	        },
	        {
	            "src": "/files/icons/android-icon-192x192_v_'.filemtime('../../files/icons/android-icon-192x192.png').'.png",
	            "sizes": "192x192",
	            "type": "image/png"
	        }
	    ],
	    "theme_color": "'.$GLOBALS['theme_color'].'"

		}';

		$path2 = '../../files/icons/';

		@file_put_contents($path2.'manifest.json', $file_xml);
}

/*
Copyright 2011-2016 Chris Jean & iThemes
Licensed under GPLv2 or above
Version 1.0.4
*/

class PHP_ICO {
	/**
	 * Images in the BMP format.
	 *
	 * @var array
	 * @access private
	 */
	var $_images = array();
	/**
	 * Flag to tell if the required functions exist.
	 *
	 * @var boolean
	 * @access private
	 */
	var $_has_requirements = false;
	/**
	 * Constructor - Create a new ICO generator.
	 *
	 * If the constructor is not passed a file, a file will need to be supplied using the {@link PHP_ICO::add_image}
	 * function in order to generate an ICO file.
	 *
	 * @param string $file Optional. Path to the source image file.
	 * @param array $sizes Optional. An array of sizes (each size is an array with a width and height) that the source image should be rendered at in the generated ICO file. If sizes are not supplied, the size of the source image will be used.
	 */
	function __construct( $file = false, $sizes = array() ) {
		$required_functions = array(
			'getimagesize',
			'imagecreatefromstring',
			'imagecreatetruecolor',
			'imagecolortransparent',
			'imagecolorallocatealpha',
			'imagealphablending',
			'imagesavealpha',
			'imagesx',
			'imagesy',
			'imagecopyresampled',
		);
		foreach ( $required_functions as $function ) {
			if ( ! function_exists( $function ) ) {
				trigger_error( "The PHP_ICO class was unable to find the $function function, which is part of the GD library. Ensure that the system has the GD library installed and that PHP has access to it through a PHP interface, such as PHP's GD module. Since this function was not found, the library will be unable to create ICO files." );
				return;
			}
		}
		$this->_has_requirements = true;
		if ( false != $file )
			$this->add_image( $file, $sizes );
	}
	/**
	 * Add an image to the generator.
	 *
	 * This function adds a source image to the generator. It serves two main purposes: add a source image if one was
	 * not supplied to the constructor and to add additional source images so that different images can be supplied for
	 * different sized images in the resulting ICO file. For instance, a small source image can be used for the small
	 * resolutions while a larger source image can be used for large resolutions.
	 *
	 * @param string $file Path to the source image file.
	 * @param array $sizes Optional. An array of sizes (each size is an array with a width and height) that the source image should be rendered at in the generated ICO file. If sizes are not supplied, the size of the source image will be used.
	 * @return boolean true on success and false on failure.
	 */
	function add_image( $file, $sizes = array() ) {
		if ( ! $this->_has_requirements )
			return false;
		if ( false === ( $im = $this->_load_image_file( $file ) ) )
			return false;
		if ( empty( $sizes ) )
			$sizes = array( imagesx( $im ), imagesy( $im ) );
		// If just a single size was passed, put it in array.
		if ( ! is_array( $sizes[0] ) )
			$sizes = array( $sizes );
		foreach ( (array) $sizes as $size ) {
			list( $width, $height ) = $size;
			$new_im = imagecreatetruecolor( $width, $height );
			imagecolortransparent( $new_im, imagecolorallocatealpha( $new_im, 0, 0, 0, 127 ) );
			imagealphablending( $new_im, false );
			imagesavealpha( $new_im, true );
			$source_width = imagesx( $im );
			$source_height = imagesy( $im );
			if ( false === imagecopyresampled( $new_im, $im, 0, 0, 0, 0, $width, $height, $source_width, $source_height ) )
				continue;
			$this->_add_image_data( $new_im );
		}
		return true;
	}
	/**
	 * Write the ICO file data to a file path.
	 *
	 * @param string $file Path to save the ICO file data into.
	 * @return boolean true on success and false on failure.
	 */
	function save_ico( $file ) {
		if ( ! $this->_has_requirements )
			return false;
		if ( false === ( $data = $this->_get_ico_data() ) )
			return false;
		if ( false === ( $fh = fopen( $file, 'w' ) ) )
			return false;
		if ( false === ( fwrite( $fh, $data ) ) ) {
			fclose( $fh );
			return false;
		}
		fclose( $fh );
		return true;
	}
	/**
	 * Generate the final ICO data by creating a file header and adding the image data.
	 *
	 * @access private
	 */
	function _get_ico_data() {
		if ( ! is_array( $this->_images ) || empty( $this->_images ) )
			return false;
		$data = pack( 'vvv', 0, 1, count( $this->_images ) );
		$pixel_data = '';
		$icon_dir_entry_size = 16;
		$offset = 6 + ( $icon_dir_entry_size * count( $this->_images ) );
		foreach ( $this->_images as $image ) {
			$data .= pack( 'CCCCvvVV', $image['width'], $image['height'], $image['color_palette_colors'], 0, 1, $image['bits_per_pixel'], $image['size'], $offset );
			$pixel_data .= $image['data'];
			$offset += $image['size'];
		}
		$data .= $pixel_data;
		unset( $pixel_data );
		return $data;
	}
	/**
	 * Take a GD image resource and change it into a raw BMP format.
	 *
	 * @access private
	 */
	function _add_image_data( $im ) {
		setMemoryForImage($im); // vamos alocar memoria para esta imagem
		$width = imagesx( $im );
		$height = imagesy( $im );
		$pixel_data = array();
		$opacity_data = array();
		$current_opacity_val = 0;
		for ( $y = $height - 1; $y >= 0; $y-- ) {
			for ( $x = 0; $x < $width; $x++ ) {
				$color = imagecolorat( $im, $x, $y );
				$alpha = ( $color & 0x7F000000 ) >> 24;
				$alpha = ( 1 - ( $alpha / 127 ) ) * 255;
				$color &= 0xFFFFFF;
				$color |= 0xFF000000 & ( $alpha << 24 );
				$pixel_data[] = $color;
				$opacity = ( $alpha <= 127 ) ? 1 : 0;
				$current_opacity_val = ( $current_opacity_val << 1 ) | $opacity;
				if ( ( ( $x + 1 ) % 32 ) == 0 ) {
					$opacity_data[] = $current_opacity_val;
					$current_opacity_val = 0;
				}
			}
			if ( ( $x % 32 ) > 0 ) {
				while ( ( $x++ % 32 ) > 0 )
					$current_opacity_val = $current_opacity_val << 1;
				$opacity_data[] = $current_opacity_val;
				$current_opacity_val = 0;
			}
		}
		$image_header_size = 40;
		$color_mask_size = $width * $height * 4;
		$opacity_mask_size = ( ceil( $width / 32 ) * 4 ) * $height;
		$data = pack( 'VVVvvVVVVVV', 40, $width, ( $height * 2 ), 1, 32, 0, 0, 0, 0, 0, 0 );
		foreach ( $pixel_data as $color )
			$data .= pack( 'V', $color );
		foreach ( $opacity_data as $opacity )
			$data .= pack( 'N', $opacity );
		$image = array(
			'width'                => $width,
			'height'               => $height,
			'color_palette_colors' => 0,
			'bits_per_pixel'       => 32,
			'size'                 => $image_header_size + $color_mask_size + $opacity_mask_size,
			'data'                 => $data,
		);
		$this->_images[] = $image;
	}
	/**
	 * Read in the source image file and convert it into a GD image resource.
	 *
	 * @access private
	 */
	function _load_image_file( $file ) {
		// Run a cheap check to verify that it is an image file.
		if ( false === ( $size = getimagesize( $file ) ) )
			return false;
		if ( false === ( $file_data = file_get_contents( $file ) ) )
			return false;
		if ( false === ( $im = imagecreatefromstring( $file_data ) ) )
			return false;
		unset( $file_data );
		return $im;
	}
}

function gerar_favicons($path='',$img='')
{
	$src = $path.$img;
	$path2 = $path."../../files/icons/";

	$testGD = get_extension_funcs("gd"); // Grab function list
	if(in_array ("imagegd2",$testGD)) $gd_version="<2"; // Check
	if(!file_exists($src)){ echo 'Imagem não existe!';}
	else
	{
		$img_original = imagecreatefrompng($src);
		$width = imagesx($img_original);
		$height = imagesy($img_original);
		$i = 0;

		$icons[$i][0]='36';$icons[$i][1]='36';$icons[$i][2]='android-icon-36x36.png';$i++;
		$icons[$i][0]='48';$icons[$i][1]='48';$icons[$i][2]='android-icon-48x48.png';$i++;
		$icons[$i][0]='72';$icons[$i][1]='72';$icons[$i][2]='android-icon-72x72.png';$i++;
		$icons[$i][0]='96';$icons[$i][1]='96';$icons[$i][2]='android-icon-96x96.png';$i++;
		$icons[$i][0]='144';$icons[$i][1]='144';$icons[$i][2]='android-icon-144x144.png';$i++;
		$icons[$i][0]='192';$icons[$i][1]='192';$icons[$i][2]='android-icon-192x192.png';$i++;
		$icons[$i][0]='192';$icons[$i][1]='192';$icons[$i][2]='apple-icon.png';$i++;
		$icons[$i][0]='57';$icons[$i][1]='57';$icons[$i][2]='apple-icon-57x57.png';$i++;
		$icons[$i][0]='60';$icons[$i][1]='60';$icons[$i][2]='apple-icon-60x60.png';$i++;
		$icons[$i][0]='72';$icons[$i][1]='72';$icons[$i][2]='apple-icon-72x72.png';$i++;
		$icons[$i][0]='76';$icons[$i][1]='76';$icons[$i][2]='apple-icon-76x76.png';$i++;
		$icons[$i][0]='114';$icons[$i][1]='114';$icons[$i][2]='apple-icon-114x114.png';$i++;
		$icons[$i][0]='120';$icons[$i][1]='120';$icons[$i][2]='apple-icon-120x120.png';$i++;
		$icons[$i][0]='144';$icons[$i][1]='144';$icons[$i][2]='apple-icon-144x144.png';$i++;
		$icons[$i][0]='152';$icons[$i][1]='152';$icons[$i][2]='apple-icon-152x152.png';$i++;
		$icons[$i][0]='180';$icons[$i][1]='180';$icons[$i][2]='apple-icon-180x180.png';$i++;
		$icons[$i][0]='192';$icons[$i][1]='192';$icons[$i][2]='apple-icon-precomposed.png';$i++;
		$icons[$i][0]='16';$icons[$i][1]='16';$icons[$i][2]='favicon-16x16.png';$i++;
		$icons[$i][0]='32';$icons[$i][1]='32';$icons[$i][2]='favicon-32x32.png';$i++;
		$icons[$i][0]='96';$icons[$i][1]='96';$icons[$i][2]='favicon-96x96.png';$i++;
		$icons[$i][0]='160';$icons[$i][1]='160';$icons[$i][2]='favicon-160x160.png';$i++;
		$icons[$i][0]='192';$icons[$i][1]='192';$icons[$i][2]='favicon-192x192.png';$i++;
		$icons[$i][0]='70';$icons[$i][1]='70';$icons[$i][2]='ms-icon-70x70.png';$i++;
		$icons[$i][0]='144';$icons[$i][1]='144';$icons[$i][2]='ms-icon-144x144.png';$i++;
		$icons[$i][0]='150';$icons[$i][1]='150';$icons[$i][2]='ms-icon-150x150.png';$i++;
		$icons[$i][0]='310';$icons[$i][1]='310';$icons[$i][2]='ms-icon-310x310.png';$i++;

		$j=0;
		while($j<$i)
		{
			$w1=$icons[$j][0];
			$h1=$icons[$j][1];
			redimencionaPNG($path, $img, $w1, $h1, $icons[$j][2], 0, $path2);
			$j++;
		}

		//Gerar favicon:
		$ico_lib = new PHP_ICO( $path.$img );
		$ico_lib->save_ico( $path2.'favicon.ico' );
	}
}

function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?php

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<?php echo $ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	</div>
<?php
}
function show_fast_tools()
{
	global $ID, $admin_id, $BOTOES_L;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr>
	<td height="35px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
	<td class="titulo2" align="center" height="20">FERRAMENTAS</td>
	</tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		$a=1;
		foreach($BOTOES_L as $key => $value)
		{
			echo $a;
			if($a==2 && isset($_GET[TBID]) && $_GET[TBID] > 0)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php
			}
			elseif($a!=2)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php
			}

			$a++;
		}
	}
	?>
	</table>
<?php }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>
			<td width="5%" align="center">&nbsp;</td>
		    <td width="95%" valign="middle" style="font-weight:bold;color:#FFF"><?php echo MODULO?></td>
		</tr>
	</table>

<?php }
function show_page_bottom_tools()
{?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" style="background:#FFF" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="150" height="35" align="right" style="font-weight:bold">Para os Selecionados:&nbsp;</td>
<td height="35" width="275"><select name="ACCOES" id="ACCOES" class="form-control">
<option value="">-</option>
<?php if(isset($GLOBALS['BOTOES_B']) && !empty($GLOBALS['BOTOES_B'])){
foreach($GLOBALS['BOTOES_B'] as $value => $key){
echo '<option value="'.$key.'">'.$value.'</option>';
}}?>
</select></td>
<td  height="35" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
</tr>
</table>
<?php
}

function acoes_selecionados()
{
	if(isset($_POST['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_POST['ACCOES']==1) // REMOVER REGISTO
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==3) // ATIVAR REGISTOS
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET garantia_visivel=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==2) // DESATIVAR REGISTOS
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET garantia_visivel=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
	elseif(isset($_GET['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_GET['ACCOES']==1) // REMOVER REGISTO
		{
			$str_id = $_GET['bloco_id'];
			if($str_id > 0)
			{
				$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link) . $sql . '<br>';
			}
		}
		elseif($_GET['ACCOES']==3) // ATIVAR REGISTO
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET garantia_visivel=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==2) // DESATIVAR REGISTO
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET garantia_visivel=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
}

function get_family_names($fam_id)
{

	$fam_ids = str_replace(';', ',', rtrim($fam_id,';'));

	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;
	if(isset($fam_id) && $fam_id > 0)
	{
		$and = ' AND PL.ID_PAI IN('.$fam_ids.')';
		$sql = '
				SELECT PL.Cabecalho, PL.Cabecalho2
				FROM products_families_lang PL
				WHERE PL.Lang="'.$GLOBALS['site_lang'].'"
		';
		$sql .= $and;
		$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		$fams = '';
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				if($rs->db_data['Cabecalho2']) $fam_nome=htmlentities_memo(stripslashes($rs->db_data['Cabecalho2']));
				else $fam_nome=htmlentities_memo(stripslashes($rs->db_data['Cabecalho']));

				$fams .= limpa_editor_lixo(stripslashes($fam_nome)).',';
				$rs->db_next();
			}

		}
		$rs->db_close();
	}


	return $fams;
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;

	$pesq = '';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq .= ' AND bl.bloco_titulo like "%'.$_GET['DESIGNACAO'].'%"';
		if(isset($_GET['DATA_PEDIDO']) && $_GET['DATA_PEDIDO']!='' && $_GET['DATA_PEDIDO']!='0000-00-00') $pesq .= ' AND b.bloco_data like "%'.$_GET['DATA_PEDIDO'].'%"';
		if(isset($_GET['garantia_visivel']) && $_GET['garantia_visivel']!='') $pesq .= ' AND b.garantia_visivel="'.$_GET['garantia_visivel'].'"';
		if(isset($_GET['ATIVIDADE']) && $_GET['ATIVIDADE']!='') $pesq .= ' AND b.bloco_categorias="'.$_GET['ATIVIDADE'].';"';
	}
	$sql = '
			SELECT
				'.implode(',',$GLOBALS['CMPS_MODULO_DADOS']).'
			FROM
				'.TBMODULO.' b
			WHERE
				1=1
				'.$pesq.'
			ORDER BY
				b.'.TBID.' ASC
			';

	$rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>
		<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center">&nbsp;</td>

		<td width="100" valign="middle" align="center"><b>Estado</b></td>
		<td valign="middle" align="left"><b>Titulo</b></td>
		<td width="100" valign="middle" align="left"><b>Nr Anos</b></td>
		<td width="100" valign="middle" align="left"><b>Valor</b></td>
		<td width="100" valign="middle" align="left"><b>Limite</b></td>
		<td width="10" valign="middle">&nbsp;</td>
		<td width="10" valign="middle">&nbsp;</td>

		</tr>
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center"><label class="label_check2" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>
		<td width="50" valign="middle" align="center"><select style="width:80px;font-size:11px;padding-top:4px;" name="garantia_visivel" id="garantia_visivel" class="form-control"><option value="" <?php if(!isset($_GET['garantia_visivel']) && !$_GET['garantia_visivel']) echo 'selected="selected"';?>>TODOS</option><option value="1" <?php if(isset($_GET['garantia_visivel']) && $_GET['garantia_visivel']=='1') echo 'selected="selected"';?>>Visivel</option><option value="2" <?php if(isset($_GET['garantia_visivel']) && $_GET['garantia_visivel']=='2') echo 'selected="selected"';?>>Invisivel</option></select></td>
		<td valign="middle" align="left"><input type="text" style="width:210px !important" name="fam_id" id="fam_id" class="form-control" value="<?php if(isset($_GET['fam_id'])) echo $_GET['fam_id']?>" /></td>
		<td valign="middle" width="90" align="left"><input type="text" style="width:80px !important" name="garantia_n_anos" id="garantia_n_anos" class="form-control" value="<?php if(isset($_GET['garantia_n_anos'])) echo $_GET['garantia_n_anos']?>" /></td>
		<td valign="middle" width="90" align="left"><input type="text" style="width:80px !important" name="garantia_valor" id="garantia_valor" class="form-control" value="<?php if(isset($_GET['garantia_valor'])) echo $_GET['garantia_valor']?>" /></td>
		<td valign="middle" width="130" align="left"><input type="text" style="width:80px !important" name="garantia_fim" id="garantia_fim" class="form-control" value="<?php if(isset($_GET['garantia_fim'])) echo $_GET['garantia_fim']?>" /></td>
		<td valign="middle" align="left" colspan="2"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa('<?php echo ((isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'pagina.php?'.TBID.'='.$_GET[TBID] : '')?>'); return false;" style="cursor:pointer" /></td>
		</tr>
		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];
			$familia_id = $rs->db_data["familia_id"];
			$titulo = $rs->db_data["titulo"];
			$garantia_n_anos = $rs->db_data["garantia_n_anos"];
			$garantia_valor = $rs->db_data["garantia_valor"];
			$garantia_ini = $rs->db_data["garantia_ini"];
			$garantia_fim = $rs->db_data["garantia_fim"];
			$garantia_visivel = $rs->db_data["garantia_visivel"];
			$garantia_visivel_img = (isset($rs->db_data["garantia_visivel"]) && $rs->db_data["garantia_visivel"]==1) ? 'ok.png' : 'hidden.png';


			// familia_id

			$fam_info = get_family_info($familia_id);


			if($garantia_visivel==1)
						{
				if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#FFFFFF";$class="r2";$n="2";}
			}
			else
			{
				if($count % 2 != 0)	{$cor="#dbdada";$class="r1";$n="1";}
				else				{$cor="#e8e8e8";$class="r2";$n="2";}
			}

			$familia_id_nome = $titulo.' - '.$fam_info['fam_nome'];
			$familia_id_nome2 = $titulo;
			?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">

			<td align="center"><label class="label_check2" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><img src="img/<?php echo $garantia_visivel_img?>"/></a></td>
			<td valign="middle" align="left"><a title="<?php echo $familia_id_nome2?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><? if($familia_id_nome){echo htmlentities_b(stripslashes($familia_id_nome));} else echo "[Edit]";?></a></td>

			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $garantia_n_anos.' '.($garantia_n_anos > 1 ? 'Anos' : 'Ano');  ;?></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo numberformat($garantia_valor);?></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo numberformat($garantia_ini).'/'.numberformat($garantia_fim);?></a></td>

			<td valign="middle"><a title="Duplicar" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&duplicar=1&Dump=<? echo uniqid("");?>"><img src="img/next-item.png" border="0" /></a></td>
			<td valign="middle"><a title="Remover" class="lista_links" onclick="if(confirm('Tem a certeza que pretente remover este registo?')) return true; else return false;" href="list.php?<?php echo TBID?>=<?php echo $id;?>&ACCOES=1&Dump=<? echo uniqid("");?>"><img src="img/delete-item.png" border="0" /></a></td>

			</tr>
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php
		if($n_pags > 1)
		{

			if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages = 'pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&garantia_visivel='.urlencode($_GET['garantia_visivel']);
			else $url_pages = 'list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&DATA_PEDIDO='.urlencode($_GET['DATA_PEDIDO']).'&garantia_visivel='.urlencode($_GET['garantia_visivel']);

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
				$pgn='onclick=window.open("'.$url_pages.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}

		return 1;
	}
	elseif($pai==0)
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <?php echo MODULO?></td>
		</tr>
		</table>
	<?php
	}
	$rs->db_close();
}
?>