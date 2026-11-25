<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left" width="100">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="bloco_visivel"><input type="checkbox" name="bloco_visivel" id="bloco_visivel" value="1" <?php if($bloco_visivel==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Ordem:</td>
<td width="45"><input type="text" name="bloco_ordem" id="bloco_ordem" class="box" value="<?php echo $bloco_ordem?>" style="width:50px"/></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Data Início:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left"><input type="text" name="bloco_data" id="bloco_data" class="box dropdate" value="<?php echo (isset($bloco_data) && $bloco_data!='0000-00-00') ? $bloco_data : date('Y-m-d'); ?>" /></td>
<td align="center" width="80">Data Fim:</td>
<td align="left"><input type="text" name="bloco_data2" id="bloco_data2" class="box dropdate" value="<?php echo (isset($bloco_data2) && $bloco_data2!='0000-00-00') ? $bloco_data2 : date('Y-m-d'); ?>" /></td>
<td align="center" width="130"> OU Permanente:</td>
<td width="45"><label class="label_check" for="bloco_permanente"><input type="checkbox" name="bloco_permanente" id="bloco_permanente" value="1" <?php if($bloco_permanente==1){ echo 'checked="checked"';} ?> /></label></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Título:</td>
<td align="left"><input class="box" type="text" name="bloco_titulo" id="bloco_titulo" value="<?php echo $bloco_titulo;?>" style="width:88%"/></td>						
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php /* <tr>						
<td align="left">Preço:</td>
<td align="left"><table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input class="box" type="text" name="bloco_preco" id="bloco_preco" value="<?php echo $bloco_preco;?>" style="width:150px"/></td>
<td align="center" width="180">Em promoção:</td>
<td width="45"><label class="label_check" for="bloco_promo"><input type="checkbox" name="bloco_promo" id="bloco_promo" value="1" <?php if($bloco_promo==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Preco Promoção:</td>
<td width="45"><input type="text" name="bloco_preco_promo" id="bloco_preco_promo" class="box" value="<?php echo $bloco_preco_promo?>" style="width:150px"/></td>
</tr>
</table></td>						
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr> */?>
<tr>						
<td align="left" valign="top">Opções:</td>
<td align="left">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<label class="label_check" for="bloco_opcao_embalagem" style="min-width:180px"><input type="checkbox" name="bloco_opcao_embalagem" id="bloco_opcao_embalagem" value="1" <?php if($bloco_opcao_embalagem==1){ echo 'checked="checked"';} ?> />Escolha da Embalagem</label><a class="preto" style="text-decoration:underline" href="../sa_blocos_embalagens/list.php">Configurar Embalagens</a><div class="clearboth"></div><br />
<label class="label_check" for="bloco_opcao_cor" style="min-width:180px"><input type="checkbox" name="bloco_opcao_cor" id="bloco_opcao_cor" value="2" <?php if($bloco_opcao_cor==2){ echo 'checked="checked"';} ?> />Escolha da Cor</label><a class="preto" style="text-decoration:underline" href="../sa_blocos_cores/list.php">Configurar Cores</a><div class="clearboth"></div><br />
<div class="clearboth"></div>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="180"><label class="label_check" for="bloco_opcao_vinhos" style="min-width:180px"><input type="checkbox" name="bloco_opcao_vinhos" id="bloco_opcao_vinhos" value="3" <?php if($bloco_opcao_vinhos==3){ echo 'checked="checked"';} ?> />Escolha dos Vinhos</label></td>
<td ><a class="preto" style="text-decoration:underline" href="../sa_blocos_vinhos/list.php">Configurar mais Vinhos</a></td>
</tr>
</table><br />

<div class="clearboth"></div>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="180"><label class="label_check" for="bloco_opcao_iguarias" style="min-width:180px"><input type="checkbox" name="bloco_opcao_iguarias" id="bloco_opcao_iguarias" value="4" <?php if($bloco_opcao_iguarias==4){ echo 'checked="checked"';} ?> />Escolha das Iguarias</label></td>
<td ><a class="preto" style="text-decoration:underline" href="../sa_blocos_iguarias/list.php">Configurar Iguarias</a></td>
</tr>
</table><br />
<div class="clearboth"></div>
<label class="label_check" for="bloco_opcao_cartao" style="min-width:180px"><input type="checkbox" name="bloco_opcao_cartao" id="bloco_opcao_cartao" value="5" <?php if($bloco_opcao_cartao==5){ echo 'checked="checked"';} ?> /> Cartão</label>&nbsp;<div class="clearboth"></div><br />
<label class="label_check" for="bloco_opcao_placafoto" style="min-width:180px"><input type="checkbox" name="bloco_opcao_placafoto" id="bloco_opcao_placafoto" value="6" <?php if($bloco_opcao_placafoto==6){ echo 'checked="checked"';} ?> /> Placa acrílica com foto</label>&nbsp;<div class="clearboth"></div><br />
<label class="label_check" for="bloco_opcao_placafrase" style="min-width:180px"><input type="checkbox" name="bloco_opcao_placafrase" id="bloco_opcao_placafrase" value="7" <?php if($bloco_opcao_placafrase==7){ echo 'checked="checked"';} ?> /> Placa acrílica com frase</label>&nbsp;<div class="clearboth"></div><br />
<label class="label_check" for="bloco_opcao_embrulho" style="min-width:180px"><input type="checkbox" name="bloco_opcao_embrulho" id="bloco_opcao_embrulho" value="8" <?php if($bloco_opcao_embrulho==8){ echo 'checked="checked"';} ?> /> Papel de embrulho</label><div class="clearboth"></div><br />
</td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left" colspan="2" height="30">Imagem Pack Inicial - popup 'Pedir desejo':</td>
</tr>	
<tr>						
<td align="left" colspan="2"><?php 
	if($bloco_ext && file_exists('../../files/documentos/'.$bloco_ext))
	{ 
		echo '<a class="pretourl_small2 fancybox_img" href="../../files/documentos/'.$bloco_ext.'" target="_blank" class="url_reno"><img src="../../files/documentos/'.$bloco_ext.'" height=100 border=0></a> | ';
		echo '<a class="preto" href="../edit_image/edit_image.php?w=619&h=609&src=../../files/documentos/'.$bloco_ext.'&ext=_enc" target="_blank" style="font-weight:bold;font-size:12px">Editar imagem</a><br /><br />';
	}?><input type="file" name="bloco_ext" id="bloco_ext" /></td>	
</tr>		
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>