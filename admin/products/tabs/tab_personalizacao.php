<?php

global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$dados = new DataObj;$dados2 = new DataObj;
/* ?>
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top" align="right">
<a href="../sa_personalizacao/pagina.php?bloco_id=0" target="_blank" class="preto" style="font-weight:bold;font-size:13px">+ Inserir Personalização</a>
</td>
</tr>
</table> */?>
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">


<?php

$sql = '
		SELECT
			pa.bloco_id, pa.bloco_tipo, pal.bloco_titulo, pp.bloco_id as ppbl, pp.Outros, pp.Outros_on,pp.ordem
		FROM
			personalizacao pa
			INNER JOIN personalizacao_lang pal ON pal.bloco_id=pa.bloco_id
			LEFT JOIN products_personalizacao2 pp ON pp.bloco_id = pa.bloco_id AND prod_id="'.$_GET['ID'].'"
		WHERE
			pal.bloco_lang="'.$GLOBALS['admin_lang'].'"
			AND pa.bloco_id_pai="0"

		GROUP BY
			pa.bloco_id
		ORDER BY
			pp.ordem=0,pp.ordem, pa.bloco_ordem=0, pa.bloco_ordem,pp.ordem,pa.bloco_id
		';

$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	// $sel_obr =  get_personalizacao_prod($_GET['ID']);
	// $sel_obr2 =  get_personalizacao_prod2($_GET['ID']);

	?>
	<div id="aabre" style="border-bottom:#000 1px solid;font-size:15px;padding:10px;padding-left:2px;background:#ffd154;font-weight:bold;cursor:pointer"  onclick="if($('#abre').is(':visible')){$('#abre').hide();$('#aabre').html('Clique para inserir novo&nbsp;');}else{$('#abre').show();$('#aabre').html('Fechar&nbsp;');}">Clique para inserir novo</div>
	<div style="display:none" id="abre">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>

	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid;width:70px">Ordem</td>
	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid;width:220px">Título</td>
	</tr>
	<tr>

	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid;width:70px"><input type="text" name="new_personalizacao_ordem[]" value="" class="form-control" style="width:50px" /></td>
	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid" colspan="3"><input type="text" name="new_personalizacao_titulo[]" value="" class="form-control" style="width:350px" /></td>
	</tr>
	<tr><td class="preto" colspan="4"style="font-weight:bold;font-size:13px;">Especificações</td></tr>
	<tr><td class="preto" colspan="4"><textarea name="new_personalizacao_outros[];" style="height:80px"></textarea></td></tr>
	</table>
	</div>

	<br><br>
	<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">
	<tr>
	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid;width:70px">Ordem</td>
	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid;width:220px">Título</td>
	<td class="preto" style="font-weight:bold;font-size:13px;border-bottom:#c0c0c0 1px solid;width:340px" align="left" colspan="2">Ativar Campo</td>
	</tr>
	<tr><td class="preto" colspan="4">&nbsp;</td></tr>
	</table>

	<?php
	while(!$dados->EOF())
	{
		// $info = get_personalizacao($_GET['ID'], $dados->db_data['bloco_id'], 0);
		$bg=$checked = '';
		if($dados->db_data['Outros_on'] == 1) {$checked = 'checked="checked"'; $bg=';background:#EDBA62';}

		?>
			<br>
			<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;border-bottom:#000 1px solid<?php echo $bg;?>">
			<tr>
			<td class="preto" style="font-weight:bold;font-size:13px;width:70px" ><input type="text" name="personalizacao_ordem[]" value="<?php echo $dados->db_data['ordem']?>" class="form-control" style="width:50px" /></td>
			<td class="preto" style="font-weight:bold;font-size:13px;width:220px"><?php echo $dados->db_data['bloco_id']; ?> - <?php echo $dados->db_data['bloco_titulo'].'<br> <span style="font-size:11px"></span>'; ?></td>

			<td class="preto" align="left" colspan="2" style="width:320px">
			<input type="hidden" value="<?php echo $dados->db_data['bloco_id']?>" name="personalizacao_id[]"><input type="checkbox" name="personalizacao_<?php echo $dados->db_data['bloco_id']; ?>" id="sel<?php echo $dados->db_data['bloco_id']; ?>" <?php echo $checked?> value="1" />
			</td>
			<td class="preto" align="left" colspan="2" style="width:20px;cursor:pointer;font-weight:bold" onclick="if($('#abre<?php echo $dados->db_data['bloco_id']?>').is(':visible')){$('#abre<?php echo $dados->db_data['bloco_id']?>').hide();$('#aabre<?php echo $dados->db_data['bloco_id']?>').html('Abrir&nbsp;');}else{$('#abre<?php echo $dados->db_data['bloco_id']?>').show();$('#aabre<?php echo $dados->db_data['bloco_id']?>').html('Fechar&nbsp;');}" id="aabre<?php echo $dados->db_data['bloco_id']?>">Abrir&nbsp;</td>
			</tr>
			</table>
			<div style="display:none" id="abre<?php echo $dados->db_data['bloco_id']?>">


			<table border="0" cellpadding="2" cellspacing="0" width="100%">
			<tr><td class="preto" colspan="10" style="border-bottom:#c0c0c0 1px solid">&nbsp;</td></tr><tr><td class="preto" colspan="4">&nbsp;</td></tr>
			<tr>

			<td class="preto" align="left" colspan="2" style="width:320px">
			<textarea onfocus="$('#sel<?php echo $dados->db_data['bloco_id']; ?>').attr('checked','checked');" name="personalizacao_outros[];" style="height:150px"><?php echo $dados->db_data['Outros']?></textarea>
			</td>
			</tr>
			<tr><td class="preto" colspan="10" style="border-bottom:#000 1px solid">&nbsp;</td></tr><tr><td class="preto" colspan="4">&nbsp;</td></tr><?php
			?></table></div><?php

		$dados->db_next();
	}

}
$dados->db_close();

?>
</td>
</tr>
</table>
