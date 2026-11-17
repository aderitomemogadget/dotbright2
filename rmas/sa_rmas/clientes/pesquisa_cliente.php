<?php
	include "settings.inc.php";

	$cliente = intval($_GET[$_GET['field']]);
	
	
	$client_nif = intval($_GET['ID']);
	$client_email = intval($_GET['ID']);
	

	

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$rs=new DataObj;
	$sql = 'SELECT ID, Username, Nome, Apelido, Morada, Codigo_Postal, Cidade, Telefone, Nif FROM clientes WHERE '.$_GET['field'].' LIKE "%'.$cliente.'%"';
	// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);	
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			echo '<a href="#" onclick="info_'.$rs->db_data['ID'].'();" class="preto client_id'.$rs->db_data['ID'].'">'.$rs->db_data['ID'].' - '.$rs->db_data['Username'].' - '.$rs->db_data['Nome'].' '.$rs->db_data['Apelido'].'</a>[spl]';
			echo $rs->db_data['ID'].'[spl]';
			echo $rs->db_data['Nif'].'[spl]';
			echo $rs->db_data['Nome'].' '.$rs->db_data['Apelido'].'[spl]';
			echo $rs->db_data['Morada'].'[spl]';
			echo $rs->db_data['Codigo_Postal'].'[spl]';
			echo $rs->db_data['Cidade'].'[spl]';
			echo $rs->db_data['Telefone'].'[spl]';
			echo $rs->db_data['Username'].'[spl]';
			echo '<script>function info_'.$rs->db_data['ID'].'(){						

						$(\'#cliente_id_site\').val("'.$rs->db_data['ID'].'");
						$(\'#cliente_nif\').val("'.$rs->db_data['Nif'].'");
						$(\'#cliente_nome\').val("'.$rs->db_data['Nome'].'");
						$(\'#cliente_morada\').val("'.$rs->db_data['Morada'].'");
						$(\'#cliente_codigopostal\').val("'.$rs->db_data['Codigo_Postal'].'");
						$(\'#cliente_localidade\').val("'.$rs->db_data['Cidade'].'");
						$(\'#cliente_telefone\').val("'.$rs->db_data['Telefone'].'");
						$(\'#cliente_email\').val("'.$rs->db_data['Username'].'");
						
						return false; 
					};</script>[split]';
			
			
			$rs->db_next();
		}	

	}
	$rs->db_close();