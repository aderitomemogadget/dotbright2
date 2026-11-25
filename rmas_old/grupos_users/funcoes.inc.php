<?
function show_page_buttons()
{
	global $task_button;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1">
        	<input class="botao" type="submit" name="Submit" value=" <?=$task_button;?> ">
        	<input class="botao" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_page_tools($filter=0)
{?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1">Pesquisar:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="pesq" style="height:20px"></td>
		<td width="28px"><input type="submit" value="submeter pesquisa" src="../images/btp.jpg" class="botao" style="width:120px;height:20px"></td>
		<td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;Com os seleccionados:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1">Apagar</option>
			</select>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="Aplicar Ação" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools()
{?>
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
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="criar novo grupo" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if($dados['Grupo_ID']!=1) $where='WHERE ID!=1';
	else $where='';
	$sql="Select * from grupos_logins ".$where;
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$designacao=$rs->db_data["Descricao"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><img title="editar grupo" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar grupo" src="del.png" border=0></a></td>
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
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if($dados['Grupo_ID']!=1) $and='AND ID!=1';
	else $and='';
	$sql="Select * from grupos_logins WHERE ID IN($reg) ".$and." order by Descricao ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$designacao=$rs->db_data["Descricao"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="editar grupo" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar grupo" src="del.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_POST['slashes']) && $_POST['slashes']=="'")
	{
		$Descricao=addslashes($_POST['Descricao']);
	}
	$Loja=addslashes($_POST['Loja']);

	$Permissoes=encodePermissoes($_POST);

	$sql='insert into grupos_logins (Descricao,Permissoes,Loja) values ("'.$Descricao.'","'.$Permissoes.'","'.$Loja.'")';
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$id=$_GET['ID'];
	$res=Runquery($db_link,$DB_name,"delete from grupos_logins where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$id=$_GET['ID'];
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from grupos_logins WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord();
	}
	else
	{
		if(isset($_POST['slashes']) && $_POST['slashes']=="'")
		{
			$Descricao=addslashes($_POST['Descricao']);
		}
		$Loja=addslashes($_POST['Loja']);

		$Permissoes=encodePermissoes($_POST);

		$sql='Update grupos_logins Set Descricao="'.$Descricao.'",Permissoes="'.$Permissoes.'",Loja="'.$Loja.'" where ID='.$id;
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}

function encodePermissoes($v_perms)
{
	$Permissoes='';
	foreach($v_perms as $varName=> $value)
	{
		$str_id=substr($varName,strpos($varName,"_")+1);
		if($str_id > 0 && substr_count($varName,"modulos_") > 0)
		{
			$Permissoes.=$str_id.'_'.$v_perms['modulos_'.$str_id].'|';
		}
	};
	$Permissoes = substr($Permissoes,0,-1);
	return $Permissoes;
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="select distinct ID,
	lower(Descricao) as Descricao,length(lower(Descricao)) AS len,
	((length(Descricao)-length(replace(lower(Descricao),'".$d."','')))/length('".$d."'))/(length(lower(Descricao))+1-length(replace(lower(Descricao),' ','')) ) AS occwds,
	((length(Descricao)-length(replace(lower(Descricao),'".$d."','')))/length('".$d."')) AS occ
	FROM grupos_logins
	WHERE (lower(Descricao) LIKE '%".$d."%') ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
?>