<?php
	/*CREATE TABLE `ifthen_mbway_logs` (
	  `ID` int(11) NOT NULL AUTO_INCREMENT,
	  `ID_Encomenda` int(11) NOT NULL,
	  `Data` datetime NOT NULL,
	  `Json` longblob NOT NULL,
	  `Estado` varchar(100) NOT NULL,
	  `Erros` longblob,
	  PRIMARY KEY (`ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;*/

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';

	$id_enc = 78306;
$mbway_tel='911154017';
$GLOBALS['MB_WAY_KEY'] = 'DTG-622087';


	/*function callIfthenpayMbWayAPI($mbw_key, $idspagamento)
	{
		$mbway_url_api = 'https://www.ifthenpay.com/mbwayWS/IfthenPayMBW.asmx/EstadoPedidosJSON';
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $mbway_url_api . '?MbWayKey=' . $mbw_key . '&canal=03&idspagamento='.$idspagamento, CURLOPT_USERAGENT => 'Ifthenpay Opencart Client'));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		//Close request to clear up some resources
		curl_close($curl);
		return json_decode($resp);
    }

    $res=callIfthenpayMbWayAPI($GLOBALS['MB_WAY_KEY'],'NosC6nUHExp35BI4tmfc');
    var_dump($res);


    die();*/

    function request($MbWayKey='', $canal='03', $idspagamento='')
    {
    	if (!is_null($MbWayKey) && !is_null($canal) && !is_null($idspagamento))
		{
			$soapUrl = 'https://www.ifthenpay.com/mbwayws/IfthenPayMBW.asmx';

			$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			  <soap:Body>
			    <EstadoPedidos xmlns="https://www.ifthenpay.com/">
			      <MbWayKey>'.$MbWayKey.'</MbWayKey>
			      <canal>'.$canal.'</canal>
			      <idspagamento>
			      	<string>'.$idspagamento.'</string>
			      </idspagamento>
			    </EstadoPedidos>
			  </soap:Body>
			</soap:Envelope>';
	        $headers = array(
                "Host: www.ifthenpay.com",
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: https://www.ifthenpay.com/EstadoPedidos",
                "Content-length: " . strlen($xml_post_string),
            );

            var_dump($xml_post_string);
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

		    var_dump($response);
			return $response;
		}
	}

	if(!$id_enc)die("0");

	$Estado=-1;


	$sql="SELECT * from ifthen_mbway_logs where ID_Encomenda=$id_enc order by ID DESC";
	$rs = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['ID_Encomenda'];
		$Data= $rs->db_data['Data'];
		$Json= $rs->db_data['Json'];
		$Estado= $rs->db_data['Estado'];
		$Erros= $rs->db_data['Erros'];

		$dados=substr($Erros,strpos($Erros,'<IdPedido>')+10, strlen($Erros));
		$id_retorno=substr($dados,0,strpos($dados,'</IdPedido>'));

		//$dados = json_decode($Erros, true);
		//

		if($Estado==0 && $id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			$responseData=request($GLOBALS['MB_WAY_KEY'], '03',$id_retorno);
var_dump($responseData);
			// if(substr_count($responseData,'Cancelled by user')>0)
			// {
			// 	$Estado=1;
			// 	$sql="UPDATE ifthen_mbway_logs set Estado=1 where ID=$IDb";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// 	$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// }
			// if(substr_count($responseData,'user authorization failed')>0)
			// {
			// 	$Estado=2;
			// 	$sql="UPDATE ifthen_mbway_logs set Estado=2 where ID=$IDb";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// 	$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// }
			// if(substr_count($responseData,'transaction declined')>0)
			// {
			// 	$Estado=3;
			// 	$sql="UPDATE ifthen_mbway_logs set Estado=3 where ID=$IDb";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// 	$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// }

			// if(substr_count($responseData,'Request successfully')>0)
			// {
			// 	$Estado=13;
			// 	$sql="UPDATE ifthen_mbway_logs set Estado=13 where ID=$IDb";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// 	$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
			// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// 	enviarEmailCliente($ID_Encomenda,'', 13);
			// 	//Agora deve enviar encomenda para o cliente de paga
			// }
		}

	}
	$rs->db_close();


	echo $Estado;




?>