<?php
	$rs_metodo = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		
	$sql = "SELECT Sandbox, Username_sandbox, Password_sandbox, Contractnumber, Username, Password  FROM metodos_pagamento WHERE Tipo=3";
	$rs_metodo->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$Sandbox=0;
	$MERCHANT_ID='';
	$ACCESS_KEY='';
	$CONTRACT_NUMBER='';
	if(!$rs_metodo->EOF())
	{
		$Sandbox		= $rs_metodo->db_data["Sandbox"];

		if($Sandbox==1)
		{
			$MERCHANT_ID = $rs_metodo->db_data["Username_sandbox"];
			$ACCESS_KEY = $rs_metodo->db_data["Password_sandbox"];
			$CONTRACT_NUMBER = $rs_metodo->db_data["Contractnumber"];
		}
		else
		{
			$MERCHANT_ID = $rs_metodo->db_data["Username"];
			$ACCESS_KEY = $rs_metodo->db_data["Password"];
			$CONTRACT_NUMBER = $rs_metodo->db_data["Contractnumber"];
		} 
	}
	$rs_metodo->db_close();
	
	
	// echo $Sandbox.'|'.$MERCHANT_ID.'|'.$ACCESS_KEY.'|'.$CONTRACT_NUMBER.'<br>';
	
	DEFINE( 'MERCHANT_ID', $MERCHANT_ID ); // Merchant ID
	DEFINE( 'ACCESS_KEY', $ACCESS_KEY ); // Certificate key
	DEFINE( 'PROXY_HOST', null); // Proxy URL (optional)
	DEFINE( 'PROXY_PORT', null); // Proxy port number without 'quotes' (optional)
	DEFINE( 'PROXY_LOGIN', '' ); // Proxy login (optional)
	DEFINE( 'PROXY_PASSWORD', '' ); // Proxy password (optional)
	if($Sandbox){
		DEFINE( 'PRODUCTION', FALSE); // Demonstration (FALSE) or production (TRUE) mode
	}else{
		DEFINE( 'PRODUCTION', TRUE); // Demonstration (FALSE) or production (TRUE) mode
	}
	DEFINE( 'PAYMENT_CURRENCY', '978' ); // Default payment currency (ex: 978 = EURO)
	DEFINE( 'ORDER_CURRENCY', '978' );
	DEFINE( 'SECURITY_MODE', 'SSL' ); // Protocol (ex: SSL = HTTPS)
	DEFINE( 'LANGUAGE_CODE', 'pt' ); // Payline pages language
	DEFINE( 'PAYMENT_ACTION', 101 ); // Default payment method
	DEFINE( 'PAYMENT_MODE', 'CPT' ); // Default payment mode
	DEFINE( 'CANCEL_URL', $GLOBALS['site_http_url'].'external_api/pagamentos/pgm_unicre/unicre.php?cancel=1'); // Default cancel URL
	DEFINE( 'NOTIFICATION_URL', $GLOBALS['site_http_url'].'external_api/pagamentos/pgm_unicre/getWebPaymentDetail_auto.php'); // Default notification URL
	DEFINE( 'RETURN_URL', $GLOBALS['site_http_url'].'external_api/pagamentos/pgm_unicre/getWebPaymentDetail.php'); // Default return URL
	DEFINE( 'CUSTOM_PAYMENT_TEMPLATE_URL', ''); // Default payment template URL
	DEFINE( 'CUSTOM_PAYMENT_PAGE_CODE', '' );
	DEFINE( 'CONTRACT_NUMBER', $CONTRACT_NUMBER ); // Contract type default (ex: 001 = CB, 003 = American Express...)
	DEFINE( 'CONTRACT_NUMBER_LIST', '' ); // Contract type multiple values (separator: ;)
	DEFINE( 'SECOND_CONTRACT_NUMBER_LIST', '' ); // Contract type multiple values (separator: ;)
?>