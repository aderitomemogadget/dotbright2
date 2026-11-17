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
			$rs=new DataObj;
			$rs2=new DataObj;
			$sql="Select * from templates_site WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$ID=$rs->db_data["ID"];
				$visivel= $rs->db_data["Visivel"];
				$destaque= $rs->db_data["Destaque"];
				$I1=$rs->db_data["img_ext"];
				$ordem=$rs->db_data["Ordem"];
				$D=str2date($rs->db_data["Data"]);
				$D2=str2date($rs->db_data["Data2"]);
				$margem_top=$rs->db_data["margem_top"];
				$url=$rs->db_data["URL"];
				$bg_color=$rs->db_data["bg_color"];
				$efeito=$rs->db_data["efeito"];
				$site_width=$rs->db_data["site_width"];
				$estilo=$rs->db_data["estilo"];
				$estilo2=$rs->db_data["estilo2"];
				$tipo=$rs->db_data["Tipo"];

				$sql="Select * from templates_site_lang WHERE ID_PAI=$ID and Lang='$admin_lang'";
				$db_res=$rs2->db_open($db_link,$DB_name,$sql);
				if($rs2)
				{
					$C=$rs2->db_data["Cabecalho"];
					$texto1=$rs2->db_data["Texto1"];
					$texto2=$rs2->db_data["Texto2"];
					$texto3=$rs2->db_data["Texto3"];
					$texto4=$rs2->db_data["Texto4"];
				}
				$rs2->db_close();
			}
			$rs->db_close(1);
		}
	}
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<SCRIPT language=Javascript src="ColorPicker2.js"></SCRIPT>
<SCRIPT language=JavaScript>
	var cp = new ColorPicker('window'); // Popup window
	var cp2 = new ColorPicker(); // DIV style
</SCRIPT>
<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
<div class="center">
 	<form name="form" method="post" action="pagina_trata.php?ID=<?=$ID;?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
    	<input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
		<input type="hidden" name="slashes" value="'">
        <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
						<tr>
						<td valign="top">
						<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
						<tr>
							<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar à lista de templates</a></td>
						</tr>
						</table>
					</tr>
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing="2" cellpadding=0>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="150"><? echo write_lang('key_ordem','Ordem');?>:</td>
                                    <td align="left" valign="top"><input name="ordem" style="WIDTH:50px; HEIGHT:20px" maxlength="255" value="<?=$ordem;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="150"><? echo write_lang('key_nome_da_template','Nome da template');?>:</td>
                                    <td align="left" valign="top"><input name="cabecalho" style="WIDTH:99%; HEIGHT:20px" maxlength="255" value="<?=$C;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="150">Tipo:</td>
                                    <td align="left" valign="top">
                                    	<select name="tipo">
                                    		<option value="" <? if(!$tipo) echo 'selected';?>>---</option>
                                    		<option value="1" <? if($tipo==1) echo 'selected';?>>Ficha de produto</option>
                                    		<option value="2" <? if($tipo==2) echo 'selected';?>>Catálogo de produto</option>
                                    		<option value="3" <? if($tipo==3) echo 'selected';?>>Produto - bloco</option>
                                    	</select>
                                    </td>
                                </tr>
                             	<tr>
                                    <td class="preto" align="left" valign="top" width="150">
                                        Top Image:<br>
                                        <table width="100%" bgcolor="#cccccc" border=0 cellspacing=0 cellpadding=0>
                                        <tr><td><font style="font-family: verdana; font-size: 9px"><? echo write_lang('key_resolucao','Resolução');?> - <i>72 DPIs</i></font></td></tr>
                                        </table>
                                    </td>
                                    <td class="preto" align="left" valign="top"><?
                                    if($I1!="")
                                    {
                                        ?><a href="<?="../../files/templates_site/".$ID."_1.".$I1;?>" target="_blank">See Image</a><br><?
                                        if($task!="apagar"){ ?><? echo write_lang('key_apagar','Apagar');?> <input type="checkbox" name="Apagar_img1" value="1"><br><? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?>: <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" /><? }
                                    }
                                    else
                                    {?><? echo write_lang('key_nova','Nova');?>: <input type="file" name="img_peq1"><input type="hidden" name="tem_foto1" value="0"><? } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="150" class="preto"><? echo write_lang('key_activar_template','Activar template');?>:</td>
                                    <td class="preto"><? echo write_lang('key_sim','Sim');?> <input name="visivel" type="checkbox" id="visivel" value="1" <? if($visivel) echo "checked"; ?> /></td>
                                </tr>
                                </table><br />
                                CODIGO:
                            <table width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                            <tr>
                                <td align="left" valign="top"><textarea id="texto1" name="texto1" style="font-size:11px;height:600px"><?=stripslashes($texto1);?></textarea></td>
                            </tr>
                            </table>
                			</td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table><? show_page_buttons();?>
            </td>
			<td width="10">&nbsp;</td>
			<td valign="top" width="200"><? show_fast_tools();?><? show_other_templates($ID);?><br>
				<table style="width:100%; background:#c6e2ee;" cellspacing="0" cellpadding="5">
				<tr><td align="left" style="font-size:9px">[name] - Nome do Recomendado</td></tr>
				<tr><td align="left" style="font-size:9px">[name2] - Nome do Recomendador</td></tr>
				<tr><td align="left" style="font-size:9px">[produto] - Produto Recomendado</td></tr>
				<tr><td align="left" style="font-size:9px">[code] - Token de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[name] - Nome de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[surname] - Apelido de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[company] - Empresa de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[address] - Endereço de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[zipcode] - Código Postal de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[country] - País de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[email] - E-mail de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[username] - Username de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[password] - Password de Registo</td></tr>
				<tr><td align="left" style="font-size:9px">[apelido] -  Apelido do Cliente</td></tr>
				<tr><td align="left" style="font-size:9px">[username] - Username do Cliente</td></tr>
				<tr><td align="left" style="font-size:9px">[password] - Password do Cliente</td></tr>
				</table>
			</td>
        </tr>
        </table>
		</form></div>
<? 	include "../layout/bottom.inc.php";?>