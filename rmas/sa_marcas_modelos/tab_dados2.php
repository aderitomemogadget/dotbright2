<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/><input type="hidden" name="Pais" id="Pais" class="box" value="165"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Ordem:</td>
<td align="left"><input class="box" type="text" name="Ordem" id="Ordem" value="<?php echo $ordem;?>" style="width:100px"/></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Marca:</td>
<td align="left"><select id="marca_id" name="marca_id" class="chzn-select" style="width:600px">
								<option>Associar Marca ao Modelo</option>
									<?php
									$sql = 'SELECT ID, marca FROM marcas_rmas';
									$db_sel = new DataObj();
									$db_sel->db_open($db_link,$DB_name,$sql);
									while(!$db_sel->EOF())
									{
										$marca_i = $db_sel->db_data['ID'];
										$nome = $db_sel->db_data['marca'];
										?>
										<option value="<?php echo $marca_i?>"<?php if ($marca_i == $marca_id)echo 'selected="selected"';?> ><?php echo $nome?></option>
										<?
										$db_sel->db_next();
									}
									$db_sel->db_close();
									?>
								</select></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Modelo:</td>
<td align="left"><input class="box" type="text" name="modelo" id="modelo" value="<?php echo $modelo;?>" style="width:99%"/></td>
</tr>

</table>