<?php
$path = dirname(__FILE__) . '/../../../core/';
require_once $path . 'core_files.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);

//$url_com = 'https://paymentgateway.integration-cb4x.fr';
$url_com = 'https://paymentgateway.cb4x.fr/';

//$url_com = 'https://paymentgateway.cb4x.fr';


$orderref = 9999180;

if(($_GET['p']) == 3 ) {

    echo 'session =>'.$_COOKIE['paymentSessionId']."<br>";
    echo 'token =>'.$_COOKIE['token_card']."<br>";

    echo '<pre>'.print_r(json_decode($_COOKIE['retorno_eleg']))."</pre><br>";

/*
    $Hmac = '336AC9E91CE394145B177CD14807D4F199A6AC74';
    $MerchantID = '81';

$UserName = 'TestUser';
    $Password = 'TestUser1234*';
    $MerchantSiteIDD = 701598;
*/
    $Hmac = 'F00C98ABACC5C2D14E04496CB45DA7AC0C9AF01D';
    $MerchantID = '81';

    $UserName = 'WEBELEC';
    $Password = 'WEBELEC1234*';
    $MerchantSiteIDD = 701598;

    //$UserName = 'y9Q6WEBELEC9FRVR_6aVCP^jMXDxb1o~';
    //$Password = 'p-@1VBU$MD=SkS6AS9SN1k=eXEyxtAtX!kDh8jE_@dr';


    $MerchantSiteID_3xg = '701598';
    $PaymentOptionRef_3xg = '81';
    $Commissons_3xg = '2.9';
    $MIN_3xg = '50';
    $MAX_3xg = '2500';



    $res = autentication_floa($UserName, $Password, $url_com);
    $res2 = explode(';', $res);
    $token = $res2[1];

    /*
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    */
    //unset($_COOKIE['paymentSessionId']);

$jsondata ='{
  "merchantId": 2678,
  "merchantSiteId": 701598,
  "attempt": 1,
  "rank": 1
} ';

    $url = "{$url_com}/v1/payment-sessions/".$_COOKIE['paymentSessionId']."/paymentResult";
    //$url = "https://paymentgateway.integration-cb4x.fr/v1/payments/109/operations/capture";

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
            'authToken: ' . $token
        )
    );
    $response = curl_exec($ch);
    $retorno = json_decode($response, true);


    if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }



    echo '<pre>';
    print_r($retorno);
    echo '</pre>';

    die();

}
/*
$Hmac = '336AC9E91CE394145B177CD14807D4F199A6AC74';
$MerchantID = '38';

$UserName = 'TestUser';
$Password = 'TestUser1234*';
$MerchantSiteIDD = 71088;
*/
$Hmac = 'F00C98ABACC5C2D14E04496CB45DA7AC0C9AF01D';
$MerchantID = '2678';

$UserName = 'WEBELEC';
$Password = 'WEBELEC1234*';
$MerchantSiteIDD = 701598;

/*
$Hmac = '336AC9E91CE394145B177CD14807D4F199A6AC74';
$MerchantID = '38';
$UserName = 'y9Q6WEBELEC9FRVR_6aVCP^jMXDxb1o~';
$Password = 'p-@1VBU$MD=SkS6AS9SN1k=eXEyxtAtX!kDh8jE_@dr';


$MerchantSiteID_3xg = '701598';
$PaymentOptionRef_3xg = '31';
$Commissons_3xg = '2.9';
$MIN_3xg = '50';
$MAX_3xg = '2500';
*/
//die(date("Y-m-d\TH:i:s.000\Z", strtotime(date('Y-m-d H:i:s'))));

//die(date('Y-m-d', strtotime(date('Y-m-d'). ' + 5 days')));
//die($_SERVER['SCRIPT_URI']);

$eligibilityRequest = ' 
 {
    "presaleFolder": {
        "shoppingCarts": [
            {
                "reference": "'.$orderref.'",
                "rawAmount": 5000,
                "productsCount": 1,
                "products": [
                    {
                        
                        "categories": [
                            {
                                "name": "Kitchen appliances",
                                "parent": ""
                            }
                        ],
                        "shipping": {
                            "method": "STD"
                        }
                    }
                ]
            }
        ],
        "customer": {
            "reference": "00123456789",
            "firstName": "Will",
            "lastName": "Santos",
            "email": "email@mail.com",
            "birthDate": "1986-07-16T00:00:00.0000000+00:00",
            "birthZipCode": "99",
            "civility": "Mr",
            "cellPhoneNumber": "123456789",
            "nationalId": "999999999",
            "homeAddress": {
                "line1": "Avenida 5 de Outubro 197",
                "zipCode": "1050-054",
                "city": "Lisboa",
                "countryCode": "PT"
            },
            "history": {
                "firstOrderDate": "'.date('Y-m-d').'T00:00:00.0000000+00:00",
                "lastOrderDate": "'.date('Y-m-d', strtotime(date('Y-m-d'). ' + 5 days')).'T00:00:00.0000000+00:00"
            }
        },
        "saleChannel": "Desktop"
    }
}
 ';
// die($eligibilityRequest);
$res = autentication_floa($UserName, $Password, $url_com);
$res2 = explode(';', $res);
$token = $res2[1];

$MerchantID = 38;// 71088;  //38;
$MerchantSiteID = 71088; // 71088;

//$url_e= " https://eligibility.integration-cb4x.fr/api/v1/eligibilities?merchantId=38&merchantSiteIds=71088";
$url_e= 'https://eligibility.integration-cb4x.fr/api/v4.0/eligibilities?merchantId=' . $MerchantID . '&merchantSiteIds=' . $MerchantSiteID.'&sendingTypes=Web';
//$url_e= 'href":"https://eligibility.integration-cb4x.fr/api/v4.0/eligibilities?merchantId=38&merchantSiteIds=71088&requestId=1476197';

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url_e);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'authToken: ' . $token,
    'Content-Type: application/json',
    'accept: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS,$eligibilityRequest);

print_r(curl_exec($ch));
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
}
curl_close($ch);
*/

$ch = curl_init();
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url_e);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'authToken: ' . $token,
    'Content-Type: application/json',
    'accept: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS,  ($eligibilityRequest));
$response = (curl_exec($ch));

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $header_size);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


//if ($_GET['log'] == 1) {
    echo "linha: 938<hr><pre>";
    // echo "<hr>Token: {$token}<hr><br>";
    echo "<hr>220 Httpcode: {$httpcode}<hr><br>";
    if($httpcode == 200 ){
        $retorno = json_decode($response, true);
        print_r($retorno);
        setcookie('retorno_eleg', $response, time()+3600); //cookie not set

        $url2 = $retorno['eligibilities'][0]['links'][0]['href'];
        $scoringToken = $retorno['eligibilities'][0]['token'];

        echo "<hr>$url2<hr><br>";



    }



echo ($response[0]['links'][0][href]);

    //print_r(json_decode($response));
    echo "</pre><hr>";
// }

$jsondata = '{
    "merchantId": "'.$MerchantID .'",
    "merchantSiteId": "'.$MerchantSiteID.'",
    "customer": {
        "customerRef": "' . $orderref . '"
        
    },
    "orderData": {
        "amount": 5000,
        "orderSummaryRef": "' . $orderref . '",
        "orderRef":  "' . $orderref . '",
        "orderDate": "'.date("Y-m-d\TH:i:s.000\Z", strtotime(date('Y-m-d H:i:s'))).'",
        "scoringToken": "' . $scoringToken . '",
        "freeText": "everythingYouWant"
    },
    "configuration": {
        "culture": "pt-PT",
        "template": "Generic",
        
        "merchantBackUrl": "https://www.webelec.pt/external_api/pagamentos/pgm_floa/floateste.php?id_enc=82&p=0",
        "merchantNotifyUrl": "https://www.webelec.pt/external_api/pagamentos/pgm_floa/floateste.php?id_enc=82&p=1",
        "merchantReturnUrl": "https://www.webelec.pt/external_api/pagamentos/pgm_floa/floateste.php?id_enc=82&p=3",
        "paymentOptionRef": "63"
    }
}
';


if ($_GET['m_json'] == 1) {
    echo($jsondata);
}

$url = $url2; // "{$url_com}/v1/payment-sessions";
//die($url);
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'authToken: ' . $token,
    'Content-Type: application/json',
    'accept: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
$response = curl_exec($ch);
$retorno = json_decode($response, true);


echo "linha: ".__LINE__."<hr><pre>";
echo "<hr><pre>  ";
print_r($response);
print_r($retorno);
setcookie('paymentSessionId', $retorno['paymentSessionId'], time()+3600); //cookie not set
setcookie('token_card', $token, time()+3600); //cookie not set

echo "</pre></hr>";
//die();
$paymentLinkUrl = $retorno['paymentSessionUrl'];

header("location:" . $paymentLinkUrl);

//$retorno->paymentSessionUrl
function autentication_floa($merchantLogin = '', $merchantPassword = '', $url_com) {

    $url =  "{$url_com}/v1/auth/token" ;


    $authorization = base64_encode($merchantLogin . ':' . $merchantPassword);

        echo "linha: ".__LINE__."<hr><pre>";
        print_r($url);
        echo "<hr><br>" . $authorization;
        echo "</pre><hr>";

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
            'authorization: ' . $authorization
        )
    );
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo "linha: ".__LINE__."<hr><pre>";
        echo "<hr>Httpcode: {$httpcode}<hr><br>";
        print_r($response);
        echo "</pre><hr>";



    $response = json_decode($response, true);
    curl_close($ch);


    $linha = __LINE__;
    $enc_id = $_GET['id_enc'];
    //esc_log($enc_id, $linha, $response);

    return $httpcode . ';' . $response;

}