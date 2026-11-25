<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
	<tr>
		<td valign="top">
			<table width="100%" border=0 cellpadding="2" cellspacing=0>
				<tr> <td><br></td></tr>
				<tr>
					<td class="preto" width="120">Imei</td>
					<? //<td><input type="text" id="imei_emp" name="imei_emp" placeholder="Imei" value="'.$m_imei_emp.'"></td> ?>
					<td class="preto">
						<select name="imei_emp" id="imei_emp" style="width:150px">
							<option value="">----------</option>
							<?php
							$rs = new DataObj;
							$sql = 'SELECT imei FROM telemoveis_emp';
							$rs->db_open($db_link, $DB_name, $sql);
							while (!$rs->EOF()){
								$imei = $rs->db_data["imei"];
								?><option value="<?php echo $imei; ?>" <?php if ($m_imei_emp == $imei) echo 'selected="selected"';?> ><?php echo $imei?></option><?php
								$rs->db_next();
							}
							$rs->db_close();
							?>
						</select>

						<script>
							$(document).ready(function() {
								$('#imei_emp').on('change', function() {
									var selectedImei = $(this).val();
									if (!selectedImei) {
										$('#marca_emp, #modelo_emp').val('');
										$('#estado_emp').val('1');
										return;
									}

									$.ajax({
										type: 'POST',
										url: "<?php echo $GLOBALS['site_http_url'].'rmas/rmas/ajax_select_emp.php'; ?>",
										data: { imei: selectedImei },
										success: function(response) {
											var data = JSON.parse(response);

											$('#marca_emp').val(data.marca || '');
											$('#modelo_emp').val(data.modelo || '');
										}
									});
								});
							});
						</script>
					</td>
				</tr>
				<tr> <td><br></td></tr>
				<tr>
					<td class="preto" width="120">Marca</td>
					<td class="preto"><input type="text" id="marca_emp" name="marca_emp" placeholder="Marca" value="<? echo $m_marca_emp ?>" readonly></td>					
				</tr>
				<tr> <td><br></td></tr>
				<tr>
					<td class="preto" width="120">Modelo</td>
					<td class="preto"><input type="text" id="modelo_emp" name="modelo_emp" placeholder="Modelo" value="<? echo $m_modelo_emp; ?>" readonly></td>				
				</tr>
				<tr> <td><br></td></tr>
				<tr>
					<td class="preto" width="120">Estado</td>
					<td class="preto">
						<select name="estado_emp" id="estado_emp">
							<option value="1" <? echo ($m_estado_emp == 1 ? ' selected' : ''); ?>>Não foi entregue</option>
							<option value="2" <? echo ($m_estado_emp == 2 ? ' selected' : ''); ?>>Entregue</option>
							<option value="3" <? echo ($m_estado_emp == 3 ? ' selected' : ''); ?>>Devolvido</option>
						</select>
					</td>
				</tr>
				<tr> <td><br><br></td></tr>
			</table>
		</td>
	</tr>
</table>
