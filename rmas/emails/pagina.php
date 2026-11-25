<?
	include "settings.inc.php";
	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';}
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
			$sql="Select * from emails WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$IDb=$rs->db_data["ID"];
				$Email=$rs->db_data["Email"];
				$Nome=$rs->db_data["Nome"];
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
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr> 
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr> 
                            <td valign="top">
                
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <? /*<tr> 
                                    <td class="preto" align="left" valign="top" width="120">Grupo:</td>
                                    <td align="left" valign="top" class="preto"><? show_lista($Grupo_ID,"emails","Grupo_ID");?>&nbsp;<a href="../grupos/list.php" class="preto">[editar]</a></td>
                                </tr>*/?>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120"><? echo write_lang('key_nome','Nome');?>:</td>
                                    <td align="left" valign="top"><input name="Nome" style="WIDTH:99%; HEIGHT:20px" maxlength="255" value="<?=$Nome;?>"></td>
                                </tr>
                                <tr> 
                                    <td class="preto" align="left" valign="top" width="120"><? echo write_lang('key_email','Email');?>:</td>
                                    <td align="left" valign="top"><input name="Email" style="WIDTH:99%; HEIGHT:20px" maxlength="255" value="<?=$Email;?>"></td>
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