<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button="Apagar";$task_form='apaga';$btn_class='btn-primary';}
	elseif($ID==0){$task_button="Inserir";$task_form='insere';$btn_class='btn-primary';}
	else {$task_button="Alterar";$task_form='altera';$btn_class='btn-primary';}
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
				$Email=$rs->db_data["email"];
				$Estado=$rs->db_data["estado"];
				$Grupo_ID=$rs->db_data["Grupo_ID"];
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
                <div class="panel panel-inverse overflow-hidden m-b-12">
                    <div class="panel-heading">
                        <h4 style="color:#fff">UTILIZADORES</h4>
                    </div>
                </div>

                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing=0 cellpadding="2"><?php
                                if(isset($dados['Permissoes'][2]) && $dados['Permissoes'][2]==1)
                                {?>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Grupo:</td>
                                    <td align="left" valign="top" class="preto"><? show_lista_users($Grupo_ID,"grupos_logins","Grupo_ID");?></td>
                                </tr><?php
                                }
                                else
                                {
                                    echo '<input type="hidden" name="Grupo_ID" value="'.$Grupo_ID.'">';
                                }?>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Nome:</td>
                                    <td align="left" valign="top"><input class="form-control" name="m_Nome" maxlength="255" value="<?=$m_Nome;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Username:</td>
                                    <td align="left" valign="top"><input class="form-control" name="Username" maxlength="255" value="<?=$Username;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Email:</td>
                                    <td align="left" valign="top"><input type="email" class="form-control" name="Email" maxlength="190" value="<?=$Email;?>" autocomplete="off"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Password:</td>
                                    <td align="left" valign="top" class="preto"><input class="form-control" name="Password" maxlength="255" value="">
                                    (se colocar uma nova password a antiga será eliminada)</td>
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
			<td valign="top" width="200"><? show_fast_tools();?><br /></td>
		</tr>
		</table>
    </form>
</div>
<? 	include "../layout/bottom.inc.php";?>