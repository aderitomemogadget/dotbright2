<?php

include "settings.inc.php";
$TableName = "forms";
global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

if ($task == "apagar"){	$task_button = "Apagar este Registo";$task_form = "apaga";$btn_class='btn-primary';}
elseif ($task == "duplicar"){	$task_button = "Duplicar este Registo";$task_form = "duplicar";$btn_class='btn-primary';}
elseif ($ID == 0){	$task_button = "Gravar o Novo Registo";$task_form = "insere";$btn_class='btn-primary';}
else{	$task_button = "Gravar alteraçoes";$task_form = "altera";$btn_class='btn-primary';}
$Name = "";
$Email = "";

if ($ID != 0)
{
	$dados = GetRSRecords2($ID);
	if ($dados)
	{
		$Name = $dados->db_data["Name"];
		$Email = $dados->db_data["Email"];
		$titulo = $dados->db_data["titulo"];
		$T = $dados->db_data["Texto0"];
		$T2 = $dados->db_data["Texto2"];
		$T3 = $dados->db_data["Texto3"];
		$T4 = $dados->db_data["Texto4"];
		$assunto = $dados->db_data["assunto"];
		$listagem = $dados->db_data["listagem"];
		$link = $dados->db_data["link"];
		$sub = $dados->db_data["sub"];
		$tipo = $dados->db_data["Tipo"];
		$campos_listagem = $dados->db_data["Campos_listagem"];
		$obrigatorio = $dados->db_data["obrigatorio"];
		$popup = $dados->db_data["popup"];
	}
	$dados->db_close(1);
}

function campos_listagem($id)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $Nome;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$obj = new DataObj;
	$sql = "SELECT * FROM form_fields WHERE ID_ORG='" . $id . "' ORDER BY Ordem";
	$obj->db_open($db_link, $DB_name, $sql);
	while (!$obj->EOF())
	{
		$res .= $obj->db_data["ID"] . ";";
		if ($obj->db_data["Muda"] == 0)	{$res2 .= "{campodesc(" . $obj->db_data["ID"] . ")}<br>";}
		else {$res2 .= "{campodesc(" . $obj->db_data["ID"] . ")}&nbsp;"; }
		$obj->db_next();
	}
	$obj->db_close();
	return array($res, $res2);
}
if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
$campos_listagem_f = campos_listagem($ID);

if ($campos_listagem == ""){	$campos_listagem = $campos_listagem_f[0];}
if (!$T){	$T = $campos_listagem_f[1];}
if (!$caminho_bt_editor){	$caminho_bt_editor1 = "../../../editor/";$caminho_bt_editor2 = "../editor/";}
else{	$caminho_bt_editor1 = $caminho_bt_editor2 = $caminho_bt_editor;}

include "../layout/header.inc.php";
include "../layout/body.inc.php";

?><script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?ID=<?=$ID;?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
		<input type="hidden" name="slashes" value="'">
		<table width="100%" align="center" border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td valign="top" align="left">
				<? include "../layout/menus.inc.php";?>
				<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="4" class="text-center">
				<tr>
					<td valign="top" bgcolor="#87ceee">
						<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
						<tr>
							<td valign="top">
								<table width=100% border=0 cellspacing=0 cellpadding=2>
									<tr><td width=9>&nbsp;</td><td align=left valign=middle class=preto><?=write_lang('key_mostrar_popup','Mostrar em Popup')?></td><td align=left valign=middle class=preto width="5"><input name="popup" type="checkbox" value="1" <?php if ($popup == 1){	echo 'checked="checked"';} ?> /></td><td>&nbsp;</td></tr>
									<tr><td></td><td valign="middle"></td><td valign=middle>&nbsp;</td><td></td></tr>
									<tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><?=write_lang('key_nome_formulario','Nome do Formulário')?></td><td width=5>&nbsp;</td></tr>
									<tr><td>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><input class="form-control" name=name style="WIDTH: 100%" maxlength=255 value="<? echo $Name;?>"></td><td>&nbsp;</td></tr>
									<tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><?=write_lang('key_formulario_email','')?></td><td width=5>&nbsp;</td></tr>
									<tr><td>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><input class="form-control" name=email style="WIDTH: 100%" maxlength=255 value="<? echo $Email;?>"></td><td>&nbsp;</td></tr>
									<tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><?=write_lang('key_formulario_campos_mostrar','')?></td><td width=5>&nbsp;</td></tr>
									<tr><td>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><input class="form-control" name=campos_listagem style="WIDTH: 100%" maxlength=255 value="<? echo $campos_listagem;?>"></td><td>&nbsp;</td></tr>
									<?/*<tr><td>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><input type="checkbox" name="obrigatorio" value="1" <? if($obrigatorio=='1') echo "checked";?>>Esconder Simbolo de campo obrigatório (<font color=#ff0000> * </font>)</td><td>&nbsp;</td></tr>*/ ?>
									<? if ($imprimir){ ?><tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto>layout for printing</td><td width=5>&nbsp;</td></tr>
									<tr><td></td><td colspan="2" align=right valign=middle>	<textarea id="texto" name="texto" style="WIDTH: 100%; HEIGHT: 300px;"><?=$T;?></textarea><? show_editor("oEdit1", "texto", 880, 450) ?></td><td></td></tr><? } ?>
									<tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><?=write_lang('key_formulario_resposta_positiva','');?></td><td width=5>&nbsp;</td></tr>
									<tr><td></td><td colspan="2" valign=middle><textarea id="texto2" name="texto2" style="WIDTH: 100%; HEIGHT: 300px;"><?=$T2;?></textarea><? show_editor("oEdit2", "texto2", "99%", 150) ?></td><td></td></tr>
									<tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><?=write_lang('key_formulario_resposta_negativa','');?></td><td width=5>&nbsp;</td></tr>
									<tr><td></td><td colspan="2" valign=middle><textarea id="texto3" name="texto3" style="WIDTH: 100%; HEIGHT: 300px;"><?=$T3;?></textarea><? show_editor("oEdit3", "texto3", "99%", 150) ?></td><td></td></tr>
									<tr><td></td><td valign="middle"></td><td valign=middle>&nbsp;</td><td></td></tr>
									<tr><td></td><td valign="middle" class=preto><?=write_lang('key_formulario_assunto','');?></td><td valign=middle><input class="form-control" name="assunto" type="text" style="width:60%" value="<?=$assunto?>"></td><td></td></tr>
									<tr><td width=9>&nbsp;</td><td colspan="2" align=left valign=middle class=preto><?=write_lang('key_formulario_corpo_mensagem','');?></td><td width=5>&nbsp;</td></tr>
									<tr><td></td><td colspan="2" valign=middle><textarea id="texto4" name="texto4" style="WIDTH: 100%; HEIGHT: 300px;"><?=$T4;?></textarea><? show_editor("oEdit4", "texto4", "99%", 300) ?></td><td></td></tr>
									<tr><td height=10 width=9></td><td width=84></td><td width=1116></td><td width=5></td></tr>
									<tr><td height=20 width=9></td><td width=84 align="right" bgcolor="#CCCCCC" class="preto"><strong><?=write_lang('key_ajuda','');?>:</strong>&nbsp;</td><td width=1116 bgcolor="#CCCCCC" class="preto"><?=write_lang('key_formulario_tag_nome','');?></td><td width=5></td></tr>
									<tr><td height=10 width=9></td><td width=84></td><td width=1116></td><td width=5></td></tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<? show_page_buttons();?>
			</td>
			<td width="10px">&nbsp;</td><td valign="top" width="200"><? show_fast_tools(); ?></td>
		</tr>
		</table>
	</form>
</div>
<? include "../layout/bottom.inc.php"; ?>