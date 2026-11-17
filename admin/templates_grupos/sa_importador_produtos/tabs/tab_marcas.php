<div style="font-size:15px">
<h2>Configure código das marcas:</h2><br>
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
		echo '<table width=100% border=0 cellpadding=5 cellspacing=0>';
		while (!$result->EOF())
		{
			if($contador == 0) echo '<tr>';
			$codigo = $result->db_data["ID"];
			$nome = $result->db_data["Cabecalho"];
			$Visivel = ($result->db_data["Visivel"] == 1 ? '<b style="color:#18c26c">Ativo</b>' : '<b style="color:#000">Inativo</b>');
			$codigo_importacao = rtrim(ltrim($result->db_data["codigo_importacao"], ';'), ';');

			?>
			<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="25" height="25" valign="middle">
				<label for="marca_<?php echo $codigo;?>"><input type="text" value="<?php echo $codigo_importacao;?>" name="marca_<?php echo $codigo;?>" id="marca_<?php echo $codigo;?>" class="chc"> &nbsp; <?php echo ($codigo_importacao!='' ? '<b style="color:#18c26c">'.$nome.'</b>' : $nome).' - '.$Visivel;?></label>
				</td>
			</tr>
			</table>
		
			</td><?
			$contador++;
			if($contador>2){echo '</tr>';$contador=0;}
			$result->db_next();
		}
		echo '</table>';
	}
	$result->db_close();
}