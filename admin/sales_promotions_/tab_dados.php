<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left">Início Campanha:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td><input type="text" name="DATA_INICIO" id="DATA_INICIO" class="box dropdate2" style="width:120px" value="<?php echo $DATA_INICIO?>" /></td>
<td align="right" align="left" width="130">Fim Campanha:&nbsp;</td>
<td><input type="text" name="DATA_FIM" id="DATA_FIM" class="box dropdate2" style="width:120px" value="<?php echo $DATA_FIM?>" /></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td width="130" align="left">Designação:</td>
<td align="left"><input class="form-control" type="text" name="DESIGNACAO" id="DESIGNACAO" value="<?php echo $DESIGNACAO;?>" class="new_input" /></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Nº Campanhas:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td><input type="text" name="MAXIMO" id="MAXIMO" class="form-control" value="<?php echo $MAXIMO?>" style="width:120px" /></td>
<td><a href="#" onclick="return false;" title="Nº de campanhas que podem ser adquiridas" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a></td>
<td align="right" align="left" width="150">Tempo(<span style="font-size:11px">MINUTOS</span>):&nbsp;</td>
<td><input type="text" name="TEMPO" id="TEMPO" class="form-control" value="<?php echo (isset($TEMPO) && $TEMPO > 0) ? $TEMPO : 60?>" style="width:120px" /></td>
<td><a href="#" onclick="return false;" title="Tempo em que a campanha fica reservada ao user no carrinho caso este não a finalize" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php /* ?>
<tr>
<td align="left">Imagem:</td>
<td align="left"><?php if($IMAGEM_EXT && file_exists('../../files/documentos/'.$IMAGEM_EXT)){echo '<a class="pretourl_small fancybox_img" href="'.$GLOBALS['site_http_url'].'files/documentos/'.$IMAGEM_EXT.'?embedded=true">Abrir Ficheiro</a><br />';}?><input type="file" name="IMAGEM_EXT" id="IMAGEM_EXT" /></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php  */?>
<tr>
<td align="left">Descrição:</td>
<td align="left" style="font-size:11px">(Visivel apenas no backoffice)<br /><textarea name="DESCRICAO" id="DESCRICAO" class="form-control"><?php echo $DESCRICAO?></textarea></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Ativo:</td>
<td align="left">
<label class="label_check" for="ATIVO"><input type="checkbox" name="ATIVO" id="ATIVO" value="1" <?php if($ATIVO==1){ echo 'checked="checked"';} ?> /></label>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>