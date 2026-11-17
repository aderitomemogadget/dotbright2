<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
<tr>
	<td colspan="2">Colunas:</td>
</tr>
<tr>
	<td align="left" width="200">&nbsp;</td>
	<td align="left">
		<table cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td align="left" width="175">
					separadas por&nbsp;<input type="text" class="form-control" value="<?php echo $Separador;?>" name="Separador" id="Separador" style="width:30px" />
				</td>
				<td align="left" width="155">fechadas com</td>

			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td align="left" width="150">&nbsp;</td>
	<td align="left">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="center" width="175">
					<input type="text" class="form-control" value="<?php echo $Separador;?>" name="Separador" id="Separador" style="width:30px" />
				</td>
				<td align="left" width="175">
					<input type="text" class="form-control" value="<?php echo $Separador2;?>" name="Separador2" id="Separador2" style="width:30px" />
				</td>
			</tr>
		</table>
	</td>
</tr>
	<tr>
		<td style="border-bottom:#c0c0c0 1px solid" colspan="2">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">
		&nbsp;
		</td>
	</tr>
	<tr>
		<td align="left">
			Caminho (URL):
		</td>
		<td align="left">
			<input type="text" name="url" id="url" class="form-control" value="<?php echo $url; ?>" style="width:250px" />
		</td>
	</tr>
</table>