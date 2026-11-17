<?
include "settings.inc.php";
include "../../../layout/header.inc.php";
include "../../../layout/body.inc.php";
?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../../../layout/menus.inc.php"; ?>
  <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
        <tr>
        	<td width="10"></td>
            <td class="text-left"><a class="branco" href="emails_removidos.php?ID=0&ID_ORG=<? echo "$ID_ORG";?>">Inserir Novo Email</a></td>
            <td width="10"></td>
        </tr>
        </table>

<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
            <td bgcolor="#E0E0E0">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td width="10" height="10"></td>
    <td width="1"></td>
    <td width="1"></td>
    <td></td>
    <td width="100"></td>
    <td width="10"></td>
  </tr>
<?
$data = BuscarRegistos2(0,$ID_ORG,"Email");
$count = 0;
while(!$data->EOF())
{
	$codigo=$data->db_data["ID"];
	$name=$data->db_data["Nome"];
	$email=$data->db_data["Email"];

	if($count % 2 != 0)	$cor = "#def2fa";
	else				$cor = "#F6F6F6";
?>
  <tr>
    <td bgcolor="<?=$cor;?>">&nbsp;</td>
    <td width="1" valign="top" bgcolor="<?=$cor;?>"><img  alt="" src="../../../images/edit.png" width="16" height="16" border="0" align="absmiddle"></td>
    <td width="1" valign = "top" bgcolor="<?=$cor;?>"></td>
    <td valign="top" bgcolor="<?=$cor;?>" class="preto">&nbsp;<a class="preto" href="emails_removidos.php?ID=<?=$data->db_data["ID"]?>&ID_ORG=<?=$ID_ORG?>"><? if($name) echo $name; else echo "[Editar]"?> - <?=$email?></a></td>
    <td width="100" align="right" valign="top" bgcolor="<?=$cor;?>"><a class="italico" href="emails_removidos.php?ID=<?=$data->db_data["ID"]?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar">&nbsp;&nbsp;&nbsp;Apagar <img  alt="" src="../../../images/del.png" width="15" height="15" border="0" align="absmiddle"></a></td>
    <td width="10" bgcolor="<?=$cor;?>">&nbsp;</td>
  </tr>
<?
	$data->db_next();
	$count++;
}
$data->db_close(1);
?>
  <tr>
    <td width="10" height="10"></td>
    <td width="1"></td>
    <td width="1"></td>
    <td></td>
    <td width="100"></td>
    <td width="10"></td>
  </tr>
</table>
</td>
</tr>
</table>
</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>

<? 	include "../../../layout/bottom.inc.php";?>