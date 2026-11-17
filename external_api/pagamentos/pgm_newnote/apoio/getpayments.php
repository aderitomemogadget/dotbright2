<?php
	require_once 'settings.php';
	class FinancialOperationAsyncResult
	{
		private $callback;
		public function __construct(callable $callback)
		{
			$this->callback = $callback;
		}

		public function financialOperationResult($arg0)
		{
			//gravar fx
			//$myFile = "data.txt";
			// opens the file for appending (file must already exist)
			//$fh = fopen($myFile, 'a');
			// Makes a CSV list of your post data
			// Write to the file
			/*$txt="Amount : ".$arg0->amount."\n
				->CurrencyCode: ".$arg0->currencyCode."\n
				->MerchantOperationID: ".$arg0->merchantOperationID."\n
				->StatusCode: ".$arg0->statusCode."\n
				->Timestamp: ".$arg0->timestamp."\n
				->Token: ".$arg0->token."\n";
				*/

			// 300  segundos deve dar timeout
			$hConn = OpenConnection($GLOBALS['Server'], $GLOBALS['UserName'], $GLOBALS['db_password']);

			$sql='UPDATE mbway SET statusCode="'.$arg0->statusCode.'",timestamp="'.$arg0->timestamp.'" WHERE token="'.$arg0->token.'"';
			$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);
			if($arg0->statusCode == '000')
			{
				//Operação validada
				$dados1 = explode('/',$arg0->merchantOperationID);
				$ID_ENCOMENDA = $dados1[0];

				$sql='UPDATE encomendas set TaxasPaypal=1 WHERE ID='.$ID_ENCOMENDA;
				$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);

				$sql='UPDATE mbway set Final_status=1 WHERE token="'.$arg0->token.'"';
				$res = ExecQuery($hConn, $GLOBALS['erp_DBName'], $sql);

				$dados = new clsRecordset();
				$sql="SELECT ID2,email,Des_Social FROM encomendas where ID='$ID_ENCOMENDA'";
				$hRes= $dados->Open( $hConn, $GLOBALS['erp_DBName'], $sql);
				if(!$dados->EOF())
				{
					$mailto = $dados->aFields["email"];
					$mailsubject = "Pagamento Encomenda ".$dados->aFields["ID2"];
					$body = "Caro(a) ".$dados->aFields["Des_Social"]."\n\nConfirmamos a receção do pagamento da sua encomenda e a mesma será processada assim que possível.\n\nCaso tenha alguma dúvida por favor envie um email para encomendas@pcdiga.com mencionando o número da encomenda.\nNão deixe de consultar a nossa FAQ em www.pcdiga.com/nm_faq.php\n\nCom os nossos melhores cumprimentos,\nPCDIGA, LDA.";

					$headers = "Content-Type: text/plain; charset=iso-8859-1\r\nFrom: ".$GLOBALS['Nome_Site']." <".$GLOBALS['form_recipient'].">\r\n";

					email($mailto,$mailsubject,$body,$headers,0);
				}
				$dados->close();

				//**********************************************************************************************
			}
			//fwrite($fh, $txt);
		}
	}

	$server = new SoapServer(null, array(
			'location' => 'https://www.pcdiga.com/pgm_mbway/getpayments.php',
			'uri' => 'http://webservices.sibsmerchant.com/FinancialOperationAsyncResult/financialOperationResultRequest',
			'encoding' => SOAP_ENCODED,
	));
	$server->setClass('FinancialOperationAsyncResult', $callback);
	$server->handle();
?>
