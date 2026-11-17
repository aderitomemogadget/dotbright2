<?
include "settings.inc.php";
include "../../../layout/header.inc.php";
include "../../../layout/body.inc.php";

if(!$email)
{
	if(!$bgn) $bgn=0;
	if($num) {
	  $limit=" LIMIT ".$bgn.", ".$num;
	  $data = BuscarRegistos2(0,$ID_ORG);
	  $total_pag=ceil($data->db_rows/$num);
	  $data->db_close();
	}
}
?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><?php require_once("../../../layout/menus.inc.php"); ?>
  <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
        <tr>
        	<td width="10"></td>
            <td class="text-left"><a class="branco" href="emails_registados.php?ID=0&ID_ORG=<? echo "$ID_ORG";?>">Inserir Novo Email</a></td>
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
$data = BuscarRegistos2(0,$ID_ORG,"Email","*",$limit);
$count = 0;
while(!$data->EOF())
{
	$codigo=$data->db_data["ID"];
	$name=$data->db_data["Nome"];
	$email=$data->db_data["Email"];
	$ID_ORG=$data->db_data["ID_ORG"];

	if($email)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$hRes=$obj->db_open($db_link,$DB_name, "SELECT * FROM nl_grupos_linguas WHERE ID_PAI=$ID_ORG AND Lang='$site_lang'");
		$grupo=$obj->db_data["Descricao"]."&nbsp;";
	}


	if($count % 2 != 0)	$cor = "#def2fa";
	else				$cor = "#F6F6F6";
?>
  <tr>
    <td bgcolor="<?=$cor;?>">&nbsp;</td>
    <td width="16"  bgcolor="<?=$cor;?>"><img  alt="" src="../../../images/edit.png" width="16" height="16" border="0" align="absmiddle"></td>
    <td bgcolor="<?=$cor;?>" class="preto"><?=$grupo?></td>
    <td bgcolor="<?=$cor;?>" class="preto">&nbsp;<a class="preto" href="emails_registados.php?ID=<?=$data->db_data["ID"]?>&ID_ORG=<?=$ID_ORG?>"><? if($name) echo $name; else echo "[Editar]"?> - <?=$email?></a></td>
    <td width="100" align="right"  bgcolor="<?=$cor;?>"><a class="italico" href="emails_registados.php?ID=<?=$data->db_data["ID"]?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar">&nbsp;&nbsp;&nbsp;Apagar <img  alt="" src="../../../images/del.png" width="15" height="15" border="0" align="absmiddle"></a></td>
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
  <tr>
    <td bgcolor="#E0E0E0">&nbsp;</td>
    <td colspan="4"  bgcolor="#E0E0E0">
    <?
    if($total_pag) {
      echo "&nbsp;Páginas:&nbsp;&nbsp;";

      $start=$bgn-($num*5);
      if($start<10) $start=0;

      $pagina=(($bgn/$num)-4);
      if($pagina<1) $pagina=1;

      if($total_pag>($pagina+10)) $total_pag_tmp=$pagina+10;
      else $total_pag_tmp=$total_pag;

      if($pagina>1) echo '<a class="preto" href="list.php?ID_ORG='.$ID_ORG.'&num='.$num.'">1</a>&nbsp;&nbsp;...&nbsp;&nbsp;';

      for($k=$pagina;$k<=$total_pag_tmp;$k++) {
        if($bgn<>$start) {
          echo '<a class="preto" href="list.php?ID_ORG='.$ID_ORG.'&bgn='.$start.'&num='.$num.'">'.$k.'</a>&nbsp;&nbsp;';
        } else {
          echo '<font class="red"><b>'.$k.'</b></font>&nbsp;&nbsp;';
        }

        $start+=$num;
      }

      if($total_pag>$total_pag_tmp) echo '&nbsp;&nbsp;...&nbsp;&nbsp;<a class="preto" href="list.php?ID_ORG='.$ID_ORG.'&bgn='.(($total_pag*$num)-$num).'&num='.$num.'">'.$total_pag.'</a>';
    }
    ?>
    </td>
    <td width="10" bgcolor="#E0E0E0">&nbsp;</td>
  </tr>
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