<?php

global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$dados = new DataObj;
/* $sql = '
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
*/

$sql = '
		SELECT
			pa.bloco_id, pal.bloco_titulo
		FROM
			modelos pa
			INNER JOIN modelos_lang pal ON pal.bloco_id=pa.bloco_id
		WHERE
			pal.bloco_lang="'.$GLOBALS['admin_lang'].'"
			'.($m_marca > 0 ? ' AND pa.m_marca='.$m_marca : '').'
		ORDER BY
			pa.m_marca,
			pa.bloco_ordem ASC,
			pa.bloco_id ASC

		';

$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	while(!$dados->EOF())
	{
		$arr_objs[$dados->db_data['bloco_id']] = $dados->db_data['bloco_titulo'];

		$dados->db_next();
	}
}
$dados->db_close();

$sql = '
		SELECT
			save_id, bloco_id, bloco_titulo, bloco_descricao, bloco_produto, bloco_url, bloco_ext, bloco_ativo, bloco_lang, bloco_x, bloco_y,
			pos_top, pos_left, width, height, anglo
		FROM
			frames_save
		WHERE
			bloco_id='.$_GET['bloco_id'].'
			AND bloco_lang="'.$GLOBALS['admin_lang'].'"
		';
$circulos = '';
$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	$a=0;
	while(!$dados->EOF())
	{
		$a++;
		$arr_acab_save[$a]['save_id'] = $dados->db_data['save_id'];
		$arr_acab_save[$a]['bloco_id'] = $dados->db_data['bloco_id'];
		$arr_acab_save[$a]['bloco_titulo'] = $dados->db_data['bloco_titulo'];
		$arr_acab_save[$a]['bloco_descricao'] = $dados->db_data['bloco_descricao'];
		$arr_acab_save[$a]['bloco_produto'] = $dados->db_data['bloco_produto'];
		$arr_acab_save[$a]['bloco_url'] = $dados->db_data['bloco_url'];
		$arr_acab_save[$a]['bloco_ext'] = $dados->db_data['bloco_ext'];
		$arr_acab_save[$a]['bloco_ativo'] = $dados->db_data['bloco_ativo'];
		$arr_acab_save[$a]['bloco_lang'] = $dados->db_data['bloco_lang'];

		$arr_acab_save[$a]['tratado'] = (($dados->db_data['pos_top']!=0 || $dados->db_data['pos_left']!=0 || $dados->db_data['width']!=0 || $dados->db_data['height']!=0 || $dados->db_data['anglo']!=0) ? 1 : 0);


		$dados->db_next();
	}
}
$dados->db_close();
?>
<style>
.maisclone{background:url(../../layout/<?php echo PROJETO?>/images/mais.png);position:absolute; width:40px;height:40px;cursor:pointer}
.maisclone:hover{background:url(../../layout/<?php echo PROJETO?>/images/mais_h.png); width:40px;height:40px;cursor:pointer}
.maisclone.active{background:url(../../layout/<?php echo PROJETO?>/images/mais_h.png); width:40px;height:40px;cursor:pointer}
</style>
<script type="text/javascript" src="../../external_api/jquery-ui-1.10.3.js"></script>
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top" style="line-height:16px;font-size:12px">

<table border="0" cellpadding="2" cellspacing="0" id="tableAcabamentos">
<tr>
<td height="25"><b>Referência</b></td>
<td><img src="cm_fill.gif" /></td>
<td height="25"><b>Modelo</b></td>
<td><img src="cm_fill.gif" /></td>
<td height="25"><b>Imagem</b></td>
<td><img src="cm_fill.gif" /></td>
<td height="25" colspan="2"><b>Ordem</b></td>
<td><img src="cm_fill.gif" /></td>

<td ><b>Ativo</b></td>

</tr>
<?php
$a = 0;
for($a=1; $a<=count($arr_acab_save); $a++)
{
?>
	<tr id="ac_ln_<?php echo $a?>">
	<td class="preto" style="border-bottom:#c0c0c0 2px solid">
	<div style="float:left;padding-top:5px;padding-right:5px">(<?php echo $arr_acab_save[$a]['save_id']?>)</div>
	<div style="float:left"> <input id="titulo<?php echo $a?>" name="titulo[]" type="text" class="objsclone box" value="<?php echo $arr_acab_save[$a]['bloco_titulo']?>" style="width:100px;text-align:left;text-indent:0" /></div>

	</td>
	<td style="border-bottom:#c0c0c0 2px solid"><img src="cm_fill.gif" /></td>
	<td class="preto" style="border-bottom:#c0c0c0 2px solid">
	<select id="descricao<?php echo $a?>" name="descricao[]" class="descclone box" style="width:250px;text-align:left;text-indent:0">
	<option value="">Selecionar</option>
	<?php foreach($arr_objs as $key => $value){ ?>
	<option value="<?php echo $key;?>" <?php if($arr_acab_save[$a]['bloco_descricao']==$key){echo 'selected="selected"';}?>><?php echo $value;?></option>
	<?php } ?>
	</select>


	</td>
	<td style="border-bottom:#c0c0c0 2px solid"><img src="cm_fill.gif" /></td>
	<td class="preto" style="border-bottom:#c0c0c0 2px solid">
	<?php
	if($arr_acab_save[$a]['bloco_ext'] && file_exists('../../files/'.FOLDER.'/'.$arr_acab_save[$a]['bloco_ext']))
	{
		if($arr_acab_save[$a]['tratado'] == 1) echo '<i class="fa fa-check-circle" style="color:#45c94c;font-size:20px"></i> | ';
		echo '<a class="pretourl_small fancybox_url" href="jquery.ui.rotation/rotate.php?image=../../../files/'.FOLDER.'/'.$arr_acab_save[$a]['bloco_ext'].'&id='.$arr_acab_save[$a]['save_id'].'&id_pai='.$arr_acab_save[$a]['bloco_id'].'" target="_blank" class="url_reno">Configurar Imagem</a><br />';
		// echo '<a class="pretourl_small fancybox_url" href="imagemanipulation/index.php?image=../../../files/'.FOLDER.'/'.$arr_acab_save[$a]['bloco_ext'].'&id='.$arr_acab_save[$a]['save_id'].'&id_pai='.$arr_acab_save[$a]['bloco_id'].'" target="_blank" class="url_reno">Configurar Imagem</a><br />';


	}?><input id="ambiente<?php echo $a?>" name="ambiente<?php echo $a?>" type="file" name="bloco_ext" id="bloco_ext" />
	</td>
	<td style="border-bottom:#c0c0c0 2px solid"><img src="cm_fill.gif" /></td>
	<td class="preto" style="border-bottom:#c0c0c0 2px solid">
	<input id="produto<?php echo $a?>" name="produto[]" type="text" class="prodclone box" value="<?php echo $arr_acab_save[$a]['bloco_produto']?>" style="width:50px;text-align:center;text-indent:0" />
	</td>


	<td style="border-bottom:#c0c0c0 2px solid"><img src="cm_fill.gif" /></td>
	<td class="preto" style="border-bottom:#c0c0c0 2px solid;text-align:center">
	<input type="checkbox" class="ativoclone" name="ac_ativo[]" <?php if($arr_acab_save[$a]['bloco_ativo']==1){ echo 'checked="checked"';}?> id="ac_ativo<?php echo $a?>" value="1" />
	<input type="hidden" id="ac_cordx<?php echo $a?>" name="ac_cordx[]" class="cordxclone" value="<?php echo $arr_acab_save[$a]['bloco_x']?>" />
	<input type="hidden" id="ac_cordy<?php echo $a?>" name="ac_cordy[]" class="cordyclone" value="<?php echo $arr_acab_save[$a]['bloco_y']?>" />
	<input type="hidden" id="saveid<?php echo $a?>" name="saveid[]" class="saveidclone" value="<?php echo $arr_acab_save[$a]['save_id']?>" />
	</td>
	<td style="border-bottom:#c0c0c0 2px solid"><img src="img/document_delete.png" width="22" title="Eliminar Linha" style="cursor:pointer" class="createAcabamento" onclick="removeAcabamento(<?php echo $a?>);" /></td>
	</tr>
<?php
}
?>
</table>
<div style="clear:both"></div>
<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr>
<td class="preto" align="left" colspan="10">
<input type="hidden" name="total_linhas_acab" id="total_linhas_acab" value="<?php echo ($a-1)?>" />
<input type="button" value="Adicionar Nova Linha"  onclick="adicionarNovoAcabamento();" style="-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;height:30px;text-align:center;font-size:12px;color:#FFF;cursor:pointer;line-height:30px;margin:0 auto;background-color:#ff7e43;background-position:right;background-repeat:no-repeat;margin-top:8px;"/>
</td>
</tr>

<tr id="ac_ln_0" style="visibility:hidden">

<td class="preto"><div style="float:left;padding-top:5px;padding-right:5px;width:20px">&nbsp;</div><div style="float:left"><input type="text" class="objsclone box" value="" style="width:100px;text-align:left;text-indent:0" /></div></td>
<td><img src="cm_fill.gif" /></td>
<td class="preto"><select class="descclone box" style="width:250px;text-align:left;text-indent:0">
<option value="">Selecionar</option>
<?php foreach($arr_objs as $key => $value){ ?><option value="<?php echo $key;?>"><?php echo $value;?></option><?php } ?>
</select></td>

<td><img src="cm_fill.gif" /></td>
<td class="preto"><input type="file" class="acabaclone" /></td>

<td><img src="cm_fill.gif" /></td>
<td class="preto"><input type="text" class="prodclone box" value="" style="width:50px;text-align:left;text-indent:0" /></td>




<td><img src="cm_fill.gif" /></td>
<td class="preto" align="center"><input type="checkbox" class="ativoclone" value="1" /><input type="hidden" class="cordxclone" value="" /><input type="hidden" class="cordyclone" value="" /><input type="hidden" class="saveidclone" value="0" /></td>

</tr>

</table>
</td>
</tr>
</table>
<div id="maisclone" class="maisclone" style="visibility:hidden"></div>
<script>
function adicionarNovoAcabamento()
{
	var num = Number($("#total_linhas_acab").val());
	var quantity = Number(num+1);

	// CLONAR CAMPOS
	var clone = $("#ac_ln_0").clone(true);
	clone.attr('id', 'ac_ln_' + quantity).attr('style','visibility:visible');
	clone.find('.saveidclone').each(function() { this.setAttribute('id', 'saveid'+quantity);this.setAttribute('name', 'saveid[]');});
	clone.find('.acabaclone').each(function() { this.setAttribute('id', 'ambiente'+quantity);this.setAttribute('name', 'ambiente'+quantity);});
	clone.find('.prodclone').each(function() { this.setAttribute('id', 'produto'+quantity);this.setAttribute('name', 'produto[]');});
	clone.find('.urlclone').each(function() { this.setAttribute('id', 'url'+quantity);this.setAttribute('name', 'url[]');});
	clone.find('.procurarprod').each(function() { this.setAttribute('href', 'getListaProdutos.php?field=produto'+quantity);});
	// clone.find('.procurarurl').each(function() { this.setAttribute('href', 'getListaPaginas.php?field=url'+quantity);});
	clone.find('.objsclone').each(function() { this.setAttribute('id', 'titulo'+quantity);this.setAttribute('name', 'titulo[]');});
	clone.find('.descclone').each(function() { this.setAttribute('id', 'descricao'+quantity);this.setAttribute('name', 'descricao[]');});
	clone.find('.ativoclone').each(function() { this.setAttribute('id', 'ac_ativo'+quantity);this.setAttribute('name', 'ac_ativo[]');});
	clone.find('.cordxclone').each(function() { this.setAttribute('id', 'ac_cordx'+quantity);this.setAttribute('name', 'ac_cordx[]');});
	clone.find('.cordyclone').each(function() { this.setAttribute('id', 'ac_cordy'+quantity);this.setAttribute('name', 'ac_cordy[]');});
	clone.appendTo('#tableAcabamentos');

	// COLONAR CIRCULO
	var clone2 = $("#maisclone").clone(true);
	clone2.attr('id', 'ac_pt_' + quantity).attr('style','visibility:visible;position:absolute;top:20px;left:20px');
	clone2.appendTo('#imgpoints');

	// A CIRCULO QUE FOI CLONADO VAI TORNA-SE DRAGABLE
	init('ac_pt_' + quantity, quantity);

	// AO CLICAR NO CIRCULO CLONADO VAMOS SELECIONAR OS CAMPOS
	$('#ac_pt_' + quantity).live('click', function()
	{
		// todos os que tiverem selecionados vamos retirar a selecao
		$('.maisclone').each(function(){ $('#'+$(this).attr('id')).removeClass('active');$('#'+$(this).attr('id')).css('z-index','9999');});
		$('#tableAcabamentos td').each(function(){ $(this).css('background','none');});

		$('tr#ac_ln_' + quantity+' td').css('background','#9eccff');
		$('#ac_pt_' + quantity).addClass('active');
		$('#ac_pt_' + quantity).css('z-index','99999');

		// return false;
	});

	// SE CLICARMOS NA LINHA DOS CAMPOS VAMOS SELECIONAR O CIRCULO
	$('#ac_ln_' + quantity).live('click', function()
	{
		// todos os que tiverem selecionados vamos retirar a selecao
		$('.maisclone').each(function(){ $('#'+$(this).attr('id')).removeClass('active');$('#'+$(this).attr('id')).css('z-index','9999');});
		$('#tableAcabamentos td').each(function(){ $(this).css('background','none');});

		$('tr#ac_ln_' + quantity+' td').css('background','#9eccff');
		$('#ac_pt_' + quantity).addClass('active');
		$('#ac_pt_' + quantity).css('z-index','99999');

		// return false;
	});

	$("#total_linhas_acab").val(quantity);
}
$( document ).ready(function()
{
	<?php for($a=1; $a<=count($arr_acab_save); $a++){?>

	// A CIRCULO QUE FOI CLONADO VAI TORNA-SE DRAGGABLE
	init('ac_pt_<?php echo $a?>', <?php echo $a?>);

	// AO CLICAR NO CIRCULO CLONADO VAMOS SELECIONAR OS CAMPOS
	$('#ac_pt_<?php echo $a?>').live('click', function()
	{
		// todos os que tiverem selecionados vamos retirar a selecao
		$('.maisclone').each(function(){ $('#'+$(this).attr('id')).removeClass('active');$('#'+$(this).attr('id')).css('z-index','9999');});
		$('#tableAcabamentos td').each(function(){ $(this).css('background','none');});

		$('tr#ac_ln_<?php echo $a?> td').css('background','#9eccff');
		$('#ac_pt_<?php echo $a?>').addClass('active');
		$('#ac_pt_<?php echo $a?>').css('z-index','99999');

		// return false;
	});

	// SE CLICARMOS NA LINHA DOS CAMPOS VAMOS SELECIONAR O CIRCULO
	$('#ac_ln_<?php echo $a?>').live('click', function()
	{
		// todos os que tiverem selecionados vamos retirar a selecao
		$('.maisclone').each(function(){ $('#'+$(this).attr('id')).removeClass('active');$('#'+$(this).attr('id')).css('z-index','9999');});
		$('#tableAcabamentos td').each(function(){ $(this).css('background','none');});

		$('tr#ac_ln_<?php echo $a?> td').css('background','#9eccff');
		$('#ac_pt_<?php echo $a?>').addClass('active');
		$('#ac_pt_<?php echo $a?>').css('z-index','99999');

		// return false;
	});
<?php }?>
});
function removeAcabamento(a){ $('#ac_ln_'+a).remove();}

function init(id, quantity)
{
	$('#'+id).draggable(
	{
		containment: '#imgpoints',
		cursor: 'move',
		snap: '#imgpoints',

		drag: function(event, ui)
		{
			id_j = str_replace('ac_pt_', '', ui.helper[0].id);
			o = $(this).offset();
			p = $(this).position();

			$('#ac_cordx'+id_j).val(p.left);
			$('#ac_cordy'+id_j).val(p.top);
        }
	});
}
function str_replace(search, replace, subject, count)
{
	var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0, f = [].concat(search), r = [].concat(replace), s = subject, ra = Object.prototype.toString.call(r) === '[object Array]', sa = Object.prototype.toString.call(s) === '[object Array]'; s = [].concat(s);
	if (count) { this.window[count] = 0; }

	for (i = 0, sl = s.length; i < sl; i++)
	{
		if (s[i] === '') { continue;}
		for (j = 0, fl = f.length; j < fl; j++)
		{
			temp = s[i] + '';
			repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
			s[i] = (temp).split(f[j]).join(repl);
			if (count && s[i] !== temp)
			{
				this.window[count] += (temp.length - s[i].length) / f[j].length;
			}
		}
	}
	return sa ? s : s[0];
}

</script>