<?
	include "settings.inc.php";
	include "../../layout/header.inc.php";
	include "../../layout/body.inc.php";
	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
?>
<script type="text/javascript" src="../../editor/ckeditor_4_5_1/ckeditor.js"></script>
<script type="text/javascript" src="../../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>

<script>function escreve_editor(valuea){document.getElementById('texto2').value=valuea;}</script>
<script>
function insert_new_text(text)
{
	CKEDITOR.instances['texto2'].insertText(text);
}
</script>

<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td valign="top" align="left"><? include "../../layout/menus.inc.php";?>
<form action="reordena.php" method="post"><input type="hidden" name="ID_ORG" value="<?=$ID_ORG?>">
<?
$dados=GetRSRecords2(0,$ID_ORG);
$count=0;
while(!$dados->EOF())
{
	$codigo=$dados->db_data["ID"];
	$ordem=$dados->db_data["Ordem"];
	$name=$dados->db_data["Name"];
	$name2=$dados->db_data["Name2"];
    if(!$name)$name='[EDITAR]';
	if(!$name) $name=$name2;
	if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
	else				{$cor="#FFFFFF";$class="r2";$n="2";}
	?>
    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
        <td width="29" align="center"><img src="../../images/page.png"></td>
		<td width="4" align="right"><img width="2" src="../../images/s6_<?=$n?>.jpg"></td>
        <td class="preto">&nbsp;<a class=preto href="form_fields.php?ID=<? echo $dados->db_data["ID"]?>&ID_ORG=<? echo $ID_ORG?>"><? echo stripslashes($name);?></a></td>
        <td class="preto" align="right" width="100"><strong><?=write_lang('key_codigo','');?></strong>:&nbsp;</td>
        <td class="preto"  width="100"><a href="javascript:insert_new_text('{campo[<?=$dados->db_data["ID"]?>]}')">{<?=write_lang('key_campo','');?>[<?=$dados->db_data["ID"]?>]}</a></td>
        <td width=100 align=right><a class=italico href="form_fields.php?ID=<? echo $dados->db_data["ID"]?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar"><img title="apagar campo" src="del.png" border=0></a></td>
        <td width="4" align="right"><img width="2" src="../../images/s6_<?=$n?>.jpg"></td>
		<td class="ordem<?=$n?>"><input class="ordem" type=text name="chk_<?=$codigo?>" value="<?=$ordem?>"></td>
	</tr>
    </table>
	<?
	$dados->db_next();
	$count++;
}
$dados->db_close(1);
?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="right" valign="middle" class="t1">
			<input class="btn btn-primary" type="submit" name="Reordenar" value="<?=write_lang('key_reordenar','');?>">
		</td>
     </tr>
	</table>
    <img src="../../images/bsh.png" width="100%" height="30px">
	<br />
</form>
<br>

		<?
        function campos_listagem($id)
        {
            global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
            $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
            $obj=new DataObj;

            $sql="Select * from form_fields where ID_ORG='".$id."' Order by Ordem";
            $obj->db_open($db_link,$DB_name,$sql);
            while(!$obj->EOF())
            {
                if($obj->db_data["Name"]) $nm = $obj->db_data["Name"];
                else  $nm = "Separador";
                $res .= "{campodesc(".$obj->db_data["ID"].")};";
                $res2 .= $nm.": {campodesc(".$obj->db_data["ID"].")};";
                $obj->db_next();
            }
            $obj->db_close();
            return array($res,$res2);
        }
        $campos_listagem_f = campos_listagem($ID_ORG);
        $campos_listagem_simple = explode(";",$campos_listagem_f[0]);
        $campos_listagem_f2 = explode(";",$campos_listagem_f[1]);

        global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
        $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
        if($update)
        {
            $sql="Update forms Set forms='".$texto2."', conf_dif='".$conf_dif."' where ID=$ID_ORG";
            $res=Runquery($db_link,$DB_name,$sql);
        }
        $obj=new DataObj;
        $sql="Select * from forms where ID='".$ID_ORG."'";
        $obj->db_open($db_link,$DB_name,$sql);
        $T2 = $obj->db_data["forms"];
        $conf_dif = $obj->db_data["conf_dif"];
        $obj->db_close();
        ?>
		<form action="" method="post">
            <input type="hidden" name="ID_ORG" value="<?=$ID_ORG?>">
            <input type="hidden" name="update" value="1">
            <table width="100%" class="listagem" border=0 cellspacing=0 cellpadding=10 bgcolor=#E0E0E0>
                </td>
            </tr><tr><td colspan="2">
                <textarea id="texto2" name="texto2" style="WIDTH: 100%; HEIGHT: 150px;"><?php echo stripslashes($T2);?></textarea><?php show_editor("oEdit1","texto2",880,450); ?>
				</td></tr>
            <tr><td bgcolor=#E0E0E0 width="280" class=preto style="font-size:12px" align="left"><input type="checkbox" name="conf_dif" value="1" <? if($conf_dif){?> checked<? }?>>&nbsp;<?=write_lang('key_formulario_layout','')?>
            </table>

            <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
            <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
            <tr>
                <td width="5px"></td>
                <td align="center" valign="middle" class="t1">
                    <input class="btn btn-primary" type="submit" name="submeter" value="<?=write_lang('key_submeter','');?>">
                </td>
             </tr>
            </table>
            <img src="../../images/bsh.png" width="100%" height="30px">
<br />



<br>
        </form>

		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><? show_fast_tools();?></td>
	</tr>
	</table>

<table class="t3" border=0 cellspacing=0 cellpadding=0 style="border:#999 1px solid;margin: 0 auto">
<tr>
<td bgcolor=#E0E0E0 class=preto style="font-size:12px;" height="25">&nbsp;&nbsp;<?=write_lang('key_pre_visualizar','');?></td>
</tr>
<tr><td style="font-size:12px; background-color:#666;text-align:left"><? show_forms($ID_ORG,1);?></td></tr>
</table><br><br>
<? 	include "../../layout/bottom.inc.php"; ?>