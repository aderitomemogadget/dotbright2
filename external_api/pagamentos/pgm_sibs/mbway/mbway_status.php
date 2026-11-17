<?php

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
	$authentication_entityId = '8a8294185332bbe601533754724914d9'; //Para producao
	$authentication_entityId = '8ac7a4c77279a71001727f745156182e'; //Para testes
    $Bearer = 'OGE4Mjk0MTg1YjY3NDU1NTAxNWI3YzE5MjhlODE3MzZ8UnI0N2VRZXNkVw==';
    $Entidade = '21075';//Para producao
    $Entidade = '25002';//Para testes

    function request($id_retorno,$authentication_entityId,$Bearer,$valor=0,$tel='',$id_enc=0)
    {
		$url = "https://test.oppwa.com/v1/payments/".$id_retorno;
		$url .= "?entityId=".$authentication_entityId;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer '.$Bearer));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if(curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		return $responseData;
	}

	$id_enc = intval($_GET['id_enc']);
	if(!$id_enc)die("0");

	$Estado=-1;


	$sql="SELECT * from sibs_mbway_logs where ID_Encomenda=$id_enc";
	$rs = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['ID_Encomenda'];
		$Data= $rs->db_data['Data'];
		$Json= $rs->db_data['Json'];
		$Estado= $rs->db_data['Estado'];
		$Erros= $rs->db_data['Erros'];
		$dados = json_decode($Erros, true);
		$id_retorno = $dados["id"];
		if($Estado==0 && $id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			$responseData = request();
			$responseData=request($id_retorno,$authentication_entityId,$Bearer,$valor,$mbway_tel,$id_enc);

			if(substr_count($responseData,'Cancelled by user')>0)
			{
				$Estado=1;
				$sql="UPDATE sibs_mbway_logs set Estado=1 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'user authorization failed')>0)
			{
				$Estado=2;
				$sql="UPDATE sibs_mbway_logs set Estado=2 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'transaction declined')>0)
			{
				$Estado=3;
				$sql="UPDATE sibs_mbway_logs set Estado=3 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}

			if(substr_count($responseData,'Request successfully')>0)
			{
				$Estado=13;
				$sql="UPDATE sibs_mbway_logs set Estado=13 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				enviarEmailCliente($ID_Encomenda,'', 13);
				//Agora deve enviar encomenda para o cliente de paga
			}
		}

	}
	$rs->db_close();


	echo $Estado;







/*
{
  "id":"8ac7a49f72bd2b760172c22badba129b",
  "paymentType":"PA",
  "paymentBrand":"MBWAY",
  "amount":"10.00",
  "currency":"EUR",
  "descriptor":"USAGE",
  "result":{
    "code":"800.400.500",
    "description":"Waiting for confirmation of non-instant payment. Denied for now."
  },
  "resultDetails":{
    "Pre-authorization validity":"2018-09-15T01:00:00.001+01:00",
    "ConnectorTxID1":"9740.6777.2407",
    "ConnectorTxID3":"S08058100624238S||351#914931936|MobilePhone|QLY",
    "ConnectorTxID2":"2020-06-17 12:05:41",
    "AcquirerResponse":"Pending"
  },
  "customParameters":{
    "SIBS_ENV":"QLY"
  },
  "buildNumber":"923d087e91e2036da6f8bcc4821c04aa6270d517@2020-06-16 11:53:56 +0000",
  "timestamp":"2020-06-17 12:05:43+0000",
  "ndc":"8ac7a4c77279a71001727f745156182e_9be9d611f042438388e8303a9695f29d"
}
*/
/*include '/home/www/pcdiga.com/public_html/pgm_mbway/settings.php';
$erro = '';

$hConn = OpenConnection($GLOBALS['Server'], $GLOBALS['UserName'], $GLOBALS['db_password']);
$obj = new clsRecordset(); $obj2 = new clsRecordset();
$sql='SELECT * FROM mbway WHERE Final_status=0 ';
//$sql='SELECT * FROM mbway WHERE Final_status=0 AND statusCode="000"';

$obj->open($hConn, $GLOBALS['erp_DBName'], $sql);
while(!$obj->EOF())
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

	echo $since_start->days.' days total<br>';
	echo $since_start->y.' years<br>';
	echo $since_start->m.' months<br>';
	echo $since_start->d.' days<br>';
	echo $since_start->h.' hours<br>';
	echo $since_start->i.' minutes<br>';
	echo $since_start->s.' seconds<br>';


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
		//‘01’ – Mobile
		//‘02’ – TV
		//‘03’ – Web
		//‘04’ – Vending
		//‘05’ – Ponto de Venda


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
		//$soap_client->setCurlOption(CURLOPT_SSLCERT, SHELL_PATH.'pgm_mbway/M0502574.pem');
		//$soap_client->setCurlOption(CURLOPT_SSLCERT, SHELL_PATH.'pgm_mbway/M0360727.pem');
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
		$xml .= '					<operationTypeCode>048</operationTypeCode>';
		$xml .= '					<merchantOprId>'.$merchantOprId.'</merchantOprId>';
		$xml .= '				</financialOperation>';

		$xml .= '				<referencedFinancialOperation>';
		$xml .= '					<amount>'.$total_formated_prev.'</amount>';
		$xml .= '					<currencyCode>'.$currencyCode_prev.'</currencyCode>';
		$xml .= '					<operationTypeCode>'.$operationTypeCode_prev.'</operationTypeCode>';
		$xml .= '					<merchantOprId>'.$merchantOprId_prev.'</merchantOprId>';
		$xml .= '				</referencedFinancialOperation>';

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

		$response = $soap_client->send($xml, $soapaction, '');
		$status_code=$response['return']['statusCode'];
		echo "ENC: $ID_ENCOMENDA || STATUS: $status_code <br>";
		if($status_code=='000')
		{
			$sql = 'UPDATE mbway SET Final_status="2" WHERE ID = "'.$IDb.'"';
			$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);
			echo $sql."<br>";

			$sql = 'UPDATE encomendas SET estado="5" WHERE ID = "'.$ID_ENCOMENDA.'"';
			echo $sql."<br>";
			$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);
		}
		else
		{
			// e porque deu erro
			$sql = 'UPDATE mbway SET Final_status="3" WHERE ID = "'.$IDb.'"';
			$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);
			echo $sql."<br>";

			$sql = 'UPDATE encomendas SET estado="5" WHERE ID = "'.$ID_ENCOMENDA.'"';
			echo $sql."<br>";
			$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);
		}


	}
	$obj->next();
}
$obj->close();
*/