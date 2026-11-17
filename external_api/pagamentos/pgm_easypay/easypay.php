<?php

	//------------------------- recebe por parametro o metodo via SSH ---------
	if(isset($argv))$metodo=$argv[1];
	else 
	{
		file_put_contents(dirname(__FILE__) .'/logs/ipn0_'.date('Y_m_d_h_i_s').'.txt',serialize($_GET).serialize($_POST));
	}
	//-------------------------------------------------------------------------

	$path = dirname(__FILE__) . '/../../../core/';
	include $path . 'core_files.php';
	$AccountId=$GLOBALS['pgm_metodos'][MP_TP_CC_EASYPAY]['user'];
	$ApiKey=$GLOBALS['pgm_metodos'][MP_TP_CC_EASYPAY]['key'];


	//------------------------- recebe por parametro o metodo via GET ---------
	if(isset($_GET['id_enc']))$ID_ENC=intval($_GET['id_enc']);
	if(isset($_GET['metodo']))$metodo=$_GET['metodo'];

	if($ID_ENC > 0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;
		$sql='SELECT * FROM encomendas WHERE ID='.$ID_ENC;
		$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		if (!$rs->EOF())
		{
			$VALOR=$rs->db_data['ID'];
			$N_Contribuinte=$rs->db_data["enc_Nif"];
			$Contacto=$rs->db_data["enc_Nome"].' '.$rs->db_data["enc_Apelido"];
			$Contacto2=$rs->db_data["enc_Nome_ent"].' '.$rs->db_data["enc_Apelido_ent"];
			$Morada=$rs->db_data["enc_Morada"];
			$Localidade=$rs->db_data["enc_Cidade"];
			$Cod_Pos=$rs->db_data["enc_Codigo_Postal"];
			$MoradaEntrega=$rs->db_data["enc_Morada_ent"];
			$LocalidadeEntrega=$rs->db_data["enc_Cidade_ent"];
			$Cod_PosEntrega=$rs->db_data["enc_Codigo_Postal_ent"];
			$telefone=$rs->db_data["enc_Telefone"];
			$telefoneEntrega=($rs->db_data["enc_Telefone_ent"] == '' ? $rs->db_data["enc_Telefone"] : $rs->db_data["enc_Telefone_ent"]);
			$email=$rs->db_data["enc_Email"];
			$Portes=$rs->db_data["Portes"];
			$Custos_logisticos=$rs->db_data["Custos_logisticos"];
			$Taxas=$rs->db_data["Taxas"];
			$ValorDesconto=$rs->db_data["ValorDesconto"];
			$TotalDocComIva=$rs->db_data["TotalDocComIva"];
			$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
			$TotalIva=$rs->db_data["TotalIva"];
			$Data_Criacao=$rs->db_data["Data_Criacao"];
			$entidadeatm=$rs->db_data["Ent_atm"];
			$referenciaatm=$rs->db_data["Ref_atm"];
			$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
			$Metodo_expedicao=$rs->db_data["Metodo_envio"];
			$enc_pagamento_dados=$rs->db_data["enc_pagamento_dados"];
			$IDGuia=$rs->db_data["IDGuia"];
			$Obs=$rs->db_data["Obs"];
			$Pais=$rs->db_data["enc_Pais"];
			$PaisEntrega=$rs->db_data["enc_Pais_ent"];				
		}
		$rs->db_close();
	}
	

	//Multibanco Reference
	if($metodo=='mb')
	{
		$ref = gerar_ref_MB($AccountId,$ApiKey,$ID_ENC,$TotalDocComIva,$Contacto,$email,$telefone);
		$sql="INSERT INTO pagamentos_easypay (Tipo,ID_Encomenda,CDate,checkoutId,Resposta1) values ('2','$ID_ENC','".date('Y-m-d H:i:s')."','".$res['id']."','".serialize($res)."')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		file_put_contents(dirname(__FILE__) .'/logs/mb_'.date('Y_m_d_h_i_s').'.txt',serialize($res));


		//print_r($ref);
		//"{"status":"ok","message":["Your request was successfully created"],"id":"2df421e3-c082-4c42-830f-0aec04988f74","method":{"type":"mb","status":"pending","entity":"11683","reference":"611006008","expiration_date":"05/26"},"customer":{"id":"610862b6-8a5e-49e7-8b30-1bd771a0c6f4"},"capture":{"id":"dafaf5f6-0c0b-46d3-b88f-5bcda6e2d624"}}"

		$sql = "UPDATE encomendas SET Ent_atm='".$ref['method']['entity']."',Ref_atm='".$ref['method']['reference']."' WHERE ID='" . $ID_ENC . "'";
		//$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

		$tabelaMulti = '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc;margin:0 auto" align="center">';
		$tabelaMulti .= "<tr>";
		$tabelaMulti .=	'<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by EASYPAY)</center></td>';
		$tabelaMulti .= "</tr>";
		$tabelaMulti .= "<tr>";
		$tabelaMulti .=	'<td rowspan="4"><div align="center"><img src="https://www.dotbright.pt/external_api/pagamentos/pgm_easypay/easypay_logo_nobrands-01.png" alt=""/></div></td>';
		$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>';
		$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.$ref['method']['entity'].'</td>';
		$tabelaMulti .= "</tr>";
		$tabelaMulti .= "<tr>";
		$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
		$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.$ref['method']['reference'].'</td>';
		$tabelaMulti .= "</tr>";
		$tabelaMulti .= "<tr>";
		$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
		$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.numberformat($TotalDocComIva).'</td>';
		$tabelaMulti .= "</tr>";
		$tabelaMulti .= "<tr>";
		$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
		$tabelaMulti .= '<td style="font-size: x-small; text-align:left">&nbsp;</td>';
		$tabelaMulti .= "</tr>";
		$tabelaMulti .= "<tr>";
		$tabelaMulti .=	'<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>';
		$tabelaMulti .= "</tr>";
		$tabelaMulti .= "</table>";

		echo $tabelaMulti;


	}
	if($metodo=='mbw')
	{
		if(isset($_GET['mbway_tel']))$telefone=$_GET['mbway_tel'];
		if($telefone=='914931936')$TotalDocComIva=0.5;
		$res = gerar_pagamento_MBW($AccountId,$ApiKey,$ID_ENC,$TotalDocComIva,$Contacto,$email,$telefone);
		file_put_contents(dirname(__FILE__) .'/logs/mbw_'.date('Y_m_d_h_i_s').'.txt',serialize($res));

		$sql="INSERT INTO pagamentos_easypay (Tipo,ID_Encomenda,mbway_tel,CDate,checkoutId,Resposta1) values ('3','$ID_ENC','$telefone','".date('Y-m-d H:i:s')."','".$res['id']."','".serialize($res)."')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

		//print_r($res);
		//$sql = "UPDATE encomendas SET Ent_atm='".$ref['method']['entity']."',Ref_atm='".$ref['method']['reference']."' WHERE ID='" . $ID_ENC . "'";
		//$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

	}
	if($metodo=='cc')
	{
		$res = gerar_pagamento_CC($AccountId,$ApiKey,$ID_ENC,$TotalDocComIva,$Contacto,$email,$telefone);
		$sql="INSERT INTO pagamentos_easypay (Tipo,ID_Encomenda,CDate,checkoutId,Resposta1) values ('3','$ID_ENC','".date('Y-m-d H:i:s')."','".$res['id']."','".serialize($res)."')";
		Runquery($db_link, $GLOBALS['DB_name'], $sql);
		file_put_contents(dirname(__FILE__) .'/logs/cc_'.date('Y_m_d_h_i_s').'.txt',serialize($res));
		$url=$res['method']['url'];

		//$sql = "UPDATE encomendas SET Ent_atm='".$ref['method']['entity']."',Ref_atm='".$ref['method']['reference']."' WHERE ID='" . $ID_ENC . "'";
		//$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		/*?>
		<div runat="server" id="ep_box" style="margin:0 auto; text-align:center; border: 1px solid #ddd; border-radius: 5px; width: 210px; min-height: 70px; padding:10px;" >
			<div style="margin-top: 10px; width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee" >
					<div style="margin-top: 20px;padding: 5px; padding-top: 10px; clear: both;" >
						<span style="color: #0088cc; text-decoration: none;">Aguarde, está a ser redireccionado</span>
					</div>
			</div>
		</div><?*/
		header("location: ".$url);
	}
	//Devolve o estado de um pagamento
	if($metodo=='status')
	{
		$Estado=-1;

		$sql="SELECT * from pagamentos_easypay where ID_Encomenda=$ID_ENC order By ID Desc";
		$rs = new DataObj;		
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF())
		{
			$IDb= $rs->db_data['ID'];
			$Data= $rs->db_data['CDate'];
			$Estado= $rs->db_data['Estado'];
			/*
			$responseData=$Resposta1=$rs->db_data['Resposta1'];
			$dados = json_decode($Resposta1, true);
			$checkoutId = $dados["checkoutId"];
			if($Estado==0 && $checkoutId != '')
			{
				//Vamos obter o estado do pagamento
				if(substr_count($responseData,'user authorization failed')>0)
				{
					$Estado=2;
					$sql="UPDATE pagamentos_easypay set Estado=2 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'Cancelled by user')>0)
				{
					$Estado=3;
					$sql="UPDATE pagamentos_easypay set Estado=3 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'transaction declined')>0)
				{
					$Estado=4;
					$sql="UPDATE pagamentos_easypay set Estado=4 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'Request successfully')>0)
				{
					$Estado=13;
					$sql="UPDATE pagamentos_easypay set Estado=13 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					//enviarEmailCliente($ID_Encomenda,'', 13);
					//o email é enviado pelo webhook
				}
			}
			//E se ja passaram mais de 4 minutos?
			$from_time = strtotime($Data);
			$to_time = strtotime(date("Y-m-d H:m:i"));

			if(round(abs($to_time - $from_time) / 60,2)*100>4)
			{
				$Estado=5;
				$sql="UPDATE pagamentos_easypay set Estado=5 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			*/
		}
		$rs->db_close();


		echo $Estado;

	}
	if($metodo=='cron')
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="SELECT * from pagamentos_easypay where Estado=0";
		$rs = new DataObj;		
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		while(!$rs->EOF())
		{
			$IDb= $rs->db_data['ID'];
			$ID_Encomenda= $rs->db_data['ID_Encomenda'];
			$Data= $rs->db_data['CDate'];
			$Estado= $rs->db_data['Estado'];
			$checkoutId= $rs->db_data['checkoutId'];
			$responseData=$Resposta1=$rs->db_data['Resposta1'];
			$dados = json_decode(unserialize($Resposta1), true);
			//echo $checkoutId = $dados["customer"]["id"];
			$res = get_pagamento_Status($AccountId,$ApiKey,$checkoutId);
			if($res['payment_status']=='failed')
			{
				$Estado=2;
				$sql="UPDATE pagamentos_easypay set Estado=2 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if($res['payment_status']=='paid')
			{
				$Estado=13;
				$sql="UPDATE pagamentos_easypay set Estado=13 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				enviarEmailCliente($ID_Encomenda,'', 13);
				//o email é enviado pelo webhook
			}
			if($res['payment_status']=='timeout')
			{
				$Estado=5;
				$sql="UPDATE pagamentos_easypay set Estado=5 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			print_r($res);
			//die();

			echo "<br><br>";
			/*
			if($Estado==0 && $checkoutId != '')
			{
				//Vamos obter o estado do pagamento
				if(substr_count($responseData,'user authorization failed')>0)
				{
					$Estado=2;
					$sql="UPDATE pagamentos_easypay set Estado=2 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'Cancelled by user')>0)
				{
					$Estado=3;
					$sql="UPDATE pagamentos_easypay set Estado=3 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'transaction declined')>0)
				{
					$Estado=4;
					$sql="UPDATE pagamentos_easypay set Estado=4 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				if(substr_count($responseData,'Request successfully')>0)
				{
					$Estado=13;
					$sql="UPDATE pagamentos_easypay set Estado=13 where ID=$IDb";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
					Runquery($db_link, $GLOBALS['DB_name'], $sql);
					//enviarEmailCliente($ID_Encomenda,'', 13);
					//o email é enviado pelo webhook
				}
			}
			//E se ja passaram mais de 4 minutos?
			$from_time = strtotime($Data);
			$to_time = strtotime(date("Y-m-d H:m:i"));

			if(round(abs($to_time - $from_time) / 60,2)*100>4)
			{
				$Estado=5;
				$sql="UPDATE pagamentos_easypay set Estado=5 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}*/
			$rs->db_next();
		}
		$rs->db_close();

	}

	function gerar_pagamento_CC($AccountId,$ApiKey,$ID_ENC=0,$VALOR=0,$c_nome='',$c_email='',$c_telefone='')
	{
		$url = 'https://api.prod.easypay.pt/2.0';

		$c_telefone=str_replace('+351','',$c_telefone);
		$c_telefone=str_replace('351','',$c_telefone);
		$c_telefone=trim($c_telefone);

		$customer=Array(
			"name" => $c_nome,
		    "email" => $c_email,
		    "phone" => $c_telefone,
		    "phone_indicative" => "+351",
		    "key" => "Encomenda º $ID_ENC"
		);

		$postValues=Array(
			"customer" => $customer,
			"type" => "sale", /* sale ou authorisation */
		    "key" => "Encomenda º $ID_ENC",
			"currency" => "EUR",
			"expiration_time" => date("Y-m-d H:i",strtotime("+3 days")), /* The last possible time to make the payment. Applicable in Multibanco payments. */
			"capture" => Array("descriptive" => "Encomenda $ID_ENC"), /* Will be displayed in the bank statement or in the MB WAY application.*/
			"value" => floatval($VALOR), /* >= 0.5 Value will be rounded to 2 decimals */
			"method" => "CC" /* "MB" "MBW" "DD" "CC" "SC" "UF" "VI" */
		);
	    $json_string=json_encode($postValues);
	    $headers[] = 'AccountId:'.$AccountId;
	    $headers[] = 'ApiKey:'.$ApiKey;
		$headers[] = 'Content-Type:application/json';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'/single');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$response = curl_exec($ch);
		$res = json_decode($response,true);

		if($res['status']=='ok')
		{
			$id = $res['id'];
			$customer_id = $res['customer']['id'];
			$capture_id = $res['capture']['id'];

		}
		return $res;
	}
	function gerar_pagamento_MBW($AccountId,$ApiKey,$ID_ENC=0,$VALOR=0,$c_nome='',$c_email='',$c_telefone='')
	{
		$url = 'https://api.prod.easypay.pt/2.0';

		$c_telefone=str_replace('+351','',$c_telefone);
		$c_telefone=str_replace('351','',$c_telefone);
		$c_telefone=trim($c_telefone);

		$customer=Array(
			"name" => $c_nome,
		    "email" => $c_email,
		    "phone" => $c_telefone,
		    "phone_indicative" => "+351",
		    "key" => "Encomenda º $ID_ENC"
		);

		$postValues=Array(
			"customer" => $customer,
			"type" => "sale", /* sale ou authorisation */
		    "key" => "Encomenda º $ID_ENC",
			"currency" => "EUR",
			"expiration_time" => date("Y-m-d H:i",strtotime("+3 days")), /* The last possible time to make the payment. Applicable in Multibanco payments. */
			"capture" => Array("descriptive" => "Encomenda $ID_ENC"), /* Will be displayed in the bank statement or in the MB WAY application.*/
			"value" => floatval($VALOR), /* >= 0.5 Value will be rounded to 2 decimals */
			"method" => "MBW" /* "MB" "MBW" "DD" "CC" "SC" "UF" "VI" */
		);
	    $json_string=json_encode($postValues);
	    $headers[] = 'AccountId:'.$AccountId;
	    $headers[] = 'ApiKey:'.$ApiKey;
		$headers[] = 'Content-Type:application/json';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'/single');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$response = curl_exec($ch);
		$res = json_decode($response,true);

		if($res['status']=='ok')
		{
			$id = $res['id'];
			$customer_id = $res['customer']['id'];
			$capture_id = $res['capture']['id'];

		}
		return $res;
	}
	function gerar_ref_MB($AccountId,$ApiKey,$ID_ENC=0,$VALOR=0,$c_nome='',$c_email='',$c_telefone='')
	{
		$url = 'https://api.prod.easypay.pt/2.0';

		$customer=Array(
			"name" => $c_nome,
		    "email" => $c_email,
		    "phone" => $c_telefone,
		    "phone_indicative" => "+351",
		    "key" => "Encomenda º $ID_ENC"
		);

		$postValues=Array(
			"customer" => $customer,
			"type" => "sale", /* sale ou authorisation */
		    "key" => "Encomenda º $ID_ENC",
			"currency" => "EUR",
			"expiration_time" => date("Y-m-d H:i",strtotime("+3 days")), /* The last possible time to make the payment. Applicable in Multibanco payments. */
			"capture" => Array("descriptive" => "Encomenda $ID_ENC"), /* Will be displayed in the bank statement or in the MB WAY application.*/
			"value" => floatval($VALOR), /* >= 0.5 Value will be rounded to 2 decimals */
			"method" => "MB" /* "MB" "MBW" "DD" "CC" "SC" "UF" "VI" */
		);
	    $json_string=json_encode($postValues);
	    $headers[] = 'AccountId:'.$AccountId;
	    $headers[] = 'ApiKey:'.$ApiKey;
		$headers[] = 'Content-Type:application/json';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'/single');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$response = curl_exec($ch);
		$res = json_decode($response,true);
		return $res;
	}
	function get_pagamento_Status($AccountId,$ApiKey,$checkoutId='')
	{
		$url = 'https://api.prod.easypay.pt/2.0/single/'.$checkoutId;

	    $json_string=json_encode($postValues);
	    $headers[] = 'AccountId:'.$AccountId;
	    $headers[] = 'ApiKey:'.$ApiKey;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$response = curl_exec($ch);
		$res = json_decode($response,true);
		return $res;
	}
?>