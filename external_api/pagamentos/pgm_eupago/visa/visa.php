<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
	$authentication_entityId = '8a8294185332bbe601533754724914d9'; //Para producao
	$authentication_entityId = '8ac7a4c77279a71001727f745156182e'; //Para testes
    $Bearer = 'OGE4Mjk0MTg1YjY3NDU1NTAxNWI3YzE5MjhlODE3MzZ8UnI0N2VRZXNkVw==';
    $Entidade = '21075';//Para producao
    $Entidade = '25002';//Para testes

	$mbway_tel=$_GET['mbway_tel'];
	$mbway_tel=str_replace('+351','',$mbway_tel);
	$mbway_tel=str_replace('00351','',$mbway_tel);
	$id_enc=intval($_GET['id_enc']);

	// https://sibs.docs.onlinepayments.pt/tutorials/integration-guide
    // Test mbway:
	function request($authentication_entityId,$Bearer,$valor=0,$tel='',$id_enc=0)
	{
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="INSERT INTO sibs_mbway_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$id_enc."','".date("Y-m-d H:m:i")."', 'Tel = $tel | Valor = $valor', '0','')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);


	    $url = "https://test.oppwa.com/v1/payments";
	    $data = "entityId=".$authentication_entityId.
	                "&amount=$valor" .
	                "&currency=EUR" .
	                "&paymentType=PA" .
	                "&testMode=EXTERNAL" .
	                "&paymentBrand=MBWAY" .
	                "&virtualAccount.accountId=351#$tel" .
	                "&customParameters[SIBS_ENV]=QLY".
	                "&shopperResultUrl=".urlencode("https://www.garageandstage.com/external_api2/pagamentos/pgm_sibs/mbway/ipn.php");

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer '.$Bearer));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $responseData = curl_exec($ch);
	    if(curl_errno($ch)) {
	        return curl_error($ch);
	    }
	    curl_close($ch);
	    $sql="UPDATE sibs_mbway_logs set Erros='".addslashes($responseData)."' WHERE ID = '$ID_Log' ";
		$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);
	    return $responseData;
	}

	if ($id_enc > 0)
	{
		$rs = new DataObj;
		$sql_all = "SELECT TotalDocComIva FROM encomendas WHERE encomendas.ID='".$id_enc."'";
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor = $rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();

		$valor=number_format($valor,2,'.','');
		$responseData=request($authentication_entityId,$Bearer,$valor,$mbway_tel,$id_enc);

		echo '<script>verifica_mbway("'.$id_enc.'");</script>';
	}

?>