<?php

	$path =  dirname( __FILE__ ) . '/../../../core/';
	include $path.'core_files.php';

	/*$authentication_entityId = '8a8294185332bbe601533754724914d9'; //Para producao
	$authentication_entityId = '8ac7a4c77279a71001727f745156182e'; //Para testes

	$Bearer = 'OGE4Mjk0MTg1YjY3NDU1NTAxNWI3YzE5MjhlODE3MzZ8UnI0N2VRZXNkVw==';
	$Entidade = '21075';//Para producao
	$Entidade = '25002';//Para testes*/


	error_reporting(E_ALL,~E_NOTICE);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

    //testes:
	$Bearer ='eyJhbGciOiJIUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJlNzYyMzE3Yi03N2IxLTQ0ZWItYTUzYy0zMjY1ZDY5NTllZGIifQ.eyJpYXQiOjE2NTA0NzI4NzcsImp0aSI6IjM0Y2YyZGE4LTM2YTktNDIxOC1hM2FmLWFlNDMwMjI3ZTQ1MCIsImlzcyI6Imh0dHBzOi8vcWx5LnNpdGUxLnNzby5zeXMuc2licy5wdC9hdXRoL3JlYWxtcy9RTFkuU1BHLkFQSSIsImF1ZCI6Imh0dHBzOi8vcWx5LnNpdGUxLnNzby5zeXMuc2licy5wdC9hdXRoL3JlYWxtcy9RTFkuU1BHLkFQSSIsInN1YiI6Ijk3ZGE0NDM0LWFjZDYtNDZjNC1iYmI0LWM4YTU1MzJkYTc0NyIsInR5cCI6Ik9mZmxpbmUiLCJhenAiOiJRTFkuU1BHLkFQSS1DTEkiLCJzZXNzaW9uX3N0YXRlIjoiZDdkNzAzMjEtMTIxNS00NWVhLThiMjAtOTk4MjhjNDAzOTYzIiwic2NvcGUiOiJvcGVuaWQgb2ZmbGluZV9hY2Nlc3MifQ.mMHSZpjdC6nMGUu1xxSHOrD0-MmZRjjbR_UfTtENZns.eyJtYyI6Ijk5OTk5OTkiLCJ0YyI6IjUzODY1In0=.7CADF3550A60B7924EE7A17E3EE3CEE802D4EE8263CD6F08B17AF0356F3073D3';
	$authentication_entityId = '043d04e6-6ba6-4e03-a4ec-71ae4ba6cc3b';
	$authentication_entityId = '043d04e66ba64e03a4ec71ae4ba6cc3b';
	$Entidade = '24000';




	//INICIO TRATAMENTO DEFINI��ES REGIONAIS
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

	// FIM TRATAMENTO DEFINI��ES REGIONAIS
	function GenerateRef($ent_chave, $ent_nif, $order_id, $order_value, $sanbox = 0,$customer_name='')
	{
		global $Entidade,$authentication_entityId,$Bearer;

		$order_value= sprintf("%01.2f", $order_value);
		$order_value =  format_number($order_value);
		//Apenas sao considerados os 4 caracteres mais a direita do order_id
		//$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));
		if ($order_value < 2)
		{
			echo "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 2 Euro";
			return;
		}

		$data1=date("Y-m-d");
		$time1=date("h:m:s");

		if($sanbox==1)
	    {
			$data2=date('Y-m-d',strtotime($date1 . "+1 day"));
	        $url = "https://test.oppwa.com/v1/payments";
	        $data = "entityId=".$authentication_entityId.
	                "&amount=$order_value".
	                "&currency=EUR" .
	                "&paymentType=PA" .
	                "&paymentBrand=SIBS_MULTIBANCO" .
	                "&merchantTransactionId=$order_id" .
	                "&customParameters[SIBSMULTIBANCO_PtmntEntty]=$Entidade" .
	                "&customParameters[SIBSMULTIBANCO_RefIntlDtTm]=".urlencode($data1."T".$time1.".001+01:00").
	                "&customParameters[SIBSMULTIBANCO_RefLmtDtTm]=".urlencode($data2."T".$time1.".001+01:00").
	                "&customer.ip=".urlencode("176.111.107.44").
	                "&customer.givenName=".urlencode("$customer_name").
	                "&billing.country=PT" .
	                "&testMode=EXTERNAL" .
	                "&customParameters[SIBS_ENV]=QLY" .
	                "&shopperResultUrl=".urlencode("https://www.biominds.com/external_api/pagamentos/pgm_sibs/ipn.php");

	                //pediram para colocar DB e INERNAL
	    }
	    else
	    {
			$data2=date('Y-m-d',strtotime($date1 . "+3 days"));
	    	//Colocar o nome do cliente
	        $url = "https://oppwa.com/v1/payments";
	        $data = "entityId=".$authentication_entityId.
	                "&amount=$order_value".
	                "&currency=EUR" .
	                "&paymentType=PA" .
	                "&paymentBrand=SIBS_MULTIBANCO" .
	                "&merchantTransactionId=$order_id" .
	                "&customParameters[SIBSMULTIBANCO_PtmntEntty]=$Entidade" .
	                "&customParameters[SIBSMULTIBANCO_RefIntlDtTm]=".urlencode($data1."T".$time1.".001+01:00").
	                "&customParameters[SIBSMULTIBANCO_RefLmtDtTm]=".urlencode($data2."T".$time1.".001+01:00").
	                "&customer.ip=".urlencode("176.111.107.44").
	                "&customer.givenName=".urlencode("$customer_name").
	                "&billing.country=PT" .
	                "&shopperResultUrl=".urlencode("https://www.biominds.com/external_api/pagamentos/pgm_sibs/ipn.php");
	    }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization:Bearer '.$Bearer));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		
		var_dump($response);

		if(curl_errno($ch)) {
		return curl_error($ch);
		}
		curl_close($ch);


		$responseData2 = json_decode($response, true);

		$referencia=$responseData2["resultDetails"];
		$referenceMB = $referencia["pmtRef"];


		//*************************/
		if($referenceMB != '')
		{
			$refs = $referenceMB;

			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs = new DataObj;
			$rs->db_open($db_link, $DB_name, 'SELECT id_order FROM sibsreferences WHERE id_order = '.$order_id);
			if(!$rs->EOF())
			{
				$sql='UPDATE sibsreferences SET refMB = "'.$refs.'", value = "'.$order_value.'",Response="'.addslashes($response).'", Date="'.date('Y-m-d H:m:i').'" WHERE id_order = "'.$order_id.'"';
			}
			else $sql='INSERT INTO sibsreferences (id_order, refMB, value, Response,Date) values ('.$order_id.', "'.$refs.'", "'.$order_value.'","'.addslashes($response).'","'.date('Y-m-d H:m:i').'")';
			$rs->db_close();

			$res = Runquery($db_link,$DB_name,$sql);

			$sql = "UPDATE encomendas SET Ent_atm='".$entidade."',Ref_atm='".str_replace(' ', '', $refs)."' WHERE ID='" . $order_id . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		else
		{
			echo 'ERRO, não foi possivel gerar a referencia';
			//$message = "/<a:message>(.*?)<\/a:message>/s";
			//if(preg_match($message,$response,$message_value)){echo $message_value[1];}
		}
		return true;
	}

	function getRef($order_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Entidade;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;

		$table_name = "sibsreferences";
		$rs->db_open($db_link,$DB_name, 'SELECT refMB,value FROM '.$table_name.' WHERE id_order = '.$order_id);
		while(!$rs->EOF())
		{
			$refs = $rs->db_data["refMB"];
			$order_value = $rs->db_data["value"];

			if($refs != -1)
			{
				$refs = substr($refs,0,3).' '.substr($refs,3,3).' '.substr($refs,6,3);
				$tabelaMulti = '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc;margin:0 auto" align="center">';
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by SIBS)</center></td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				//$tabelaMulti .=	'<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_MB125x80px.png" alt=""/></div></td>';
				$tabelaMulti .=	'<td rowspan="4"><div align="center">SIBS</div></td>';
				$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>';
				$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.$Entidade.'</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
				$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'. $refs.'</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
				$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.number_format($order_value, 2,',', ' ').'&nbsp;&euro;</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
				$tabelaMulti .= '<td style="font-size: x-small; text-align:left">&nbsp;</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "</table>";
			}
			$rs->db_next();
		}
		$rs->db_close();


		if($order_id > 0 )
		{
			// $id_enc = $_GET['id_enc'];
			// $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
			$sql = 'UPDATE encomendas SET enc_pagamento_dados="'.addslashes($tabelaMulti).'" WHERE ID = '.$order_id;
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}

		return $tabelaMulti;
	}

	// $path = '../../core/';
	// require_once $path . 'core_files.php';
	$rs_metodo = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);



	$sql = "SELECT chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=11";
	$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$sandbox = 0;
	$conta_ifthen = '';
	if (!$rs_metodo->EOF())
	{
		$chave = $rs_metodo->db_data["chavebo"];
		$entidade = $rs_metodo->db_data["entidade"];
		$sandbox = $rs_metodo->db_data['sandox'];
	}
	$rs_metodo->db_close();

	//$chave = '14B6CA9A-AA8F-4882-BE45-31271FB8019F';
	//$entidade = '502291990';

	$sandbox=1;


	if ($chave && $entidade && $_GET['id_enc'] > 0)
	{
		$id_enc = $_GET['id_enc'];
		$rs = new DataObj;
		$sql_all = "SELECT TotalDocComIva FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor = $rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();

		if(GenerateRef($chave, $entidade, $id_enc, number_format($valor , 2, '.', ''), $sandbox,$m_customer_name))
		{
			echo getRef($id_enc);
		}
		else
		{
			$msg_tmp.='Não foi possivel gerar referência.';
		}
		echo $msg_tmp;
	}
?>