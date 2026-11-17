<?php
	include "settings.inc.php";
	if($task=='apagar')	{$task_button="Apagar";$task_form='apaga';}
	elseif($ID==0){$task_button="Inserir";$task_form='insere';}
	else {$task_button="Alterar";$task_form='altera';}
	$D=getdate();
	$D2=getdate();
	$v_permissoes = array();
	$C="";$T="";$I1="";$I2="";$ordem="0";

	if($ID!=0)
	{
		if($ID!=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;$rs2=new DataObj;
			$sql="Select * from publications WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$IDb=$rs->db_data["ID"];
				$Nome=$rs->db_data["Nome"];
				$Tipo_=$rs->db_data["Tipo"];
				$Aprovado=$rs->db_data["Aprovado"];
				$Visivel=$rs->db_data["Visivel"];
                $Ordem=$rs->db_data['Ordem'];
				$I1=$rs->db_data["Ext"];

			}
			$rs->db_close(1);
		}
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
		<input type="hidden" name="slashes" value="'">
    	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                     <td class="preto" align="left" valign="top" width="120">Vísivel:</td>
                                    <td class="preto" align="left" valign="top" > <input type="checkbox" <?php if($Visivel) echo ' checked="checked" ';?> name="Visivel" value="1"></td>
                                </tr>
                                </table>
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Tipo:</td>
                                    <td align="left" valign="top">

                                        <select name="Tipo" id="Tipo">
                                        <?php
                                        foreach($publications as $key => $Tipo)
                                        {
                                            ?>
                                            <option value="<?php echo $key;?>" <?php if($key == $Tipo_) echo ' selected '; ?>><?php echo $Tipo;?></option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                </table>
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Titulo:</td>
                                    <td align="left" valign="top"><input name="Nome" style="WIDTH:99%;" maxlength="255" value="<?=$Nome;?>"></td>
                                </tr>
                                </table>
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Aprovado:</td>
                                    <td align="left" valign="top"><input placeholder="2018" name="Aprovado"  maxlength="4" value="<?=$Aprovado;?>"></td>
                                </tr>
                                </table>
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Ordem:</td>
                                    <td align="left" valign="top"><input placeholder="0" name="Ordem"  maxlength="4" value="<?=$Ordem;?>"></td>
                                </tr>
                                </table>
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
									<td width="120" class="preto">Documento:&nbsp;</td>
									<td>
									<?
									if($I1!="" && file_exists("../../files/publications/".$ID."_1.".$I1))
									{

										if(file_exists("../../files/publications/$ID"."_1.".$I1)){?>&nbsp;&nbsp;<a class="fancybox" href="<? echo "../../files/publications/".$ID."_1.".$I1;?>" style="color:#000">Ver Documento</a>&nbsp;&nbsp;<? }?><?
										if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
									}
									else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?></td>
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
			<td valign="top" width="220"><? show_fast_tools();?></td>
		</tr>
		</table>
    </form>
</div>
<? 	include "../layout/bottom.inc.php";?>