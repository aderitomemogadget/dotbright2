<?php
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
			$sql="Select * from linguas WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$IDb=$rs->db_data["ID"];
				$Sigla=$rs->db_data["Sigla"];
				$ordem=$rs->db_data["Ordem"];
				$visivel=$rs->db_data["Visivel"];
				$designacao=$rs->db_data["Cabecalho"];
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
    	<table class="t3" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td align="left" style="vertical-align:top">
            	<? include "../layout/menus.inc.php"; ?>
            	<div class="panel panel-inverse overflow-hidden m-b-12">
			        <div class="panel-heading">
			            <h4 style="color:#fff">LÍNGUAS</h4>
			        </div>
			    </div>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top" align="left">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Ordem:</td>
                                    <td align="left" valign="top"><input name="ordem" style="WIDTH:50px; HEIGHT:20px" maxlength="255" value="<?=$ordem;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Sigla:</td>
                                    <td align="left" valign="top"><input name="sigla" style="WIDTH:20; HEIGHT:20px" maxlength="10" value="<?=$Sigla;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Designacao:</td>
                                    <td align="left" valign="top"><input name="cabecalho" style="WIDTH:99%; HEIGHT:20px" maxlength="255" value="<?=$designacao;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Activar lingua no site:</td>
                                    <td align="left" valign="top"><input type="checkbox" name="visivel" value="1" <? if($visivel=='1') echo "checked";?>> SIM</td>
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
			<td valign="top" width="200" valign="top"><? show_fast_tools();?></td>
		</tr>
		</table>
    </form>
</div>
<? 	include "../layout/bottom.inc.php";?>