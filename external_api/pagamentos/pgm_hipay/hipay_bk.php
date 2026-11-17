<?php
$path = dirname( __FILE__ ).'/../../core/';
include $path.'core_files.php';
require_once dirname( __FILE__ ).'/lib/nusoap.php';

error_reporting(E_ALL);
ini_set('display_errors',1);



$rs_metodo = new DataObj;
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

$sql = "SELECT Sandbox, Username, Password, Account_id, Merchant_site_id FROM metodos_pagamento WHERE Tipo=6";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
$sandbox = 0;

if (!$rs_metodo->EOF())
{
	$Username = $rs_metodo->db_data["Username"];
	$Password = $rs_metodo->db_data["Password"];
	$Account_id = $rs_metodo->db_data["Account_id"];
	$Merchant_site_id = $rs_metodo->db_data["Merchant_site_id"];
	$sandbox = $rs_metodo->db_data['Sandbox'];
}
$rs_metodo->db_close();


if(isset($_GET['id_enc']) && $Username != '' && $Password != '' && $Account_id != '' && $Merchant_site_id != '' && intval($_GET['id_enc']) > 0)
{
	
	$id_enc = intval($_GET['id_enc']);
	$rs = new DataObj;
	$sql_all = "SELECT TotalDocComIva, enc_Email FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
	// echo $sql_all.'<br>';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
	if (!$rs->EOF())
	{
		$valor = $rs->db_data['TotalDocComIva'];
		$enc_Email = $rs->db_data['enc_Email'];
	}
	$rs->db_close();
	
	
	
	
	define('ENVIRONMENT', (($sandbox == 1) ? 0 : 1));

	if (ENVIRONMENT)
		$ws_url = "https://ws.hipay.com/";
	else
		$ws_url = "https://test-ws.hipay.com/";

	$ws_url .= "soap/payment-v2/generate?wsdl";


	// ACTION 2: Redirect = 1, Iframe = 2 or Info = 3
	define('RESPONSE_ACTION', "3");


	// ACTION 3:  webservice and account credentials - please check Hipay Wallet Account (API access)
	$wsLogin = $Username;
	$wsPassword = $Password;
	
	$wsLogin = '2d07f5a394f405bf15b1b2ef6781f2b8';
	$wsPassword = '34e1d8bc7e4d84ed0e4de27d1aa64a91';
	
	$websiteId = $Merchant_site_id;
	$websiteCategory = 233;
	$shopId = $Account_id;


	try {

		// $client = new SoapClient($ws_url);
		$client = new nusoap_client($ws_url, true);
		$err = $client->getError();
		if ($err) 
		{
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}

		$ip=$_SERVER['REMOTE_ADDR'];
		$currentDate = date('Y-m-dTH:i:s');
		$uid = md5($currentDate.uniqid());
		

		$parameters = new stdClass(); 
		$parameters->parameters = array(
		// $parameters = array(
			'wsLogin' => trim($wsLogin),
			'wsPassword' => trim($wsPassword),
			'websiteId' => trim($websiteId),
			'categoryId' => trim($websiteCategory),
			'currency' => 'EUR',
			'amount' => number_format($valor , 2, '.', ''),
			'rating' => 'ALL',
			'locale' => 'pt_PT',
			'customerIpAddress' => $ip,
			'merchantReference' => $id_enc,
			'description' => 'Encomenda: '.$id_enc,
			'executionDate' => $currentDate,
			'manualCapture' => 0,
			'customerEmail' => $enc_Email,
			'merchantComment' => 'no comments',
			'emailCallback' => $GLOBALS['Default_notifications_email'],
			'urlCallback' => $GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=success',
			'urlAccept' => $GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=success',
			'urlDecline' => $GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=cancel',
			'urlCancel' => $GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=cancel',
			'urlLogo' =>  $GLOBALS['site_http_url'] .'layout/'.PROJETO.'/logos/logo_top.png',
			'shopId' => $shopId,
			'freedata' => '<freeData><item><key>keyChallenge</key><value>'.$uid.'</value></item></freeData>'	
		);					

			print_r($parameters);
		// $result = $client->generate($parameters);
		
		$result = $client->call('generate', array('parameters' => $parameters));
		
// echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
// echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
// echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
		
		// print_r($client->response);
		
		// $xml = simplexml_load_string($client->response, "SimpleXMLElement", LIBXML_NOCDATA);
		// $xml = new SimpleXMLElement($client->response);
		// $xml->addAttribute('version', '1.0');
		// $json = json_encode($xml);
		// $array = json_decode($json,TRUE);
		
		print_r($client->response);
		exit;
		if ($result->generateResult->code == 0) 
		{
			switch (RESPONSE_ACTION) 
			{
				case '1':
					header("location:" . $result->generateResult->redirectUrl );
					break;
				
				case '2':
					echo "<iframe frameborder=0 width=1000 height=1500 src='".$result->generateResult->redirectUrl. "'></iframe>";
					break;

				case '3':
				default:
					echo $result->generateResult->redirectUrl;
					break;
			}
		} else {
			echo "Error: " . $result->generateResult->code . ": " . $result->generateResult->description;
		}


	} catch (Exception $e) {
		var_dump($e);
	}

}

/* 
require_once ('mapi_package.php');
// if there is not action variable, set the default action of 'process'
if (empty($_GET['action'])) $_GET['action'] = 'process';

switch ($_GET['action']) 
{
	case 'process':
		if ($id_enc > 0) 
		{
			$params = new HIPAY_MAPI_PaymentParams();
			//The Hipay platform connection parameters. This is not the information used to connect to your Hipay //account, but the specific login and password used to connect to the payment platform.
			//The login is the ID of the hipay merchant account receiving the payment, and the password is
			//the « merchant password » set within your Hipay account.
			$params->setLogin($Username,$Password);
			//$params->setLogin('115119', '201311');
			// The amounts will be credited to account 59118, except the taxes which will be credited to account 59119
			$params->setAccounts($Account_id,$Account_id);
			//$params->setAccounts(115119, 115119);
			// The payment interface will be in International French by default
			$params->setDefaultLang('pt_PT');
			// The interface will be the Web interface
			$params->setMedia('WEB');
			//The order content is intended for people at least 16 years old.
			$params->setRating('+16');
			// This is a single payment
			$params->setPaymentMethod(HIPAY_MAPI_METHOD_SIMPLE);
			// The capture take place immediately
			$params->setCaptureDay(HIPAY_MAPI_CAPTURE_IMMEDIATE);
			// The amounts are expressed in Euros, this has to be the same currency as the merchant's account.
			$params->setCurrency('EUR');
			// The merchant-selected identifier for this order is REF6522
			$params->setIdForMerchant($id_enc);
			//Two data elements of type key=value are declared and will be returned to the merchant after the payment in the notification data feed [C].
			$params->setMerchantDatas('id_enc', $id_enc);
			//$params->setMerchantDatas('credit', '10');
			// This order relates to the web site which the merchant declared in the Hipay platform.
			// The I.D. assigned to this website is ?9'
			$params->setMerchantSiteId($Merchant_site_id);
			//$params->setMerchantSiteId(6515);
			// If the payment is accepted, the user will be redirected to this page
			$params->setURLOk($GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=success');
			// If the payment is refused, the user will be redirected to this page
			$params->setUrlNok($GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=cancel');
			// If the user cancels the payment, he will be redirected to this page
			$params->setUrlCancel($GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=cancel');
			// The email address used to send the notifications, on top of the http notifications.
			// cf chap 19 : RECEIVING A RESULTS NOTIFICATION ABOUT A PAYMENT ACTION
			$params->setEmailAck($GLOBALS['Default_SMTP_Email']);
			// The merchant's site will be notified of the result of the payment by a call to the script
			// "listen_hipay_notification.php?
			// cf chap 19 : RECEIVING A RESULTS NOTIFICATION ABOUT A PAYMENT ACTION
			$params->setUrlAck($GLOBALS['site_http_url'] . 'external_api/pgm_hipay/hipay.php?action=notification');
			// The background color of the interface will be #FFFFFF (default color recommended)
			$t = $params->setBackgroundColor('#FFFFFF');
			$t = $params->check();
			if (!$t) {
				echo "An error occurred while creating the paymentParams object";
				exit;
			}
			$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
			$rs = new DataObj;
			$sql_all = "SELECT ID_Cliente, TotalDocComIva, MOEDA FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
			if (!$rs->EOF()) {
				$ID_Cliente = $rs->db_data['ID_Cliente'];
				$valor = $rs->db_data['TotalDocComIva'];
				$MOEDA = $rs->db_data['MOEDA'];
			}
			$rs->db_close();
			$rs2 = new DataObj;
			$sql_all = "SELECT Nome, Username, Pais FROM clientes WHERE clientes.ID='" . $ID_Cliente . "'";
			$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
			if (!$rs2->EOF()) {
				$firstname = $rs2->db_data['Nome'];
				$email = $rs2->db_data['Username'];
				$Pais = $rs2->db_data['Pais'];
			}
			$rs2->db_close();
			$valor = number_format($valor, 2, ".", "");
			//criar linha de produto [apenas vai ter um com o valor total da encomenda]
			$item1 = new HIPAY_MAPI_Product();
			$item1->setName('Cokidy Nº:' . $id_enc);
			$item1->setquantity(1);
			$item1->setPrice($valor);
			$item1->setCategory(5);
			$t = $item1->check();
			if (!$t) {
				echo "An error occurred while creating a product object";
				exit;
			}
			$order = new HIPAY_MAPI_Order();
			// Order title and information
			$order->setOrderTitle('Cokidy Nº:' . $id_enc);
			// The order category is 3 (Books)
			// Refer to annex 7 to see how to find out what category your site belongs to.
			//$order->setOrderCategory(610);
			$order->setOrderCategory(618);
			// The shipping costs are 1.50 Euros excluding taxes, and $tax1 is applied
			$order->setShipping(0, array());
			// The insurance costs are 2 Euros excluding taxes, and $tax1 and $tax3 are applied
			$order->setInsurance(0, array());
			// The fixed costs are 2.25 Euros excluding taxes, and $tax3 is applied to this amount
			$order->setFixedCost(0, array());
			$t = $order->check();
			if (!$t) {
				echo "An error occurred while creating a product object";
				exit;
			}

			try {
				$payment = new HIPAY_MAPI_SimplePayment($params, $order, array(
					$item1
				));
			}
			catch(Exception $e) {
				echo "Error" . $e->getMessage();
			}
			$xmlTx = $payment->getXML();
			$output = HIPAY_MAPI_SEND_XML::sendXML($xmlTx);
			$r = HIPAY_MAPI_COMM_XML::analyzeResponseXML($output, $url, $err_msg);
			if ($r === true) {
				// The internet user is sent to the URL indicated by the Hipay platform
				header('Location: ' . $url);
				// echo $url;

			}
			else {
				// Une erreur est intervenue
				echo 'erro' . $err_msg;
				// $url_error = "/error.html?;
				//header('Location: '.$url_error) ;

			}
		}

		break;

	case 'success':
		$page_id = 130652;
		$path = '../../core/';
		require_once $path . 'core_files.php';
		require_once '../../layout/header.php';
		require_once '../../layout/template_topo.php';
		show_alerta('<span style="color:#4f7e9e">' . $GLOBALS['key_shop_msg1'][$GLOBALS['site_lang']] . '</span><br /><br />', "msg");
		unset($_SESSION['ss_shop']);
		unset($_SESSION['ss_n_enc']);
		$shop = array();
		$n_enc = 0;
		require_once '../../layout/template_bottom.php';

		break;

	case 'cancel':
		$page_id = 130652;
		$path = '../../core/';
		require_once $path . 'core_files.php';
		require_once '../../layout/header.php';
		require_once '../../layout/template_topo.php';
		show_alerta('<span style="color:#FF0000">' . $GLOBALS['key_shop_msg1_c'][$GLOBALS['site_lang']] . '</span><br /><br />', "msg");
		require_once '../../layout/template_bottom.php';

		break;

	case 'notification':
		if (isset($_POST['xml'])) {
			$r = HIPAY_MAPI_COMM_XML::analyzeNotificationXML($_POST['xml'], $operation, $status, $date, $time, $transid, $amount, $currency, $idformerchant, $merchantdatas, $emailClient, $subscriptionId, $refProduct);
			// An error occurs
			if ($r === false) {
				// Error log, in a text file on the server
				//file_put_contents("result.txt", "Erreur");

			}
			else {
				$path = '/home/cokidyco/public_html/core/';
				require_once $path . 'core_files.php';
				$id_enc2 = $merchantdatas['id_enc'];
				if (isset($payment_status) && $payment_status == 'ok') {
					//pagamento realizado com sucesso
					defineNovoEstado($id_enc2, 3);
				}
				elseif (isset($payment_status) && $payment_status == 'nok') {
					//pagamento falhou
					defineNovoEstado($id_enc2, 5);
				}
				elseif (isset($payment_status) && $payment_status == 'cancel') {
					//pagamento cancelado
					defineNovoEstado($id_enc2, 4);
				}
			}
		}

		break;
} */

function defineNovoEstado($id_enc, $estado)
{


	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$sql = "UPDATE encomendas SET Estado=".$estado." WHERE ID='" . $id_enc . "'";
	$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	if ($res) {
		$enc_info = get_purchase_info($id_enc);
		$prods = '';
		$total = 0;
		$total2 = 0;




		if(!$estado) $estado_str='Pendente';
        if($estado==1) $estado_str='Aceite';
        if($estado==2) $estado_str='Cancelada';
		if($estado==3) $estado_str='Pagamento Aceite';
		if($estado==4) $estado_str='Pagamento Cancelado';
		if($estado==5) $estado_str='Pagamento Rejeitado';



		$body = array
				(
					'[n_enc]' 				=> $id_enc,
					'[cart_data]' 			=> '',
					'[delivery_type]' 		=> $enc_info['enc_cabecalho']['enc_tipo_transportadora'],
					'[metodo_pagamento]' 	=> $enc_info['enc_cabecalho']['enc_MetodoPagamento'],
					'[name_del]' 			=> $enc_info['enc_cabecalho']['enc_Nome_ent'],
					'[surname_del]' 		=> $enc_info['enc_cabecalho']['enc_Apelido_ent'],
					'[address_del]' 		=> $enc_info['enc_cabecalho']['enc_Morada_ent'],
					'[zipcode_del]' 		=> $enc_info['enc_cabecalho']['enc_Codigo_Postal_ent'],
					'[country_del]' 		=> $enc_info['enc_cabecalho']['enc_Pais_ent'],
					'[email_del]' 			=> $enc_info['enc_cabecalho']['enc_Email_ent'],
					'[ic_del]' 				=> $enc_info['enc_cabecalho']['enc_Nif_ent'],
					'[name_bil]' 			=> $enc_info['enc_cabecalho']['enc_Nome'],
					'[surname_bil]' 		=> $enc_info['enc_cabecalho']['enc_Apelido'],
					'[address_bil]' 		=> $enc_info['enc_cabecalho']['enc_Morada'],
					'[zipcode_bil]' 		=> $enc_info['enc_cabecalho']['enc_Codigo_Postal'],
					'[country_bil]' 		=> $enc_info['enc_cabecalho']['enc_Pais'],
					'[email_bil]' 			=> $enc_info['enc_cabecalho']['enc_Email'],
					'[ic_bil]' 				=> $enc_info['enc_cabecalho']['enc_Nif'],
					'[new_purchase_state]' 	=> $estado_str
				);


				$message = template_email($GLOBALS['template_encomenda_admin'], $body);





			$message['body'].='<table id="cost_'.$id_enc.'" width="727" border="0" cellpadding="0" cellspacing="0" style="background-color:#b4e1e6"><tr><td>';
			$message['body'].='<table width="100%" border="0" cellspacing="10" cellpadding="0"><tr><td bgcolor="#FFFFFF" style="font-size:12px; line-height:30px; font-weight:normal;color:#939598;text-transform:uppercase;font-weight:bold;font-family:Arial;font-smooth:always;">';

			$total_fim=0;
			for ($a = 0; $a < count($enc_info['enc_produtos']); $a++) {
				$prod_ID = $enc_info['enc_produtos'][$a]['prod_ID'];
				$prod_Nome = $enc_info['enc_produtos'][$a]['prod_Nome'];
				$prod_Qtd = $enc_info['enc_produtos'][$a]['prod_Qtd'];
				$prod_Valor = $enc_info['enc_produtos'][$a]['prod_Valor'];
				$prod_Descricao = $enc_info['enc_produtos'][$a]['prod_Descricao'];
				$prod_Chave = $enc_info['enc_produtos'][$a]['prod_Key'];

				$referencia = get_modelo_product($prod_Chave);

				$message['body'].='<tr><td bgcolor="#FFFFFF" style="font-size:12px; line-height:30px; font-weight:normal;color:#939598;text-transform:uppercase;font-weight:bold;font-family:Arial;font-smooth:always;padding-left:10px;vertical-align:middle">';
				$message['body'].=$referencia.' - ';
				$message['body'].=$prod_Qtd;
				$message['body'].='</td>';
				$message['body'].='<td id="cost_val_'.$prod_ID.'" width="150" height="29" bgcolor="#FFFFFF" align="right" style="font-size:16px; line-height:28px; color:#289BA2;padding-right:10px;vertical-align:middle">'.numberformat($prod_Valor * $prod_Qtd).'</td></tr>';

				$total_fim+=($prod_Valor * $prod_Qtd);
			}

			$message['body'].='<tr>
			<td align="right" style="color:#1f8c93;font-size:14px;line-height:21px">'.$GLOBALS['key_subtotal'][$GLOBALS['site_lang']].'</td>
			<td height="29" bgcolor="#FFFFFF" align="right" style="font-size:16px; line-height:28px; color:#289BA2;text-transform:uppercase;font-weight:bold;font-family:Arial;padding-right:10px;vertical-align:middle">'.numberformat($total_fim).'</td>
			</tr>
			<tr>
			<td align="right" style="color:#1f8c93;font-size:14px; font-weight:normal;text-transform:uppercase;font-weight:bold;font-family:Arial">'.$GLOBALS['key_delivery_charges'][$GLOBALS['site_lang']].'</td>
			<td height="29" bgcolor="#FFFFFF"align="right" style="font-size:16px; line-height:28px; color:#289BA2;text-transform:uppercase;font-weight:bold;font-family:Arial;font-smooth:always;padding-right:10px;vertical-align:middle">'.numberformat($enc_info['enc_cabecalho']['enc_Portes']).'</td>
			</tr>
			<tr>
			<td align="right" style="color:#1f8c93;font-size:14px; font-weight:normal;text-transform:uppercase;font-weight:bold;font-family:Arial">'.$GLOBALS['key_total_cost'][$GLOBALS['site_lang']].'</td>
			<td height="29" bgcolor="#FFFFFF" align="right" style="font-size:19px; color:#289BA2;text-transform:uppercase;font-weight:bold;font-family:Arial;padding-right:10px;vertical-align:middle">'.numberformat($enc_info['enc_cabecalho']['enc_TotalDocSemIva']).'</td>
			</tr>';
			$message['body'].='</table>';
			$message['body'].='</td></tr></table>';


		send_email($GLOBALS['Default_SMTP_Email'], $message['subject'], $message['body']);
		//send_email('joao@memogadget.com', $message['subject'], $message['body']);


		$myFile = "/home/cokidyco/public_html/files/cp_shop_" . $id_enc . ".txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $message['body']);
		fclose($fh);


		$body = array
				(
					'[n_enc]' 				=> $id_enc,
					'[name]' 				=> $enc_info['enc_cabecalho']['enc_Nome_ent'],
					'[surname]' 			=> $enc_info['enc_cabecalho']['enc_Apelido_ent'],
					'[email]' 				=> $enc_info['enc_cabecalho']['enc_Email_ent'],
					'[delivery_address]' 	=> $enc_info['enc_cabecalho']['enc_Morada_ent'],
					'[delivery_codpos]' 	=> $enc_info['enc_cabecalho']['enc_Codigo_Postal_ent'],
					'[delivery_country]' 	=> $enc_info['enc_cabecalho']['enc_Pais_ent'],
					'[cart_data]' 			=> '',
					'[delivery_type]' 		=> $enc_info['enc_cabecalho']['enc_tipo_transportadora'],
					'[metodo_pagamento]' 	=> $enc_info['enc_cabecalho']['enc_MetodoPagamento']
				);


				$message = template_email($GLOBALS['template_order2'], $body);





			$message['body'].='<table id="cost_'.$id_enc.'" width="727" border="0" cellpadding="0" cellspacing="0" style="background-color:#b4e1e6"><tr><td>';
			$message['body'].='<table width="100%" border="0" cellspacing="10" cellpadding="0"><tr><td bgcolor="#FFFFFF" style="font-size:12px; line-height:30px; font-weight:normal;color:#939598;text-transform:uppercase;font-weight:bold;font-family:Arial;font-smooth:always;">';

			$total_fim=0;
			for ($a = 0; $a < count($enc_info['enc_produtos']); $a++) {
				$prod_ID = $enc_info['enc_produtos'][$a]['prod_ID'];
				$prod_Nome = $enc_info['enc_produtos'][$a]['prod_Nome'];
				$prod_Qtd = $enc_info['enc_produtos'][$a]['prod_Qtd'];
				$prod_Valor = $enc_info['enc_produtos'][$a]['prod_Valor'];
				$prod_Descricao = $enc_info['enc_produtos'][$a]['prod_Descricao'];
				$prod_Chave = $enc_info['enc_produtos'][$a]['prod_Key'];

				$message['body'].='<tr><td bgcolor="#FFFFFF" style="font-size:12px; line-height:30px; font-weight:normal;color:#939598;text-transform:uppercase;font-weight:bold;font-family:Arial;font-smooth:always;padding-left:10px;vertical-align:middle">';
				$message['body'].=$prod_Nome.' - ';
				if($prod_Descricao!=''){$message['body'].=$prod_Descricao.' - ';}
				if(products_info_cart($prod_ID,$prod_Chave)!=''){$message['body'].='<b>'.products_info_cart($prod_ID,$prod_Chave).'</b> - '; echo '<b>'.products_info_cart($prod_ID,$prod_Chave).'</b> - ';}
				$message['body'].=$prod_Qtd;
				$message['body'].='</td>';
				$message['body'].='<td id="cost_val_'.$prod_ID.'" width="150" height="29" bgcolor="#FFFFFF" align="right" style="font-size:16px; line-height:28px; color:#289BA2;padding-right:10px;vertical-align:middle">'.numberformat($prod_Valor * $prod_Qtd).'</td></tr>';

				$total_fim+=($prod_Valor * $prod_Qtd);
			}

			$message['body'].='<tr>
			<td align="right" style="color:#1f8c93;font-size:14px;line-height:21px">'.$GLOBALS['key_subtotal'][$GLOBALS['site_lang']].'</td>
			<td height="29" bgcolor="#FFFFFF" align="right" style="font-size:16px; line-height:28px; color:#289BA2;text-transform:uppercase;font-weight:bold;font-family:Arial;padding-right:10px;vertical-align:middle">'.numberformat($total_fim).'</td>
			</tr>
			<tr>
			<td align="right" style="color:#1f8c93;font-size:14px; font-weight:normal;text-transform:uppercase;font-weight:bold;font-family:Arial">'.$GLOBALS['key_delivery_charges'][$GLOBALS['site_lang']].'</td>
			<td height="29" bgcolor="#FFFFFF"align="right" style="font-size:16px; line-height:28px; color:#289BA2;text-transform:uppercase;font-weight:bold;font-family:Arial;font-smooth:always;padding-right:10px;vertical-align:middle">'.numberformat($enc_info['enc_cabecalho']['enc_Portes']).'</td>
			</tr>
			<tr>
			<td align="right" style="color:#1f8c93;font-size:14px; font-weight:normal;text-transform:uppercase;font-weight:bold;font-family:Arial">'.$GLOBALS['key_total_cost'][$GLOBALS['site_lang']].'</td>
			<td height="29" bgcolor="#FFFFFF" align="right" style="font-size:19px; color:#289BA2;text-transform:uppercase;font-weight:bold;font-family:Arial;padding-right:10px;vertical-align:middle">'.numberformat($enc_info['enc_cabecalho']['enc_TotalDocSemIva']).'</td>
			</tr>';
			$message['body'].='</table>';
			$message['body'].='</td></tr></table>';


			send_email($enc_info['enc_cabecalho']['enc_Email'], $message['subject'], $message['body']);
	}
}
?>

