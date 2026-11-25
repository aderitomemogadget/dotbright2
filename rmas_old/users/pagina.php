<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button="Apagar";$task_form='apaga';}
	elseif($ID==0){$task_button="Inserir";$task_form='insere';}
	else {$task_button="Alterar";$task_form='altera';}
	$D=getdate();
	$D2=getdate();
	$C="";$T="";$I1="";$I2="";$ordem="0";
	if($ID!=0)
	{
		if($ID!=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;$rs2=new DataObj;
			$sql="Select * from logins WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$IDb=$rs->db_data["ID"];
				$Username=$rs->db_data["username"];
				$m_Nome=$rs->db_data["Nome"];
				$Estado=$rs->db_data["estado"];
                $Grupo_ID=$rs->db_data["Grupo_ID"];
				$tecnico=$rs->db_data["tecnico"];
			}
			$rs->db_close(1);
		}
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>">
		<input type="hidden" name="slashes" value="'">
    	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Group:</td>
                                    <td align="left" valign="top" class="preto"><? show_lista_users($Grupo_ID,"grupos_logins","Grupo_ID");?><? /*?>&nbsp;<a href="../grupos_users/list.php" class="preto">[editar]</a>*/?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Name:</td>
                                    <td align="left" valign="top"><input name="m_Nome" style="WIDTH:255px; HEIGHT:20px" maxlength="255" value="<?=$m_Nome;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Username:</td>
                                    <td align="left" valign="top"><input name="Username" style="WIDTH:255px; HEIGHT:20px" maxlength="255" value="<?=$Username;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Password:</td>
                                    <td align="left" valign="top" class="preto"><input name="Password" style="WIDTH:255px; HEIGHT:20px" maxlength="20" value="">
                                    (se colocar uma nova password a antiga será eliminada)</td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">É tecnico:</td>
                                    <td align="left" valign="top" class="preto"><input type="checkbox" name="tecnico" value="1" <? if($tecnico=='1') echo "checked";?>> SIM</td>
                                </tr>
                                <tr>
                                    <td colspan="2" height=20></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Activo:</td>
                                    <td align="left" valign="top" class="preto"><input type="checkbox" name="Estado" value="1" <? if($Estado=='1') echo "checked";?>> SIM</td>
                                </tr>
                                </table>
                           </td>
                       </tr>
                       </table>
                	</td>
				</tr>
                </table><? show_page_buttons();?>
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top"><? show_fast_tools();?><br /></td>
		</tr>
		</table>
    </form>
</div>
<? 	include "../layout/bottom.inc.php";?>