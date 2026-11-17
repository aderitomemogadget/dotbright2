<?
function ChangeRecord($id,$nome,$apelido,$empresa,$email,$contacto,$morada,$codpos,$localidade,$tel,$fax,$data,$obs)
{
	if(!FoundRegisto($id))
	{
		return InsertNewRecord($id_org,$nome,$apelido,$empresa,$email,$contacto,$morada,$codpos,$localidade,$tel,$fax,$data,$obs);
	}
	else {
	    global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$nome = str2SQLstr($nome);
		$apelido = str2SQLstr($apelido);
		$empresa = str2SQLstr($empresa);
		$email = str2SQLstr($email);
		$contacto = str2SQLstr($contacto);
		$morada = str2SQLstr($morada);
		$codpos = str2SQLstr($codpos);
		$localidade = str2SQLstr($localidade);
		$tel = str2SQLstr($tel);
		$fax = str2SQLstr($fax);
		$data = date2SQLstr($data);
		$obs = str2SQLstr($obs);
		$sql = "Update nl_emails_removidos Set Nome=$nome, Apelido=$apelido, Empresa=$empresa, Email=$email, Contacto=$contacto, Morada=$morada, CodPos=$codpos, Localidade=$localidade, Telefone=$tel, Fax=$fax, DataNasc=$data, Obs = $obs where ID = $id";
		$res = Runquery($db_link,$DB_name,$sql);
		if(!$res)echo db_error($db_link);
	    return($res);
	}
}

function BuscarRegistos2($id=0,$id_org=0,$ordem="",$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	if($id!=0) {
		$condicao = " WHERE ID=$id";
		if($id_org!=0) $condicao = "$condicao AND ID_ORG=$id_org";
	} else {
		$condicao="";
		if($id_org!=0) $condicao = " WHERE ID_ORG=$id_org";
	}

	if($ordem!="") $ordem=" ORDER BY ".$ordem;

	$hRes=$obj->db_open($db_link,$DB_name, "SELECT ".$campos." FROM nl_emails_removidos".$condicao.$ordem);
	return $obj;
}

function InsertNewRecord($id_org,$nome,$apelido,$empresa,$email,$contacto,$morada,$codpos,$localidade,$tel,$fax,$data,$obs)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id_org = str2SQLstr($id_org);
	$nome = str2SQLstr($nome);
	$apelido = str2SQLstr($apelido);
	$empresa = str2SQLstr($empresa);
	$email = str2SQLstr($email);
	$contacto = str2SQLstr($contacto);
	$morada = str2SQLstr($morada);
	$codpos = str2SQLstr($codpos);
	$localidade = str2SQLstr($localidade);
	$tel = str2SQLstr($tel);
	$fax = str2SQLstr($fax);
	$data = date2SQLstr($data);
	$obs = str2SQLstr($obs);

	$sql = "insert into nl_emails_removidos (ID_ORG, Nome, Apelido, Empresa, Email, Contacto, Morada, CodPos, Localidade, Telefone, Fax, DataNasc, Obs) values ($id_org,$nome,$apelido,$empresa,$email,$contacto,$morada,$codpos,$localidade,$tel,$fax,$data,$obs)";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function DeleteRecord($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

    $sql= "delete from nl_emails_removidos where ID = $id";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	return($res);
}
function show_fast_tools()
{?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class="titulo2 text-center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="gerir newsletter" onclick="javascript:window.open('../list.php?dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
    <tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="gerir emails" onclick="javascript:window.open('../../newsletter_emails/list.php?dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
    <tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="importar emails" onclick="javascript:window.open('../../newsletter_import/index.php?dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
    <tr>
		<td width="5px" align="left"><img src="../../../images/s2.jpg"></td>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="enviar newsletter" onclick="javascript:window.open('../../newsletter_send/index.php?dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../../../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../../../images/bsh.png" width="200px" height="30px">
<? } ?>

