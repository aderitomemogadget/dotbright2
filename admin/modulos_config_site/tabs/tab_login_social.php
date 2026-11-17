<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">ID Login Redes Sociais</td>
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
<td align="left" width="150">&nbsp;</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" width="175">ID da Conta</td>
<td align="left" width="155">Chave</td>
<td align="left" width="140">Ativo</td>
</tr>
</table>

</td>
</tr>




<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Facebook ID:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="facebook_id" id="facebook_id" class="form-control" value="<?php echo $facebook_id; ?>" style="width:140px" />
		</td>
		<td align="left" width="170">
			<input type="text" name="facebook_secret" id="facebook_secret" class="form-control" value="<?php echo $facebook_secret; ?>" style="width:140px" />
		</td>
		<td align="center">
			<label class="label_check" for="facebook_ativo">
				<input type="checkbox" name="facebook_ativo" id="facebook_ativo" value="1" <?php if($facebook_ativo==1){ echo 'checked="checked"';} ?> />
			</label>
		</td>
	</tr>
	</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Google ID:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="google_id" id="google_id" class="form-control" value="<?php echo $google_id; ?>" style="width:140px" /></td>
		<td align="left" width="170">
			<input type="text" name="google_secret" id="google_secret" class="form-control" value="<?php echo $google_secret; ?>" style="width:140px" /></td>
		<td align="left">
			<label class="label_check" for="google_ativo">
				<input type="checkbox" name="google_ativo" id="google_ativo" value="1" <?php if($google_ativo==1){ echo 'checked="checked"';} ?> />
			</label>
		</td>
	</tr>
	</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Twitter ID:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="twitter_id" id="twitter_id" class="form-control" value="<?php echo $twitter_id; ?>" style="width:140px" /></td>
		<td align="left" width="170">
			<input type="text" name="twitter_secret" id="twitter_secret" class="form-control" value="<?php echo $twitter_secret; ?>" style="width:140px" /></td>
		<td align="left">
			<label class="label_check" for="twitter_ativo">
				<input type="checkbox" name="twitter_ativo" id="twitter_ativo" value="1" <?php if($twitter_ativo==1){ echo 'checked="checked"';} ?> />
			</label>
		</td>
	</tr>
	</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">AddThis</td>
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
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">&nbsp;</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" width="175">Pub ID</td>
<td align="left" width="140">Ativo</td>
</tr>
</table>

</td>
</tr>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">AddThis:</td>
<td align="left">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" width="170">
			<input type="text" name="addthis_id" id="addthis_id" class="form-control" value="<?php echo $addthis_id; ?>" style="width:140px" /></td>
		<td align="left">
			<label class="label_check" for="addthis_ativo">
				<input type="checkbox" name="addthis_ativo" id="addthis_ativo" value="1" <?php if($addthis_ativo==1){ echo 'checked="checked"';} ?> />
			</label>
		</td>
	</tr>
	</table>
</td>
</tr>

</table>