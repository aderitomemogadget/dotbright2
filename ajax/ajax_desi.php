<?php
	$path='../core/';
	include $path.'core_files.php';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	if(intval($_GET['prod_id']) > 0)
	{
		$lingua = $GLOBALS['admin_lang'];
		$m_id=intval($_GET['prod_id']);
		$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT Cabecalho,Texto1 from products_lang WHERE ID_PAI='.$m_id.' AND Lang="'.$lingua.'"');
				echo converterPontoParaTraco(limpa_editor_lixo(stripslashes(strlen($rs->db_data['Texto1']) > 150 ? html_cut($rs->db_data['Texto1'],147)."... " : $rs->db_data['Texto1'])));
		$rs->db_close();
	}
?>