<?php

	/*CREATE TABLE `vivawallet_data` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `secure_key` varchar(150) DEFAULT NULL,
	  `OrderCode` varchar(255) DEFAULT NULL,
	  `ErrorCode` varchar(50) DEFAULT NULL,
	  `ErrorText` varchar(255) DEFAULT NULL,
	  `Timestamp` datetime DEFAULT NULL,
	  `ref` varchar(150) DEFAULT NULL,
	  `total_cost` int(11) DEFAULT NULL,
	  `currency` char(3) DEFAULT NULL,
	  `order_state` char(1) DEFAULT NULL,
	  `sessionid` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;*/


	function pagar_com_visa($enc_id=0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;
		$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'"';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if (!$rs->EOF())
		{
			$email = $rs->db_data['enc_Email'];
			$amount = $rs->db_data['TotalDocComIva'];

			$wb_total = number_format($amount, 2, '.', '');
			$wb_instal_total = round($amount);

			$wb_total_cents = number_format($amount, 2, '.', '')*100;
			$wb_total_cents = round($wb_total_cents);

		}
		$rs->db_close();


		$MerchantID =  '8ada17ef-ad2f-46d5-8fda-e8033fdd28da';
		$Password =   'RVtygP2J5cSOZQBDYAsgsrVbHK01LG';


		//sand box:
		// $BaseUrl =  'https://demo.vivapayments.com';
		//A successful transaction can be simulated with the test card number 4111111111111111. Use any future expiration date and 111 for the CVV2. The minimum transaction amount is 30p.


		//Producao:
		$BaseUrl =  'https://www.vivapayments.com';
		$Sourcecode =  '9083';
		$currency_symbol = '978';//EUR


		$poststring['Amount'] = $wb_total_cents;
		$poststring['RequestLang'] = 'pt-PT';
		$poststring['Email'] = $email;
		$poststring['MerchantTrns'] = $enc_id;
		$poststring['SourceCode'] = $Sourcecode;
		$poststring['CurrencyCode'] = $currency_symbol;
		$poststring['PaymentTimeOut'] = '300';
		$TmSecureKey = 'd2ViaXQuYnovbGljZW5zZS50eHQ='; // for extra encryption options


		// Initialise cURL
		$curl = curl_init($BaseUrl."/api/orders");
		curl_setopt($curl, CURLOPT_PORT, 443);
		$postargs = 'amount='.urlencode($poststring['Amount']).'&requestLang='.urlencode($poststring['RequestLang']).'&email='.urlencode($poststring['Email']).'&maxInstallments='.urlencode($maxperiod).'&merchantTrns='.urlencode($poststring['MerchantTrns']).'&SourceCode='.urlencode($poststring['SourceCode']).'&CurrencyCode='.urlencode($poststring['CurrencyCode']).'&PaymentTimeOut=300&DisableIVR=true';

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERPWD, $MerchantID.':'.$Password);
		$curlversion = curl_version();
		if(!preg_match("/NSS/" , $curlversion['ssl_version'])){
			curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, "TLSv1");
		}

		// execute curl
		$response = curl_exec($curl);

		if(curl_error($curl)){
			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postargs);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_USERPWD, $MerchantID.':'.$Password);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
		}


		curl_close($curl);

		try {
			if (version_compare(PHP_VERSION, '5.3.99', '>='))
			{
				$resultObj=json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
			}
			else
			{
				$response = preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', $response, 1);
				$resultObj = json_decode($response);
			}
		}
		catch( Exception $e )
		{
			echo "Result is not a json object (" . $e->getMessage() . ")";
		}

		if ($resultObj->ErrorCode==0)
		{	//success when ErrorCode = 0
			$OrderCode = $resultObj->OrderCode;
			$ErrorCode = $resultObj->ErrorCode;
			$ErrorText = $resultObj->ErrorText;
		}
		else{
			echo "Unable to create order code (" . $resultObj->ErrorText . ")";
		}

		$sql = "insert into vivawallet_data (enc_id, OrderCode, ErrorCode, ErrorText, Timestamp, ref, total_cost, currency, order_state) values ('".$enc_id."','".$OrderCode."','".$ErrorCode."','".$ErrorText."',now(),'".$cart->id."','".$wb_total_cents."','".$dest_currency['iso_code']."','I')";
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);

		$VivawalletUrl = $BaseUrl.'/web/newtransaction.aspx';
		$post_variables = Array(
		'VivawalletUrl' 		=> $VivawalletUrl,
		'Ref' 				=> $OrderCode);
		//Agora vamos fazer post dos dados
		/*$formpost = '';
		foreach ($post_variables as $name => $value) {
			$formpost.= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
		}*/
		//echo '<form id="vivawallet_confirmation_form" name="vivawallet_confirmation" data-ajax="false" action="'.$formurl.'" method="post">'.$formpost.'</form>';
		echo '<script>window.open("'.$VivawalletUrl.'?Ref='.$OrderCode.'","_top");</script>';
		//return $response;
	}

	function pagar_com_visa_sandbox($enc_id=0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;
		$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'"';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if (!$rs->EOF())
		{
			$email = $rs->db_data['enc_Email'];
			$amount = $rs->db_data['TotalDocComIva'];

			$wb_total = number_format($amount, 2, '.', '');
			$wb_instal_total = round($amount);

			$wb_total_cents = number_format($amount, 2, '.', '')*100;
			$wb_total_cents = round($wb_total_cents);

		}
		$rs->db_close();


		$MerchantID =  '0f55430e-a0e9-44e4-a7a1-052c1114debb';
		$Password =   'RVtygP2J5cSOZQBDYAsgsrVbHK01LG';
		$pub_key='ZGUbrPGgo7sDGHNUrRgtfMXTmV1dJ1hzZ4KHHm9rrgk=';
		$cliente_ID='zqdtfdxh6g9qa3ws2sbafuw3gwqf21grxui2uttraxds8.apps.vivapayments.com';
		$cliente_Secret='660F5T5qX0ni39601ku52G8UvrrCjV';


		//sand box:
		//A successful transaction can be simulated with the test card number 4111111111111111.
		//Use any future expiration date and 111 for the CVV2. The minimum transaction amount is 30p.

		$BaseUrl =  'https://demo.vivapayments.com';
		$Sourcecode =  '6572';
		$currency_symbol = '978';//EUR


		$poststring['Amount'] = $wb_total_cents;
		$poststring['RequestLang'] = 'pt-PT';
		$poststring['Email'] = $email;
		$poststring['MerchantTrns'] = $enc_id;
		$poststring['SourceCode'] = $Sourcecode;
		$poststring['CurrencyCode'] = $currency_symbol;
		$poststring['PaymentTimeOut'] = '300';
		$TmSecureKey = 'd2ViaXQuYnovbGljZW5zZS50eHQ='; // for extra encryption options


		// Initialise cURL
		$curl = curl_init($BaseUrl."/api/orders");
		curl_setopt($curl, CURLOPT_PORT, 443);
		$postargs = 'amount='.urlencode($poststring['Amount']).'&requestLang='.urlencode($poststring['RequestLang']).'&email='.urlencode($poststring['Email']).'&maxInstallments='.urlencode($maxperiod).'&merchantTrns='.urlencode($poststring['MerchantTrns']).'&SourceCode='.urlencode($poststring['SourceCode']).'&CurrencyCode='.urlencode($poststring['CurrencyCode']).'&PaymentTimeOut=300&DisableIVR=true';

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERPWD, $MerchantID.':'.$Password);
		$curlversion = curl_version();
		if(!preg_match("/NSS/" , $curlversion['ssl_version'])){
			curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, "TLSv1");
		}

		// execute curl
		$response = curl_exec($curl);

		if(curl_error($curl)){
			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postargs);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_USERPWD, $MerchantID.':'.$Password);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
		}


		curl_close($curl);

		try {
			if (version_compare(PHP_VERSION, '5.3.99', '>='))
			{
				$resultObj=json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
			}
			else
			{
				$response = preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', $response, 1);
				$resultObj = json_decode($response);
			}
		}
		catch( Exception $e )
		{
			echo "Result is not a json object (" . $e->getMessage() . ")";
		}

		if ($resultObj->ErrorCode==0)
		{	//success when ErrorCode = 0
			$OrderCode = $resultObj->OrderCode;
			$ErrorCode = $resultObj->ErrorCode;
			$ErrorText = $resultObj->ErrorText;
		}
		else{
			echo "Unable to create order code (" . $resultObj->ErrorText . ")";
		}

		$sql = "insert into vivawallet_data (enc_id, OrderCode, ErrorCode, ErrorText, Timestamp, ref, total_cost, currency, order_state) values ('".$enc_id."','".$OrderCode."','".$ErrorCode."','".$ErrorText."',now(),'".$cart->id."','".$wb_total_cents."','".$dest_currency['iso_code']."','I')";
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);

		$VivawalletUrl = $BaseUrl.'/web/newtransaction.aspx';
		$post_variables = Array(
		'VivawalletUrl' 		=> $VivawalletUrl,
		'Ref' 				=> $OrderCode);
		//Agora vamos fazer post dos dados
		/*$formpost = '';
		foreach ($post_variables as $name => $value) {
			$formpost.= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
		}*/
		//echo '<form id="vivawallet_confirmation_form" name="vivawallet_confirmation" data-ajax="false" action="'.$formurl.'" method="post">'.$formpost.'</form>';
		echo '<script>window.open("'.$VivawalletUrl.'?Ref='.$OrderCode.'","_top");</script>';
		//return $response;
	}
?>