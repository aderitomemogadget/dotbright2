<style>
TABLE.p1 td {padding:0px}
label {margin-bottom:0px !important}
</style>
<table width="100%" border="1" cellpadding=5 cellspacing=0>
	<tr>
		<td style="vertical-align:top">
	        <table width="100%" border="0" cellpadding="1" cellspacing="0">
		        <tr>
					<td width="25"  height="25" valign="middle">
						<input class="form-control" style="max-width:100px;float:left" type="checkbox" style="width:100px" value="1"name="marcas_atraves_nome" id="marcas_atraves_nome;?>" class="chc" <?php echo ($marcas_atraves_nome == 1 ? 'checked="checked"' :'');?>>
					</td>
					<td align=left>
						<span style="line-height:25px; padding-left: 10px">
							<b style="color:#ff0c00">Associar automaticamente as marcas através do nome</b>
						</span>
					</td>
				</tr>
				 <tr>
					<td height="25" valign="middle">
						<input class="form-control" style="max-width:100px;float:left" type="checkbox" style="width:100px" value="1" name="cria_marcas_novas" id="cria_marcas_novas;?>" class="chc" <?php echo ($cria_marcas_novas == 1 ? 'checked="checked"' :'');?>>

					</td><td align=left >
						<span style="line-height:25px; padding-left: 10px">
							<b style="color:#ff0c00">Criar automaticamente marcas novas</b>
						</span>
					</td>
				</tr>
	        </table>
	    </td>
   </tr>
</table>
<br><br><? if(!$marcas_atraves_nome){?>
	<div style="font-size:15px">
	<h2>Configure código das marcas que vem no ficheiro caso a associação não for feita pelo nome:</h2><br>
	- Separar por ponto e virgula(;) no caso de ter mais de um código
	</div><br>
	<?php
	global $DB_host, $DB_user, $DB_pass, $DB_name, $admin_lang;
	$dbh = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	if($dbh)
	{
		$sql = "
				SELECT
					brands.ID, brands.Visivel, brands.codigo_importacao, Nome as Cabecalho
				FROM
					brands
					INNER JOIN brands_lang ON brands_lang.ID_PAI=brands.ID
				WHERE
					Lang='$admin_lang'
				GROUP BY
					brands.ID
				ORDER BY
					Ordem ASC,
					Nome ASC
				";
		$result=new DataObj;
		$result->db_open($dbh,$DB_name,$sql);
		if(!$result->EOF())
		{
			$contador=0;
			echo '<table width=100% border=0 cellpadding=1 cellspacing=0>';
			while (!$result->EOF())
			{
				if($contador == 0) echo '<tr>';
				$codigo = $result->db_data["ID"];
				$nome = $result->db_data["Cabecalho"];
				$Visivel = ($result->db_data["Visivel"] == 1 ? '<b style="color:#18c26c">Ativo</b>' : '<b style="color:#000">Inativo</b>');

				$rs3=new DataObj;

				$codigo_importacao = '';
				$sql = 'SELECT * from a_importador_brands WHERE ID_FAM = '.$codigo.' AND conf_id = '.$Supplier_ID;
				$rs3->db_open($dbh,$DB_name,$sql);
				if(!$rs3->EOF())
				{
					$codigo_importacao = rtrim(ltrim($rs3->db_data["Codigo"], ';'), ';');
				}
				$rs3->db_close();

				//$codigo_importacao = rtrim(ltrim($result->db_data["codigo_importacao"], ';'), ';');

				?>
				<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="25" height="25" valign="middle">
						<label for="marca_<?php echo $codigo;?>">
							<input class="form-control" style="max-width: 100px; float: left;" type="text" value="<?php echo $codigo_importacao;?>" name="marca_<?php echo $codigo;?>" id="marca_<?php echo $codigo;?>" class="chc">
							&nbsp;
							<span style="line-height: 25px;">
								<?php echo ($codigo_importacao!='' ? '<b style="color:#18c26c">'.$nome.'</b>' : $nome).' - '.$Visivel;?>
							</span>
						</label>
					</td>
				</tr>
				</table>

				</td><?
				$contador++;
				if($contador>3){echo '</tr>';$contador=0;}
				$result->db_next();
			}
			echo '</table>';
		}
		$result->db_close();
	}

}