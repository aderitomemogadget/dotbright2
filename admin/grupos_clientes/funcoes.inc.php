<?php
function show_page_buttons()
{
	global $task_button;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn btn-primary" type="submit" name="Submit" value="<? echo $task_button;?>">
        	<input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">GRUPO DE CLIENTES</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<? echo $_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" class="btn btn-primary" ></td>
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
			</select>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Criar Novo Grupo" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<? echo uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select * from grupos_clientes";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if(!$rs->EOF()){
		while(!$rs->EOF())
		{
			$count++;
			$IDb=$rs->db_data["ID"];
			$designacao=$rs->db_data["Descricao"];
			if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
			else				{$cor="#FFFFFF";$class="r2";$n="2";}
			$i=0;
			?><table border=0 cellspacing=0 cellpadding=0 class="<? echo $class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<? echo $cor;?>'">
				<td width="30" align="center"><input type="checkbox" name="chk_<? echo $IDb?>" id="chk_<? echo $IDb?>" value="<? echo $IDb?>" class="cb"></td>
				
	            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<? echo $IDb;?>&ID_ORG=<? echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Edit]";?></a></td>
				<td valign="middle" width="30"><img title="<? echo write_lang('key_editar_grupo','Editar Grupo')?>" src="ok.png" border=0 align="absmiddle"></td>
				<td width="30" align="center" class=preto><a class="italico" href="pagina.php?ID=<? echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_editar_pagina','Apagar Grupo')?>" src="del2.png" border=0></a></td>
			</tr>
			</table><?
			$rs->db_next();
			$i++;
		}
	}else{
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
			else				{$cor="#FFFFFF";$class="r2";$n="2";}
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<? echo $class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<? echo $cor;?>'">
				<td width="29" align="left" class="preto">Sem Grupos</td>
			</tr>
			</table>
	<?}

	$rs->db_close();
}
function show_content_from_search($reg)
{
if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from grupos_clientes WHERE ID IN($reg) order by Descricao ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$designacao=$rs->db_data["Descricao"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<? echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<? echo $cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<? echo $IDb?>" id="chk_<? echo $IDb?>" value="<? echo $IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<? echo $n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<? echo write_lang('key_editar_grupo','Editar Grupo')?>" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<? echo $IDb;?>&ID_ORG=<? echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Edit]";?></a></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<? echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar grupo" src="del2.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord($Descricao)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$Descricao = addslashes($_POST['Descricao']);

	$sql="insert into grupos_clientes (Descricao) values ('$Descricao')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id = $_POST['ID'];
	$res = Runquery($db_link,$DB_name, "delete from grupos_clientes where ID=$id");
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$Descricao)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;

	$id = $_POST['ID'];
	$Descricao = addslashes($_POST['Descricao']);

	$rs->db_open($db_link,$DB_name,"Select * from grupos_clientes WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord($Descricao);
	}
	else
	{

		$sql="Update grupos_clientes Set Descricao='$Descricao' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
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
	FROM grupos_clientes
	WHERE (lower(Descricao) LIKE '%".$d."%') ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
?>