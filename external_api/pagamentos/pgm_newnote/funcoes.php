<?php

	function get_all_mbway_pagamentos()
	{
		global $CA_certificate,$Private_Key,$Client_Certificate,$url,$passphrase,$API_KEY,$TPA_ID,$Authorization;
		// Initialise cURL
	  	$ch = curl_init($url);
	  	$actualUrl = $url;
	  	curl_setopt($ch, CURLOPT_URL, $actualUrl);
	  	// The --key option - If your key file has a password, you will need to set
	  	curl_setopt($ch, CURLOPT_SSLKEY, $Private_Key);
	  	// The --cacert option
	  	curl_setopt($ch, CURLOPT_CAINFO, $CA_certificate);
	  	// The --cert option
		curl_setopt($ch, CURLOPT_SSLCERT, $Client_Certificate);
		curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $passphrase);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array(
			    'Tpa-Id: '.$TPA_ID,
			    'Authorization: '.$Authorization,
		        'Content-Type:application/json'
		    )
		);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}




	function pagar_mbway($tel='',$valor='',$descricao='')
	{
		global $CA_certificate,$Private_Key,$Client_Certificate,$url,$passphrase,$API_KEY,$TPA_ID,$Authorization;
		// Initialise cURL
	  	$ch = curl_init($url);
	  	$actualUrl = $url;

	  	$data = array("values" => array(
		"alias" => $tel,
		"aliasTypeCode" => "601",
		"amount" => $valor,
		"currencyCode" => "7010",
		"description" => $descricao,
		"username" => 'widget manager'
		));
		$data_string = json_encode($data);

		$actualUrl = $url.'?username=admin';

	  	curl_setopt($ch, CURLOPT_URL, $actualUrl);
	  	// The --key option - If your key file has a password, you will need to set
	  	curl_setopt($ch, CURLOPT_SSLKEY, $Private_Key);
	  	// The --cacert option
	  	curl_setopt($ch, CURLOPT_CAINFO, $CA_certificate);
	  	// The --cert option
		curl_setopt($ch, CURLOPT_SSLCERT, $Client_Certificate);
		curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $passphrase);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array(
			    'Tpa-Id: '.$TPA_ID,
			    'Authorization: '.$Authorization,
		        'Content-Type:application/json',
	        	'Content-Length: ' . strlen($data_string)
		    )
		);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}



	function get_status_mbway_tel($id='')
	{
		global $CA_certificate,$Private_Key,$Client_Certificate,$url,$passphrase,$API_KEY,$TPA_ID,$Authorization;
		// Initialise cURL
	  	$ch = curl_init($url.'/'.$id);
	  	$actualUrl = $url.'/'.$id;
	  	curl_setopt($ch, CURLOPT_URL, $actualUrl);
	  	// The --key option - If your key file has a password, you will need to set
	  	curl_setopt($ch, CURLOPT_SSLKEY, $Private_Key);
	  	// The --cacert option
	  	curl_setopt($ch, CURLOPT_CAINFO, $CA_certificate);
	  	// The --cert option
		curl_setopt($ch, CURLOPT_SSLCERT, $Client_Certificate);
		curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $passphrase);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_POST, 1);
		// curl_setopt($ch, CURLOPT_VERBOSE, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array(
			    'Tpa-Id: '.$TPA_ID,
			    'Authorization: '.$Authorization,
		        'Content-Type:application/json'
		    )
		);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}

?>