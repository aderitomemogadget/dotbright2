<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="100">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="bloco_visivel"><input type="checkbox" name="bloco_visivel" id="bloco_visivel" value="1" <?php if($bloco_visivel==1){ echo 'checked="checked"';} ?> /></label></td>

<td align="center" width="100">Ordem:</td>
<td width="45"><input type="text" name="bloco_ordem" id="bloco_ordem" class="form-control" value="<?php echo $bloco_ordem?>" style="width:50px"/></td>
<?php  if((isset($_GET['bloco_id_pai']) && $_GET['bloco_id_pai'] > 0) || $bloco_id_pai > 0){?>

<td align="center" width="180">Especifição Pai:</td>
<td><?php echo gera_lista_path((isset($_GET['bloco_id_pai']) && $_GET['bloco_id_pai'] > 0) ? $_GET['bloco_id_pai'] : $bloco_id_pai); ?></td>
<?php }else{?></td>
<?php }?>
</tr>
</table>
</td>
</tr>
<?php /*  if((isset($_GET['bloco_id_pai']) && $_GET['bloco_id_pai'] > 0) || $bloco_id_pai > 0){?>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Categoria:</td>
<td align="left"><?php gera_lista_familias($m_familia, "products_families", "products_families_lang");?></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Marca:</td>
<td align="left"><?php gera_lista_marca($m_marca, "brands", "brands_lang");?></td>
</tr>
<?php } */?>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Titulo:</td>
<td align="left"><input class="form-control" type="text" name="bloco_titulo" id="bloco_titulo" value="<?php echo $bloco_titulo;?>" style="width:99%"/></td>
</tr>


<?php
/* <tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">#Hash Tag:</td>
<td align="left"><input class="form-control" type="text" name="bloco_texto" id="bloco_texto" value="<?php echo $bloco_texto;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Width(px):</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input type="text" name="width" id="width" class="form-control" value="<?php echo $width?>" style="width:100px"/></td>
<td align="center" width="180">Height(px):</td>
<td width="45"><input type="text" name="height" id="height" class="form-control" value="<?php echo $height?>" style="width:100px"/></td>


</tr>
</table>
<b>Nota:</b> Ao alterar o campo WIDTH AND HEIGHT, será necessaria a configuração de todos os frames que pertencem à categoria
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Imagem Teste:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left">
<?php if($bloco_ext && file_exists('../../files/'.FOLDER.'/'.$bloco_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$bloco_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="bloco_ext" id="bloco_ext" />
<br><b>Nota:</b> A imagem tem que ter o tamanho definido em Width and Height acima configurados
</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Preço (C/IVA):</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left">
<input type="text" name="bloco_preco" id="bloco_preco" class="form-control" value="<?php echo $bloco_preco?>" style="width:100px"/>
</td>
</tr>
</table>
</td>
</tr>
*/?>

<?php/*  if($bloco_id_pai > 0){?>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><?php include 'tab_acabamentos.php'; ?></td></tr>
<?php } */?>

</table>