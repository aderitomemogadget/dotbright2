<?php
require_once 'settings.php';
$erro = '';

//********************************************** Pedido de Operação Financeira
/* DEFINICOES MBWAY */
$messageType = 'N0003';
$tipo = 'compra';
$moeda = 'EUR';
$casas_decimais = 2;
$cli_tipo = 1;
$iPAddress = $_SERVER['SERVER_ADDR'];
$posId = '892075';

$channel = '03';
/*
‘01’ – Mobile
‘02’ – TV
‘03’ – Web
‘04’ – Vending
‘05’ – Ponto de Venda
*/

$operationTypeCode = '';
if($tipo == 'compra') $operationTypeCode='022';
if($tipo == 'devolucao') $operationTypeCode='023';
if($tipo == 'autorizacao') $operationTypeCode='024';
if($tipo == 'compra_pos_autorizacao') $operationTypeCode='025';
if($tipo == 'cancelamento_autorizacao') $operationTypeCode='026';
if($tipo == 'anulacao') $operationTypeCode='048';

$currencyCode = '';
if($moeda == 'EUR') $moeda_code = '978';
$currencyCode = $moeda_code.$casas_decimais;

$hConn = OpenConnection($GLOBALS['Server'], $GLOBALS['UserName'], $GLOBALS['db_password']);
$obj = new clsRecordset();

if(isset($_GET['IDENC']) && intval($_GET['IDENC']) > 0 && isset($_GET['telMBWay']) && intval($_GET['telMBWay']) > 0)
{
	$ID_ENCOMENDA = $_GET['IDENC'];
	$telMBWay = $_GET['telMBWay'];

	$sql_all = "SELECT ID2,TotalDocComIva FROM encomendas WHERE ID='$ID_ENCOMENDA'";
	$obj->open($hConn, $GLOBALS['erp_DBName'], $sql_all);
	if(!$obj->EOF())
	{
		$ID2 = $obj->aFields['ID2'];
		$TotalDocComIva = $obj->aFields['TotalDocComIva'];
	}
	else
	{
		$erro = 'Encomenda não Encontrada';
	}
	$obj->close();
	if($erro) echo '<br>erro1: '.$erro;

	/* Identificação do Cliente */
	if($cli_tipo == 1)
	{
		// Se for por Nr Telemovel
		if(strlen($telMBWay) == 9)
		{
			$xmlCliente = '<alias><aliasName>351#'.$telMBWay.'</aliasName><aliasTypeCde>001</aliasTypeCde></alias>';
		}
		else
		{
			$erro = 'O numero de Telefone não é válido';
		}
	}
	elseif($cli_tipo == 2)
	{
		// Se for por E-mail
		if(filter_var($telMBWay, FILTER_VALIDATE_EMAIL))
		{
			$xmlCliente = '<alias><aliasName>'.$telMBWay.'</aliasName><aliasTypeCde>002</aliasTypeCde></alias>';
		}
		else
		{
			$erro = 'O E-mail não é válido';
		}
	}
	if($erro)echo '<br>erro2: '.$erro;

	$soap_client = new nusoap_client(WS_HTTP_URL);
	$soap_client->setUseCURL(true);
	$soap_client->useHTTPPersistentConnection();
	$soap_client->setCurlOption(CURLOPT_SSL_VERIFYHOST, FALSE);
	$soap_client->setCurlOption(CURLOPT_SSL_VERIFYPEER, 0);
	$soap_client->setCurlOption(CURLOPT_RETURNTRANSFER, 1);
	$soap_client->setCurlOption(CURLOPT_SSLCERT, SHELL_PATH.'pgm_mbway/M0168451.pem');
	//$soap_client->setCurlOption(CURLOPT_SSLCERT, SHELL_PATH.'pgm_mbway/M0360727.pem');
	//$soap_client->setCurlOption(CURLOPT_SSLCERT, SHELL_PATH.'pgm_mbway/M0502574.pem'); // dev
	$soap_client->setCurlOption(CURLOPT_SSLCERTPASSWD, TOKEN);
	$soap_client->setCurlOption(CURLOPT_SSLKEYTYPE, 'PEM');
	$soapaction = "http://financial.services.merchant.channelmanagermsp.sibs/MerchantFinancialOperationWS/requestFinancialOperationRequest";
	$total_formated = str_replace(',','',number_format($TotalDocComIva,$casas_decimais,',',''));
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
	$xml .= '					<amount>'.$total_formated.'</amount>';
	$xml .= '					<currencyCode>'.$currencyCode.'</currencyCode>';
	$xml .= '					<operationTypeCode>'.$operationTypeCode.'</operationTypeCode>';
	$xml .= '					<merchantOprId>'.$merchantOprId.'</merchantOprId>';
	$xml .= '				</financialOperation>';

/*	$xml .= '				<referencedFinancialOperation>';
  	$xml .= '					<amount></amount>';
 	$xml .= '				 	<currencyCode></currencyCode>';
  	$xml .= '					<operationTypeCode></operationTypeCode>';
  	$xml .= '					<merchantOprId></merchantOprId>';
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

	//GRavar Operação

	$sql='INSERT INTO mbway (ID_ENC,amount,currencyCode,operationTypeCode,merchantOprId,Final_status,telMBWay) values ("'.$ID_ENCOMENDA.'","'.$total_formated.'","'.$currencyCode.'","'.$operationTypeCode.'","'.$merchantOprId.'","0","'.$telMBWay.'")';
	$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);

	$sql = "UPDATE encomendas SET MsgHash='$merchantOprId' WHERE ID='$ID_ENCOMENDA'";
	$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);

}
$response = $soap_client->send($xml, $soapaction, '');
/*Array ( [return] =>
		Array ( [amount] => 200
				[currencyCode] => 9782
				[merchantOperationID] => 82866/20151103160013
				[statusCode] => 000 [timestamp] => 2015-11-03T16:00:15.275Z [token] => EhnBmhEZkPbGlEy0n3Ef28smlsGZgSKR ) )*/

$status_code=$response['return']['statusCode'];

$sql='UPDATE mbway SET statusCode="'.$response['return']['statusCode'].'",timestamp="'.$response['return']['timestamp'].'",token="'.$response['return']['token'].'",Final_status="0" WHERE merchantOprId = "'.$merchantOprId.'"';
$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);

if($status_code=='000')$msg='Agora necessita apenas de validar o seu pagamento através da sua aplicação <br>MB Way<br><br>';

if($status_code=='010')$msg='Não foi possível concluir a operação de associação de identificador devido a recusa na aplicação.<br><br>';
if($status_code=='020')$msg='Não foi possível concluir a operação de compra devido a recusa na aplicação.<br><br>';
if($status_code=='100')$msg='Não foi possível concluir a operação.<br><br>';
if($status_code=='101')$msg='Ou Operação expirada, por favor tente novamente.<br><br>';
if($status_code=='102')$msg='A mensagem enviada não está de acordo com o formato especificado para o serviço invocado.<br><br>';
if($status_code=='103')$msg='Não foi encontrado o identificador de POS enviado.<br><br>';
if($status_code=='104')$msg='A operação não é permitida ao Utilizador devido a problemas no seu serviço MB WAY.<br><br>';
if($status_code=='110')$msg='Tipo de Alias não autorizado para o Comerciante<br><br>';
if($status_code=='111')$msg='O formato do Alias enviado não corresponde ao Tipo de Aliasdefinido.<br><br>';
if($status_code=='112')$msg='O Alias já foi criado anteriormente.<br><br>';
if($status_code=='113')$msg='O Alias usado como identificador não foi encontrado.<br><br>';
if($status_code=='120')$msg='Identificação de operação financeira duplicada<br><br>';
if($status_code=='121')$msg='O tipo de operação financeira enviada não foi encontrado.<br><br>';
if($status_code=='122')$msg='Operação não permitida - Verifique o estado do seu serviço.<br><br>';
if($status_code=='123')$msg='Operação de Referência não encontrada ou parâmetros enviados incorretos.<br><br>';
if($status_code=='124')$msg='Não foi encontrada nenhuma Operação Financeira efetuada nos últimos 30 minutos para o Utilizador indicado.<br><br>';

// echo '<h2>Request</h2><pre>' . htmlspecialchars($soap_client->request, ENT_QUOTES) . '</pre>';
// echo '<h2>Response</h2><pre>' . htmlspecialchars($soap_client->response, ENT_QUOTES) . '</pre>';
// echo '<h2>Debug</h2><pre>' . htmlspecialchars($soap_client->getDebug(), ENT_QUOTES) . '</pre>';


?><div style="margin:auto"><img border=0 src="mbway.jpg"></div>
<div style="text-align:center;font-family:arial;font-size:12px"><?=$msg?></div>
<div style="margin:auto"><img border=0 src="mbway_example.jpg"></div>
