<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="150">Manutenção:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td ><label class="label_check" for="offline"><input type="checkbox" name="offline" id="offline" value="1" <?php if($offline==1){ echo 'checked="checked"';} ?> />Colocar Loja em Manutenção</label></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Cor de Fundo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr><td align="left" width="22">
<div style="width:20px;height:20px;background:#<?php echo ($offline_cor !='' ? str_replace('#', '', $offline_cor): 'transparent'); ?>"></div>
</td>
<td align="left">
<input type="text" name="offline_cor" id="offline_cor" class="form-control" value="<?php echo str_replace('#', '', $offline_cor); ?>" style="width:100px" />
</td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" style="vertical-align:top">Corpo:</td>
<td align="left">
<textarea name="offline_txt" id="offline_txt" class="form-control" style="height:200px;width:98%"><?php echo $offline_txt; ?></textarea>
</td>
</tr>
</table>