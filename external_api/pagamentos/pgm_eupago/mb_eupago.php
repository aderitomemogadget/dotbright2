<?php

	$path =  dirname( __FILE__ ) . '/../../../core/';
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

	function GenerateRef($ent_chave, $order_id, $order_value)
	{
		$order_value= sprintf("%01.2f", $order_value);
		$order_value =  format_number($order_value);
		//Apenas sao considerados os 4 caracteres mais a direita do order_id
		//$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));
		if ($order_value < 2)
		{
			echo "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 2 Euro";
			return;
		}

		$response = getReferenciaMB($order_id,$order_value);
	    $responseData2 = (array)$response;

		$Entidade=$responseData2["entidade"];
		$referenceMB = $responseData2["referencia"];

		//*************************/
		if($referenceMB != '')
		{
			$refs = $referenceMB;

			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs = new DataObj;
			$rs->db_open($db_link, $DB_name, 'SELECT id_order FROM eupagoreferences WHERE id_order = '.$order_id);
			if(!$rs->EOF())
			{
				$sql='UPDATE eupagoreferences SET refMB = "'.$refs.'", value = "'.$order_value.'",Response="'.addslashes($response).'", Date="'.date('Y-m-d H:m:i').'" WHERE id_order = "'.$order_id.'"';
			}
			else $sql='INSERT INTO eupagoreferences (id_order, refMB, value, Response,Date) values ('.$order_id.', "'.$refs.'", "'.$order_value.'","'.addslashes($response).'","'.date('Y-m-d H:m:i').'")';
			$rs->db_close();

			$res = Runquery($db_link,$DB_name,$sql);

			$sql = "UPDATE encomendas SET Ent_atm='".$entidade."',Ref_atm='".str_replace(' ', '', $refs)."' WHERE ID='" . $order_id . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		else
		{
			echo 'ERRO, não foi possivel gerar a referencia';
		}
		return true;
	}

	function getRef($order_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Entidade;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;

		$table_name = "eupagoreferences";
		$rs->db_open($db_link,$DB_name, 'SELECT refMB,value FROM '.$table_name.' WHERE id_order = '.$order_id);
		while(!$rs->EOF())
		{
			$refs = $rs->db_data["refMB"];
			$order_value = $rs->db_data["value"];

			if($refs != -1)
			{
				$refs = substr($refs,0,3).' '.substr($refs,3,3).' '.substr($refs,6,3);
				$tabelaMulti = '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc;margin:0 auto" align="center">';
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by EUPAGO)</center></td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td rowspan="4"><div align="center">EUPAGO</div></td>';
				$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>';
				$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.$Entidade.'</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
				$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'. $refs.'</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
				$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.number_format($order_value, 2,',', ' ').'&nbsp;&euro;</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
				$tabelaMulti .= '<td style="font-size: x-small; text-align:left">&nbsp;</td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "<tr>";
				$tabelaMulti .=	'<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>';
				$tabelaMulti .= "</tr>";
				$tabelaMulti .= "</table>";
			}
			$rs->db_next();
		}
		$rs->db_close();


		if($order_id > 0 )
		{
			$sql = 'UPDATE encomendas SET enc_pagamento_dados="'.addslashes($tabelaMulti).'" WHERE ID = '.$order_id;
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}

		return $tabelaMulti;
	}

	$rs_metodo = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

	/*$sql = "SELECT chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=".MP_TP_MB_EUPAGO;
	$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$sandbox = 0;
	$conta_ifthen = '';
	if (!$rs_metodo->EOF())
	{
		$chave = $rs_metodo->db_data["chavebo"];
		$entidade = $rs_metodo->db_data["entidade"];
		$sandbox = $rs_metodo->db_data['sandox'];
	}
	$rs_metodo->db_close();*/

	//$chave = '14B6CA9A-AA8F-4882-BE45-31271FB8019F';
	//$entidade = '502291990';

	/*if ($chave && $entidade && $_GET['id_enc'] > 0)
	{*/
		$id_enc = $_GET['id_enc'];
		$rs = new DataObj;
		$sql_all = "SELECT TotalDocComIva FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor = $rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();

		if(GenerateRef($chave, $id_enc, number_format($valor , 2, '.', '')))
		{
			echo getRef($id_enc);
		}
		else
		{
			$msg_tmp.='Não foi possivel gerar referência.';
		}
		echo $msg_tmp;
	/*}*/
?>