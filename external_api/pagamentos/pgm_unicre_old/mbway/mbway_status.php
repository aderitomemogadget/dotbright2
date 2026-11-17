<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	require_once $path.'core_files.php';
	//$path =  dirname( __FILE__ ) . '/../../../../core/';
	//Credenciais de producao:
    /*$sandbox=1;
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
	}*/


    /*function request($id_retorno,$authentication_entityId,$Bearer,$valor=0,$tel='',$id_enc=0)
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
	}*/

	$id_enc = intval($_GET['id_enc']);
	if(!$id_enc)die("0");

	$Estado=-1;


	$sql="SELECT * from sibs_mbway_logs where ID_Encomenda=$id_enc";
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
		$responseData=$Erros= $rs->db_data['Erros'];
		$dados = json_decode($Erros, true);
		$id_retorno = $dados["id"];
		if($Estado==0 && $id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			
			//$responseData=request($id_retorno,$authentication_entityId,$Bearer,$valor,$mbway_tel,$id_enc);

			if(substr_count($responseData,'Cancelled by user')>0)
			{
				$Estado=1;
				$sql="UPDATE sibs_mbway_logs set Estado=1 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'user authorization failed')>0)
			{
				$Estado=2;
				$sql="UPDATE sibs_mbway_logs set Estado=2 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'transaction declined')>0)
			{
				$Estado=3;
				$sql="UPDATE sibs_mbway_logs set Estado=3 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}

			if(substr_count($responseData,'Request successfully')>0)
			{
				$Estado=13;
				$sql="UPDATE sibs_mbway_logs set Estado=13 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				//enviarEmailCliente($ID_Encomenda,'', 13);
				//o email é enviado pelo webhook
			}
		}

	}
	$rs->db_close();


	echo $Estado;


?>