<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Tipo Promoção:</td>
<td align="left">
<div style="float:left;height:40px">Para adquir a campanha:</div>

<div class="clearboth"></div>

<div style="float:left;height:40px"><label class="label_radio" for="INTERVALO_QUANTIDADES" style="width:570px;font-size:12px"><input type="radio" name="TIPOPROMOCAO" id="INTERVALO_QUANTIDADES" value="1" <?php if($TIPOPROMOCAO==1 || !$TIPOPROMOCAO){ echo 'checked="checked"';} ?> /><u>Quantidade Mínima</u> de produtos da campanha no carrinho <u>for igual ou superior</u> a <b>(unidades)</b></label></div>
<div style="float:left;height:40px"><input type="text" name="MINIMO1" id="MINIMO1" class="form-control" value="<?php echo $MINIMO1?>" style="width:60px;text-align:center;padding:0;margin-top:22px" /></div>

<div class="clearboth"></div>

<div style="float:left;height:40px"><label class="label_radio" for="INTERVALO_QUANTIDADES2" style="width:570px;font-size:12px"><input type="radio" name="TIPOPROMOCAO" id="INTERVALO_QUANTIDADES2" value="4" <?php if($TIPOPROMOCAO==4){ echo 'checked="checked"';} ?> /><u>Quantidade</u> de produtos da campanha no carrinho <u>for igual</u> a <b>(unidades)</b><br /><span style="font-size:11px">Nota: (Este tipo de promoção só é valida para campanhas do tipo <b>NORMAL</b></span>)</label></div>
<div style="float:left;height:40px"></div>

<div class="clearboth"></div>

<div style="float:left;height:40px"><label class="label_radio" for="INTERVALO_VALORES" style="width:570px;font-size:12px"><input type="radio" name="TIPOPROMOCAO" id="INTERVALO_VALORES" value="2" <?php if($TIPOPROMOCAO==2){ echo 'checked="checked"';} ?> /><u>Valor Mínimo Acumulado</u> dos produtos da campanha for igual ou superior a <b>(em euros)</b></label></div>
<div style="float:left;height:40px"><input type="text" name="MINIMO2" id="MINIMO2" class="form-control" value="<?php echo $MINIMO2?>" style="width:60px;text-align:center;padding:0" /></div>

<div class="clearboth"></div>

<div style="float:left;height:40px"><label class="label_radio" for="VALOR_ACUMULADO" style="width:570px;font-size:12px"><input type="radio" name="TIPOPROMOCAO" id="VALOR_ACUMULADO" value="3" <?php if($TIPOPROMOCAO==3){ echo 'checked="checked"';} ?> /><u>Valor total do carrinho</u> for igual ou superior a <b>(em euros)</b></label></div>

<div style="float:left;height:40px"></div>
<div style="float:left;height:40px"><input type="text" name="MINIMO3" id="MINIMO3" class="form-control" value="<?php echo $MINIMO3?>" style="width:60px;text-align:center;padding:0" /></div>
<div class="clearboth"></div>
<div style="position:relative"><div style="position:absolute;font-size:11px;top:-12px">
  <!--NOTA: <b>para esta promoção ser ativada, o carrinho tem de ter <u>pelo menos um</u> produto da campanha e o desconto só é feito no(s) produto(s) abrangidos pela promoção</b>-->
  NOTA: <b>para promoções com desconto, este só é aplicado aos produtos definidos no backoffice</b>
</div></div>
<input type="hidden" name="MINIMO" id="MINIMO" class="form-control" value="<?php echo $MINIMO?>" style="width:120px" />
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<?php

if($TIPOCAMPANHA==1 || !$TIPOCAMPANHA){?>
<tr>
<td align="left" width="150">Tipo Campanha:</td>
<td align="left"><label class="label_radio" for="NORMAL"><input type="radio" name="TIPOCAMPANHA" id="NORMAL" value="1" <?php if($TIPOCAMPANHA==1 || !$TIPOCAMPANHA){ echo 'checked="checked"';} ?> /> Normal</label></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php } ?>
<?php if($TIPOCAMPANHA==2 || !$TIPOCAMPANHA){?>
<tr>
<td align="left" width="150"><?php if($TIPOCAMPANHA==2){?>Tipo Campanha:<?php }else{?>&nbsp;<?php }?></td>
<td align="left"><label class="label_radio" for="DESCONTOPARA3"><input type="radio" name="TIPOCAMPANHA" id="DESCONTOPARA3" value="2" <?php if($TIPOCAMPANHA==2){ echo 'checked="checked"';} ?> /> Desconto dos 3</label>
	<a href="#" onclick="return false;" title="Desconto dos 3:|- X% de desconto no produto de maior valor|- Y% de desconto no produto de valor intermédio|- Z% de desconto no produto de menor valor||X - desconto com a menor percentagem (10%, por exemplo)|Y - desconto com a percentagem intermédia (20%, por exemplo)|Z - desconto com a maior percentagem (30%, por exemplo)" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a></td><? /* Compre 3 produtos da campanha:|- 25% de desconto no 1º|- 50% de desconto no 2º|- 75% de desconto no 3º||- 2º produto, que beneficia de 50% de desconto, tem que ser o de maior valor |- 3º produto tem que ser o de menor valor */ ?>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php } ?>
<?php if($TIPOCAMPANHA==3 || !$TIPOCAMPANHA){?>
<tr>
<td align="left" width="150"><?php if($TIPOCAMPANHA==3){?>Tipo Campanha:<?php }else{?>&nbsp;<?php }?></td>
<td align="left"><label class="label_radio" for="OFERTA"><input type="radio" name="TIPOCAMPANHA" id="OFERTA" value="3" <?php if($TIPOCAMPANHA==3){ echo 'checked="checked"';} ?> /> Oferta de 1 ou Vários Produtos</label> <a href="#" onclick="return false;" title="O cliente tem direito a <b><u>todos os produtos</u></b> especificados pela FFS" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php } ?>
<?php if($TIPOCAMPANHA==4 || !$TIPOCAMPANHA){?>
<tr>
<td align="left" width="150"><?php if($TIPOCAMPANHA==4){?>Tipo Campanha:<?php }else{?>&nbsp;<?php }?></td>
<td align="left"><label class="label_radio" for="OFERTA2"><input type="radio" name="TIPOCAMPANHA" id="OFERTA2" value="4" <?php if($TIPOCAMPANHA==4){ echo 'checked="checked"';} ?> /> Oferta de 1 dos Produtos</label> <a href="#" onclick="return false;" title="O cliente escolhe 1 dos produtos de oferta pré-selecionados pela FFS" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php } ?>
<?php /* if($TIPOCAMPANHA==6 || !$TIPOCAMPANHA){?>
<tr>
<td align="left" width="150"><?php if($TIPOCAMPANHA==6){?>Tipo Campanha:<?php }else{?>&nbsp;<?php }?></td>
<td align="left"><label class="label_radio" for="DESCONTO2IGUAL3"><input type="radio" name="TIPOCAMPANHA" id="DESCONTO2IGUAL3" value="6" <?php if($TIPOCAMPANHA==6){ echo 'checked="checked"';} ?> /> Promoção 2=3</label> <a href="#" onclick="return false;" title="Compre 3 produtos da campanha:| produto de menor valor é oferecido" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php } */ ?>
<?php if($TIPOCAMPANHA==7 || !$TIPOCAMPANHA){?>
<tr>
<td align="left" width="150"><?php if($TIPOCAMPANHA==7){?>Tipo Campanha:<?php }else{?>&nbsp;<?php }?></td>
<td align="left"><label class="label_radio" for="CONJUNTO"><input type="radio" name="TIPOCAMPANHA" id="CONJUNTO" value="7" <?php if($TIPOCAMPANHA==7){ echo 'checked="checked"';} ?> /> Atribuir desconto a 2</label> <a href="#" onclick="return false;" title="- Maior desconto para o produto de menor valor | - Menor desconto para o produto de maior valor" class="navlinkx"><img src="img/q.png" align="absmiddle"  border="0" /></a></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php } ?>
</table>