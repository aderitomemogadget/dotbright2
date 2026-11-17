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

	$GLOBALS['PAYSHOP_KEY'] = 'XSE-077653';

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
		}
		return $response;
	}


	$id_enc = intval($_GET['id_enc']);
	if(!$id_enc)die("0");

	$Estado=-1;//pendente


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
		// $dados=substr($Erros,'<EstadoPedidos>', strlen($Erros));
		// var_dump($dados);
		$id_retorno=substr($dados,0,strpos($dados,'</IdPedido>'));

		//$dados = json_decode($Erros, true);
		//$id_retorno = $dados["IdPedido"];

		if($Estado==0 && $id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			$responseData=request($GLOBALS['PAYSHOP_KEY'], '03',$id_retorno);
			$sql="UPDATE ifthen_mbway_logs set Erros2='".addslashes($responseData)."' WHERE ID = '$IDb' ";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			// var_dump($responseData);

			// //Aguarda
			// if(substr_count($responseData,'<Estado>000</Estado>')>0)
			// {
			// 	$Estado=0;
			// }
			// else
			// {
				//Cancelled by user
				if(substr_count($responseData,'</IdPedido><Estado>020</Estado>')>0)
				{
					$Estado=1;
					$sql="UPDATE ifthen_mbway_logs set Estado=1 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=15, Transaction_Code = '".$id_retorno."' where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				// if(substr_count($responseData,'</IdPedido><Estado>123</Estado>')>0)
				// {
				// 	$Estado=2;
				// 	$sql="UPDATE ifthen_mbway_logs set Estado=2 where ID=$IDb";
				// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
				// 	$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				// 	Runquery($db_link, $GLOBALS['DB_name'], $sql);
				// }
				if(substr_count($responseData,'</IdPedido><Estado>100</Estado>')>0)
				{
					$Estado=3;
					$sql="UPDATE ifthen_mbway_logs set Estado=3 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=15, Transaction_Code = '".$id_retorno."' where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'</IdPedido><Estado>000</Estado>')>0)
				{
					$Estado=13;
					$sql="UPDATE ifthen_mbway_logs set Estado=33 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=33, Transaction_Code = '".$id_retorno."' where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					enviarEmailCliente($ID_Encomenda,'', 33);
					//Agora deve enviar encomenda para o cliente de paga
				}
			// }

		}

	}
	$rs->db_close();


	echo $Estado;




?>