<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left">Título:</td>
<td align="left">
<input type="text" name="title" id="title" class="form-control" value="<?php echo $title; ?>" style="width:250px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>
<td align="left">Caminho (URL):</td>
<td align="left">
<input type="text" name="url" id="url" class="form-control" value="<?php echo $url; ?>" style="width:250px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left">Caminho Email:</td>
<td align="left">
<input type="text" name="url_email" id="url_email" class="form-control" value="<?php echo $url_email; ?>" style="width:250px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="150">HTTPS:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="200"><label class="label_check" for="forcar_https"><input type="checkbox" name="forcar_https" id="forcar_https" value="1" <?php if($forcar_https==1){ echo 'checked="checked"';} ?> />&nbspForçar https no website</label></td>
<td >Nota: para esta funcionalidade é necessário ter um certificado SSL instalado</td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Seta para scroll up:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="200"><label class="label_check" for="Seta_scrollup"><input type="checkbox" name="Seta_scrollup" id="Seta_scrollup" value="1" <?php if($Seta_scrollup==1){ echo 'checked="checked"';} ?> /></label></td>

</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">ID Revendedor:</td>
<td align="left">
<input type="text" name="revendedor_id" id="revendedor_id" class="form-control" value="<?php echo $revendedor_id; ?>" style="width:100px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">País Default:</td>
<td align="left">
<input type="text" name="pais_id_default" id="pais_id_default" class="form-control" value="<?php echo $pais_id_default; ?>" style="width:100px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Caminho Exec:</td>
<td align="left">
<input type="text" name="exec_caminho" id="exec_caminho" class="form-control" value="<?php echo $exec_caminho; ?>" style="width:250px" />
</td>
</tr>


</table>