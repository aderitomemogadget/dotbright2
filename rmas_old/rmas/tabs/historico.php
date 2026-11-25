<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">
	<table width="100%" cellpadding="5" cellspacing="0" border="1"
		   style="background-color: #FFF; color: black;">
		<thead>
		<th>Utilizador</th>
		<th>Estado</th>
		<th>Data Hora</th>
		</thead>
		<tbody>
		<?php
		$rs3->db_open($db_link,$DB_name,"SELECT rl.*,u.Nome FROM rmas_logs rl INNER JOIN logins u ON (u.ID=rl.login_id) WHERE rl.rma_id=$ID ORDER BY criado DESC");
		if ($rs3->EOF()) {
			?>
			<tr>
				<td colspan="3" align="center">Não existem registos</td>
			</tr>
			<?php
		} else {
			while (!$rs3->EOF()) {
				?>
				<tr>
					<td><?php echo $rs3->db_data['Nome'] ?></td>
					<td>
						<?php
							if(isset($GLOBALS['estados_rmas']))
							{							
								$estados_rmas = estado_encomenda_rmas($rs3->db_data['estado'], 'pt');
			
								echo $estados_rmas['titulo'];				
							}
						?>
					</td>
					<td align="center"><?php echo $rs3->db_data['criado'] ?></td>
				</tr>
				<?php
				$rs3->db_next();
			}
		}
		?>
		</tbody>
	</table>
</td>
</tr>
</table>