<?php
$path = dirname(__FILE__) . '/../../../core/';
require_once $path . 'core_files.php';
/* lembrar de criar essa tabela no banco de dados
DROP TABLE IF EXISTS `floa_logs`;
CREATE TABLE `floa_logs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Encomenda` int(11) NOT NULL,
  `Data` date NOT NULL,
  `Json` varchar(2000) NOT NULL,
  `Json_eleg` varchar(3000) NOT NULL,
  `Estado` varchar(255) NOT NULL,
  `Erros` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

function dadosfloa_logs($id_floa){

    global $DB_host, $DB_user, $DB_pass, $DB_name, $total_amount;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $dados = new DataObj;

    $sql = "SELECT Json FROM `floa_logs` WHERE ID='$id_floa'";
    $dados->db_open($db_link, $DB_name, $sql);
    $return = '';

    //print_r($dados);
    if ($dados->EOF()) {
        $json = json_decode($dados->db_data["Json"], true);
        return $json;
    }

    $json = json_decode($dados->db_data["Json"], true);
    $dados->db_close();
    return $json;
}

/*retorno de pagamento */
if($_GET['ret']==3){
    $id_enc2 = $_GET['id_enc'];

    $json_post = json_encode($_POST);
    $json_get  = json_encode($_GET);
    file_put_contents('log/floa'.date('Y_m_d_h_i_s').'.txt', file_get_contents('php://input').' GET: '.$json_get.' POST: '.$json_post);

    $id_enc = $_GET['id_enc'];
    $orderRef =  $_POST['orderRef'];

    $orderRef = explode('A', $orderRef);
    $id_enc2 = $orderRef[0];//$_POST['item_number'];
    $id_floa = $orderRef[1];//$_POST['item_number'];

    $card_type = $_POST['cardType']; //confere se é visa ou mastercard ou outro

    if($_POST['returnCode'] == 0){
        $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='13' WHERE ID='" . $id_enc2 . "'";
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
        enviarEmailCliente($id_enc2);


        $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $sql = "UPDATE floa_logs SET estado = 'Paga' WHERE ID=" .$id_floa;
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

    }else{
        $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $sql = "UPDATE encomendas SET Encomenda_Paga=0, Estado='14' WHERE ID='" . $id_enc2 . "'";
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

        $dados_ret = dadosfloa_logs($id_floa);
        $dados_post = json_decode($json_post, true);

        $jsonData = array_merge($dados_ret, $dados_post);
        $updatedJson = json_encode($jsonData);

        $sql = "UPDATE floa_logs SET json = '$updatedJson' WHERE ID=" .$id_floa;
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

    }


    /*
    elseif (isset($payment_status)) {
        $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='" . $payment_status . "',Estado='14' WHERE ID='" . $id_enc2 . "'";
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
    }
*/
    $myFile = dirname(__FILE__) . '/../../../files/ipn_' . $id_enc2 . '.txt';
    $fh = fopen($myFile, 'w') or die("can't open file");
    fwrite($fh, print_r($_POST, true) . "\n" . $sql);
    fclose($fh);


    $url = "https://www.dotbright.pt/p5-registo-utilizador-pt?id_enc={$id_enc}&show_compras=1";
    header("location:" . $url);

    exit();


}


unset($_SESSION['buca_erros']);
$enc_id = $_GET['id_enc'];
$log_floa = floa_logs($enc_id);

$cred_config = $config_floa;

$metodo = dadosencomenda();

if($metodo== 18) {
    $cred = [
            'MerchantID'      => 2551,
            'MerchantSiteIDD' => 700945,
            'ref'             => $log_floa['ref'],
            'enc_id'          => $enc_id,
            'id_log'          => $log_floa['id_log'],
            'pay_optionref'   => 81
    ];
}
if($metodo == 19) {
    $cred = [
            'MerchantID'      => 2551,
            'MerchantSiteIDD' => 700946,
            'ref'             => $log_floa['ref'],
            'enc_id'          => $enc_id,
            'id_log'          => $log_floa['id_log'],
            'pay_optionref'   => 63
    ];
}


$credentials = array_merge($cred_config, $cred);
//print_r($credentials);
//die();
function dadosencomenda(){
    $enc_id = $_GET['id_enc']; //  32;
    global $DB_host, $DB_user, $DB_pass, $DB_name, $total_amount;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $dados = new DataObj;

    $sql = "SELECT Metodo_pagamento FROM encomendas WHERE ID='$enc_id'";
    $dados->db_open($db_link, $DB_name, $sql);
    $return = '';

    //print_r($dados);
    if ($dados->EOF()) {
        echo 'aaaa';
        echo false;
        die();
    }

    $metodo = $dados->db_data["Metodo_pagamento"];
    $dados->db_close();
return $metodo;
}

function floa_logs($enc_id){

    $ret = array();
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

    $sql = "INSERT INTO floa_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('" . $enc_id . "','" . date("Y-m-d H:m:i") . "', '', 'A PREPARAR O ENVIO PARA ONEY','')";
    $res = Runquery($db_link, $DB_name, $sql);
    $ID_Log = db_get_last_insert_id($db_link);

    $orderRef = $enc_id . 'A' . $ID_Log;
    $ret['ref']    =  $enc_id . 'A' . $ID_Log;
    $ret['id_log'] = $ID_Log;


    return $ret;
}

function autentication_floa($credentials) {

    $url = "{$credentials['url_com']}/v1/auth/token";

    $authorization = base64_encode($credentials['UserName'] . ':' . $credentials['Password']);

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
    $response = json_decode($response, true);

    if ($_GET['debug'] == 1) {
        echo "<pre>Debug Floa function autentication_floa </pre>";
        echo "linha: " . __LINE__ . "<hr>";
        echo "<pre>url: " . $url . "<hr></pre>";
        echo "<pre>authorization: " . $authorization . "<hr></pre>";
        echo "<pre><hr>Httpcode: {$httpcode}</pre>";
        echo "<pre>Retorno da API: ";
        print_r($response);
        echo "</pre><hr>";
    }
    curl_close($ch);

    if ($httpcode == 200) {
        return $response;
    }
    else {
        return 0;
    }
    /*
    print_r($response);
        die();

        return $httpcode . ';' . $response;*/
}



function DadosEligibility($credentials) {

    $enc_id = $credentials['enc_id']; //  32;
    global $DB_host, $DB_user, $DB_pass, $DB_name, $total_amount; 
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs = new DataObj;
    $dados = new DataObj;
    $result = new DataObj;
    $rs_metodo = new DataObj;

    $reportDelayInDays = 0;

    // Encomenda
    $sql = "SELECT * FROM encomendas WHERE ID='$enc_id'";
    $dados->db_open($db_link, $DB_name, $sql);
    $return = '';

    //print_r($dados);
    if ($dados->EOF()) {
        echo 'aaaa';
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

    $TotalDocComIva =$_SESSION['total_amount'] =  round($dados->db_data["TotalDocComIva"], 2);

/*
    //Vamos gravar o log das acoes para usar mais tarde
    $sql = "INSERT INTO floa_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('" . $enc_id . "','" . date("Y-m-d H:m:i") . "', '', 'A PREPARAR O ENVIO PARA ONEY','')";
    $res = Runquery($db_link, $DB_name, $sql);
    $ID_Log = db_get_last_insert_id($db_link);
    $orderRef = $credentials['ref']; //$enc_id . 'A' . $ID_Log;
*/

    $item_list = array();

    $sql = 'SELECT * FROM encomendas_produtos where ID_PAI=' . $id_enc . ' order by ID ASC';
    $count = 0;
    $res = $result->db_open($db_link, $DB_name, $sql);
    $item_list = array();
    while (!$result->EOF()) {
        $id_linha = $result->db_data["ID"];
        $id_pai = $result->db_data["ID_PAI"];
        //$Ref = BuscaProdutoIdByRef($result->db_data["Ref"]);
        $nome = $result->db_data["Nome"];
        $valor = $result->db_data["Valor"];
        $qtd = $result->db_data["Qtd"];
        $valor2 = number_format($valor * $qtd, 2, '.', '');//*$qtd
        $nome = limpa_special($nome);

        $item_list_aux = array(
                "name" => $nome,
                "rawAmount" => intval($valor2 * 100),
                "shipping" => array("method" => "UPS"),
                "address" => array("line1"=> substr($dados->db_data["enc_Morada_ent"], 0, 60),
                                "zipCode"=> $dados->db_data["enc_Codigo_Postal"],
                                "city"=> $dados->db_data["enc_Cidade_ent"],
                                "countryCode"=> "pt"
                            )
        );

        $count += $qtd;
        array_push($item_list, $item_list_aux);
        $result->db_next();
    }
    $result->db_close();


    //Portes
    $item_list_aux = array(
            "name" => 'Portes',
            "rawAmount" => intval($port * 100),
            "shipping" => array("method" => "UPS")

    );
    array_push($item_list, $item_list_aux);
    $count++;//addiciona os portes

    //------
    //dados entrega
    $t_nome = rem_caract_esp($dados->db_data["enc_Nome"]);
    $t_apelido = rem_caract_esp($dados->db_data["enc_Apelido"]);
    $t_telefone = preg_replace("/[^0-9]/", "", $dados->db_data["enc_Telefone_ent"]);
    if ($t_telefone == '') {
        $t_telefone = null;
    }

    $t_email = $dados->db_data["enc_Email"];
    $t_morada = substr($dados->db_data["enc_Morada_ent"], 0, 60);
    $t_codpos1 = $dados->db_data["enc_Codigo_Postal"];
    if ($t_codpos1 == '') {
        $t_codpos1 = null;
    }

    $t_cidade = $dados->db_data["enc_Cidade_ent"];
    $t_contribunite = $dados->db_data["N_Contribuinte"];
    $enc_Nif = preg_replace("/[^0-9]/", "", $dados->db_data["enc_Nif"]);

    $t_nome = rem_caract_esp($t_nome);
    $t_apelido = limpa_special($t_apelido);
    $t_morada = limpa_special($t_morada);
    $Local_descarga = $dados->db_data["enc_Morada_ent"];

    //-----------------
    if ($diferentes == 1) {
        $t_morada_aux = $Local_descarga;
        $t_morada_aux2 = '';
        if (strlen($t_morada_aux) > 38) {
            $t_morada_aux = substr($Local_descarga, 0, 38);
            $t_morada_aux2 = substr($Local_descarga, 38, 76);
        }
        if ($t_morada_aux2 != '') {
            $delivery_address = array(
                    "line1" => substr($t_morada_aux, 0, 38),

                    "line2" => null,
                    "line3" => null,
                    "line4" => null,
                    "zipCode" => substr($t_codpos1, 0, 8),
                    "city" => substr($t_cidade, 0, 100),
                    "countryCode" => "PT"
            );
        } else {
            $delivery_address = array(
                    "line1" => substr($t_morada_aux, 0, 38),
                    "line2" => null,
                    "line3" => null,
                    "line4" => null,
                    "zipCode" =>   substr($t_codpos1, 0, 8),
                    "city" => substr($t_cidade, 0, 100),
                    "countryCode" => "PT"
            );
        }
    } else {
        if ($t_morada2 != '') {
            $delivery_address = array(
                    "line1" => substr($t_morada, 0, 38),
                    "line2" => null,
                    "line3" => null,
                    "line4" => null,
                    "zipCode" =>  $t_codpos1,
                    "city" => substr($t_cidade, 0, 100),
                    "countryCode" => "PT"
            );
        } else {
            $delivery_address = array(
                    "line1" => substr($t_morada, 0, 38),
                    "line2" => null,
                    "line3" => null,
                    "line4" => null,
                    "zipCode" =>  $t_codpos1,
                    "city" =>  substr($t_cidade, 0, 100),
                    "countryCode" => "PT"
            );
        }
    }

    if (!$t_apelido) {
        $split = explode(' ', $t_nome);
        $t_nome = $split[0];
        $t_apelido = $split[1];
    }
    if (!$t_apelido) $t_apelido = 'Site Dotbright';


    $customer = array(
            "reference" => $id_cliente,
            "civility" => "Mr",
            "firstName" => substr($t_nome, 0, 64),
            "lastName" => substr($t_apelido, 0,64),
            "email" => $t_email,
            "homePhoneNumber" => $t_telefone,
            "cellPhoneNumber" => null,
            "birthDate" => "{$enc_data_nascimento}T00:00:00.0000000+00:00",

            "homeAddress" => $delivery_address,
            "history" =>[
                    "firstOrderDate"=>  date("Y-m-d\TH:i:s.000\Z", strtotime(date('Y-m-d H:i:s'))) ,
                    "lastOrderDate"=>  date("Y-m-d\TH:i:s.000\Z", strtotime(date('Y-m-d H:i:s')))
                    ],
            "nationalId" => $enc_Nif
    );


    $shoppingCarts = array(
            array(
                    "reference" => $credentials['ref'],
                    "rawAmount" => intval($TotalDocComIva * 100),
                    "productsCount" => $count,
                    "products" => $item_list,
                    "categories"=> [
                            [
                                "name"=> "products",
                                "parent"=> ""
                            ],
                            [
                                "name"=> "vol",
                                "parent"=> "products"
                            ]
                    ],

            )
    );


    $eligibilityRequest = [
            "presaleFolder" =>
                    [
                            "shoppingCarts" => $shoppingCarts,
                            "customer" => $customer,
                            "reportDelayInDays" => 0,
                            "saleChannel" => "Desktop"
                    ]
    ];


    $sql = "UPDATE floa_logs SET Json_eleg = '" . json_encode($eligibilityRequest) . "' WHERE ID=" . $credentials['id_log'];
    //die($sql);
    $res = Runquery($db_link, $DB_name, $sql);
    
 
    return json_encode($eligibilityRequest);
}

function getEligibility($credentials) {  //print_r($credentials);
    $retorno = array();
    $eligibilityRequest = DadosEligibility($credentials);

    $url_e = $credentials['ulr_com_eleg'] . '/api/v4.0/eligibilities?merchantId=' . $credentials['MerchantID'] . '&merchantSiteIds=' . $credentials['MerchantSiteIDD']
            . '&sendingTypes=Web';

    $ch = curl_init();
//curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url_e);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'authToken: ' . $credentials['token'],
            'Content-Type: application/json',
            'accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($eligibilityRequest));
    $response = (curl_exec($ch));

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $header_size);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($_GET['debug'] == 1) {
        echo "<br><br><pre>Debug Floa function getEligibility </pre>";
        echo "linha: " . __LINE__ . "<hr>";
        echo "<pre>url: " . $url_e . "<hr></pre>";
        echo "<pre>authorization: " . $credentials['token'] . "<hr></pre>";
        echo "<pre><hr>Httpcode: {$httpcode}</pre>";
        echo "<pre>Dados Json Eligibility</pre>";
        echo "<pre>{$eligibilityRequest}<hr></pre>";

        echo "<pre>Retorno da API: ";
        print_r(json_decode($response));
        echo "</pre><hr>";
    }
    if ($httpcode == 200) {
        return json_decode($response);
    }
    else {
        return 0;

    }


}

function dadosJson($credentials) {
    global $DB_host, $DB_user, $DB_pass, $DB_name, $total_amount;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

    $dadosJson = '{
        "merchantId": "' . $credentials['MerchantID'] . '",
    "merchantSiteId": "' . $credentials['MerchantSiteIDD'] . '",
    "customer": {
            "customerRef": "' . $credentials['ref'] . '"
        
    },
    "orderData": {
        "amount": "' . intval($_SESSION['total_amount'] * 100) . '",
        "orderSummaryRef": "' . $credentials['ref'] . '",
        "orderRef":  "' . $credentials['ref'] . '",
        "orderDate": "' . date("Y-m-d\TH:i:s.000\Z", strtotime(date('Y-m-d H:i:s'))) . '",
        "scoringToken": "' . $credentials['scoringToken'] . '",
        "freeText": "everythingYouWant"
    },
    "configuration": {
            "culture": "pt-PT",
        "template": "Generic",
        
        "merchantBackUrl": "https://www.dotbright.pt/p5-registo-utilizador-pt?id_enc='.$credentials['enc_id'].'&show_compras=1",
        "merchantNotifyUrl": "https://www.dotbright.pt/external_api/pagamentos/pgm_floa/floa.php?id_enc='.$credentials['enc_id'].'&p=1",
        "merchantReturnUrl": "https://www.dotbright.pt/external_api/pagamentos/pgm_floa/floa.php?id_enc='.$credentials['enc_id'].'&ret=3",
        "paymentOptionRef": "' . $credentials['pay_optionref'] . '"
    }
}
';
unset($_SESSION['total_amount']);
    $sql = "UPDATE floa_logs SET Json = '" . $dadosJson . "' WHERE ID=" . $credentials['id_log'];
   //die($sql);
    $res = Runquery($db_link, $DB_name, $sql);

    return $dadosJson;
}

function getpaymentlink($credentials) {


    $url = $credentials['url_pay_Session']; // "{$url_com}/v1/payment-sessions";
    $jsondata = dadosJson($credentials);

//die($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'authToken: ' . $credentials['token'],
            'Content-Type: application/json',
            'accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $retorno = json_decode($response, true);


    if ($_GET['debug'] == 1) {
        echo "<pre>Debug Floa function getpaymentlink </pre>";
        echo "linha: " . __LINE__ . "<hr>";
        echo "<pre>url: " . $url . "<hr></pre>";
        echo "<pre>Dados Json getpaymentlink</pre>";
        echo "<pre>{$jsondata}<hr></pre>";

        echo "<pre>authorization: " . $credentials['token'] . "<hr></pre>";
        echo "<pre><hr>Httpcode: {$httpcode}</pre>";
        echo "<pre>Retorno da API: ";
        print_r($retorno);
        echo "</pre><hr>";
    }

    if ($httpcode == 200) {
        return $retorno;
    }
    else {
        return 0;

    }

}

function rem_caract_esp($string)
{

    // matriz de entrada
    $what = array(
            'ä',
            'ã',
            'à',
            'á',
            'â',
            'ê',
            'ë',
            'è',
            'é',
            'ï',
            'ì',
            'í',
            'ö',
            'õ',
            'ò',
            'ó',
            'ô',
            'ü',
            'ù',
            'ú',
            'û',
            'À',
            'Á',
            'É',
            'Í',
            'Ó',
            'Ú',
            'ñ',
            'Ñ',
            'ç',
            'Ç',
            ' ',
            '-',
            '(',
            ')',
            ',',
            ';',
            ':',
            '|',
            '!',
            '"',
            '#',
            '$',
            '%',
            '&',
            '/',
            '=',
            '?',
            '~',
            '^',
            '>',
            '<',
            'ª',
            'º'
    );

    // matriz de saída
    $by = array(
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'A',
            'A',
            'E',
            'I',
            'O',
            'U',
            'n',
            'n',
            'c',
            'C',
            ' ',
            ' ',
            '',
            '',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_',
            '_'
    );

    // devolver a string
    return str_replace($what, $by, $string);
}




$res = autentication_floa($credentials);
if ($res === 0) {
    echo "Erro na autenticação";
    die();

}
else {
    $ret['token'] = $res;
    $credentials = array_merge($credentials, $ret);
    //print_r($credentials);
}

$Eligibility = getEligibility($credentials);
if ($Eligibility == 0) {
    echo "Erro na Eleigibility";
    die();

}
else{
    $dados_ret = ($Eligibility);
    if(isset($dados_ret->eligibilities[0]->errors) && count($dados_ret->eligibilities[0]->errors) > 0){
        echo "Erro na Eleigibility".count($dados_ret->eligibilities[0]->errors == '');
        printr($dados_ret->eligibilities[0]->errors);
        $_SESSION['buca_erros'] = $dados_ret->eligibilities[0]->errors;
        if($_GET['debug']==1){}else {
            header("location:https://www.dotbright.pt/p5-registo-utilizador-pt?id_enc={$enc_id}&show_compras=1");
        }
        die();
    }

    $retorno['scoringToken'] = $dados_ret->eligibilities[0]->token;
    $retorno['url_pay_Session'] = $dados_ret->eligibilities[0]->links[0]->href;
    $credentials = array_merge($credentials, $retorno);

}

$paymentlink = getpaymentlink($credentials);

if ($paymentlink == 0) {
    echo "Erro na getpaymentlink";
    die();
}
else {

    $dados_ret = $paymentlink;
    //print_r($dados_ret);
    if ($dados_ret['operationSucceeded'] != 1 ) {
        die($dados_ret['responseMessage']);
    }else{
        $url = $dados_ret['paymentSessionUrl'];
        header("location:" . $url);
        exit;
    }

    //$credentials = array_merge($credentials, $retorno);

    }

