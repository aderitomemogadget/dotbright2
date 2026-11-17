<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';

	$mbway_tel=$_GET['mbway_tel'];
	$mbway_tel=str_replace('+351','',$mbway_tel);
	$mbway_tel=str_replace('00351','',$mbway_tel);
	$id_enc=intval($_GET['id_enc']);

	$GLOBALS['MB_WAY_KEY'] = 'DTG-622087';

	function sendMBWayRequest($MbWayKey='', $canal=3, $referencia='', $valor='', $nrtlm='', $email='', $descricao='')
	{
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="INSERT INTO ifthen_mbway_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$referencia."','".date("Y-m-d H:m:i")."', 'Tel = $nrtlm | Valor = $valor', '0','')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);

		if (!is_null($MbWayKey) && !is_null($canal) && !is_null($referencia) && !is_null($valor) && !is_null($nrtlm) )
		{
			$soapUrl = 'https://www.ifthenpay.com/mbwayws/IfthenPayMBW.asmx';

			$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
				<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				  <soap:Body>
				    <SetPedido xmlns="https://www.ifthenpay.com/">
				      <MbWayKey>'.$MbWayKey.'</MbWayKey>
				      <canal>'.$canal.'</canal>
				      <referencia>'.$referencia.'</referencia>
				      <valor>'.$valor.'</valor>
				      <nrtlm>'.$nrtlm.'</nrtlm>
				      <email>'.$email.'</email>
				      <descricao>'.$descricao.'</descricao>
				    </SetPedido>
				  </soap:Body>
				</soap:Envelope>';
	        $headers = array(
                "Host: www.ifthenpay.com",
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: https://www.ifthenpay.com/SetPedido",
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
            if(curl_errno($ch)) {
		        return curl_error($ch);
		    }
		    curl_close($ch);

		    $sql="UPDATE ifthen_mbway_logs set Erros='".addslashes($response)."' WHERE ID = '$ID_Log' ";
			$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);
		    return $response;

            /*if(!empty($response))
            {

	            $currency = '€';
	        	$tabela = '<div align="center">
	        	<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc" align="center">
	        	<tr>
	        		<td style="font-size: small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por MB Way (by -IFTHEN)</center></td>
	        	</tr>
	        	<tr>
	            	<td rowspan="3"><div align="center"><img src="mbway.jpg" alt=""/></div></td>
	            </tr>
	            <tr>
	                <td style="font-size: small; font-weight:bold; text-align:left">Telemóvel:</td>
	                <td style="font-size: small; text-align:left">'. $nrtlm .'</td>
	            </tr>
	            <tr>
	            	<td style="font-size: small; font-weight:bold; text-align:left">Valor:</td>
	                <td style="font-size: small; text-align:left">' . number_format( $valor, 2, ',', ' ' ). ' ' . $currency . '</td>
	            </tr>
	            <tr>
	                <td style="font-size: x-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>Caso não efetuar o pagamento dentro de 5 minutos o seu pedido será cancelado!</center></td>
	            </tr>
	        	</table></div>';
            }*/

        }
        else return 'Informação incompleta para poder invocar o serviço MB Way!';


        //return $tabela;
	}


	if ($id_enc > 0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;

		$sql_all = "SELECT TotalDocComIva,enc_Email FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor=$rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();
		$valor=number_format($valor,2,'.','');

		$res = sendMBWayRequest($GLOBALS['MB_WAY_KEY'], '3', $id_enc, $valor, $mbway_tel, $enc_Email, '');

		echo '<script>verifica_mbway("'.$id_enc.'");</script>';
	}
?>