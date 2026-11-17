<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left" width="150">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="visivel"><input type="checkbox" name="visivel" id="visivel" value="1" <?php if($visivel==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>						
<td align="left">Título:</td>
<td align="left">
<input type="text" name="titulo" id="titulo" class="box" value="<?php echo $titulo; ?>" style="width:99%" />
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Info:</td>
<td align="left">
<textarea name="info" id="info" class="box"style="width:99%"><?php echo $info; ?></textarea>
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
<td align="left">Exluir  Produtos:</td>
<td align="left">
<table width="100%" cellpadding=0 cellspacing=0 border=0 >
<tr>

<td style="font-weight:bold">
<?php if(isset($_GET['id']) && $_GET['id'] > 0){?>
<div class="boardbt fancybox3" href="avancada.php?ID_PAI=<?php echo $_GET['id'].'&Desconto=0&prdcmp=1';?>" style="cursor:pointer">Configurar/Visualizar Produtos (<?php echo $tem_voucher_prods;?> Exluídos)</div>
<?php }else{?>
<div class="boardbt">Para adicionar produtos clique primeiro em Gravar Novo Registo</div>
<?php }?>
</td>

</tr>
</table> 
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>						
	<td align="left">Valor Pagar:</td>
	<td align="left">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="45"><input type="text" name="valor" id="valor" class="box" value="<?php echo $valor; ?>" style="width:150px" /></td>
			<td align="center" width="150">Ativar Promoção</td>
			<td align="left">
				<label class="label_check" for="promo_ativo"><input type="checkbox" name="promo_ativo" id="promo_ativo" value="1" <?php if($promo_ativo==1){ echo 'checked="checked"';} ?> /></label>
			</td>
			<td align="center" width="170">Valor Pagar Promoção:</td>
			<td align="left">
				<input type="text" name="valor_promo" id="valor_promo" class="box" value="<?php echo $valor_promo; ?>" style="width:150px" />
			</td>
		</tr>
		</table>
	</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>						
	<td align="left">Por intervalo de pesos:</td>
	<td align="left">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="45"><label class="label_check" for="portes_pesos"><input type="checkbox" name="portes_pesos" id="portes_pesos" value="1" <?php if($portes_pesos==1){ echo 'checked="checked"';} ?> /></label></td>
			<td align="center" width="150">Formula:</td>
			<td align="left">
				<textarea class="form-control" name="portes_pesos_formula" style="width:400px"><?=$portes_pesos_formula;?></textarea>
				<?php echo htmlentities("if(Peso > 0 && Peso < 1000) echo 10;")."<br>".htmlentities("if(Peso > 1000 && Peso < 2000) echo 20;");?>
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
<td align="left"><input type="text" name="ini" id="ini" class="box" value="<?php echo $ini; ?>" style="width:150px" /></td>
<td align="center" width="80">Valor Fim:</td>
<td align="left"><input type="text" name="fim" id="fim" class="box" value="<?php echo $fim; ?>" style="width:150px" /></td>
</tr>
</table>
</td>
</tr>

</table>