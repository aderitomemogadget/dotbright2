<?php

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';

	$mbway_tel=$_GET['mbway_tel'];
	$mbway_tel=str_replace('+351','',$mbway_tel);
	$mbway_tel=str_replace('00351','',$mbway_tel);
	$id_enc=intval($_GET['id_enc']);
    

    global $Entidade;
	$Entidade="12142";

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);


    function get_api_key()
	{
		return 'bd3d-07b6-6b87-9a91-335a';
	}
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

    function pedidoMBW($chave_api, $nota_de_encomenda, $valor_da_encomenda, $alias)
    {
		// chamada do serviço SOAP - produção
		$client = @new SoapClient('https://clientes.eupago.pt/eupagov20.wsdl');



		$arraydados = array (
			"chave" => get_api_key(),
			"valor" => format_number($valor_da_encomenda),
			"id" => $nota_de_encomenda,
			"alias" => $alias
		);


		$result = $client->pedidoMBW($arraydados);
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


	
	if ($id_enc > 0)
	{
		$rs = new DataObj;
		$sql_all = "SELECT TotalDocComIva FROM encomendas WHERE encomendas.ID='".$id_enc."'";
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor = $rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();

		$valor=number_format($valor,2,'.','');
		$responseData=pedidoMBW($chave_api,$id_enc,$valor,$mbway_tel);
		$responseData=serialize($responseData);

		$sql='INSERT INTO eupago_mbway_logs (ID_Encomenda,Data,Json,Estado,Erros) values ("'.$id_enc.'", "'.date('Y-m-d H:m:i').'", "","0","'.addslashes($responseData).'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);


		echo '<script>verifica_mbway("'.$id_enc.'");</script>';
	}

?>