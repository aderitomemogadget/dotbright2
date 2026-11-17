<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Stock Manual</td>
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
<td align="left" width="150">Aparece quando for</td>
<td align="center" width="60">Valor</td>
<td align="center" width="165">Tag Lingua</td>
<td align="left" width="140">Ativo</td>
</tr>
</table>

</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150" <?php if($stock_azul_ativo==1){ echo 'style="background:#b0cd50;font-weight:bold"';} ?>>Stock Azul:</td>
<td align="left">



<table cellpadding="0" cellspacing="0" border="0" <?php if($stock_azul_ativo==1){ echo 'style="background:#b0cd50"';} ?>>
<tr>
<td align="left" width="150"><?php intervalos_stock('azul', $stock_azul_aparace); ?></td>
<td align="left" width="65"><input type="text" name="stock_azul_qtd" id="stock_azul_qtd" class="form-control" value="<?php echo $stock_azul_qtd; ?>" style="width:50px" /></td>
<td align="left" width="170"><input type="text" name="stock_azul" id="stock_azul" class="form-control" value="<?php echo $stock_azul; ?>" style="width:140px" /></td>
<td align="center"><label class="label_check" for="stock_azul_ativo"><input type="checkbox" name="stock_azul_ativo" id="stock_azul_ativo" value="1" <?php if($stock_azul_ativo==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>

</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" <?php if($stock_vermelho_ativo==1){ echo 'style="background:#b0cd50;font-weight:bold"';} ?>>Stock Vermelho:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0" <?php if($stock_vermelho_ativo==1){ echo 'style="background:#b0cd50"';} ?>>
<tr>

<td align="left" width="150"><?php intervalos_stock('vermenho', $stock_vermenho_aparace); ?></td>
<td align="left" width="65"><input type="text" name="stock_vermelho_qtd" id="stock_vermelho_qtd" class="form-control" value="<?php echo $stock_vermelho_qtd; ?>" style="width:50px" /></td>
<td align="left" width="170"><input type="text" name="stock_vermelho" id="stock_vermelho" class="form-control" value="<?php echo $stock_vermelho; ?>" style="width:140px" /></td>
<td align="left"><label class="label_check" for="stock_vermelho_ativo"><input type="checkbox" name="stock_vermelho_ativo" id="stock_vermelho_ativo" value="1" <?php if($stock_vermelho_ativo==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>

</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" <?php if($stock_amarelo_ativo==1){ echo 'style="background:#b0cd50;font-weight:bold"';} ?>>Stock Amarelo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0" <?php if($stock_amarelo_ativo==1){ echo 'style="background:#b0cd50"';} ?>>
<tr>
<td align="left" width="150"><?php intervalos_stock('amarelo', $stock_amarelo_aparece); ?></td>
<td align="left" width="65"><input type="text" name="stock_amarelo_qtd" id="stock_amarelo_qtd" class="form-control" value="<?php echo $stock_amarelo_qtd; ?>" style="width:50px" /></td>
<td align="left" width="170"><input type="text" name="stock_amarelo" id="stock_amarelo" class="form-control" value="<?php echo $stock_amarelo; ?>" style="width:140px" /></td>
<td align="left"><label class="label_check" for="stock_amarelo_ativo"><input type="checkbox" name="stock_amarelo_ativo" id="stock_amarelo_ativo" value="1" <?php if($stock_amarelo_ativo==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>

</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" <?php if($stock_verde_ativo==1){ echo 'style="background:#b0cd50;font-weight:bold"';} ?>>Stock Verde:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0" <?php if($stock_verde_ativo==1){ echo 'style="background:#b0cd50"';} ?>>
<tr>

<td align="left" width="150"><?php intervalos_stock('verde', $stock_verde_aparece); ?></td>
<td align="left" width="65"><input type="text" name="stock_verde_qtd" id="stock_verde_qtd" class="form-control" value="<?php echo $stock_verde_qtd; ?>" style="width:50px" /></td>
<td align="left" width="170"><input type="text" name="stock_verde" id="stock_verde" class="form-control" value="<?php echo $stock_verde; ?>" style="width:140px" /></td>
<td align="left"><label class="label_check" for="stock_verde_ativo"><input type="checkbox" name="stock_verde_ativo" id="stock_verde_ativo" value="1" <?php if($stock_verde_ativo==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>

</td>
</tr>

</table>