<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Ordenar por Default:</td>
<td align="left">
<select name="ordenar_por" id="ordenar_por" class="form-control" style="width:250px">
<option value="-1" <?php echo ($ordenar_por == -1 ? 'selected="selected"' : ''); ?>>Relevancia</option>
<option value="0" <?php echo ($ordenar_por == 0 ? 'selected="selected"' : ''); ?>>A..Z</option>
<option value="1" <?php echo ($ordenar_por == 1 ? 'selected="selected"' : ''); ?>>Z..A</option>
<option value="2" <?php echo ($ordenar_por == 2 ? 'selected="selected"' : ''); ?>>Mais Barato</option>
<option value="3" <?php echo ($ordenar_por == 3 ? 'selected="selected"' : ''); ?>>Mais Caro</option>
</select>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">IVA Default:</td>
<td align="left">
<input type="text" name="iva_default" id="iva_default" class="form-control" value="<?php echo $iva_default; ?>" style="width:100px" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" style="vertical-align:top">Notificação Encomendas:<br><br><span style="font-size:11px">Nota: Emails separados com virgula(,)</span></td>
<td align="left">
	<textarea name="emails_encomenda" id="emails_encomenda" class="form-control" style="height:100px"><?php echo $emails_encomenda; ?></textarea>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr>
	<td colspan="2">
		<table style="width:100%">
		<tr>
			<td style="width: 230px;">Utilizar icon: </td> <td> <input type="checkbox" name="icon_bc" onclick="checkuncheck(this)" value="<?=$icon_bc?>" <?=$icon_bc==1?'checked':''?>> <i class="fas fa-home fa-2x"></i></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>Utilizar texto (por o nome da key): </td> <td> <input class="form-control" type="text" name="texto_bc" value="<?=$texto_bc==''?'home':$texto_bc?>" style="width: 100%;"></td>
		</tr>
		<tr>
			<td>
				Quantidade mínima para apressentar a opção de crédito: 
			</td> 
			<td> 
				<input class="form-control" type="text" name="cred_min" value="<?=$cred_min?>" style="width: 100%;max-width: 200px;display: inline-block;">
				&nbsp;&nbsp;				
				Utilizar o valor mínimo: 
				<a href="../importadores/credito/import.php?dp=<?=uniqid()?>" style="color:red;">
				(Se não tem check utiliza os valores da tabela)
				<a/>&nbsp;
				<input type="checkbox" value="<?=$onoff_valor_min?>" onclick="this.value=this.value==0?1:0" name="onoff_valor_min" <?=$onoff_valor_min==1?'checked':''?>>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<script type="text/javascript">
	function checkuncheck(obj){
		if(obj.value==0)obj.value = 1;
		else obj.value = 0;
	}
</script>