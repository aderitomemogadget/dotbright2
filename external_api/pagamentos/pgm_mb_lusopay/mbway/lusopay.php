<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';


	//INICIO TRATAMENTO DEFINICOES REGIONAIS
	function format_number($number)
	{
		$verifySepDecimal = number_format(99,2);
		$valorTmp = $number;
		$sepDecimal = substr($verifySepDecimal, 2, 1);
		$hasSepDecimal = True;
		$i=(strlen($valorTmp)-1);
		for($i;$i!=0;$i-=1)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==",")
			{
				$hasSepDecimal = True;
				$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
				break;
			}
		}
		if($hasSepDecimal!=True)
		{
			$valorTmp=number_format($valorTmp,2);
			$i=(strlen($valorTmp)-1);
			for($i;$i!=1;$i--)
			{
				if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==",")
				{
					$hasSepDecimal = True;
					$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
					break;
				}
			}
		}

		for($i=1;$i!=(strlen($valorTmp)-1);$i++)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)=="," || substr($valorTmp,$i,1)==" ")
			{
				$valorTmp = trim(substr($valorTmp,0,$i)).trim(substr($valorTmp,1+$i));
				break;
			}
		}

		if (strlen(strstr($valorTmp,'@'))>0)
		{
			$valorTmp = trim(substr($valorTmp,0,strpos($valorTmp,'@'))).trim($sepDecimal).trim(substr($valorTmp,strpos($valorTmp,'@')+1));
		}

		return $valorTmp;
	}

	/**
	 * Lusopay Call to Send MB Way request
	 *
	 * @param string $ent_chave Lusopay GUID.
	 * @param string $ent_nif User Vat Number.
	 * @param int    $order_id Order ID.
	 * @param float  $order_value Order Value.
	 * @param string $phoneNumber customer phoneNumber.
	 * @param string $send_email send payment notification
	 *
	 * @return string
	 */

	/**
	 * Dados para testes
	 *
	 * $ent_chave = '9CE4639A-5125-4B5E-8160-0C2DFD98AD8A';
	 * $ent_nif = '999999999';
	 * $phoneNumber = 920419530;
	 */

	 /**
	  * É necessário enviar-nos o IP para permitir fazer os testes.
	  */

	function sendMBWayRequest($ent_chave, $ent_nif, $ent_nif2, $order_id, $order_value, $phoneNumber, $send_email, $sanbox = 0)
	{
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="INSERT INTO lusopay_mbway_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$order_id."','".date("Y-m-d H:m:i")."', 'Tel = $phoneNumber | Valor = $order_value', '0','')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);

		//$order_value = sprintf("%01.2f", $order_value);
		$order_value = format_number($order_value);
		if (!is_null($ent_chave) && !is_null($ent_nif) && !is_null($order_id) && !is_null($order_value) && !is_null($phoneNumber) && !is_null($send_email))
		{
			$soapUrl = '';
			if($ent_nif === '999999999')
			{
				$soapUrl = 'https://services.lusopay.com/PaymentServices_test/PaymentServices.svc?wsdl';
			}
			else
			{
				$soapUrl = 'https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl';
			}

			$xml_post_string = '<?xml version="1.0" encoding="utf-8"
			?>
			<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:pay="http://schemas.datacontract.org/2004/07/PaymentServices">
	        <soapenv:Body>
	            <tem:sendMBWayRequest>
	                <tem:clientGuid>' . $ent_chave . '</tem:clientGuid>
	                <tem:vatNumber>' . $ent_nif2 . '</tem:vatNumber>
	                <tem:cellPhoneNumber>' . $phoneNumber . '</tem:cellPhoneNumber>
	                <tem:amount>' . numberformat( $order_value,4 ) . '</tem:amount>
	                <tem:externalReference>' . $order_id . '</tem:externalReference>
	                <tem:sendEmail>true</tem:sendEmail>
	            </tem:sendMBWayRequest>
	        </soapenv:Body>
	        </soapenv:Envelope>';
	        $headers = array(
                "Host: services.lusopay.com",
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: http://tempuri.org/IPaymentServices/sendMBWayRequest",
                "Content-length: " . strlen($xml_post_string),
            );
            //SOAPAction: your op URL
            $url = $soapUrl;
            // PHP cURL for http connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);

            curl_close($ch);
            $merchantOperationID = "/<a:merchantOperationID>(.*?)<\/a:merchantOperationID>/s";
        	$statusCode = "/<a:statusCode>(.*?)<\/a:statusCode>/s";
        	$statusMessage = "/<a:statusMessage>(.*?)<\/a:statusMessage>/s";
        	$timeStamp = "/<a:timeStamp>(.*?)<\/a:timeStamp>/s";
        	$token = "/<a:token>(.*?)<\/a:token>/s";
        	if (preg_match($merchantOperationID, $response, $merchantOperationID_value) &&
            	preg_match($statusCode, $response, $statusCode_value) &&
            	preg_match($statusMessage, $response, $statusMessage_value) &&
            	preg_match($timeStamp, $response, $timeStamp_value) &&
            	preg_match($token, $response, $token_value)
        	)
        	{
            	$resp[1] = $merchantOperationID_value[1];
            	$resp[2] = $statusCode_value[1];
            	$resp[3] = $statusMessage_value[1];
            	$resp[4] = $timeStamp_value[1];
            	$resp[5] = $token_value[1];
            	//Pode colocar aqui para inserir alguma informação numa tabela
        	}
        	else
        	{
            	$message = "/<a:message>(.*?)<\/a:message>/s";
            	if (preg_match($message, $response, $message_value) && preg_match($statusMessage, $response, $statusMessage_value))
            	{
                	echo $message_value[1] . "/n" . $statusMessage_value[1];
            	}
        	}
        	$currency = '€';
        	$tabela = '<div align="center">
        	<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc" align="center">
        	<tr>
        		<td style="font-size: small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por MB Way (by LUSOPAY)</center></td>
        	</tr>
        	<tr>
            	<td rowspan="3"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/logo_lusopaymbway.png" alt=""/></div></td>
            </tr>
            <tr>
                <td style="font-size: small; font-weight:bold; text-align:left">Telemóvel:</td>
                <td style="font-size: small; text-align:left">'. $phoneNumber .'</td>
            </tr>
            <tr>
            	<td style="font-size: small; font-weight:bold; text-align:left">Valor:</td>
                <td style="font-size: small; text-align:left">' . numberformat( $order_value ) . '</td>
            </tr>
            <tr>
                <td style="font-size: x-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>Caso não efetuar o pagamento dentro de 5 minutos o seu pedido será cancelado!</center></td>
            </tr>
        	</table></div>';
        }
        else return 'Informação incompleta para poder invocar o serviço MB Way!';


        return $tabela;
	}
	function getStatus()
	{

	}

	$rs_metodo = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

	$sql = "SELECT chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=".MP_TP_MB_LUSOPAY;
	$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$sandbox = 0;
	$conta_ifthen = '';
	if (!$rs_metodo->EOF())
	{
		$chave = $rs_metodo->db_data["chavebo"];
		$entidade = $rs_metodo->db_data["entidade"];
		$subentidade = $rs_metodo->db_data["subentidade"];
		$sandbox = $rs_metodo->db_data['sandox'];
	}
	$rs_metodo->db_close();

	if ($chave && $entidade && $subentidade && $_GET['id_enc'] > 0)
	{
		$id_enc = $_GET['id_enc'];
		$rs = new DataObj;
		$sql_all = "SELECT TotalDocComIva,enc_Email FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor=$rs->db_data['TotalDocComIva'];
			$enc_Email=$rs->db_data['enc_Email'];
			$enc_Telefone=$rs->db_data['enc_Telefone'];
		}
		$rs->db_close();

		if($_GET['mbway_tel'])$enc_Telefone=$_GET['mbway_tel'];

		/** Dados para testes*/
	 	/*$entidade = '999999999';
	 	$chave = '9CE4639A-5125-4B5E-8160-0C2DFD98AD8A';
	  	$enc_Telefone = 920419530;*/



		if($res = sendMBWayRequest($chave,$entidade,$subentidade,$id_enc,$valor,$enc_Telefone,$enc_Email, $sandbox))
		{
			echo $res;
		}
		else
		{
			echo 'Não foi possivel gerar referência.';
		}
	}
?>