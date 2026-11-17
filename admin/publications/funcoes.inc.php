<?php
function show_page_buttons()
{
	global $task_button;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1">
        	<input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button;?> ">
        	<input class="btn btn-primary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%">
<?
}
function show_page_tools($filter=0)
{?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1">Pesquisar:</td>
		<td class=pesq><input class="form-control" type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="pesq" ></td>
		<td width="28px"><input type="submit" value="submeter pesquisa" src="../images/btp.jpg" class="btn btn-primary"></td>
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
		<td>&nbsp;<input type="submit"  name="submeter" value="Aplicar Ação" class="btn btn-primary"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%">
<?
}
function show_fast_tools()
{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" value="criar nova publicação" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_content_list()
{
	global $publications;

	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="SELECT * FROM publications ORDER BY Ordem ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Nome=$rs->db_data["Nome"];
		$Aprovado=$rs->db_data["Aprovado"];
		$Data_Criacao=$rs->db_data["Data_Criacao"];
		$Visivel=$rs->db_data["Visivel"];

		$Tipo=$publications[$rs->db_data["Tipo"]];

		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35">
				<?php if($Visivel){ ?>
					<img title="" src="ok.png" border=0 align="absmiddle">
				<?php } else { ?>
					<img title="" src="hidden.png" border=0 align="absmiddle">
				<?php } ?>
			</td>
            <td valign="middle" width="150"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Tipo){echo htmlentities_b(stripslashes($Tipo));} else echo "[Editar]";?></a></td>
            <td valign="middle" width="300"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Nome){echo htmlentities_b(stripslashes($Nome));} else echo "[Editar]";?></a></td>
            <td valign="middle" width="70"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Aprovado){echo htmlentities_b(stripslashes($Aprovado));} else echo "[Editar]";?></a></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Data_Criacao){echo htmlentities_b(stripslashes($Data_Criacao));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar publicação" src="del.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function show_content_from_search($reg)
{

	global $publications;
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from publications WHERE ID IN($reg)";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Nome=$rs->db_data["Nome"];
		$Aprovado=$rs->db_data["Aprovado"];
		$Tipo=$publications[$rs->db_data["Tipo"]];
		$Data_Criacao=$rs->db_data["Data_Criacao"];

		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><img title="editar publicação" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle" width="200"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Tipo){echo htmlentities_b(stripslashes($Tipo));} else echo "[Editar]";?></a></td>
            <td valign="middle" width="130"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Nome){echo htmlentities_b(stripslashes($Nome));} else echo "[Editar]";?></a></td>
            <td valign="middle" width="70"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Aprovado){echo htmlentities_b(stripslashes($Aprovado));} else echo "[Editar]";?></a></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Data_Criacao){echo htmlentities_b(stripslashes($Data_Criacao));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar publicação" src="del.png" border=0></a></td>
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

	$img_path='../../files/publications/';

	if(isset($_POST['slashes']) && $_POST['slashes']=="'")
	{
		$Nome = addslashes($_POST['Nome']);
		$Aprovado 		= addslashes($_POST['Aprovado']);
		// $Potencia 	= addslashes($_POST['Potencia']);
		$Ordem 		= intval($_POST['Ordem']);
		$Tipo 		= intval($_POST['Tipo']);
		$Visivel 	= intval($_POST['Visivel']);

		$Img_peq1["name"] = $_FILES['img_peq1']['name'];
		$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];

		$altera_img=0;
		if($Img_peq1["name"])
		{
			$altera_img = 1;
			$pos 		= strrpos($Img_peq1["name"],".");
			$Img_ext 	= substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);
		}


		// $sql="insert into publications (Referencia, Marca, Potencia, Mes, Ano, Img_ext, Visivel) values ('$Referencia', '$Marca', '$Potencia', '$Mes', '$Ano', '$Img_ext', '$Visivel')";
		$sql="INSERT INTO publications
						(
						Nome,
						Tipo,
						Aprovado,
						Ordem,
						Ext,
						Visivel
						)
						VALUES
						(
						'$Nome',
						'$Tipo',
						'$Aprovado',
						'$Ordem',
						'$Img_ext',
						'$Visivel')";

		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res)echo db_error($db_link);
		else
		{
			$new_id=db_get_last_insert_id($db_link);
			if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$Img_ext);
		}
		db_closedb_conection($db_link);
	}
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$id=$_GET['ID'];
	$res=Runquery($db_link,$DB_name,"delete from publications where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$img_path='../../files/publications/';
	$rs=new DataObj;

	$id=$_GET['ID'];
	$Img_ext = '';
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select Ext from publications WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$Img_ext = $rs->db_data['Ext'];
	}
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord();
	}
	else
	{
		if(isset($_POST['slashes']) && $_POST['slashes']=="'")
		{
			$Img_peq1["name"] = $_FILES['img_peq1']['name'];
			$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];

			$altera_img=0;
			if($Img_peq1["name"] != '')
			{
				$altera_img=1;
				$pos=strrpos($Img_peq1["name"],".");
				$Img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);
			}
			if($_POST['tem_foto1'] && $_POST['Apagar_img1']){$ficheiro1=$id."_1.".$Img_ext; unlink($img_path.$ficheiro1);if(!$altera_img)$Img_ext='';}
			if($altera_img) copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$Img_ext);

			$Nome = addslashes($_POST['Nome']);
			$Aprovado		= addslashes($_POST['Aprovado']);

			$Ordem 		= intval($_POST['Ordem']);
			$Tipo 		= intval($_POST['Tipo']);
			$Visivel 	= intval($_POST['Visivel']);

			$sql='Update publications Set Nome="'.$Nome.'", Ordem="'.$Ordem.'", Aprovado="'.$Aprovado.'", Tipo="'.$Tipo.'", Ext="'.$Img_ext.'", Visivel="'.$Visivel.'" where ID='.$id;
			$res=Runquery($db_link,$DB_name,$sql);
			db_closedb_conection($db_link);
		}
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
	lower(Nome) as Nome,length(lower(Nome)) AS len,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."'))/(length(lower(Nome))+1-length(replace(lower(Nome),' ','')) ) AS occwds,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."')) AS occ
	FROM publications
	WHERE (lower(Nome) LIKE '%".$d."%') ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
?>