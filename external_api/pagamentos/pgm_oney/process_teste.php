<?php
    $path =  dirname( __FILE__ ) . '/../../../core/';
    include $path.'core_files.php';
?>
<html>
<head><title>::PHP Oney::</title></head>
<body>
<!-- <body onLoad="document.oney_form.submit();"> -->
<!-- <form method="post" name="oney_form" action="<?=$paypal[url]?>"> -->

<?php
//showVariables();
?>
<center><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="333333">Processing Transaction . . . </font></center>
<!-- </form> -->
<?php

$n_encomenda = "202001028";
session_start();
if($n_encomenda)
{
    global $DB_host,$DB_user,$erp_db_password,$DB_pass,$lang,$ss_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$erp_db_password);
    $dados=new DataObj;
    // Encomenda
	$sql="SELECT * FROM encomendas WHERE ID2='$n_encomenda'";
	$dados->Open($db_link, $DB_pass, $sql);
	$enc_nome="Clickfiel Online Nº $n_encomenda";

	$TotalDocComIva = round($dados->aFields["TotalDocComIva"], 2);

	$valor = $TotalDocComIva;
   	$sql="SELECT * FROM logins WHERE ID='$ss_id'";
	$dados->open($db_link, $DB_pass, $sql);
	$firstname=$dados->aFields["Nome"];
	$email=$dados->aFields["username"];



	$LANGUAGE_CODE = "PT";
    $MERCHANT_GUID = "b36d5c06-bf0a-49d3-880f-4445fbb2c9bd";
    $PSP_GUID = "f13a7218-96c4-4a51-91bb-3d19d567136a";

    $API_KEY_PAYMENT = "9fc219556fe1490caca1f4e6e47d98c9";
   	$API_KEY_MARKETING = "e68bf1e5b1174180ab3529a6d8b6b29d";

	$SANDBOX_GATEWAY_URL = "https://api-staging.oney.io/staging";
    $PRODUCTION_GATEWAY_URL = "https://api.oney.io/";

    $FacilyPayURL = "/payments/v1/purchase/facilypay_url";
    $Oneycardpayment = "/payments/v1/purchase/card_payment_url";

	/*all URL must be encoded. Encoding must be realized on all the url except “http//” or “https://” value. So, if there is a pipe “|” in the url, the encoded url will contain “%7C” instead of the “|”.*/

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $SANDBOX_GATEWAY_URL.$FacilyPayURL);










    $item_list = array(
    	"is_main_item"=>1, /* 1: if it is the main product (the most expensive) 0: otherwise. item_list MUST contain at least one main item */
	    "category_code"=>"8", /* 1: Food & drinks 2: Auto & motorcycle 3: Culture & entertainment 4: Home & garden 5: Home appliances 6: Bidding and multi purchasing 7: Flowers & gifts 8: Computers & software 9: Health & beauty 10: Personal services 11: Professional services 12: Sport 13: Clothing & accessories 14: Travel & tourism 15: Hifi, photo & video 16: Telephone & communication */
	    "label"=>"Micro ondes Brandt",
	    "item_external_code"=>"BRAN4588",
	    "quantity"=>"1",
	    "price"=>"15.15", /* Price with decimal 15,2 */
	    "marketplace_merchant_flag"=>0, /* 1: if the item comes from a marketplace */
	    "marketplace_merchant_name"=>null /* Mandatory if marketplace_merchant_flag value is 1 */
    );

    $delivery_address = array(
        "line1"=>"Avenida Professor Doutor Augusto", /* Street number, Street type and Street name */
        "postal_code"=>"2675-079",
        "municipality"=>"Odivelas", /* Max Len = 32 for France */
        "country_code"=>"PRT",
        "country_label"=>"Portugal"
    );
	$recipient = array(
        /*'recipient_honorific_code'    =>  "3", *//* opcional,  1: Mr 2: Madam 3: Miss */
        'surname' => "TOT",
        'first_name'    => "TATT",
        'phone_number'    =>  "+351914931936" /* Prefixed with international area code. Example: (+33)|0033|+33|33|+33(0)|0XXXXXXXXX */
    );
	$delivery = array(
        'delivery_date'    =>  date('Y-m-d'), /* Expected delivery date, Format : yyyy-MM-dd */
        'delivery_mode_code' => "4", /* Code of the delivery mode, 1 : Collection of the goods in the merchant store 2 : Collection in a third party point (like ups, alveol, etc.) 3 : Collection in an airport, train station or travel agency 4 : Carrier (La Poste, Colissimo, UPS, DHL...or any private carrier) 5 : Electronic ticket issuance, download, etc. */
        'delivery_option'    => "2", /* Delivery delay 1 : Express (< 24 hours) 2 : Standard 3 : Priority */
        /*'priority_delivery_code'    =>  "1",*/ /* Mandatory if delivery_option value is 3 (Priority) 1 : Less than or equal to 1 hour 2 : Greater than 1 hour 3 : Immediate 4 : 24/24 7/7 */
        'address_type'    =>  "5", /* Delivery address type 1 : Merchant 2 : Third party relay point 3 : Airport, train station, travel agency 4 : Billing address 5 : Delivery address 6 : Electronic way (ticket, download) */
        'recipient'    =>  $recipient, /* Mandatory if adress_type = 5 */
        'delivery_address'    =>  $delivery_address
    );

	$purchase = array(
        'external_reference_type'    =>  "CMDE", /* Allow to qualify the type of external_reference, Mandatory value: CMDE */
        'external_reference' => $n_encomenda, /* Unique identifier of the merchant on the purchase, Number to identify a customer's order unit (ex: '1371075') without space */
        'purchase_amount'    =>  "112.98", /* Total price of the purchase, Total price of the basket including any payment already made with other means of payment (gift cards, having, etc.)The price is decimal (Len = 18 max) 15,2 */
        'currency_code'    =>  "EUR", /* Currency code */
        'delivery'    =>  $delivery, /* Delivery data */
        'number_of_items'    =>  "1", /* Number of items in the basket */
        'item_list'    =>  array($item_list)
    );



	$identity = array(
        "person_type"=>"2", /* 1 : Company 2 : Individuals */
        "honorific_code"=>0, /* 0: Undefined 1: Mr 2: Madam 3: Miss */
        "birth_name"=>"ADERITO",
        /*"last_name"=>"DUPONT",*/
        "first_name"=>"ADERITO"
        /*"birth_date"=>"1990-01-25",*/
        /*"birth_municipality_code"=>"59000",*/
        /*"birth_arrondissement_code"=>"",*/
        /*"birth_country_code"=>"FRA",*/
        /*"citizenship_country_code"=>"FRA"*/
    );

    $contact_details = array(
        "landline_number"=>"00351914931936", /* At least one of the numbers must be provided. Prefixed with international area code. Example: (+33)|0033|+33|33|+33(0)|0XXXXXXXXX */
        "mobile_phone_number"=>"00351914931936",
        "email_address"=>"aderito@memogadget.com"
    );


    $customer_address = array(
    	"line1"=>"8 rue Pierre et Marie Curie",
    	"postal_code"=>"59260",
    	"municipality"=>"PORTO",
    	"country_code"=>"PRT",
    	"country_label"=>"Portugal"
    );


	$customer = array(
        "customer_external_code"=>"ABA123sB", /* Customer unique ID merchant side */
        "language_code"=>$LANGUAGE_CODE, /* Customer language code , Used to display Oney page with customer language */
        "identity"	=> $identity, /* Object */
        "contact_details" => $contact_details,/* Object */
        "customer_address" => $customer_address/* Object */
    );


    $business_transaction = array(
        "code"=>"3x002", /* To identify the offer */
        "version"=>null,
        "business_transaction_type"=>"AFFECTE"
    );

    /*$business_transaction_list = array(
        "business_transaction"=>$business_transaction
    );*/


    $payment = array(
        "payment_amount"=>666.50, /* Decimal amount (Len = 18 max) #5: Optional if payment_type = 2 */
        "currency_code"=>"EUR",
        "payment_type"=>0,
        "business_transaction"=>$business_transaction /* Mandatory even if business_transaction_list is not present #5: Optional if payment_type = 2 */
    );



    $navigation = array(
        "success_url"=>"https://www.clickfiel.pt", /* Return success url */
        "fail_url"=>"https://www.clickfiel.pt",
        "server_response_url"=>"https://www.clickfiel.pt" /* Must be “https//…..” */
    );




    $postValues = array(
    	'merchant_guid' => $MERCHANT_GUID, /* A corporation may have several merchant_guid */
        'psp_guid' => $PSP_GUID, /* PSP unique ID provided by Oney. */
        'merchant_request_id' => $n_encomenda, /* Must be unique in each request */
        'purchase'    =>  $purchase,
        'customer'	  =>  $customer,/*#5: Optional if payment_type = 2*/
        'payment'	  =>  $payment,
        'navigation'	  =>  $navigation,
        'merchant_context'	  =>  ""
    );


	$json_string=json_encode($postValues);
echo '<pre>';
	var_dump($json_string);


	$headers[] = 'X-Oney-Authorization:'.$API_KEY_PAYMENT;
	$headers[] = 'X-Oney-Partner-Country-Code:PT';
	$headers[] = 'X-Oney-Secret:None';
    $headers[] = 'Content-Type:application/json';

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

    $data = curl_exec($curl);

	var_dump($data);




}






?>
</body>
</html>