<?php
$path = dirname(__FILE__) . '/../../../core/';
include $path . 'core_files.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

$rs_metodo = new DataObj();
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

$sandbox = 0;
$Username = '';
$Password = '';
$Account_id = '';
$Merchant_site_id = '';

$sql = "SELECT Sandbox, Username, Password, Account_id, Merchant_site_id FROM metodos_pagamento WHERE Tipo=6";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF()) {
	$Username = $rs_metodo->db_data["Username"];
	$Password = $rs_metodo->db_data["Password"];
	$Account_id = $rs_metodo->db_data["Account_id"];
	$Merchant_site_id = $rs_metodo->db_data["Merchant_site_id"];
	$sandbox = $rs_metodo->db_data['Sandbox'];
}
$rs_metodo->db_close();

if (empty($_GET['action'])) $_GET['action'] = 'process';

switch ($_GET['action']) {
	case 'process':

		if (isset($_GET['id_enc']) && $Username != '' && $Password != '' && $Account_id != '' && $Merchant_site_id != '' && intval($_GET['id_enc']) > 0) 
		{

			$id_enc = intval($_GET['id_enc']);
			if(!isset($_SESSION['hipay_encid'])){
				$_SESSION['hipay_encid'] = $id_enc;
			}
			$rs = new DataObj();
			$sql_all = "SELECT TotalDocComIva, enc_Email FROM encomendas WHERE encomendas.ID='$id_enc'";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
			if (!$rs->EOF()) {
				$valor = $rs->db_data['TotalDocComIva'];
				$enc_Email = $rs->db_data['enc_Email'];


				if ($sandbox == 0)
					$ws_url = "https://ws.hipay.com/";
				else
					$ws_url = "https://test-ws.hipay.com/";

				$ws_url .= "soap/payment-v2/generate?wsdl";


				// ACTION 2: Redirect = 1, Iframe = 2 or Info = 3
				define('RESPONSE_ACTION', "1");


				// ACTION 3:  webservice and account credentials - please check Hipay Wallet Account (API access)
				$wsLogin = $Username;
				$wsPassword = $Password;
				$websiteId = $Merchant_site_id;
				$websiteCategory = 233;
				$shopId = $Account_id;

				try {

					$client = new SoapClient($ws_url);

					$ip = $_SERVER['REMOTE_ADDR'];
					$currentDate = date('Y-m-dTH:i:s');
					$uid = md5($currentDate . uniqid());

					$parameters = new stdClass();
					$parameters->parameters = array(
						// $parameters = array(
						'wsLogin' => trim($wsLogin),
						'wsPassword' => trim($wsPassword),
						'websiteId' => trim($websiteId),
						'categoryId' => trim($websiteCategory),
						'currency' => 'EUR',
						'amount' => number_format($valor, 2, '.', ''),
						'rating' => 'ALL',
						'locale' => 'pt_PT',
						'customerIpAddress' => $ip,
						'merchantReference' => $id_enc,
						'description' => 'Encomenda: ' . $id_enc,
						'executionDate' => $currentDate,
						'manualCapture' => 0,
						'customerEmail' => $enc_Email,
						'merchantComment' => 'no comments',
						'emailCallback' => $GLOBALS['Default_notifications_email'],
						'urlCallback' => SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_HIPAY]['url'].'hipay.php?action=ipn',
						'urlAccept' => SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_HIPAY]['url'].'hipay.php?action=success',
						'urlDecline' => SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_HIPAY]['url'].'hipay.php?action=cancel',
						'urlCancel' => SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_HIPAY]['url'].'hipay.php?action=cancel',
						'urlLogo' => $GLOBALS['site_http_url'] . 'layout/' . PROJETO . '/logos/logo_top.png',
						'shopId' => $shopId,
						'freedata' => '<freeData><item><key>merchantReference</key><value>' . $id_enc . '</value></item></freeData>'
					);

					$result = $client->generate($parameters);

					if ($result->generateResult->code == 0) {
						switch (RESPONSE_ACTION) {
							case '1':
								header("location:" . $result->generateResult->redirectUrl);
								break;

							case '2':
								echo "<iframe frameborder=0 width=1000 height=1500 src='" . $result->generateResult->redirectUrl . "'></iframe>";
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
			$rs->db_close();
		}
		break;
	case 'success':
		// Order was successful...
		$enc_id = $_SESSION['hipay_encid'];
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='Pending',Estado='5' WHERE ID='$enc_id'";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

		$referer = $GLOBALS['site_http_url'];
		echo drawRedirectPage('' . $GLOBALS['key_shop_msg1'][$GLOBALS['site_lang']] . '', $referer, 1000);
		unset($_SESSION['ss_shop']);
		unset($_SESSION['ss_n_enc']);
		$shop = array();
		$n_enc = 0;

		if ($GLOBALS['module_voucher'] == 1) {
			unset($_SESSION['ss_voucher']);
			unset($_SESSION['ss_n_voucher']);
			$voucher = array();
			$n_voucher = 0;
		}
		break;
	case 'cancel':
		$referer = $GLOBALS['site_http_url'];

		$enc_id = $_SESSION['hipay_encid'];
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='Cancel',Estado='4' WHERE ID='$enc_id'";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

		if($dados=cliente_logado()){
			$referer=$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1&id_enc='.$enc_id;
		}

		echo drawRedirectPage(''.$GLOBALS['key_shop_msg1_c'][$GLOBALS['site_lang']].'>',$referer,1000);
		unset($_SESSION['ss_shop']);
		unset($_SESSION['ss_n_enc']);
		$shop=array(); $n_enc=0;

		if($GLOBALS['module_voucher']==1){
			unset($_SESSION['ss_voucher']);
			unset($_SESSION['ss_n_voucher']);
			$voucher=array(); $n_voucher=0;
		}

		break;
	case 'ipn':

		if (isset($_POST['xml'])) {

			$paymentOperation = $_POST['xml']['operation'];
			$paymentStatus = $_POST['xml']['status'];
			$id_enc = $_POST['xml']['merchantDatas']['merchantReference'];

			if ($paymentOperation == 'capture') {
				if ($paymentStatus == 'ok') {
					$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
					$sql = "UPDATE encomendas SET Encomenda_Paga=1,Encomenda_Status='$paymentStatus',Estado='3' WHERE ID='$id_enc'";
					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					enviarEmailCliente($id_enc);
				}
				elseif ($paymentStatus == 'nok' || $paymentStatus == 'cancel') {
					$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
					$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='$paymentStatus',Estado='5' WHERE ID='$id_enc'";
					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
			}
		}
		break;
}
