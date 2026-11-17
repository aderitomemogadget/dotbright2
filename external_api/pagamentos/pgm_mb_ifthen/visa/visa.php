<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';

	$id_enc=intval($_GET['id_enc']);
	$RequestId = $_GET['requestId'];
	$GLOBALS['CCARD_KEY'] = 'FHP-338990';

	function sendVISARequest($CCARD_KEY='', $orderId='', $valor='')
	{
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="INSERT INTO ifthen_visa_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$orderId."','".date("Y-m-d H:m:i")."', 'Valor = $valor', '0','')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);

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

			$sql="UPDATE ifthen_visa_logs set Erros='".addslashes($response)."' WHERE ID = '$ID_Log' ";
			$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);
		    return $responseData;

        }
        else return 'Informação incompleta para poder invocar o serviço VISA!';


        //return $tabela;
	}

	if ($id_enc > 0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;
		$sql_all = "SELECT TotalDocComIva,enc_Email FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())$valor=$rs->db_data['TotalDocComIva'];
		$rs->db_close();
		$valor=number_format($valor,2,'.','');
		$res = sendVISARequest($GLOBALS['CCARD_KEY'], $id_enc, $valor);
		if($res['Message']=='Success')
		{
			$sql="UPDATE ifthen_visa_logs set RequestId='".$res['RequestId']."' WHERE ID_Encomenda = '$id_enc' ";
			$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);


			echo '<script>window.open("'.$res['PaymentUrl'].'","_top")</script>';
		}
	}
?>