<?
	include "settings.inc.php";
	$linguas_arr = explode(",",$linguas);
	$linguas_desc_arr = explode(",",$linguas_desc);

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$config= new DataObj;
	$sql_config="select * from nl_config where ID=1";
	$hRes_config= $config->db_open($db_link,$DB_name,$sql_config);
	if(!$config->EOF())
	{
		$ID=$config->db_data["ID"];
		$cabecalho=$config->db_data["cabecalho"];
		$SMTP=$config->db_data["SMTP"];
		$SMTP_Email=$config->db_data["SMTP_Email"];
		$SMTP_login=$config->db_data["SMTP_login"];
		$SMTP_pass=$config->db_data["SMTP_pass"];
		$emails_vez=$config->db_data["emails_vez"];
		$EmailToReply=$config->db_data["EmailToReply"];
		$cabecalhoToReply=$config->db_data["cabecalhoToReply"];

		$task_button = "Actualizar configurações";
	}
	else{$task_button = "Inserir configurações";}
	$config->db_close();

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<style>.new_quadros{background:#FFF !important}</style>
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
    <td valign="top"><? include "../layout/menus.inc.php"; ?>
        <div class="panel panel-inverse overflow-hidden m-b-12">
            <div class="panel-heading">
                <h4 style="color:#fff">NEWSLETTER - CONFIG</h4>
            </div>
        </div>
<form name="form" method="post" action="config_trata.php?dump=<?=uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="slashes" value="'">
<input type="hidden" name="ID" value="<?=$ID?>">

<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="5">
<tr>
    <td width="3" height="10"></td>
    <td width="138" align="left" valign="top"></td>
    <td align="left" valign="top" width="578"></td>
    <td width="6"></td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">Nome interno:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="cabecalho" maxlength="255" value="<?=$cabecalho;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">SMTP server:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP" maxlength="255" value="<?=$SMTP;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">SMTP Email:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_Email" maxlength="255" value="<?=$SMTP_Email;?>"></td>
    <td width="6">&nbsp;</td>
</tr>

<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">SMTP login:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_login" maxlength="255" value="<?=$SMTP_login;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">SMTP pass:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_pass" maxlength="255" value="<?=$SMTP_pass;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">Emails de cada vez:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="emails_vez" maxlength="255" value="<?=$emails_vez;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">Enviar e-mail para responder:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="EmailToReply" maxlength="255" value="<?=$EmailToReply;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
<tr>
    <td height="25" width="3">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="138">Cabeçalho para responder:</td>
    <td align="left" valign="top" width="578"> <input class="form-control" name="cabecalhoToReply" maxlength="255" value="<?=$cabecalhoToReply;?>"></td>
    <td width="6">&nbsp;</td>
</tr>
</table>

<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
			<input class="btn btn-primary" type="submit" name="Submit" value="<?=$task_button?>">
            &nbsp;
            <input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onClick="window.open('list.php','_self')">
		</td>
     </tr>
	</table>

</form>

</td>
<td width="10"></td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>