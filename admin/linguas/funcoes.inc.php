<?
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<?
}
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">LINGUAS</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 class="text-center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" class="text-center"></td>
		<td width="91px" class="t1">Pesquisar:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="Submeter pesquisa" class="btn btn-primary" ></td>
		<td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;Com os selecionados:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1">Apagar</option>
				<option value="2">Reordenar</option>
				<option value="4">Tornar visível</option>
				<option value="5">Tornar invisível</option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familia(0,"pages","pages_lang");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="Aplicar Ação" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2 text-center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Criar nova lingua" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<? }
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select * from linguas";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Sigla=$rs->db_data["Sigla"];
		$ordem=$rs->db_data["Ordem"];
		$Visivel=$rs->db_data["Visivel"];
		$designacao=$rs->db_data["Cabecalho"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="text-center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="35" align="center" valign="middle"><img title="<? if($Visivel) echo "visivel"; else echo "invisivel ";?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>
			<td width="200" align="right" class=preto><a target="_new" class="italico" href="../../index.php?site_lingua=<?=$Sigla?>">Ver site nesta lingua</a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar lingua" src="del.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
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
	$sql="Select * from linguas WHERE ID IN($reg) order by Ordem,Cabecalho ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Sigla=$rs->db_data["Sigla"];
		$ordem=$rs->db_data["Ordem"];
		$designacao=$rs->db_data["Cabecalho"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="text-center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="editar lingua" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar lingua" src="del.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord($ordem,$sigla,$cabecalho,$visivel)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho=str2SQLstr($cabecalho);
	$sigla=str2SQLstr($sigla);
	$visivel=str2SQLstr($visivel);
	$sql="insert into linguas (Ordem,Sigla,Cabecalho,Visivel) values ('$ordem',$sigla,$cabecalho,$visivel)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from linguas where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$ordem,$sigla,$cabecalho,$visivel)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from linguas WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord($ordem,$sigla,$cabecalho,$visivel);
	}
	else
	{	$cabecalho=str2SQLstr($cabecalho);
		$sigla=str2SQLstr($sigla);
		$visivel=str2SQLstr($visivel);
		$sql="Update linguas Set Ordem='$ordem',Cabecalho=$cabecalho,Sigla=$sigla,Visivel=$visivel where ID=$id";
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
	$sql="SELECT distinct ID,
	lower(Cabecalho) as Descricao,length(lower(Cabecalho)) AS len,
	((length(Cabecalho)-length(replace(lower(Cabecalho),'".$d."','')))/length('".$d."'))/(length(lower(Cabecalho))+1-length(replace(lower(Cabecalho),' ','')) ) AS occwds,
	((length(Cabecalho)-length(replace(lower(Cabecalho),'".$d."','')))/length('".$d."')) AS occ
	FROM linguas
	WHERE (lower(Cabecalho) LIKE '%".$d."%' OR Sigla like '%".$d."%' ) ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
?>