<div style="font-size:15px">
<h2>Configure código das categorias:</h2><br>
- Separar por ponto e virgula(;) no caso de ter mais de um código
</div><br>
<?php 
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang; 
		$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($dbh)
		{
			$sql = "
					SELECT 
						products_families.ID, products_families.visivel, products_families.codigo_importacao, Cabecalho 
					FROM 
						products_families 
						INNER JOIN products_families_lang ON products_families_lang.ID_PAI=products_families.ID 
					WHERE 
						ID_ORG='0' 
						AND Lang='$admin_lang' 
					GROUP BY
						products_families.ID
					ORDER BY 
						Ordem ASC,
						Cabecalho ASC
					";
			$result=new DataObj;
			$result2=new DataObj;
			$result->db_open($dbh,$DB_name,$sql);
			if(!$result->EOF())
			{
				$contador=0;
				echo '<table width="100%" border="0" cellpadding=5 cellspacing=0>';
				while (!$result->EOF())
				{
					if($contador == 0) echo '<tr>';
					$codigo = $result->db_data["ID"];
					$nome = $result->db_data["Cabecalho"];
					$visivel = ($result->db_data["visivel"] == 1 ? '<b style="color:#18c26c">Ativo</b>' : '<b style="color:#000">Inativo</b>');
					$codigo_importacao = rtrim(ltrim($result->db_data["codigo_importacao"], ';'), ';');
	
					?>
                    <td style="vertical-align:top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
						<td width="25" height="25" valign="middle">
						<label for="fam_<?php echo $codigo;?>"><input type="text" style="width:100px" value="<?php echo $codigo_importacao;?>" name="fam_<?php echo $codigo;?>" id="fam_<?php echo $codigo;?>" class="chc"> &nbsp; <?php echo ($codigo_importacao!='' ? '<b style="color:#ff0c00">'.$nome.'</b>' : $nome).'-'.$visivel;?></label>
						</td>
					</tr>
                    </table>
					<?php tree_products($codigo)?>
					</td><?
                    $contador++;
					if($contador>2){echo '</tr>';$contador=0;}
					
					
					
					$result->db_next();
				}
				echo '</table>';
			}
			$result->db_close();
		}
		function tree_products($menu_ID=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$admin_lang; 
			$dbh = db_opendb_conection($DB_host,$DB_user,$DB_pass);
			if($dbh && $menu_ID>0)
			{
				$result2 = new DataObj;
				$result3 = new DataObj;
				$sql = "
						SELECT 
							products_families.ID, products_families.visivel, products_families.codigo_importacao, Cabecalho 
						FROM 
							products_families 
							INNER JOIN products_families_lang ON products_families_lang.ID_PAI=products_families.ID 
						WHERE 
							ID_ORG='$menu_ID' 
							AND Lang='$admin_lang'
						GROUP BY
						products_families.ID
						ORDER BY 
							Ordem ASC,
							Cabecalho ASC
						";
				$result2->db_open($dbh,$DB_name,$sql);
				if(!$result2->EOF())
				{
					echo '<table width=100% border=0 cellpadding=0 cellspacing=0><tr>
                    	<td width=10>&nbsp;</td>
                        <td height=15 class=menu_titulo>
						<table width=100% border=0 cellpadding=0 cellspacing=0>';
					while (!$result2->EOF())
					{ 
						$codigo = $result2->db_data["ID"];
						$nome = $result2->db_data["Cabecalho"];
						$codigo_importacao = rtrim(ltrim($result2->db_data["codigo_importacao"], ';'), ';');
						$visivel = ($result2->db_data["visivel"] == 1 ? '<b style="color:#18c26c">Ativo</b>' : '<b style="color:#000">Inativo</b>');
					
						echo '<tr><td width=5></td><td class=menu width="25" height=25>
						
						<label for="fam_'.$codigo.'"><input type="text" value="'.$codigo_importacao.'" name="fam_'.$codigo.'"  style="width:100px" id="fam_'.$codigo.'"  class="chc"> &nbsp; '.($codigo_importacao!='' ? '<b style="color:#18c26c">'.$nome.'</b>' : $nome).' - '.$visivel.'</label>
						
						</td></tr>';
						echo '<tr><td width=5></td><td class=menu>';
						tree_products($codigo);
						echo '</td></tr>';
						
						$result2->db_next();
					}
					echo '</table></td>
                    </tr>
                    </table>';
				}
				$result2->db_close();
			} 
		}?>
	
	
