<?php

	$includes_path = "/home/app/public_html/admin/core/";
	include $includes_path."includes.inc.php";

	$authentication_entityId = ''; //Para producao
	$authentication_entityId = '8ac7a4c778260c340178268392c50af4'; //Para testes
	$Bearer = 'OGFjN2E0Yzk3NGQ0Yjg4YjAxNzRkOGI4MWMzNTA2Zjh8OFI4Y3F6emNXbg==';
	$Entidade = '';//Para producao
	$Entidade = '23867';//Para testes

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


	$sql="SELECT * from sibsreferences where Estado=0";
	$rs = new DataObj;
	$db_link = db_opendb_conection(SERVER,USERNAME,DB_PASSWORD);
	$rs->db_open($db_link,DBNAME, $sql);
	while (!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['id_order'];
		$Data= $rs->db_data['Data'];
		$Estado= $rs->db_data['Estado'];
		$Erros= $rs->db_data['Response'];

		$dados = json_decode($Erros, true);

		$id_retorno = $dados["id"];

		echo "id_retorno=$id_retorno\n";

		if($id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			$responseData = request();
echo			$responseData=request($id_retorno,$authentication_entityId,$Bearer,$valor,$mbway_tel,$id_enc);

			if(substr_count($responseData,'Cancelled by user')>0)
			{
				$sql="UPDATE sibsreferences set Estado=1 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'user authorization failed')>0)
			{
				$sql="UPDATE sibsreferences set Estado=2 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'transaction declined')>0)
			{
				$sql="UPDATE sibsreferences set Estado=3 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'cannot find transaction')>0)
			{
				$sql="UPDATE sibsreferences set Estado=4 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);
			}
			if(substr_count($responseData,'"code":"000.000.000"')>0 && substr_count($responseData,'"paymentType":"RC"')>0)
			{
				$sql="UPDATE sibsreferences set Estado=13 where ID=$IDb";
				Runquery($db_link,DBNAME, $sql);
				/*$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
				Runquery($db_link,DBNAME, $sql);
				enviarEmailCliente($ID_Encomenda,'', 13);*/
				//Agora deve enviar encomenda para o cliente de paga
			}

		}
		$rs->db_next();
	}
	$rs->db_close();
?>