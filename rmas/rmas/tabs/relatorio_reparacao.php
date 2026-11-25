<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
<tr>
	<td valign="top">
		<table width="100%" border=0 cellpadding="2" cellspacing=0>
		<tr>
			<td class="preto">Orçamento:
				<textarea name="m_observacoestec" id="m_observacoestec" style="WIDTH:100%; HEIGHT:100px" class="box"><?php echo $m_observacoestec; ?></textarea>
			</td>
		</tr>
			<tr>
				<td class="preto">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td>
					VALOR:
					</td>
					<td width="120" class="preto" align="center"><input class="box" type="text" name="m_valor" id="m_valor" style="WIDTH:100px; HEIGHT:25px" value="<?php echo number_format($m_valor,2)?>"></td>
					<td>
					<label class="label_check" for="envio_email_orcamento"><input type="checkbox" id="envio_email_orcamento" name="envio_email_orcamento" value="1" <?=!empty($rma_repair_order_id)?'disabled':''?>> <?=!empty($rma_repair_order_id)?'Email de orçamento RRE desativado':'enviar orçamento ao cliente por email'?></label>
					</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr><td><br></td></tr>
			<tr>
				<td class="preto">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="180">
					Localização do equipamento:
					</td>
					<td width="200" class="preto" align="center">
					<select id="sel_local" name="sel_local" class="chzn-select" style="width: 200px;">
						<option value="">----------</option>
							<?
							$sql = 'SELECT zone_name FROM localizacoes_lang';
							$db_sel = new DataObj();
							$db_sel->db_open($db_link,$DB_name,$sql);
							while(!$db_sel->EOF()) {
								$zone_name=$db_sel->db_data['zone_name'];
								?>
								<option value="<?php echo $zone_name?>"<?php if ($zone == $zone_name) echo 'selected="selected"';?> ><?php echo $zone_name?></option>
								<?
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
			<tr><td><br></td></tr>
			<tr>
				<td class="preto titulo"><b>Descrição do técnico:</b></td>
			</tr>
			<tr>
				<td class="preto">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td>
				<select id="m_tecnico" name="m_tecnico" class="chzn-select" style="width: 300px;" onchange="$('#m_observacoestec_relatorio').val($('#m_observacoestec_relatorio').val() ? $('#m_observacoestec_relatorio').val()+', '+$('option:selected', this).attr('title') : $('option:selected', this).attr('title')); $('#m_tecnico option:first').attr('selected','selected');$('#m_tecnico').trigger('liszt:updated');$('#m_tecnico').trigger('chosen:updated');">
				<option value="">-</option>
				<?php show_rmas_descricao_tecnico($m_tecnico); ?>
				
				<?php /* <option value="0" <?php echo (!isset($m_tecnico) || $m_tecnico == 0 ? 'selected="selected"' : '');?>>Actualização de software</option>
				<option value="1" <?php echo ($m_tecnico == 1 ? 'selected="selected"' : '');?>>Desbloqueio</option>
				<option value="2" <?php echo ($m_tecnico == 2 ? 'selected="selected"' : '');?>>Remoção de conta iCloud</option>
				<option value="3" <?php echo ($m_tecnico == 3 ? 'selected="selected"' : '');?>>Remoção de conta Google</option>
				<option value="4" <?php echo ($m_tecnico == 4 ? 'selected="selected"' : '');?>>Reparação de circuito de memória</option>
				<option value="5" <?php echo ($m_tecnico == 5 ? 'selected="selected"' : '');?>>Substituição do módulo frontal (Touch+LCD)</option>
				<option value="6" <?php echo ($m_tecnico == 6 ? 'selected="selected"' : '');?>>Substituição do Touch</option>
				<option value="7" <?php echo ($m_tecnico == 7 ? 'selected="selected"' : '');?>>Substituição do LCD</option>
				<option value="8" <?php echo ($m_tecnico == 8 ? 'selected="selected"' : '');?>>Reparação do circuito de áudio</option>
				<option value="9" <?php echo ($m_tecnico == 9 ? 'selected="selected"' : '');?>>Reparação do circuito de alimentação</option>
				<option value="10" <?php echo ($m_tecnico == 10 ? 'selected="selected"' : '');?>>Reparação do circuito de rádio frequência</option>
				<option value="11" <?php echo ($m_tecnico == 11 ? 'selected="selected"' : '');?>>Reparação do circuito de WIFI/bluetooth</option>
				<option value="12" <?php echo ($m_tecnico == 12 ? 'selected="selected"' : '');?>>Substituição de bateria</option>
				<option value="13" <?php echo ($m_tecnico == 13 ? 'selected="selected"' : '');?>>Substituição da Board</option>
				<option value="14" <?php echo ($m_tecnico == 14 ? 'selected="selected"' : '');?>>Substituição do equipamento</option>*/?>
				</select> 
				</td>
				<td width="120" class="preto" align="center">Novo IMEI</td>
				<td>
				<input class="box" type="text" id="m_novo_imei" name="m_novo_imei" value="<?php echo $m_novo_imei?>" style="width:150px">
				</td>
				</tr>
				</table>
				<textarea class="box" name="m_observacoestec_relatorio" id="m_observacoestec_relatorio" style="WIDTH:100%; HEIGHT:200px"><?php echo $m_observacoestec_relatorio; ?></textarea>

				</td>
			</tr>
			<tr><td><br></td></tr>
			<tr><td><?php include 'tab_imagens.php'; ?></td></tr>
			<tr><td><br></td></tr>
			<tr>
				<td class="preto">Observações Internas (invisivel para o cliente): </td>
			</tr>
			<tr>

				<td class="preto" align="left" valign="top">
					<textarea class="box" name="m_obsint" id="m_obsint" style="width:100%;height:100px;" ><?php echo $m_obsint ?></textarea><? /* show_editor("m_acessorios","m_acessorios",780,150) */ ?>
				</td>
			</tr>
			<tr>
				<td class="preto">

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
							if (value == 0) {

							}
							else{
								$('#tabela-reparacao tr').eq(value + 1).remove();
							reordenaLinhasTabela();
							}
							
						}
						function adicionaLinha() {

							$('#tabela-reparacao tr:last').after('<tr>' + $('#tabela-reparacao tr:last').html() + '</tr>');
							reordenaLinhasTabela();
							return false;
						}
						function reordenaLinhasTabela() {
							$('img[id="linha-reparacao"]').each(function (index) {
								$(this).attr('onclick','removeLinha(' + index + ')');
								$('input[name="ref[]"]').eq(index).attr('onkeyup','getProduto(this.value,' + index + ')');
							});
						}

						function actualizaTotais() {
							var totalsIVA=0;
							$('input[name="prec[]"]').each(function (index) {
								if (!isNaN($(this).val()) && $(this).val() != '') {
									$('input[name="qtd[]"]').eq(index).val()
									totalsIVA += $('input[name="qtd[]"]').eq(index).val() * parseFloat($(this).val());
								}
							});
							$('#totalsiva-reparacao').html('€ ' + totalsIVA.toFixed(2));
							$('#totalciva-reparacao').html('€ ' + (totalsIVA * 1.23).toFixed(2));
							return false;
						}
					</script>
					<br>

					Inventário de Peças :
					<!-- <label>
						<input type="checkbox" id="pvp_reven" value="1"> PVP Revendedor
					</label> -->
					<br><br>
					<table width="100%" border="1" id="tabela-reparacao">
						<tr>
							<th align="center" width="40">Qtd.</th>
							<th align="center" width="70">Referencia</th>
							<th align="center" width="400">Descrição</th>
							<!-- <th align="center" width="50">Preço S/IVA</th>
							<th align="center" width="100">Disponibilidade</th> -->
							<th align="center" width="30">Acção</th>
						</tr>
						<?php
						$rs->db_open($db_link,$DB_name,'SELECT * FROM rmas_linhas WHERE ID_ORG="'.$IDb.'" ORDER BY ID ASC');
						//print_r($rs);
						$lin=0;
						
						while (!$rs->EOF()) {
							?>
							<tr>
								<td>
									<input class="input-reparacao box" type="text" name="qtd[]" value="<?php echo $rs->db_data['Quantidade']; ?>">
								</td>
								<td>
									<input onkeyup="getProduto(this.value,<?php echo $lin; ?>)" class="input-reparacao box" type="text" name="ref[]" value="<?php echo $rs->db_data['Ref']; ?>">
								</td>
								<td>
									<input style="text-align: left;width:100%" class="input-reparacao box" type="text" name="desc[]" value="<?php echo $rs->db_data['Cabecalho']; ?>">
								</td><?php /*
								<td>
									<input class="input-reparacao" type="text" name="prec[]" value="<?php //echo $rs->db_data['Preco']; ?>">
								</td>
								<td>
									<input class="input-reparacao" type="text" name="disp[]" value="<?php // echo $rs->db_data['Disponibilidade']; ?>">
								</td> */?>
								<td align="center">
									<img id="linha-reparacao" src="del2.png" border="0" onclick="removeLinha(<?php echo $lin; ?>)" style="cursor:pointer" />
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
								<input class="input-reparacao box" type="text" name="qtd[]" value="">
							</td>
							<td>
								<input onkeyup="getProduto(this.value,<?php echo $lin; ?>)" class="input-reparacao box" type="text" name="ref[]" value="">
							</td>
							<td>
								<input style="text-align: left;width:100%" class="input-reparacao box" type="text" name="desc[]" value="">
							</td><!--
							<td>
								<input class="input-reparacao" type="text" name="prec[]" value="">
							</td>
							<td>
								<input class="input-reparacao" type="text" name="disp[]" value="">
							</td> -->
							<td align="center">
								<img id="linha-reparacao" src="del2.png" border=0 onclick="removeLinha(<?php echo $lin; ?>)"  style="cursor:pointer" />
							</td>
						</tr>
					</table>
					<br/><br/>
					<a href="#" onclick="adicionaLinha(); return false;">Adicionar Linha [+]</a><br/>
					<table width="100%" border="0">
						<tr>
							<td align="right">
							<table border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td>
							<label class="label_check" for="m_aceite"><input value="1" type="checkbox" name="m_aceite" id="m_aceite" <? if($m_aceite==1) echo ' checked=""';?> ><b>Não aceite</b> </label>
							</td>
							<td>
							<label class="label_check" for="m_pago"><input value="1" type="checkbox" name="m_pago" id="m_pago" <? if($m_pago==1) echo ' checked=""';?> ><b>Não pago</b> </label>
							</td>
							<td width="120" class="preto" align="center" style="font-size:22px">Total:</td>
							<td>
							<input class="box" type="text" id="total" name="total" value="<?php echo number_format($total,2);?>" style="font-size: 32px;width: 150px;height:42px !important">
							</td>
							</tr>
							</table>
		
							</td>
						</tr>
					</table>
				</td>
			</tr>

		</table>
	</td>
</tr>
</table>
