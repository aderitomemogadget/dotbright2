<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td align="left">Ativo:</td>
		<td align="left">
			<label class="label_check" for="ATIVO"><input type="checkbox" name="ATIVO" id="ATIVO" value="1" <?php if($ATIVO==1){ echo 'checked="checked"';} ?> /></label>
		</td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td width="130" align="left">Tipo de Grupo:</td>
		<td align="left">
			<select name="Tipo_grupo" id="Tipo_grupo" class="form-control">
				<?php foreach($export_grupos as $key => $value){?>
				<option value="<?php echo $key?>" <?php if($Tipo_grupo == $key) echo ' selected="selected" ';?>><?php echo $value['nome'];?></option>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td width="130" align="left">Designação:</td>
		<td align="left"><input class="form-control" type="text" name="DESIGNACAO" id="DESIGNACAO" value="<?php echo $DESIGNACAO;?>" /></td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td align="left" width="150">Tipo Margem:</td>
		<td align="left">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left" width="180">
					<label class="label_radio" for="MARGEMVALOR" style="width:150px"><input type="radio" name="TIPOMARGEM" id="MARGEMVALOR" value="1" <?php if($TIPOMARGEM==1 || !$TIPOMARGEM){ echo 'checked="checked"';} ?> />Valor </label>
					<div class="clearboth"></div>
					<label class="label_radio" for="MARGEMPERCENTAGEM" style="width:150px"><input type="radio" name="TIPOMARGEM" id="MARGEMPERCENTAGEM" value="2" <?php if($TIPOMARGEM==2){ echo 'checked="checked"';} ?> />Percentagem</label>
				</td>
				<td align="right" align="left" width="44">&nbsp;</td>
				<td><input type="text" name="VALOR" id="VALOR" class="form-control" value="<?php echo $VALOR?>" style="width:120px" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>
<table width="100%" cellpadding="1" cellspacing="0" border="0">
	<tr>
		<td width="130" align="left">Produtos pertencentes a:</td>
		<td align="left">
			<select name="TIPORELACAO" id="TIPORELACAO" class="form-control">
				<option value="1" <?php if($TIPORELACAO == "1") echo ' selected="selected" ';?>>Tem de pertencer a (Categorias e marcas), acresce os Produtos adicionais</option>
				<option value="2" <?php if($TIPORELACAO == "2") echo ' selected="selected" ';?>>Tem de pertencer a (Categorias e marcas), exclui os Produtos adicionais</option>
				<option value="3" <?php if($TIPORELACAO == "3") echo ' selected="selected" ';?>>Basta pertencer a (Categorias ou marcas), acresce os Produtos adicionais</option>
				<option value="4" <?php if($TIPORELACAO == "4") echo ' selected="selected" ';?>>Basta pertencer a (Categorias ou marcas), exclui os Produtos adicionais</option>
				<option value="5" <?php if($TIPORELACAO == "5") echo ' selected="selected" ';?>>Todas as Categorias, EXCLUINDO marcas</option>
				<option value="6" <?php if($TIPORELACAO == "6") echo ' selected="selected" ';?>>Todas as Marcas EXCLUINDO Categorias</option>
				<!-- <option value="7" <?php if($TIPORELACAO == "7") echo ' selected="selected" ';?>>Marcas-E-Produtos EXCLUINDO Categorias</option>
				<option value="8" <?php if($TIPORELACAO == "8") echo ' selected="selected" ';?>>Categorias-OU-Marcas-OU-Produtos</option>
				<option value="9" <?php if($TIPORELACAO == "9") echo ' selected="selected" ';?>>Categorias-OU-Marcas</option>
				<option value="10" <?php if($TIPORELACAO == "10") echo ' selected="selected" ';?>>Categorias-OU-Produtos</option>
				<option value="11" <?php if($TIPORELACAO == "11") echo ' selected="selected" ';?>>Marcas-OU-Produtos</option>
				<option value="12" <?php if($TIPORELACAO == "12") echo ' selected="selected" ';?>>Categorias-OU-Marcas-EXCLUINDO-Produtos</option>
				<option value="13" <?php if($TIPORELACAO == "13") echo ' selected="selected" ';?>>Categorias-OU-Produtos-EXCLUINDO-Marcas</option>
				<option value="14" <?php if($TIPORELACAO == "14") echo ' selected="selected" ';?>>Marcas-OU-Produtos-EXCLUINDO-Categorias</option>
				<option value="15" <?php if($TIPORELACAO == "15") echo ' selected="selected" ';?>>SÓ Categorias</option>
				<option value="16" <?php if($TIPORELACAO == "16") echo ' selected="selected" ';?>>SÓ Marcas</option>
				<option value="17" <?php if($TIPORELACAO == "17") echo ' selected="selected" ';?>>SÓ Produtos</option> -->
			</select>
		</td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>