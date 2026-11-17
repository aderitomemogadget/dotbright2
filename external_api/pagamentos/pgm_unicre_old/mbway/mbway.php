<?php
	$path=dirname( __FILE__ )."/../../../../core/";
	require_once $path.'core_files.php';


    $sanbox=1;
	if($sanbox==1)
	{		
	    //Credenciais de Teste:
	    $authentication_entityId = '8ac7a4c97b519e7c017b53a2a54a0eb8';
	    $Bearer = 'OGFjN2E0Yzk3NGQ0Yjg4YjAxNzRkOGI4MWMzNTA2Zjh8OFI4Y3F6emNXbg==';
	    $Entidade = '23867';
	}
	else
	{		
		//Credenciais de producao:
	    $authentication_entityId = '8ac9a4c77868c08d0178ad19d9052c65';
	    $Bearer = 'OGFjOWE0Y2Q3ODY4YzA4ZDAxNzhhZDE5ZDc5MzQ1NTR8OVRSUUdFQUtzUg==';
	    $Entidade = '25802';
	}


	$mbway_tel=$_GET['mbway_tel'];
	$mbway_tel=str_replace('+351','',$mbway_tel);
	$mbway_tel=str_replace('00351','',$mbway_tel);
	$id_enc=$_GET['id_enc'];
	$ID_Log=0;

	// https://sibs.docs.onlinepayments.pt/tutorials/integration-guide
    // Test mbway:
	function request($authentication_entityId,$Bearer,$valor=0,$tel='',$id_enc=0,$sanbox=0,$customer_name='')
	{
		global $ID_Log;
		$rs = new DataObj;
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$sql="INSERT INTO sibs_mbway_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$id_enc."','".date("Y-m-d H:m:i")."', 'Tel = $tel | Valor = $valor', '0','')";
		$res = Runquery($db_link,$GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);


	    if($sanbox==1)
	    {
	    	$url = "https://test.oppwa.com/v1/payments";
	    	$data = "entityId=".$authentication_entityId.
		                "&amount=$valor" .
		                "&currency=EUR" .
		                "&paymentType=DB" .
		                "&paymentBrand=MBWAY" .
		                "&virtualAccount.accountId=351#$tel" .
		                "&testMode=EXTERNAL" .
                    	"&customParameters[SIBS_ENV]=QLY";
	    }
		else
		{
			$url = "https://oppwa.com/v1/payments";
		    $data = "entityId=".$authentication_entityId.
		                "&amount=$valor" .
		                "&currency=EUR" .
		                "&paymentType=DB" .
		                "&paymentBrand=MBWAY" .	
				        "&customer.givenName=".urlencode("$customer_name").
						"&merchantTransactionId=$id_enc" .
		                "&virtualAccount.accountId=351#$tel";
		}
		//echo $data;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer '.$Bearer));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $responseData = curl_exec($ch);
	    if(curl_errno($ch))
	    {
	        //return curl_error($ch);
	    }
	    curl_close($ch);

	    $sql="UPDATE sibs_mbway_logs set Erros='".addslashes($responseData)."' WHERE ID = '$ID_Log' ";
		$rs=Runquery($db_link,$GLOBALS['DB_name'], $sql);
	    return $responseData;
	}

	if ($id_enc != '')
	{
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs = new DataObj;
		$rs->db_open($db_link,$GLOBALS['DB_name'], 'SELECT * FROM encomendas WHERE ID = "'.$id_enc.'"');
		if(!$rs->EOF())
		{
			$Contacto=$rs->db_data['Contacto'];
			$valor=$rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();


		$valor=number_format($valor,2,'.','');
		$responseData=request($authentication_entityId,$Bearer,$valor,$mbway_tel,$id_enc,$sanbox,$Contacto);


		$sql = 'UPDATE pagamentos_unicre SET Resposta1 = "'.addslashes(serialize($responseData)).'" WHERE ID_ENC = '.$id_enc;
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);

		$mbway_result = json_decode($responseData, true);
		foreach ($mbway_result as $key => $value )
		{
			$mbway_result[$key] = $value;
		}
		$msg='';
		$erro=0;
		switch ($mbway_result['result']['code'])
		{
		    case '000.000.000':$erro=0;$msg='Transaction succeeded';break;
			case '000.000.100':$erro=0;$msg='successful request';break;
			case '000.100.105':$erro=0;$msg='Chargeback Representment is successful';break;
			case '000.100.106':$erro=0;$msg='Chargeback Representment cancellation is successful';break;
			case '000.100.110':$erro=0;$msg='Request successfully processed in Merchant in Integrator Test Mode';break;
			case '000.100.111':$erro=0;$msg='Request successfully processed in Merchant in Validator Test Mode';break;
			case '000.100.112':$erro=0;$msg='Request successfully processed in Merchant in Connector Test Mode';break;
			case '000.300.000':$erro=0;$msg='Two-step transaction succeeded';break;
			case '000.300.100':$erro=0;$msg='Risk check successful';break;
			case '000.300.101':$erro=0;$msg='Risk bank account check successful';break;
			case '000.300.102':$erro=0;$msg='Risk report successful';break;
			case '000.300.103':$erro=0;$msg='Exemption check successful';break;
			case '000.310.100':$erro=0;$msg='Account updated';break;
			case '000.310.101':$erro=0;$msg='Account updated (Credit card expired)';break;
			case '000.310.110':$erro=0;$msg='No updates found, but account is valid';break;
			case '000.600.000':$erro=0;$msg='transaction succeeded due to external update';break;

			case '000.400.000':$erro=0;$msg='Transaction succeeded (please review manually due to fraud suspicion)';break;
			case '000.400.010':$erro=0;$msg='Transaction succeeded (please review manually due to AVS return code)';break;
			case '000.400.020':$erro=0;$msg='Transaction succeeded (please review manually due to CVV return code)';break;
			case '000.400.040':$erro=0;$msg='Transaction succeeded (please review manually due to amount mismatch)';break;
			case '000.400.050':$erro=0;$msg='Transaction succeeded (please review manually because transaction is pending)';break;
			case '000.400.060':$erro=0;$msg='Transaction succeeded (approved at merchant\'s risk)';break;
			case '000.400.070':$erro=0;$msg='Transaction succeeded (waiting for external risk review)';break;
			case '000.400.080':$erro=0;$msg='Transaction succeeded (please review manually because the service was unavailable)';break;
			case '000.400.081':$erro=0;$msg='Transaction succeeded (please review manually, as the risk status not available yet due network timeout)';break;
			case '000.400.082':$erro=0;$msg='Transaction succeeded (please review manually, as the risk status not available yet due processing timeout)';break;
			case '000.400.090':$erro=0;$msg='Transaction succeeded (please review manually due to external risk check)';break;
			case '000.400.100':$erro=0;$msg='Transaction succeeded, risk after payment rejected';break;
			case '000.400.110':$erro=3;$msg='Authentication successful (frictionless flow)';break;

			case '000.200.000':$erro=3;$msg='transaction pending';break;
			case '000.200.001':$erro=3;$msg='Transaction pending for acquirer, the consumer is not present';break;
			case '000.200.100':$erro=3;$msg='successfully created checkout';break;
			case '000.200.101':$erro=3;$msg='successfully updated checkout';break;
			case '000.200.102':$erro=3;$msg='successfully deleted checkout';break;
			case '000.200.103':$erro=3;$msg='checkout is pending';break;
			case '000.200.200':$erro=3;$msg='Transaction initialized';break;
			case '000.200.201':$erro=3;$msg='QR Scanned/Link Clicked, waiting for the final authentication result';break;

			case '100.400.500':$erro=3;$msg='waiting for external risk';break;
			case '800.400.500':$erro=0;$msg='Waiting for confirmation of non-instant payment. Denied for now.';break;
			case '800.400.501':$erro=0;$msg='Waiting for confirmation of non-instant debit. Denied for now.';break;
			case '800.400.502':$erro=0;$msg='Waiting for confirmation of non-instant refund. Denied for now.';break;

			case '000.400.101':$erro=3;$msg='card not participating/authentication unavailable';break;
			case '000.400.102':$erro=3;$msg='user not enrolled';break;
			case '000.400.103':$erro=3;$msg='Technical Error in 3D system';break;
			case '000.400.104':$erro=3;$msg='Missing or malformed 3DSecure Configuration for Channel';break;
			case '000.400.105':$erro=3;$msg='Unsupported User Device - Authentication not possible';break;
			case '000.400.106':$erro=3;$msg='invalid payer authentication response(PARes) in 3DSecure Transaction';break;
			case '000.400.107':$erro=3;$msg='Communication Error to VISA/Mastercard Directory Server';break;
			case '000.400.108':$erro=3;$msg='Cardholder Not Found - card number provided is not found in the ranges of the issuer';break;
			case '000.400.109':$erro=3;$msg='Card is not enrolled for 3DS version 2';break;
			case '000.400.200':$erro=3;$msg='risk management check communication error';break;

			case '800.100.100':$erro=3;$msg='transaction declined for unknown reason';break;
			case '800.100.150':$erro=3;$msg='transaction declined (refund on gambling tx not allowed)';break;
			case '800.100.151':$erro=3;$msg='transaction declined (invalid card)';break;
			case '800.100.152':$erro=3;$msg='transaction declined by authorization system';break;
			case '800.100.153':$erro=3;$msg='transaction declined (invalid CVV)';break;
			case '800.100.154':$erro=3;$msg='transaction declined (transaction marked as invalid)';break;
			case '800.100.155':$erro=3;$msg='transaction declined (amount exceeds credit)';break;
			case '800.100.156':$erro=3;$msg='transaction declined (format error)';break;
			case '800.100.157':$erro=3;$msg='transaction declined (wrong expiry date)';break;
			case '800.100.158':$erro=3;$msg='transaction declined (suspecting manipulation)';break;
			case '800.100.159':$erro=3;$msg='transaction declined (stolen card)';break;
			case '800.100.160':$erro=3;$msg='transaction declined (card blocked)';break;
			case '800.100.161':$erro=3;$msg='transaction declined (too many invalid tries)';break;
			case '800.100.162':$erro=3;$msg='transaction declined (limit exceeded)';break;
			case '800.100.163':$erro=3;$msg='transaction declined (maximum transaction frequency exceeded)';break;
			case '800.100.164':$erro=3;$msg='transaction declined (merchants limit exceeded)';break;
			case '800.100.165':$erro=3;$msg='transaction declined (card lost)';break;
			case '800.100.166':$erro=3;$msg='transaction declined (Incorrect personal identification number)';break;
			case '800.100.167':$erro=3;$msg='transaction declined (referencing transaction does not match)';break;
			case '800.100.168':$erro=3;$msg='transaction declined (restricted card)';break;
			case '800.100.169':$erro=3;$msg='transaction declined (card type is not processed by the authorization center)';break;
			case '800.100.170':$erro=3;$msg='transaction declined (transaction not permitted)';break;
			case '800.100.171':$erro=3;$msg='transaction declined (pick up card)';break;
			case '800.100.172':$erro=3;$msg='transaction declined (account blocked)';break;
			case '800.100.173':$erro=3;$msg='transaction declined (invalid currency, not processed by authorization center)';break;
			case '800.100.174':$erro=3;$msg='transaction declined (invalid amount)';break;
			case '800.100.175':$erro=3;$msg='transaction declined (invalid brand)';break;
			case '800.100.176':$erro=3;$msg='transaction declined (account temporarily not available. Please try again later)';break;
			case '800.100.177':$erro=3;$msg='transaction declined (amount field should not be empty)';break;
			case '800.100.178':$erro=3;$msg='transaction declined (PIN entered incorrectly too often)';break;
			case '800.100.179':$erro=3;$msg='transaction declined (exceeds withdrawal count limit)';break;
			case '800.100.190':$erro=3;$msg='transaction declined (invalid configuration data)';break;
			case '800.100.191':$erro=3;$msg='transaction declined (transaction in wrong state on aquirer side)';break;
			case '800.100.192':$erro=3;$msg='transaction declined (invalid CVV, Amount has still been reserved on the customer\'s card and will be released in a few business days. Please ensure the CVV code is accurate before retrying the transaction)';break;
			case '800.100.195':$erro=3;$msg='transaction declined (UserAccount Number/ID unknown)';break;
			case '800.100.196':$erro=3;$msg='transaction declined (registration error)';break;
			case '800.100.197':$erro=3;$msg='transaction declined (registration cancelled externally)';break;
			case '800.100.198':$erro=3;$msg='transaction declined (invalid holder)';break;
			case '800.100.199':$erro=3;$msg='transaction declined (invalid tax number)';break;
			case '800.100.200':$erro=3;$msg='Refer to Payer due to reason not specified';break;
			case '800.100.201':$erro=3;$msg='Account or Bank Details Incorrect';break;
			case '800.100.202':$erro=3;$msg='Account Closed';break;
			case '800.100.203':$erro=3;$msg='Insufficient Funds';break;
			case '800.100.204':$erro=3;$msg='Mandate Expired';break;
			case '800.100.205':$erro=3;$msg='Mandate Discarded';break;
			case '800.100.206':$erro=3;$msg='Refund of an authorized payment requested by the customer';break;
			case '800.100.207':$erro=3;$msg='Refund requested';break;
			case '800.100.208':$erro=3;$msg='Direct debit not enabled for the specified account or bank';break;
			case '800.100.402':$erro=3;$msg='cc/bank account holder not valid';break;
			case '800.100.403':$erro=3;$msg='transaction declined (revocation of authorisation order)';break;
			case '800.100.500':$erro=3;$msg='Card holder has advised his bank to stop this recurring payment';break;
			case '800.100.501':$erro=3;$msg='Card holder has advised his bank to stop all recurring payments for this merchant';break;
			case '800.700.100':$erro=3;$msg='transaction for the same session is currently being processed, please try again later.';break;
			case '800.700.101':$erro=3;$msg='family name too long';break;
			case '800.700.201':$erro=3;$msg='given name too long';break;
			case '800.700.500':$erro=3;$msg='company name too long';break;
			case '800.800.102':$erro=3;$msg='Invalid street';break;
			case '800.800.202':$erro=3;$msg='Invalid zip';break;
			case '800.800.302':$erro=3;$msg='Invalid city';break;

			case '900.100.100':$erro=3;$msg='unexpected communication error with connector/acquirer';break;
			case '900.100.200':$erro=3;$msg='error response from connector/acquirer';break;
			case '900.100.201':$erro=3;$msg='error on the external gateway (e.g. on the part of the bank, acquirer,...)';break;
			case '900.100.202':$erro=3;$msg='invalid transaction flow, the requested function is not applicable for the referenced transaction.';break;
			case '900.100.203':$erro=3;$msg='error on the internal gateway';break;
			case '900.100.300':$erro=3;$msg='timeout, uncertain result';break;
			case '900.100.301':$erro=3;$msg='Transaction timed out without response from connector/acquirer. It was reversed.';break;
			case '900.100.310':$erro=3;$msg='Transaction timed out due to internal system misconfiguration. Request to acquirer has not been sent.';break;
			case '900.100.400':$erro=3;$msg='timeout at connectors/acquirer side';break;
			case '900.100.500':$erro=3;$msg='timeout at connectors/acquirer side (try later)';break;
			case '900.100.600':$erro=3;$msg='connector/acquirer currently down';break;
			case '900.200.100':$erro=3;$msg='Message Sequence Number of Connector out of sync';break;
			case '900.300.600':$erro=3;$msg='user session timeout';break;
			case '900.400.100':$erro=3;$msg='unexpected communication error with external risk provider';break;

			case '600.100.100':$erro=3;$msg='Unexpected Integrator Error (Request could not be processed)';break;
			case '800.500.100':$erro=3;$msg='direct debit transaction declined for unknown reason';break;
			case '800.500.110':$erro=3;$msg='Unable to process transaction - ran out of terminalIds - please contact acquirer';break;
			case '800.600.100':$erro=3;$msg='transaction is being already processed';break;
			case '800.800.400':$erro=3;$msg='Connector/acquirer system is under maintenance';break;
			case '800.800.800':$erro=3;$msg='The payment system is currenty unavailable, please contact support in case this happens again.';break;
			case '800.800.801':$erro=3;$msg='The payment system is currenty unter maintenance. Please apologize for the inconvenience this may cause. If you were not informed of this maintenance window in advance, contact your sales representative.';break;
			case '999.999.888':$erro=3;$msg='UNDEFINED PLATFORM DATABASE ERROR';break;
			case '999.999.999':$erro=3;$msg='UNDEFINED CONNECTOR/ACQUIRER ERROR';break;

			case '100.395.101':$erro=3;$msg='Bank not supported for Giropay';break;
			case '100.395.102':$erro=3;$msg='Account not enabled for Giropay e.g. test account';break;
			case '100.395.501':$erro=3;$msg='Previously pending online transfer transaction timed out';break;
			case '100.395.502':$erro=3;$msg='Acquirer/Bank reported timeout on online transfer transaction';break;
			case '100.396.101':$erro=3;$msg='Cancelled by user';break;
			case '100.396.102':$erro=3;$msg='Not confirmed by user';break;
			case '100.396.103':$erro=3;$msg='Previously pending transaction timed out';break;
			case '100.396.104':$erro=3;$msg='Uncertain status - probably cancelled by user';break;
			case '100.396.106':$erro=3;$msg='User did not agree to payment method terms';break;
			case '100.396.201':$erro=3;$msg='Cancelled by merchant';break;
			case '100.397.101':$erro=3;$msg='Cancelled by user due to external update';break;
			case '100.397.102':$erro=3;$msg='Rejected by connector/acquirer due to external update';break;

			case '300.100.100':$erro=3;$msg='Transaction declined (additional customer authentication required)';break;

			case '100.370.100':$erro=3;$msg='transaction declined';break;
			case '100.370.110':$erro=3;$msg='transaction must be executed for German address';break;
			case '100.370.111':$erro=3;$msg='system error( possible incorrect/missing input data)';break;
			case '100.380.100':$erro=3;$msg='transaction declined';break;
			case '100.380.101':$erro=3;$msg='transaction contains no risk management part';break;
			case '100.380.110':$erro=3;$msg='transaction must be executed for German address';break;
			case '100.380.201':$erro=3;$msg='no risk management process type specified';break;
			case '100.380.305':$erro=3;$msg='no frontend information provided for asynchronous transaction';break;
			case '100.380.306':$erro=3;$msg='no authentication data provided in risk management transaction';break;
			case '100.380.401':$erro=3;$msg='User Authentication Failed';break;
			case '100.380.501':$erro=3;$msg='risk management transaction timeout';break;
			case '100.400.000':$erro=3;$msg='transaction declined (Wrong Address)';break;
			case '100.400.001':$erro=3;$msg='transaction declined (Wrong Identification)';break;
			case '100.400.002':$erro=3;$msg='transaction declined (Insufficient credibility score)';break;
			case '100.400.005':$erro=3;$msg='transaction must be executed for German address';break;
			case '100.400.007':$erro=3;$msg='System error ( possible incorrect/missing input data)';break;
			case '100.400.020':$erro=3;$msg='transaction declined';break;
			case '100.400.021':$erro=3;$msg='transaction declined for country';break;
			case '100.400.030':$erro=3;$msg='transaction not authorized. Please check manually';break;
			case '100.400.039':$erro=3;$msg='transaction declined for other error';break;
			case '100.400.040':$erro=3;$msg='authorization failure';break;
			case '100.400.041':$erro=3;$msg='transaction must be executed for German address';break;
			case '100.400.042':$erro=3;$msg='transaction declined by SCHUFA (Insufficient credibility score)';break;
			case '100.400.043':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
			case '100.400.044':$erro=3;$msg='transaction not authorized. Please check manually';break;
			case '100.400.045':$erro=3;$msg='SCHUFA result not definite. Please check manually';break;
			case '100.400.051':$erro=3;$msg='SCHUFA system error (possible incorrect/missing input data)';break;
			case '100.400.060':$erro=3;$msg='authorization failure';break;
			case '100.400.061':$erro=3;$msg='transaction declined (Insufficient credibility score)';break;
			case '100.400.063':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
			case '100.400.064':$erro=3;$msg='transaction must be executed for Austrian, German or Swiss address';break;
			case '100.400.065':$erro=3;$msg='result ambiguous. Please check manually';break;
			case '100.400.071':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
			case '100.400.080':$erro=3;$msg='authorization failure';break;
			case '100.400.081':$erro=3;$msg='transaction declined';break;
			case '100.400.083':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
			case '100.400.084':$erro=3;$msg='transaction can not be executed for given country';break;
			case '100.400.085':$erro=3;$msg='result ambiguous. Please check manually';break;
			case '100.400.086':$erro=3;$msg='transaction declined (Wrong Address)';break;
			case '100.400.087':$erro=3;$msg='transaction declined (Wrong Identification)';break;
			case '100.400.091':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
			case '100.400.100':$erro=3;$msg='transaction declined - very bad rating';break;
			case '100.400.120':$erro=3;$msg='authorization failure';break;
			case '100.400.121':$erro=3;$msg='account blacklisted';break;
			case '100.400.122':$erro=3;$msg='transaction must be executed for valid German account';break;
			case '100.400.123':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
			case '100.400.130':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
			case '100.400.139':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
			case '100.400.140':$erro=3;$msg='transaction declined by GateKeeper';break;
			case '100.400.141':$erro=3;$msg='Challenge by ReD Shield';break;
			case '100.400.142':$erro=3;$msg='Deny by ReD Shield';break;
			case '100.400.143':$erro=3;$msg='Noscore by ReD Shield';break;
			case '100.400.144':$erro=3;$msg='ReD Shield data error';break;
			case '100.400.145':$erro=3;$msg='ReD Shield connection error';break;
			case '100.400.146':$erro=3;$msg='Line item error by ReD Shield';break;
			case '100.400.147':$erro=3;$msg='Payment void and transaction denied by ReD Shield';break;
			case '100.400.148':$erro=3;$msg='Payment void and transaction challenged by ReD Shield';break;
			case '100.400.149':$erro=3;$msg='Payment void and data error by ReD Shield';break;
			case '100.400.150':$erro=3;$msg='Payment void and connection error by ReD Shield';break;
			case '100.400.151':$erro=3;$msg='Payment void and line item error by ReD Shield';break;
			case '100.400.152':$erro=3;$msg='Payment void and error returned by ReD Shield';break;
			case '100.400.241':$erro=3;$msg='Challenged by Threat Metrix';break;
			case '100.400.242':$erro=3;$msg='Denied by Threat Metrix';break;
			case '100.400.243':$erro=3;$msg='Invalid sessionId';break;
			case '100.400.260':$erro=3;$msg='authorization failure';break;
			case '100.400.300':$erro=3;$msg='abort checkout process';break;
			case '100.400.301':$erro=3;$msg='reenter age/birthdate';break;
			case '100.400.302':$erro=3;$msg='reenter address (packstation not allowed)';break;
			case '100.400.303':$erro=3;$msg='reenter address';break;
			case '100.400.304':$erro=3;$msg='invalid input data';break;
			case '100.400.305':$erro=3;$msg='invalid foreign address';break;
			case '100.400.306':$erro=3;$msg='delivery address error';break;
			case '100.400.307':$erro=3;$msg='offer only secure methods of payment';break;
			case '100.400.308':$erro=3;$msg='offer only secure methods of payment; possibly abort checkout';break;
			case '100.400.309':$erro=3;$msg='confirm corrected address; if not confirmed, offer secure methods of payment only';break;
			case '100.400.310':$erro=3;$msg='confirm bank account data; if not confirmed, offer secure methods of payment only';break;
			case '100.400.311':$erro=3;$msg='transaction declined (format error)';break;
			case '100.400.312':$erro=3;$msg='transaction declined (invalid configuration data)';break;
			case '100.400.313':$erro=3;$msg='currency field is invalid or missing';break;
			case '100.400.314':$erro=3;$msg='amount invalid or empty';break;
			case '100.400.315':$erro=3;$msg='invalid or missing email address (probably invalid syntax)';break;
			case '100.400.316':$erro=3;$msg='transaction declined (card missing)';break;
			case '100.400.317':$erro=3;$msg='transaction declined (invalid card)';break;
			case '100.400.318':$erro=3;$msg='invalid IP number';break;
			case '100.400.319':$erro=3;$msg='transaction declined by risk system';break;
			case '100.400.320':$erro=3;$msg='shopping cart data invalid or missing';break;
			case '100.400.321':$erro=3;$msg='payment type invalid or missing';break;
			case '100.400.322':$erro=3;$msg='encryption method invalid or missing';break;
			case '100.400.323':$erro=3;$msg='certificate invalid or missing';break;
			case '100.400.324':$erro=3;$msg='Error on the external risk system';break;
			case '100.400.325':$erro=3;$msg='External risk system not available';break;
			case '100.400.326':$erro=3;$msg='Risk bank account check unsuccessful';break;
			case '100.400.327':$erro=3;$msg='Risk report unsuccessful';break;
			case '100.400.328':$erro=3;$msg='Risk report unsuccessful (invalid data)';break;

			case '800.400.100':$erro=3;$msg='AVS Check Failed';break;
			case '800.400.101':$erro=3;$msg='Mismatch of AVS street value';break;
			case '800.400.102':$erro=3;$msg='Mismatch of AVS street number';break;
			case '800.400.103':$erro=3;$msg='Mismatch of AVS PO box value fatal';break;
			case '800.400.104':$erro=3;$msg='Mismatch of AVS zip code value fatal';break;
			case '800.400.105':$erro=3;$msg='Mismatch of AVS settings (AVSkip, AVIgnore, AVSRejectPolicy) value';break;
			case '800.400.110':$erro=3;$msg='AVS Check Failed. Amount has still been reserved on the customer\'s card and will be released in a few business days. Please ensure the billing address is accurate before retrying the transaction.';break;
			case '800.400.150':$erro=3;$msg='Implausible address data';break;
			case '800.400.151':$erro=3;$msg='Implausible address state data';break;

			case '100.380.401':$erro=3;$msg='User Authentication Failed';break;
			case '100.390.101':$erro=3;$msg='purchase amount/currency mismatch';break;
			case '100.390.102':$erro=3;$msg='PARes Validation failed';break;
			case '100.390.103':$erro=3;$msg='PARes Validation failed - problem with signature';break;
			case '100.390.104':$erro=3;$msg='XID mismatch';break;
			case '100.390.105':$erro=3;$msg='Transaction rejected because of technical error in 3DSecure system';break;
			case '100.390.106':$erro=3;$msg='Transaction rejected because of error in 3DSecure configuration';break;
			case '100.390.107':$erro=3;$msg='Transaction rejected because cardholder authentication unavailable';break;
			case '100.390.108':$erro=3;$msg='Transaction rejected because merchant not participating in 3DSecure program';break;
			case '100.390.109':$erro=3;$msg='Transaction rejected because of VISA status \'U\' or AMEX status \'N\' or \'U\' in 3DSecure program';break;
			case '100.390.110':$erro=3;$msg='Cardholder Not Found - card number provided is not found in the ranges of the issuer';break;
			case '100.390.111':$erro=3;$msg='Communication Error to VISA/Mastercard Directory Server';break;
			case '100.390.112':$erro=3;$msg='Technical Error in 3D system';break;
			case '100.390.113':$erro=3;$msg='Unsupported User Device - Authentication not possible';break;
			case '100.390.115':$erro=3;$msg='Authentication failed due to invalid message format';break;
			case '100.390.116':$erro=3;$msg='Access denied to the authentication system';break;
			case '100.390.117':$erro=3;$msg='Authentication failed due to invalid data fields';break;
			case '100.390.118':$erro=3;$msg='Authentication failed due to suspected fraud';break;
			case '800.400.200':$erro=3;$msg='Invalid Payer Authentication in 3DSecure transaction';break;

			case '100.100.701':$erro=3;$msg='suspecting fraud, this card may not be processed';break;
			case '800.200.159':$erro=3;$msg='account or user is blacklisted (card stolen)';break;
			case '800.200.160':$erro=3;$msg='account or user is blacklisted (card blocked)';break;
			case '800.200.165':$erro=3;$msg='account or user is blacklisted (card lost)';break;
			case '800.200.202':$erro=3;$msg='account or user is blacklisted (account closed)';break;
			case '800.200.208':$erro=3;$msg='account or user is blacklisted (account blocked)';break;
			case '800.200.220':$erro=3;$msg='account or user is blacklisted (fraudulent transaction)';break;
			case '800.300.101':$erro=3;$msg='account or user is blacklisted';break;
			case '800.300.102':$erro=3;$msg='country blacklisted';break;
			case '800.300.200':$erro=3;$msg='email is blacklisted';break;
			case '800.300.301':$erro=3;$msg='ip blacklisted';break;
			case '800.300.302':$erro=3;$msg='ip is anonymous proxy';break;
			case '800.300.401':$erro=3;$msg='bin blacklisted';break;
			case '800.300.500':$erro=3;$msg='transaction temporary blacklisted (too many tries invalid CVV)';break;
			case '800.300.501':$erro=3;$msg='transaction temporary blacklisted (too many tries invalid expire date)';break;
			case '800.310.200':$erro=3;$msg='Account closed';break;
			case '800.310.210':$erro=3;$msg='Account not found';break;
			case '800.310.211':$erro=3;$msg='Account not found (BIN/issuer not participating)';break;

			case '800.110.100':$erro=3;$msg='duplicate transaction';break;
			case '800.120.100':$erro=3;$msg='Rejected by Throttling.';break;
			case '800.120.101':$erro=3;$msg='maximum number of transactions per account already exceeded';break;
			case '800.120.102':$erro=3;$msg='maximum number of transactions per ip already exceeded';break;
			case '800.120.103':$erro=3;$msg='maximum number of transactions per email already exceeded';break;
			case '800.120.200':$erro=3;$msg='maximum total volume of transactions already exceeded';break;
			case '800.120.201':$erro=3;$msg='maximum total volume of transactions per account already exceeded';break;
			case '800.120.202':$erro=3;$msg='maximum total volume of transactions per ip already exceeded';break;
			case '800.120.203':$erro=3;$msg='maximum total volume of transactions per email already exceeded';break;
			case '800.120.300':$erro=3;$msg='chargeback rate per bin exceeded';break;
			case '800.120.401':$erro=3;$msg='maximum number of transactions or total volume for configured MIDs or CIs exceeded';break;
			case '800.130.100':$erro=3;$msg='Transaction with same TransactionId already exists';break;
			case '800.140.100':$erro=3;$msg='maximum number of registrations per mobile number exceeded';break;
			case '800.140.101':$erro=3;$msg='maximum number of registrations per email address exceeded';break;
			case '800.140.110':$erro=3;$msg='maximum number of registrations of mobile per credit card number exceeded';break;
			case '800.140.111':$erro=3;$msg='maximum number of registrations of credit card number per mobile exceeded';break;
			case '800.140.112':$erro=3;$msg='maximum number of registrations of email per credit card number exceeded';break;
			case '800.140.113':$erro=3;$msg='maximum number of registrations of credit card number per email exceeded';break;
			case '800.150.100':$erro=3;$msg='Account Holder does not match Customer Name';break;
			case '800.160.100':$erro=3;$msg='Invalid payment data for configured Shopper Dispatching Type';break;
			case '800.160.110':$erro=3;$msg='Invalid payment data for configured Payment Dispatching Type';break;
			case '800.160.120':$erro=3;$msg='Invalid payment data for configured Recurring Transaction Dispatching Type';break;
			case '800.160.130':$erro=3;$msg='Invalid payment data for configured TicketSize Dispatching Type';break;

			case '500.100.201':$erro=3;$msg='Channel/Merchant is disabled (no processing possible)';break;
			case '500.100.202':$erro=3;$msg='Channel/Merchant is new (no processing possible yet)';break;
			case '500.100.203':$erro=3;$msg='Channel/Merchant is closed (no processing possible)';break;
			case '500.100.301':$erro=3;$msg='Merchant-Connector is disabled (no processing possible)';break;
			case '500.100.302':$erro=3;$msg='Merchant-Connector is new (no processing possible yet)';break;
			case '500.100.303':$erro=3;$msg='Merchant-Connector is closed (no processing possible)';break;
			case '500.100.304':$erro=3;$msg='Merchant-Connector is disabled at gateway (no processing possible)';break;
			case '500.100.401':$erro=3;$msg='Connector is unavailable (no processing possible)';break;
			case '500.100.402':$erro=3;$msg='Connector is new (no processing possible yet)';break;
			case '500.100.403':$erro=3;$msg='Connector is unavailable (no processing possible)';break;
			case '500.200.101':$erro=3;$msg='No target account configured for DD transaction';break;
			case '600.200.100':$erro=3;$msg='invalid Payment Method';break;
			case '600.200.200':$erro=3;$msg='Unsupported Payment Method';break;
			case '600.200.201':$erro=3;$msg='Channel/Merchant not configured for this payment method';break;
			case '600.200.202':$erro=3;$msg='Channel/Merchant not configured for this payment type';break;
			case '600.200.300':$erro=3;$msg='invalid Payment Type';break;
			case '600.200.310':$erro=3;$msg='invalid Payment Type for given Payment Method';break;
			case '600.200.400':$erro=3;$msg='Unsupported Payment Type';break;
			case '600.200.500':$erro=3;$msg='Invalid payment data. You are not configured for this currency or sub type (country or brand)';break;
			case '600.200.501':$erro=3;$msg='Invalid payment data for Recurring transaction. Merchant or transaction data has wrong recurring configuration.';break;
			case '600.200.600':$erro=3;$msg='invalid payment code (type or method)';break;
			case '600.200.700':$erro=3;$msg='invalid payment mode (you are not configured for the requested transaction mode)';break;
			case '600.200.800':$erro=3;$msg='invalid brand for given payment method and payment mode (you are not configured for the requested transaction mode)';break;
			case '600.200.810':$erro=3;$msg='invalid return code provided';break;
			case '600.300.101':$erro=3;$msg='Merchant key not found';break;
			case '600.300.200':$erro=3;$msg='merchant source IP address not whitelisted';break;
			case '600.300.210':$erro=3;$msg='merchant notificationUrl not whitelisted';break;
			case '600.300.211':$erro=3;$msg='shopperResultUrl not whitelisted';break;
			case '800.121.100':$erro=3;$msg='Channel not configured for given source type. Please contact your account manager.';break;
			case '800.121.200':$erro=3;$msg='Secure Query is not enabled for this entity. Please contact your account manager.';break;

			case '100.150.100':$erro=3;$msg='request contains no Account data and no registration id';break;
			case '100.150.101':$erro=3;$msg='invalid format for specified registration id (must be uuid format)';break;
			case '100.150.200':$erro=3;$msg='registration does not exist';break;
			case '100.150.201':$erro=3;$msg='registration is not confirmed yet';break;
			case '100.150.202':$erro=3;$msg='registration is already deregistered';break;
			case '100.150.203':$erro=3;$msg='registration is not valid, probably initially rejected';break;
			case '100.150.204':$erro=3;$msg='account registration reference pointed to no registration transaction';break;
			case '100.150.205':$erro=3;$msg='referenced registration does not contain an account';break;
			case '100.150.300':$erro=3;$msg='payment only allowed with valid initial registration';break;
			case '100.350.100':$erro=3;$msg='referenced session is REJECTED (no action possible).';break;
			case '100.350.101':$erro=3;$msg='referenced session is CLOSED (no action possible)';break;
			case '100.350.200':$erro=3;$msg='undefined session state';break;
			case '100.350.201':$erro=3;$msg='referencing a registration through reference id is not applicable for this payment type';break;
			case '100.350.301':$erro=3;$msg='confirmation (CF) must be registered (RG) first';break;
			case '100.350.302':$erro=3;$msg='session already confirmed (CF)';break;
			case '100.350.303':$erro=3;$msg='cannot deregister (DR) unregistered account and/or customer';break;
			case '100.350.310':$erro=3;$msg='cannot confirm (CF) session via XML';break;
			case '100.350.311':$erro=3;$msg='cannot confirm (CF) on a registration passthrough channel';break;
			case '100.350.312':$erro=3;$msg='cannot do passthrough on non-internal connector';break;
			case '100.350.313':$erro=3;$msg='registration of this type has to provide confirmation url';break;
			case '100.350.314':$erro=3;$msg='customer could not be notified of pin to confirm registration (channel)';break;
			case '100.350.315':$erro=3;$msg='customer could not be notified of pin to confirm registration (sending failed)';break;
			case '100.350.400':$erro=3;$msg='no or invalid PIN (email/SMS/MicroDeposit authentication) entered';break;
			case '100.350.500':$erro=3;$msg='unable to obtain personal (virtual) account - most likely no more accounts available';break;
			case '100.350.601':$erro=3;$msg='registration is not allowed to reference another transaction';break;
			case '100.350.602':$erro=3;$msg='Registration is not allowed for recurring payment migration';break;

			case '100.250.100':$erro=3;$msg='job contains no execution information';break;
			case '100.250.105':$erro=3;$msg='invalid or missing action type';break;
			case '100.250.106':$erro=3;$msg='invalid or missing duration unit';break;
			case '100.250.107':$erro=3;$msg='invalid or missing notice unit';break;
			case '100.250.110':$erro=3;$msg='missing job execution';break;
			case '100.250.111':$erro=3;$msg='missing job expression';break;
			case '100.250.120':$erro=3;$msg='invalid execution parameters, combination does not conform to standard';break;
			case '100.250.121':$erro=3;$msg='invalid execution parameters, hour must be between 0 and 23';break;
			case '100.250.122':$erro=3;$msg='invalid execution parameters, minute and seconds must be between 0 and 59';break;
			case '100.250.123':$erro=3;$msg='invalid execution parameters, Day of month must be between 1 and 31';break;
			case '100.250.124':$erro=3;$msg='invalid execution parameters, month must be between 1 and 12';break;
			case '100.250.125':$erro=3;$msg='invalid execution parameters, Day of week must be between 1 and 7';break;
			case '100.250.250':$erro=3;$msg='Job tag missing';break;
			case '100.360.201':$erro=3;$msg='unknown schedule type';break;
			case '100.360.300':$erro=3;$msg='cannot schedule(SD) unscheduled job';break;
			case '100.360.303':$erro=3;$msg='cannot deschedule(DS) unscheduled job';break;
			case '100.360.400':$erro=3;$msg='schedule module not configured for LIVE transaction mode';break;

			case '700.100.100':$erro=3;$msg='reference id not existing';break;
			case '700.100.200':$erro=3;$msg='non matching reference amount';break;
			case '700.100.300':$erro=3;$msg='invalid amount (probably too large)';break;
			case '700.100.400':$erro=3;$msg='referenced payment method does not match with requested payment method';break;
			case '700.100.500':$erro=3;$msg='referenced payment currency does not match with requested payment currency';break;
			case '700.100.600':$erro=3;$msg='referenced mode does not match with requested payment mode';break;
			case '700.100.700':$erro=3;$msg='referenced transaction is of inappropriate type';break;
			case '700.100.701':$erro=3;$msg='referenced a DB transaction without explicitly providing an account. Not allowed to used referenced account.';break;
			case '700.100.710':$erro=3;$msg='cross-linkage of two transaction-trees';break;
			case '700.300.100':$erro=3;$msg='referenced tx can not be refunded, captured or reversed (invalid type)';break;
			case '700.300.200':$erro=3;$msg='referenced tx was rejected';break;
			case '700.300.300':$erro=3;$msg='referenced tx can not be refunded, captured or reversed (already refunded, captured or reversed)';break;
			case '700.300.400':$erro=3;$msg='referenced tx can not be captured (cut off time reached)';break;
			case '700.300.500':$erro=3;$msg='chargeback error (multiple chargebacks)';break;
			case '700.300.600':$erro=3;$msg='referenced tx can not be refunded or reversed (was chargebacked)';break;
			case '700.300.700':$erro=3;$msg='referenced tx can not be reversed (reversal not possible anymore)';break;
			case '700.300.800':$erro=3;$msg='referenced tx can not be voided';break;
			case '700.400.000':$erro=3;$msg='serious workflow error (call support)';break;
			case '700.400.100':$erro=3;$msg='cannot capture (PA value exceeded, PA reverted or invalid workflow?)';break;
			case '700.400.101':$erro=3;$msg='cannot capture (Not supported by authorization system)';break;
			case '700.400.200':$erro=3;$msg='cannot refund (refund volume exceeded or tx reversed or invalid workflow?)';break;
			case '700.400.300':$erro=3;$msg='cannot reverse (already refunded|reversed, invalid workflow or amount exceeded)';break;
			case '700.400.400':$erro=3;$msg='cannot chargeback (already chargebacked or invalid workflow?)';break;
			case '700.400.402':$erro=3;$msg='chargeback can only be generated internally by the payment system';break;
			case '700.400.410':$erro=3;$msg='cannot reversal chargeback (chargeback is already reversaled or invalid workflow?)';break;
			case '700.400.411':$erro=3;$msg='cannot reverse chargeback or invalid workflow (second chargeback)';break;
			case '700.400.420':$erro=3;$msg='cannot reversal chargeback (no chargeback existing or invalid workflow?)';break;
			case '700.400.510':$erro=3;$msg='capture needs at least one successful transaction of type (PA)';break;
			case '700.400.520':$erro=3;$msg='refund needs at least one successful transaction of type (CP or DB or RB or RC)';break;
			case '700.400.530':$erro=3;$msg='reversal needs at least one successful transaction of type (CP or DB or RB or PA)';break;
			case '700.400.540':$erro=3;$msg='reconceile needs at least one successful transaction of type (CP or DB or RB)';break;
			case '700.400.550':$erro=3;$msg='chargeback needs at least one successful transaction of type (CP or DB or RB)';break;
			case '700.400.560':$erro=3;$msg='receipt needs at least one successful transaction of type (PA or CP or DB or RB)';break;
			case '700.400.561':$erro=3;$msg='receipt on a registration needs a successfull registration in state \'OPEN\'';break;
			case '700.400.562':$erro=3;$msg='receipts are configured to be generated only internally by the payment system';break;
			case '700.400.565':$erro=3;$msg='finalize needs at least one successful transaction of type (PA or DB)';break;
			case '700.400.570':$erro=3;$msg='cannot reference a waiting/pending transaction';break;
			case '700.400.580':$erro=3;$msg='cannot find transaction';break;
			case '700.400.590':$erro=3;$msg='installment needs at least one successful transaction of type (DB or PA)';break;
			case '700.400.600':$erro=3;$msg='finalize needs at least one successful transaction of type (IN, DB, PA or CD)';break;
			case '700.400.700':$erro=3;$msg='initial and referencing channel-ids do not match';break;
			case '700.450.001':$erro=3;$msg='cannot transfer money from one account to the same account';break;
			case '700.500.001':$erro=3;$msg='referenced session contains too many transactions';break;
			case '700.500.002':$erro=3;$msg='capture or preauthorization appears too late in referenced session';break;
			case '700.500.003':$erro=3;$msg='test accounts not allowed in production';break;
			case '700.500.004':$erro=3;$msg='cannot refer a transaction which contains deleted customer information';break;

			case '100.300.101':$erro=3;$msg='invalid test mode (please use LIVE or INTEGRATOR_TEST or CONNECTOR_TEST)';break;
			case '100.300.200':$erro=3;$msg='transaction id too long';break;
			case '100.300.300':$erro=3;$msg='invalid reference id';break;
			case '100.300.400':$erro=3;$msg='missing or invalid channel id';break;
			case '100.300.401':$erro=3;$msg='missing or invalid sender id';break;
			case '100.300.402':$erro=3;$msg='missing or invalid version';break;
			case '100.300.501':$erro=3;$msg='invalid response id';break;
			case '100.300.600':$erro=3;$msg='invalid or missing user login';break;
			case '100.300.601':$erro=3;$msg='invalid or missing user pwd';break;
			case '100.300.700':$erro=3;$msg='invalid relevance';break;
			case '100.300.701':$erro=3;$msg='invalid relevance for given payment type';break;
			case '100.370.100':$erro=3;$msg='transaction declined';break;
			case '100.370.101':$erro=3;$msg='responseUrl not set in Transaction/Frontend';break;
			case '100.370.102':$erro=3;$msg='malformed responseUrl in Transaction/Frontend';break;
			case '100.370.110':$erro=3;$msg='transaction must be executed for German address';break;
			case '100.370.111':$erro=3;$msg='system error( possible incorrect/missing input data)';break;
			case '100.370.121':$erro=3;$msg='no or unknown ECI Type defined in Authentication';break;
			case '100.370.122':$erro=3;$msg='parameter with null key provided in 3DSecure Authentication';break;
			case '100.370.123':$erro=3;$msg='no or unknown verification type defined in 3DSecure Authentication';break;
			case '100.370.124':$erro=3;$msg='unknown parameter key in 3DSecure Authentication';break;
			case '100.370.125':$erro=3;$msg='Invalid 3DSecure Verification_ID. Must have Base64 encoding a Length of 28 digits';break;
			case '100.370.131':$erro=3;$msg='no or unknown authentication type defined in Transaction/Authentication@type';break;
			case '100.370.132':$erro=3;$msg='no result indicator defined Transaction/Authentication/resultIndicator';break;
			case '100.500.101':$erro=3;$msg='payment method invalid';break;
			case '100.500.201':$erro=3;$msg='payment type invalid';break;
			case '100.500.301':$erro=3;$msg='invalid due date';break;
			case '100.500.302':$erro=3;$msg='invalid mandate date of signature';break;
			case '100.500.303':$erro=3;$msg='invalid mandate id';break;
			case '100.500.304':$erro=3;$msg='invalid mandate external id';break;
			case '100.600.500':$erro=3;$msg='usage field too long';break;
			case '100.900.500':$erro=3;$msg='invalid recurrence mode';break;
			case '200.100.101':$erro=3;$msg='invalid Request Message. No valid XML. XML must be url-encoded! maybe it contains a not encoded ampersand or something similar.';break;
			case '200.100.102':$erro=3;$msg='invalid Request. XML load missing (XML string must be sent within parameter \'load\')';break;
			case '200.100.103':$erro=3;$msg='invalid Request Message. The request contains structural errors';break;
			case '200.100.150':$erro=3;$msg='transaction of multirequest not processed because of subsequent problems';break;
			case '200.100.151':$erro=3;$msg='multi-request is allowed with a maximum of 10 transactions only';break;
			case '200.100.199':$erro=3;$msg='Wrong Web Interface / URL used. Please check out the Tech Quick Start Doc Chapter 3.';break;
			case '200.100.201':$erro=3;$msg='invalid Request/Transaction tag (not present or [partially] empty)';break;
			case '200.100.300':$erro=3;$msg='invalid Request/Transaction/Payment tag (no or invalid code specified)';break;
			case '200.100.301':$erro=3;$msg='invalid Request/Transaction/Payment tag (not present or [partially] empty)';break;
			case '200.100.302':$erro=3;$msg='invalid Request/Transaction/Payment/Presentation tag (not present or [partially] empty)';break;
			case '200.100.401':$erro=3;$msg='invalid Request/Transaction/Account tag (not present or [partially] empty)';break;
			case '200.100.402':$erro=3;$msg='invalid Request/Transaction/Account(Customer, Relevance) tag (one of Account/Customer/Relevance must be present)';break;
			case '200.100.403':$erro=3;$msg='invalid Request/Transaction/Analysis tag (Criterions must have a name and value)';break;
			case '200.100.404':$erro=3;$msg='invalid Request/Transaction/Account (must not be present)';break;
			case '200.100.501':$erro=3;$msg='invalid or missing customer';break;
			case '200.100.502':$erro=3;$msg='invalid Request/Transaction/Customer/Name tag (not present or [partially] empty)';break;
			case '200.100.503':$erro=3;$msg='invalid Request/Transaction/Customer/Contact tag (not present or [partially] empty)';break;
			case '200.100.504':$erro=3;$msg='invalid Request/Transaction/Customer/Address tag (not present or [partially] empty)';break;
			case '200.100.601':$erro=3;$msg='invalid Request/Transaction/(ApplePay|GooglePay) tag (not present or [partially] empty)';break;
			case '200.100.602':$erro=3;$msg='invalid Request/Transaction/(ApplePay|GooglePay)/PaymentToken tag (not present or [partially] empty)';break;
			case '200.100.603':$erro=3;$msg='invalid Request/Transaction/(ApplePay|GooglePay)/PaymentToken tag (decryption error)';break;
			case '200.200.106':$erro=3;$msg='duplicate transaction. Please verify that the UUID is unique';break;
			case '200.300.403':$erro=3;$msg='Invalid HTTP method';break;
			case '200.300.404':$erro=3;$msg='invalid or missing parameter';break;
			case '200.300.405':$erro=3;$msg='Duplicate entity';break;
			case '200.300.406':$erro=3;$msg='Entity not found';break;
			case '200.300.407':$erro=3;$msg='Entity not specific enough';break;
			case '800.900.100':$erro=3;$msg='sender authorization failed';break;
			case '800.900.101':$erro=3;$msg='invalid email address (probably invalid syntax)';break;
			case '800.900.200':$erro=3;$msg='invalid phone number (has to start with a digit or a '+', at least 7 and max 25 chars long)';break;
			case '800.900.201':$erro=3;$msg='unknown channel';break;
			case '800.900.300':$erro=3;$msg='invalid authentication information';break;
			case '800.900.301':$erro=3;$msg='user authorization failed, user has no sufficient rights to process transaction';break;
			case '800.900.302':$erro=3;$msg='Authorization failed';break;
			case '800.900.303':$erro=3;$msg='No token created';break;
			case '800.900.399':$erro=3;$msg='Secure Registration Problem';break;
			case '800.900.401':$erro=3;$msg='Invalid IP number';break;
			case '800.900.450':$erro=3;$msg='Invalid birthdate';break;

			case '100.800.100':$erro=3;$msg='request contains no street';break;
			case '100.800.101':$erro=3;$msg='The combination of street1 and street2 must not exceed 201 characters.';break;
			case '100.800.102':$erro=3;$msg='The combination of street1 and street2 must not contain only numbers.';break;
			case '100.800.200':$erro=3;$msg='request contains no zip';break;
			case '100.800.201':$erro=3;$msg='zip too long';break;
			case '100.800.202':$erro=3;$msg='invalid zip';break;
			case '100.800.300':$erro=3;$msg='request contains no city';break;
			case '100.800.301':$erro=3;$msg='city too long';break;
			case '100.800.302':$erro=3;$msg='invalid city';break;
			case '100.800.400':$erro=3;$msg='invalid state/country combination';break;
			case '100.800.401':$erro=3;$msg='state too long';break;
			case '100.800.500':$erro=3;$msg='request contains no country';break;
			case '100.800.501':$erro=3;$msg='invalid country';break;

			case '100.700.100':$erro=3;$msg='customer.surname may not be null';break;
			case '100.700.101':$erro=3;$msg='customer.surname length must be between 0 and 50';break;
			case '100.700.200':$erro=3;$msg='customer.givenName may not be null';break;
			case '100.700.201':$erro=3;$msg='customer.givenName length must be between 0 and 50';break;
			case '100.700.300':$erro=3;$msg='invalid salutation';break;
			case '100.700.400':$erro=3;$msg='invalid title';break;
			case '100.700.500':$erro=3;$msg='company name too long';break;
			case '100.700.800':$erro=3;$msg='identity contains no or invalid \'paper\'';break;
			case '100.700.801':$erro=3;$msg='identity contains no or invalid identification value';break;
			case '100.700.802':$erro=3;$msg='identification value too long';break;
			case '100.700.810':$erro=3;$msg='specify at least one identity';break;
			case '100.900.100':$erro=3;$msg='request contains no email address';break;
			case '100.900.101':$erro=3;$msg='invalid email address (probably invalid syntax)';break;
			case '100.900.105':$erro=3;$msg='email address too long (max 50 chars)';break;
			case '100.900.200':$erro=3;$msg='invalid phone number (has to start with a digit or a '+', at least 7 and max 25 chars long)';break;
			case '100.900.300':$erro=3;$msg='invalid mobile phone number (has to start with a digit or a '+', at least 7 and max 25 chars long)';break;
			case '100.900.301':$erro=3;$msg='mobile phone number mandatory';break;
			case '100.900.400':$erro=3;$msg='request contains no ip number';break;
			case '100.900.401':$erro=3;$msg='invalid ip number';break;
			case '100.900.450':$erro=3;$msg='invalid birthdate';break;
			case '100.900.500':$erro=3;$msg='invalid recurrence mode';break;


			case '100.100.100':$erro=3;$msg='request contains no creditcard, bank account number or bank name';break;
			case '100.100.101':$erro=3;$msg='invalid creditcard, bank account number or bank name';break;
			case '100.100.104':$erro=3;$msg='invalid unique id / root unique id';break;
			case '100.100.200':$erro=3;$msg='request contains no month';break;
			case '100.100.201':$erro=3;$msg='invalid month';break;
			case '100.100.300':$erro=3;$msg='request contains no year';break;
			case '100.100.301':$erro=3;$msg='invalid year';break;
			case '100.100.303':$erro=3;$msg='card expired';break;
			case '100.100.304':$erro=3;$msg='card not yet valid';break;
			case '100.100.305':$erro=3;$msg='invalid expiration date format';break;
			case '100.100.400':$erro=3;$msg='request contains no cc/bank account holder';break;
			case '100.100.401':$erro=3;$msg='cc/bank account holder too short or too long';break;
			case '100.100.402':$erro=3;$msg='cc/bank account holder not valid';break;
			case '100.100.500':$erro=3;$msg='request contains no credit card brand';break;
			case '100.100.501':$erro=3;$msg='invalid credit card brand';break;
			case '100.100.600':$erro=3;$msg='empty CVV for VISA,MASTER, AMEX not allowed';break;
			case '100.100.601':$erro=3;$msg='invalid CVV/brand combination';break;
			case '100.100.650':$erro=3;$msg='empty CreditCardIssueNumber for MAESTRO not allowed';break;
			case '100.100.651':$erro=3;$msg='invalid CreditCardIssueNumber';break;
			case '100.100.700':$erro=3;$msg='invalid cc number/brand combination';break;
			case '100.100.701':$erro=3;$msg='suspecting fraud, this card may not be processed';break;
			case '100.200.100':$erro=3;$msg='bank account contains no or invalid country';break;
			case '100.200.103':$erro=3;$msg='bank account has invalid bankcode/name account number combination';break;
			case '100.200.104':$erro=3;$msg='bank account has invalid acccount number format';break;
			case '100.200.200':$erro=3;$msg='bank account needs to be registered and confirmed first. Country is mandate based.';break;
			case '100.210.101':$erro=3;$msg='virtual account contains no or invalid Id';break;
			case '100.210.102':$erro=3;$msg='virtual account contains no or invalid brand';break;
			case '100.211.101':$erro=3;$msg='user account contains no or invalid Id';break;
			case '100.211.102':$erro=3;$msg='user account contains no or invalid brand';break;
			case '100.211.103':$erro=3;$msg='no password defined for user account';break;
			case '100.211.104':$erro=3;$msg='password does not meet safety requirements (needs 8 digits at least and must contain letters and numbers)';break;
			case '100.211.105':$erro=3;$msg='wallet id has to be a valid email address';break;
			case '100.211.106':$erro=3;$msg='voucher ids have 32 digits always';break;
			case '100.212.101':$erro=3;$msg='wallet account registration must not have an initial balance';break;
			case '100.212.102':$erro=3;$msg='wallet account contains no or invalid brand';break;
			case '100.212.103':$erro=3;$msg='wallet account payment transaction needs to reference a registration';break;


			case '100.550.300':$erro=3;$msg='request contains no amount or too low amount';break;
			case '100.550.301':$erro=3;$msg='amount too large';break;
			case '100.550.303':$erro=3;$msg='amount format invalid (only two decimals allowed).';break;
			case '100.550.310':$erro=3;$msg='amount exceeds limit for the registered account.';break;
			case '100.550.311':$erro=3;$msg='exceeding account balance';break;
			case '100.550.312':$erro=3;$msg='Amount is outside allowed ticket size boundaries';break;
			case '100.550.400':$erro=3;$msg='request contains no currency';break;
			case '100.550.401':$erro=3;$msg='invalid currency';break;
			case '100.550.601':$erro=3;$msg='risk amount too large';break;
			case '100.550.603':$erro=3;$msg='risk amount format invalid (only two decimals allowed)';break;
			case '100.550.605':$erro=3;$msg='risk amount is smaller than amount (it must be equal or bigger then amount)';break;
			case '100.550.701':$erro=3;$msg='amounts not matched';break;
			case '100.550.702':$erro=3;$msg='currencies not matched';break;

			case '100.380.101':$erro=3;$msg='transaction contains no risk management part';break;
			case '100.380.201':$erro=3;$msg='no risk management process type specified';break;
			case '100.380.305':$erro=3;$msg='no frontend information provided for asynchronous transaction';break;
			case '100.380.306':$erro=3;$msg='no authentication data provided in risk management transaction';break;

			case '000.100.200':$erro=3;$msg='Reason not Specified';break;
			case '000.100.201':$erro=3;$msg='Account or Bank Details Incorrect';break;
			case '000.100.202':$erro=3;$msg='Account Closed';break;
			case '000.100.203':$erro=3;$msg='Insufficient Funds';break;
			case '000.100.204':$erro=3;$msg='Mandate not Valid';break;
			case '000.100.205':$erro=3;$msg='Mandate Cancelled';break;
			case '000.100.206':$erro=3;$msg='Revocation or Dispute';break;
			case '000.100.207':$erro=3;$msg='Cancellation in Clearing Network';break;
			case '000.100.208':$erro=3;$msg='Account Blocked';break;
			case '000.100.209':$erro=3;$msg='Account does not exist';break;
			case '000.100.210':$erro=3;$msg='Invalid Amount';break;
			case '000.100.211':$erro=3;$msg='Transaction succeeded (amount of transaction is smaller then amount of pre-authorization)';break;
			case '000.100.212':$erro=3;$msg='Transaction succeeded (amount of transaction is greater then amount of pre-authorization)';break;
			case '000.100.220':$erro=3;$msg='Fraudulent Transaction';break;
			case '000.100.221':$erro=3;$msg='Merchandise Not Received';break;
			case '000.100.222':$erro=3;$msg='Transaction Not Recognized By Cardholder';break;
			case '000.100.223':$erro=3;$msg='Service Not Rendered';break;
			case '000.100.224':$erro=3;$msg='Duplicate Processing';break;
			case '000.100.225':$erro=3;$msg='Credit Not Processed';break;
			case '000.100.226':$erro=3;$msg='Cannot be settled';break;
			case '000.100.227':$erro=3;$msg='Configuration Issue';break;
			case '000.100.228':$erro=3;$msg='Temporary Communication Error - Retry';break;
			case '000.100.229':$erro=3;$msg='Incorrect Instructions';break;
			case '000.100.230':$erro=3;$msg='Unauthorised Charge';break;
			case '000.100.231':$erro=3;$msg='Late Representment';break;
			case '000.100.232':$erro=3;$msg='Liability Shift';break;
			case '000.100.233':$erro=3;$msg='Authorization-Related Chargeback';break;
			case '000.100.234':$erro=3;$msg='Non receipt of merchandise';break;
			case '000.100.299':$erro=3;$msg='Unspecified (Technical)';break;

		}
		if($erro=='0')
		{
			//echo '2|0|0';
		}
		else 
		{
			//echo '1|'.$erro.'|'.$msg;

			$sql="UPDATE sibs_mbway_logs set Estado=3 where ID=$ID_Log";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			$sql="UPDATE encomendas set Estado=15 where ID=$id_enc";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
	}

?>