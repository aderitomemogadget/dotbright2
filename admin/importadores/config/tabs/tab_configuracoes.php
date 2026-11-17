<div style="font-size:15px">
	<h2>Insira o nome dos campos que vem Ficheiro que vai ser importado (Primeira linha do CSV se for CSV)</h2><br>
</div><br>
<table border="1" cellspacing="0" cellpadding="5" style="border:#FFF 1px solid;background:#FFF">
<tr>
<?php
$d=7;
$d2=0;

foreach($GLOBALS['arr_campos_importar'] as $campos)
{
	if($d == $d2)
	{
		echo '</tr><tr>';
		$d2 = 0;
	}
	?><td align="center" style="font-weight:bold;font-size:10px;vertical-align:top">
	<table border="0" cellspacing="0" cellpadding="5">
	<tr><td align="left" style="font-weight:bold"><?php echo ($campos['INFO'] != '' ? $campos['INFO'] : $campos['CAMPO']);?></td></tr>
	<tr><td align="left" style="font-weight:bold"><input class="form-control" type="text" value='<?php echo ${$campos['CAMPO']};?>' name="<?php echo $campos['CAMPO']?>" style="width:120px"></td></tr>
	<tr><td align="left" style="font-weight:bold;color:#FF0000;font-size:10px"><?php if($campos['OBRIGATORIO'] == 1){?>Obrigatório<?php }?></td></tr>
	</table>
	</td><?php
	$d2++;
}
?>
</tr>
</table>