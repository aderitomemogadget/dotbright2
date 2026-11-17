<?php

	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);

	function format_number($number)
	{
		$verifySepDecimal = number_format(99, 2);
		$valorTmp = $number;
		$sepDecimal = substr($verifySepDecimal, 2, 1);
		$hasSepDecimal = True;
		$i = (strlen($valorTmp) - 1);

		for ($i; $i != 0; $i-= 1) {
			if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == ",") {
				$hasSepDecimal = True;
				$valorTmp = trim(substr($valorTmp, 0, $i)) . "@" . trim(substr($valorTmp, 1 + $i));

				break;
			}
		}
		if ($hasSepDecimal != True) {
			$valorTmp = number_format($valorTmp, 2);
			$i = (strlen($valorTmp) - 1);

			for ($i; $i != 1; $i--) {
				if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == ",") {
					$hasSepDecimal = True;
					$valorTmp = trim(substr($valorTmp, 0, $i)) . "@" . trim(substr($valorTmp, 1 + $i));

					break;
				}
			}
		}

		for ($i = 1; $i != (strlen($valorTmp) - 1); $i++) {
			if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == "," || substr($valorTmp, $i, 1) == " ") {
				$valorTmp = trim(substr($valorTmp, 0, $i)) . trim(substr($valorTmp, 1 + $i));

				break;
			}
		}
		if (strlen(strstr($valorTmp, '@')) > 0) {
			$valorTmp = trim(substr($valorTmp, 0, strpos($valorTmp, '@'))) . trim($sepDecimal) . trim(substr($valorTmp, strpos($valorTmp, '@') + 1));
		}

		return $valorTmp;
	}


	function GenerateRef($Entidade,$Bearer, $authentication_entityId, $order_id, $order_value, $sanbox = 0)
	{
		$order_value= sprintf("%01.2f", $order_value);
		$order_value =  format_number($order_value);
		if ($order_value < 2)
		{
			echo "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 2 Euro";
			return;
		}
		$data1=date("Y-m-d");
		$time1=date("h:m:s");
		$data2=date('Y-m-d',strtotime($date1 . "+3 days"));
		$url = "https://test.oppwa.com/v1/payments";

	    $data = "entityId=".$authentication_entityId.
	                "&amount=$order_value".
	                "&currency=EUR" .
	                "&paymentType=PA" .
	                "&paymentBrand=SIBS_MULTIBANCO" .
	                "&merchantTransactionId=$order_id" .
	                "&customParameters[SIBSMULTIBANCO_PtmntEntty]=$Entidade" .
	                "&customParameters[SIBSMULTIBANCO_RefIntlDtTm]=".urlencode($data1."T".$time1.".001+01:00").
	                "&customParameters[SIBSMULTIBANCO_RefLmtDtTm]=".urlencode($data2."T".$time1.".001+01:00").
	                "&customer.ip=".urlencode("176.111.107.44").
	                "&customer.surname=Garage" .
	                "&customer.givenName=Stage" .
	                "&billing.country=PT" .
	                "&testMode=EXTERNAL" .
	                "&customParameters[SIBS_ENV]=QLY" .
	                "&shopperResultUrl=".urlencode("https://www.davidpaixao.com/external_api/pagamentos/pgm_cgd/ipn.php");
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	                   'Authorization:Bearer '.$Bearer));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = curl_exec($ch);
	    if(curl_errno($ch))
	    {
	        return curl_error($ch);
	    }
	    curl_close($ch);

	    var_dump($response);

	}


	global $Entidade,$authentication_entityId,$Bearer;

	$authentication_entityId = '8a8294185332bbe601533754724914d9'; //Para producao
	$authentication_entityId = '8ac7a4c77279a71001727f745156182e'; //Para testes
    $Bearer = 'OGE4Mjk0MTg1YjY3NDU1NTAxNWI3YzE5MjhlODE3MzZ8UnI0N2VRZXNkVw==';
    $Entidade = '21075';//Para producao
    $Entidade = '25002';//Para testes


    $id_enc = '1';
    $valor = '5';

	GenerateRef($Entidade,$Bearer, $authentication_entityId, $id_enc, number_format($valor , 2, '.', ''), 1);


?>