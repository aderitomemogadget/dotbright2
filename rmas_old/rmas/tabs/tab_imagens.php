<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" style="border: solid 1px #000">
<tr> 
<td valign="top">

<table width="100%" border="0" cellpadding="2" cellspacing="0" id="list_images">
<tr><td class="preto" style="width:150px"></td><td class="preto"></td></tr>
<?php

$arr_imagens = array();
if($ID) $arr_imagens = getImagensRmas($ID);
$tam = count($arr_imagens);
if($tam > 0)
{
	$next_img_number = $arr_imagens[($tam-1)]['Ordem']+1;

	$input=1;

	for ($i=0; $i < $tam; $i++) 
	{
		$ordem = $arr_imagens[$i]['Ordem'];

		if($ordem==$input)
		{
			echo '<tr>';
			echo '<td class="preto">Imagem '.$ordem.'</td>';
			echo '<td class="preto" align="left" valign="top">';
			echo '<div class="imagens_400"><a href="../../files/rmas/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis").'" target="_blank"><img style="width:100%;" src="../../files/rmas/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis").'"></a></div>';

			if($task!="apagar")
			{
				echo 'Remove <input type="checkbox" name="Apagar_img['.$ordem.']" value="1" /> or Change <input type="file" name="img_peq['.$ordem.']"> <input type="hidden" name="tem_foto['.$ordem.']" value="1" />';
			}
			echo '<br></td>';
			echo '</tr>';
			$input++;
		}
		else
		{
			while ($ordem!=$input) 
			{
				echo '<tr>';
				echo '<td class="preto">Imagem '.($input).'</td>';
				echo '<td class="preto" align="left" valign="top">';
				echo '<input type="file" name="img_peq['.($input).']"> <input type="hidden" name="tem_foto['.($input).']" value="0" />';
				echo '</td>';
				echo '</tr>';
				$input++;
			}
			echo '<tr>';
			echo '<td class="preto">Imagem '.$ordem.'</td>';
			echo '<td class="preto" align="left" valign="top">';
			echo '<div class="imagens_400"><a href="../../files/rmas/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis").'" target="_blank"><img style="width:100%;" src="../../files/rmas/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis").'?>"></a></div>';

			if($task!="apagar")
			{
				echo 'Remove <input type="checkbox" name="Apagar_img['.$ordem.']" value="1" /> or Change <input type="file" name="img_peq['.$ordem.']"> <input type="hidden" name="tem_foto['.$ordem.']" value="1" />';
			}
			echo '<br></td>';
			echo '</tr>';
			$input++;
		}
	}
}
else
{
	$next_img_number = $tam+2;
	echo '<tr>';
	echo '<td class="preto">Imagem 1</td>';
	echo '<td class="preto" align="left" valign="top">';
	echo '<input type="file" name="img_peq[1]"> <input type="hidden" name="tem_foto[1]" value="0" />';
	echo '</td>';
	echo '</tr>';
}
?>                                                      
</table>

<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr>
<td class="preto"></td>
<td class="preto"><input type="hidden" id="next_img_number" value="<?php echo $next_img_number?>" /><input type="button" id="add_img" value="Adicionar +1" style="float:right; padding: 10px 20px;" onclick="adicionarNovaImagem();"/></td></tr>
</table>

</td>
</tr>
</table>
<script>function adicionarNovaImagem(){
	var next=parseInt($("#next_img_number").val());

	var str='<tr><td class="preto">Imagem '+next+'</td><td class="preto" align="left" valign="top"><input type="file" name="img_peq['+next+']"> <input type="hidden" name="tem_foto['+next+']" value="0" /></td></tr>';
	$("#list_images").append(str);
	$("#next_img_number").val(next+1);
}
</script>
