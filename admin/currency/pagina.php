<?php
	include "settings.inc.php";

	if($task=="apagar_draft"){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';$btn_class='btn-primary';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';$btn_class='btn-primary';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';$btn_class='btn-primary';}
	elseif($currency_id==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';$btn_class='btn-primary';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';$btn_class='btn-primary';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';$btn_class='btn-primary';}

	$D=getdate();
	$D2=getdate();
	$currency_country=0;$currency_lingua='';$currency_estado=0;$country='';
	if($currency_id!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;
		$sql="Select * from currency WHERE currency_id=$currency_id";
		$db_res = $rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$currency_id=$rs->db_data["currency_id"];
			$country=$rs->db_data["country"];
			$currency_country=$rs->db_data["currency_country"];
			$currency_lingua=$rs->db_data["currency_lingua"];
			$currency_value=$rs->db_data["currency_value"];
			$currency_estado=$rs->db_data["currency_estado"];
			$currency_symbol=$rs->db_data["currency_symbol"];
			$currency_update=$rs->db_data["currency_update"];
			$I1=$rs->db_data["img_ext"];
		}
		$rs->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<style>
input[type=text].currency
{
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	border:#aaaaaa 1px solid;
	height:24px;
	padding-left:5px
}
</style>
<script>$(".fancybox").fancybox();</script>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?prev=0&amp;currency_id=<?=$currency_id?>&amp;task_form=<?=$task_form?>&amp;dump=<?=uniqID("")?>" enctype="multipart/form-data">
		<input type="hidden" name="slashes" value="'">
    	<table class="t3 text-center" border=0 cellpadding=0 cellspacing=0  align="center">
        <tr>
            <td valign="top"><?php include "../layout/menus.inc.php"; ?>
            	<div class="panel panel-inverse overflow-hidden m-b-12">
			        <div class="panel-heading">
			            <h4 style="color:#fff">MOEDA</h4>
			        </div>
			    </div>

                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="4">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
								<tr>
                                    <td class="preto" align="left" valign="top" width="120"><?php echo write_lang('key_pais','País')?>:</td>
                                    <td align="left" valign="top"><?php echo getCountriesAll($country); ?></td>
									<td rowspan="4" width="200" valign="top" class="preto2" style="background:#b1b1b1"><span style="color:#000"><?php echo write_lang('key_nota','Nota')?>:</span>
									<br /><span style="color:#000;font-size:11px;font-weight:normal">&nbsp;- <?php echo write_lang('key_valores_com_ponto','Valores com ponto')?> 0<u>.</u>00 <br />&nbsp;- <?php echo write_lang('key_aceita_ate_4_casas_decimais','Aceita até 4 casas decimais')?> 0.<u>0000</u></span></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120"><?php echo write_lang('key_moeda','Moeda')?>:</td>
                                    <td align="left" valign="top"><?php getCurrencyCountriesAll($currency_country); ?></td>
                                </tr>
								<tr>
                                    <td class="preto" align="left" valign="top" width="120"><?php echo write_lang('key_lingua','Lingua')?>:</td>
                                    <td align="left" valign="top"><?php linguas_disponiveis($currency_lingua); ?></td>
                                </tr>
								<tr>
									<td class="preto" align="left" valign="top" width="120">Imagem:&nbsp;</td>
									<td align="left" valign="top" class="preto"><?
									if($I1!="" && file_exists('../../files/currency/'.$currency_id.'_1.'.$I1))
									{
										if(file_exists('../../files/currency/'.$currency_id.'_1.'.$I1)){?>&nbsp;&nbsp;<a class="fancybox" href="<?php echo "../../files/currency/".$currency_id."_1.".$I1;?>" style="color:#000">Ver Imagem</a>&nbsp;&nbsp;<?php }?><?
										if($task!="apagar"){?><?php echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <?php echo write_lang('key_ou','Ou');?> <?php echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><?php }
									}
									else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><?php }?></td>

								</tr>
								<tr>
									<td class="preto" align="left" valign="top" width="120">&nbsp;</td>
                                    <td align="left" valign="top" class="preto">&nbsp;</td>
                                </tr>
								<tr>
                                    <td class="preto" align="left" colspan="3" style="border-left:#c0c0c0 1px solid;border-top:#c0c0c0 1px solid;border-right:#c0c0c0 1px solid; background:#c0c0c0" height="35">&nbsp;<span style="font-size:12px"><b><?php echo write_lang('key_se_for_selecionado','Se for selecionado "Update Automático", os seguintes campos vão ser atualizados')?></b></span></td>
                                </tr>
								<tr>
                                    <td class="preto" align="left" valign="top" width="120" style="border-left:#c0c0c0 1px solid" height="25">&nbsp;<?php echo write_lang('key_update_automatico','Update Automático')?>:</td>
                                    <td align="left" valign="top" class="preto" colspan="2" style="border-right:#c0c0c0 1px solid"><input type="checkbox" name="currency_update" value="1" <?php if($currency_update==1) echo "checked";?>> <?php echo write_lang('key_sim','Sim')?></td>
                                </tr>
								<tr>
                                    <td class="preto" align="left" valign="top" width="120" style="border-left:#c0c0c0 1px solid">&nbsp;<?php echo write_lang('key_valor_da_moeda','Valor da Moeda')?>:</td>
                                    <td align="left" valign="top" class="preto" colspan="2" style="border-right:#c0c0c0 1px solid"><input class="currency" type="text" name="currency_value" value="<?php echo $currency_value;?>"> para 1€</td>
                                </tr>
								<tr>
                                    <td class="preto" align="left" valign="top" width="120" style="border-left:#c0c0c0 1px solid">&nbsp;<?php echo write_lang('key_simbolo_da_moeda','Simbolo da Moeda')?>:</td>
                                    <td align="left" valign="top" class="preto" colspan="2" style="border-right:#c0c0c0 1px solid"><input class="currency" type="text" name="currency_symbol" value="<?php echo $currency_symbol;?>"></td>
                                </tr>

								<tr>
                                    <td class="preto" align="left" valign="top" width="120" style="border-left:#c0c0c0 1px solid;border-bottom:#c0c0c0 1px solid">&nbsp;</td>
                                    <td align="left" valign="top" class="preto" colspan="2" style="border-right:#c0c0c0 1px solid;border-bottom:#c0c0c0 1px solid">&nbsp;</td>
                                </tr>
								<tr>
									<td class="preto" align="left" valign="top" width="120">&nbsp;</td>
                                    <td align="left" valign="top" class="preto" colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120"><?php echo write_lang('key_ativo','Ativo')?>:</td>
                                    <td align="left" valign="top" class="preto" colspan="2"><input type="checkbox" name="currency_estado" value="1" <?php if($currency_estado==1) echo "checked";?>> <?php echo write_lang('key_sim','Sim')?></td>
                                </tr>

                                </table>
                           </td>
                       </tr>
                       </table>
                	</td>
				</tr>
                </table><?php show_page_buttons();?>
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top" width="200"><?php show_fast_tools();?></td>
		</tr>
		</table>
    </form>
</div>
<?php 	include "../layout/bottom.inc.php";?>