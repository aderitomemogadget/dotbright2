<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
	$id_enc=intval($_GET['id_enc']);
	$GLOBALS['PAYSHOP_KEY'] = 'XSE-077653';


	function get_api_key()
	{
		return 'bd3d-07b6-6b87-9a91-335a';
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

	function sendPayshopRequest($payshopkey='',$id_enc='',$valor='')
	{
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="INSERT INTO ifthen_payshop_logs (ID_Encomenda,Data,Estado,Erros) VALUES('".$id_enc."','".date("Y-m-d H:m:i")."','0','')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);

		if (!is_null($payshopkey) && !is_null($id_enc) && !is_null($valor) )
		{
			$response=getReferenciaPS($id_enc, $valor);

			//$sql="UPDATE ifthen_payshop_logs set Erros='".addslashes($response)."' WHERE ID = '$ID_Log' ";
			//$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);

		    return $response;

        }
        else return 'Informação incompleta para poder gerar a referencia payshop!';

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

		$refgen = sendPayshopRequest($GLOBALS['PAYSHOP_KEY'],$id_enc,$valor);
		$refgen2=(array)$refgen;


		if($refgen!='')
		{
			$msg_tmp.= '<table width="350" border=0 cellspacing=0 cellpadding=0 style="border:1px solid grey;margin:0 auto;border-collapse:inherit;border-spacing:10px;">';
			$msg_tmp.= '<tr><td align="center"><img style="width:150px" src="' .SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_EUPAGO]['url']. 'payshop.png" border="0" /></td></tr>';
			$msg_tmp.= "<tr><td align='center' style='font-weight:bold;'>&euro;&nbsp;" . number_format($valor , 2, ',', ' ').'</td></tr>';
			$msg_tmp.= "<tr><td align='center' style='font-weight:bold'>".$refgen2['referencia'].'</td></tr>';
			$msg_tmp.= '<tr><td align="center" style="font-size:13px">Para pagamento em agentes Payshop.</td></tr>';
			$msg_tmp.= '</table>';

			$sql = 'UPDATE encomendas SET enc_pagamento_dados="'.addslashes($msg_tmp).'" WHERE ID = '.$id_enc;
			Runquery($db_link, $GLOBALS['DB_name'], $sql);

		}else{
			$msg_tmp.='Não foi possivel gerar referência.';
		}
		echo $msg_tmp;

	}
?>