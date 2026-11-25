<?php
function show_lista_users($ID_Grupo,$tabela,$campo_name)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if($dados['Grupo_ID']!=1) $where='WHERE ID!=1';
	else $where='';

	$rs->db_open($db_link,$DB_name,'Select * from '.$tabela.' '.$where);
	echo '<select name="'.$campo_name.'">';
	// if($id_org==0) echo '<option value="0" selected>- all -</option>';
	// else echo '<option value="0">- all -</option>';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if($ID_Grupo==$rs->db_data['ID']) echo ' selected ';
		echo '>';
		echo $rs->db_data['Descricao'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}

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
		<td width="28px"><input type="submit" value="Submeter pesquisa" src="../images/btp.jpg" class="botao" style="width:120px;height:20px"></td>
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
				<option value="2">Mover para o Grupo</option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_lista($Grupo_ID,"grupos_logins","Grupo_ID");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="Aplicar Ação" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools()
{
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);

	?>
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
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Criar novo utilizador" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<?php if(isset($dados['Permissoes'][2]) && $dados['Permissoes'][2]==1){ ?>
	<tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Gerir Grupos" onclick="javascript:window.open('../grupos_users/list.php','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>

	<?php } ?>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	global $ID, $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if($dados['Grupo_ID']==1){
		$sql="Select * from logins";
	}
	else
	{
		$sql="Select * from logins where Grupo_ID!=1";
	}
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Grupo_ID=$rs->db_data["Grupo_ID"];
		$designacao=$rs->db_data["Nome"];
		$Estado=$rs->db_data["estado"];
		$tecnico=$rs->db_data["tecnico"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;

		if($Grupo_ID)
		{
			$sql="Select * from grupos_logins WHERE ID=$Grupo_ID";
			$db_res=$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())$grupo_nome=$rs2->db_data["Descricao"];
			$rs2->db_close();
		}
		else $grupo_nome="";

		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><img title="editar utilizador" src="<? if($Estado) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>

            <td width="160" align="center"><? if($grupo_nome){echo htmlentities_b(stripslashes($grupo_nome));} else echo "";?></td>

			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar utilizador" src="del2.png" border=0></a></td>

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
	$sql="Select * from logins WHERE ID IN($reg) order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Sigla=$rs->db_data["Sigla"];
		$designacao=$rs->db_data["Nome"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="editar utilizador" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar utilizador" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>

            <td width="160" align="center"><? if($grupo_nome){echo htmlentities_b(stripslashes($grupo_nome));} else echo "";?></td>

			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar utilizador" src="del.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord($Username,$Password,$Nome,$Estado,$tecnico,$Grupo_ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$Username=str2SQLstr($Username);
	$Nome=str2SQLstr($Nome);
	$Estado=str2SQLstr($Estado);
	$tecnico=str2SQLstr($tecnico);
	$Grupo_ID=str2SQLstr($Grupo_ID);
	if($Password)
	{
		$Password=str2SQLstr(encripta($Password));
		$sql="insert into logins (username,password,Nome,estado,tecnico,Grupo_ID) values ($Username,$Password,$Nome,$Estado,$tecnico,$Grupo_ID)";
	}
	else $sql="insert into logins (username,Nome,estado,tecnico,Grupo_ID) values ($Username,$Nome,$Estado,$tecnico,$Grupo_ID)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from logins where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$Username,$Password,$Nome,$Estado,$tecnico,$Grupo_ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from logins WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord($Username,$Password,$Nome,$Estado,$tecnico,$Grupo_ID);
	}
	else
	{
		$Nome=str2SQLstr($Nome);
		$Estado=str2SQLstr($Estado);
		$Grupo_ID=str2SQLstr($Grupo_ID);
		$tecnico=str2SQLstr($tecnico);
		if($Password)
		{
			$Password=str2SQLstr(encripta($Password));
			$sql="Update logins Set Nome=$Nome,estado=$Estado,tecnico=$tecnico,Grupo_ID=$Grupo_ID,password=$Password where ID=$id";
		}
		else $sql="Update logins Set Nome=$Nome,estado=$Estado,tecnico=$tecnico,Grupo_ID=$Grupo_ID where ID=$id";
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
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if($dados['Grupo_ID']!=1){
		$and = ' AND Grupo_ID!=1 ';
	}
	$sql='SELECT * FROM logins
	WHERE (lower(username) LIKE "%'.$d.'%" or lower(Nome) LIKE "%'.$d.'%") '.$and.' ORDER BY username DESC,Nome DESC';
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
?>