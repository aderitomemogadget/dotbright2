<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	require_once $path.'core_files.php';

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

	file_put_contents('log/log1_'.date('Y_m_d_h_i_s').'.txt', 'GET: '.serialize($_GET).' | POST: '.serialize($_POST).' | REQUEST: '.serialize($_REQUEST));

	/*
		const PAYMENT_GATEWAY_INTE = 'https://paymentgateway.integration-cb4x.fr/';
		const PAYMENT_GATEWAY_PROD = 'https://paymentgateway.cb4x.fr/';

		const ELIGIBILITY_INTE = 'https://eligibility.integration-cb4x.fr/';
		const ELIGIBILITY_PROD = 'https://eligibility.cb4x.fr/';

		const SERVICES_INTE = 'https://paymentservices.integration-cb4x.fr/MerchantGatewayFrontService.svc/';
		const SERVICES_PROD = 'https://paymentservices.cb4x.fr/MerchantGatewayFrontService.svc/';
	*/

	$Hmac='80A7EE2A3FE306F1BDB0B5D31E186A5AE1EE1B82';
	$MerchantID='1021';	
	// dados de integração 
	//$UserName='ATDRM';
	//$Password='ATDRM1234*';
	$UserName='8KT5ATDRM1$NjsC5mAIrSMM1-tUr9y2J';
	$Password='HNNG8Ih-EjY-mfWted5ok8TQMV9p+b_1#E5_1qquGnX';

	//pre-prod:
	$MerchantSiteID_4xg='73719';
	$PaymentOptionRef_4xg='63';
	$Commissons_4xg='3.25';
	$MIN_4xg='50';
	$MAX_4xg='2500';

	$MerchantSiteID_3xg='73718';
	$PaymentOptionRef_3xg='81';
	$Commissons_3xg='2.9';
	$MIN_3xg='50';
	$MAX_3xg='2500';


	//Until you receive your personalized test IDs, here are some generic test IDs you can use.
	/*
	$MerchantId='38';
	$Hmac='336AC9E91CE394145B177CD14807D4F199A6AC74';
	$UserName='TestUser';
	$Password='TestUser1234*';
	Minimum order amount : 100€
	Maximum amount for an order : 2000€

	UniqueClientId for a Spanish order : 40919740A
	UniqueClientId for an Italian order : RSSMRA74D22A001Q
	UniqueClientId for a Portuguese order : 999999999
	*/
	// floa integração 1 integração 0 produção
	$GLOBALS['floa_integ'] = 0;
	$enc_id = $_GET['id_enc'];
	//$enc_id = 944;
	$m_pag = metodo_pagamento($enc_id);

	if($m_pag == 25){ $MerchantSiteIDD = $MerchantSiteID_3xg;}
	if($m_pag == 24){ $MerchantSiteIDD = $MerchantSiteID_4xg;}

	// 24 4x
	// 25 3x
	//die($m_pag);


	echo '<div style="width:100%;text-align:center"><img style="width:100%;max-width:513px" src="redireccionamento.png"></div>';

function metodo_pagamento($enc_id)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $dados=new DataObj;
    $sql="SELECT Metodo_pagamento FROM encomendas WHERE ID='$enc_id'";    
    $dados->db_open($db_link, $DB_name, $sql);
    $return = '';
    while (!$dados->EOF())
    {
        $m_pag = $dados->db_data["Metodo_pagamento"];

        $dados->db_next();
    }
    $dados->db_close();
 	return $m_pag;
}

// if there is not action variable, set the default action of 'process'
if (empty($_GET['action'])) $_GET['action'] = 'process';
switch ($_GET['action']) 
{ 
	case 'process':
		// start process... https://floapay.readme.io/reference/pay-by-link
		$res = autentication_floa($UserName,$Password);
		
		if($_GET['log']==1){	
         echo " linha 103<hr><pre>";
         print_r($res);
         echo "</pre><hr>";
		}
        
		

        $res2=explode(';',$res);
		if($res2[0]==200)
		{
			$token=$res2[1];
			//$res = getPreEligibility($token,$MerchantID,$MerchantSiteID,$enc_id);
            //$res3 = getEligibility($token,$MerchantID,$MerchantSiteID_4xg,$enc_id);

            $res3 = getEligibility($token,$MerchantID,$MerchantSiteIDD,$enc_id);
			
			if($_GET['log']==1){	
			echo "linha 120<hr><pre>";
			   print_r($res3);
			echo "</pre><hr>";
			}
        


			if($res3["state"]==true)
			{
				$eligibility_token = $res3["datas"]["token"];
				$orderReference = $res3["datas"]["orderRef"];
				$orderRowsAmount = $res3["datas"]["totalAmount"];
				$orderFeesAmount = 0;
				$orderDiscountAmount = 0;
				$orderShippingCost = 0;
				//$res4 = getpaymentlink($eligibility_token,$token,$MerchantID,$MerchantSiteID_4xg,$orderReference,$orderRowsAmount,$orderFeesAmount,$orderDiscountAmount,$orderShippingCost);
				$res4 = getpaymentlink($eligibility_token,$token,$MerchantID,$MerchantSiteIDD,$orderReference,$orderRowsAmount,$orderFeesAmount,$orderDiscountAmount,$orderShippingCost);

                $paymentLinkUrl=$res4['paymentLinkUrl'];
				header("location:".$paymentLinkUrl);
			}
			else print_r($res3);
		}
	break;
	case 'success':
		// Order was successful...
		$enc_id = $_POST['item_number'];
		// $enc_id = $_SESSION['paypal_encid'];
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		
		
		$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='Pending',Estado='13' WHERE ID='".$enc_id."' AND Estado=''";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

    	$referer = $GLOBALS['site_http_url'];

    	
		echo drawRedirectPage(getLinguas('key_shop_msg1'), $referer, 3000, 1);
		
		$rs = new DataObj;
		$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'" AND Assistencia=1';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF()) 
		{
			unset($_SESSION['ss_shop_reparacoes']);
			unset($_SESSION['ss_n_enc_reparacoes']);
			$shop_reparacoes = array();
			$n_enc_reparacoes = 0;
		}
		else
		{
			unset($_SESSION['ss_shop']);
			unset($_SESSION['ss_n_enc']);
			$shop = array(); 
			$n_enc = 0;
			
			if($GLOBALS['module_voucher']==1)
			{
				unset($_SESSION['ss_voucher']);
				unset($_SESSION['ss_n_voucher']);
				$voucher = array(); 
				$n_voucher = 0;
			}
		}
		$rs->db_close();
		
		$myFile =  dirname( __FILE__ ).'/../../../files/success_'.$enc_id.'.txt';
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, print_r($_POST, true)."\n".$sql);
		fclose($fh);		

	break;
    case 'cancel':
    	$referer='';
    	
    	$enc_id = $_SESSION['paypal_encid'];
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql = "UPDATE encomendas SET Encomenda_Paga=0, Encomenda_Status='Cancel', Estado='15' WHERE ID='".$enc_id."'";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

    	if($dados = cliente_logado())
		{
    		$referer = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1&id_enc='.$enc_id;
    	}
		else
		{
			$referer = $GLOBALS['site_http_url'];
		}
	
	
		$rs = new DataObj;
		$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'" AND Assistencia=1';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF()) 
		{
			unset($_SESSION['ss_shop_reparacoes']);
			unset($_SESSION['ss_n_enc_reparacoes']);
			$shop_reparacoes = array();
			$n_enc_reparacoes = 0;
		}
		else
		{
			unset($_SESSION['ss_shop']);
			unset($_SESSION['ss_n_enc']);
			$shop = array(); 
			$n_enc = 0;
			if($GLOBALS['module_voucher']==1)
			{
				unset($_SESSION['ss_voucher']);
				unset($_SESSION['ss_n_voucher']);
				$voucher = array(); 
				$n_voucher = 0;
			}
		}
		$rs->db_close();	

		
		echo drawRedirectPage(getLinguas('key_shop_msg1_c'), $referer, 3000, 1);    	
		
	break;
	case 'ipn':
	   	
		$id_enc = $_POST['txn_id'];
		$id_enc2 = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$txn_type = $_POST['txn_type'];

		if(isset($payment_status) && $payment_status=='Completed')
		{
			$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
			$sql = "UPDATE encomendas SET Encomenda_Paga=1,Encomenda_Status='".$payment_status."',Estado='13' WHERE ID='".$id_enc2."'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		    enviarEmailCliente($id_enc2);
		}
		elseif(isset($payment_status))
		{
			$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
			$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='".$payment_status."',Estado='14' WHERE ID='" . $id_enc2 . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		
		$myFile =  dirname( __FILE__ ).'/../../../files/ipn_'.$id_enc2.'.txt';
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, print_r($_POST, true)."\n".$sql);
		fclose($fh);
		
	break;
}



function url(){
    if($GLOBALS['floa_integ']==1) {
        $url = 'https://paymentgateway.integration-cb4x.fr/v1/auth/token';
    }
    if($GLOBALS['floa_integ']==0) {
        $url = 'https://paymentgateway.cb4x.fr/';
    }


    /*
    Integration : https://paymentgateway.integration-cb4x.fr/swagger/
	Prodution : https://paymentgateway.cb4x.fr/swagger/

    Integration : https://eligibility.integration-cb4x.fr/swagger/
	Production : https://eligibility.cb4x.fr/swagger/
	*/
    return $url;
}
function url_eligibility(){

    if($GLOBALS['floa_integ']==1) {
        $url = 'https://eligibility.integration-cb4x.fr/';
    }
    if($GLOBALS['floa_integ']==0) {
        $url = 'https://eligibility.cb4x.fr/';
    }

  //  $url ='https://eligibility.integration-cb4x.fr/' ;//'https://paymentgateway.integration-cb4x.fr/v1/auth/token';
    /*
    Integration : https://paymentgateway.integration-cb4x.fr/swagger/
	Prodution : https://paymentgateway.cb4x.fr/swagger/

    Integration : https://eligibility.integration-cb4x.fr/swagger/
	Production : https://eligibility.cb4x.fr/swagger/
	*/
    return $url;
}


function autentication_floa($merchantLogin='',$merchantPassword='')
{
    $url = url()."v1/auth/token";// 'https://paymentgateway.integration-cb4x.fr/v1/auth/token';

    /*
    Integration : https://paymentgateway.integration-cb4x.fr/swagger/
	Prodution : https://paymentgateway.cb4x.fr/swagger/

    Integration : https://eligibility.integration-cb4x.fr/swagger/
	Production : https://eligibility.cb4x.fr/swagger/
	*/
	   
	$authorization = base64_encode($merchantLogin.':'.$merchantPassword);

if($_GET['log']==1){	
	 echo "linha: 316<hr><pre>";
         print_r($url);
		 echo "<hr><br>".$authorization;
         echo "</pre><hr>";
}

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'accept: application/json',
            'authorization: '.$authorization
        )
    );
    $response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if($_GET['log']==1){	
	  echo "linha: 343<hr><pre>";
	  echo "<hr>Httpcode: {$httpcode}<hr><br>";	 
      print_r($response);
      echo "</pre><hr>";
	}
	
    
   	$response = json_decode($response, true); 
    curl_close ($ch);
	
	
       
        

	$linha =  __LINE__;
    $enc_id = $_GET['id_enc'];
    esc_log($enc_id,$linha,$response);

    return $httpcode.';'.$response;
}

function getPreEligibility($token,$MerchantID,$MerchantSiteID,$enc_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $dados=new DataObj;
    $result = new DataObj;
	$rs_metodo = new DataObj;

	$reportDelayInDays=0;

	// Encomenda
	$sql="SELECT * FROM encomendas WHERE ID='$enc_id'";
	$dados->db_open($db_link, $DB_name, $sql);
	$return = '';
	if($dados->EOF())
	{
		echo false;
		die();
	}
	$id_enc = $dados->db_data["ID"];
	$CDate = $dados->db_data["Data_Criacao"];
	$id_cliente = $dados->db_data["ID_Cliente"];
	$estado = $dados->db_data["Estado"];
    $metodo_pagamento = $dados->db_data["Metodo_pagamento"];

	//Taxas,  portes e cobrança
	$port = $dados->db_data["Portes"];
	$enc_Morada = $dados->db_data["enc_Morada"];
	$enc_Morada_ent = $dados->db_data['enc_Morada_ent'];

	$diferentes = 0;
	if ($enc_Morada != $enc_Morada_ent) $diferentes = 1;
	//observações
	$text2 = $dados->db_data["Obs"];
	$id_pag = $dados->db_data["Metodo_pagamento"];
	$id_exp = $dados->db_data["Metodo_envio"];

	$return .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>';
	$return .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
		<tr>
			<td style="font-size:0;line-height:0" height="40">&nbsp;</td>
		</tr>
		<tr>
			<td style="padding: 0px 15px 3px 15px"><a href="'.SITE_HTTP_URL.'" border="0"><img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/logos/logo_email.png" border="0" width="255"></a></td>
		</tr>

		<tr>
			<td style="font-size: 0; line-height: 0;" height="20">&nbsp;</td>
		</tr>
		<tr>
			<td style="padding: 15px 15px 3px 15px; font-size: 22px; color: #000; background: #fdf5f5; font-family: Arial">Encomenda realizada com sucesso!</td>
		</tr>
		<tr>
			<td style="padding: 0px 15px 5px 15px; font-size: 13px; font-family: Arial; background: #fdf5f5; line-height: 20px;">
			<p>Obrigado pela confiança! Iremos dar início à submição da tua encomenda com o número <b>'.$id_enc.'</b> para aprovação Oney e esta já conta no histórico da tua <a href="'.SITE_HTTP_URL.'conta/encomendas" style="color:#f16522; text-decoration: none;">Conta Pessoal</a>. Temos a certeza que irás ficar satisfeito com a compra. Até breve!</p>
		</td>
		</tr>
		</table>
		
	</table>';
	echo $return;


	$TotalDocComIva = round($dados->db_data["TotalDocComIva"], 2);
	//Vamos gravar o log das acoes para usar mais tarde
	$sql="INSERT INTO floa_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$enc_id."','".date("Y-m-d H:m:i")."', '', 'A PREPARAR O ENVIO PARA ONEY','')";
	$res=Runquery($db_link, $DB_name, $sql);
	$ID_Log=db_get_last_insert_id($db_link);
	$numero_oney=$enc_id.'A'.$ID_Log;


	$item_list=array();

	$sql = 'SELECT * FROM encomendas_produtos where ID_PAI='.$id_enc.' order by ID ASC';
	$count = 0;
	$res = $result->db_open($db_link, $DB_name, $sql);
	$item_list = array();
	while (!$result->EOF())
	{
		$id_linha = $result->db_data["ID"];
		$id_pai = $result->db_data["ID_PAI"];
		//$Ref = BuscaProdutoIdByRef($result->db_data["Ref"]);
		$nome = $result->db_data["Nome"];
		$valor = $result->db_data["Valor"];
		$qtd = $result->db_data["Qtd"];
		$valor2 = number_format($valor*$qtd, 2, '.', '');//*$qtd
		$nome = limpa_special($nome);


		$item_list_aux = array(
	    	"name"=>$nome,
		    "rawAmount"=>intval($valor2*100),
		    "shipping"=>array("method" => "UPS")
	    );

	    $count+=$qtd;
	    array_push($item_list,$item_list_aux);
		$result->db_next();
	}
	$result->db_close();

	//Portes
	$item_list_aux = array(
		    "name"=>'Portes',
		    "rawAmount"=>intval($port*100),
		    "shipping"=>array("method" => "UPS")
    );
    array_push($item_list,$item_list_aux);
    $count++;//addiciona os portes

    //------
    //dados entrega
	$t_nome = $dados->db_data["enc_Nome_ent"];
	$t_apelido = $dados->db_data["enc_Apelido_ent"];
	$t_telefone = $dados->db_data["enc_Telefone_ent"];
	$t_email = $dados->db_data["enc_Email"];
	$t_morada = substr($dados->db_data["enc_Morada_ent"],0,60);
	$t_codpos1 = $dados->db_data["enc_Codigo_Postal_ent"];
	$t_cidade = $dados->db_data["enc_Cidade_ent"];
	$t_contribunite = $dados->db_data["N_Contribuinte"];
	$enc_Nif = $dados->db_data["enc_Nif"];
	$t_nome = limpa_special($t_nome);
	$t_apelido = limpa_special($t_apelido);
	$t_morada = limpa_special($t_morada);
	$Local_descarga = $dados->db_data["enc_Morada_ent"];

	//-----------------
	if($diferentes==1)
	{
		$t_morada_aux=$Local_descarga;
		$t_morada_aux2='';
		if(strlen($t_morada_aux) > 38)
		{
			$t_morada_aux = substr($Local_descarga,0,38);
			$t_morada_aux2 = substr($Local_descarga,38,76);
		}
		if($t_morada_aux2 != '')
    	{
    		$delivery_address = array(
	        "line1"=>substr($t_morada_aux,0,38),
	        "zipCode"=>substr($t_codpos1,0,8),
	        "city"=>substr($t_cidade,0,100),
	        "countryCode"=>"PT"
	    	);
    	}
    	else
    	{
			$delivery_address = array(
		        "line1"=>substr($t_morada_aux,0,38),
		        "zipCode"=>substr($t_codpos1,0,8),
		        "city"=>substr($t_cidade,0,100),
		        "countryCode"=>"PT"
		    );
    	}
    }
    else
    {
    	if($t_morada2 != '')
    	{
			$delivery_address = array(
		        "line1"=>substr($t_morada,0,38),
		        "zipCode"=>"$t_codpos1",
		        "city"=>substr($t_cidade,0,100),
		        "countryCode"=>"PT"
		    );
    	}
    	else
    	{
		    $delivery_address = array(
		        "line1"=>substr($t_morada,0,38),
		        "zipCode"=>"$t_codpos1",
		        "city"=>substr($t_cidade,0,100),
		        "countryCode"=>"PT"
		    );
    	}
    }

    if(!$t_apelido)
    {
    	$split=explode(' ',$t_nome);
    	$t_nome=$split[0];
    	$t_apelido=$split[1];
    }
    if(!$t_apelido)$t_apelido='Site ATD';


    $customer = array(
            "reference"         => $id_cliente,
            "civility"     		=> "Mr",
            "firstName"     	=> substr($t_nome,64),
            "lastName"    		=> substr($t_apelido,64),
            "email"          	=> $t_email,
            "homePhoneNumber"   => $t_telefone,
            "homeAddress" 		=> $delivery_address
        );

    $shoppingCarts = array(array(
            "reference"         => $numero_oney,
            "rawAmount"     	=> intval($TotalDocComIva*100),
            "productsCount"     => $count,
            "products"          => $item_list
        ));


    $eligibilityRequest = ["presaleFolder" => 
        [
            "shoppingCarts"     => $shoppingCarts,
            "customer"          => $customer,
            "saleChannel"       => "Desktop"
        ]
    ];

    echo '<pre>';
    echo json_encode($eligibilityRequest);



    $linha =  __LINE__;
    $enc_id = $_GET['id_enc'];
    esc_log($enc_id,$linha,$eligibilityRequest);

	//$url = 'https://eligibility.integration-cb4x.fr/';
    //$url = 'https://eligibility.integration-cb4x.fr/';

    $url = url_eligibility();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url.'api/v1/pre-eligibilities/?merchantId='.$MerchantID.'&merchantSiteIds='.$MerchantSiteID);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'authToken: '.$token,
        'Content-Type: application/json',
        'accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eligibilityRequest));
    $response       = curl_exec($ch);

	$linha =  __LINE__;
    $enc_id = $_GET['id_enc'];
    esc_log($enc_id,$linha,$response);


   // var_dump($response);

   // echo "<hr>";



    $header_size    = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body           = substr($response, $header_size);
    $httpcode       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode == 200)
    {
        $responseJson = json_decode($body);
        if (isset($responseJson->eligibilities[0]->hasAgreement) && $responseJson->eligibilities[0]->hasAgreement == true) {
            return array(
                'state' => true,
                'datas' => [
                    "token"         => $responseJson->eligibilities[0]->token,
                    "hasAgreement"  => $responseJson->eligibilities[0]->hasAgreement,
                    "totalAmount"   => $responseJson->eligibilities[0]->totalAmount,
                    "requestId"     => $responseJson->eligibilities[0]->requestId,
                    "orderRef"      => $orderRef,
                    "links"         => $responseJson->eligibilities[0]->links
                ]
            );
        } else {
            if (isset($responseJson->eligibilities[0]->errors[0])) {
                floaLogger::error("[ELIGIBILITY] ".$responseJson->eligibilities[0]->errors[0]);
                $error = $responseJson->eligibilities[0]->errors[0];
            } else {
                $error = '';
            }
            return array(
                'state'     => false, 
                'message'   => "[ELIGIBILITY] ".$error
            );
        }            
    } else {
        $message = "";
        switch($httpcode)
        {
            case 400: 
                $message = '[ELIGIBILITY] You have send a bad request on FLOA environnement.';
                break;
            case 401: 
                $message = '[ELIGIBILITY] You are not authorized to make this request on FLOA environnement.';
                break;
            case 403:
                $message = '[ELIGIBILITY] Your credentials are incorrect, access denied.';
                break;
            case 406:
                $message = '[ELIGIBILITY] Your request is not acceptable.';
                break;
            case 500:
                $message = '[ELIGIBILITY] Internal server error on FLOA environnement.';
                break;
            default:
                $message = '[ELIGIBILITY] Unknown error on FLOA environment.';
                break;
        }
        floaLogger::error($message);
        return array(
            'state'     => false, 
            'message'   => $message
        );
    }
}

function getEligibility($token,$MerchantID,$MerchantSiteID,$enc_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $dados=new DataObj;
    $result = new DataObj;
	$rs_metodo = new DataObj;

	$reportDelayInDays=0;

	// Encomenda
	$sql="SELECT * FROM encomendas WHERE ID='$enc_id'";
	$dados->db_open($db_link, $DB_name, $sql);
	$return = '';

    //print_r($dados);
	if($dados->EOF())
	{

		echo false;
		die();
	}

	$id_enc = $dados->db_data["ID"];
	$CDate = $dados->db_data["Data_Criacao"];
	$id_cliente = $dados->db_data["ID_Cliente"];


	$estado = $dados->db_data["Estado"];

	//Taxas,  portes e cobrança
	$port = $dados->db_data["Portes"];
	$enc_Morada = $dados->db_data["enc_Morada"];
	$enc_Morada_ent = $dados->db_data['enc_Morada_ent'];
    $enc_data_nascimento = $dados->db_data['enc_DataNascimento'];

    $diferentes = 0;
	if ($enc_Morada != $enc_Morada_ent) $diferentes = 1;
	//observações
	$text2 = $dados->db_data["Obs"];
	$id_pag = $dados->db_data["Metodo_pagamento"];
	$id_exp = $dados->db_data["Metodo_envio"];

	/*$return .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>';
	$return .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
		<tr>
			<td style="font-size:0;line-height:0" height="40">&nbsp;</td>
		</tr>
		<tr>
			<td style="padding: 0px 15px 3px 15px"><a href="'.SITE_HTTP_URL.'" border="0"><img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/logos/logo_email.png" border="0" width="255"></a></td>
		</tr>

		<tr>
			<td style="font-size: 0; line-height: 0;" height="20">&nbsp;</td>
		</tr>
		<tr>
			<td style="padding: 15px 15px 3px 15px; font-size: 22px; color: #000; background: #fdf5f5; font-family: Arial">Encomenda realizada com sucesso!</td>
		</tr>
		<tr>
			<td style="padding: 0px 15px 5px 15px; font-size: 13px; font-family: Arial; background: #fdf5f5; line-height: 20px;">
			<p>Obrigado pela confiança! Iremos dar início à submição da tua encomenda com o número <b>'.$id_enc.'</b> para aprovação Oney e esta já conta no histórico da tua <a href="'.SITE_HTTP_URL.'conta/encomendas" style="color:#f16522; text-decoration: none;">Conta Pessoal</a>. Temos a certeza que irás ficar satisfeito com a compra. Até breve!</p>
		</td>
		</tr>
		</table>
	</table>';
	echo $return;*/


	$TotalDocComIva = round($dados->db_data["TotalDocComIva"], 2);
	//Vamos gravar o log das acoes para usar mais tarde
	$sql="INSERT INTO floa_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$enc_id."','".date("Y-m-d H:m:i")."', '', 'A PREPARAR O ENVIO PARA ONEY','')";
	$res=Runquery($db_link, $DB_name, $sql);
	$ID_Log=db_get_last_insert_id($db_link);
	$orderRef=$enc_id.'A'.$ID_Log;


	$item_list=array();

	$sql = 'SELECT * FROM encomendas_produtos where ID_PAI='.$id_enc.' order by ID ASC';
	$count = 0;
	$res = $result->db_open($db_link, $DB_name, $sql);
	$item_list = array();
	while (!$result->EOF())
	{
		$id_linha = $result->db_data["ID"];
		$id_pai = $result->db_data["ID_PAI"];
		//$Ref = BuscaProdutoIdByRef($result->db_data["Ref"]);
		$nome = $result->db_data["Nome"];
		$valor = $result->db_data["Valor"];
		$qtd = $result->db_data["Qtd"];
		$valor2 = number_format($valor*$qtd, 2, '.', '');//*$qtd
		$nome = limpa_special($nome);

		$item_list_aux = array(
	    	"name"=>$nome,
		    "rawAmount"=>intval($valor2*100),
		    "shipping"=>array("method" => "UPS")
	    );

	    $count+=$qtd;
	    array_push($item_list,$item_list_aux);
		$result->db_next();
	}
	$result->db_close();

	//Portes
	$item_list_aux = array(
		    "name"=>'Portes',
		    "rawAmount"=>intval($port*100),
		    "shipping"=>array("method" => "UPS")
    );
    array_push($item_list,$item_list_aux);
    $count++;//addiciona os portes

    //------
    //dados entrega
	$t_nome = rem_caract_esp($dados->db_data["enc_Nome_ent"]);
    $t_apelido = rem_caract_esp($dados->db_data["enc_Apelido_ent"]);
	$t_telefone =  preg_replace("/[^0-9]/", "",  $dados->db_data["enc_Telefone_ent"]);
    if($t_telefone == ''){ $t_telefone = null; }

	$t_email = $dados->db_data["enc_Email"];
	$t_morada = substr($dados->db_data["enc_Morada_ent"],0,60);
	$t_codpos1 = $dados->db_data["enc_Codigo_Postal_ent"];
    if($t_codpos1 == ''){$t_codpos1 = null;}

	$t_cidade = $dados->db_data["enc_Cidade_ent"];
	$t_contribunite = $dados->db_data["N_Contribuinte"];
	$enc_Nif = preg_replace("/[^0-9]/", "",$dados->db_data["enc_Nif"]);

	$t_nome = rem_caract_esp($t_nome);
	$t_apelido = limpa_special($t_apelido);
	$t_morada = limpa_special($t_morada);
	$Local_descarga = $dados->db_data["enc_Morada_ent"];

	//-----------------
	if($diferentes==1)
	{
		$t_morada_aux=$Local_descarga;
		$t_morada_aux2='';
		if(strlen($t_morada_aux) > 38)
		{
			$t_morada_aux = substr($Local_descarga,0,38);
			$t_morada_aux2 = substr($Local_descarga,38,76);
		}
		if($t_morada_aux2 != '')
    	{
    		$delivery_address = array(
	        "line1"=>substr($t_morada_aux,0,38),

                "line2" => null,
                "line3" => null,
                "line4" => null,
                "zipCode"=>substr($t_codpos1,0,8),
	        "city"=>substr($t_cidade,0,100),
	        "countryCode"=>"PT"
	    	);
    	}
    	else
    	{
			$delivery_address = array(
		        "line1"=>substr($t_morada_aux,0,38),
                "line2" => null,
                "line3" => null,
                "line4" => null,
		        "zipCode"=>substr($t_codpos1,0,8),
		        "city"=>substr($t_cidade,0,100),
		        "countryCode"=> "PT"
		    );
    	}
    }
    else
    {
    	if($t_morada2 != '')
    	{
			$delivery_address = array(
		        "line1"=>substr($t_morada,0,38),
                "line2" => null,
                "line3" => null,
                "line4" => null,
		        "zipCode"=>$t_codpos1,
		        "city"=>substr($t_cidade,0,100),
		        "countryCode"=>"PT"
		    );
    	}
    	else
    	{
		    $delivery_address = array(
		        "line1"=>substr($t_morada,0,38),
                "line2" => null,
                "line3" => null,
                "line4" => null,
		        "zipCode"=> "$t_codpos1",
		        "city"=>substr($t_cidade,0,100),
		        "countryCode"=>"PT"
		    );
    	}
    }

    if(!$t_apelido)
    {
    	$split=explode(' ',$t_nome);
    	$t_nome=$split[0];
    	$t_apelido=$split[1];
    }
    if(!$t_apelido)$t_apelido='Site ATD';


    $customer = array(
            "reference"         => $id_cliente,
            "civility"     		=> "Mr",
            "firstName"     	=> substr($t_nome,64),
            "lastName"    		=> substr($t_apelido,64),
            "email"          	=> $t_email,
            "homePhoneNumber"   => $t_telefone,
            "cellPhoneNumber"   => null,
            "birthDate"			=> "{$enc_data_nascimento}T00:00:00.0000000+00:00",

            "homeAddress" 		=> $delivery_address,
            "nationalId"    	=> $enc_Nif //$enc_Nif
        );

    $shoppingCarts = array(array(
            "reference"         => $orderRef,
            "rawAmount"     	=> intval($TotalDocComIva*100),
            "productsCount"     => $count,
            "products"          => $item_list
        ));


    $eligibilityRequest = ["presaleFolder" => 
        [
            "shoppingCarts"     => $shoppingCarts,
            "customer"          => $customer,
            "reportDelayInDays" => 0,
            "saleChannel"       => "Desktop"
        ]
    ];

    //echo '<pre>';
    //echo json_encode($eligibilityRequest);
    //echo '</pre>';

	//pre-prod:
	/*
	$MerchantSiteID_4xg='73719';
	$PaymentOptionRef_4xg='63';
	$Commissons_4xg='3.25';
	$MIN_4xg='50';
	$MAX_4xg='2500';
	*/

    //echo "<hr>";
	$url = url_eligibility(); //'https://eligibility.integration-cb4x.fr/';
    //die('url'.url());
    //$url = url();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url.'api/v1/eligibilities/?merchantId='.$MerchantID.'&merchantSiteIds='.$MerchantSiteID);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'authToken: '.$token,
        'Content-Type: application/json',
        'accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eligibilityRequest));
    $response       = curl_exec($ch);
	
	if($_GET['log']==1){	
	  echo "linha: 938<hr><pre>";
	  echo "<hr>Token: {$token}<hr><br>";	 
	  echo "<hr>Httpcode: {$httpcode}<hr><br>";	 
      print_r($response);
      echo "</pre><hr>";
	}

   // $linha =  __LINE__;
  //  $enc_id = $_GET['id_enc'];
  //  esc_log($enc_id,$linha,$response);

    //var_dump($response);
    //echo "<hr>";



    $header_size    = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body           = substr($response, $header_size);
    $httpcode       = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //echo '$httpcode'.$httpcode.'<br>';
    //echo "906<hr><pre>";
    //print_r($body);
    //echo "</pre><hr>";

    if ($httpcode == 200)
    {
        $responseJson = json_decode($body);

        if (isset($responseJson->eligibilities[0]->hasAgreement) && ($responseJson->eligibilities[0]->hasAgreement == true)) {
              return array(
                'state' => true,
                'datas' => [
                    "token"         => $responseJson->eligibilities[0]->token,
                    "hasAgreement"  => $responseJson->eligibilities[0]->hasAgreement,
                    "totalAmount"   => $responseJson->eligibilities[0]->totalAmount,
                    "requestId"     => $responseJson->eligibilities[0]->requestId,
                    "orderRef"      => $orderRef,
                    "links"         => $responseJson->eligibilities[0]->links
                ]
            );


        } else {
            if (isset($responseJson->eligibilities[0]->errors[0])) {
                $error = $responseJson->eligibilities[0]->errors[0];
            } else {
                $error = '';
            }
            return array(
                'state'     => false, 
                'message'   => "[ELIGIBILITY] ".$error
            );
        }

    } else {
        $message = "";
        switch($httpcode)
        {
            case 400: 
                $message = '[ELIGIBILITY] You have send a bad request on FLOA environnement.';
                break;
            case 401: 
                $message = '[ELIGIBILITY] You are not authorized to make this request on FLOA environnement.';
                break;
            case 403:
                $message = '[ELIGIBILITY] Your credentials are incorrect, access denied.';
                break;
            case 406:
                $message = '[ELIGIBILITY] Your request is not acceptable.';
                break;
            case 500:
                $message = '[ELIGIBILITY] Internal server error on FLOA environnement.';
                break;
            default:
                $message = '[ELIGIBILITY] Unknown error on FLOA environment.';
                break;
        }
        floaLogger::error($message);
        return array(
            'state'     => false, 
            'message'   => $message
        );
    }


}

function getpaymentlink($eligibility_token,$token,$MerchantID,$MerchantSiteID,$orderReference,$orderRowsAmount,$orderFeesAmount,$orderDiscountAmount,$orderShippingCost)
{
    $dados = array(
            "orderReference"         => $orderReference,
            "invoiceId"     		=> "0",
          /*  "paymentUrls"     	=> array(
            		"merchantBackUrl"     			=> "https://atd-rm.pt/external_api/pagamentos/pgm_floa/floa.php",
            		"merchantNotifyUrl"     		=> "https://atd-rm.pt/external_api/pagamentos/pgm_floa/floa.php",
            		"merchantReturnUrl"     		=> "https://atd-rm.pt/external_api/pagamentos/pgm_floa/floa.php",
            	), */
        "paymentUrls"     	=> array(
            		"merchantBackUrl"     			=>  $GLOBALS['site_http_url']."p27-carrinho-finalizar-pt",
            		"merchantNotifyUrl"     		=>  $GLOBALS['site_http_url']."p27-carrinho-finalizar-pt",
            		"merchantReturnUrl"     		=> "https://www.atd-rm.pt/p5-registo-utilizador-pt?show_compras=1", //"https://atd-rm.pt/external_api/pagamentos/pgm_floa/floa.php",
            	),
            "culture"    			=> "pt-pt",
            "freeText"          	=> "free",
            "orderRowsAmount"   	=> $orderRowsAmount,
            "orderFeesAmount" 		=> $orderFeesAmount,
            "orderDiscountAmount"   => $orderDiscountAmount,
            "orderShippingCost"   => $orderShippingCost
        );

    $linha =  __LINE__;
    $enc_id = $_GET['id_enc'];
    esc_log($enc_id,$linha,$dados);

     	

    //echo '<pre>';
    //echo json_encode($eligibilityRequest);
	//$url = 'https://eligibility.integration-cb4x.fr/';
    $url = url_eligibility();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url.'api/v1/eligibilities/'.$eligibility_token.'/payment-links/send?merchantId='.$MerchantID.'&merchantSiteId='.$MerchantSiteID.'&sendingTypes=Web');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'authToken: '.$token,
        'Content-Type: application/json',
        'accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    $response = curl_exec($ch);


  //  $linha =  __LINE__;
  //  $enc_id = $_GET['id_enc'];
  //  esc_log($enc_id,$linha,$dados);

    return (json_decode($response,true));
}


function rem_caract_esp($string) {

    // matriz de entrada
    $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç',' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );

    // matriz de saída
    $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C',' ',' ','','','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_' );

    // devolver a string
    return str_replace($what, $by, $string);
}

function esc_log($enc_id,$linha,$dados){

//die($dados);


	$mylog_files =  dirname( __FILE__ ).'/log/'.$linha.'_sol_'.$enc_id.'.txt';
		$flog = fopen($mylog_files, 'w') or die("can't open file");
		fwrite($flog, print_r($_POST, true)."\n".print_r($dados, TRUE));
		fclose($flog);	

}
