<?
include "settings.inc.php";
include "../../layout/header.inc.php";
include "../../layout/body.inc.php";
?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top"><? include "../../layout/menus.inc.php"; ?>
<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
        <tr>
        	<td width="10"></td>
            <td class="text-left">&nbsp;</td>
            <td width="10"></td>
        </tr>
        </table>
<form name="form" method="post" action="mover_trata.php" enctype="multipart/form-data">
<input type="hidden" name="ID_Grupo" value="<?=$ID?>">
<table class="new_quadros" width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
            <td bgcolor="#E0E0E0">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td width="10" bgcolor="#E0E0E0">&nbsp;</td>
    <td colspan="4" valign="top" bgcolor="#E0E0E0"><a class="italico" href="grupos.php?ID=0"></a></td>
    <td width="10" bgcolor="#E0E0E0">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="10"></td>
    <td></td>
	<td></td>
	<td></td>
	<td></td>
    <td width="10"></td>
  </tr><?
  	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="select Descricao from nl_grupos inner join nl_grupos_linguas on nl_grupos_linguas.ID_PAI=nl_grupos.ID where nl_grupos.ID=$ID";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	$descricao=$obj->db_data["Descricao"];
	$obj->db_close();
	?>
  <tr>
    <td width="10" height="10" bgcolor="#E0E0E0"></td>
    <td bgcolor="#E0E0E0" class="preto">Mover todos os emails do grupo </td>
	<td bgcolor="#E0E0E0" class="preto">Para o grupo:</td>
    <td align="right" bgcolor="#E0E0E0" class="preto"></td>
    <td bgcolor="#E0E0E0"></td>
	<td bgcolor="#E0E0E0"></td>
  </tr>
  <tr>
    <td width="10" height="10" bgcolor="#E0E0E0"></td>
    <td bgcolor="#E0E0E0" class="preto"><?=$descricao?></td>
	<td bgcolor="#E0E0E0" class="preto"><select name="grupo_id">
	<?
	$sql="select nl_grupos.ID,Descricao from nl_grupos inner join nl_grupos_linguas on nl_grupos_linguas.ID_PAI=nl_grupos.ID where nl_grupos.ID!=$ID";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
  echo '<option value="0">Sem Grupo</option>';
	while(!$obj->EOF())
	{
		$codigo=$obj->db_data["ID"];
		$descricao=$obj->db_data["Descricao"];
		echo "<option value=\"" . $obj->db_data["ID"] . "\">". nl2br(htmlentities($descricao)) . "</option>";
		$obj->db_next();
	}
	?>
	</select></td>
    <td bgcolor="#E0E0E0" class="preto"></td>
    <td bgcolor="#E0E0E0" align="right"><input class="btn btn-primary" type="submit" name="Gravar alterações" value="mover"></td>
	<td bgcolor="#E0E0E0"></td>
  </tr>
  <tr>
    <td width="10" height="10"></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
	<td width="10"></td>
  </tr>
</table>
</td>
</tr>
</table>
</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>

<? 	include "../../layout/bottom.inc.php";?>