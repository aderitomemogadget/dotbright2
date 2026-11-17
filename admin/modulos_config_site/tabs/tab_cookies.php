<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Cookies</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" width="150">&nbsp;</td>
<td align="center" width="60">&nbsp;</td>
<td align="center" width="100">&nbsp;</td>
<td align="center" width="140">&nbsp;</td>
<td align="left" width="200">&nbsp;</td>
</tr>
</table>

</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Texto da barra:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="left" width="100%">
				<input type="text" name="cookie_txt_barra" id="cookie_txt_barra" class="form-control" value="<?php echo $cookie_txt_barra; ?>" style="width:100%" />
			</td>		
		</tr>
	</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Texto da modal:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td align="left" width="100%">
			<!-- <input type="text" name="cookie_txt_modal" id="cookie_txt_modal" class="form-control" value="<?php echo $cookie_txt_modal; ?>" style="width:100%" /> -->
			<textarea name="cookie_txt_modal" id="cookie_txt_modal" class="form-control" rows="5" cols="40"><?php echo $cookie_txt_modal; ?></textarea>

		</td>		
	</tr>
	</table>
</td>
</tr>
</td>
</tr>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Posição na página:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="left" width="170">
				topo:
			</td>
			<td align="left">
				<label class="label_check">
					<input type="radio" name="cookie_posicao" id="cookie_topo" value="2" <?php if($cookie_posicao==2){ echo 'checked="checked"';} ?> />
				</label>
			</td>
		</tr>
		<tr>
			<td align="left" width="170">
				centro:
			</td>
			<td align="left">
				<label class="label_check">
					<input type="radio" name="cookie_posicao" id="cookie_centro" value="1" <?php if($cookie_posicao==1){ echo 'checked="checked"';} ?> />
				</label>
			</td>
		</tr>
		<tr>
			<td align="left" width="170">
				fundo:
			</td>
			<td align="left">
				<label class="label_check">
					<input type="radio" name="cookie_posicao" id="cookie_fundo" value="0" <?php if($cookie_posicao==0){ echo 'checked="checked"';} ?> />
				</label>
			</td>
		</tr>
	</table>
</td>
</tr>

</table>