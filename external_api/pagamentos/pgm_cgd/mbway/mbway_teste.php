<?php

include '/home/www/pcdiga.com/public_html/pgm_mbway/settings.php';
$erro = '';

$hConn = OpenConnection($GLOBALS['Server'], $GLOBALS['UserName'], $GLOBALS['db_password']);
$obj = new clsRecordset();
$obj2 = new clsRecordset();
$sql='SELECT * FROM mbway WHERE ID=397';

$obj->open($hConn, $GLOBALS['erp_DBName'], $sql);
if(!$obj->EOF())
{
	//Vamos verificar se ja passou 300 segundos = 5m.
	$IDb          = $obj->aFields['ID'];
	$ID_ENCOMENDA = $obj->aFields['ID_ENC'];
	$timestamp1   = $obj->aFields['timestamp'];
	$timestamp2   = date("Y-m-d\TH:i:s.000\Z");

	$start_date = new DateTime($timestamp1);
	$since_start = $start_date->diff(new DateTime($timestamp2));

	$sql_all = "SELECT ID2 FROM encomendas WHERE ID='$ID_ENCOMENDA'";
	$obj2->open($hConn, $GLOBALS['erp_DBName'], $sql_all);
	$ID2 = 0;
	if(!$obj2->EOF())
	{
		$ID2 = $obj2->aFields['ID2'];
	}
	else
	{
		$erro = 'Encomenda não Encontrada';
	}
	$obj2->close();

/*	echo $since_start->days.' days total<br>';
	echo $since_start->y.' years<br>';
	echo $since_start->m.' months<br>';
	echo $since_start->d.' days<br>';
	echo $since_start->h.' hours<br>';
	echo $since_start->i.' minutes<br>';
	echo $since_start->s.' seconds<br>';*/


	if($since_start->i >= 5)
	{
		//Passaram 5 minutos vamos anular a transação
		$total_formated_prev    = $obj->aFields['amount'];
		$currencyCode_prev      = $obj->aFields['currencyCode'];
		$operationTypeCode_prev = $obj->aFields['operationTypeCode'];
		$merchantOprId_prev     = $obj->aFields['merchantOprId'];
		$timestamp              = $obj->aFields['timestamp1'];
		$statusCode             = $obj->aFields['statusCode'];
		$token                  = $obj->aFields['token'];
		$ID_ENCOMENDA           = $obj->aFields['ID_ENC'];
		$telMBWay               = $obj->aFields['telMBWay'];

		//$posId = '25636'; //Dev
		$posId          = '892075';
		$iPAddress      = $_SERVER['SERVER_ADDR'];
		$messageType    = 'N0003';
		$tipo           = 'compra';
		$moeda          = 'EUR';
		$casas_decimais = 2;
		$cli_tipo       = 1;
		$channel        = '03';
		/*
		‘01’ – Mobile
		‘02’ – TV
		‘03’ – Web
		‘04’ – Vending
		‘05’ – Ponto de Venda
		*/

		// Se for por Nr Telemovel
		if(strlen($telMBWay) == 9)
		{
			$xmlCliente = '<alias><aliasName>351#'.$telMBWay.'</aliasName><aliasTypeCde>001</aliasTypeCde></alias>';
		}
		else
		{
			$erro = 'O numero de Telefone não é válido';
		}
		if($erro)echo '<br>erro2: '.$erro;

		$soap_client = new nusoap_client(WS_HTTP_URL);
		$soap_client->setUseCURL(true);
		$soap_client->useHTTPPersistentConnection();
		$soap_client->setCurlOption(CURLOPT_SSL_VERIFYHOST, FALSE);
		$soap_client->setCurlOption(CURLOPT_SSL_VERIFYPEER, 0);
		$soap_client->setCurlOption(CURLOPT_RETURNTRANSFER, 1);
		$soap_client->setCurlOption(CURLOPT_SSLCERT, SHELL_PATH.'pgm_mbway/M0168451.pem');
		$soap_client->setCurlOption(CURLOPT_SSLCERTPASSWD, TOKEN);
		$soap_client->setCurlOption(CURLOPT_SSLKEYTYPE, 'PEM');
		$soapaction = "http://financial.services.merchant.channelmanagermsp.sibs/MerchantFinancialOperationWS/requestFinancialOperationRequest";

		$merchantOprId=$ID_ENCOMENDA.'/'.(date('Ymd'));
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:fin="http://financial.services.merchant.channelmanagermsp.sibs/">';
		$xml .= '	<soapenv:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">';
		$xml .= '		<wsa:Action>'.$soapaction.'</wsa:Action>';
		$xml .= '		<wsa:ReplyTo>';
		$xml .= '			<wsa:Address>https://www.pcdiga.com/pgm_mbway/getpayments.php</wsa:Address>';
		$xml .= '		</wsa:ReplyTo>';
		$xml .= '	</soapenv:Header>';
		$xml .= '	<soapenv:Body>';
		$xml .= '		<fin:requestFinancialOperation>';
		$xml .= '			<arg0>';
		$xml .= '				<messageType>'.$messageType.'</messageType>';
		$xml .= '				<aditionalData>Enc. '.$ID2.'</aditionalData>';
		$xml .= $xmlCliente; //alias
		$xml .= '				<financialOperation>';
		$xml .= '					<amount>'.$total_formated_prev.'</amount>';
		$xml .= '					<currencyCode>'.$currencyCode_prev.'</currencyCode>';
		$xml .= '					<operationTypeCode>022</operationTypeCode>';
		$xml .= '					<merchantOprId>'.$merchantOprId.'</merchantOprId>';
		$xml .= '				</financialOperation>';

	/*	$xml .= '				<referencedFinancialOperation>';
		$xml .= '					<amount>'.$total_formated_prev.'</amount>';
		$xml .= '					<currencyCode>'.$currencyCode_prev.'</currencyCode>';
		$xml .= '					<operationTypeCode>'.$operationTypeCode_prev.'</operationTypeCode>';
		$xml .= '					<merchantOprId>'.$merchantOprId_prev.'</merchantOprId>';
		$xml .= '				</referencedFinancialOperation>';*/

		$xml .= '				<merchant>';
		$xml .= '					<iPAddress>'.$iPAddress.'</iPAddress>';
		$xml .= '					<posId>'.$posId.'</posId>';
		$xml .= '				</merchant>';
		$xml .= '				<messageProperties>';
		$xml .= '					<channel>'.$channel.'</channel>';
		$xml .= '					<apiVersion>1</apiVersion>';
		$xml .= '					<channelTypeCode>VPOS</channelTypeCode>';
		$xml .= '					<networkCode>MULTIB</networkCode>';
		$xml .= '					<serviceType>01</serviceType>';
		$xml .= '					<timestamp>'.date("Y-m-d\TH:i:s.000\Z").'</timestamp>';
		$xml .= '				</messageProperties>';
		$xml .= '			</arg0>';
		$xml .= '		</fin:requestFinancialOperation>';
		$xml .= '	</soapenv:Body>';
		$xml .= '</soapenv:Envelope>';

/*echo "CALL: <br>".$soapaction."<br>";
echo "<br><hr><pre>".htmlspecialchars($xml)."</pre>";*/

		$response = $soap_client->send($xml, $soapaction, '');

		echo 'Error: <pre>' . $soap_client->getDebug() . '</pre>';

/*		echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($soap_client->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($soap_client->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
    var_dump($response);*/


print_r($response);
/*
		print_r($response);
		$status_code=$response['return']['statusCode'];
		echo '<br><h2>Debug</h2><pre>' . htmlspecialchars($soap_client->debug(), ENT_QUOTES) . '</pre>';
		echo "<br>ENC: $ID_ENCOMENDA || STATUS: $status_code <br>";
*/



	}

}
$obj->close();
