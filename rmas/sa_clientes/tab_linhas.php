<?php

global $DB_host,$DB_user,$DB_pass,$DB_name;	
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$dados = new DataObj;


$sql = '
		SELECT 
			 '.implode(',', $CMPS_MODULO_DADOS_LANG).'
		FROM 
			'.TBMODULO_LANG.'
		WHERE
			ID_Galeria='.$_GET['ID'].'
		';
$circulos = '';
$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	$a=0;
	while(!$dados->EOF())
	{
		$a++;
		$arr_acab_save[$a]['ID'] = $dados->db_data['ID'];
		$arr_acab_save[$a]['ID_Galeria'] = $dados->db_data['ID_Galeria'];
		$arr_acab_save[$a]['ID_Product'] = htmlentities($dados->db_data['ID_Product']);
		$arr_acab_save[$a]['ID_COR'] = htmlentities($dados->db_data['ID_COR']);
		$arr_acab_save[$a]['Product_Desc'] = htmlentities($dados->db_data['Product_Desc']);
		$arr_acab_save[$a]['Criado'] = htmlentities($dados->db_data['Criado']);
	
		
		$arr_acab_save[$a]['bloco_prazo_entrega'] = $dados->db_data['bloco_prazo_entrega'];
				
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
<td height="25" colspan="2"><b>Produto</b></td>
<td colspan="2"><img src="cm_fill.gif" /></td>
</tr>
<?php 
$a = 0;
for($a=1; $a<=count($arr_acab_save); $a++)
{
?>
	<tr id="ac_ln_<?php echo $a?>">
	<td class="preto" style="border-bottom:#c0c0c0 2px solid"><input id="produto_desc<?php echo $a?>" autocomplete="off" readonly="" name="produto_desc[]" type="text" class="produto_descclone box" value="<?php echo $arr_acab_save[$a]['Product_Desc']?>" style="width:650px;text-align:left;text-indent:0" /><input id="produto<?php echo $a?>" name="produto[]" type="hidden" class="prodclone box" value="<?php echo $arr_acab_save[$a]['ID_Product']?>" style="width:50px;text-align:left;text-indent:0" /><input id="cor<?php echo $a?>" name="cor[]" type="hidden" class="corclone box" value="<?php echo $arr_acab_save[$a]['ID_COR']?>" style="width:50px;text-align:left;text-indent:0" /></td>
	<td class="preto" style="border-bottom:#c0c0c0 2px solid" width="50"><a href="getListaProdutos.php?field=produto<?php echo $a?>&field2=produto_desc<?php echo $a?>&field3=cor<?php echo $a?>" class="procurarprod fancybox2 pretourl_small3"><img src="img/pesquisa_icon.png" border="0" /></a></td>
	<td style="border-bottom:#c0c0c0 2px solid"><input type="hidden" id="bloco_linhas_id<?php echo $a?>" name="bloco_linhas_id[]" class="saveidclone" value="<?php echo $arr_acab_save[$a]['ID']?>" /><img src="img/document_delete.png" width="22" title="Eliminar Linha" style="cursor:pointer" class="createAcabamento" onclick="removeAcabamento(<?php echo $a?>);" /></td>
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
<td class="preto" style="border-bottom:#c0c0c0 2px solid"><input type="text" autocomplete="off" readonly="" class="produto_descclone box" value="" style="width:650px;text-align:left;text-indent:0" /><input type="hidden" class="prodclone box" value="" style="width:50px;text-align:left;text-indent:0" /><input type="hidden" class="corclone box" value="" style="width:50px;text-align:left;text-indent:0" /></td>
<td class="preto" style="border-bottom:#c0c0c0 2px solid" width="50"><a href="" class="procurarprod fancybox2 pretourl_small3"><img src="img/pesquisa_icon.png" border="0" /></a></td>

<td class="preto" style="border-bottom:#c0c0c0 2px solid;text-align:center"><input type="hidden" class="saveidclone" value="" /></td>
</tr>

</table>
</td>
</tr>
</table>

<script>
function adicionarNovoAcabamento()
{
	var num = Number($("#total_linhas_acab").val());
	var quantity = Number(num+1);								
	
	// CLONAR CAMPOS
	var clone = $("#ac_ln_0").clone(true);	
	clone.attr('id', 'ac_ln_' + quantity).attr('style','visibility:visible');
	clone.find('.saveidclone').each(function() { this.setAttribute('id', 'bloco_linhas_id'+quantity);this.setAttribute('name', 'bloco_linhas_id[]');});
	clone.find('.produto_descclone').each(function() { this.setAttribute('id', 'produto_desc'+quantity);this.setAttribute('name', 'produto_desc[]');});
	clone.find('.prodclone').each(function() { this.setAttribute('id', 'produto'+quantity);this.setAttribute('name', 'produto[]');});
	clone.find('.corclone').each(function() { this.setAttribute('id', 'cor'+quantity);this.setAttribute('name', 'cor[]');});
	clone.find('.procurarprod').each(function() { this.setAttribute('href', 'getListaProdutos.php?field=produto'+quantity+'&field2=produto_desc'+quantity+'&field3=cor'+quantity);});
	// clone.find('.objsclone').each(function() { this.setAttribute('id', 'bloco_prazo_entrega'+quantity);this.setAttribute('name', 'bloco_prazo_entrega[]');});
	// clone.find('.prod_qtd1').each(function() { this.setAttribute('id', 'prod_qtd1_'+quantity);this.setAttribute('name', 'prod_qtd1[]');});
	// clone.find('.preco1').each(function() { this.setAttribute('id', 'preco1_'+quantity);this.setAttribute('name', 'preco1[]');});
	// clone.find('.prod_qtd2').each(function() { this.setAttribute('id', 'prod_qtd2_'+quantity);this.setAttribute('name', 'prod_qtd2[]');});
	// clone.find('.preco2').each(function() { this.setAttribute('id', 'preco2_'+quantity);this.setAttribute('name', 'preco2[]');});
	
	clone.appendTo('#tableAcabamentos');
	
	
	// SE CLICARMOS NA LINHA DOS CAMPOS VAMOS SELECIONAR O CIRCULO
	$('#ac_ln_' + quantity).live('click', function()
	{	
		// todos os que tiverem selecionados vamos retirar a selecao
		$('.maisclone').each(function(){ $('#'+$(this).attr('id')).removeClass('active');$('#'+$(this).attr('id')).css('z-index','9999');});
		$('#tableAcabamentos td').each(function(){ $(this).css('background','none');});
		
		$('tr#ac_ln_' + quantity+' td').css('background','#9eccff');
		// $('#ac_pt_' + quantity).addClass('active');
		// $('#ac_pt_' + quantity).css('z-index','99999');
		
		// return false;
	});
		 
	$("#total_linhas_acab").val(quantity);	
}
$( document ).ready(function() 
{ 
	<?php for($a=1; $a<=count($arr_acab_save); $a++){?>
		
	// SE CLICARMOS NA LINHA DOS CAMPOS VAMOS SELECIONAR O CIRCULO
	$('#ac_ln_<?php echo $a?>').live('click', function()
	{	
		// todos os que tiverem selecionados vamos retirar a selecao
		$('.maisclone').each(function(){ $('#'+$(this).attr('id')).removeClass('active');$('#'+$(this).attr('id')).css('z-index','9999');});
		$('#tableAcabamentos td').each(function(){ $(this).css('background','none');});
		
		$('tr#ac_ln_<?php echo $a?> td').css('background','#9eccff');
		// $('#ac_pt_<?php echo $a?>').addClass('active');
		// $('#ac_pt_<?php echo $a?>').css('z-index','99999');
		
		// return false;
	});
<?php }?>
});
function removeAcabamento(a){ if(confirm('Tem a certeza que quer remover este produto?')){$('#ac_ln_'+a).remove();}}

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

