<?php
function show_page_tools($filter=0)
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 class="text-center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" class="text-center"></td>
		<td width="91px" class="t1">Pesquisar:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="submeter pesquisa" class="btn btn-primary" ></td>
		<td width="10">&nbsp;</td>
        <td width="91px" class="t1">&nbsp;listar:</td>
        <td><select class="form-control" onchange="document.lista_paginas.submit()" name="tipos"><option value="0">todos</option><option value="1" <? if($tipos==1) echo 'selected="selected"';?>>paginas publicas</option><option value="2" <? if($tipos==2) echo 'selected="selected"';?>>paginas privadas</option><option value="3" <? if($tipos==3) echo 'selected="selected"';?>>noticias</option></select></td>
	</tr>
	</table>
<? }
function show_page_buttons()
{
	global $task_button,$ID,$ID_ORG,$num;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"> <input class="btn btn-primary" type="submit" name="Submit" value="<? echo $task_button;?> Campo" style="cursor:pointer">
        <input class="btn btn-secondary" type="button" name="cancelar" value="Voltar Atrás" style="cursor:pointer" onClick="window.open('list.php?ID_ORG=<?=$ID_ORG?>&num=<?=$num?>','_self')"></td>
     </tr>
	</table>
	<img src="../../images/bsh.png" width="800px">
<?
}
function show_fast_tools()
{
	global $ID_ORG;?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"></td>
		<td class="titulo2 text-center">FAST TOOLS</td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="inserir novo registo" onclick="javascript:window.open('form_records.php?ID=0&ID_ORG=<?=$ID_ORG?>&bgn=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
	</table>
	<img src="../../images/bsh.png" width="200px" height="30px">
<? }
function GetRSRecords2($id=0,$campos="*")
{
  global $DB_host,$DB_user,$DB_pass,$DB_name;
  $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
  $obj=new DataObj;
  if($id!=0) $condicao="where ID=$id";
  else $condicao="";
  $hRes=$obj->db_open($db_link,$DB_name,"Select $campos from form_records $condicao");
  return $obj;
}
function InsertNewRecord($name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$name1=str2SQLstr($name);
	$email1=str2SQLstr($email);
	$titulo=str2SQLstr($titulo);
	$assunto=str2SQLstr($assunto);
	$listagem=str2SQLstr($listagem);
	$link=str2SQLstr($link);
	$sub=str2SQLstr($sub);
	$campos_listagem=str2SQLstr($campos_listagem);
	$tipo=str2SQLstr($tipo);
	//INSERE VALORES NA BD
	$sql="insert into form_records (Name,Email,Texto0,Texto2,Texto3,Campos_listagem,Tipo,Texto4,assunto) values ($name1,$email1,'$texto','$texto2','$texto3',$campos_listagem,$tipo,'$texto4',$assunto)";

	$res=Runquery($db_link,$DB_name,$sql);
	//CASO NAO TENHA INSERIDO OS VALORES COM SUCESSO MOSTRA O ERRO
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome2;
	if(DeleteRecord_sublinks($Nome2,$id,$img_path))
	{
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$res=Runquery($db_link,$DB_name,"delete from form_records where ID=$id");
		if(!$res) echo db_error($db_link);
		db_closedb_conection($db_link);
		return($res);
	}
}
//////////////////////////////////////////////////////////////7777
function DeleteRecord_sublinks($Nome,$id_org,$img_path)
{
	$dados=GetRSRecords_sublinks($Nome,0,$id_org);
	$result=1;
	while(!$dados->EOF())
	{
		$id=$dados->db_data["ID"];
		$result=DeleteRecord_sublink($Nome,$id,$img_path);
		if(!$result)
		{
			$dados->db_close();
			return $result;
		}
		$dados->db_next();
	}
	$dados->db_close();
	return $result;
}
function GetRSRecords_sublinks($Nome,$id=0,$id_org=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where ID=$id";
		if($id_org!=0) $condicao="$condicao and where ID_ORG=$id_org";
	}
	else
	{
		$condicao="";
		if($id_org!=0) $condicao="where ID_ORG=$id_org";
	}
	$hRes=$obj->db_open($db_link,$DB_name,"Select $campos from form_records $condicao order by Data Desc");
	return $obj;
}
function DeleteRecord_sublink($Nome,$id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select * from form_records where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	$res=Runquery($db_link,$DB_name,"delete from form_records where ID=$id");
	if($res)
	{
		if($ext1!="") //CASO TENHA IMAGENS ELIMINA-AS
		{
			$ficheiro1=$id."_1.".$ext1;
			unlink($img_path.$ficheiro1);
		}
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
//////////////////////////////////////////////////////////////7777
function ChangeRecord($id,$name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from form_records WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
	}
	$rs->db_close();
	if(!$altera) InsertNewRecord($name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$name1=str2SQLstr($name);
		$email1=str2SQLstr($email);
		$titulo=str2SQLstr($titulo);
		$assunto=str2SQLstr($assunto);
		$listagem=str2SQLstr($listagem);
		$link=str2SQLstr($link);
		$sub=str2SQLstr($sub);
		$campos_listagem=str2SQLstr($campos_listagem);
		$tipo=str2SQLstr($tipo);
		$sql="Update form_records Set Name=$name1,Email=$email1,Texto0='$texto',Texto2='$texto2',Texto3='$texto3',Campos_listagem=$campos_listagem,Tipo=$tipo,Texto4='$texto4',assunto=$assunto where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}

function guarda_bd_form2($campo_id, $DB_host,$DB_user, $Password,$DB_name, $Tabela="form_records")
{
	$db_link = db_opendb_conection($DB_host,$DB_user, $Password);
	$sql = "insert into $Tabela (ID_ORG) values ($campo_id)";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res) echo db_error($db_link);
	else
	{
	    $rec= new DataObj;
		$rec->db_open($db_link,$DB_name, "Select MAX(ID) from $Tabela");
		$codigo=$rec->db_data[0];
		return $codigo;
    }
	db_closedb_conection($db_link);
	return($res);
}

function guarda_bd_campos2($campo_id, $campo, $valor, $DB_host,$DB_user, $Password,$DB_name, $Tabela="form_records_fields")
{
	$db_link = db_opendb_conection($DB_host,$DB_user, $Password);
	$campo = str2SQLstr($campo);
	$valor = str2SQLstr($valor);
	$sql = "insert into $Tabela (ID_ORG, Name, Valor) values ($campo_id, $campo, $valor)";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function GetId($ID_ORG, $f_nome, $f_data1, $f_data2, $main_date, $product = "")
{
		$id_list = array();
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$teste=new DataObj;
		$sql = "SELECT ID FROM form_fields WHERE Tipo = 32";
		$teste->db_open ($db_link,$DB_name,$sql);
		$prod_id = $teste->db_data["ID"];

		$obj=new DataObj;
		$sql = "SELECT ID FROM form_records WHERE ID_ORG = ". $ID_ORG. " ORDER BY ID DESC";
		$obj->db_open ($db_link,$DB_name,$sql);
		while(!$obj->EOF())
		{
			$id_org[] = $obj->db_data["ID"];
			$obj->db_next();
		}

		foreach ($id_org as $id)
		{
			$obj=new DataObj;
			$sql = "SELECT * FROM form_records_fields WHERE ID_ORG = $id";
			$hRes = $obj->db_open($db_link,$DB_name,$sql);
			while(!$obj->EOF())
			{
				$Name = $obj->db_data["Name"];
				$Value = $obj->db_data["Valor"];
				$id = $obj->db_data["ID_ORG"];
				if($f_nome=="" && !$f_data1=="" && !$f_data2=="")
				{
					if($Name==$main_date && $Value >= $f_data1 && $Value <=$f_data2)
					{
						$id_list[] = $id;
					}
				}
				elseif(!$f_nome=="")
				{
					if($Name==$product && $Value==$f_nome)
					{
						$id_by_name[] = $id;
					}
					if(!$f_data1=="" && !$f_data2=="" && $id_by_name)
					{
						$id_list = GetIdByDate ($id_by_name, $f_data1, $f_data2, $main_date);
					}
					else
					{
						$id_list = $id_by_name;
					}
				}
				else
				{
					$id_list[] = $id;
				}
				$obj->db_next();
			}
		}
		return $id_list;
}
function GetIdByDate($id_by_name, $f_data1, $f_data2, $main_date)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	foreach($id_by_name as $id)
	{
		$obj=new DataObj;
		$sql = "SELECT * FROM form_records_fields WHERE ID_ORG = $id ORDER BY ID ASC";
		$hRes = $obj->db_open ($db_link,$DB_name,$sql);
		while(!$obj->EOF())
		{
			$Name = $obj->db_data["Name"];
			$Value = $obj->db_data["Valor"];
			$id = $obj->db_data["ID_ORG"];
			if($Name==$main_date && $Value >= $f_data1 && $Value <= $f_data2)
			{
				$id_list[] = $id;
			}
			$obj->db_next();
		}
	}
	return $id_list;
}
?>
