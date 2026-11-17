<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="garantia_visivel"><input type="checkbox" name="garantia_visivel" id="garantia_visivel" value="1" <?php if($garantia_visivel==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Acumulativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Acumulativo"><input type="checkbox" name="Acumulativo" id="Acumulativo" value="1" <?php if($Acumulativo==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left">Título:</td>
<td align="left">
<input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo $titulo; ?>" style="width:250px" />
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Info:</td>
<td align="left">
<textarea name="info" id="info" class="form-control"style="width:99%"><?php echo $info; ?></textarea>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Termos e Condições:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left">
<?php if($bloco_ext && file_exists('../../files/'.FOLDER.'/'.$bloco_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$bloco_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="bloco_ext" id="bloco_ext" />
<br><b>Nota:</b> Em caso de não colocar o ficheiro com termos vamos colocar o url para os termos gerais
</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Imagem:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left">
<?php if($img_ext && file_exists('../../files/'.FOLDER.'/'.$img_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$img_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="img_ext" id="img_ext" />
<br><b>Nota:</b> Em caso de não colocar o ficheiro com termos vamos colocar o url para os termos gerais
</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left">Familias:</td>
<td align="left">
<table width="100%" cellpadding=0 cellspacing=0 border=0 >
<tr>

<td style="font-weight:bold">
<?php echo gera_lista_familias($familia_id);?>
</td>

</tr>
</table>
</td>
</tr>



<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>
<td align="left">Nº Anos:</td>
<td align="left">
<input type="text" name="garantia_n_anos" id="garantia_n_anos" class="form-control" value="<?php echo $garantia_n_anos; ?>" style="width:150px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>
<td align="left">Valor Pagar:</td>
<td align="left">

<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input type="text" name="garantia_valor" id="garantia_valor" class="form-control" value="<?php echo $garantia_valor; ?>" style="width:150px" /></td>
<td align="center" width="150">Ativar Promoção</td>
<td align="left">
<label class="label_check" for="promo_ativo"><input type="checkbox" name="promo_ativo" id="promo_ativo" value="1" <?php if($promo_ativo==1){ echo 'checked="checked"';} ?> /></label>
</td>
<td align="center" width="170">Valor Pagar Promoção:</td>
<td align="left">
<input type="text" name="valor_promo" id="valor_promo" class="form-control" value="<?php echo $valor_promo; ?>" style="width:150px" />
</td>
</tr>
</table>


</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left">Valor Início:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left"><input type="text" name="garantia_ini" id="garantia_ini" class="form-control" value="<?php echo $garantia_ini; ?>" style="width:150px" /></td>
<td align="center" width="80">Valor Fim:</td>
<td align="left"><input type="text" name="garantia_fim" id="garantia_fim" class="form-control" value="<?php echo $garantia_fim; ?>" style="width:150px" /></td>
</tr>
</table>
</td>
</tr>

</table>