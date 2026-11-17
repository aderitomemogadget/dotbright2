<?
include "settings.inc.php";
include "../../layout/header.inc.php";
include "../../layout/body.inc.php";
?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top"><? include "../../layout/menus.inc.php"; ?>
<table class="listagem" width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="10" height="10" bgcolor="#E0E0E0"></td>
    <td width="1" bgcolor="#E0E0E0"></td>
    <td width="1" bgcolor="#E0E0E0"></td>
    <td bgcolor="#E0E0E0" class="preto">Grupo</td>
	<td align="right" bgcolor="#E0E0E0" class="preto">FOI CONSIDERADO SPAM</td>
    <td align="right" bgcolor="#E0E0E0" class="preto">DESTINATARIO DEIXOU DE EXISTIR</td>
    <td bgcolor="#E0E0E0"></td>
  </tr>
<?
$dados=BuscarRegistosNew(0,"nl_grupos","nl_grupos_linguas","nl_grupos.ID=nl_grupos_linguas.ID_PAI","nl_grupos_linguas.Lang='$site_lang'","Descricao","nl_grupos.ID,grupos.Visivel,nl_grupos_linguas.Descricao");
$count=0;
$total1=0;
$total2=0;
while(!$dados->EOF())
{
	$codigo=$dados->db_data["ID"];
	$Visivel=$dados->db_data["Visivel"];
	$descricao=$dados->db_data["Descricao"];

	$numero=0;
	$numero2=0;

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="select count(*) as Num from nl_emails_removidos where ID_ORG=$codigo and (motivo ='For security reasons certain items found in an email with your address as the sender have not been accepted' OR
	motivo ='SpamReason' OR motivo ='Disallowed content found in MIME attachment' OR motivo ='permitted sender' OR motivo ='permitted sender hosts' OR motivo ='Received: from unknown'
	OR motivo ='Sender IP must resolve')";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	$numero=$obj->db_data["Num"];
	$total1=$total1+$numero;
	$obj->db_close();

	$sql="select count(*) as Num from nl_emails_removidos where ID_ORG=$codigo and (motivo <> 'For security reasons certain items found in an email with your address as the sender have not been accepted' AND
	motivo <> 'SpamReason' and motivo <> 'Disallowed content found in MIME attachment' and motivo <> 'permitted sender' and motivo <> 'permitted sender hosts'
	and motivo <> 'Received: from unknown' and motivo <> 'Sender IP must resolve')";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	$numero2=$obj->db_data["Num"];
	$obj->db_close();
	$total2=$total2+$numero2;

	if($Visivel){
		if($count % 2 !=0)	$cor="#CCFF00";
		else				$cor="#EBFF98";
	} else {
		if($count % 2 !=0)	$cor="#E0E0E0";
		else				$cor="#F6F6F6";
	}
?>
  <tr>
    <td bgcolor="<?=$cor;?>">&nbsp;</td>
    <td width="1" valign="top" bgcolor="<?=$cor;?>"><a class="italico" href="grupos.php?ID=<?=$dados->db_data["ID"]?>&accao=alterar"><img title="Editar grupo" src="../../templates/backoffice_img/edit.png" width="16" height="16" border="0" align="absmiddle"></a></td>
    <td width="1" valign="top" bgcolor="<?=$cor;?>"></td>
    <td valign="top" bgcolor="<?=$cor;?>" class="preto">&nbsp;<? if($descricao) echo htmlentities($descricao); else echo "[Editar]"?></td>
	<td width="100" align="right" valign="top" bgcolor="<?=$cor;?>" class="preto"><?=number_format($numero,0,"",".")?> emails</td>
	<td width="100" align="right" valign="top" bgcolor="<?=$cor;?>" class="preto"><?=number_format($numero2,0,"",".")?> emails</td>
    <td align="right" valign="top" bgcolor="<?=$cor;?>"></td>
    <td width="10" bgcolor="<?=$cor;?>">&nbsp;</td>
  </tr>
<?
	$dados->db_next();
	$count++;
}
$dados->db_close(1);
?>
  <tr>
    <td width="10" height="10"></td>
    <td width="1"></td>
    <td width="1"></td>
    <td></td>
    <td class="preto" align="right"><?=number_format($total1,0,"",".")?> emails </td>
	<td width="100" class="preto" align="right"><?=number_format($total2,0,"",".")?> emails</td>
    <td></td>
	<td width="10"></td>
  </tr>
</table>

</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>

<? 	include "../../layout/bottom.inc.php";?>