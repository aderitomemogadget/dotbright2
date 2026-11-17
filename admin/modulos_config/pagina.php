<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button="Apagar";$task_form='apaga';}
	elseif($ID==0){$task_button="Inserir";$task_form='insere';}
	else {$task_button="Alterar";$task_form='altera';}
	$C="";$T="";$I1="";$I2="";$ordem="0";
	if($ID!=0) 
	{
		if($ID!=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			$rs2=new DataObj;
			$sql="Select * from modulos_config WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$IDb=$rs->db_data["ID"];
				$Tipo=$rs->db_data["Tipo"];
				$Nome=$rs->db_data["Nome"];
				$Valor=$rs->db_data["Valor"];
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
    	<table class="t3" border=0 cellpadding=0 cellspacing=0 align="center">
        <tr>
            <td valign="top"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr> 
                    <td valign="top" align="left">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr> 
                            <td valign="top">
                                
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Tipo:</td>
                                    <td align="left" valign="top">
                                    	<SELECT name="Tipo">
                                    		<option value="0" <? if(!$Tipo) echo 'selected';?>>genéricas</option>
                                    		<option value="1" <? if($Tipo==1) echo 'selected';?>>classes para serem usadas pelos elementos</option>
                                    		<!-- <option value="2" <? if($Tipo==2) echo 'selected';?>>---</option>
                                    		<option value="3" <? if($Tipo==3) echo 'selected';?>>---</option> -->
                                    	</SELECT>
                                    </td>
                                </tr>
                                <tr> 
                                    <td class="preto" align="left" valign="top" width="120">Nome:</td>
                                    <td align="left" valign="top"><input name="Nome" style="WIDTH:100%; HEIGHT:20px" maxlength="255" value="<?=$Nome;?>"></td>
                                </tr>
                                <tr> 
                                    <td class="preto" align="left" valign="top" width="120">Valor:</td>
                                    <td align="left" valign="top"><textarea name="Valor" style="WIDTH:100%; HEIGHT:300px"><?=$Valor;?></textarea></td>
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