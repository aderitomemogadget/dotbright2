<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" colspan="2">Base de Dados <font style="font-size: 11px">(Se deixar estes campos em branco, as configurações default serão lidas do ficheiro conf.php)</font></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Servidor:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="dblocalhost" id="dblocalhost" class="form-control" value="<?php echo $dblocalhost; ?>" style="width:140px" />
		</td>
	</tr>
	</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Nome de Utilizador:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="dbusername" id="dbusername" class="form-control" value="<?php echo $dbusername; ?>" style="width:140px" /></td>
	</tr>
	</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Palavra-Passe:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="dbpassword" id="dbpassword" class="form-control" value="<?php echo $dbpassword; ?>" style="width:140px" /></td>
	</tr>
	</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Nome Base de Dados:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="dbname" id="dbname" class="form-control" value="<?php echo $dbname; ?>" style="width:140px" /></td>
	</tr>
	</table>
</td>
</tr>

</table>