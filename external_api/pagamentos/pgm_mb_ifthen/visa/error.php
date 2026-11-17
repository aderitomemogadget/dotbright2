<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	require_once $path.'core_files.php';

	$enc_id = $_POST['id'];
	$RequestId = $_GET['requestId'];

	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj;
	$sql_all = "SELECT * FROM ifthen_visa_logs WHERE RequestId='".$RequestId."'";
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
	if (!$rs->EOF())$enc_id2=$rs->db_data['ID_Encomenda'];
	$rs->db_close();

	if($enc_id==$enc_id2)
	{
		$sql = "UPDATE encomendas SET Encomenda_Paga=1,Encomenda_Status='".$payment_status."',Estado='14' WHERE ID='".$enc_id."'";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);


		$sql="UPDATE ifthen_visa_logs set Estado='-1' WHERE RequestId='".$RequestId."'";
		$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);


		$referer = $GLOBALS['site_http_url'];
		echo drawRedirectPage(getLinguas('key_shop_msg1_c'), $referer, 3000, 1);
	}
	else echo 'ERRO';
?>