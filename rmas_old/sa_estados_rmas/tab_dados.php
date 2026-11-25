
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="100">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Visivel"><input type="checkbox" name="Visivel" id="Visivel" value="1" <?php if($Visivel==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Nivel:</td>
<td width="45"><input type="text" name="Ordem" id="Ordem" class="box" value="<?php echo $Ordem?>" style="width:50px"/></td>
<td align="center" width="180">Cor:</td>
<td width="45"><input type="text" name="Cor" id="Cor" class="box" value="<?php echo $Cor?>" style="width:50px"/></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Envia Email:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Email"><input type="checkbox" name="Email" id="Email" value="1" <?php if($Email==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="220">Envia SMS:</td>
<td width="45"><label class="label_check" for="sms_envia"><input type="checkbox" name="sms_envia" id="sms_envia" value="1" <?php if($sms_envia==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="220">Assistência em modo de leitura:</td>
<td width="45"><label class="label_check" for="f_leitura"><input type="checkbox" name="f_leitura" id="f_leitura" value="1" <?php if($f_leitura==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Grupo:</td>
<td align="left"><?php cabecalhos_estados($f_estado); ?></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Titulo:</td>
<td align="left"><input class="box" type="text" name="Cabecalho" id="Cabecalho" value="<?php echo $Cabecalho;?>" style="width:99%"/></td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Corpo Email:</td>
<td align="left"><textarea class="box" name="Texto" id="Texto" style="width:99%;height:300px"><?php echo $Texto;?></textarea>  <?php show_editor_small("oEdit1", "Texto", 880, 250);?></td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Corpo Email (NOVO):</td>
<td align="left"><textarea class="box" name="Texto2" id="Texto2" style="width:99%;height:300px"><?php echo $Texto2;?></textarea>  <?php show_editor_small("oEdit1", "Texto2", 880, 250);?></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Corpo SMS:</td>
<td align="left"><div id="charNum"></div><textarea class="box" name="Sms" id="Sms" style="width:99%;height:100px"><?php echo $Sms;?></textarea> 
<script>
var max = 150;
$(document).ready(function()
{
	$('#Sms').keyup(function () 
	{	  
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#charNum').text(' chegou ao limite de caracteres');
	  } else {
		var chars = max - len;
		$('#charNum').text('Faltam '+chars+' caracteres');
	  }
	});
	
	 var len = $('#Sms').val().length;
	  if (len >= max) {
		$('#charNum').text(' chegou ao limite de caracteres');
	  } else {
		var chars = max - len;
		$('#charNum').text('Faltam '+chars+' caracteres');
	  }

});
</script>
</td>
</tr>



<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" colspan="2">Observações Internas:<br><br></td>
</tr>
<tr>

<td align="left" colspan="2"><textarea class="box" name="Obs" id="Obs" style="width:99%;height:300px"><?php echo $Obs;?></textarea></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

</table>