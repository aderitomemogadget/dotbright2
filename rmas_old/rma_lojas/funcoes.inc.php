<?
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?

				echo '<input class="botao" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="botao" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="botao" type="button" name="Apagar" value="<? echo write_lang('key_apagar','Apagar')?>" onMouseUp="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="botao" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools()
{
	global $ID;?>
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
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Criar nova Localização" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="pesq" style="height:20px"></td>
		<td width="28px"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" src="../images/btp.jpg" class="botao" style="width:120px;height:20px"></td>
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
				<option value="2"><? echo write_lang('key_reordenar','Reordenar')?></option>
				<option value="4"><? echo write_lang('key_tornar_visivel','Tornar visivel')?></option>
				<option value="5"><? echo write_lang('key_tornar_invisivel','Tornar invisivel')?></option>
			</select>
		</td>
        <td width="5px"></td>

		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID,
	lower(Texto) as Descricao,length(lower(Texto)) AS len,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."'))/(length(lower(Texto))+1-length(replace(lower(Texto),' ','')) ) AS occwds,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."')) AS occ
	FROM menus_links_lang
	WHERE ((lower(Texto) LIKE '%".$d."%' OR URL_Nome like '%".$d."%') AND Lang='$admin_lang' ) ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_options($id_org=0,$id_first=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia2" style="width:60%; height:16px">';
	if($id_org==0) echo '<option value="0" selected>- '.write_lang('key_nenhuma','Nenhuma').' -</option>';
	else echo '<option value="0">- '.write_lang('key_nenhuma','Nenhuma').' -</option>';
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
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$zone_type = $_POST['zone_type'];
	$zone_order = $_POST['zone_order'];
	$zone_visible = $_POST['zone_visible'];

	$sql = "insert into rma_lojas(zone_type,zone_order,zone_visible) values ('$zone_type','$zone_order','$zone_visible')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);
		InsertNewRecordLinguas($new_id);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function InsertNewRecordLinguas($zone_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang) $m_lang = $admin_lang;
	$zone_name = addslashes($_POST['zone_name']);
	$sql = "insert into rma_lojas_lang (zone_id,zone_name,zone_morada,zone_phone,zone_email,zone_movel,zone_web,zone_namesmall,zone_codpos,zone_fax,zone_logo,zone_lang)
	values ($zone_id,'$zone_name','$zone_morada','$zone_phone','$zone_email','$zone_movel','$zone_web','$zone_namesmall','$zone_codpos','$zone_fax','$zone_logo','$m_lang')";

	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
    return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$zone_id = $_POST['zone_id'];

    $sql="delete from rma_lojas where zone_id=$zone_id";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
        $sql2="delete from rma_lojas_lang where zone_id=$zone_id";
	    $res2=Runquery($db_link,$DB_name,$sql2);
	}
	else echo db_error($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;

	$zone_id = $_POST['zone_id'];
	$zone_type = $_POST['zone_type'];
	$zone_order = $_POST['zone_order'];
	$zone_visible = $_POST['zone_visible'];

	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from rma_lojas WHERE zone_id='$zone_id'");
	if(!$rs->EOF()) $altera=1;
	$rs->db_close();

	if(!$altera) return InsertNewRecord();
	else
	{
		$sql="Update rma_lojas Set zone_type='$zone_type',zone_order='$zone_order',zone_visible='$zone_visible' where zone_id=$zone_id";
		$res = Runquery($db_link,$DB_name,$sql);
		ChangeRecordLinguas($zone_id);
		db_closedb_conection($db_link);
		return($res);
	}
}
function ChangeRecordLinguas($zone_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang) $m_lang = $admin_lang;
	$zone_name = addslashes($_POST['zone_name']);
	$zone_morada=addslashes($_POST['zone_morada']);
	$zone_phone=addslashes($_POST['zone_phone']);
	$zone_email=addslashes($_POST['zone_email']);
	$zone_movel=addslashes($_POST['zone_movel']);
	$zone_web=addslashes($_POST['zone_web']);
	$zone_namesmall=addslashes($_POST['zone_namesmall']);
	$zone_codpos=addslashes($_POST['zone_codpos']);
	$zone_fax=addslashes($_POST['zone_fax']);
	$zone_logo=addslashes($_POST['zone_logo']);


	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from rma_lojas_lang WHERE zone_id='$zone_id' and zone_lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();
	if(!$altera)return InsertNewRecordLinguas($zone_id);
	else
	{
		$sql="Update rma_lojas_lang Set zone_name='$zone_name',
		zone_morada='$zone_morada',zone_phone='$zone_phone',
		zone_email='$zone_email',zone_movel='$zone_movel',
		zone_web='$zone_web',zone_namesmall='$zone_namesmall',
		zone_codpos='$zone_codpos',zone_fax='$zone_fax',zone_logo='$zone_logo'
 		where zone_id='$zone_id' and zone_lang='$m_lang'";

		//echo $sql;
		$res=Runquery($db_link,$DB_name,$sql);
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
    $sql="Select $campos from menus_links $cond order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
function show_menupath($id_org,$table="menus_links",$pagina="pagina.php")
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
		$str.=" - <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&Dump=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" - <a href=\"list.php?Dump=".uniqid('')."\" class=\"new_caminho\">Menus</a>";
	return $str;
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos,$where_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select * from rma_lojas order by zone_order,zone_type $where_loja";
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$zone_id=$rs->db_data["zone_id"];
		$zone_type=$rs->db_data["zone_type"];
		$zone_visible=$rs->db_data["zone_visible"];
		$zone_order=$rs->db_data["zone_order"];

		$sql="Select * from rma_lojas_lang where zone_id='".$zone_id."' and zone_lang='$admin_lang'";
		$rs2->db_open($db_link,$DB_name,$sql);
		$zone_name=$rs2->db_data["zone_name"];
		$rs2->db_close();

		if(!$zone_name) $descricao="N/A";

		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}

		$i=0;
		?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$zone_id?>" id="chk_<?=$zone_id?>" value="<?=$zone_id?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td width="5"></td>
			<td valign="middle" width="35"><img title="<? if($zone_visible) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($zone_visible) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
            <td valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?zone_id=<?=$zone_id;?>&Dump=<? echo uniqid("");?>"><? if($zone_name){echo htmlentities_b(stripslashes($zone_name));} else echo "[Edit]";?></a></td>
			<!-- <td class="ordem<?=$n?>"><select name="zone_type_<?=$zone_id?>"><? for($a=0;$a<9;$a++){?><option value="<? echo $a;?>" <? if($zone_type==$a){?>selected="selected"<? }?>>Zone <? echo $a;?></option><? }?></select></td> -->
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$zone_id?>" value="<?=$zone_order?>" /></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?zone_id=<?=$zone_id?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
?>