<?php

if(isset($_GET['tem_email']) && $_GET['tem_email'] == 1)
{
	if(isset($_GET['enviar_email']) && $_GET['enviar_email'] == 1)
	{

		enviaEmail($ID_Cliente,2);
		echo '<script>alert("Email foi enviado com sucesso!");window.open("pagina.php?ID='.$_GET['ID'].'","_self");</script>';
	}
}

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/><input type="hidden" name="Pais" id="Pais" class="box" value="165"/>
<tr><td colspan="2">&nbsp;</td></tr>

<tr>
<td align="left" width="100">Ativo:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><label class="label_check" for="Estado"><input type="checkbox" name="Estado" id="Estado" value="1" <?php if($Estado==1){ echo 'checked="checked"';} ?> /></label></td>
<td width="100">Tipo Cliente:</td>
<td><?php
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
		</select></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="150">Nome:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="150"><input class="box" type="text" name="Nome" id="Nome" value="<?php echo $Nome;?>" style="width:99%"/></td>
<td align="center" width="220">Sobrenome</td>
<td width="150"><input class="box" type="text" name="Sobrenome" id="Sobrenome" value="<?php echo $Sobrenome;?>" style="width:99%"/></td>
<td align="center" width="220">Apelido</td>
<td width="150"><input class="box" type="text" name="Apelido" id="Apelido" value="<?php echo $Apelido;?>" style="width:99%"/></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td align="left">Email(Username):</td>
	<td align="left"><input class="box" type="text" name="Username" id="Username" value="<?php echo $Username;?>" style="width:99%"/></td>
</tr>
<tr>
	<td align="left">Email Real ?:</td>
	<td align="left">
		<label class="label_check" for="tem_email"><input type="checkbox" name="tem_email" id="tem_email" value="1" <?php if($tem_email==1){ echo 'checked="checked"';} ?> /></label>
	</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td align="left">Password:</td>
	<td align="left"><?php echo base64_decode($Password);?> (<a href="pagina.php?ID=<?php echo $_GET['ID']; ?>&enviar_email=1" class="preto" style="font-size:13px;text-decoration:underline">Re-enviar password ao cliente</a>)</td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Empresa:</td>
<td align="left"><input class="box" type="text" name="Empresa" id="Empresa" value="<?php echo $Empresa;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Morada:</td>
<td align="left"><input class="box" type="text" name="Morada" id="Morada" value="<?php echo $Morada;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Cidade:</td>
<td align="left"><input class="box" type="text" name="Cidade" id="Cidade" value="<?php echo $Cidade;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Código Postal:</td>
<td align="left"><input class="box" type="text" name="Codigo_Postal" id="Codigo_Postal" value="<?php echo $Codigo_Postal;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Telefone:</td>
<td align="left"><input class="box" type="text" name="Telefone" id="Telefone" value="<?php echo $Telefone;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Telefone2:</td>
<td align="left"><input class="box" type="text" name="Telefone2" id="Telefone2" value="<?php echo $Telefone2;?>" style="width:99%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Nif:</td>
<td align="left"><input class="box" type="text" name="Nif" id="Nif" value="<?php echo $Nif;?>" style="width:99%"/></td>
</tr>
</table>