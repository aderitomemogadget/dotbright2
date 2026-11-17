<?php

	$IDb=intval($_GET['IDb']);
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$Suppliers_nomes;
	$rs=new Dataobj;

	$sql="Select * from msg_predefinidas_lang WHERE Lang='pt' and ID_PAI=$IDb";
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	if(!$rs->EOF())
	{
		$Titulo=$rs->db_data["Cabecalho"];
		$Msg=$rs->db_data["Texto"];
	}

?>
<table width="100%" border="0" cellpadding="5" cellspacing=0>
<tr>
    <td valign="top" width="100%">Assunto: </td>
</tr>
<tr>
    <td valign="top" width="100%"><input type="text" id="titulo_mgs" name="titulo_mgs" style="width:100%" value="<?=$Titulo?>"></td>
</tr>
<tr>
    <td valign="top" width="100%">Mensagem: </td>
</tr>
<tr>
    <td valign="top" width="100%"><textarea name="txt_msgs" id="txt_msgs" style="width:100%;height:100px"><?=$Msg?></textarea></td>
</tr>
<tr>
    <td valign="top" width="100%"><input class="btn btn-primary" type="button" name="Enviar" value=" Enviar " onclick="send_msg()"> </td>
</tr>
</table>
<?php $rs->db_close();?>