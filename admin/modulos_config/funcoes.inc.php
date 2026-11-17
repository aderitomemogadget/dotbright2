<?
function show_page_buttons()
{
	global $task_button;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);">
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
		<td width=29 class="text-center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" class="text-center"></td>
		<td width="91px" class="t1">Pesquisar:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"></td>
		<td width="28px"><input type="submit" value="Submeter pesquisa" src="../images/btp.jpg" class="btn btn-primary" ></td>
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
		<td width="125px"><select name="flag" id="flag" class="form-control">
				<option value="" selected="selected"></option>
				<option value="1">Apagar</option>
				<!--option value="2">Reordenar</option>
				<option value="4">Tornar visível</option>
				<option value="5">Tornar invisível</option-->
			</select>
		</td>
        <td width="5px"></td>
		<td>&nbsp;<input type="submit"  name="submeter" value="Aplicar Ação" class="btn btn-primary"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools()
{?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"></td>
		<td class="titulo2 text-center">FAST TOOLS</td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" value="Inserir novo elemento na CSS" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid('');?>','_self')"></td>
		<td width="5px" align="right"></td>
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
	$sql="Select * from modulos_config";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Tipo=$rs->db_data["Tipo"];
		$Nome=$rs->db_data["Nome"];
		$Valor=$rs->db_data["Valor"];

		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="text-center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
            <td valign="middle">
            	&nbsp;<a title="editar" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&d=<?=uniqid('');?>"><? if($Nome){echo htmlentities_b(stripslashes($Nome));} else echo "[Editar]";?></a>
            	---> <?php if(!$Tipo) echo 'GENERICA';else echo 'CLASSE';?>
            </td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&d=<?=uniqid('');?>"><img title="apagar" src="del.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$Tipo?>"></td>
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
	$sql="Select * from modulos_config WHERE ID IN($reg) order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Nome=$rs->db_data["Nome"];
		$Tipo=$rs->db_data["Tipo"];
		$Valor=$rs->db_data["Valor"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="text-center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&d=<?=uniqid('');?>"><? if($Nome){echo htmlentities_b(stripslashes($Nome));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&d=<?=uniqid('');?>"><img title="apagar lingua" src="del.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$Tipo?>"></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord($Tipo,$Nome,$Valor)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql="insert into modulos_config (Tipo,Nome,Valor) values ('$Tipo','$Nome','$Valor')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from modulos_config where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$Tipo,$Nome,$Valor)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from modulos_config WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord($Tipo,$Nome,$Valor);
	}
	else
	{
		$sql="Update modulos_config Set Tipo='$Tipo',Valor='$Valor',Nome='$Nome' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function regera_css2()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from modulos_config";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;

	$fh = fopen('../../files/classes.css', 'w');
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Nome=$rs->db_data["Nome"];
		$Tipo=$rs->db_data["Tipo"];
		$Valor=$rs->db_data["Valor"];

		fwrite($fh, $Nome.' {'.$Valor.'}');
		//fwrite($fh, $Nome.' !important{'.$Valor.'}');

		$rs->db_next();
		$i++;
	}

	fclose($fh);
	chmod($cache_filename, 0755);

	$rs->db_close();
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID,
	lower(Nome) as Descricao,length(lower(Nome)) AS len,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."'))/(length(lower(Nome))+1-length(replace(lower(Nome),' ','')) ) AS occwds,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."')) AS occ
	FROM modulos_config
	WHERE (lower(Nome) LIKE '%".$d."%' OR Valor like '%".$d."%' ) ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
?>