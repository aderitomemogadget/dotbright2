<?
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
<?
}
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">UTILIZADORES</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
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
		<td width="250px" class="t1">&nbsp;Com os selecionados:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1">Apagar</option>
				<option value="2">Mover para o Grupo</option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="150px"><? show_lista($Grupo_ID,"grupos_logins","Grupo_ID");?><style>
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
{
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Criar novo utilizador" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	<?php if(isset($dados['Permissoes'][2]) && $dados['Permissoes'][2]==1){ ?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Gerir Grupos" onclick="javascript:window.open('../grupos_users/list.php','_self')"></td>
	</tr>
	<?php } ?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
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
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
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
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>
			<td valign="middle" width="35"><img title="editar utilizador" src="<? if($Estado) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle"></td>
            <td width="160" align="center"><? if($grupo_nome){echo htmlentities_b(stripslashes($grupo_nome));} else echo "";?></td>
			<td width="30" align="center" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar utilizador" src="del2.png" border=0></a></td>
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
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
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
function InsertNewRecord($Username,$Password,$Nome,$Estado,$Grupo_ID,$Email='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$Username=str2SQLstr($Username);
	$Nome=str2SQLstr($Nome);
	$Estado=str2SQLstr($Estado);
	$Grupo_ID=str2SQLstr($Grupo_ID);
	$Email=str2SQLstr($Email);
	if($Password)
	{
		$Password=str2SQLstr(password_hash($Password, PASSWORD_DEFAULT));
		$sql="insert into logins (username,password,Nome,estado,Grupo_ID,email) values ($Username,$Password,$Nome,$Estado,$Grupo_ID,$Email)";
	}
	else $sql="insert into logins (username,Nome,estado,Grupo_ID,email) values ($Username,$Nome,$Estado,$Grupo_ID,$Email)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id,$img_path='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from logins where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$Username,$Password,$Nome,$Estado,$Grupo_ID,$Email='')
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
		return InsertNewRecord($Username,$Password,$Nome,$Estado,$Grupo_ID,$Email);
	}
	else
	{
		$Nome=str2SQLstr($Nome);
		$Estado=str2SQLstr($Estado);
		$Grupo_ID=str2SQLstr($Grupo_ID);
		$Email=str2SQLstr($Email);
		if($Password)
		{
			$Password=str2SQLstr(password_hash($Password, PASSWORD_DEFAULT));
			$sql="Update logins Set Nome=$Nome,estado=$Estado,Grupo_ID=$Grupo_ID,email=$Email,password=$Password where ID=$id";
		}
		else $sql="Update logins Set Nome=$Nome,estado=$Estado,Grupo_ID=$Grupo_ID,email=$Email where ID=$id";
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