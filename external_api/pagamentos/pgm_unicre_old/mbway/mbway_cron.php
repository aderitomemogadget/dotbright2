<?php

//**************************************************
// evitar usar este metodo, mas está a funcionar
//**************************************************

	$includes_path=dirname( __FILE__ )."/../../../core/";
	require_once $path.'core_files.php';

	//4 minutos
    $sandbox=1;
	if($sandbox==1)
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


	

    function request($id_retorno,$authentication_entityId,$Bearer,$valor=0,$tel='',$id_enc=0)
    {
		$url = "https://test.oppwa.com/v1/payments/".$id_retorno;
		$url .= "?entityId=".$authentication_entityId;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer '.$Bearer));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if(curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		return $responseData;
	}


	$sql="SELECT * from sibs_mbway_logs where Estado=0";
	$rs = new DataObj;
	$db_link = db_opendb_conection(SERVER,USERNAME,DB_PASSWORD);
	$rs->db_open($db_link,DBNAME, $sql);
	while (!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['ID_Encomenda'];
		$ID_Pagamento_unicre = str_replace('Enc','',$ID_Encomenda);

		$Data= $rs->db_data['Data'];
		$Json= $rs->db_data['Json'];
		$Estado= $rs->db_data['Estado'];
		$Erros= $rs->db_data['Erros'];

		$dados = json_decode($Erros, true);

//		var_dump($dados);

		$id_retorno = $dados["id"];

		echo $id_retorno."\n";

		if($id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			$responseData = request();
			$responseData=request($id_retorno,$authentication_entityId,$Bearer,$valor,$mbway_tel,$id_enc);
			var_dump($responseData);


			if(substr_count($responseData,'Cancelled by user')>0)
			{
				$sql="UPDATE sibs_mbway_logs set Estado=1 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);

				$sql="UPDATE pagamentos_unicre SET Estado=1 where ID=$ID_Pagamento_unicre";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'user authorization failed')>0)
			{
				$sql="UPDATE sibs_mbway_logs set Estado=2 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);

				$sql="UPDATE pagamentos_unicre SET Estado=2 where ID=$ID_Pagamento_unicre";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'transaction declined')>0)
			{
				$sql="UPDATE sibs_mbway_logs set Estado=3 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);

				$sql="UPDATE pagamentos_unicre SET Estado=3 where ID=$ID_Pagamento_unicre";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'cannot find transaction')>0)
			{
				$sql="UPDATE sibs_mbway_logs set Estado=4 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);

				$sql="UPDATE pagamentos_unicre SET Estado=4 where ID=$ID_Pagamento_unicre";
				Runquery($db_link,DBNAME, $sql);
			}

			if(substr_count($responseData,'Request successfully')>0)
			{
				$sql="UPDATE sibs_mbway_logs set Estado=13 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);


				$sql="UPDATE pagamentos_unicre SET Estado=13 where ID=$ID_Pagamento_unicre";
				Runquery($db_link,DBNAME, $sql);
				/*$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
				Runquery($db_link,DBNAME, $sql);
				enviarEmailCliente($ID_Encomenda,'', 13);*/
			}
		}
		echo "<hr>";
//		echo $Erros;

		$rs->db_next();
	}
	$rs->db_close();


?>