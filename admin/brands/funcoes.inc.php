<?php
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<? echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{
	global $ID;?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Nova Coleção" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MARCAS</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" class="btn btn-primary" ></td>
        <td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_tools_destaques()
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"></td>
		<td width="150px" class=pesq></td>
		<td width="28px"></td>
        <td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><? echo write_lang('key_apagar','Apagar')?></option>
				<option value="2"><? echo write_lang('key_reordenar','Reordenar');?></option>
				<option value="4"><? echo write_lang('key_tornar_visivel','Tornar visivel')?></option>
				<option value="5"><? echo write_lang('key_tornar_invisivel','Tornar invisivel')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_page_bottom_tools_destaques()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="2"><? echo write_lang('key_reordenar','Reordenar')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? //show_options(0,0,"brands","brands_lang");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','aAplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function SearchRecords($d='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($d)
	{
		$d = strtolower(ltrim(rtrim($d)));
		$sql = "SELECT DISTINCT b.ID, b.Visivel,b.Ordem, bl.ID_PAI,  bl.Nome  AS Descricao FROM brands b inner join brands_lang bl ON bl.ID_PAI=b.ID WHERE ((lower(bl.Nome) LIKE '%".$d."%')) AND bl.lang='".$GLOBALS['admin_lang']."' GROUP BY bl.Nome ORDER BY Ordem ASC,bl.Nome ASC";
	}
	else $sql = "SELECT DISTINCT b.ID, b.Visivel,b.Ordem, bl.ID_PAI, bl.Nome  AS Descricao FROM brands b inner join brands_lang bl ON bl.ID_PAI=b.ID WHERE bl.lang='".$GLOBALS['admin_lang']."' GROUP BY bl.ID_PAI ORDER BY Ordem ASC,bl.Nome ASC";


	$rs->db_open($db_link,$DB_name,$sql);
	$cnt=1;
	while(!$rs->EOF())
	{
			$info[$cnt]= array("ID"=>$rs->db_data["ID"],"ID_PAI"=>$rs->db_data["ID_PAI"],"NAME"=>$rs->db_data["Descricao"],"Visible"=>$rs->db_data["Visivel"],"Ordem"=>$rs->db_data["Ordem"]);
			$cnt++;
			$rs->db_next();
			$x=1;

	}
	$rs->db_close();
	return $info;
}
function show_options($id_org=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	// echo $id_org;
	$sql="Select * from $Nomeb where ID_ORG='0' order by Ordem Asc";
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia2" style="width:60%; height:16px" class="form-control">';

    while(!$rs->EOF())
    {
        echo '<option value="'. $rs->db_data["ID"] .'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
		echo '>';
		echo 'Zone '.$rs->db_data["Ordem"];
		echo '</option>';

	    // show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas);
        $rs->db_next();
    }
    $rs->db_close();
    echo '</select>&nbsp;';
    db_closedb_conection($db_link);
}
function show_options_templates($template=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='Select '.$Nomeb.'.ID, '.$Nomeb_linguas.'.Cabecalho from '.$Nomeb.' inner join '.$Nomeb_linguas.' on '.$Nomeb_linguas.'.ID_PAI = '.$Nomeb.'.ID where Visivel=1 and Lang="'.$admin_lang.'"';
	$rs->db_open($db_link,$DB_name,$sql);

	$template = explode(';',$template);
	while(!$rs->EOF())
    {
		echo '<input type="checkbox" value="1" name="template_id'.$rs->db_data["ID"].'"';
		 if(in_array($rs->db_data["ID"],$template)){ echo ' checked="checked" ';}
		echo' />&nbsp;'.$rs->db_data["Cabecalho"].'&nbsp;&nbsp;';

        $rs->db_next();
    }
    $rs->db_close();

    db_closedb_conection($db_link);
}
function show_optionsb($id_org=0,$id_first=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
    {
        echo '<option value="'. $rs->db_data["ID"] .'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$rs->db_data["ID"]. " and Lang='$admin_lang'";
        $rs2=new DataObj;
	    $db_res=$rs2->db_open($db_link,$DB_name,$sql);
		echo '>';
		if(!$rs2->db_data["URL_Nome2"]) echo "N/A";else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas);
        $rs->db_next();
    }
    $rs->db_close();
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;
	$altera_img2=0;

	if($GLOBALS['Img_peq1']["name"]){$altera_img = 1; $pos = strrpos($GLOBALS['Img_peq1']["name"],"."); $img_ext = substr($GLOBALS['Img_peq1']["name"],$pos+1,strlen($GLOBALS['Img_peq1']["name"])+1-$pos);	}
	if($GLOBALS['Img_peq2']["name"]){$altera_img2 = 1; $pos = strrpos($GLOBALS['Img_peq2']["name"],"."); $img_ext2 = substr($GLOBALS['Img_peq2']["name"],$pos+1,strlen($GLOBALS['Img_peq2']["name"])+1-$pos);	}

	$visivel = intval($_POST['visivel']);
	$ordem = intval($_POST['ordem']);
	$preco_revenoff = intval($_POST['preco_revenoff']);
	$tipo = intval($_POST['tipo']);
	$ref_integracao = $_POST['ref_integracao'];
	// $Texto1 = intval($_POST['preco_revenoff']);


	$sql = '
			INSERT INTO brands
				(
					img_ext,
					img_ext2,
					Visivel,
					Ordem,
					preco_revenoff,
					tipo,
					ref_integracao
				)
				VALUES
				(
					"'.$img_ext.'",
					"'.$img_ext2.'",
					"'.$visivel.'",
					"'.$ordem.'",
					"'.$preco_revenoff.'",
					"'.$tipo.'",
					"'.$ref_integracao.'"
				)';
	$res = Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($GLOBALS['Img_peq1']["temp"],$GLOBALS['img_path'].$new_id."_1.".$img_ext);
		if($altera_img2) copia_segura($GLOBALS['Img_peq2']["temp"],$GLOBALS['img_path'].$new_id."_2.".$img_ext2);
		$res=InsertNewRecordLinguas($new_id);
	}
	else echo db_error($db_link).$sql;
	db_closedb_conection($db_link);

	//$exe_file = $GLOBALS['site_shell_url'] . "crons/gera_brandsdestaque.php";
	//exec('php '.$exe_file.' > /dev/null &');
	return($res);
}
function InsertNewRecordLinguas($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(!isset($_POST['m_lang']) || $_POST['m_lang']=='') $m_lang = $admin_lang;
	else  $m_lang = $_POST['m_lang'];
	$nome = $_POST['nome'];
	$Texto1 = $_POST['Texto1'];
	$Texto2 = $_POST['Texto2'];


	if($_POST['slashes']=="'")
	{
		$nome=addslashes($nome);
		$Texto1=addslashes($Texto1);
		$Texto2=addslashes($Texto2);
		$m_lang=addslashes($m_lang);
	}

	$sql = '
			INSERT INTO
				brands_lang
				(
					ID_PAI,
					Nome,
					Texto1,
					Texto2,
					Lang
				)
				VALUES
				(
					"'.$id_pai.'",
					"'.$nome.'",
					"'.$Texto1.'",
					"'.$Texto2.'",
					"'.$m_lang.'"
				)
			';
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link).$sql;
    return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id = intval($_POST['ID']);

    $sql = "DELETE FROM brands WHERE ID=$id";
	$res = Runquery($db_link,$DB_name,$sql);
	if($res)
	{
        $sql2 = "DELETE FROM brands_lang WHERE ID_PAI=$id";
	    $res2 = Runquery($db_link,$DB_name,$sql2);
	}
	else echo db_error($db_link);


	//$exe_file = $GLOBALS['site_shell_url'] . "crons/gera_brandsdestaque.php";
	//exec('php '.$exe_file.' > /dev/null &');
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$rs = new DataObj;

	if(!isset($_POST['m_lang']) || $_POST['m_lang']=='') $m_lang = $admin_lang;
	else  $m_lang = $_POST['m_lang'];

	$id  			= intval($_POST['ID']);
	$visivel 		= intval($_POST['visivel']);
	$preco_revenoff = intval($_POST['preco_revenoff']);
	$tipo			= intval($_POST['tipo']);
	$ordem 			= intval($_POST['ordem']);
	$tem_foto1 		= intval($_POST['tem_foto1']);
	$tem_foto2 		= intval($_POST['tem_foto2']);
	$Apagar_img1 	= intval($_POST['Apagar_img1']);
	$Apagar_img2 	= intval($_POST['Apagar_img2']);
	$ref_integracao = $_POST['ref_integracao'];

	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from brands WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();

	if(!$altera) return InsertNewRecord();
	else
	{
		$altera_img=0; if($GLOBALS['Img_peq1']["name"]){ $altera_img=1; $pos = strrpos($GLOBALS['Img_peq1']["name"],"."); $img_ext = substr($GLOBALS['Img_peq1']["name"],$pos+1,strlen($GLOBALS['Img_peq1']["name"])+1-$pos); }
		if($tem_foto1 && $Apagar_img1){$ficheiro1 = $id."_1.".$img_ext;unlink($GLOBALS['img_path'].$ficheiro1); if(!$altera_img) $img_ext='';}
		if($altera_img) copia_segura($GLOBALS['Img_peq1']["temp"],$GLOBALS['img_path'].$id."_1.".$img_ext);


		$altera_img2=0; if($GLOBALS['Img_peq2']["name"]){ $altera_img2=1; $pos = strrpos($GLOBALS['Img_peq2']["name"],"."); $img_ext2 = substr($GLOBALS['Img_peq2']["name"],$pos+1,strlen($GLOBALS['Img_peq2']["name"])+1-$pos); }
		if($tem_foto2 && $Apagar_img2){$ficheiro1 = $id."_2.".$img_ext;unlink($GLOBALS['img_path'].$ficheiro1); if(!$altera_img2) $img_ext2='';}
		if($altera_img2) copia_segura($GLOBALS['Img_peq2']["temp"],$GLOBALS['img_path'].$id."_2.".$img_ext2);


		$sql = '
				UPDATE
					brands
				SET
					Visivel="'.$visivel.'",
					img_ext="'.$img_ext.'",
					img_ext2="'.$img_ext2.'",
					Ordem="'.$ordem.'",
					preco_revenoff="'.$preco_revenoff.'",
					tipo="'.$tipo.'",
					ref_integracao="'.$ref_integracao.'"
				WHERE
					ID="'.$id.'"
				';
		$res = Runquery($db_link,$DB_name,$sql);
		ChangeRecordLinguas();
		db_closedb_conection($db_link);

		$exe_file = $GLOBALS['site_shell_url'] . "crons/gera_brandsdestaque.php";
		exec('php '.$exe_file.' > /dev/null &');
		return($res);
	}
}
function ChangeRecordLinguas()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	if(!isset($_POST['m_lang']) || $_POST['m_lang']=='') $m_lang = $admin_lang;
	else  $m_lang = $_POST['m_lang'];
	$id_pai = intval($_POST['ID']);
	$nome = $_POST['nome'];
	$Texto1 = $_POST['Texto1'];
	$Texto2 = $_POST['Texto2'];

	if($_POST['slashes']=="'")
	{
		$nome=addslashes($nome);
		$Texto1=addslashes($Texto1);
		$Texto2=addslashes($Texto2);
		$m_lang=addslashes($m_lang);
	}

	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from brands_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF()) $altera = 1;
	$rs->db_close();
	if(!$altera) return InsertNewRecordLinguas($id_pai);
	else
	{
		$sql = "Update brands_lang Set Nome='$nome',Texto1='$Texto1',Texto2='$Texto2' where ID_PAI='$id_pai' and Lang='$m_lang'";
		$res = Runquery($db_link,$DB_name,$sql);
		return($res);
	}
}


function GetRecords_Recursos($id=0,$id_org=0,$campos="*",$first=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id!=0)
	{
		$cond="where ID=$id";
		if($id_org!=0 || $first!=0)	$cond="$cond and ID_ORG=$id_org";
	}
	else
	{
		$cond="where ID > 0";
		if($id_org!=0 || $first!=0)	$cond=$cond." and ID_ORG=$id_org";
	}
    $sql="Select $campos from brands $cond order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
function show_menupath($id_org,$table="brands",$pagina="pagina.php")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT * FROM $table WHERE ID=$id_org";
		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["ID"];
		$id_org1=$rs->db_data["ID_ORG"];
		$rs->db_close();
		$sql="SELECT * FROM $table"."_lang WHERE ID_PAI=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["URL_Nome2"]);
		$C1=$C!="" ? $C : "[EDIT]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1);
		$str.=" - <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&Dumpe=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" - <a href=\"list.php?Dump=".uniqid('')."\" class=\"new_caminho\">Menus</a>";
	return $str;
}
//BRAND FIX
function show_content_list($info)
{
	$count++;

	$count = count($info);
	for($i=1; $i<=$count ; $i++)
	{
		$IDb = $info[$i]["ID_PAI"];
		$descricao = $info[$i]["NAME"];
		$visivel = $info[$i]["Visible"];
		$ordem = $info[$i]["Ordem"];
		if(!$descricao) $descricao="N/A";
		if($i % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
	?>
    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
        <td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>


		<td width="10"></td>
	    <td valign="middle" align="left"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="preto" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=0&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";?></a></td>
        <td valign="middle" width="35"><?
		$imgvar="";
		$imgvar .='<img title="';
		if($visivel)$imgvar .=write_lang('key_pagina_visivel','Página Visivel');
		else $imgvar .=write_lang('key_pagina_invisivel','Página Invisivel');
		$imgvar .='" src="';
		if($visivel) $imgvar .= "ok.png";
		else $imgvar .= "hidden.png";
		$imgvar .='" border=0 align="absmiddle" />';
		echo $imgvar;?>
        </td>
	    <td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>

		<td class="ordem<?=$n?>" style="text-align:center"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
	</tr>
	</table>
	<?
	}
}

function show_content_list_destaques()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql="SELECT b.ID,b.Visivel,bl.Nome,bo.* FROM  brands b inner join brands_lang bl on bl.ID_PAI=b.ID left join brands_destaques bo on bo.ID_Brand=b.ID WHERE bl.Lang='".$admin_lang."' order by bo.Ordem ASC,bl.Nome ASC";
	$rs2->db_open($db_link,$DB_name,$sql);
	while(!$rs2->EOF())
	{
		$IDb 		= $rs2->db_data["ID"];
		$IDbrand 	= $rs2->db_data["ID_Brand"];
		$ordem 		= $rs2->db_data["Ordem"];
		if($IDbrand>0)$checked=' checked="checked" ';
		else{
			$checked='';
			$ordem=0;
		}
		$descricao 	= $rs2->db_data["Nome"];
		$visivel 	= $rs2->db_data["Visivel"];
		if(!$descricao) $descricao="N/A";
		if($i % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" <?=$checked?>></td>

		<td width="29" align="center"><img src="../images/page.png"></td>

		<td width="5"></td>
		<td valign="middle" width="35">
		<?
		$imgvar="";
		$imgvar .='<img title="';
		if($visivel)$imgvar .=write_lang('key_pagina_visivel','Página Visivel');
		else $imgvar .=write_lang('key_pagina_invisivel','Página Invisivel');
		$imgvar .='" src="';
		if($visivel) $imgvar .= "ok.png";
		else $imgvar .= "hidden.png";
		$imgvar .='" border=0 align="absmiddle" />';
		echo $imgvar;?>
		</td>
		<td valign="middle" align="left" ><? while($i<$contador){echo "-";$i++;}?><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";?></td>
		<td  width="20" align="right" class=preto></td>
		<td width="20" align="right" class=preto><input type="text" name="field_<?=$IDb?>" style="width:35px;" value="<?=$ordem?>"></td>

		</tr>
		</table><?
		$rs2->db_next();
	}
	$rs2->db_close();
}
function show_content_list_offers()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql="SELECT b.ID,b.Visivel,bl.Nome,bo.* FROM  brands b inner join brands_lang bl on bl.ID_PAI=b.ID left join brands_offers bo on bo.ID_Brand=b.ID  WHERE bl.Lang='".$admin_lang."' order by bo.Ordem ASC,bl.Nome ASC";
	$rs2->db_open($db_link,$DB_name,$sql);
	while(!$rs2->EOF())
	{
		$IDb 		= $rs2->db_data["ID"];
		$IDbrand 	= $rs2->db_data["ID_Brand"];
		$ordem 		= $rs2->db_data["Ordem"];
		if($IDbrand>0)$checked=' checked="checked" ';
		else
		{
			$checked='';
			$ordem=0;
		}
		$descricao 	= $rs2->db_data["Nome"];
		$visivel 	= $rs2->db_data["Visivel"];
		if(!$descricao) $descricao="N/A";
		if($i % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" <?=$checked?>></td>

		<td width="29" align="center"><img src="../images/page.png"></td>

		<td width="5"></td>
		<td valign="middle" width="35"><?
		$imgvar="";
		$imgvar .='<img title="';
		if($visivel)$imgvar .=write_lang('key_pagina_visivel','Página Visivel');
		else $imgvar .=write_lang('key_pagina_invisivel','Página Invisivel');
		$imgvar .='" src="';
		if($visivel) $imgvar .= "ok.png";
		else $imgvar .= "hidden.png";
		$imgvar .='" border=0 align="absmiddle" />';
		echo $imgvar;?>
		</td>
		<td valign="middle" align="left" ><? while($i<$contador){echo "-";$i++;}?><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";?></td>
		<td  width="20" align="right" class=preto></td>
		<td width="20" align="right" class=preto><input type="text" name="field_<?=$IDb?>" style="width:35px;" value="<?=$ordem?>"></td>

		</tr>
		</table>
		<?
		$rs2->db_next();
	}
	$rs2->db_close();
}
function show_content_list2($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador += 3;
	$rs=new DataObj;$rs2=new DataObj;
	if($tipos==1) $sql="Select * from brands where ID='".$pai."' order by ID"; /// Paginas publicas
	elseif($tipos==2) $sql="Select * from brands where ID='".$pai."' order by ID"; /// Paginas privadas
	elseif($tipos==3) $sql="Select * from brands where ID='".$pai."' order by ID"; /// noticias
	else $sql="Select * from brands where ID_ORG='".$pai."' order by ID";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$ID_ORG=$rs->db_data["ID_ORG"];
		$visivel=$rs->db_data["Visivel"];
		$Protected=$rs->db_data["Protected"];
		$descricao='Zone '.$rs->db_data["Ordem"];
		$qtd = explode('-',$rs->db_data["Largura"]);
		$preco=$rs->db_data["Altura"];
		$descricao = 'From: '.$qtd[0].' - To: '.$qtd[1].' / &euro;'.number_format($preco,2,',','.');
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>

            <td width="29" align="center"><?
			if($tipo==0) echo '<img src="../images/page.png" />';
			if($tipo==1) echo '<img src="../images/page_priv.png" />';
			if($tipo==2) echo '<img src="../images/page_news.png" />';

			?></td>

			<td width="25"><?
				$rs2=new DataObj;
				$sql="Select * from brands where ID=".$rs->db_data["ID"];
				$db_res=$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF() && $pai==0){?><img alt="<? echo write_lang('key_ver_mais_paginas','Ver mais páginas')?>" src="../images/<? if($pai==0) echo "more.gif"; else echo "bullet.gif";?>" border=0><? }
				else echo '<img width="22" height="8" src="../images/blank.gif" />';
				$rs2->db_close();?>
			</td>
			<td valign="middle" width="35">&nbsp;</td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="preto" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";?></a></td>
			<td width="20" align="right" class=preto><!--<a class="italico" href="pagina.php?ID=<? //$IDb?>&ID_ORG=<? //$ID_ORG;?>&task=duplicar&Dump=<? //echo uniqid("");?>"><img title="<? //echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a>--></td>
			<td width="20" align="right" class=preto><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a><? }else echo '<img title="" src="locked.png"  border=0 align="absmiddle" />'; ?></td>

		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function show_content_from_search($reg)
{
if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs_lang=new DataObj;
	$sql="Select distinct(brands.ID) as IDb,brands.*,brands_lang.Nome from brands inner join brands_lang on brands.ID=brands_lang.ID_PAI where ID_PAI='".$pai."' and lang='$admin_lang' AND ID_PAI IN($reg) order by Nome ASC";
	$rs_lang->db_open($db_link,$DB_name,$sql);
	$count=0;
	while(!$rs_lang->EOF())
	{	$count++;
		$IDb=$rs_lang->db_data["IDb"];
		$id_org=$rs_lang->db_data["ID_ORG"];
		$id_pai=$rs_lang->db_data["ID_PAI"];
		$visivel=$rs_lang->db_data["Visivel"];
		$ordem=$rs_lang->db_data["Ordem"];
		$tipo=$rs_lang->db_data["Tipo"];
		$descricao=$rs_lang->db_data["URL_Nome"];
		$Protected=$rs_lang->db_data["Protected"];
		$T=$rs_lang->db_data["Texto"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
	?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" /></td>

        <td width="29" align="center"><?
			if($tipo==0) echo '<img src="../images/page.png" />';
			if($tipo==1) echo '<img src="../images/page_priv.png" />';
			if($tipo==2) echo '<img src="../images/page_news.png" />';

			?></td>
		<td valign="middle" width="35">

		<?
		while($i<$contador)
		{
			echo "&nbsp;";
			$i++;
		}?>
        <img title="<? if($visivel) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" />

        </td>

            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";?></a></td>
			<td width="72" align="right" class=preto><!--<a class="italico" href="pagina.php?ID=<? //$IDb?>&ID_ORG=<? //$ID_ORG;?>&task=duplicar&Dump=<? //echo uniqid("");?>"><img title="<? //echo write_lang('key_editar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a>--></td>
			<td width="100" align="right" class=preto><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0 /></a><? }else echo '<img title="" src="locked.png"  border=0 align="absmiddle" />'; ?></td>

			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
	  </tr>
	  </table><?
	  $rs_lang->db_next();
  }
  $rs_lang->db_close();
}
?>