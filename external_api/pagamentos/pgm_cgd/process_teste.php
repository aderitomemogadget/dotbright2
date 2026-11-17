<?php

    $path =  dirname( __FILE__ ) . '/../../../core/';
    include $path.'core_files.php';


    $authentication_entityId = '8a8294185332bbe601533754724914d9';
    $Bearer = 'OGE4Mjk0MTg1MzMyYmJlNjAxNTMzNzU0NzZjMzE1Mjd8RzV3UDVUekY1aw==';
    $Entidade = '21075';


    //Credenciais de Teste:
    /*$authentication_entityId = '8ac7a4c77279a71001727f745156182e';
    $Bearer = 'OGE4Mjk0MTg1YjY3NDU1NTAxNWI3YzE5MjhlODE3MzZ8UnI0N2VRZXNkVw==';
    $Entidade = '25002';*/


    function request($authentication_entityId,$Bearer) {
    $url = "https://test.oppwa.com/v1/payments";
    $data = "entityId=$authentication_entityId" .
                "&amount=10.00" .
                "&currency=EUR" .
                "&paymentType=PA" .
                "&paymentBrand=SIBS_MULTIBANCO" .
                "&merchantTransactionId=abc12345" .
                "&customParameters[SIBSMULTIBANCO_PtmntEntty]=$Entidade" .
                "&customParameters[SIBSMULTIBANCO_RefIntlDtTm]=2016-06-06T12:28:32.001+01:00" .
                "&customParameters[SIBSMULTIBANCO_RefLmtDtTm]=2016-06-06T13:28:32.001+01:00" .
                "&customer.ip=176.111.107.44" .
                "&customer.surname=Garage" .
                "&customer.givenName=Stage" .
                "&billing.country=PT" .
                "&testMode=EXTERNAL" .
                "&customParameters[SIBS_ENV]=QLY" .
                "&shopperResultUrl=https://sibs.docs.onlinepayments.pt/tutorials/server-to-server";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer '.$Bearer));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $responseData;
}
$responseData = request($authentication_entityId,$Bearer);


$responseData2 = json_decode($responseData, true);
$referencia=$responseData2["result"];

echo $referencia["code"];

die();



echo '<pre><textarea style="width:100%;height:100%">';

var_dump(json_decode($responseData));

echo '</textarea></pre>';

    die();


    //test sibs multibanco:
    /*function request() {
    $url = "https://test.oppwa.com/v1/payments";
    $data = "entityId=8a8294185332bbe601533754724914d9" .
                "&amount=10.00" .
                "&currency=EUR" .
                "&paymentType=PA" .
                "&paymentBrand=SIBS_MULTIBANCO" .
                "&merchantTransactionId=abc12345" .
                "&customParameters[SIBSMULTIBANCO_PtmntEntty]=21075" .
                "&customParameters[SIBSMULTIBANCO_RefIntlDtTm]=2016-06-06T12:28:32.001+01:00" .
                "&customParameters[SIBSMULTIBANCO_RefLmtDtTm]=2016-06-06T13:28:32.001+01:00" .
                "&customer.ip=123.123.123.123" .
                "&customer.surname=Jones" .
                "&customer.givenName=Jane" .
                "&billing.country=PT" .
                "&shopperResultUrl=https://sibs.docs.onlinepayments.pt/tutorials/server-to-server";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer OGE4Mjk0MTg1MzMyYmJlNjAxNTMzNzU0NzZjMzE1Mjd8RzV3UDVUekY1aw=='));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $responseData;
}
$responseData = request();
*/

    // Test mbway:
    /*function request() {
    $url = "https://test.oppwa.com/v1/payments";
    $data = "entityId=8a8294185332bbe601533754724914d9" .
                "&amount=10.00" .
                "&currency=EUR" .
                "&paymentType=PA" .
                "&paymentBrand=MBWAY" .
                "&virtualAccount.accountId=351#911222111" .
                "&shopperResultUrl=https://sibs.docs.onlinepayments.pt/tutorials/server-to-server" .
                "&customParameters[SIBS_ENV]=QLY";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer OGE4Mjk0MTg1MzMyYmJlNjAxNTMzNzU0NzZjMzE1Mjd8RzV3UDVUekY1aw=='));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $responseData;
}
$responseData = request();*/



//Get Payment Status
//
/*function request() {
    $url = "https://test.oppwa.com/v1/payments/{id}";
    $url .= "?entityId=8a8294185332bbe601533754724914d9";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer OGE4Mjk0MTg1MzMyYmJlNjAxNTMzNzU0NzZjMzE1Mjd8RzV3UDVUekY1aw=='));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if(curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $responseData;
}
$responseData = request();*/

?>