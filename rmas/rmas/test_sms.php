<?
//die();

	include "settings.inc.php";
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);

	send_sms_('914931936','','teste');

phpinfo();

	function send_sms_($numero = 0, $numero2 = 0, $sms = '')
{
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

?>