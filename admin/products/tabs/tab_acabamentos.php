<?php

global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$dados = new DataObj;
$arr_acab = [];
$arr_objs = [];
$arr_acab_save = [];
$sql = '
		SELECT
			pa.bloco_id, pal.bloco_titulo
		FROM
			products_acabamentos pa
			INNER JOIN products_acabamentos_lang pal ON pal.bloco_id=pa.bloco_id
		WHERE
			pal.bloco_lang="'.$GLOBALS['admin_lang'].'"
		';

$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	while(!$dados->EOF())
	{
		$arr_acab[$dados->db_data['bloco_id']] = $dados->db_data['bloco_titulo'];

		$dados->db_next();
	}
}
$dados->db_close();


$sql = '
		SELECT
			pa.bloco_id, pal.bloco_titulo, pal.bloco_resumo
		FROM
			products_objetos pa
			INNER JOIN products_objetos_lang pal ON pal.bloco_id=pa.bloco_id
		WHERE
			pal.bloco_lang="'.$GLOBALS['admin_lang'].'"
		';

$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	while(!$dados->EOF())
	{
		$arr_objs[$dados->db_data['bloco_id']] = $dados->db_data['bloco_titulo'].' - '.$dados->db_data['bloco_resumo'];

		$dados->db_next();
	}
}
$dados->db_close();

$sql = '
		SELECT
			save_id, acabamento_id, objeto_id, prod_id, ativo, ordem
		FROM
			products_acabamentos_save
		WHERE
			prod_id='.$ID.'
		ORDER BY
			ordem
		';

$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	$a=0;
	while(!$dados->EOF())
	{
		$a++;
		$arr_acab_save[$a]['ordem'] = $dados->db_data['ordem'];
		$arr_acab_save[$a]['ativo'] = $dados->db_data['ativo'];
		$arr_acab_save[$a]['acabamento_id'] = $dados->db_data['acabamento_id'];
		$arr_acab_save[$a]['objeto_id'] = $dados->db_data['objeto_id'];

		$dados->db_next();
	}
}
$dados->db_close();
?>

<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">

<table border="0" cellpadding="2" cellspacing="0" id="tableAcabamentos">
<tr>
<td height="25"><b>Objeto</b></td>
<td><img src="cm_fill.gif" /></td>
<td height="25"><b>Acabamento</b></td>
<td><img src="cm_fill.gif" /></td>
<td ><b>Ativo</b></td>
<td><img src="cm_fill.gif" /></td>
<td ><b>Ordem</b></td>
</tr>
<?php
$a = 1;
for($a=1; $a<=count($arr_acab_save); $a++)
{
?>
	<tr id="ac_ln_<?php echo $a?>">
	<td class="preto">
	<select id="objeto<?php echo $a?>" name="objeto[]" class="form-control objsclone" style="width:180px">
	<option value="" selected>Objetos</option>
	<?php
	foreach($arr_objs as $ac_id => $ac_title)
	{
		?><option value="<?php echo $ac_id?>" <?php if($arr_acab_save[$a]['objeto_id'] == $ac_id){ echo 'selected="selected"';}?>><?php echo $ac_title?></option><?php
	}
	?>
	</select>
	</td>
	<td><img src="cm_fill.gif" /></td>
	<td class="preto">
	<select id="acabamento<?php echo $a?>" name="acabamento[]" class="form-control acabaclone" style="width:180px">
	<option value="" selected>Acabamento</option>
	<?php
	foreach($arr_acab as $ac_id => $ac_title)
	{
		?><option value="<?php echo $ac_id?>" <?php if($arr_acab_save[$a]['acabamento_id'] == $ac_id){ echo 'selected="selected"';}?>><?php echo $ac_title?></option><?php
	}
	?>
	</select>
	</td>
	<td><img src="cm_fill.gif" /></td>
	<td class="preto"><input type="checkbox" class="ativoclone" name="ac_ativo[]" <?php if($arr_acab_save[$a]['ativo']==1){ echo 'checked="checked"';}?> id="ac_ativo<?php echo $a?>" value="1" /></td>
	<td><img src="cm_fill.gif" /></td>
	<td><input type="text" class="orderclone box" name="ac_ordem[]" id="ac_ordem<?php echo $a?>" value="<?php echo $arr_acab_save[$a]['ordem']?>"  style="width:50px;text-align:center;text-indent:0" /></td>

	<td><img src="avancada/document_delete.png" width="22" title="Eliminar Linha" style="cursor:pointer" class="createAcabamento" onclick="removeAcabamento(<?php echo $a?>);" /></td>
	</tr>
<?php
}
?>

</table>
<div style="clear:both"></div>
<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr>
<td class="preto"></td>
<td class="preto" align="left" colspan="10">
<input type="hidden" name="total_linhas_acab" id="total_linhas_acab" value="<?php echo ($a-1)?>" />
<input type="button" value="Adicionar Nova Linha"  onclick="adicionarNovoAcabamento();" style="-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;height:30px;text-align:center;font-size:12px;color:#FFF;cursor:pointer;line-height:30px;margin:0 auto;background-color:#ff7e43;background-position:right;background-repeat:no-repeat;margin-top:8px;"/>
</td>
</tr>

<tr id="ac_ln_0" style="visibility:hidden">
<td class="preto">
<select class="objsclone" style="width:180px">
<option value="" selected>Objetos</option>
<?php
foreach($arr_objs as $ac_id => $ac_title)
{
	?><option value="<?php echo $ac_id?>"><?php echo $ac_title?></option><?php
}
?>
</select>
</td>
<td><img src="cm_fill.gif" /></td>
<td class="preto">
<select class="acabaclone" style="width:180px">
<option value="" selected>Acabamento</option>
<?php
foreach($arr_acab as $ac_id => $ac_title)
{
	?><option value="<?php echo $ac_id?>"><?php echo $ac_title?></option><?php
}
?>
</select>
</td>
<td><img src="cm_fill.gif" /></td>
<td class="preto"><input type="checkbox" class="ativoclone" value="1" /></td>
<td><img src="cm_fill.gif" /></td>
<td><input type="text" class="orderclone box" value="0" style="width:50px;text-align:center;text-indent:0" /></td>
</tr>

</table>
</td>
</tr>
</table>
<script>
function adicionarNovoAcabamento()
{
	var num = Number($("#total_linhas_acab").val());

	if(num < 50)
	{
		var quantity = Number(num+1);
		var clone = $("#ac_ln_0").clone(true);

		clone.attr('id', 'ac_ln_' + quantity).attr('style','visibility:visible');
		clone.find('.acabaclone').each(function() { this.setAttribute('class', 'form-control');this.setAttribute('id', 'acabamento'+quantity);this.setAttribute('name', 'acabamento[]');});
		clone.find('.objsclone').each(function() { this.setAttribute('class', 'form-control');this.setAttribute('id', 'objeto'+quantity);this.setAttribute('name', 'objeto[]');});
		clone.find('.ativoclone').each(function() { this.setAttribute('id', 'ac_ativo'+quantity);this.setAttribute('name', 'ac_ativo[]');});
		clone.find('.orderclone').each(function() { this.setAttribute('id', 'ac_ordem'+quantity);this.setAttribute('name', 'ac_ordem[]');});

		clone.appendTo('#tableAcabamentos');

		$(".form-control").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});

		$("#total_linhas_acab").val(quantity);
	}
	else
	{
		alert('Só são permitidos 50 Acabamentos!');
	}
}
function removeAcabamento(a){ $('#ac_ln_'+a).remove(); }
</script>
