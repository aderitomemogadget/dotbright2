<?php

	$path = dirname(__FILE__) . '/../../../core/';
	include $path . 'core_files.php';
	$AccountId=$GLOBALS['pgm_metodos'][MP_TP_CC_EASYPAY]['user'];
	$ApiKey=$GLOBALS['pgm_metodos'][MP_TP_CC_EASYPAY]['key'];



	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$sql="SELECT * from pagamentos_easypay where ID_Encomenda=1685";
	$rs = new DataObj;		
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	while(!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['ID_Encomenda'];
		$Data= $rs->db_data['CDate'];
		$Estado= $rs->db_data['Estado'];
		$checkoutId= $rs->db_data['checkoutId'];
		$responseData=$Resposta1=$rs->db_data['Resposta1'];
		$dados = json_decode(unserialize($Resposta1), true);
		//echo $checkoutId = $dados["customer"]["id"];
		$res = get_pagamento_Status($AccountId,$ApiKey,$checkoutId);

		print_r($res);

		$rs->db_next();
	}
	$rs->db_close();


	function get_pagamento_Status($AccountId,$ApiKey,$checkoutId='')
	{
		$url = 'https://api.prod.easypay.pt/2.0/single/'.$checkoutId;

	    $json_string=json_encode($postValues);
	    $headers[] = 'AccountId:'.$AccountId;
	    $headers[] = 'ApiKey:'.$ApiKey;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$response = curl_exec($ch);
		$res = json_decode($response,true);
		return $res;
	}


?>