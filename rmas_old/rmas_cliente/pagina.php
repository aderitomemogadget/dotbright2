<?php
include "settings.inc.php";
set_time_limit(0);
ini_set('max_execution_time', '9999999999');
ini_set('memory_limit', '2048M');
if($_GET['log'])
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
if($task == 'apagar')
{
	$task_button = write_lang('key_apagar_este_registo', 'Apagar este Registo');
	$task_form = 'apaga';
}
elseif($task == 'duplicar')
{
	$task_button = write_lang('key_duplicar_este_registo', 'Duplicar este Registo');
	$task_form = 'duplicar';
}
elseif($ID == 0)
{
	$task_button = write_lang('key_gravar_o_novo_registo', 'Gravar o Novo Registo');
	$task_form = 'insere';
}
else
{
	$task_button = write_lang('key_gravar_alteracoes', 'Gravar alteraçőes');
	$task_form = 'altera';
}

if(trim($Nivel) == "") $Nivel = 0;
include '../layout/header.inc.php';
include '../layout/body.inc.php';
global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
$rs = new DataObj;
$rs2 = new DataObj;
$rs3 = new DataObj;

if(!$caminho_bt_editor)
{
	$caminho_bt_editor1 = "../../../editor/";
	$caminho_bt_editor2 = "../editor/";
}
else
{
	$caminho_bt_editor1 = $caminho_bt_editor2 = $caminho_bt_editor;
}

$C = '';
$T = '';
$ordem = 0;

if($ID != 0)
{
	$rs->db_open($db_link, $DB_name, "SELECT * FROM rmas_cliente WHERE id=$ID");
	if($rs)
	{
		$IDb = $rs->db_data['id'];
		$cliente_id = $rs->db_data['cliente_id'];
		$nome = $rs->db_data['nome'];
		$apelido = $rs->db_data['apelido'];
		$empresa = $rs->db_data['empresa'];
		$nif = $rs->db_data['nif'];
		$telefone = $rs->db_data['telefone'];
		$email = $rs->db_data['email'];
		$morada = $rs->db_data['morada'];
		$codp = $rs->db_data['codp'];
		$nenc = $rs->db_data['nenc'];
		$nfat = $rs->db_data['nfat'];
		$obs = $rs->db_data['obs'];
		$obsint = $rs->db_data['obsint'];
		$obscli = $rs->db_data['obscli'];
		$estado = $rs->db_data['estado'];
		$criado = $rs->db_data['criado'];
		$alterado = $rs->db_data['alterado'];
	}
	$rs->db_close();
}
?>
<script language=JavaScript src='../editor/scripts/innovaeditor.js'></script>
<script language="JavaScript">
	if(document.getElementById)
	{
		document.write('<style type="text/css">\n');
		document.write('.submenu{display: none;}\n');
		document.write('</style>\n');
	}
	function showmenu_prod(obj, obj2)
	{
		document.form.tab_a.value = obj;
		document.form.tab_b.value = obj2;
		if(document.getElementById)
		{
			var el = document.getElementById(obj);
			var el2 = document.getElementById(obj2);
			var ar = document.getElementById("masterdiv").getElementsByTagName("div");
			var ar2 = document.getElementById("masterdiv").getElementsByTagName("td");
			if(el.style.display != "block")
			{
				for(var i = 0; i < ar.length; i++)
				{
					v = "sub" + (i + 1);
					if(ar[i].className == "submenu") ar[i].style.display = "none";
				}
				for(var i = 0; i < ar2.length; i++)
				{
					v = "sub" + (i + 1);
					if(ar2[i].className == "branco") ar2[i].style.background = "#5694B4";
				}
				el.style.display = "block";
				el2.style.background = "#000000";
			}
		}
	}
	window.onload = function()
	{
		<? if($t1 && $t2){?>showmenu_prod('<?=$t1?>', '<?=$t2?>');
		<? }else{?>showmenu_prod('sub1', 'pinta1');<? }?>}
	$(function()
	{
		$('.result').hide();
	});
	function get_cs_user()
	{
		var t = $('.result').is(':visible');
		if(t == false)
		{
			$('#choose').html('<img src="del2.png" border="0" title="close box" />');
			$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>', function(data)
			{
				$('.result').show("slow");
				$('.result').html(data);
			});
		}
		else
		{
			$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
			$('.result').hide("slow");
		}
	}
	function totext(div)
	{
		$('#m_cliente_url').val($('#' + div).val());
		$('#cor').val($('#_' + div).val());
		$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
		$('.result').hide("slow");
	}
	function search()
	{
		$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>&pesquisa=' + $('#search_by').val(), function(data)
		{
			$('.result').html(data);
		});
	}
	function empty()
	{
		$('#m_cliente_url').val("");
		$('#cor').val(0);
		$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
		$('.result').hide("slow");
	}
	function getCliente(tipo, valor)
	{
		$.get('getCliente.php?' + tipo + '=' + valor, function(data)
		{
			if(data.trim() != '')
			{
				var array_cli = data.split('&;');
				if(array_cli.length > 0)
				{
					$('#ID_Cliente').val(array_cli[0]).attr('title', array_cli[0]);
					$('#cliente_nome').val(array_cli[1]).attr('title', array_cli[1]);
					$('#cliente_apelido').val(array_cli[2]).attr('title', array_cli[2]);
					$('#cliente_username').val(array_cli[3]).attr('title', array_cli[3]);
					$('#cliente_morada').val(array_cli[7]).attr('title', array_cli[7]);
					$('#cliente_cp').val(array_cli[8]).attr('title', array_cli[8]);
					$('#cliente_telefone').val(array_cli[4]).attr('title', array_cli[4]);
					$('#cliente_empresa').val(array_cli[5]).attr('title', array_cli[5]);
				}
				$('#dados_cliente').show();
			}
		});
	}
	function getProduto(valor, linha)
	{
		var PVP = 0;
		$.get('getProduto.php?ref=' + valor + '&PVP=' + PVP, function(data)
		{
			if(data != '')
			{
				var array_cli = data.split('&;');
				if(array_cli.length > 0)
				{
					$('input[name="desc[]"]').eq(linha).val(array_cli[1]);
					$('input[name="prec[]"]').eq(linha).val(array_cli[2]);
				}
			}
		});
	}
	var n = 1;
	function copiaEquipamentoRMA()
	{
		n++;
		var new_line = $('#linha-rma').clone().html();
		var search = '_1';
		var replace = '_' + n.toString();
		var res = new_line.replace(new RegExp(search, 'g'), replace);
		$('#rmas-cliente').append('<tr>' + res + '</tr>');
	}
</script>
<form name="form" method="post" action="pagina_trata.php?ID=<?=$ID?>&task_form=<?=$task_form?>&d=<?=uniqID('')?>"
      enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
	<input type="hidden" name="slashes" value="'">
	<input type="hidden" name="tab_a" value="0">
	<input type="hidden" name="tab_b" value="0">
	<input type="hidden" name="ID" value="<?=$ID?>">

	<div class="center">
		<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
					<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
						<tr>
							<td valign="top">
								<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5"
								       class="titulo_janela">
									<tr>
										<td></td>
									</tr>
								</table>
								<div id="masterdiv" style="background-color: rgb(246, 246, 246);"><br/>

									<style>
										/*
										.rmas-cliente input, .rmas-cliente select {
											width: calc(100% - 35px);
											padding: 5px;
											margin-bottom: 5px;
										}

										.rmas-cliente select {
											height: 30px;
										}

										.rmas-cliente2 input {
											width: calc(100% - 20px);
											padding: 5px;
											margin-bottom: 5px;
										}
										*/
									</style>
									<div style="position:relative; width: 100%; display:block;" class="submenu" id="sub1">
										<input type="hidden" name="cliente_id" value="<?=$id?>">

										<table width="100%" cellpadding="5" cellspacing="0" border="0" class="rmas-cliente">
											<tr>
												<td class="preto" width="120">Tracking:</td>
												<td class="preto" align="left" valign="top">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td width="100">
																<input type="text" readonly value="<?php echo "RMA".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT); ?>" maxlength="255">
															</td>
															<td class="preto" align="right" width="100">Estado:&nbsp;&nbsp;&nbsp;</td>
															<td width="100">
																<select name="m_estado">
																	<?php options_estado($estado, 1); ?>
																</select>
															</td>
															<td class="preto">
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="envio_email" value="1" type="checkbox">&nbsp;&nbsp;&nbsp;Envio
																de e-mail ao Cliente
															</td>
														</tr>
													</table>

												</td>
											</tr>
											<tr>
												<td class="preto" width="120">NIF:</td>
												<td class="preto" align="left" valign="top">
													<input name="m_nif" value="<?=$nif?>" onchange="getCliente('nif',this.value)" style="WIDTH:150px; HEIGHT:20px" maxlength="255">
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Nome:</td>
												<td class="preto" align="left" valign="top">
													<table border="0" cellpadding="0" cellspacing="0">
														<tbody>
														<tr>
															<td>
																<input name="m_nome" id="cliente_nome" style="WIDTH:150px; HEIGHT:20px" name="m_nome" value="<?=$nome?>">
															</td>
															<td class="preto" align="right" width="98">Apelido:&nbsp;&nbsp;&nbsp;</td>
															<td>
																<input style="WIDTH:150px; HEIGHT:20px" id="cliente_apelido" name="m_apelido" value="<?=$apelido?>">
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Morada:</td>
												<td class="preto" align="left" valign="top">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td>
																<input name="m_morada" value="<?=$morada?>" id="cliente_morada" style="WIDTH:400px; HEIGHT:20px" maxlength="255">
															</td>
															<td class="preto" align="right" width="98">Código Postal:&nbsp;&nbsp;&nbsp;</td>
															<td>
																<input style="WIDTH:150px; HEIGHT:20px" id="cliente_cp" name="m_codp" value="<?=$codp?>">
															</td>
														</tr>
													</table>

												</td>
											</tr>
											<tr>
												<td class="preto" width="120">E-mail:</td>
												<td class="preto" align="left" valign="top">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td>
																<input name="m_email" value="<?=$email?>" id="cliente_username" onchange="getCliente('email',this.value)" style="WIDTH:400px; HEIGHT:20px" maxlength="255">
															</td>
															<td class="preto" align="right" width="98">Telefone:&nbsp;&nbsp;&nbsp;</td>
															<td>
																<input name="m_telefone" value="<?=$telefone?>" id="cliente_telefone" onchange="getCliente('telef',this.value)" style="WIDTH:150px; HEIGHT:20px" maxlength="255">
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Empresa:</td>
												<td class="preto" align="left" valign="top">
													<input name="m_empresa" maxlength="255" value="<?=$empresa?>" id="cliente_empresa" style="WIDTH:400px; HEIGHT:20px" maxlength="255">
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Nº Fatura:</td>
												<td class="preto" align="left" valign="top">
													<table border="0" cellpadding="0" cellspacing="0">
														<tbody>
														<tr>
															<td>
																<input style="WIDTH:150px; HEIGHT:20px" name="m_nfat" value="<?=$nfat?>">
															</td>
															<td class="preto" align="right" width="98">Nº Encomenda:&nbsp;&nbsp;&nbsp;</td>
															<td>
																<input style="WIDTH:150px; HEIGHT:20px" name="m_nenc" value="<?=$nenc?>">
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Observações:</td>
												<td class="preto" align="left" valign="top">
													<textarea name="m_obs" id="m_obs" style="height:100px; width: 90%; border-color:#5694B4"><?=$obs?></textarea>
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Observações internas:</td>
												<td class="preto" align="left" valign="top">
													<textarea name="m_obsint" id="m_obsint" style="height:100px; width: 90%; border-color:#5694B4"><?=$obsint?></textarea>
												</td>
											</tr>
											<tr>
												<td class="preto" width="120">Observações cliente:</td>
												<td class="preto" align="left" valign="top">
													<textarea name="m_obscli" id="m_obscli" style="height:100px; width: 90%; border-color:#5694B4"><?=$obscli?></textarea>
												</td>
											</tr>

											<tr>
												<td colspan="40">
													<style type="text/css">
														.input-reparacao {
															background: #FFF;
															border: none !important;
															width: 95% !important;
															text-align: center;
														}
													</style>
													<script>
														function removeLinha(value) {
															$('#tabela-reparacao tr').eq(value + 1).remove();
															reordenaLinhasTabela();
														}
														function adicionaLinha() {

															$('#tabela-reparacao tr:last').after('<tr>' + $('#tabela-reparacao tr:last').html() + '</tr>');
															reordenaLinhasTabela();
															return false;
														}
														function reordenaLinhasTabela() {
															$('img[id="linha-reparacao"]').each(function (index) {
																$(this).attr('onclick', 'removeLinha(' + index + ')');
																$('input[name="ref[]"]').eq(index).attr('onkeyup', 'getProduto(this.value,' + index + ')');
															});
														}

													</script>
													<br><br>
													<table width="100%" border="1" id="tabela-reparacao">
														<tr>
															<th align="center" width="40">Qtd.</th>
															<th align="center" width="70">Referencia</th>
															<th align="center" width="400">Descrição</th>
															<th align="center" width="30">Acção</th>
														</tr>
														<?php
														$rs->db_open($db_link, $DB_name, 'SELECT * FROM rmas_cliente_linha WHERE rmas_cliente_id = '.$IDb.' ORDER BY ordem');
														//print_r($rs);
														$lin = 0;
														$total = 0;
														while (!$rs->EOF())
														{
															?>
															<tr>
																<td>
																	<input class="input-reparacao" type="text" name="qtd[]" value="<?php echo $rs->db_data['qtd']; ?>">
																</td>
																<td>
																	<input onkeyup="getProduto(this.value,<?php echo $lin; ?>)" class="input-reparacao" type="text" name="ref[]" value="<?php echo $rs->db_data['ref']; ?>">
																</td>
																<td>
																	<input style="text-align: left;" class="input-reparacao" type="text" name="desc[]" value="<?php echo $rs->db_data['descricao']; ?>">
																</td>
																<td align="center">
																	<img id="linha-reparacao" src="del2.png" border=0 onclick="removeLinha(<?php echo $lin; ?>)">
																</td>
															</tr>
															<?
															$lin++;
															$rs->db_next();
														}
														$rs->db_close();

														?>
														<tr>
															<td>
																<input class="input-reparacao" type="text" name="qtd[]" value="">
															</td>
															<td>
																<input onkeyup="getProduto(this.value,<?php echo $lin; ?>)" class="input-reparacao" type="text" name="ref[]" value="">
															</td>
															<td>
																<input style="text-align: left;" class="input-reparacao" type="text" name="desc[]" value="">
															</td>
															<td align="center">
																<img id="linha-reparacao" src="del2.png" border=0 onclick="removeLinha(<?php echo $lin; ?>)">
															</td>
														</tr>
													</table>
													<br/><br/>
													<a href="#" onclick="adicionaLinha(); return false;">Adicionar Linha</a><br/>
												</td>
											</tr>
										</table>
										<?php
										/*
										<div class="botao" id="mais-1" style="padding: 2px 15px; float:right; margin:15px; line-height: 20px" onclick="copiaEquipamentoRMA()">Acrescentar mais um equipamento</div>
										*/
										?>
										<div style="clear:both"></div>
									</div>

							</td>
						</tr>
					</table>
					<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td></td>
						</tr>
					</table>
					<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td>&nbsp;<input class="botao" type="submit" name="Submit" value="<?=$task_button;?>">
								<input class=botao type="button" name="cancelar" value=" Cancelar "
								       onMouseUp="window.open('list.php','_self')"/>
						</tr>
					</table>
					<img src="../images/bsh.png" width="890px">
				</td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200px"><? show_fast_tools(); ?><br/></td>
			</tr>
		</table>
	</div>
</form>
</body>
</html>