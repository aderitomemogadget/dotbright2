<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="130">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Visivel"><input type="checkbox" name="Visivel" id="Visivel" value="1" <?php if($Visivel==1){ echo 'checked="checked"';} ?> /></label></td>
<td align="center" width="180">Ordem:</td>
<td width="45"><input type="text" name="Ordem" id="Ordem" class="form-control" value="<?php echo $Ordem?>" style="width:50px"/></td>
<td align="center" width="210">Grupo:</td>
<td width="45"><select name="<?php echo TBIDORG?>" class="form-control" style="width:180px"><option value="" <?php if((isset($_GET[TBIDORG]) && $_GET[TBIDORG] > 0) ? $_GET[TBIDORG] : $ID_ORG){ echo 'selected="selected"';} ?>>Alinhado à Esquerda</option><option value="1" <?php if($estilo2==1){ echo 'selected="selected"';} ?>>Alinhado ao Centro</option></select></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Nome Template:</td>
<td align="left"><input class="form-control" type="text" name="Cabecalho" id="Cabecalho" value="<?php echo $Cabecalho;?>" style="width:88%" /></td>
</tr>

<?php /* if(((isset($_GET[TBIDORG]) && $_GET[TBIDORG] > 0) ? $_GET[TBIDORG] : $ID_ORG)){ */?>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" colspan="2" height="30">Imagem de Fundo - <span style="font-size:11px">Resolução - 72 DPIs</span></td>
</tr>
<tr>
<td align="left" colspan="2"><?php
	if($img_ext && file_exists('../../files/'.TBMODULO.'/'.$bloco_ext))
	{
		echo '<a class="pretourl_small2 fancybox_img" href="../../files/'.TBMODULO.'/'.$bloco_ext.'" target="_blank" class="url_reno"><img src="../../files/'.TBMODULO.'/'.$img_ext.'" height="100" border="0" /></a> | ';
		// echo '<a class="preto" href="../edit_image/edit_image.php?w=619&h=609&src=../../files/'.TBMODULO.'/'.$img_ext.'&ext=_enc" target="_blank" style="font-weight:bold;font-size:12px">Editar imagem</a><br /><br />';
	}?><input type="file" name="img_ext" id="img_ext" /></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="130">Margem no Topo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="110"><input class="form-control" type="text" name="margem_top" id="margem_top" value="<?php echo $margem_top;?>" style="width:100px" /> </td>
<td align="left">em pixeis</td>
</tr>
</table>

</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="130">Largura do site:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="110"><input class="form-control" type="text" name="site_width" id="site_width" value="<?php echo $site_width;?>" style="width:100px" /></td>
<td width="80" align="left">em pixeis</td>
<td width="45"><select name="estilo" class="form-control" style="width:180px"><option value="" <?php if(!$estilo){ echo 'selected="selected"';} ?>>Alinhado à Esquerda</option><option value="1" <?php if($estilo==1){ echo 'selected="selected"';} ?>>Alinhado ao Centro</option></select></td>
<td align="center" width="210">Conteúdos Dentro do Site:</td>
<td width="45"><select name="estilo2" class="form-control" style="width:180px"><option value="" <?php if(!$estilo2){ echo 'selected="selected"';} ?>>Alinhado à Esquerda</option><option value="1" <?php if($estilo2==1){ echo 'selected="selected"';} ?>>Alinhado ao Centro</option></select></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="130">Cor de Fundo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="110"><input type="text" name="bg_color" id="bg_color" class="form-control" value="<?php echo $bg_color?>" style="width:100px"/></td>
<td width="150" align="center">Efeito Pretendido:</td>
<td width="180">
<select name="efeito" class="form-control" style="width:180px">
<option value="" <?php if(!$efeito){ echo 'selected="selected"';} ?>>Repetir Imagem</option>
<option value="1" <?php if($efeito==1){ echo 'selected="selected"';} ?>>Repetir Imagem na Horizontal</option>
<option value="2" <?php if($efeito==2){ echo 'selected="selected"';} ?>>Repetir Imagem na Vertical</option>
<option value="3" <?php if($efeito==3){ echo 'selected="selected"';} ?>>Imagem Alinhada ao Centro/Topo</option>
<option value="4" <?php if($efeito==4){ echo 'selected="selected"';} ?>>Imagem a Fullscreen</option>
</select></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" colspan="2" height="25">Topo Genérico:</td>
</tr>
<tr>
<td align="left" colspan="2"><div style="width:850px;font-size:11px"><textarea id="Texto1" name="Texto1" style="width:200px;height:200px;font-size:11px"><?=htmlentities_memo(stripslashes($Texto1));?></textarea></div></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" colspan="2" height="25">Rodapé genérico:</td>
</tr>
<tr>
<td align="left" colspan="2"><div style="width:850px;font-size:11px"><textarea id="Texto2" name="Texto2" style="width:200px;height:200px;font-size:11px"><?=htmlentities_memo(stripslashes($Texto2));?></textarea></div></td>
</tr>
<?php/*  }else{?>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" colspan="2" height="25">Folha de Estilos:</td>
</tr>
<tr>
<td align="left" colspan="2"><div style="width:850px"><textarea id="Texto3" name="Texto3" style="width:200px;height:200px"><?=htmlentities_memo(stripslashes($Texto3));?></textarea></div></td>
</tr>
<?php } */?>
</table>