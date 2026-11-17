<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);

	$CCARD_KEY = 'FHP-338990';

	function sendVISARequest($CCARD_KEY = '', $orderId = '', $valor = '')
	{
		if (!is_null($CCARD_KEY) && !is_null($orderId) && !is_null($valor))
		{
        	$url= 'https://ifthenpay.com/api/creditcard/init/'.$CCARD_KEY;
	        // The data to send to the API
			$postData = array(
			    "orderId" => $orderId,
	            "amount" => $valor,
	            "successUrl" => urlencode(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_VISA_IFTHEN]['url'] . 'success.php'),
	            "errorUrl" => urlencode(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_VISA_IFTHEN]['url'] . 'error.php'),
	            "cancelUrl" => urlencode(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_VISA_IFTHEN]['url'] . 'cancel.php'),
	            "language" => "pt"
			);

			// Setup cURL
			$ch = curl_init($url);
			curl_setopt_array($ch, array(
			    CURLOPT_POST => TRUE,
			    CURLOPT_RETURNTRANSFER => TRUE,
			    CURLOPT_HTTPHEADER => array(
			        'Content-Type: application/json'
			    ),
			    CURLOPT_POSTFIELDS => json_encode($postData)
			));

			// Send the request
			$response = curl_exec($ch);

			// Check for errors
			if($response === FALSE){
			    die(curl_error($ch));
			}

			// Decode the response
			$responseData = json_decode($response, TRUE);

			// Close the cURL handler
			curl_close($ch);

			return $responseData;

	    }
	    else return 'Informação incompleta para poder invocar o serviço VISA!';
	}

	$res = sendVISARequest($CCARD_KEY,124,100.05);
	if($res['Message']=='Success')
	{
//		echo '<script>window.open("'.$res['PaymentUrl'].'","_top")</script>';
	}

	var_dump($res);

	//echo

?>aaa