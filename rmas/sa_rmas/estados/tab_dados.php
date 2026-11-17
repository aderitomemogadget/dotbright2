<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="<?php echo CAMPO_LINGUA?>" id="<?php echo CAMPO_LINGUA?>" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>						
<td align="left" width="100">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Visivel"><input type="checkbox" name="Visivel" id="Visivel" value="1" <?php if($Visivel==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Nivel:</td>
<td width="45"><input type="text" name="Ordem" id="Ordem" class="box" value="<?php echo $Ordem?>" <?php if($Ordem > 0){?>readonly=""<?php }?> style="width:50px"/></td>

<td align="center" width="180">Categoria:</td>
<td width="45"><?php echo gera_lista_path((isset($_GET['estado_id_pai']) && $_GET['estado_id_pai'] > 0) ? $_GET['estado_id_pai'] : $estado_id_pai); ?></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left" width="100">Envia Email:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Email"><input type="checkbox" name="Email" id="Email" value="1" <?php if($Email==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Cor:</td>
<td width="45"><input type="text" name="Cor" id="Cor" class="box" value="<?php echo $Cor?>" style="width:50px"/></td>
</tr>
</table>
</td>
</tr>



<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Nome:</td>
<td align="left"><input class="box" type="text" name="Cabecalho" id="Cabecalho" value="<?php echo $Cabecalho;?>" style="width:99%"/></td>						
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Nome Alternativo:</td>
<td align="left"><input class="box" type="text" name="Cabecalho2" id="Cabecalho2" value="<?php echo $Cabecalho2;?>" style="width:99%"/></td>						
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Corpo Email:</td>
<td align="left"><textarea class="box" name="Texto" id="Texto" style="width:99%;height:300px"><?php echo $Texto;?></textarea>  <? show_editor_small("oEdit1", "Texto", 880, 250)?></td>						
</tr>
		
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

</table>