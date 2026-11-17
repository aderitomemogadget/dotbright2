
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="100">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Visivel"><input type="checkbox" name="Visivel" id="Visivel" value="1" <?php if($Visivel==1){ echo 'checked="checked"';} ?> /></label></td>
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
</tr>
</table>
</td>
</tr>



<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Titulo da mensagem:</td>
<td align="left"><input class="form-control" type="text" name="Cabecalho" id="Cabecalho" value="<?php echo $Cabecalho;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Corpo Mensagem:</td>
<td align="left"><textarea class="form-control" name="Texto" id="Texto" style="width:99%;height:300px"><?php echo $Texto;?></textarea>  <? show_editor_small("oEdit1", "Texto", 880, 250)?></td>
</tr>

</table>