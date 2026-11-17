<?php

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	//include $path.'core_files.php';


	$chave='bd3d-07b6-6b87-9a91-335a';
	$id_enc=4;
	$valor=10;


		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(-1);

	$res = getReferenciaPS($id_enc,$valor);
	$responseData2 = (array)$res;

var_dump($responseData2);
	echo	$Entidade=$responseData2["entidade"];
		$referenceMB = $responseData2["referencia"];



	function get_api_key()
	{
		return 'bd3d-07b6-6b87-9a91-335a';
	}

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
	function getReferenciaMB($order_id, $valor, $per_dup = 0, $deadline = null)
	{
	    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
	    $args = array(
	      'chave' => get_api_key(),
	      'valor' => format_number( $valor ),
	      'id' => $order_id,
	      'per_dup' => $per_dup
	    );

	    if ( isset( $deadline ) && !empty( $deadline ) ) {
	      $args['data_inicio'] = date('Y-m-d');
	      $args['data_fim'] = date('Y-m-d', strtotime('+' . $deadline . ' day', strtotime( $args['data_inicio'] ) ) );
	      return $client->gerarReferenciaMBDL( $args );
	    }

	    return $client->gerarReferenciaMB( $args );
    }

    function getReferenciaPS($order_id, $valor)
    {
	    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
	    return $client->gerarReferenciaPS(array(
	      "chave" => get_api_key(),
	      "valor" => format_number($valor),
	      "id" => $order_id
	    ));
	  }



	  function getReferenciaPQ( $order_id, $valor ) {
    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->gerarReferenciaPQ(array(
      "chave" => get_api_key(),
      "valor" => format_number( $valor ),
      "id" => $order_id
    ));
  }



  function getReferenciaMBW($order_id, $valor, $telefone) {
    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->pedidoMBW(array(
      "chave" => get_api_key(),
      "valor" => format_number($valor),
      "id" => $order_id,
      "alias" => $telefone
    ));
  }



  function pedidoCC($order, $valor, $logo_url, $return_url, $lang, $comment) {
    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->pedidoCC(array(
      'chave' => get_api_key(),
      'valor' => format_number( $valor ),
      'id' => $order->get_id(),
      'url_logotipo' => $logo_url,
      'url_retorno' => $return_url,
      'nome' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
      'email' => $order->get_billing_email(),
      'lang' => $lang,
      'comentario' => $comment,
      'tds' => 1
    ));
  }



  function pedidoPF($order, $valor, $return_url, $comment)
  {
    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->pedidoPF(array(
      'chave' => get_api_key(),
      'valor' => format_number($valor),
      'id' => $order->get_id(),
      'admin_callback' => '',
      'url_retorno' => $return_url,
      'comentario' => $comment,
    ));
  }



  function pedidoPSC($order, $valor, $return_url, $lang, $comment)
  {
    $client = @new SoapClient('https://clientes.eupago.pt/eupagov16.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->pedidoPSC(array(
      'chave' => get_api_key(),
      'valor' => format_number($valor),
      'id' => $order->get_id(),
      'url_retorno' => $return_url,
      'comentario' => $comment,
      'admin_callback' => '',
      'nome' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
      'email' => $order->get_billing_email(),
      'lang' => $lang,
    ));
  }

?>