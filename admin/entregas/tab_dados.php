<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left" width="150">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Visivel"><input type="checkbox" name="Visivel" id="Visivel" value="1" <?php if($Visivel==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Ordem:</td>
<td width="45"><input type="text" name="Ordem" id="Ordem" class="box" value="<?php echo $Ordem?>" style="width:50px"/></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>						
<td align="left">Nome Tipo:</td>
<td align="left"><input class="box" type="text" name="Nome" id="Nome" value="<?php echo $Nome;?>" style="width:99%"/></td>						

</tr>
<tr><td><br/></td></tr>
<tr>						
<td align="left">Quantidade de dias:</td>
<td align="left"><input class="box" type="text" name="dias" id="dias" value="<?php echo $dias;?>" style="width:99%"/></td>						

</tr>
<tr><td><br/></td></tr>
<tr>						
<td align="left">Custo (%):</td>
<td align="left"><input class="box" type="text" name="custo_entrega" id="custo_entrega" value="<?php echo $custo_entrega;?>" style="width:99%"/></td>						

</tr>
<tr><td><br/></td></tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

</table>