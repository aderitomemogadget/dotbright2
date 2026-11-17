<?php
	$path =  dirname( __FILE__ ) . '/../../../core/';
	include $path.'core_files.php';

?>
<html>
<head><title>::PHP Oney::</title></head>
<body>
<center><font face="Verdana,Arial" size="2" color="333333">Processing Transaction . . . </font></center>
<?php
$n_encomenda = $_POST['idEncomenda'];

if($n_encomenda)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $dados=new DataObj;
    $result = new DataObj;
	$rs_metodo = new DataObj;



    /*$sql = "SELECT * FROM metodos_pagamento WHERE Tipo=21";
	$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$Sandbox = 0;
	$conta_ifthen = '';
	if (!$rs_metodo->EOF())
	{
		$metodo_sandbox = $rs_metodo->db_data["metodo_sandbox"];
		$sandbox = $rs_metodo->db_data["Sandbox"];
		$chave = $rs_metodo->db_data["chavebo"];
		$entidade = $rs_metodo->db_data["entidade"];
		$subentidade = $rs_metodo->db_data["subentidade"];
	}
	$rs_metodo->db_close();*/




    // Encomenda
	$sql="SELECT * FROM encomendas WHERE ID='$n_encomenda'";
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

	//Taxas,  portes e cobrança
	$port = $dados->db_data["Portes"];
	$enc_Morada = $dados->db_data["enc_Morada"];
	$enc_Morada_ent = $dados->db_data['enc_Morada_ent'];

	$diferentes = 0;
	if ($enc_Morada != $enc_Morada_ent) $diferentes = 1;
	//observações
	$text2 = $dados->db_data["Obs"];


	//---------------------para pagamentos---------------------------

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
	$sql="INSERT INTO oney_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$n_encomenda."','".date("Y-m-d H:m:i")."', '', 'A PREPARAR O ENVIO PARA ONEY','')";
	$res=Runquery($db_link, $DB_name, $sql);
	$ID_Log=db_get_last_insert_id($db_link);
	$numero_oney=$n_encomenda.'A'.$ID_Log;


	$LANGUAGE_CODE = "PT";
    $MERCHANT_GUID = "9817f122-f441-487e-b575-d9ebd0b8e79c";
    $PSP_GUID = "be313d13-b7a6-4e2c-b703-0ddc8a005122";

    $API_KEY_PAYMENT = "a67818c911164fdd9794ceabdf2c594b";
   	$API_KEY_MARKETING = "03bcea2501f3425fabf7d362bc3abefc";

	$SANDBOX_GATEWAY_URL = "https://api-staging.oney.io/staging";
    $PRODUCTION_GATEWAY_URL = "https://api.oney.io/";

	$metodo_sandbox=1;


    if($metodo_sandbox)$GATEWAY_URL = $SANDBOX_GATEWAY_URL;
	else $GATEWAY_URL = $PRODUCTION_GATEWAY_URL;

    $FacilyPayURL = "/payments/v1/purchase/facilypay_url";
    $Oneycardpayment = "/payments/v1/purchase/card_payment_url";

    $Oney_confirm_url		 ="/payments/v1/purchase/psp_guid/{psp_guid}/merchant_guid/{merchant_guid}/reference/{reference}/action/confirm";
	$Oney_cancel_url 		 ="/payments/v1/purchase/psp_guid/{psp_guid}/merchant_guid/{merchant_guid}/reference/{reference}/action/cancel";
	$Oney_get_payment_status ="/payments/v1/purchase/psp_guid/{psp_guid}/merchant_guid/{merchant_guid}/reference/{reference}?language_code={language_code}";

	/*all URL must be encoded. Encoding must be realized on all the url except “http//” or “https://” value. So, if there is a pipe “|” in the url, the encoded url will contain “%7C” instead of the “|”.*/


	$sql = 'SELECT * FROM encomendas_produtos where ID_ORG='.$id_enc.' order by ID ASC';
	$res = $rs->db_open($db_link, $DB_name, $sql);
	$item_list = array();
	$valor_maior=0;
	$main_item_linha=0;
	while (!$rs->EOF())
	{
		$id_linha = $rs->db_data["ID"];
		$valor = $rs->db_data["Valor"];
		if($valor>$valor_maior)
		{
			$valor_maior=$valor;
			$main_item_linha=$id_linha;
		}
		$rs->db_next();
	}
	$rs->db_close();



	$sql = 'SELECT * FROM encomendas_produtos where ID_ORG='.$id_enc.' order by ID ASC';
	$count = 0;
	$res = $result->db_open($db_link, $DB_name, $sql);
	$item_list = array();
	while (!$result->EOF())
	{
		$id_linha = $result->db_data["ID"];
		$id_pai = $result->db_data["ID_PAI"];
		$Ref = BuscaProdutoIdByRef($result->db_data["Ref"]);
		$nome = $result->db_data["Nome"];
		$valor = $result->db_data["Valor"];
		$qtd = $result->db_data["Qtd"];
		$valor2 = number_format($valor, 2, '.', '');//*$qtd
		$nome = limpa_special($nome);

		$is_main_item=0;
		if($id_linha==$main_item_linha)
		{
			$is_main_item=1;
		}

		$item_list_aux = array(
	    	"is_main_item"=>$is_main_item, /* 1: if it is the main product (the most expensive) 0: otherwise. item_list MUST contain at least one main item */
		    "category_code"=>"8", /* 1: Food & drinks 2: Auto & motorcycle 3: Culture & entertainment 4: Home & garden 5: Home appliances 6: Bidding and multi purchasing 7: Flowers & gifts 8: Computers & software 9: Health & beauty 10: Personal services 11: Professional services 12: Sport 13: Clothing & accessories 14: Travel & tourism 15: Hifi, photo & video 16: Telephone & communication */
		    "label"=>$nome,
		    "item_external_code"=>$Ref,
		    "quantity"=>$qtd,
		    "price"=>$valor2, /* Price with decimal 15,2 */
		    "marketplace_merchant_flag"=>0, /* 1: if the item comes from a marketplace */
		    "marketplace_merchant_name"=>null /* Mandatory if marketplace_merchant_flag value is 1 */
	    );
	    array_push($item_list,$item_list_aux);
	    //$item_list=$item_list_aux;
		$count+=$qtd;
		$result->db_next();
	}
	$result->db_close();

	//Portes
	$item_list_aux = array(
	    	"is_main_item"=>0, /* 1: if it is the main product (the most expensive) 0: otherwise. item_list MUST contain at least one main item */
		    "category_code"=>"11", /* 1: Food & drinks 2: Auto & motorcycle 3: Culture & entertainment 4: Home & garden 5: Home appliances 6: Bidding and multi purchasing 7: Flowers & gifts 8: Computers & software 9: Health & beauty 10: Personal services 11: Professional services 12: Sport 13: Clothing & accessories 14: Travel & tourism 15: Hifi, photo & video 16: Telephone & communication */
		    "label"=>'Delivery cost',
		    "item_external_code"=>'Delivery cost',
		    "quantity"=>1,
		    "price"=>$port
    );
    array_push($item_list,$item_list_aux);
    $count++;//addiciona os portes

	//dados entrega
	$t_nome = $dados->db_data["enc_Nome_ent"];
	$t_apelido = $dados->db_data["enc_Apelido_ent"];
	$t_telefone = $dados->db_data["enc_Telefone_ent"];
	$t_email = $dados->db_data["enc_Email"];
	$t_morada = substr($dados->db_data["enc_Morada_ent"],0,60);
	$t_codpos1 = $dados->db_data["enc_Codigo_Postal_ent"];
	$t_cidade = $dados->db_data["enc_Cidade_ent"];
	$t_contribunite = $dados->db_data["N_Contribuinte"];
	$t_nome = limpa_special($t_nome);
	$t_apelido = limpa_special($t_apelido);
	$t_morada = limpa_special($t_morada);


	$Local_descarga = $dados->db_data["enc_Morada_ent"];

	if(!$t_telefone)
	{
		die("Erro, não é possivel prosseguir, falta o numero de telefone no seu perfil");
	}
	else
	{
		$t_telefone=str_replace(' ','',$t_telefone);
		$t_telefone=substr($t_telefone,-9);
		$t_telefone='+351'.$t_telefone;
	}
	if(strlen($t_morada) > 38)
	{
		$t_morada = substr($rs2->aFields["Morada"],0,38);
		$t_morada2 = substr($rs2->aFields["Morada"],38,76);
	}
	/*$t_codpos1 = substr($t_codpos1,0,4);
	$t_codpos2 = substr($t_codpos2,0,3);*/

	//nacex
    if($id_exp==3)
    {
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
		        "line1"=>substr($t_morada_aux,0,38), /* Street number, Street type and Street name */
		        "line2"=>substr($t_morada_aux2,0,38), /* Street number, Street type and Street name */
		        "postal_code"=>substr($t_codpos1,0,8),
		        "municipality"=>substr($t_cidade,0,100), /* Max Len = 32 for France */
		        "country_code"=>"PRT",
		        "country_label"=>"Portugal"
		    	);
	    	}
	    	else
	    	{
				$delivery_address = array(
			        "line1"=>substr($t_morada_aux,0,38), /* Street number, Street type and Street name */
			        "postal_code"=>substr($t_codpos1,0,8),
			        "municipality"=>substr($t_cidade,0,100), /* Max Len = 32 for France */
			        "country_code"=>"PRT",
			        "country_label"=>"Portugal"
			    );
	    	}


	    }
	    else
	    {
	    	if($t_morada2 != '')
	    	{
				$delivery_address = array(
			        "line1"=>substr($t_morada,0,38), /* Street number, Street type and Street name */
			        "line2"=>substr($t_morada2,0,38), /* Street number, Street type and Street name */
			        "postal_code"=>"$t_codpos1",
			        "municipality"=>substr($t_localidade,0,100), /* Max Len = 32 for France */
			        "country_code"=>"PRT",
			        "country_label"=>"Portugal"
			    );
	    	}
	    	else
	    	{
			    $delivery_address = array(
			        "line1"=>substr($t_morada,0,38), /* Street number, Street type and Street name */
			        "postal_code"=>"$t_codpos1",
			        "municipality"=>substr($t_localidade,0,100), /* Max Len = 32 for France */
			        "country_code"=>"PRT",
			        "country_label"=>"Portugal"
			    );
	    	}

	    }
    }
    else //Levantamento na loja
    {
    	$delivery_address = array(
	        "line1"=>'Rua Fonte Cova Nº 30', /* Street number, Street type and Street name */
	        "postal_code"=>"4650-072",
	        "municipality"=>'Airaes Felgueiras', /* Max Len = 32 for France */
	        "country_code"=>"PRT",
	        "country_label"=>"Portugal"
	    );
    }


    if($t_apelido != '')
    {
		$recipient = array(
	        /*'recipient_honorific_code'    =>  "3", *//* opcional,  1: Mr 2: Madam 3: Miss */
	        'surname' => substr($t_apelido,0,32),
	        'first_name'    => substr($t_nome,0,32),
	        'phone_number'    =>  substr($t_telefone,0,15) /* Prefixed with international area code. Example: (+33)|0033|+33|33|+33(0)|0XXXXXXXXX */
	    );
    }
    else
    {
    	$recipient = array(
	        'first_name'    => substr($t_nome,0,32),
	        'phone_number'    =>  substr($t_telefone,0,15) /* Prefixed with international area code. Example: (+33)|0033|+33|33|+33(0)|0XXXXXXXXX */
	    );
	}


	//nacex
    if($id_exp==3){

    	if($diferentes==1) $address_type=5;
    	else $address_type=4;

    	$delivery_mode_code=4;
    }
    else {$address_type=1;$delivery_mode_code=1;}

	$delivery = array(
        'delivery_date'    =>  date('Y-m-d',strtotime("+3 days")), /* Expected delivery date, Format : yyyy-MM-dd */
        'delivery_mode_code' => $delivery_mode_code, /* Code of the delivery mode, 1 : Collection of the goods in the merchant store 2 : Collection in a third party point (like ups, alveol, etc.) 3 : Collection in an airport, train station or travel agency 4 : Carrier (La Poste, Colissimo, UPS, DHL...or any private carrier) 5 : Electronic ticket issuance, download, etc. */
        'delivery_option'    => "2", /* Delivery delay 1 : Express (< 24 hours) 2 : Standard 3 : Priority */
        /*'priority_delivery_code'    =>  "1",*/ /* Mandatory if delivery_option value is 3 (Priority) 1 : Less than or equal to 1 hour 2 : Greater than 1 hour 3 : Immediate 4 : 24/24 7/7 */
        'address_type'    =>  $address_type, /* Delivery address type 1 : Merchant 2 : Third party relay point 3 : Airport, train station, travel agency 4 : Billing address 5 : Delivery address 6 : Electronic way (ticket, download) */
        'recipient'    =>  $recipient, /* Mandatory if adress_type = 5 */
        'delivery_address'    =>  $delivery_address
    );

	$purchase = array(
        'external_reference_type'    =>  "CMDE", /* Allow to qualify the type of external_reference, Mandatory value: CMDE */
        'external_reference' => $numero_oney, /* Unique identifier of the merchant on the purchase, Number to identify a customer's order unit (ex: '1371075') without space */
        'purchase_amount'    =>  $TotalDocComIva, /* Total price of the purchase, Total price of the basket including any payment already made with other means of payment (gift cards, having, etc.)The price is decimal (Len = 18 max) 15,2 */
        'currency_code'    =>  "EUR", /* Currency code */
        'delivery'    =>  $delivery, /* Delivery data */
        'number_of_items'    =>  $count, /* Number of items in the basket */
        'item_list'    =>  ($item_list)
        /*'item_list'    =>  array($item_list)*/
    );



	if($t_apelido != '')
    {
		$identity = array(
	        "person_type"=>"2", /* 1 : Company 2 : Individuals */
	        "honorific_code"=>0, /* 0: Undefined 1: Mr 2: Madam 3: Miss */
	        "birth_name"=>substr($t_apelido,0,32),
	        /*"last_name"=>"DUPONT",*/
	        "first_name"=>substr($t_nome,0,32)
	        /*"birth_date"=>"1990-01-25",*/
	        /*"birth_municipality_code"=>"59000",*/
	        /*"birth_arrondissement_code"=>"",*/
	        /*"birth_country_code"=>"FRA",*/
	        /*"citizenship_country_code"=>"FRA"*/
	    );
	}
	else
	{
		$identity = array(
	        "person_type"=>"2", /* 1 : Company 2 : Individuals */
	        "honorific_code"=>0, /* 0: Undefined 1: Mr 2: Madam 3: Miss */
	        "birth_name"=>substr($t_nome,0,32),
	        "first_name"=>substr($t_nome,0,32)
	    );
	}

    $contact_details = array(
        "landline_number"=>substr($t_telefone,0,15), /* At least one of the numbers must be provided. Prefixed with international area code. Example: (+33)|0033|+33|33|+33(0)|0XXXXXXXXX */
        "mobile_phone_number"=>substr($t_telefone,0,15),
        "email_address"=>substr($t_email,0,100)
    );

    if($t_morada2 != '')
    {
		$customer_address = array(
	    	"line1"=>substr($t_morada,0,38),
	    	"line2"=>substr($t_morada2,0,38),
	    	"postal_code"=>"$t_codpos1",
	    	"municipality"=>substr($t_localidade,0,100),
	    	"country_code"=>"PRT",
	    	"country_label"=>"Portugal"
	    );
    }
    else
    {
	    $customer_address = array(
	    	"line1"=>substr($t_morada,0,38),
	    	"postal_code"=>"$t_codpos1",
	    	"municipality"=>substr($t_localidade,0,100),
	    	"country_code"=>"PRT",
	    	"country_label"=>"Portugal"
	    );
    }

	$customer = array(
        "customer_external_code"=>$id_cliente, /* Customer unique ID merchant side */
        "language_code"=>$LANGUAGE_CODE, /* Customer language code , Used to display Oney page with customer language */
        "identity"	=> $identity, /* Object */
        "contact_details" => $contact_details,/* Object */
        "customer_address" => $customer_address/* Object */
    );

	$_GET['p']=3;

	if(intval($_GET['p'])==3)
	{
		if($metodo_sandbox==1)
		{
			$business_transaction = array(
		        "code"=>"3x001", /* To identify the offer */
		        "version"=>null,
		        "business_transaction_type"=>"AFFECTE"
		    );
		}
		else
		{
		    $business_transaction = array(
		        "code"=>"3x002", /* To identify the offer */
		        "version"=>null,
		        "business_transaction_type"=>"AFFECTE"
		    );
		}
	}
	else
	{
		if($metodo_sandbox==1)
		{
			$business_transaction = array(
		        "code"=>"4x001", /* To identify the offer */
		        "version"=>null,
		        "business_transaction_type"=>"AFFECTE"
		    );
		}
		else
		{
			$business_transaction = array(
		        "code"=>"4x002", /* To identify the offer */
		        "version"=>null,
		        "business_transaction_type"=>"AFFECTE"
		    );
		}
	}


    $payment = array(
        "payment_amount"=>$TotalDocComIva, /* Decimal amount (Len = 18 max) #5: Optional if payment_type = 2 */
        "currency_code"=>"EUR",
        "payment_type"=>0,
        "business_transaction"=>$business_transaction /* Mandatory even if business_transaction_list is not present #5: Optional if payment_type = 2 */
    );

    $navigation = array(
        "success_url"=>$GLOBALS['site_http_url']."pgm_oney/success.php", /* Return success url */
        "fail_url"=>$GLOBALS['site_http_url']."pgm_oney/cancelled.php",
        "server_response_url"=>$GLOBALS['site_http_url']."pgm_oney/info.php" /* Must be “https//…..” */
    );

    $postValues = array(
    	'merchant_guid' => $MERCHANT_GUID, /* A corporation may have several merchant_guid */
        'psp_guid' => $PSP_GUID, /* PSP unique ID provided by Oney. */
        'merchant_request_id' => $ID_Log, /* Must be unique in each request */
        'purchase'    =>  $purchase,
        'customer'	  =>  $customer,/*#5: Optional if payment_type = 2*/
        'payment'	  =>  $payment,
        'navigation'	  =>  $navigation,
        'merchant_context'	  =>  ""
    );


	$json_string=json_encode($postValues);

	/*echo "<br><br>";
	echo $json_string;
	echo "<br><br>";*/
	//die('#debug');
	//echo "<br><br>";
	//echo '<textarea>';var_dump($postValues); echo '</textarea>';
	//echo $GATEWAY_URL.$FacilyPayURL;


	$headers[] = 'X-Oney-Authorization:'.$API_KEY_PAYMENT;
	$headers[] = 'X-Oney-Partner-Country-Code:PT';
	$headers[] = 'X-Oney-Secret:None';
    $headers[] = 'Content-Type:application/json';



    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $GATEWAY_URL.$FacilyPayURL);

	$sql="UPDATE oney_logs set Json='".addslashes($json_string)."', Estado='ENVIADO PARA ONEY' WHERE ID = '$ID_Log' ";
	$res=Runquery($db_link, $DB_name, $sql);


	curl_setopt($curl, CURLOPT_CUSTOMREQUEST , "POST");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE);
    curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILE);

    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);

    curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
    //Default timeout is 0 (zero) which means it never times out during transfer.
    //curl_setopt($curl, CURLOPT_TIMEOUT_MS, 400);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); //300 seconds é o default mesmo que se coloque 0
	curl_setopt($curl, CURLOPT_TIMEOUT, 20000); //timeout in 20 seconds

	curl_setopt($curl, CURLOPT_LOW_SPEED_TIME, 60000);
  	curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 30000);

	set_time_limit(0);// to infinity

    $tentativas=0;
    $data = curl_exec($curl);

    if($metodo_sandbox==1)$url='https://demo.oney.pt/Subscription/PaymentPage_Entry.aspx';
	else $url='https://3x4x.oney.pt/Subscription/PaymentPage_Entry.aspx';

    if(substr_count($data,$url))
    {

    	$sql="UPDATE oney_logs set Erros='".addslashes($data)."' WHERE ID = '$ID_Log' ";
		$res=Runquery($db_link, $DB_name, $sql);
		$data=substr($data,strpos($data,'"returned_url"')+17,strlen($data));
		$data=substr($data,0,strpos($data,'"'));
    	echo '<script>window.open("'.$data.'","_top")</script>';
    }
    else
    {
    	$sql="UPDATE oney_logs set Erros='".addslashes($data)."' WHERE ID = '$ID_Log' ";
		$res=Runquery($db_link, $DB_name, $sql);
    	echo "ERRO";
    	echo "<br>";
    	var_dump($data);
    }
}
?>
</body>
</html>