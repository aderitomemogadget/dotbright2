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
<td align="left">Tipo de Custo:</td>

<td align="left">
		<select name="tipo" id="tipo" onchange="muda_tipo(this.value)" style="width:100px;" >
		<option value="1" <?php if($tipo=='1')echo "selected";?>> Valor Fixo</option>
		<option value="2" <?php if($tipo=='2')echo "selected";?>> Margem</option>
		</select>
<input  type="hidden" name="Tipo" id="Tipo" value="<? echo $tipo;?>" />
</tr>
<tr><td><br/></td></tr>
<tr id="custo_fixo" <?php if($tipo!='1')echo 'style="visibility:collapse;"';?>>
<td align="left">Valor Fixo (&#8364;):</td>
<td align="left"><input class="box" type="text" name="Custos" id="Custos" value="<?php echo $Custos;?>" style="width:99%"/></td>						
</tr>
<tr id="margem_div"<?php if($tipo!='2')echo 'style="visibility:collapse;"';?> >
<td align="left">Margem (%):</td>
<td align="left"><input class="box" type="text" name="margem" id="margem" value="<?php echo $margem;?>" style="width:99%"/></td>						
</tr>
<script>
 function muda_tipo(value){
	 document.getElementById("Tipo").value=value;
	 if(value==1){
		document.getElementById("margem_div").style.visibility="collapse"; 
		document.getElementById("custo_fixo").style.visibility="visible"; 
	 }else if(value==2){
		document.getElementById("margem_div").style.visibility="visible"; 
		document.getElementById("custo_fixo").style.visibility="collapse"; 	
	 }
 }
</script>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

</table>