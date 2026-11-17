<?php
include "settings.inc.php";

	error_reporting(E_ERROR);
	ini_set('display_errors', 1);
$keyword = trim($_GET['keyword']);

if($keyword != '')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;

	
	$cond = '';
	$cond .= " Nif LIKE '%".$keyword."%'";
	$cond .= " OR Username LIKE '%".$keyword."%'";
	$cond .= " OR Telefone LIKE '%".$keyword."%'";
	$cond .= " OR Telefone2 LIKE '%".$keyword."%'";
	$cond .= " OR Nome LIKE '%".$keyword."%'";
	$cond .= " OR Sobrenome LIKE '%".$keyword."%'";
	$cond .= " OR Apelido LIKE '%".$keyword."%'";
	
	$sql = "SELECT * FROM clientes WHERE 1=1 AND (".$cond.") GROUP BY Username DESC LIMIT 20";
	// echo $sql;
	$rs->db_open($db_link,$DB_name, $sql);
	if(!$rs->EOF())
	{ 
		?><ul id="country-list"><?php
		while(!$rs->EOF())
		{
			?><li style="position:relative" onclick="selectClient('<?php echo addslashes($rs->db_data['ID']).'[split]'.addslashes($rs->db_data['Nome']).'[split]'.addslashes($rs->db_data['Apelido']).'[split]'.addslashes($rs->db_data['Username']).'[split]'.addslashes($rs->db_data['Telefone']).'[split]'.addslashes($rs->db_data['Empresa']).'[split]'.addslashes($rs->db_data['Nif']).'[split]'.addslashes($rs->db_data['Morada']).'[split]'.addslashes($rs->db_data['Codigo_Postal']).'[split]'.addslashes($rs->db_data['Telefone']).'[split]'.addslashes($rs->db_data['Telefone2']).'[split]'.addslashes($rs->db_data['Sobrenome']).'[split]'.addslashes($rs->db_data['Grupo_ID']);?>', 0);">
			
			<?php echo $rs->db_data['Nome'].' '.$rs->db_data['Apelido'].' | '.$rs->db_data['Telefone'].' | '.$rs->db_data['Telefone2'].' | '.$rs->db_data['Username'].' | '.$rs->db_data['Nif']; ?> 		
			</li><?php
		
			$rs->db_next();
		}
		?></ul><?php
	}
	else
	{
		$rs->db_close();
		$cond = '';
		$cond .= " cliente_nif LIKE '%".$keyword."%'";
		$cond .= " OR cliente_username LIKE '%".$keyword."%'";
		$cond .= " OR cliente_telefone1 LIKE '%".$keyword."%'";
		$cond .= " OR cliente_telefone2 LIKE '%".$keyword."%'";
		$cond .= " OR cliente_nome LIKE '%".$keyword."%'";
		$cond .= " OR cliente_sobrenome LIKE '%".$keyword."%'";
		$cond .= " OR cliente_apelido LIKE '%".$keyword."%'";
		
		$sql = "SELECT * FROM rmas WHERE 1=1 AND (".$cond.") GROUP BY cliente_username DESC LIMIT 20";
		$rs->db_open($db_link,$DB_name, $sql);
		if(!$rs->EOF())
		{
			?><ul id="country-list"><?php
			while(!$rs->EOF())
			{
				?><li style="position:relative" onclick="selectClient2('<?php echo addslashes($rs->db_data['ID']).'[split]'.addslashes($rs->db_data['cliente_nome']).'[split]'.addslashes($rs->db_data['cliente_apelido']).'[split]'.addslashes($rs->db_data['cliente_username']).'[split]'.addslashes($rs->db_data['cliente_telefone1']).'[split]'.addslashes($rs->db_data['cliente_empresa']).'[split]'.addslashes($rs->db_data['cliente_nif']).'[split]'.addslashes($rs->db_data['cliente_morada']).'[split]'.addslashes($rs->db_data['cliente_cp']).'[split]'.addslashes($rs->db_data['cliente_telefone1']).'[split]'.addslashes($rs->db_data['cliente_telefone2']).'[split]'.addslashes($rs->db_data['cliente_sobrenome']).'[split]'.addslashes($rs->db_data['Grupo_ID']);?>', 0);">
				
				<?php echo $rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_sobrenome'].' '.$rs->db_data['cliente_apelido'].' | '.$rs->db_data['cliente_telefone1'].' | '.$rs->db_data['cliente_telefone2'].' | '.$rs->db_data['cliente_username'].' | '.$rs->db_data['cliente_nif']; ?> 		
				</li><?php
			
				$rs->db_next();
			}
			?></ul><?php
		}
		else
		{
			?><ul id="country-list"><?php
			?><li>Sem Resultados</li><?php
			?></ul><?php
		}
	}
	$rs->db_close(1);
}
?>