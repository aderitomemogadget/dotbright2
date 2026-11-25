<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr> 
<td valign="top" style="line-height:13px">
<b>AJUDA:</b><br /><br />
   1 - Para carateristicas que apareçam apenas em na listagem de produtos deverá ser selecionada na coluna 'Visivel em' : Listagem Familias <br />
   2 - Para carateristicas que apareçam apenas em na ficha do produto para seleção deverá ser selecionada na coluna 'Visivel em' : Ficha Produto <br />
   3 - Para carateristicas que apareçam na listagem de produtos e na ficha do produto para seleção deverá ser selecionada na coluna 'Visivel em' : Ambos <br />
   4 - Apenas carateristicas visiveis em 'Ficha de produto' ou 'Ambos' permitem configurar preços <br />
   5 - Sempre que adicionar ou remover carateristicas deverá 'Gravar' <br />
   6 - Sempre que adicionar/alterar/remover carateristicas para seleção na 'Ficha de produto' todos o preços em 'Tabela de Preços por Combinações' vão ser removidos porque as combinações vão ser diferentes
   <br><br>
   Legenda:<br><br>
   <div style="float:left; width:28px"><img src="attr_filtros.png" width="22" title="" /></div> <div style="float:left; width:800px;padding-top:5px"> Adicionar/alterar categorias/filtros</div><div style="clear:both"></div>
   <div style="float:left; width:28px"><img src="avancada/refresh.png" width="22" title="" /></div> <div style="float:left; width:800px"> Sempre que Adicionar/alterar categorias/filtros não precisa de fazer refresh à pagina, clique neste botão e as caixas de seleção vão ser atualizadas com os novos dados na linha</div><br><div style="clear:both"></div>
   <div style="float:left; width:28px"><img src="avancada/document_delete.png" width="22" title="" /></div> <div style="float:left; width:800px;padding-top:5px"> Remover Linha</div><div style="clear:both"></div><br>
</td></tr></table>
<br />
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr> 
<td valign="top">
<?php							
	$arr_cat = getFiltros1();
	if(isset($_GET['ID']) && $_GET['ID'] > 0) $prd_fltrs = getFiltrosProd($ID_Fam);						
?>
<table border="0" cellpadding="2" cellspacing="0" id="tablefiltros">
<tr>
<td colspan="3" height="25"><b>Categoria</b></td>
<td colspan="3"><b>Filtro</b></td>
<td colspan="3"><b>Visivel em</b></td>
</tr>
<?php 
$a = 1;
for($a=1; $a<=count($prd_fltrs); $a++)
{
?>
	<tr id="ln_<?php echo $a?>">
	<td class="preto">Filtro <?php echo $a?>:</td>
	<td class="preto">
	<select name="filtro[]" id="filtro<?php echo $a?>" class="chzn-select catclone" style="width:180px" onchange="getCatValues(this.value, '', <?php echo $a?>)">
	<option value="">Associar Filtro</option>
	<?php
	foreach($arr_cat as $categoria)
	{
		?><option value="<?php echo $categoria[0]?>" <?php if(is_array($prd_fltrs) && $prd_fltrs[$a]['cat_id'] == $categoria[0]) echo "selected"; ?> ><?php echo $categoria[1]?></option><?php					
	}
	?>
	</select>
	</td>
	<td><img src="cm_fill.gif" /></td>
	<td class="preto">Valor <?php echo $a?>:</td><td class="preto" width="180">
	<div id="value_<?php echo $a?>">
	<select name="valor[]" id="valor<?php echo $a?>" class="chzn-select2 valclone" style="width:180px"><option value="">Escolha um filtro para ver valores</option></select>
	</div>
	</td>
	<td class="preto">Opções <?php echo $a?>:</td><td><select  class="chzn-select3" name="opcoes[]" id="opcoes<?php echo $a?>"><option value="0" <?php if(is_array($prd_fltrs) && $prd_fltrs[$a]['filtro_area'] == 0) echo "selected"; ?>>Listagem Família</option><option value="1" <?php if(is_array($prd_fltrs) && $prd_fltrs[$a]['filtro_area'] == 1) echo "selected"; ?>>Ficha do Produto para escolha</option><option value="3" <?php if(is_array($prd_fltrs) && $prd_fltrs[$a]['filtro_area'] == 3) echo "selected"; ?>>Ficha do Produto Descritivo</option><option value="2" <?php if(is_array($prd_fltrs) && $prd_fltrs[$a]['filtro_area'] == 2) echo "selected"; ?>>Ambos</option></select></td>
	<td><img src="attr_filtros.png" title="adicionar novo filtro/valor" onclick="CreateFiltro(<?php echo $a?>)" style="cursor:pointer" /></td>
	<td><img src="avancada/refresh.png" title="atualizar informação" style="cursor:pointer" onclick="refreshf(<?php echo $a?>);" /></td>
	<td><img src="avancada/document_delete.png" width="22" title="Eliminar Linha" style="cursor:pointer" class="createf2" onclick="removef(<?php echo $a?>);" /></td>
	</tr>
<?php 							
}
?>

</table>
<table width="60%" border="0" cellpadding="2" cellspacing="0">
<tr>
<td class="preto"></td>
<td class="preto" align="left" colspan="10">
<input type="hidden" name="total_linhas" id="total_linhas" value="<?php echo ($a-1)?>" />
<input type="button" value="Adicionar Nova Linha"  onclick="adicionarNovoFiltro();" style="-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;height:30px;text-align:center;font-size:12px;color:#FFF;cursor:pointer;line-height:30px;margin:0 auto;background-color:#ff7e43;background-position:right;background-repeat:no-repeat;margin-top:8px;"/></td>
</tr>
<tr id="ln_0" style="visibility:hidden">
<td class="preto txtf">Filtro:</td><td class="preto">
<select class="catclone" style="width:180px">
<option value="" selected>Associar Filtro</option>
<?php
foreach($arr_cat as $categoria)
{
	?><option value="<?php echo $categoria[0]?>"><?php echo $categoria[1]?></option><?php					
}
?>
</select>
</td>
<td><img src="cm_fill.gif" /></td>
<td class="preto txtv">Valor:</td><td class="preto" width="180">
<div class="divchange">
<select class="valclone" style="width:180px"><option value="">Escolha um filtro para ver valores</option></select>
</div>
</td>
<td class="preto txto">Opções <?php echo $a?>:</td><td><select class="optclone"><option value="0">Listagem Família</option><option value="1">Ficha do Produto para escolha</option><option value="3">Ficha do Produto Descritivo</option><option value="2">Ambos</option></select></td>
<td><img src="attr_filtros.png" title="adicionar novo filtro/valor" class="createf" style="cursor:pointer" /></td>
<td><img src="avancada/refresh.png" title="Atualizar Linha" style="cursor:pointer" class="createf2" /></td>
<!--td><img src="avancada/document_delete.png" width="22" title="Eliminar Linha" style="cursor:pointer" class="createf2" /></td-->
</tr>
</table>
</td>
</tr>
</table>
<script>
function CreateFiltro()
{
	window.open('../filters/list.php','_blank');
}
function create_new_filter(campo)
{
	// alert(campo);
	var href = '../filters/pagina_simples.php?category_id=0';
	$.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;
}
function refreshf(a)
{							
	$('#filtro'+a).empty(); $('#valor'+a).empty(); var fmm = '';
	$.post('products_filtros_cat_reload.php', fmm, function(data)
	{
		$('#filtro'+a).append(data);
		$('#filtro'+a).trigger('chosen:updated');
		$('#valor'+a).trigger('chosen:updated');
		// $('.chzn-select').trigger('chosen:updated');
	})
	.error(function(request, status, error) 
	{
	
	});						
}
function removef(a)
{							
	$('#ln_'+a).remove();			
}
function adicionarNovoFiltro()
{
	var num = Number($("#total_linhas").val());	
	
	if(num < 100)
	{
		var quantity = Number(num+1);								
		var clone = $("#ln_0").clone(true);
		
		clone.attr('id', 'ln_' + quantity).attr('style','visibility:visible');
		clone.find('.catclone').each(function()	{ this.setAttribute('class', 'chzn-select');this.setAttribute('onchange', 'getCatValues(this.value, \'\', '+quantity+')');this.setAttribute('id', 'filtro'+quantity);this.setAttribute('name', 'filtro[]');});
		clone.find('.valclone').each(function() { this.setAttribute('class', 'chzn-select2'); this.setAttribute('id', 'valor'+quantity);this.setAttribute('name', 'valor[]');});
		clone.find('.optclone').each(function() { this.setAttribute('class', 'chzn-select3'); this.setAttribute('id', 'opcoes'+quantity);this.setAttribute('name', 'opcoes[]');});
		clone.find('.divchange').each(function(){ this.setAttribute('id', 'value_'+quantity);});
		clone.find('.createf').each(function(){ this.setAttribute('onclick', 'CreateFiltro('+quantity+')');});
		clone.find('.createf2').each(function(){ this.setAttribute('onclick', 'refreshf('+quantity+')');});
		clone.find('.txtf').each(function(){ $(this).html('Filtro '+quantity);});
		clone.find('.txtv').each(function(){ $(this).html('Valor '+quantity);});
		clone.find('.txto').each(function(){ $(this).html('Opções '+quantity);});
		
		clone.appendTo('#tablefiltros');
		
		$(".chzn-select").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
		$(".chzn-select2").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
		$(".chzn-select3").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});

		$("#total_linhas").val(quantity);
	}
	else
	{
		alert('Só são permitidos 11 filtros!');
	}
}
var loaded_content = '';
function getCatValues(cat_id, value_id, a)
{

	$('#value_' + a).html('<img src="avancada/loading.gif" />');
	
	if(value_id) var fmm = 'cat_id='+cat_id+'&value_id='+value_id+'&a='+a;
	else var fmm = 'cat_id='+cat_id+'&a='+a;
	$.post('products_filtros_valores.php', fmm, function(data)
	{
		if(loaded_content!=data)
		{
			$('#value_' + a).empty();
			$('#value_' + a).html(data);
			$(".chzn-select").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
			$(".chzn-select2").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
			$(".chzn-select3").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
			// $(".chzn-select-deselect").chosen({allow_single_deselect:true});
			// $(".chzn-select2-deselect").chosen({allow_single_deselect:true});
		}								
		loaded_content = data;
	})
	.error(function(request, status, error) 
	{
	
	});
}
$(document).ready(function()
{
	$('.fancybox2').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;
	}); 

	<?php for($a=1; $a<=count($prd_fltrs); $a++){?>getCatValues(<?php echo $prd_fltrs[$a]['cat_id']?>, <?php echo $prd_fltrs[$a]['valor_id']?>, <?php echo $a?>);<?php }?>
	
	$('.dropdate').datepicker({showOn: "button", buttonImage: "calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});

});
</script>