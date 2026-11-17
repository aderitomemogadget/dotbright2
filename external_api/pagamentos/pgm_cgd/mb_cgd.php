<?php

	$path =  dirname( __FILE__ ) . '/../../../core/';
	include $path.'core_files.php';

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

	$authentication_entityId = '8a8294185332bbe601533754724914d9'; //Para producao
	$authentication_entityId = '8ac7a4c77279a71001727f745156182e'; //Para testes


    $Bearer = 'OGE4Mjk0MTg1YjY3NDU1NTAxNWI3YzE5MjhlODE3MzZ8UnI0N2VRZXNkVw==';
    $Entidade = '21075';//Para producao
    $Entidade = '25002';//Para testes

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
	function GenerateRef($Entidade,$Bearer, $authentication_entityId, $order_id, $order_value, $sanbox = 0)
	{
		$order_value= sprintf("%01.2f", $order_value);
		$order_value =  format_number($order_value);
		if ($order_value < 2)
		{
			echo "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 2 Euro";
			return;
		}
		$data1=date("Y-m-d");
		$time1=date("h:m:s");
		$data2=date('Y-m-d',strtotime($date1 . "+3 days"));
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
	                "&customer.surname=David" .
	                "&customer.givenName=Paixao" .
	                "&billing.country=PT" .
	                "&testMode=EXTERNAL" .
	                "&customParameters[SIBS_ENV]=QLY" .
	                "&shopperResultUrl=".urlencode("https://www.davidpaixao.com/external_api/pagamentos/pgm_cgd/ipn.php");

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	                   'Authorization:Bearer '.$Bearer));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = curl_exec($ch);
	    if(curl_errno($ch))
	    {
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
			$rs->db_open($db_link, $DB_name, 'SELECT id_order FROM cgdreferences WHERE id_order = '.$order_id);
			if(!$rs->EOF())
			{
				$sql='UPDATE cgdreferences SET refMB = "'.$refs.'", value = "'.$order_value.'",Response="'.addslashes($response).'", Date="'.date('Y-m-d H:m:i').'" WHERE id_order = "'.$order_id.'"';
			}
			else $sql='INSERT INTO cgdreferences (id_order, refMB, value, Response,Date) values ('.$order_id.', "'.$refs.'", "'.$order_value.'","'.addslashes($response).'","'.date('Y-m-d H:m:i').'")';
			$rs->db_close();
			$res = Runquery($db_link,$DB_name,$sql);
			$sql = "UPDATE encomendas SET Ent_atm='".$entidade."',Ref_atm='".str_replace(' ', '', $refs)."' WHERE ID='" . $order_id . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		else
		{
			echo 'ERRO, não foi possivel gerar a referencia';
		}
		return true;
	}

	function getRef($order_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Entidade;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;

		$table_name = "cgdreferences";
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
				$tabelaMulti .=	'<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by CGD)</center></td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				//$tabelaMulti .=	'<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_MB125x80px.png" alt=""/></div></td>';
				$tabelaMulti .=	'<td rowspan="4"><div align="center">CGD</div></td>';
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

	$rs_metodo = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);



	$sql = "SELECT chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=".MP_TP_MB_CGD;
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


	if ($_GET['id_enc'] > 0)
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

		if(GenerateRef($Entidade,$Bearer, $authentication_entityId, $id_enc, number_format($valor , 2, '.', ''), $sandbox))
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