

<div style="font-size:15px">
<h2>Campos Obrigatórios no Ficheiro</h2><br>

</div><br>


<table border="1" cellspacing="0" cellpadding="5" style="margin:0 auto;border:#FFF 1px solid;background:#FFF;">
<tr>
<?php
$d = 7;
foreach($GLOBALS['arr_campos_importar'] as $campos)
{
	if($d == $d2)
	{
		echo '</tr><tr>';
		$d2 = 0;
	}
	?><td align="center" style="font-weight:bold;font-size:10px;vertical-align:top">
	<table border="0" cellspacing="0" cellpadding="5">
	<tr><td align="left" style="font-weight:bold"><?php echo ($campos['INFO'] != '' ? $campos['INFO'] : $campos['CAMPO']);?></td></tr>
	<tr><td align="left" style="font-weight:bold"><input type="text" value="<?php echo ${$campos['CAMPO']};?>" name="<?php echo $campos['CAMPO'];?>" style="width:120px"></td></tr>
	<tr><td align="center" style="font-weight:bold;color:#FF0000;font-size:10px"><?php if($campos['OBRIGATORIO'] == 1){?>Obrigatório<?php }?></td></tr>
	</table>
	</td><?php
	$d2++;
}
?>
</tr>
</table>





<!------------------------------------->

<div style="display:inline-block;padding:5px;vertical-align:top;border:#FFF 1px solid;background:#FFF;min-height:118px">
									<h2>Separadores Colunas</h2><br>
									<div style="width:145px;display:inline-block;padding:5px 0">Colunas separadas com</div><div style="width:40px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Separador;?>" name="Separador" id="Separador" style="width:30px"> </div><br>
									<div style="clear:both"></div>
									<div style="width:145px;display:inline-block;padding:5px 0">Colunas fechadas com</div><div style="width:40px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Separador2;?>" name="Separador2" id="Separador2" style="width:30px"> </div><br>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Ações Produtos</h2><br>
									<label for="conf1_0"><input type="radio" value="0" name="conf1" id="conf1_0" <?php echo ($conf1 == 0 ? 'checked="checked"' : '');?>> Inserir/Atualizar</label>
									<label for="conf1_1"><input type="radio" value="1" name="conf1" id="conf1_1" <?php echo ($conf1 == 1 ? 'checked="checked"' : '');?>> Apenas Inserir</label>
									<label for="conf1_2"><input type="radio" value="2" name="conf1" id="conf1_2" <?php echo ($conf1 == 2 ? 'checked="checked"' : '');?>> Apenas Atualizar</label>
									<label for="conf1_3"><input type="radio" value="3" name="conf1" id="conf1_3" <?php echo ($conf1 == 3 ? 'checked="checked"' : '');?>> Eliminar</label>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px;display:none">
									<h2>Ações Categorias</h2><br>
									<label for="conf2_1"><input type="radio" value="0" name="conf2" id="conf2_1" <?php echo ($conf2 == 0 ? 'checked="checked"' : '');?>> Desativado</label>
									<label for="conf2_2"><input type="radio" value="1" name="conf2" id="conf2_2" <?php echo ($conf2 == 1 ? 'checked="checked"' : '');?>> Criar/Atualizar</label>
									<label for="conf2_3"><input type="radio" value="2" name="conf2" id="conf2_3" <?php echo ($conf2 == 2 ? 'checked="checked"' : '');?>> Apenas Criar</label>
									<label for="conf2_4"><input type="radio" value="3" name="conf2" id="conf2_4" <?php echo ($conf2 == 3 ? 'checked="checked"' : '');?>> Apenas Atualizar</label>

								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px;display:none">

									<h2>Ações Marcas</h2><br>
									<label for="conf3_1"><input type="radio" value="0" name="conf3" id="conf3_1" <?php echo ($conf3 == 0 ? 'checked="checked"' : '');?>> Desativado</label>
									<label for="conf3_2"><input type="radio" value="1" name="conf3" id="conf3_2" <?php echo ($conf3 == 1 ? 'checked="checked"' : '');?>> Criar/Atualizar Marca</label>
									<label for="conf3_3"><input type="radio" value="2" name="conf3" id="conf3_3" <?php echo ($conf3 == 2 ? 'checked="checked"' : '');?>> Apenas Criar</label>
									<label for="conf3_4"><input type="radio" value="3" name="conf3" id="conf3_4" <?php echo ($conf3 == 3 ? 'checked="checked"' : '');?>> Apenas Atualizar</label>

								</div>

								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Preços</h2><br>
									<label for="Preco_conf"><input type="radio" value="0" name="Preco_conf" id="Preco_conf" <?php echo ($Preco_conf == 0 ? 'checked="checked"' : '');?>> Preços com IVA INC.</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
										<label for="Preco_conf2"><input type="radio" value="1" name="Preco_conf" id="Preco_conf2" <?php echo ($Preco_conf == 1 ? 'checked="checked"' : '');?>> Preços sem IVA INC.</label>
										<div style="width:96px;display:inline-block;padding:5px 0">Acrescentar IVA</div><div style="width:40px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Preco_iva_valor;?>" name="Preco_iva_valor" id="Preco_iva_valor" style="width:30px"> </div><br>
									</div>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Imagens</h2><br>
									<label for="Imagem_conf"><input type="radio" value="0" name="Imagem_conf" id="Imagem_conf" <?php echo ($Imagem_conf == 0 ? 'checked="checked"' : '');?>> Download Imagens de Url</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
										<label for="Imagem_conf21"><input type="radio" value="1" name="Imagem_conf" id="Imagem_conf21" <?php echo ($Imagem_conf == 1 ? 'checked="checked"' : '');?>> Download Imagens Pasta Servidor</label>
										<div style="width:135px;display:inline-block;padding:5px 0">/files/importar/imagens/</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Imagem_pasta;?>" name="Imagem_pasta" id="Imagem_pasta" style="width:70px"> </div><br>
									</div>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Stock</h2><br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Azul</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock1;?>" name="Stock1" id="Stock1" style="width:70px"> </div> (por encomenda)<br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Vermelho</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock2;?>" name="Stock2" id="Stock2" style="width:70px"> </div> esgotado<br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Amarelo</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock3;?>" name="Stock3" id="Stock3" style="width:70px"> </div><br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Verde</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock4;?>" name="Stock4" id="Stock4" style="width:70px"> </div><br>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Ações Ficheiro</h2><br>
									<div style="width:110px;display:inline-block;padding:5px 0">Código Importação</div><div style="width:120px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $codigo_importacao;?>" name="codigo_importacao" id="codigo_importacao" style="width:120px"> </div><br>
									<label for="Eliminar_conf"><input type="radio" value="0" name="Eliminar_conf" id="Eliminar_conf" <?php echo ($Eliminar_conf == 0 ? 'checked="checked"' : '');?>> Desativado</label>
									<label for="Eliminar_conf1"><input type="radio" value="1" name="Eliminar_conf" id="Eliminar_conf1" <?php echo ($Eliminar_conf == 1 ? 'checked="checked"' : '');?>> Eliminar Produtos que não vinham no ficheiro</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
									<label for="Eliminar_conf2"><input type="radio" value="2" name="Eliminar_conf" id="Eliminar_conf2" <?php echo ($Eliminar_conf == 2 ? 'checked="checked"' : '');?>> Eliminar Produtos que não vinham no ficheiro e que estejam fora de gama</label>
									<div style="width:110px;display:inline-block;padding:5px 0">Com Valor</div><div style="width:120px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $fora_gama_codigo;?>" name="fora_gama_codigo" id="fora_gama_codigo" style="width:120px"> </div><br>
									</div>
								</div>