<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td align="left" width="100">Ativo:</td>
	<td align="left">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="45"><label class="label_check" for="Activo"><input type="checkbox" name="Activo" id="Activo" value="1" <?php if($Activo==1){ echo 'checked="checked"';} ?> /></label></td>
		</tr>
		</table>
	</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td align="left" width="150">IP:</td>
	<td align="left">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="45"><input type="text" name="IP" id="IP" class="form-control" value="<?php echo $IP?>" style="width:300px"/></td>
		</tr>
		</table>
	</td>
</tr>

<tr><td colspan="2">&nbsp;</td></tr>

</table>