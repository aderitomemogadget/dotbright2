<table width="100%" border=0 cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">

	<table width="100%" border=0 cellpadding="2" cellspacing="0" style="background-color: #87ceee">
	<tr>
	<td class="preto" style="padding:5px">
	<label class="label_check" for="tipo_reparacao_po"><input type="checkbox" name="tipo_reparacao_po"  id="tipo_reparacao_po" value="1" <?php if($tipo_reparacao_po) echo ' checked=""';?>><span> Pedido de orçamento</span></label>
	<label class="label_check" for="tipo_reparacao_gl"><input type="checkbox" name="tipo_reparacao_gl" id="tipo_reparacao_gl" value="1" <?php if($tipo_reparacao_gl) echo ' checked=""';?>><span> Garantia de loja</span></label>
	<label class="label_check" for="tipo_reparacao_gf"><input type="checkbox" name="tipo_reparacao_gf" id="tipo_reparacao_gf" value="1" <?php if($tipo_reparacao_gf) echo ' checked=""';?>><span> Garantia do fabricante</span></label>
	<label class="label_check" for="tipo_reparacao_gr"><input type="checkbox" name="tipo_reparacao_gr" id="tipo_reparacao_gr" value="1" <?php if($tipo_reparacao_gr) echo ' checked=""';?>><span> Garantia de reparação</span></label>
	</td>
	<td class="preto" style="padding:5px" align="right">
	<input type="text" style="width: 250px" id="ndocumento" class="box" name="ndocumento" type="text" value="<?php echo ($ndocumento != '' ? $ndocumento : 'insira o numero de documento'); ?>" onfocus="if(this.value == 'insira o numero de documento') {this.value='';}" />
	</td>
	</tr>
	</table>

	<br>

	<table width="100%" border=0 cellpadding="2" cellspacing=0 style="background-color: #87ceee">
	<tr>
	<td class="preto" style="width:130px">
	<b>Tipo de reparação:</b>
	</td>
	<td class="preto">
	<label class="label_radio" for="tipo_reparacao1"><input type="radio" name="tipo_reparacao" id="tipo_reparacao1" value="Expresso" <?php if($tipo_reparacao == "Expresso") echo ' checked=""';?>><span> Expresso</span></label>
	<label class="label_radio" for="tipo_reparacao2"><input type="radio" name="tipo_reparacao" id="tipo_reparacao2" value="Entrega no dia" <?php if($tipo_reparacao == "Entrega no dia") echo ' checked=""';?>><span> Entrega no dia</span></label>
	<label class="label_radio" for="tipo_reparacao3"><input type="radio" name="tipo_reparacao" id="tipo_reparacao3" value="Normal" <?php if($tipo_reparacao == "Normal" || !$tipo_reparacao) echo ' checked=""';?>><span> Normal</span></label>
	</td>
	</tr>
	</table>

	<br>

	<br>
	<table width="100%" border=0 cellpadding="2" cellspacing="0" style="background:#FFF">
	<tr>
	<td class="preto" align="left" valign="top" colspan="2">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="120"><b>Pesquisar Cliente</b></td>
		<td>
		<div class="frmSearch">
		<input class="box" name="cliente_pesquisa" id="cliente_pesquisa" type="text" style="WIDTH:300px;" autocomplete="off" maxlength="255" value="<?php echo $cliente_pesquisa; ?>">
		<div id="suggesstion-box"></div>
		<div style="margin-top:2px">Digite Telefone ou Email ou NIF</div>
		</div>
		</td>
		<td class="preto" align="right" width="48"><img src="search2.png" id="cli_click" style="cursor:pointer" /></td>
		<td class="preto" align="center" width="350"><div id="cliente_associado"><?php
		if(isset($_GET['ID']) && $_GET['ID'] > 0)
		{
			if($ID_Cliente > 0)
			{
				echo '<a href="../sa_clientes/pagina.php?ID='.$ID_Cliente.'"><img src="ok.png" border="0" align="absmiddle" style="vertical-align:middle"> <span style="color:#0078d7;font-weight:bold">Assistência Associada a Cliente Website</span></a>';
			}
			elseif($ID_Cliente2 > 0)
			{
				echo '<a href="../sa_clientes/pagina.php?ID='.$ID_Cliente2.'"><img src="ok.png" border="0" align="absmiddle" style="vertical-align:middle"> <span style="color:#0078d7;font-weight:bold">Assistência Associada a Cliente RMA<br>(sem conta website)</span></a>';
			}
			else
			{
				echo '<img src="alerta.png" border="0" align="absmiddle" style="vertical-align:middle"> <span style="color:#FF0000;font-weight:bold">É necessário associar Assistência a um Cliente</span> ';
			}
		}?></div></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>

	<br>
	<table width="100%" border=0 cellpadding="2" cellspacing="0"  style="background:#FFF">

	<tr>
	<td class="preto" width="120">Nome:</td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td>
		<input class="box" name="cliente_nome" id="cliente_nome" style="WIDTH:150px;" type="text" maxlength="255" value="<?php echo $cliente_nome; ?>">
		</td>
		<td width="100" class="preto" align="center">Sobrenome:</td>
		<td>
		<input class="box" name="cliente_sobrenome" id="cliente_sobrenome" type="text" style="WIDTH:150px;" maxlength="255" value="<?php echo $cliente_sobrenome; ?>">
		</td>
		<td width="100" class="preto" align="right">Apelido:</td>
		<td width="10" class="preto" align="right"><img src="cm_fill.gif" width="10" height="1"></td>
		<td>
		<input class="box" name="cliente_apelido" id="cliente_apelido" type="text" style="WIDTH:150px;" maxlength="255" value="<?php echo $cliente_apelido; ?>">
		</td>
		</tr>
		</table>
	</td>
	</tr>
		<tr>
	<td class="preto" width="120">Telefone 1:</td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td>
		<div style="position:relative"><input class="box" name="cliente_telefone1" type="text" id="cliente_telefone1" style="WIDTH:150px;" maxlength="255" value="<?php echo $cliente_telefone1; ?>"><div style="position:absolute;top:1px;right:0;cursor:pointer" title="Pesquisar por Telefone 1" onclick="pesqCliente($('#cliente_telefone1').val(),'tel1');"><img src="search.png" border="0" /></div></div>
		</td>
		<td width="100" class="preto" align="center">Telefone 2:</td>
		<td>
		<div style="position:relative"><input class="box" name="cliente_telefone2" id="cliente_telefone2" type="text" style="WIDTH:150px;" maxlength="255" value="<?php echo $cliente_telefone2; ?>"><div style="position:absolute;top:1px;right:0;cursor:pointer" title="Pesquisar por Telefone 2" onclick="pesqCliente($('#cliente_telefone2').val(),'tel2');"><img src="search.png" border="0" style=/></div>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td class="preto" width="120">Morada: </td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td><input class="box" name="cliente_morada" id="cliente_morada" type="text" style="WIDTH:400px" maxlength="255" value="<?php echo $cliente_morada; ?>"></td>
		<td width="100" class="preto" align="right">Código Postal:</td>
		<td width="10" class="preto" align="right"><img src="cm_fill.gif" width="10" height="1"></td>
		<td><input class="box" name="cliente_cp" id="cliente_cp" style="WIDTH:150px;" type="text" maxlength="255" value="<?php echo $cliente_cp; ?>"></td>
		</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td class="preto" width="120">E-mail: <input type="checkbox" name="fake" id="fake" value="1" <?php if($fake == 1){?>checked="checked"<?php }?> /> nd</td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td><div style="position:relative"><input class="box" name="cliente_username" id="cliente_username" type="text" style="WIDTH:400px" maxlength="255" value="<?php echo $cliente_username; ?>"><div style="position:absolute;top:1px;right:0;cursor:pointer" title="Pesquisar por E-mail" onclick="pesqCliente($('#cliente_username').val(),'email');"><img src="search.png" border="0" /></div></div></td>
		<td width="100" class="preto" align="right">Tipo de cliente:</td>
		<td width="10" class="preto" align="right"><img src="cm_fill.gif" width="10" height="1"></td>
		<td>
		<?php
		$sql = 'SELECT ID, Descricao FROM grupos_clientes';
		$db_sel = new DataObj();
		$marca_id_sel = 0;
		$db_sel->db_open($db_link,$DB_name,$sql);
		?>
		<select style="width: 150px;" name="Grupo_ID" id="Grupo_ID" class="form-control chzn-select">
		<option value="0">CLIENTE FINAL</option>
		<?php
		while(!$db_sel->EOF())
		{
			?><option value="<?php echo $db_sel->db_data['ID']?>" <?php if ($db_sel->db_data['ID'] == $Grupo_ID)echo 'selected="selected"';?>><?php echo $db_sel->db_data['Descricao']?></option><?php

			$db_sel->db_next();
		}
		$db_sel->db_close();
		?>
		</select>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td class="preto" width="120">Empresa: </td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td><input class="box" name="cliente_empresa" id="cliente_empresa" type="text" style="WIDTH:400px" maxlength="255" value="<?php echo $cliente_empresa; ?>"></td>
		<td width="100" class="preto" align="right">NIF:</td>
		<td width="10" class="preto" align="right"><img src="cm_fill.gif" width="10" height="1"></td>
		<td><div style="position:relative"><input class="box" name="cliente_nif" id="cliente_nif" type="text"  style="WIDTH:150px;" maxlength="255" value="<?php echo $cliente_nif; ?>" /><div style="position:absolute;top:1px;right:0;cursor:pointer" title="Pesquisar por NIF" onclick="pesqCliente($('#cliente_nif').val(),'nif');"><img src="search.png" border="0" /></div></div></td> <?php /* onchange="getCliente('nif',this.value)" */?>
		</tr>
		</table>
	</td>
	</tr>
	</table>
	<?php /*
	<div style="display:none">
	<br>
	<table width="100%" border=0 cellpadding="2" cellspacing="0"  style="background:#FFF">

	<tr>
	<td class="preto" width="120"><b>Conta Cliente</b>:</td>
	<td class="preto" align="left" valign="top">
		<label class="label_radio" for="novo_cliente" style="margin-top: 0px;"><input type="radio" name="novo_cliente" id="novo_cliente" value="0" /><span style="font-weight:bold"> Não fazer nada</span></label>
		<div style="clear:both"></div>
		<label class="label_radio" for="novo_cliente1" style="margin-top: 0px;"><input type="radio" name="novo_cliente" id="novo_cliente1" value="1" <?php if($ID_Cliente == 0){?>checked="checked"<?php }?> /><span style="font-weight:bold"> Este cliente não existe, <u>Criar Conta</u></span></label>
		<div style="clear:both"></div>
		<label class="label_radio" for="novo_cliente2" style="margin-top: 0px;"><input type="radio" name="novo_cliente" id="novo_cliente2" value="2" /><span style="font-weight:bold"> Este cliente existe, <u>Atualizar Conta</u></span></label>
		<div style="clear:both"></div>
		<label class="label_radio" for="novo_cliente3" style="margin-top: 0px;"><input type="radio" name="novo_cliente" id="novo_cliente3" value="3" <?php if($ID_Cliente > 0){?>checked="checked"<?php }?>/><span style="font-weight:bold"> Este cliente existe, <u>Atualizar Apenas Dados em Falta</u></span></label>
	</td>
	</tr>
	</table>
	</div>
	*/ ?>
</td>
</tr>
</table>
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">

	<table width="100%" border=0 cellpadding="2" cellspacing=0>
<tr>
	<td class="preto" width="120"> Marca:</td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="150">
			<input type="text" name="nova_marca" id="nova_marca" style="display: none;width:150px" class="box" placeholder="Insira nova Marca">
			<select id="sel_marca" name="m_marca" onchange="newmarca(this.value, this);" class="chzn-select" style="width:150px">
			<option value="">----------</option>
			<option value="new">NOVA</option>
				<?php
				$sql = 'SELECT ID, marca FROM marcas_rmas ORDER BY Ordem ASC, marca ASC';
				$db_sel = new DataObj();
				$marca_id_sel = 0;
				$db_sel->db_open($db_link,$DB_name,$sql);
				while(!$db_sel->EOF())
				{
					$nome = $db_sel->db_data['marca'];
					if ($m_marca == $nome && $marca_id_sel == 0) $marca_id_sel = $db_sel->db_data['ID'];

					?>
					<option data="<?php echo $db_sel->db_data['ID'];?>" value="<?php echo $nome?>" <?php if ($m_marca == $nome)echo 'selected="selected"';?> ><?php echo $nome?></option>
					<?
					$db_sel->db_next();
				}
				$db_sel->db_close();
				?>
			</select>

			<script>
				function newmarca(val, t)
				{
					if (val == "new")
					{
						var marca = document.getElementById('nova_marca');
						marca.style.display = "block";

						var modelo = document.getElementById('novo_modelo');
						modelo.style.display = "block";

						if(document.getElementById('get_modelos'))
						{
							var option = $('option:selected', t).attr('data');
							getModelos(option);
						}
					}
					else
					{
						var marca = document.getElementById('nova_marca');
						marca.style.display = "none";
						var modelo = document.getElementById('novo_modelo');
						modelo.style.display = "none";

						if(document.getElementById('get_modelos'))
						{
							var option = $('option:selected', t).attr('data');
							getModelos(option);
						}

					}
				}
				function getModelos(marca_id)
				{
					var result = '';
					$.ajax({
						type: "GET",
						url:  "<?php echo $GLOBALS['site_http_url'].'rmas/ajax/getModelos.php';?>",
						data: {m:marca_id}
					})
					.done(function (resultado)
					{
						result = resultado.split('[split]');

						$('#get_modelos').html(result[0]);

					});

				}
			</script>
		</td>
		<td width="100" class="preto" align="center"><?php echo write_lang('key_modelo','Modelo') ?>:</td>
		<td>

			<div id="get_modelos">
			<input type="text" name="novo_modelo" id="novo_modelo" style="display: none;width:150px" class="box" placeholder="Insira novo Modelo">
			<?php
			$db_sel = new DataObj();
			if($marca_id_sel > 0) $sql = 'SELECT modelo FROM modelos_rmas WHERE marca_id='.$marca_id_sel.' ORDER BY Ordem ASC, modelo ASC';
			// echo
			// else $sql = 'SELECT modelo FROM modelos_rmas WHERE marca_id='.$marca_id_sel;
			// echo $sql;
			if($marca_id_sel > 0) $db_sel->db_open($db_link,$DB_name,$sql);

			?>

			<select id="sel_modelo" name="m_modelo" onchange="newmodelo(this.value);" class="chzn-select" style="width:150px">
			<option value="">----------</option>
			<option value="new">NOVO</option>
				<?php
				if($marca_id_sel > 0)
				{
					while(!$db_sel->EOF())
					{
						$nome = $db_sel->db_data['modelo'];
						?>
						<option value="<?php echo $nome; ?>" <?php if ($m_modelo == $nome) echo 'selected="selected"';?> ><?php echo $nome?></option>
						<?php
						$db_sel->db_next();
					}

					$db_sel->db_close();
				}
				?>
			</select>
			</div>
			<script>
				function newmodelo(val){
					if (val == "new") {
					   var modelo = document.getElementById('novo_modelo');
						modelo.style.display = "block";
					}
					else {
						var modelo = document.getElementById('novo_modelo');
						modelo.style.display = "none";
					}
				}
			</script>
		</td>

		</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td class="preto" align="left">IMEI:</td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="150"><input class="box" id="m_imei" name="m_imei" type="text" style="WIDTH:150px;" maxlength="255" value="<?php echo $m_imei; ?>"></td>
		<td width="100" class="preto" align="center">Nr. Serie:</td>
		<td>
			<input class="box" id="m_nserie" name="m_nserie" type="text" style="WIDTH:150px;" maxlength="255" value="<?php echo $m_nserie; ?>" onchange="verificaNrSerie6Meses(this.value)" />
			<div id="avisoNrSerie" style="float:left;"></div>
			<script>
				function verificaNrSerie6Meses(valor) {
					$.get('verificaNrSerie.php?nr=' + valor,function (data)
					{
						$('#avisoNrSerie').html(data);
					});
				}
			</script>
		</td>
		<td class="preto" width="130" align="right">Vossa Referência:&nbsp;&nbsp;</td>
		<td width="120"><input class="box" id="m_referencia" name="m_referencia" type="text" style="width:120px;text-align:left;" maxlength="255" value="<?php echo $m_referencia; ?>"></td>
		</tr>
		</table>
	</td>
	</tr>
		<tr>
	<td class="preto">Tracking:</td>
	<td class="preto" align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="150" valign="top"><input type="text" class="box" name="m_tracking" style="width:150px" maxlength="255" disabled value="<? if ($IDb != 0) echo "DB".str_pad((int)$IDb,6,"0",STR_PAD_LEFT); else echo ''; ?>"> </td>
		<td width="100" align="center">Código de desbloqueio:&nbsp;&nbsp;</td>
		<td width="150"><input class="box" style="width: 150px; text-align: left;" type="text" id="codigo_desbloqueio" name="codigo_desbloqueio" value="<?php echo ($codigo_desbloqueio == '' ? '' : $codigo_desbloqueio); /* $codigo_desbloqueio == '0' ||  */?>"></td>
		<td width="130" class="preto" align="right">Padrão:&nbsp;&nbsp;</td>
		<td valign="top">
		<div style="position:relative">
		<input class="box" type="text" onclick="showpattern();$('#buttonpattern2').hide();$('#buttonpattern').show();" name="m_padrao" id="m_padrao" value="<?php echo ($m_padrao == 0 || $m_padrao == '' ? '' : $m_padrao);?>" style="width:120px;float:left">
		<input type="button" id="buttonpattern2" onclick="showpattern();$('#buttonpattern2').hide();$('#buttonpattern').show();" style="float:left;display:block;height: 25px;width: 65px;background: #87ceee;" value="Mostrar">
		<input type="button" id="buttonpattern" onclick="hidepattern();$('#buttonpattern').hide();$('#buttonpattern2').show();" style="float:left;display:none;height: 25px;width: 65px;background: #87ceee;" value="Esconder">
		<div id="mypattern" class="mhn-lock pattern_minha"></div>
		</div>
		<style type="text/css">
		.pattern_minha{position: absolute; z-index: 99;display: none; border-radius: 10px; width: 275px !important; height: 273px !important; background-color: rgb(46, 101, 167) !important; right:-7px; top: 27px; border: 1px solid #000000; }
		</style>

		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>

	<br>
	<div style="background:#87ceee;height:30px;line-height:30px;color:#000;padding-left:10px;font-weight:bold;font-size:15px;cursor:pointer" onclick="if($('#software').is(':visible')){$('#software').hide();}else{$('#software').show();}"><img src="down.png" border="0" style="float:left" /> Software <?php if(count($software_arr) > 0){?>(<?php echo count($software_arr).' selecionado'.(count($software_arr) > 1  ? 's' : '');?>)<?php }?></div>
	<div style="display:none;padding:5px;background-color: #87ceee; border-top:#000 1px solid" id="software">
	<table width="100%" border=0 cellpadding="2" cellspacing=0 style="background-color: #87ceee; font-size:13px">
	<tr>
	<td class="preto" align="left" valign="top" style="font-size:13px">
		<label class="label_check" for="software_dr"><input type="checkbox" id="software_dr" name="software_dr" value="1" <?php if($software_dr) echo ' checked=""';?>><span>Desbloqueio à REDE</span></label> <div class="clear"></div>
		<label class="label_check" for="software_rc"><input type="checkbox" id="software_rc" name="software_rc" value="1" <?php if($software_rc) echo ' checked=""';?>><span>Remover Código de Segurança</span></label> <div class="clear"></div>
		<label class="label_check" for="software_na"><input type="checkbox" id="software_na" name="software_na" value="1" <?php if($software_na) echo ' checked=""';?>><span>Não arranca</span></label> <div class="clear"></div>
	</td>
	<td class="preto" align="left" valign="top" style="font-size:13px">
		<label class="label_check" for="software_cg"><input type="checkbox" id="software_cg" name="software_cg" value="1" <?php if($software_cg) echo ' checked=""';?>><span>Remover Conta Google</span></label> <div class="clear"></div>
		<!--label class="label_check" for="software_ci"><input type="checkbox" id="software_ci" name="software_ci" value="1" <?php if($software_ci) echo ' checked=""';?>><span>Remover Conta iCloud</span></label> <div class="clear"></div-->
		<label class="label_check" for="software_ci"><input type="checkbox" id="software_ci" name="software_ci" value="1" <?php if($software_ci) echo ' checked=""';?>><span>Sem Imei no Sistema</span></label> <div class="clear"></div>
		<label class="label_check" for="software_bl"><input type="checkbox" id="software_bl" name="software_bl" value="1" <?php if($software_bl) echo ' checked=""';?>><span>Bloqueia / está lento</span></label> <div class="clear"></div>
	</td>
	</tr>
	</table>
	</div>
	<br>


	<div style="background:#f9d83e;height:30px;line-height:30px;color:#000;padding-left:10px;font-weight:bold;font-size:15px;cursor:pointer" onclick="if($('#hardware').is(':visible')){$('#hardware').hide();}else{$('#hardware').show();}"><img src="down.png" border="0" style="float:left" /> Hardware <?php if(count($hardware_arr) > 0){?>(<?php echo count($hardware_arr).' selecionado'.(count($hardware_arr) > 1  ? 's' : '');?>)<?php }?></div>
	<div style="display:none;padding:5px;background-color: #f9d83e; border-top:#000 1px solid" id="hardware">
	<table width="100%" border=0 cellpadding="2" cellspacing=0 style="background-color: #f9d83e; font-size:13px">
	<tr>
	<td class="preto" align="left" valign="top" style="font-size:13px">
		<label class="label_check" for="hardware_tp"><input value="1" type="checkbox" id="hardware_tp" name="hardware_tp" <?php if($hardware_tp) echo ' checked=""';?>><span>Touch partido</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_tnf"><input value="1" type="checkbox" id="hardware_tnf" name="hardware_tnf" <?php if($hardware_tnf) echo ' checked=""';?>><span>Touch não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_lp"><input value="1" type="checkbox" id="hardware_lp" name="hardware_lp" <?php if($hardware_lp) echo ' checked=""';?>><span>LCD partido</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_lpb"><input value="1" type="checkbox" id="hardware_lpb" name="hardware_lpb" <?php if($hardware_lpb) echo ' checked=""';?>><span>LCD em branco/preto</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_nc"><input value="1" type="checkbox" id="hardware_nc" name="hardware_nc" <?php if($hardware_nc) echo ' checked=""';?>><span>Não carrega</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_ssa"><input value="1" type="checkbox" id="hardware_ssa" name="hardware_ssa" <?php if($hardware_ssa) echo ' checked=""';?>><span>Sem sinal áudio no auscultador</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_sa"><input value="1" type="checkbox" id="hardware_sa" name="hardware_sa" <?php if($hardware_sa) echo ' checked=""';?>><span>Sinal áudio no auscultador baixo ou com Ruído</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_sam"><input value="1" type="checkbox" id="hardware_sam" name="hardware_sam" <?php if($hardware_sam) echo ' checked=""';?>><span>Sem sinal áudio no microfone</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_sp"><input value="1" type="checkbox" id="hardware_sp" name="hardware_sp" <?php if($hardware_sp) echo ' checked=""';?>><span>Sensor de proximidade</span></label> <div class="clear"></div>
	</td>
	<td class="preto" align="left" valign="top" style="font-size:13px">
		<label class="label_check" for="hardware_sal"><input value="1" type="checkbox" id="hardware_sal" name="hardware_sal" <?php if($hardware_sal) echo ' checked=""';?>><span>Sem sinal áudio no altifalante</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_saa"><input value="1" type="checkbox" id="hardware_saa" name="hardware_saa" <?php if($hardware_saa) echo ' checked=""';?>><span>Sinal áudio no altifalante baixo ou com Ruído</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_nl"><input value="1" type="checkbox" id="hardware_nl" name="hardware_nl" <?php if($hardware_nl) echo ' checked=""';?>><span>Não Liga</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_ncrg"><input value="1" type="checkbox" id="hardware_ncrg" name="hardware_ncrg" <?php if($hardware_ncrg) echo ' checked=""';?>><span>Não capta REDE GSM</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_ncrw"><input value="1" type="checkbox" id="hardware_ncrw" name="hardware_ncrw" <?php if($hardware_ncrw) echo ' checked=""';?>><span>Não capta REDE WIFI/bluetooth</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_svl"><input value="1" type="checkbox" id="hardware_svl" name="hardware_svl" <?php if($hardware_svl) echo ' checked=""';?>><span>Lente LCD partido(LCD OK) 7 dias</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_cfnf"><input value="1" type="checkbox" id="hardware_cfnf" name="hardware_cfnf" <?php if($hardware_cfnf) echo ' checked=""';?>><span>Camera frontal não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_cpnf"><input value="1" type="checkbox" id="hardware_cpnf" name="hardware_cpnf" <?php if($hardware_cpnf) echo ' checked=""';?>><span>Camera principal não funciona</span></label> <div class="clear"></div>
	</td>
	<td class="preto" align="left" valign="top" style="font-size:13px">
		<label class="label_check" for="hardware_bhn"><input value="1" type="checkbox" id="hardware_bhn" name="hardware_bhn" <?php if($hardware_bhn) echo ' checked=""';?>><span>Botão Home não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_bvn"><input value="1" type="checkbox" id="hardware_bvn" name="hardware_bvn" <?php if($hardware_bvn) echo ' checked=""';?>><span>Botão de Volume não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_bsn"><input value="1" type="checkbox" id="hardware_bsn" name="hardware_bsn" <?php if($hardware_bsn) echo ' checked=""';?>><span>Botão de Silêncio não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_bof"><input value="1" type="checkbox" id="hardware_bof" name="hardware_bof" <?php if($hardware_bof) echo ' checked=""';?>><span>Botão ON/OFF não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_caj"><input value="1" type="checkbox" id="hardware_caj" name="hardware_caj" <?php if($hardware_caj) echo ' checked=""';?>><span>Conector audioJack não funciona</span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_lsim"><input value="1" type="checkbox" id="hardware_lsim" name="hardware_lsim" <?php if($hardware_lsim) echo ' checked=""';?>><span>Leitor SIM </span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_lmsd"><input value="1" type="checkbox" id="hardware_lmsd" name="hardware_lmsd" <?php if($hardware_lmsd) echo ' checked=""';?>><span>Leitor MicroSD </span></label> <div class="clear"></div>
		<label class="label_check" for="hardware_lc"><input value="1" type="checkbox" id="hardware_lc" name="hardware_lc" <?php if($hardware_lc) echo ' checked=""';?>><span>Lente da camera partida</span></label> <div class="clear"></div>


	</td>
	</tr>
	</table>
	</div>

	<table width="100%" border=0 cellpadding="2" cellspacing=0 style="margin-top: 10px; margin-bottom: 10px;">
	<tr>
	<td class="preto" width="120">Pré Orçamento:</td>
	<td class="preto" align="left" valign="top">
		<input class="box" id="preorcamento" name="preorcamento" type="text" style="width:120px;text-align:left;" maxlength="255" value="<?php echo ($preorcamento > 0  ? $preorcamento : '');?>">
	</td>
	</tr>
	</table>

	<table width="100%" border=0 cellpadding="2" cellspacing=0 style="margin-top: 10px; margin-bottom: 10px;">
	<tr>
	<td class="preto" width="120">Descrição da Avaria:</td>
	<td class="preto" align="left" valign="top">
		<textarea class="box" id="m_avaria" name="m_avaria" style="width:100%;height:100px;"><?php echo $m_avaria ?></textarea>
	</td>
	</tr>
	</table>

	<table class="rma-equipment-details" width="100%" border=0 cellpadding="2" cellspacing=0 >
	<tr>
	<td class="preto rma-equipment-condition" width="235">

		<table class="rma-equipment-condition-table" width="100%" border=0 cellpadding="2" cellspacing=0 style="margin-top: 10px; margin-bottom: 10px;">
		<tr>
		<td class="preto" width="120">Estado geral do equipamento:</td>
		<td class="preto" align="left" valign="top">
			<select id="m_estado_equipamento" name="m_estado_equipamento" style="width: 100%;" class="chzn-select rma-equipment-condition-select">

			<option value="0" <?php if(!$m_estado_equipamento || $m_estado_equipamento==0) echo ' selected="selected"';?> >Riscos ligeiros</option>
			<option value="1" <?php if($m_estado_equipamento == 1) echo ' selected="selected"';?> >Alguns riscos e mossas</option>
			<option value="2" <?php if($m_estado_equipamento == 2) echo ' selected="selected"';?> >Muitos riscos e mossas</option>
			<option value="3" <?php if($m_estado_equipamento == 3) echo ' selected="selected"';?> >Muito danificado</option>
			<option value="4" <?php if($m_estado_equipamento == 4) echo ' selected="selected"';?> >Sem marca visíveis</option>
			</select>
		</td>
		</tr>
		</table>
	</td>
	<td class="preto" width="220">Acessórios:</td>
	<td class="preto" width="220">Observações internas:</td>
	</tr>
	<tr>
	<td class="preto" width="120" valign="top" style="padding-right: 10px;" data-mobile-label="Descrição do estado">
		<textarea class="box" name="m_estado_equipamento_text" id="m_estado_equipamento_text" style="width:100%;height:100px"><?php echo $m_estado_equipamento_text?></textarea>
	</td>
	<td class="preto" width="120" style="padding-right: 10px;" data-mobile-label="Acessórios">
		<?php if(!$m_acessorios){ $m_acessorios = 'O cliente deixou algum acessorio? Quais?'; ?>
		<textarea class="box" name="m_acessorios" id="m_acessorios" style="width:100%;height:100px" placeholder="<?php echo $m_acessorios?>"></textarea>
		<?php }else{ ?>
		<textarea class="box" name="m_acessorios" id="m_acessorios" style="width:100%;height:100px"><?php echo $m_acessorios?></textarea>
		<?php } ?>
	</td>
	<td class="preto" width="120" data-mobile-label="Observações internas">
		<?php if(!$m_observacoes){
		$m_observacoes='Campo não visivel para o cliente';?>
		<textarea class="box" name="m_observacoes" id="m_observacoes" style="width:100%;height:100px" placeholder="<?php echo $m_observacoes?>"></textarea>
		<?php }else { ?>
		<textarea class="box" name="m_observacoes" id="m_observacoes" style="width:100%;height:100px"><?php echo $m_observacoes?></textarea>
		<?php } ?>
	</td>
	</tr><?


	if($notes)
	{?>
	<tr>
		<td colspan="3" class="preto" style="background-color: #ff0000;color:#fff;padding:30px;text-align:center">O cliente respondeu:<br><?=$notes?></td>
	</tr>
	<tr>
		<td colspan="3" class="preto" style="background-color:#c6f7d0;color:#000;padding:5px;text-align:center">
			<?
			if($lida==0)
			{
				?>MARCAR COMO LIDA: <input onclick="make_lida()" type="checkbox" value="1" name="lida"><?
			}
			else
			{
				?>LIDA<?
			}?>
		</td>
	</tr>
	<?
	}
	?>
	</table>

		</td>
	</tr>
</table>
<script type="text/javascript">
	function make_lida()
	{
		$.ajax({
				type: "GET",
				url: "https://www.dotbright.pt/rmas/rmas/update_lida.php?ID=<?=$ID?>"
			}).done(function(resultado){});
	}
</script>
