<?
//die();

	include "settings.inc.php";
	include 'lib/nusoap.php';

	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

	send_sms_('914931936','','teste');
	//send_sms_('916871471','','teste');

	// phpinfo();




function send_sms_($numero = 0, $numero2 = 0, $sms = '')
{
	if(file_exists('/home/dotbright/public_html/rmas/rmas/sms_nos'))return send_sms_nos_($numero, $numero2, $sms);

	$mobile = 0;
	if(valida_telemovel($numero))
	{
		$mobile = trim($numero);
	}
	elseif(valida_telemovel($numero2))
	{
		$mobile = trim($numero2);
	}

	if($mobile > 0 && trim($sms) != '' && strlen($mobile) == 9)
	{
		$params = array('apikey'    => '6166d69692d12d13dde92617e541b144b1f1907c',
                    'listID'    => '1',
                    'cellphone' => '351-'.trim($mobile),
                    // 'email'    	=> 'address@mailserver.com',
                    'message'   => trim($sms),
                    'subject'   => 'new sms message',
                    'from'    	=> 'DOT BRIGHT');


		// using Soap with SoapClient
		$client = new SoapClient('http://api.e-goi.com/v2/soap.php?wsdl');
		$response  = $client->sendSMS($params);


		echo "====== REQUEST HEADERS =====" . PHP_EOL;
	    var_dump($client->__getLastRequestHeaders());
	    echo "========= REQUEST ==========" . PHP_EOL;
	    var_dump($client->__getLastRequest());
	    echo "========= RESPONSE =========" . PHP_EOL;
	    var_dump($response);


	}
}

function send_sms_nos_($numero = 0, $numero2 = 0, $mensagem = '')
{
	$mobile = 0;
	$mobile = trim($numero);
	if($mobile > 0 && strlen($mobile) >= 9 && trim($mensagem) != '')
	{
		// echo "entrou";
		$endpoint = 'http://smspro.nos.pt/SmsPro/smsprows.asmx';
	    $result = array(
	        'error' => '',
	        'data'  => array()
	    );

	    $mensagem = trim($mensagem);

	    $soapHeader = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body>';
	    $soapFooter = '</s:Body></s:Envelope>';
	    $soapstr = '';
	    $soapstr .='<SendSMSSelId xmlns="http://www.outsystems.com" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';

			$soapstr .='<TenantName>dotbright1</TenantName>';
			$soapstr .='<strUsername>DOTBRIGHTLDA</strUsername>';
			$soapstr .='<strPassword>KgzaCr9u</strPassword>';
			$soapstr .='<MsisdnList>'.$numero.'</MsisdnList>';
			$soapstr .='<strMessage>'.$mensagem.'</strMessage>';
			$soapstr .='<strIdentifier>dotbright1</strIdentifier>';/* numero original */
			$soapstr .='<bolInSender>true</bolInSender>';


	    $soapstr .= '</SendSMSSelId>';

	    // echo formatXmlString($soapstr);
		if ($soapstr == '')
	    {
	        echo 'Não foram passados parametros';
	        exit;
	    }
	    else
	    {
	        $soapstr = $soapHeader . $soapstr . $soapFooter;

			$client = new nusoap_client($endpoint, false, false, false, false, false, 0, 300); //se nao existir temos de fazer include


			



	        $err= $client->getError();
	        if ($err)
	        {
	        	echo '<pre>';
		        print_r($err);
		        echo '</pre>';

				$result['error'] = $err;
	            return $result;
	        }

	        $client->setUseCurl(0);
	        $client->soap_defencoding = 'UTF-8';
	        $client->decode_utf8 = false;
	        $client->encode_utf8 = true;
	        $client->useHTTPPersistentConnection();
	        $soapResult = $client->send($soapstr, 'http://SmsPro/SMSPROWS/SendSMSSelId');

	        //nota, nao posso ter echos nesta variavel ou vai ter influencia em todo o sistema
	        echo '<pre>';
	       	 print_r($soapResult);
	        echo '</pre>';


	        $result = $soapResult;
	        if ($client->fault)
	        {
	            $result['error'] = print_r($soapResult, true);
	        }
	        else
	        {
	            $err = $client->getError();

				if ($err)
				{
					echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
				} 
	        }
	    }
	}
	if($soapResult['ReturnCode'] == '999') return 1;
	else return 0;
}

?>