<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
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
<input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo $titulo; ?>" style="width:99%" />
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
<td align="left">Marcas:</td>
<td align="left">
<table width="100%" cellpadding=0 cellspacing=0 border=0 >
<tr>

<td style="font-weight:bold" id="load_brads">
<?php echo gera_lista_marcas($marca_id);?>
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
<td align="left">Margem:</td>
<td align="left">

<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input type="text" name="valor" id="valor" class="form-control" value="<?php echo $valor; ?>" style="width:150px" /></td>

</tr>
</table>


</td>
</tr>


</table>