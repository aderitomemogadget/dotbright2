<?php
require_once "settings.inc.php";
?><link rel="stylesheet" type="text/css" href="../admin_estilos.css" /><link rel="stylesheet" type="text/css" href="sa.css?d=1" /><?php
global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs = new DataObj;
$sql_renova = 
			'
			SELECT 
				CLASS,
				N_ORDEM_PORT,
				IDENTIFICACAO,
				N_BASE
			FROM 
				sa_classes
			WHERE
				VISIVEL=1
			ORDER BY 
				CLASS ASC
			';
$rs->db_open($db_link, $DB_name, $sql_renova);
if(!$rs->EOF() || $rs->EOF())
{
	?><table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>						
	<td align="left">Classe</td>
	<td align="left">Número</td>
	<td>Identificação</td>
	</tr>
	<?php
	while(!$rs->EOF())
	{			
		$CLASS = $rs->db_data['CLASS'];			
		$N_ORDEM_PORT = $rs->db_data['N_ORDEM_PORT'];
		$IDENTIFICACAO = $rs->db_data['IDENTIFICACAO'];
		$N_BASE = $rs->db_data['N_BASE'];
		?>
		<tr>						
		<td align="left"><?php echo $CLASS?></td>
		<td align="left"><?php echo $N_ORDEM_PORT?></td>
		<td><?php echo $IDENTIFICACAO?></td>
		</tr>				
		<?php
		$rs->db_next();
	}
	?></table><?php
}
$rs->db_close();	
?>
