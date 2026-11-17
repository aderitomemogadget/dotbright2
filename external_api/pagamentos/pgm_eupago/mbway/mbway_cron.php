<?php

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
    
    global $Entidade;
	$Entidade="12142";

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);


    function get_api_key()
	{
		return 'bd3d-07b6-6b87-9a91-335a';
	}

    function informacaoReferencia($chave_api, $referencia, $entidade)
    {
		// chamada do serviço SOAP - produção
		$client = @new SoapClient('https://clientes.eupago.pt/eupagov20.wsdl');
		//$client = @new SoapClient('https://clientes.eupago.pt/eupagov20.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
		// chamada do serviço SOAP - sandbox
		//$client = @new SoapClient('https://sandbox.eupago.pt/replica.eupagov20.wsdl');



		$arraydados = array (
			"chave" => get_api_key(),
			"referencia" => $referencia,
			"entidade" => $entidade
		);


		$result = $client->informacaoReferencia($arraydados);
		// verifica erros na execução do serviço e exibe o resultado
		if (is_soap_fault($result))
		{
		
			echo "SOAP Fault:(faultcode: ".$result->faultcode.",faultstring: ".$result->faultstring;
		
		} 
		else 
		{
			//estados possíveis: 0 sucesso. -10 Chave inválida. -9 Valores incorretos
			if ($result->estado == 0)
			{
				//colocar a ação de sucesso
				return $result;
			} 
			else 
			{
				//ação insucesso
			}
		}
	}




	echo $sql="SELECT * from eupago_mbway_logs where Estado=0 order by ID DESC";
	$rs = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	while (!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['id_order'];
		$Data= $rs->db_data['Date'];
		$Estado= $rs->db_data['Estado'];
		$Erros= $rs->db_data['Response'];
		$referencia= $rs->db_data['refMB'];

		$dados =informacaoReferencia($chave_api, $referencia, $Entidade);
		$dados=serialize($dados);

		if(substr_count($dados,'estado_referencia";s:4:"paga"')>0)
		{
			$sql="UPDATE eupago_mbway_logs set Estado=1 where ID=$IDb";
			$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);
			$sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='13' WHERE ID='" . $ID_Encomenda . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		

		$rs->db_next();
	}
	$rs->db_close();
