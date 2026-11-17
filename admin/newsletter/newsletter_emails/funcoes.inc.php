<?
function BuscarRegistos2($id=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;
    $condicao="";
	if($id!=0) $condicao=" WHERE ID = $id";
	$sql="SELECT ".$campos." FROM nl_grupos".$condicao;
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	db_closedb_conection($db_link);
	return $obj;
}

function BuscarRegistosNew($id=0,$tabela,$tabela_linguas,$ligacao="",$condicao="",$ordem="",$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;
	if($id!=0 && $condicao!="") $condicao=" WHERE ID = $id AND (".$condicao.")";
	else if($id!=0) $condicao=" WHERE ID = $id";
	else if($condicao!="") $condicao=" WHERE ".$condicao;
	if($ordem!="") $ordem=" ORDER BY ".$ordem;
	$sql="SELECT ".$campos." FROM ".$tabela." INNER JOIN ".$tabela_linguas." ON ".$ligacao.$condicao.$ordem;
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	db_closedb_conection($db_link);
	return $obj;
}

function InsertNewRecord()
{
	if(isset($_POST['descricao'])) $descricao=$_POST['descricao'];
	if(isset($_POST['visible'])) $visible=$_POST['visible'];

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$visible= str2SQLstr($visible);
	$sql = "insert into nl_grupos (Visivel) values ($visible)";
	$res = Runquery($db_link,$DB_name,$sql);
	if ($res)
	{
	    $rec= new DataObj;
		$rec->db_open($db_link,$DB_name, "Select MAX(ID) as ID from nl_grupos");

		InsertNewRecordLinguas($rec->db_data['ID'],$descricao);
		$rec->db_close();
	}
	else	echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function InsertNewRecordLinguas($id_pai,$descricao)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$descricao = str2SQLstr($descricao);
	$sql = "insert into nl_grupos_linguas (ID_PAI, Descricao, Lang) values ($id_pai,$descricao, '$site_lang')";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
    return($res);
}

function DeleteRecord()
{
	if(isset($_POST['ID'])) $ID=$_POST['ID'];

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $sql= "delete from nl_grupos where ID = $ID";
	$res = Runquery($db_link,$DB_name,$sql);
	if ($res)
	{
		if($ext1!="")
		{
			$sql2= "delete from nl_grupos_linguas where ID_PAI = $ID";
	        $res2 = Runquery($db_link,$DB_name,$sql2);
		}
	}
	else	echo db_error($db_link);
	return($res);
}
function ChangeRecord()
{
	//echo "ChangeRecord<br/>";
	if(isset($_POST['ID'])) $ID=$_POST['ID'];
	if(isset($_POST['descricao'])) $descricao=$_POST['descricao'];
	if(isset($_POST['visible'])) $visible=$_POST['visible'];

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$visible= str2SQLstr($visible);
	$sql = "Update nl_grupos Set Visivel=$visible where ID=$ID";
	//echo "sql: ".$sql."<br/>";
	//die();
	$res = Runquery($db_link,$DB_name,$sql);
	db_closedb_conection($db_link);
	ChangeRecordLinguas($ID,$descricao);
	return($res);
}
function ChangeRecordLinguas($id_pai,$descricao)
{
	if(!$id_pai)
	{
		return InsertNewRecordLinguas($id_pai,$descricao);
	}
	else {
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$descricao = str2SQLstr($descricao);
		$sql = "Update nl_grupos_linguas Set Descricao = $descricao where ID_PAI = $id_pai and Lang = '$site_lang'";
		$res = Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}

function BuscarRegistosLinguas($id_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$condicao="where ID_PAI=$id_pai and lang='$site_lang'";
	$hRes=$obj->db_open($db_link,$DB_name, "Select * from nl_grupos_linguas $condicao");
	db_closedb_conection($db_link);
	return $obj;
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
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="gerir emails" onclick="javascript:window.open('../newsletter_emails/list.php?dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
    <tr>
		<td width="5px" align="left"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="exportar emails" onclick="javascript:window.open('../exportar.php?dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
	</table>
	<img src="../../images/bsh.png" width="200px" height="30px">
<? } ?>