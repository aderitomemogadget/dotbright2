<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
<tr> 
<td valign="top"><?
	if(!$aproved || !$m_marca)
	{?>
	<table width="100%" border=0 cellpadding="2" cellspacing=0 height="80">
	<tr>
		<td class="preto" align="center" valign="middle" bgcolor="#ffa590">ATENÇÃO, ESTE PRODUTO AINDA NÃO CUMPRE OS REQUISITOS MINIMO PARA FICAR VISIVEL NO SITE</td>
	</tr>
	</table>
	<? } ?>
	
	
	<br />
	
	<table width="100%" border=0 cellpadding="2" cellspacing=0>
	<tr>
		<td class="preto" width="120"><? echo write_lang('key_ordem','Ordem')?>:</td>
		<td class="preto" align="left" valign="top">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td width="100"><input class="box" name="ordem" style="WIDTH:50px" maxlength="5" value="<? echo $ordem;?>" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<?php if($tem_marcas){?>
	<tr>
		<td class="preto" width="120"> Marca:</td>
		<td class="preto" align="left" valign="top">
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width="170"><? gera_lista_marcas2("marca", "brands", "brands_lang", $m_marca);?></td>
				<td class="preto">&nbsp;</td> 
			</tr>
			</table>
		</td>
	</tr><? }?>
	 <tr>
		<td class="preto" width="120"> <? echo write_lang('key_categoria', 'Categoria');?>/Familia:</td>
		<td class="preto" align="left" valign="top">
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width="170"><?php gera_lista_familias($ID_Fam,"products_families","products_families_lang");?></td>
				<td class="preto">&nbsp;</td> 
			</tr>
			</table>
		</td>
	</tr>
	  <tr>
		<td class="preto" width="180">Referência:</td>
		<td class="preto" align="left" valign="top"><input class="box" type="text" name="ref" style="WIDTH:175px" maxlength="255" value="<? echo $Ref;?>" /> </td>
	</tr>
	<tr>
		<td class="preto"><? echo write_lang('key_nome','Nome')?>/Descrição:</td>
		<td class="preto" align="left" valign="top"><input class="box" type="text" name="cabecalho" style="WIDTH:99%" maxlength="255" value="<? echo $C;?>" /> </td>
	</tr> 
	<?php /* <tr>
		<td class="preto"><? echo write_lang('key_nome','Nome')?>/Descrição 2:</td>
		<td class="preto" align="left" valign="top"><input class="box" type="text" name="subtitulo" style="WIDTH:99%" maxlength="255" value="<? echo $ST;?>" /> </td>
	</tr>  */?>
	<tr>
		<td class="preto">&nbsp;</td>
	</tr>
	<?php if(isset($prod_conf) && $prod_conf['tem_bonus'] == 1){?>
	<tr>
		<td class="preto">Bonus:</td>
		<td class="preto" align="left" valign="top">
		
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td width="110"><input class="box" type="text" name="pontos" style="WIDTH:100px" maxlength="255" value="<? echo $pontos;?>" /></td>
		<td class="preto"><select name="tpoints" class="chzn-select6" style="width:80px"><option value="€" <? echo $select2; ?>>€</option><option value="%"  <? echo $select1; ?>>%</option></select></td> 
		</tr>
		</table>
		
		
		</td>
	</tr> 
	<tr>
		<td class="preto">&nbsp;</td>
	</tr>
	<?php }
	if(isset($prod_conf) && $prod_conf['tem_sincro'] == 1){?>
	<tr>
		<td class="preto">Desativar Sincronização auto:</td>
		<td class="preto" align="left" valign="top"><div style="float:left"><input type="checkbox" name="sync" <?php if($sync || (isset($_GET['ID']) && $_GET['ID'] == 0)){?> checked="checked" <? }?> value="1" /></div><div style="float:left;margin-left:5px"><span style="color:#FF0000;font-weight:bold;font-size:x-small;">1 - Se esta opção não for selecionada sempre que houver atualização da integração toda a informação será atualizada<br />2 - As Características do produto vão ignorar esta flag caso o produto seja um produto de integração</span></div></td>
	</tr>
	<tr>
		<td class="preto">&nbsp;</td>
	</tr>
	
	<?php 
	}
	if($GLOBALS['id_revendedor'] > 0){?>
		<tr>
		<td class="preto">Desativar Desconto 4%:</td>
		<td class="preto" align="left" valign="top"><div style="float:left"><input type="checkbox" name="descontopreco" <?php if($descontopreco || (isset($_GET['ID']) && $_GET['ID'] == 0)){?> checked="checked" <?php }?> value="1" /></div><div style="float:left;margin-left:5px"></div></td>
	</tr>
	<tr>
		<td class="preto">&nbsp;</td>
	</tr>
	
	<?php }?>
	<? if($id_revendedor > 0){?>
	<tr>
		<td class="preto">Buscar Preço Cliente Final:</td>
		<td class="preto" align="left" valign="top"><input type="checkbox" name="preco_revenoff" <?php if($preco_revenoff){?> checked="checked" <?php }?> value="1" /></td>
	</tr>
	<tr>
	<td class="preto" colspan="4">&nbsp;</td>
	</tr> 								
	<?php }
	if(isset($prod_conf) && $prod_conf['tem_estados'] == 1){?>
					
	<tr bgcolor="#EAEAEA">
	<td class="preto">Estados do produto:</td>
	<td class="preto" width="180" align="left" valign="top" colspan="3">
	<select name="estados_produto" class="chzn-select" style="width:180px">
	<option value="0" <?php if(isset($estados_produto) && $estados_produto == 0 || !isset($estados_produto)){echo 'selected="selected"';}?>>Normal</option>
	<option value="1" <?php if(isset($estados_produto) && $estados_produto == 1){echo 'selected="selected"';}?>>Semi-Novo</option>
	<option value="2" <?php if(isset($estados_produto) && $estados_produto == 2){echo 'selected="selected"';}?>>Usado</option>
	</select>
	</td>
	</tr>  
	<tr>
	<td class="preto">&nbsp;</td>
	</tr>
	<?php 
	}
	if(isset($prod_conf) && $prod_conf['tem_tabelas_precos'] == 1){?>
	<tr bgcolor="#EAEAEA">
	<td class="preto" colspan="4" align="center" height="30" style="font-weight:bold">Tabela de Preços Predefinida</td>
	</tr> 								
	<tr bgcolor="#EAEAEA">
	<td class="preto" width="180" align="left" valign="top" colspan="3"><?php echo products_objs($ID, $sync); ?></td>
	</tr>  
	<tr>
	<td class="preto">&nbsp;</td>
	</tr>
	<?php 
	}

	if(isset($prod_conf) && $prod_conf['tem_filtros_combo'] == 1)
	{
		$ffpa = filtros_ficha_produto_adm($ID, $ID_Fam);
		if($ffpa)
		{
		?>
		<tr bgcolor="#EAEAEA">
		<td class="preto" colspan="4" align="center" height="30" style="font-weight:bold">Tabela de Preços por Combinações de Filtros <a href="#" class="preto" style="text-decoration:underline" onclick="showmenu_prod('sub4','pinta4');return false;">Configurar Caracteristicas</a><br><span style="color:#FF0000;font-weight:bold;font-size:x-small; vertical-align:super;">(ATENÇÃO: caso acrescente/elimine uma caracteristica, todos os preços na tabela abaixo serão removidos)</span></td>
		</tr> 								
		<tr bgcolor="#EAEAEA">
		<td class="preto" width="180" align="center" valign="top" colspan="3"><?php echo $ffpa;?><br /></td>
		</tr>  
		<tr>
		<td class="preto">&nbsp;</td>
		</tr>
	<?php } }?>
	
	<? if($sync==0 && $id_revendedor > 0){?>
	<tr> 
	<td class="preto"><? echo write_lang('key_peso','Peso')?> Macorlux(kg):</td>
	<td class="preto" align="left" valign="top"><input class="box" type="text" name="peso" style="WIDTH:100px;background:#EAEAEA" readonly="" value="<? echo $peso;?>" /></td>
	</tr>
	<?php }?>
	
	<tr> 
	<td class="preto"><? echo write_lang('key_peso','Peso')?>(kg):</td>
	<td class="preto" align="left" valign="top"><input class="box" type="text" name="peso_cliente" style="WIDTH:100px" value="<? echo ((isset($peso_cliente) && $peso_cliente > 0) ? $peso_cliente : $peso);?>" /></td>
	</tr>
	<tr>
	<td class="preto">&nbsp;</td>
	</tr>
	<?php if(isset($prod_conf) && $prod_conf['tem_tempo_entrega'] == 1){?>
	<tr>
	<td class="preto">Tempo entrega:</td>
	<td class="preto" colspan="2"> <input class="box" type="text" name="tempo_entrega" value="<?php echo $tempo_entrega?>" /></td>
	</tr>
	<tr>
	<td class="preto">&nbsp;</td>
	</tr>
	<?php }?>
	<?php if(isset($prod_conf) && $prod_conf['tem_carrinho'] == 1){?>
	<tr>
	<td class="preto">Ativar Carrinho:</td>
	<td class="preto"  colspan="2"> <input type="radio" name="carrinho" value="1" <?php if($carrinho==1) echo "checked='checked'";?> /></td>
	</tr>
	<tr>
	<td class="preto">Desativar Carrinho:</td>
	<td class="preto"  colspan="2"><input type="radio" name="carrinho" value="0" <?php if($carrinho==0) echo "checked='checked'";?> /></td>
	</tr>
	<tr>
	<td class="preto">&nbsp;</td>
	</tr>
	<?php }?>
	<tr>
		<td class="preto"><img src="ok.png" width="20" height="20" align="absmiddle" /><?php  echo write_lang('key_activar_produto','Activar Produto')?>:</td>
		<td class="preto"  colspan="2"> <input type=checkbox name=aproved value=1 <?php  if($aproved) echo "checked";?>>&nbsp;<?php  echo write_lang('key_sim','Sim')?></td>
	</tr>
	</table>
	
</td>
</tr>
</table>