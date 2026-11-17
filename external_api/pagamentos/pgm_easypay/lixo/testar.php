<?php
//require_once('./api/Easypay.php');

//error_reporting(E_ALL,~E_NOTICE);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


//conta para gerar url:
$user = '11683';
$entity='11683';
$AccountId='e73b8be1-44bc-4d49-a185-e65e0cfcaf69';//'83e329a1-147f-48d1-a894-6f28bffdb9d3';
$ApiKey='2cedbb14-2387-4919-9eae-df5b16837839';//'569d6b05-e247-4c24-829b-26fbff13f93d';
$url="https://api.prod.easypay.pt/2.0/checkout";

$sandbox=1;

if($sandbox==1)
{
	$url="https://api.test.easypay.pt/2.0/single";

	$chaveId='2cedbb14-2387-4919-9eae-df5b16837839';//CHAVE ID
	$key='5d9b448a-dfc3-4c23-815f-79c034b455b5';      //CHAVE
	$code='5b78ecb97a50f4f52c5141448f357d91';//acesso por codigo em vez de ip
	$conta_pagamentos='998974514'; // OU CIN


	$user='ecommerce';

	$entity='11683';
	$cin='998974514';



	$ENTIDADE='11683';
	$CONTA_PAGAMENTOS='998974514';
	$AccountId='e73b8be1-44bc-4d49-a185-e65e0cfcaf69';

}



$valor=$tvalue = 1200;
$o_name = 'Aderito Fernandes';
$o_email = 'aderito@memogadget.com';
$o_mobile = '914931936';
$o_nif = '510007082';
$id_enc=1;



/*


Molaflex:
Conta de Pagamentos: 999802358	
Account UID: 22e8b871-9fd0-437e-a4c6-5f883b6f3475	PT	
Entidade: 11683	--> Francisco & Brites Lda. (Neocasa)
token: 87ac92886a71fc5572f7d5fa15376c22

Neocasa
Conta de Pagamentos: 2411	
Account UID: 41e16489-5fb3-4c90-8099-38b110f38319	PT	
Entidade: 10611	--> Francisco & Brites Lda. (Neocasa)
token:  c1e8370d51c7a1dd4fa5bf3ce0048f7e


"AccountId: 83e329a1-147f-48d1-a894-6f28bffdb9d3",
"ApiKey: 569d6b05-e247-4c24-829b-26fbff13f93d",
'Content-Type: application/json',

*/



//$ep_data = easypaydata(10);

/*$headers = Array(
	"AccountId" => $AccountId,
	"ApiKey" => $ApiKey,
    'Content-Type: application/json',
);*/

$headers = [
        "AccountId: ".$AccountId,
        "ApiKey: ".$ApiKey,
        'Content-Type: application/json',
    ];


/*$body = [
        "type"     => ["single"],
        "payment"  => [
            "methods" => ["cc", "mbw"],
            "key"     => strval($id_enc),
            "type"    => "authorisation",
        ],
        "customer" => [
            "name"             => $o_name,
            "email"            => $o_email,
            "key"              => strval($id_enc),
            "phone_indicative" => "+351",
            "phone"            => $o_telefone,
            "fiscal_number"    => $o_nif,
        ],
        "order"    => [
            "key"   => strval($id_enc),
            "value" => floatval($tvalue),
            "items" => [
                [
                    "key"         => "Order Item Key Example",
                    "description" => "Item N1",
                    "value"       => floatval($tvalue),
                    "quantity"    => 1,
                ],
            ],
        ],
        "config"   => ["key" => "value"],
    ];*/


//1 - obter uma pre-autorizacao de pagamento

$res = get_autorization($AccountId,$ApiKey,$id_enc);
function get_autorization($id_enc)
{

	$valor=$tvalue = 300;
	$o_name = 'Aderito Fernandes';
	$o_email = 'aderito@memogadget.com';
	$o_telefone = '914931936';
	$o_nif = '510007082';



	$headers = [
        "AccountId: ".$AccountId,
        "ApiKey: ".$ApiKey,
        'Content-Type: application/json',
    ];
    $body = [
        "type"     => ["single"],
        "payment"  => [
            "methods" => ["cc", "mbw"],
            "key"     => strval($id_enc),
            "type"    => "authorisation",
        ],
        "customer" => [
            "name"             => $o_name,
            "email"            => $o_email,
            "key"              => strval($id_enc),
            "phone_indicative" => "+351",
            "phone"            => $o_telefone,
            "fiscal_number"    => $o_nif,
        ],
        "order"    => [
            "key"   => strval($id_enc),
            "value" => floatval($tvalue),
            "items" => [
                [
                    "key"         => "Order Item Key Example",
                    "description" => "Item N1",
                    "value"       => floatval($tvalue),
                    "quantity"    => 1,
                ],
            ],
        ],
        "config"   => ["key" => "value"],
    ];
    try {
		$curlOpts = [
	        CURLOPT_URL            => "https://api.prod.easypay.pt/2.0/checkout",
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_POST           => 1,
	        CURLOPT_TIMEOUT        => 60,
	        CURLOPT_POSTFIELDS     => json_encode($body),
	        CURLOPT_HTTPHEADER     => $headers,
	    ];
		$curl = curl_init();
	    curl_setopt_array($curl, $curlOpts);
	    $response_body = curl_exec($curl);
	    curl_close($curl);
	    $response = json_decode($response_body, true);
	}
	catch( Exception $e )
	{
		$response = false;
	}
	return $response;

}

$pagamento_id = $res["id"];
$res2 = create_payment($pagamento_id,$AccountId,$ApiKey,$id_enc);
function create_payment($pagamento_id,$AccountId,$ApiKey,$id_enc)
{
	$valor=$tvalue = 1200;
	$o_name = 'Aderito Fernandes';
	$o_email = 'aderito@memogadget.com';
	$o_telefone = '930428909';
	$o_nif = '510007082';


	$headers = [
        "AccountId: ".$AccountId,
        "ApiKey: ".$ApiKey,
        'Content-Type: application/json',
    ];


	$body = [
	    "key" => "Key Example",
	    "method" => "sc",        // Enum: "mb" "cc" "mbw" "dd" "bb" "vi" "uf" "sc"     uf = prestacoes cartao universo,  sc = santander consumer entre 300 e 1000
	    "type"	=> "sale",
	    "value"	=> floatval(300),
	    "currency"	=> "EUR",
	    "expiration_time" => date('Y-d-m 12:00', strtotime(date("Y-d-m 12:00"). ' + 1 days')),
	    "capture" => [
	        "transaction_key" => "Transaction Key Example",
	        "descriptive" => "Descriptive Example",
	        "capture_date" => "2018-12-31",
	        "account" => [
	            "id" => $pagamento_id,
	        ],
	    ],
	    "customer" => [
	        "name" => "Customer Example",
	        "email" => "customer@example.com",
	        "key" => "Key Example",
	        "phone_indicative" => "+351",
	        "phone" => "911234567",
	        "fiscal_number" =>"PT123456789",
	    ],
	    "sdd_mandate" => [
	    "name" => "Name Example",
	    "email" => "sdd_email@example.com",
	    "account_holder" => "Account Holder Example",
	    "key" => "SDD Key Example",
	    "iban" => "PT50002700000001234567833",
	    "phone" => "911234567",
	    "max_num_debits" =>"12",
	    ],
	];

	try {
		$curlOpts = [
	        CURLOPT_URL => "https://api.prod.easypay.pt/2.0/single",
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_POST           => 1,
	        CURLOPT_TIMEOUT        => 60,
	        CURLOPT_POSTFIELDS     => json_encode($body),
	        CURLOPT_HTTPHEADER     => $headers,
	    ];

		$curl = curl_init();
	    curl_setopt_array($curl, $curlOpts);
	    $response_body = curl_exec($curl);
	    curl_close($curl);
	    $response = json_decode($response_body, true);
	}
	catch( Exception $e )
	{
		$response = false;
	}
	return $response;
}

print_r($res2);

/*
$body=Array(
			    "type" => array("single"),
				"key" => strval($id_enc),
			    "methods" => Array("cc","mbw"),
			    "value" => floatval($tvalue),
			    "currency" => "EUR",
			    "expiration_time" => date('Y-d-m 12:00', strtotime(date("Y-d-m 12:00"). ' + 1 days')),
			    "capture" => Array(
				        		   "transaction_key" => strval($id_enc),
				        		   "descriptive"=> strval($id_enc),
				        		   "capture_date" => date("Y-m-d"),
				        		   "account" => array(
				        		   		"id" => $AccountId
				        		   	),
				    			   ),
				"customer" => Array(
				        		   "name" => $o_name,
				        		   "email" => $o_email,
				        		   "key" => strval($id_enc),
				        		   "phone_indicative" => "+351",
				        		   "phone" => $o_telefone,
				        		   "fiscal_number" => $o_nif,
				    			   ),
			);*/


?>
<script>window.location.replace("<?php echo $res2['method']['url'];?>");</script>

<div runat="server" id="ep_box" style="margin:0 auto; text-align:center; border: 1px solid #ddd; border-radius: 5px; width: 210px; min-height: 70px; padding:10px;" >
	<img src="http://store.easyp.eu/img/easypay_logo_nobrands-01.png" style="height:40px; margin-bottom: 10px;" title="Se quer pagar uma referência multibanco utilize a easypay" alt="Se quer pagar uma referência multibanco utilize a easypay" />
	<div style="margin-top: 10px; width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee" >
		<a href="<?php echo $res2['method']['url'];?>">
			<img src="http://store.easyp.eu/img/visa_mc_bw-01.png" />
			<div style="margin-top: 20px;padding: 5px; padding-top: 10px; clear: both;" >
				<span style="color: #0088cc; text-decoration: none;">Aguarde, está a ser redireccionado</span>
			</div>
		</a>
	</div>
</div>
