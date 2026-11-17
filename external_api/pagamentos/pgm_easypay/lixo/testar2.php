<?php




    $body = [
        "type"     => ["single"],
        "payment"  => [
            "methods" => ["cc", "mb", "mbw"],
            "key"     => "Key Example",
            "type"    => "authorisation",
        ],
        "customer" => [
            "name"             => "Customer Example",
            "email"            => "customer@example.com",
            "key"              => "Key Example",
            "phone_indicative" => "+351",
            "phone"            => "911234567",
            "fiscal_number"    => "PT123456789",
        ],
        "order"    => [
            "key"   => "Order Key Example",
            "value" => 12.50,
            "items" => [
                [
                    "key"         => "Order Item Key Example",
                    "description" => "Item N1",
                    "value"       => 10,
                    "quantity"    => 2,
                ],
                [
                    "key"         => "Order Item Key Example",
                    "description" => "Item N2",
                    "value"       => 2.50,
                ],
            ],
        ],
        "config"   => ["key" => "value"],
    ];


/*
Molaflex:
Conta de Pagamentos: 999802358  
Account UID: 22e8b871-9fd0-437e-a4c6-5f883b6f3475   PT  
Entidade: 11683 --> Francisco & Brites Lda. (Neocasa)
token: 87ac92886a71fc5572f7d5fa15376c22

Neocasa
Conta de Pagamentos: 2411   
Account UID: 41e16489-5fb3-4c90-8099-38b110f38319   PT  
Entidade: 10611 --> Francisco & Brites Lda. (Neocasa)
token:  c1e8370d51c7a1dd4fa5bf3ce0048f7e
*/



    $headers = [
        "AccountId: 83e329a1-147f-48d1-a894-6f28bffdb9d3",
        "ApiKey: 569d6b05-e247-4c24-829b-26fbff13f93d",
        'Content-Type: application/json',
    ];

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


    var_dump($response_body);



?>