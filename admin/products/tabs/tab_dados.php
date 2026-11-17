<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
<tr>
<td valign="top"><?
	if(!$aproved || !$m_marca)
	{?>
		<div class="alert alert-danger alert-bordered show">
            <strong>ATENÇÃO!</strong>
            ATENÇÃO, ESTE PRODUTO AINDA NÃO CUMPRE OS REQUISITOS MINIMO PARA FICAR VISIVEL NO SITE
            <span class="close" data-dismiss="alert">×</span>
        </div>
	<? }
	if(!$stock)
	{?>
		<div class="alert alert-warning alert-bordered show">
            <strong>ATENÇÃO!</strong>
            ESTE PRODUTO NÃO TEM STOCK
            <span class="close" data-dismiss="alert">×</span>
        </div>
	<? } ?>
	<br />
	<div id="SEO" style="display:none; background: #CCCCCC; padding: 10px;">
		<script>
			function change_seo_description(SEO_description)
			{
				$('#SEO_description_preview').text(SEO_description);
				if(SEO_description == ''){$('#SEO_description_preview').text('<?=$C?>');}
			}
			function change_seo_title(SEO_title)
			{
				$('#SEO_title_preview_link').text(SEO_title);
				if(SEO_title == ''){$('#SEO_title_preview_link').text('<?=$C?>');}
			}
		</script>
		<h1 class="text-center">SEO OPTIMIZATION</h1><br/>
		<table width="100%" cellpadding="5" cellspacing=0 border=0 bgcolor="#CCCCCC">
	    <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top"><?php echo write_lang('key_meta_title','Meta Title');?>:</td>
	        <td ><input name="SEO_title" style="WIDTH: 100%" maxlength="255" value="" class="form-control" onkeyup="change_seo_title(this.value)"><script>document.form.SEO_title.value="<?php echo str_replace('"','\"',$SEO_title);?>";</script><br />
	        <?php echo write_lang('key_txt_title','');?>
			</td>
	    </tr>
	    <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top"><?php echo write_lang('key_meta_description','Meta Description');?>:</td>
	        <td ><input name="SEO_description" style="WIDTH: 100%" maxlength="255" value="" class="form-control" onkeyup="change_seo_description(this.value)"><script>document.form.SEO_description.value="<?php echo str_replace('"','\"',$SEO_description);?>";</script><br />
	        <?php echo write_lang('key_txt_description','');?>
			</td>
	    </tr>
	    <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top"><?php echo write_lang('key_pagina_keywords','Página Keywords');?>:</td>
	        <td ><input name="SEO_keywords" style="WIDTH: 100%" maxlength="255" value="" class="form-control"><script>document.form.SEO_keywords.value="<?php echo str_replace('"','\"',$SEO_keywords);?>";</script><br />
	         <?php echo write_lang('key_txt_processos','');?></td>
	    </tr>
	    <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top"><?php echo write_lang('key_produto_alt','Alt texto das imagens/fotos');?>:</td>
	        <td ><input name="SEO_alt" style="WIDTH: 100%" maxlength="255" value="" class="form-control"><script>document.form.SEO_alt.value="<?php echo str_replace('"','\"',$SEO_alt);?>";</script><br />
	     </td>
	    </tr>
	    <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top"><?php echo write_lang('key_produto_h1','H1');?>:</td>
	        <td ><input name="SEO_h1" style="WIDTH: 100%" maxlength="255" value="" class="form-control"><script>document.form.SEO_h1.value="<?php echo str_replace('"','\"',$SEO_h1);?>";</script><br />
	     </td>
	    </tr>
	    <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top">Imagem alternativa para partilhas:</td>
	        <td>
	        	<?php
				if($SEO_img!='')
				{
				?>
					<div class="imagens_400"><img src="<? echo "../../files/products/".$ID."_seo_".$admin_lang.".".$SEO_img;?>" alt="" border="0" /></div><br />
					<?php
					if($task != "apagar")
					{
						echo write_lang('key_apagar','Apagar');?>
						<input type="checkbox" name="Apagar_SEO_img" value="1">
						<?php echo write_lang('key_ou','ou'); echo write_lang('key_alterar','Alterar')?> <input type="file" name="SEO_img" /><input type="hidden" name="tem_SEO_img" value="1" /><?php
					}
				}
				else
				{
					echo write_lang('key_nova','Nova')?> : <input type="file" name="SEO_img" /><input type="hidden" name="tem_SEO_img" value="1" />
				<?php }?>

		    </td>
	    </tr>
	   <tr>
	        <td width="5" height="26"></td>
	        <td width="100"  valign="top">Partilhar:</td>
	        <td><?
	        	$url=$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['products_page_id'], $ID, 2,$admin_lang);
	        	?>
				<a class="btn btn-primary" href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">FACEBOOK</a>
				<a class="btn btn-primary" href="#" onclick="window.open('https://twitter.com/intent/tweet?text=Tweet%20this&url=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">TWITTER</a>
				<a class="btn btn-primary" href="#" onclick="window.open('https://www.linkedin.com/sharing/share-offsite?url=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">LINKEDIN</a>
				<a class="btn btn-primary" href="#" onclick="window.open('https://developers.facebook.com/tools/debug/?q=<?=$url?>','_blank');return false;">DEBUG FACEBOOK</a>
		    </td>
	    </tr>
	    </table>
	    <div class="SERP_preview" style="background:#fff;padding:20px;margin:0 auto">
	    	<div width="100" style="padding-bottom:10px;display:inline-block">
	    		<div style="float:left;width:auto"><img src="../layout/logo-google-snippet.png" width="50"></div>
	    		<div style="float:left;width:auto;font-size:14px;font-weight:bold;padding-left:5px">Snippet</div>
	    	</div>
	    	<div style="border: solid 1px #CCC; padding: 18px;">
		    	<div id="SEO_title_preview" style="text-align:left;width:100%;padding-bottom:5px"><a id="SEO_title_preview_link" style="color:#12c; font-size: 16px;" href="<?php echo $site_http_url.seo($site_http_url.'pages.php?page_id=3&prod_id='.$ID, $ID,2, $admin_lang) ?>" target="_blank"><?= $SEO_title_preview ?></a></div>
		    	<div id="SEO_url_preview" style="text-align:left;width:100%;color:#093;padding-bottom:5px"><?=$site_http_url.seo($site_http_url.'pages.php?page_id=3&prod_id='.$ID, $ID,2, $admin_lang) ?></div>
		    	<div id="SEO_description_preview" style="text-align:left;width:100%;padding-bottom:5px"><?=$SEO_description_preview ?></div>
	    	</div>
	    </div>
	<br>
	</div>
	<table width="100%" border=0 cellpadding="2" cellspacing=0>
	<tr>
		<td class="preto" width="250">Data criação: <strong><?=$Data_criacao;?></strong></td>
		<td class="preto">Data alteração: <strong><?=$Data_update;?></strong> <strong><?php

		if($ID_Admin != '' )
        {
        	$dados = getDadosAdministradorAdmin($ID_Admin);
        	echo $dados['Nome'];
 		}
 		else echo "IMPORTADOR";


		?></strong></td>
	</tr>
	<tr>
		<td class="preto" width="120"><? echo write_lang('key_ordem','Ordem')?>:</td>
		<td class="preto" align="left" valign="top">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="400"><input class="form-control" name="ordem" style="WIDTH:50px" maxlength="5" value="<?=$ordem;?>" /></td>
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
				<td width="170"><? gera_lista_marcas2("marca","brands","brands_lang",$m_marca);?></td>
				<td class="preto">&nbsp;</td>
			</tr>
			</table>
		</td>
	</tr><? }?>
	 <tr>
		<td class="preto" width="120"> <? echo write_lang('key_categoria','Categoria');?>/Familia:</td>
		<td class="preto" align="left" valign="top">
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width="170"><?php gera_lista_familias($ID_Fam,"products_families","products_families_lang");?></td>
				<td class="preto">
					<!--<div style="width: 200px; padding: 5px; background-color: #EEE; border:1px solid #DDD; position: absolute; right: 20px; top: 20px; text-align: right; line-height: 18px; font-size: 120%;">--><?

						// novo campo Preco Custo criado a 2020.11.06

						// // PVP ---------------------------
						// // $prods=\get_produtc_info($_GET['ID']);
						// // var_dump($prods['prod_precos']);
						// $prod_precos=get_produtc_price($_GET['ID']);
						// // echo '+++'.$prod_precos['PVP'];
						// // var_dump($prods2['prod_precos']);
						// // if($prods['prod_precos']['PVP'] > 0)
						// if($prod_precos['PVP']>0)
						// {
						// 	// $pvp=$prods['prod_precos']['PVP'];
						// 	$pvp=$prod_precos['PVP'];
						// 	// $txiva=$prods['prod_precos']['IVA'];
						// 	// $pvpsiva=($pvp/(1+($txiva/100)));

						// 	if($sync)
						// 	{
						// 		$pcusto=$preco_custo_custom;
						// 		$pcusto2=$pcusto/1.23;
						// 		$fornecedor_custom = $GLOBALS['site_title'];
						// 	}
						// 	else
						// 	{
						// 		// P. custo ----------------------
						// 		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
						// 		$rs=new DataObj;$rs2=new DataObj;
						// 		$sql="SELECT * FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$_GET['ID']."' AND obj_type_id > 0 and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs.obj_id ASC";
						// 		$rs->db_open($db_link,$DB_name,$sql);
						// 		if(!$rs->EOF())
						// 		{
						// 			$pcusto=$rs->db_data["obj_pvp"];
						// 			$pcusto2=$pcusto/1.23;
						// 			// $pcustoiva=$rs->db_data["obj_iva"];
						// 			// $pcustosiva=($pcusto/(1+($pcustoiva/100)));
						// 			// var_dump($rs->db_data);
						// 			$obj_Supplier_ID=$rs->db_data['obj_Supplier_ID'];
						// 			$fornecedor_custom = $Suppliers_nomes[$obj_Supplier_ID];
						// 			// $rs->db_next();
						// 		}
						// 		$rs->db_close();
						// 	}

						// 	// $pcusto=$pcusto-83;

						// 	if($pcusto)
						// 	{
						// 		$margem = ($pvp-$pcusto);
						// 		$margem_perc = (($margem*100)/$pvp);
						// 	}
						// 	echo 'PVP Site: '.number_format($pvp,2,',','.').' &euro;';
						// 	$preco_site_s_iva = $pvp/1.23;
						// 	echo '<br>PREÇO SITE S/IVA: '.number_format($preco_site_s_iva,2,',','.').' &euro;';
						// 	// echo '<br>PVP s/IVA: '.number_format($pvpsiva,2,',','.').' &euro;';
						// 	echo '<br>P. Custo: '.number_format($pcusto,2,',','.').' &euro;';
						// 	echo '<br>P. Custo S/IVA: '.number_format($pcusto2,2,',','.').' &euro;';
						// 	echo '<br>Fornecedor: '.$fornecedor_custom;
						// 	if($margem)
						// 		echo '<br>Margem: '.number_format($margem,2,',','.').' &euro; ('.number_format($margem_perc, 2).'%)';
						// 	else
						// 		echo '<br>Margem: NÃO É POSSÍVEL CALCULAR';
						// }

					?><!--</div>-->
				</td>
			</tr>
			</table>
		</td>
	</tr>
	  <tr>
		<td class="preto" width="180">Referência:</td>
		<td class="preto" align="left" valign="top"><input class="form-control" type="text" name="ref" style="WIDTH:300px" maxlength="255" value="<? echo $Ref;?>" /> </td>
	</tr>
	</tr>
	  <tr>
		<td class="preto" width="180">EAN:</td>
		<td class="preto" valign="top">
			<div style="white-space:nowrap;">
				<div style="float:left"><input class="form-control" type="text" name="cod_ean" style="WIDTH:300px" maxlength="255" value="<? echo $cod_ean;?>" /></div>
				<?php if($GLOBALS['link_kk']==1){?><div style="float:left;line-height:30px;margin-left:10px"><a href='https://www.kuantokusta.pt/search?q=<? echo $cod_ean; ?>' target ="_blank">KuantoKusta</a></div><? }?>
			</div>
		</td>
	</tr>
	<tr>
		<td class="preto"><? echo write_lang('key_nome','Nome')?>/Descrição:</td>
		<td class="preto" align="left" valign="top"><input class="form-control" type="text" name="cabecalho" style="WIDTH:100%" maxlength="255" value="<? echo $C;?>" /> </td>
	</tr>
	<?php

	if($GLOBALS['CAMPOS_EXTRA'] > 0)
	{
		$i=0;
		while($i<count($GLOBALS['CAMPOS_EXTRA']))
		{?>
			<tr>
				<td class="preto"><?=$GLOBALS['CAMPOS_EXTRA'][$i]['nome']?>:</td>
				<td class="preto" align="left" valign="top">
					<table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width="110"><input class="form-control" type="text" name="<?=$GLOBALS['CAMPOS_EXTRA'][$i]['campo']?>" style="WIDTH:100px" maxlength="255" value="<?=$GLOBALS['CAMPOS_EXTRA'][$i]['valor']?>" /></td>
					</tr>
					</table>
				</td>
			</tr><?

			$i++;
		}
	}

	/* if($GLOBALS['id_revendedor'] > 0){?>
		<tr>
		<td class="preto">Desativar Desconto 4%:</td>
		<td class="preto" align="left" valign="top"><div style="float:left"><input type="checkbox" name="descontopreco" <?php if($descontopreco || (isset($_GET['ID']) && $_GET['ID'] == 0)){?> checked="checked" <?php }?> value="1" /></div><div style="float:left;margin-left:5px"></div></td>
	</tr>
	<tr>
		<td class="preto">&nbsp;</td>
	</tr>

	<?php } */?>
	<? /*if($id_revendedor > 0){?>
	<tr>
		<td class="preto">Buscar Preço Cliente Final:</td>
		<td class="preto" align="left" valign="top"><input type="checkbox" name="preco_revenoff" <?php if($preco_revenoff){?> checked="checked" <?php }?> value="1" /></td>
	</tr>
	<?php }*/

	if(isset($prod_conf) && $prod_conf['tem_tabelas_precos'] == 1)
	{?>
	<tr>
		<td class="preto" colspan="4"><br>
			<fieldset>
			<legend>Definição de preço</legend>
		</td>
	</tr>
	<tr>
		<td class="preto">Desativar margem familia:</td>
		<td class="preto" align="left"><div style="float:left"><input type="checkbox" name="margem_fam" id="margem_fam" <?php if($margem_fam == 1){?> checked="checked" <? }?> value="1" /></div></td>
	</tr>
	<tr>
		<td class="preto" width="180" align="left" valign="top" colspan="3"><?php
			if($GLOBALS['tem_importadores'] == 1) echo products_objs($ID,$sync,$preco_custo_custom);
			else echo products_objs_standard($ID, $sync,$preco_custo_custom);
		?></td>
	</tr>
	<?php
	}?>
	<tr>
		<td class="preto" colspan="4"><br>
			<fieldset>
			<legend>Outras informações</legend>
		</td>
	</tr><?php

	if($sync==0 && $id_revendedor > 0)
	{?>
	<tr>
		<td class="preto"><? echo write_lang('key_peso','Peso')?> Fornecedor(kg):</td>
		<td class="preto" align="left" valign="top"><input class="form-control" type="text" name="peso" style="WIDTH:100px;background:#EAEAEA" readonly="" value="<? echo $peso;?>" /></td>
	</tr><?php
	}?>
	<tr>
		<td class="preto"><? echo write_lang('key_peso','Peso')?>(kg):</td>
		<td class="preto" align="left" valign="top"><input class="form-control" type="text" name="peso_cliente" style="WIDTH:100px" value="<? echo ((isset($peso_cliente) && $peso_cliente > 0) ? $peso_cliente : $peso);?>" /></td>
	</tr>
	<?php if(isset($prod_conf) && $prod_conf['tem_dimensoes'] == 1){?>
	<tr>
		<?php
			if($qnt_esp == 1)
			{
				echo '<td class="preto"> Profundidade (cm) :</td>
					  <td class="preto"> <input class="form-control" type="text" name="profundidade" id="profundidade" value="<?php echo $profundidade ?>"> </td>
					  ';
			}else{
				echo '<td class="preto"> Dimensões  (cm) :</td><td><div style="width:30%;float:left;">Altura</div><div style="width:30%;float:left;">Largura</div><div style="width:30%;float:left;">Profundidade</div></td></tr>';
				echo '<tr><td class="preto"> </td><td><div style="width:30%;float:left;"><input style="width:90%" class="form-control" type="text" name="altura" id="altura" value="'.$altura.'"></div><div style="width:30%;float:left;"><input style="width:90%" class="form-control" type="text" name="largura" id="largura" value="'.$largura.'"></div><div style="width:30%;float:left;"><input style="width:90%" class="form-control" type="text" name="profundidade" id="profundidade" value="'.$profundidade.'"></div></td>';
			}
		?>
	</tr>
	<?php }?>

	<?php if(isset($prod_conf) && $prod_conf['tem_servicos_associados'] == 1){?>
	<tr>
		<td class="preto">Serviços :</td>
		<td class="preto" colspan="2">
 		<?
 		$rs2 = new DataObj;
 		$sql = 'SELECT t.ID, t.Ordem, t.Visivel, tl.Nome FROM servicos t INNER JOIN servicos_lang tl ON tl.ID_PAI = t.ID WHERE tl.Lang="'.$GLOBALS['admin_lang'].'"ORDER BY t.Ordem ASC, t.ID ASC ';
 		$rs2->db_open($db_link,$DB_name,$sql);
 		if($rs2->EOF()) // NÂO EXISTE NA LINGUA SELECIONADA VAMOS BUSCAR EM PT
		{
			$rs2->db_close();
			$sql = 'SELECT t.ID, t.Ordem, t.Visivel, tl.Nome FROM servicos t INNER JOIN servicos_lang tl ON tl.ID_PAI = t.ID WHERE tl.Lang="pt"ORDER BY t.Ordem ASC, t.ID ASC ';
			$rs2->db_open($db_link,$DB_name,$sql);
		}
		if(!$rs2->EOF())
		{
			?><select name="servicos[]" id="servicos" multiple="" class="chzn-select" ><?php
			while(!$rs2->EOF())
			{?>
				<option <?php if(in_array($rs2->db_data['ID'],$servicos)) echo 'selected="selected"';?> value="<?php echo $rs2->db_data['ID'];?>"><?php echo $rs2->db_data['Nome'];?></option>
				<?php
				$rs2->db_next();
			}
			?></select><?php
		}
		$rs2->db_close();
		?>
		</td>
	</tr>
	<? }?>

	<?php if(isset($prod_conf) && $prod_conf['tem_varios_tempos_entrega'] == 1){?>
	<tr>
	<td class="preto">Tempos de entrega escolhidos :</td>
	<td class="preto" colspan="2">
 	<?
 		$rs2 = new DataObj;

 		$sql = 'SELECT t.ID, t.Ordem, t.Visivel, tl.Nome FROM entrega t INNER JOIN entrega_lang tl ON tl.ID_PAI = t.ID WHERE tl.Lang="'.$GLOBALS['admin_lang'].'"ORDER BY t.Ordem ASC, t.ID ASC ';
		$rs2->db_open($db_link,$DB_name,$sql);
		if($rs2->EOF()) // NÂO EXISTE NA LINGUA SELECIONADA VAMOS BUSCAR EM PT
		{
			$rs2->db_close();
			$sql = 'SELECT t.ID, t.Ordem, t.Visivel, tl.Nome FROM entrega t INNER JOIN entrega_lang tl ON tl.ID_PAI = t.ID WHERE tl.Lang="pt"ORDER BY t.Ordem ASC, t.ID ASC ';
			$rs2->db_open($db_link,$DB_name,$sql);
		}

		if(!$rs2->EOF())
		{
			?><select name="entregas[]" id="entregas" multiple="" class="chzn-select" ><?php
			while(!$rs2->EOF())
			{
				?>
				<option <?php if(in_array($rs2->db_data['ID'],$entregas)) echo 'selected="selected"';?> value="<?php echo $rs2->db_data['ID'];?>"><?php echo $rs2->db_data['Nome'];?></option>
				<?php
				$rs2->db_next();
			}
			?></select><?php
		}
		$rs2->db_close();
		?>
		</td>
	</tr>
	<?php }?>


	<?php if(isset($prod_conf) && $prod_conf['tem_tempo_entrega'] == 1 && !$prod_conf['tem_varios_tempos_entrega']){?>
	<tr>
		<td class="preto">Tempo entrega:</td>
		<td class="preto" colspan="2"> <input class="form-control" type="text" name="tempo_entrega" style="WIDTH:100px"  value="<?php echo $tempo_entrega?>" /></td>
	</tr>
	<?php }
	if($sync==0 && $id_revendedor > 0){ ?>
	<tr>
		<td class="preto">Tipo Stock:</td>
		<td class="preto" width="180" align="left" colspan="3">
			<select name="estados_produto" class="form-control4" style="width:300px">
				<option value="0" <?php if(isset($estados_produto) && $estados_produto == 0 || !isset($estados_produto)){echo 'selected="selected"';}?>>Stock vêm da Integração</option>
				<option value="1" <?php if(isset($estados_produto) && $estados_produto == 1){echo 'selected="selected"';}?>>Stock Manual</option>
			</select>
		</td>
	</tr>
	<?php
	}

	if(isset($prod_conf) && $prod_conf['tem_sincro'] == 1)
	{
		?>
		<tr>
			<td class="preto">Desativar Sincronização auto:</td>
			<td class="preto" align="left"><div style="float:left"><input type="checkbox" name="sync" id="sync" <?php if($sync || (isset($_GET['ID']) && $_GET['ID'] == 0)){?> checked="checked" <? }?> value="1" /></div><div style="float:left;margin-left:5px"><span style="color:#348fe2;font-weight:bold;font-size:x-small;">1 - Se esta opção não for selecionada sempre que houver atualização da integração toda a informação será atualizada<br />2 - As Características do produto vão ignorar esta flag caso o produto seja um produto de integração</span></div></td>
		</tr>
		<!-- novo campo Preco Custo criado a 2020.11.06 -->
		<tr id="preco_custo_custom_container" style="<?php if(!$sync){ ?>display: none;<?php } ?>">
			<td class="preto">Preço Custo (s/ IVA):</td>
			<td class="preto" align="left"><input class="form-control" type="text" name="preco_custo_custom" style="WIDTH:300px" maxlength="255" value="<? echo $preco_custo_custom;?>" /></td>
		</tr>
		<script type="text/javascript">
			$('#sync').on('click', function(){
				var chk = $(this).attr('checked');
				// console.log('sync click = '+chk);
				if(chk=='checked')
					$('#preco_custo_custom_container').show();
				else
					$('#preco_custo_custom_container').hide();
			});
		</script>
		<?php
	}

	if(isset($prod_conf) && $prod_conf['tem_upload_fx'] == 1){?>
	<tr>
		<td class="preto">Poduto com submissão de Ficheiro:</td>
		<td class="preto" align="left"><div style="float:left"><input type="checkbox" name="c_submissao_ficheiro" <?php if($c_submissao_ficheiro == 1){?> checked="checked" <? }?> value="1" />&nbsp;<?php  echo write_lang('key_sim','Sim')?></div></td>
	</tr>
	<?php
	}
	if(isset($prod_conf) && $prod_conf['tem_prod_grafica'] == 1){?>
	<tr>
		<td class="preto">Preços multiplos:</td>
		<td class="preto" align="left" valign="top"><div style="float:left"><input type="checkbox" name="produto_standard" <?php if($produto_standard == 1){?> checked="checked" <? }?> value="1" /><span style="color:#FF0000;font-weight:bold;font-size:x-small;">&nbsp;Se este campo estiver ativo os preços vem das caracteristicas do produto</span></div></td>
	</tr>
	<?php
	}


	if(isset($prod_conf) && $prod_conf['tem_encomenda_sem_stock'] == 1){?>
	<tr>
		<td class="preto">Pode encomendar sem stock:</td>
		<td class="preto" align="left"><div style="float:left"><input type="checkbox" name="encomenda_sem_stock" <?php if($encomenda_sem_stock == 1){?> checked="checked" <? }?> value="1" />&nbsp;<?php  echo write_lang('key_sim','Sim')?></div></td>
	</tr>
	<?php
	}

	if(isset($prod_conf) && $prod_conf['tem_carrinho'] == 1){?>
	<tr>
		<td class="preto"><img src="ok.png" width="20" height="20" align="absmiddle" />Ativar Carrinho:</td>
		<td class="preto" colspan="2"> <input type=checkbox name=carrinho value=1 <?php  if($carrinho) echo "checked";?>>&nbsp;<?php  echo write_lang('key_sim','Sim')?></td>
	</tr>
	<?php }?>
	<?php
	if($GLOBALS['destaque_homepage']==1){?>
	<tr>
		<td class="preto"><img src="ok.png" width="20" height="20" align="absmiddle" /><?php  echo write_lang('key_tem_prod_destaque_homepage','Especial destaque na homepage')?>:</td>
		<td class="preto" colspan="2"> <input type=checkbox name=tem_prod_destaque_homepage value=1 <?php  if($tem_prod_destaque_homepage) echo "checked";?>>&nbsp;<?php  echo write_lang('key_sim','Sim')?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="preto"><img src="ok.png" width="20" height="20" align="absmiddle" /><?php  echo write_lang('key_activar_produto','Activar Produto')?>:</td>
		<td class="preto" colspan="2"><div style="border:solid 1px #348fe2;padding:10px;width:100px"><input type=checkbox name=aproved value=1 <?php  if($aproved) echo "checked";?>>&nbsp;<?php  echo write_lang('key_sim','Sim')?></div></td>
	</tr>

	<!-- RESERVA DE STOCK-->
	<?
	if($GLOBALS['mostra_reserva_stock']==1)
	{?>
	<tr>
		<td class="preto" colspan="4"><br>
			<fieldset>
			<legend>Reserva de stock:</legend>
		</td>
		</tr>
		<tr>
			<td class="preto" width="180" align="left" valign="top" colspan="3"><?php echo reserva_stock($ID); ?></td>
		</tr>
		<?php
	}
	?>
	<tr>
		<td class="preto" colspan="4"><br>
			<fieldset>
			<legend>Influenciadores de preço</legend>
		</td>
	</tr>
	<tr>
		<td class="preto" width="180" align="left" valign="top" colspan="3"><?php

		$preco_default=get_produtc_price($ID);

		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
		{
			$custos_logisticos=portes_extra_prod($ID,$preco_default['PVP']);
			for($a=0; $a<count($custos_logisticos); $a++)
			{

				$totalciva += ($custos_logisticos[$a]['valor'] );
				$total_custos_logisticos+=($custos_logisticos[$a]['valor'] );
			}
			if($total_custos_logisticos>0)
			{
				echo '<h3 style="font-size:16px;padding:5px">CUSTO LOGÍSTICO<br></h3>';
				echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc">
				<tr>
					<td class="preto" style="padding:2px" width="450">DOMICÍLIO:</td>
					<td class="preto" style="padding:2px">'.numberformat($total_custos_logisticos).'</td>
				</tr>
				<tr>
					<td class="preto" style="padding:2px" width="450">LOJA:</td>
					<td class="preto" style="padding:2px">'.numberformat(0).'</td>
				</tr>
				</table><br>';
			}
		}
		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1)
		{
			$garantia=extensao_garantia_prod($ID,$preco_default['PVP']);
			if(!empty($garantia))
			{
				echo '<h3 style="font-size:16px;padding:5px">EXTENSÃO DE GARANTIA<br></h3>';
				echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc">';
				for($a=0; $a<count($garantia); $a++)
				{
					$garantia[$a]['titulo'] = 'Garantia Extra de '.$garantia[$a]['anos'].($garantia[$a]['anos'] > 1 ? ' anos' : ' ano' ).'&nbsp';
					echo '<tr>
						<td class="preto" style="padding:2px" width="450">'.$garantia[$a]['titulo'].':</td>
						<td class="preto" style="padding:2px">'.($garantia[$a]['promo_ativo'] == 1 ? '<span style="text-decoration: line-through; font-size:11px;color:#333333">'.numberformat($garantia[$a]['valor_antigo']).'</span>&nbsp; <span style="font-size:12px">'.($garantia[$a]['valor'] > 0 ? 'Agora: ' : '').'</span>&nbsp;' : '&nbsp; ').''.($garantia[$a]['valor'] > 0 ? '+ '.numberformat($garantia[$a]['valor']) : 'Gratuíto').'</td>
					</tr>';
				}
				echo '</table><br>';
			}
		}

		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
		{
			$servicos_extra=servicos_extra_prod($ID,$preco_default['PVP']);
			if(!empty($servicos_extra))
			{
				echo '<h3 style="font-size:16px;padding:5px">SERVIÇOS EXTRA<br></h3>';
				echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc">';
				for($a=0; $a<count($servicos_extra); $a++)
				{
					echo '<tr>
						<td class="preto" style="padding:2px" width="450">'.htmlentities($servicos_extra[$a]['info']).':</td>
						<td class="preto" style="padding:2px">'.($servicos_extra[$a]['promo_ativo'] == 1 ? '<span style="text-decoration: line-through; font-size:11px;color:#333333">'.numberformat($servicos_extra[$a]['valor_antigo']).'</span>&nbsp; <span style="font-size:12px">'.($servicos_extra[$a]['valor'] > 0 ? 'Agora: ' : '').'</span>&nbsp;' : '&nbsp; ').''.($servicos_extra[$a]['valor'] > 0 ? '+ '.numberformat($servicos_extra[$a]['valor']) : 'Gratuíto').'</td>
					</tr>';
				}
				echo '</table><br>';
			}
		}

		//***********************************tem_multiportes
		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
		{
			$portes=multi_portes_prod($ID,$preco_default['PVP']);
			if(!empty($portes))
			{
				echo '<h3 style="font-size:16px;padding:5px">ENTREGA<br></h3>';
				echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc">';
				for($a=0; $a<count($portes); $a++)
				{
					echo '<tr>
						<td class="preto" style="padding:2px" width="450">'.$portes[$a]['titulo'].':</td>
						<td class="preto" style="padding:2px">'.($portes[$a]['promo_ativo'] == 1 ? '<span style="text-decoration:line-through;font-size:11px;color:#333333">'.numberformat($portes[$a]['valor_antigo']).'</span>&nbsp; <span style="font-size:12px">'.($portes[$a]['valor'] > 0 ? 'Agora: ' : '').'</span>&nbsp;' : '&nbsp; ').''.($portes[$a]['valor'] > 0 ? '+ '.numberformat($portes[$a]['valor']) : 'Gratuíto').'</td>
					</tr>';
				}
				echo '</table><br>';
			}
		}


		if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade']== 1)
		{
			echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc"><tr><td>Cobrar metade do custo logistico para segundo ou mais artigos com custo logistico.</td></tr></table>';
		}
		if(isset($GLOBALS['portes_nao_pagar_premium']) && $GLOBALS['portes_nao_pagar_premium']== 1)
		{
			echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc"><tr><td>Não pagar portes nem custo logistico do artigo com portes premium.</td></tr></table>';
		}
		$valor_portes_gratuitos=getPortesGratuitos();
		if($valor_portes_gratuitos>0)echo '<table width="100%" cellspacing="0" cellpadding="4" style="border:solid 1px #ccc"><tr><td>PORTES GRATUITOS PARA VALORES SUPERIORES A '.$valor_portes_gratuitos.'</td></tr></table>';


		?></td>
	</tr>
	</table>
</td>
</tr>
</table>