<?php
header('Content-Type: text/html;charset=iso-8859-1');
ini_set('default_socket_timeout', 5000);
$path = dirname(__FILE__) . '/../';
include $path . 'core_files.php';
include 'nusoap-0.9.5/lib/nusoap.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

ini_set('max_execution_time', '60000');
ini_set('max_input_time', '60000');
ini_set('memory_limit', '-1');


$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$rs      = new DataObj();
$rs2     = new DataObj();

$fields      = db_list_fields($GLOBALS['DB_name'], 'encomendas', $db_link);
$columns     = db_num_fields($fields);
$field_array = array();
for ($i = 0;$i < $columns;$i++) {
    $field_array[] = db_field_name($fields, $i);
}
if (!in_array('CTT_RequestID', $field_array)) {
    $res = Runquery($db_link, $GLOBALS['DB_name'], 'ALTER TABLE encomendas ADD CTT_RequestID varchar(255) NOT NULL');
}
if (!in_array('CTT_FirstObjectCode', $field_array)) {
    $res = Runquery($db_link, $GLOBALS['DB_name'],
        'ALTER TABLE encomendas ADD CTT_FirstObjectCode varchar(255) NOT NULL');
}
if (!in_array('CTT_LastObjectCode', $field_array)) {
    $res = Runquery($db_link, $GLOBALS['DB_name'],
        'ALTER TABLE encomendas ADD CTT_LastObjectCode varchar(255) NOT NULL');
}
if (!in_array('CTT_SubProductCode', $field_array)) {
    $res = Runquery($db_link, $GLOBALS['DB_name'],
        'ALTER TABLE encomendas ADD CTT_SubProductCode varchar(255) NOT NULL');
}

$sand_box = 1;

if ($sand_box == 1) {
    $ctt_url               = 'https://portal.cttexpresso.pt/CTTExpressoWSQ/CTTShipmentProviderWS.svc?wsdl';
    $ctt_n_cliente         = 11302030;
    $ctt_n_contrato        = 300248721;
    $ctt_autenticathion_id = 'a66740b4-ded3-4359-9dfa-4ab1074fc277';
    $ctt_user_id           = 'acb5146f-dcdd-448a-ab5b-9e6ea377e048';
} else {
    // FALTA OS DADOS DE PRODUCAO
    $ctt_url               = 'https://portal.cttexpresso.pt/webservicecttexpresso/CTTShipmentProviderWS.svc?wsdl';
    $ctt_n_cliente         = 11302030;
    $ctt_n_contrato        = 300248721;
    $ctt_autenticathion_id = 'a66740b4-ded3-4359-9dfa-4ab1074fc277';
    $ctt_user_id           = 'acb5146f-dcdd-448a-ab5b-9e6ea377e048';
}


// pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
$RequestID = generateCode(4, 4) . '-' . generateCode(2, 2) . '-' . generateCode(2, 2) . '-' . generateCode(2,
        2) . '-' . generateCode(6, 6);
$RequestID = verify_generateCode($RequestID);

$sql="Select ID from encomendas where importado_phc > 0 AND fatura_sage <>''";

        $rs->db_open($db_link,$DB_name,$sql);

        while(!$rs->EOF())
        {

        $id_enc = $rs->db_data['ID'];
        // Informação do destinatário
        $dados_enc = get_encomenda_dados($id_enc);

        $SenderData   = array(
            'Type'        => 'Sender',
            // 1-Endereço do remetente | 2-Endereço do destinatário | 3-Endereço de devolução | 4-Endereço de envio caso entrega no endereço original falhe.
            'Name'        => truncate_s($dados_enc['nome'] . ' ' . $dados_enc['apelido'], 60),
            // 60 varchar
            'ContactName' => truncate_s($dados_enc['nome'] . ' ' . $dados_enc['apelido'], 60),
            // 60 varchar
            'Address'     => truncate_s($dados_enc['morada'], 100),
            // 100 varchar
            'City'        => truncate_s($dados_enc['cidade'], 50),
            // 50 varchar
            'PTZipCode4'  => '4470',
            // TODO
            'PTZipCode3'  => '005',
            // TODO
            'Country'     => truncate_s($dados_enc['pais'], 3),
            // 3 char
            'Email'       => truncate_s($dados_enc['email'], 200),
            // 200 varchar
            'Phone'       => truncate_s($dados_enc['telefone'], 20),
            // 20 varchar
            // 'MobilePhone' => truncate_s($dados_enc['telefone'], 20), // 20 varchar
        );
        $ReceiverData = array(
            'Type'        => 'Receiver',
            // 1-Endereço do remetente | 2-Endereço do destinatário | 3-Endereço de devolução | 4-Endereço de envio caso entrega no endereço original falhe.
            'Name'        => truncate_s($dados_enc['nome'] . ' ' . $dados_enc['apelido'], 60),
            // 60 varchar
            'ContactName' => truncate_s($dados_enc['nome'] . ' ' . $dados_enc['apelido'], 60),
            // 60 varchar
            'Address'     => truncate_s($dados_enc['morada'], 100),
            // 100 varchar
            'City'        => truncate_s($dados_enc['cidade'], 50),
            // 50 varchar
            'PTZipCode4'  => '4470',
            // TODO
            'PTZipCode3'  => '005',
            // TODO
            'Country'     => truncate_s($dados_enc['pais'], 3),
            // 3 char
            'Email'       => truncate_s($dados_enc['email'], 200),
            // 200 varchar
            'Phone'       => truncate_s($dados_enc['telefone'], 20),
            // 20 varchar
            // 'MobilePhone' => truncate_s($dados_enc['telefone'], 20), // 20 varchar
        );
        // Dados da remessa
        $ShipmentData = array(
            'IsDevolution'    => 'false',
            /* Informa se o envio a ser criado é uma devolução */
            'Weight'          => (ceil($dados_enc['peso_prods']) > 0 ? ceil($dados_enc['peso_prods']) : 1),
            // NÃO EXISTEM PESOS TEVE QUE SE FORÇAR 1kg PARA TESTAR
            'Quantity'        => 1,
            /* Quantidade de objectos */
            // duvida $dados_enc['qtd_prods'],
            'ClientReference' => $id_enc
            /* Referência do Cliente */
            // VER QUE CAMPO É ESTE(Referencia do cliente)
        );

        // Dados da remessa
        $ShipmentCTT = array(
            'HasSenderInformation' => 'false',
            'ReceiverData'         => $ReceiverData,
            'SenderData'           => $SenderData,
            'ShipmentData'         => $ShipmentData
        );

        // Dados da expedição
        //
        $DeliveryNote = array(
            'ClientId'              => $ctt_n_cliente, /* Número do cliente "MAKENOTES" */
            'ContractId'            => $ctt_n_contrato, /* Número de contrato do cliente "MAKENOTES" */
            'DistributionChannelId' => 99, /* Código de canal de distribuição (99 - E M S) */
            'SubProductId'          => 'ENCF005.01', /* codigo do produto */
            'ShipmentCTT'           => array('ShipmentCTT' => $ShipmentCTT)
        );

        $parameters = array(
            'url'      => $ctt_url,
            'ClientId' => $ctt_n_cliente,
            "Input"    => array(
                "AuthenticationID" => $ctt_autenticathion_id,
                "RequestID"        => $RequestID,
                "UserId"           => $ctt_user_id,
                "DeliveryNote"     => $DeliveryNote
            )
        );

        $result = callCTTCreateShipment($parameters);
        if ($result['error'] != '') {
            echo '<pre>';
            echo $result['error'];
            echo '</pre>';
        } else {

            echo '<pre>' . print_r($result['data'], true) . '</pre>';



            $url = 'http://192.168.1.202/lojas/makenotes.pt_2018/zebra/testeZebra.php';

            //TODO FALTA VALIDAR + que uma label e se existe label!
            $data = array('label' => $result['data']['ShipmentDataOutput']['LabelList']['LabelData']['Label']);

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) { /* Handle error */ }

            var_dump($result);



            //$label_content = $result['data']['CreateShipmentResult']['ShipmentData']['ShipmentDataOutput']['LabelList']['LabelData']['Label'];//CA1
            //$myFile        = dirname(__FILE__) . "/../../files/importar/cs_enc83_" . date('YmdHis') . ".label";

        }
    $rs->db_next();
}
$rs->db_close();
exit;


//echo '$values = $client->CompleteShipment($parameters);<br>';


/*echo "====== REQUEST HEADERS =====" . PHP_EOL;
var_dump($client->__getLastRequestHeaders());
echo "========= REQUEST ==========" . PHP_EOL;
var_dump($client->__getLastRequest());
echo "========= RESPONSE =========" . PHP_EOL;
var_dump($values);*/


/*echo '<pre>';
print_r(json_encode($values));
echo '</pre>';*/

//print_r($values);
$myFile = dirname(__FILE__) . "/../../files/importar/cs_enc83_" . date('YmdHis') . ".php";
$fh2    = fopen($myFile, 'w');
fwrite($fh2, print_r($array, true));
fclose($fh2);


//vamos ler o pdf para uma variavel
$ClientReference = $array['CreateShipmentResult']['ShipmentData']['ShipmentDataOutput']['ClientReference'];//GuiaTransportA4.pdf


// $pdf_content=$array[CreateShipmentResult][ShipmentData][ShipmentDataOutput][DocumentsList][DocumentData][0][File];//GuiaTransportA4.pdf
// $myFile = dirname( __FILE__ )."/../../files/importar/cs_enc83_".date('YmdHis').".pdf";
// $fh4 = fopen($myFile,'w');
// fwrite($fh4, $pdf_content);
// fclose($fh4);

// $pdf_content=$array[CreateShipmentResult][ShipmentData][ShipmentDataOutput][DocumentsList][DocumentData][1][File];//CA1
// $myFile = dirname( __FILE__ )."/../../files/importar/cs_enc83_".date('YmdHis')."_1.pdf";
// $fh4 = fopen($myFile,'w');
// fwrite($fh4, $pdf_content);
// fclose($fh4);


// $pdf_content=$array[CreateShipmentResult][ShipmentData][ShipmentDataOutput][DocumentsList][DocumentData][2][File];//CA1
// $myFile = dirname( __FILE__ )."/../../files/importar/cs_enc83_".date('YmdHis')."_2.pdf";
// $fh4 = fopen($myFile,'w');
// fwrite($fh4, $pdf_content);
// fclose($fh4);


$label_content = $array['CreateShipmentResult']['ShipmentData']['ShipmentDataOutput']['LabelList']['LabelData']['Label'];//CA1
$myFile        = dirname(__FILE__) . "/../../files/importar/cs_enc83_" . date('YmdHis') . ".label";

echo '<pre>';
print_r($label_content);
echo '</pre>';


$fh4 = fopen($myFile, 'w');
fwrite($fh4, $label_content);
fclose($fh4);


// FECHAR O ENVIO
// Vai se ter que gravar esta informação por encoemnda
$FirstObject = $array['CreateShipmentResult']['ShipmentData']['ShipmentDataOutput']['FirstObject'];//CA1
$LastObject  = $array['CreateShipmentResult']['ShipmentData']['ShipmentDataOutput']['LastObject'];//CA1


$parameters_close = array(
    "Input" => array(
        "AuthenticationID"      => $ctt_autenticathion_id,
        "RequestID"             => $RequestID,
        "UserId"                => $ctt_user_id,
        'ClientId'              => $ctt_n_cliente,
        'ContractId'            => $ctt_n_contrato,
        "DistributionChannel"   => 99,
        "ListOfShipmentToClose" => array(
            "Shipment2Close" => array(
                'FirstObjectCode' => $FirstObject,
                'LastObjectCode'  => $LastObject,
                'SubProductCode'  => 'ENCF005.01'
            )
        )
    )
);


$client = new SoapClient($ctt_url, array(
    'trace'              => true,
    'connection_timeout' => 5000,
    'cache_wsdl'         => WSDL_CACHE_NONE,
    'keep_alive'         => true,
));
$values = $client->CloseShipment($parameters_close);


/*echo '<pre>';
print_r(json_encode($values));
echo '</pre>';*/


$pdf_content = $array['CloseShipmentResult']['DocumentsList']['DocumentData'][0]['File'];//GuiaTransportA4.pdf
$myFile      = dirname(__FILE__) . "/../../files/importar/FINAL_cs_enc83_1_" . date('YmdHis') . ".pdf";
$fh4         = fopen($myFile, 'w');
fwrite($fh4, $pdf_content);
fclose($fh4);

$pdf_content = $array['CloseShipmentResult']['DocumentsList']['DocumentData'][1]['File'];//GuiaTransportA4.pdf
$myFile      = dirname(__FILE__) . "/../../files/importar/FINAL_cs_enc83_2_" . date('YmdHis') . ".pdf";
$fh4         = fopen($myFile, 'w');
fwrite($fh4, $pdf_content);
fclose($fh4);

$pdf_content = $array['CloseShipmentResult']['DocumentsList']['DocumentData'][3]['File'];//GuiaTransportA4.pdf
$myFile      = dirname(__FILE__) . "/../../files/importar/FINAL_cs_enc83_3_" . date('YmdHis') . ".pdf";
$fh4         = fopen($myFile, 'w');
fwrite($fh4, $pdf_content);
fclose($fh4);


$myFile = dirname(__FILE__) . "/../../files/importar/FINAL_CLOSE_cs_enc83_" . date('YmdHis') . ".php";
$fh2    = fopen($myFile, 'w');
fwrite($fh2, print_r($array, true));
fclose($fh2);

print_r($values);
// $myFile = dirname( __FILE__ )."/../../files/importar/cs_enc83_".date('YmdHis').".tmp";
// $fh3 = fopen($myFile,'w');
// fwrite($fh3, $values);
// fclose($fh3);


//echo '<pre>';
//print_r($parameters);
//echo '</pre>';

// echo '<pre>';
// print_r($values);


die();


// $myFile = dirname( __FILE__ )."/../../files/importar/enc83.txt";
// echo '1';
$myFile = $site_http_url . "files/importar/enc83.txt";

$file = file_get_contents($myFile);
// $array = json_decode(json_encode($file),false);
// $array = object_2_array($file);
// echo '2';
print_r($array);
echo '2';
// $fh2 = fopen($myFile, 'w');
// fwrite($fh2, print_r($values,true)."\n");
// fclose($fh2);
// echo '</pre>';
// $values = $client->"CTTShipmentProviderWS" => $ctt_autenticathion_id($parameters);

/*		$array = json_decode(json_encode($values), true);


$sql="SELECT * FROM encomendas WHERE Data_expedicao BETWEEN '$ini' AND '$fim' AND (Estado = 7 OR Estado = 16)";
$dados->db_open($db_link,$GLOBALS['DB_name'],$sql);
if(!$dados->EOF())
{
while(!$dados->EOF())
{
    $first = ltrim(rtrim($dados->db_data['enc_Nome']));
    $last = ltrim(rtrim($dados->db_data['enc_Apelido']));

    if($dados->db_data['enc_Email'])
    {
        $sql = "SELECT * FROM encomendas_produtos WHERE ID_PAI=".$dados->db_data['ID'].' AND Tipo=1';
        $dados2->db_open($db_link,$GLOBALS['DB_name'],$sql);
        while(!$dados2->EOF())
        {
            $csv .= $dados->db_data['enc_Email'].";".$dados->db_data['ID'].";".$dados->db_data['Data_Criacao'].";".$last.";".$first.";".$dados2->db_data['Ref'].";".$dados2->db_data['Nome']."\r\n";




            $dados2->db_next();
        }
        $dados2->db_close();
    }
    $dados->db_next();
}
}
$dados->db_close();
*/


function generateCode($numchars = 5, $digits = 1)
{
    $ret = substr(str_shuffle('ABCDEFabcdef'), 0, $digits);
    $ret .= substr(str_shuffle('0123456789'), 0, $numchars);

    return $ret;
}

function verify_generateCode($chave_secreta = '')
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

    $dados2 = new DataObj();
    $sql    = "SELECT ID FROM encomendas WHERE CTT_RequestID='$chave_secreta'";
    $dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$dados2->EOF()) {
        $RequestID     = generateCode(4, 4) . '-' . generateCode(2, 2) . '-' . generateCode(2,
                2) . '-' . generateCode(2, 2) . '-' . generateCode(6, 6);
        $chave_secreta = verify_generateCode($RequestID);
    }
    $dados2->db_close();

    return $chave_secreta;
}

function get_encomenda_dados($enc_id = 0)
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

    $dados2 = new DataObj();
    $return = array();
    $sql    = "SELECT ID_Cliente, enc_Nome_ent, enc_Apelido_ent, enc_Morada_ent, enc_Cidade_ent, enc_Codigo_Postal_ent, enc_Telefone_ent, enc_Pais_ent, enc_Email FROM encomendas WHERE ID='$enc_id'";
    $dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$dados2->EOF()) {
        $return = array(
            'ID_Cliente'    => $dados2->db_data['ID_Cliente'],
            'nome'          => $dados2->db_data['enc_Nome_ent'],
            'apelido'       => $dados2->db_data['enc_Apelido_ent'],
            'morada'        => $dados2->db_data['enc_Morada_ent'],
            'cidade'        => $dados2->db_data['enc_Cidade_ent'],
            'codigo_postal' => $dados2->db_data['enc_Codigo_Postal_ent'],
            'telefone'      => '229862544'/*$dados2->db_data['enc_Telefone_ent']*/,
            'pais'          => ($dados2->db_data['enc_Pais_ent'] == 165 ? 'PT' : 'PT'),
            'email'         => $dados2->db_data['enc_Email'],
            'qtd_prods'     => get_encomenda_produtos($enc_id),
            'peso_prods'    => get_encomenda_peso($enc_id)
        );
    }
    $dados2->db_close();

    return $return;
}

function get_encomenda_produtos($enc_id = 0)
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

    $dados2 = new DataObj();
    $return = array();
    $sql    = "SELECT sum(Qtd) AS QTD_PRODS FROM encomendas_produtos WHERE ID_PAI='$enc_id'";
    $dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$dados2->EOF()) {
        $return = $dados2->db_data['QTD_PRODS'];
    }
    $dados2->db_close();

    return $return;
}

function get_encomenda_peso($enc_id = 0)
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

    $dados2 = new DataObj();
    $return = array();
    $sql    = "SELECT sum(Peso) AS Peso_PRODS FROM encomendas_produtos ep INNER JOIN products p ON p.ID = ep.ID_Produto WHERE ID_PAI='$enc_id'";
    $dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$dados2->EOF()) {
        $return = $dados2->db_data['Peso_PRODS'];
    }
    $dados2->db_close();

    return $return;
}

function truncate_s($str = '', $limit = 0)
{
    if ($limit == 0) {
        $return = $str;
    }    // ILIMITADO
    else {
        $return = mb_strimwidth($str, 0, $limit, '', 'utf-8');
    }

    return $return;
}

function callCTTCloseShipment($params)
{
    $result = array(
        'error' => '',
        'data'  => array()
    );

    $soapHeader = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:ctt="http://schemas.datacontract.org/2004/07/CTTExpressoWS"><soapenv:Header/><soapenv:Body><tem:CloseShipment>';
    $soapFooter = '</tem:CloseShipment></soapenv:Body></soapenv:Envelope>';

    $soapstr = '';
    if (isset($params['Input'])) {
        // Input
        $input = $params['Input'];

        $soapstr .= '<tem:Input>';
        $soapstr .= '<ctt:AuthenticationID>' . $input['AuthenticationID'] . '</ctt:AuthenticationID>';
        $soapstr .= '<ctt:ClientId>' . $input['ClientId'] . '</ctt:ClientId>';
        $soapstr .= '<ctt:ContractId>' . $input['ContractId'] . '</ctt:ContractId>';
        $soapstr .= '<ctt:DistributionChannel>' . $input['DistributionChannel'] . '</ctt:DistributionChannel>';

        if (isset($input['ListOfShipmentToClose'])) {
            // ListOfShipmentToClose
            $listOfShipmentToClose = $input['ListOfShipmentToClose'];

            $soapstr .= '<ctt:ListOfShipmentToClose>';

            if (count($listOfShipmentToClose) > 0) {
                foreach ($listOfShipmentToClose as $shipment2Close) {
                    // Shipment2Close
                    $soapstr .= '<ctt:Shipment2Close>';
                    $soapstr .= '<ctt:FirstObjectCode>' . $shipment2Close['FirstObjectCode'] . '</ctt:FirstObjectCode>';
                    $soapstr .= '<ctt:LastObjectCode>' . $shipment2Close['LastObjectCode'] . '</ctt:LastObjectCode>';
                    $soapstr .= '<ctt:SubProductCode>' . $shipment2Close['SubProductCode'] . '</ctt:SubProductCode>';
                    $soapstr .= '</ctt:Shipment2Close>';
                }
            }

            $soapstr .= '</ctt:ListOfShipmentToClose>';
        }

        $soapstr .= '<ctt:RequestID>' . $input['RequestID'] . '</ctt:RequestID>';
        $soapstr .= (isset($senderData['UserID'])) ? '<ctt:UserID>' . $input['UserID'] . '</ctt:UserID>' : '';
        $soapstr .= '</tem:Input>';
    }

    if ($soapstr == '') {
        echo 'Não foram passados parametros';
        exit;
    } else {
        $soapstr = $soapHeader . $soapstr . $soapFooter;

        $client = new nusoap_client($params['url'], false, false, false, false, false, 50, 50);
        $err    = $client->getError();
        if ($err) {
            $result['error'] = $err;

            return $result;
        }
        $client->setUseCurl(0);
        $client->useHTTPPersistentConnection();
        $soapResult = $client->send($soapstr, 'http://tempuri.org/ICTTShipmentProviderWS/CloseShipment');
        if ($client->fault) {
            $result['error'] = print_r($soapResult, true);
        } else {
            $err = $client->getError();
            if ($err) {
                $result['error'] = $err;
            } else {
                if (isset($soapResult['CloseShipmentResult']['Status']) && $soapResult['CreateShipmentResult']['Status'] == 'Success') {
                    $result['data'] = $soapResult['CloseShipmentResult']['DocumentsList'];
                } else {
                    $result['error'] = print_r($soapResult['CloseShipmentResult']['ErrorsList'], true);
                }
            }
        }
    }

    return $result;
}

function callCTTCreateShipment($params)
{
    $result = array(
        'error' => '',
        'data'  => array()
    );

    $soapHeader = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:ctt="http://schemas.datacontract.org/2004/07/CTTExpressoWS" xmlns:ctt1="http://schemas.datacontract.org/2004/07/CTTExpressoWS.Models.ShipmentProvider" xmlns:ctt2="http://schemas.datacontract.org/2004/07/CTTExpressoWS.Models.ShipmentProvider.SEPs"><soapenv:Header/><soapenv:Body><tem:CreateShipment>';
    $soapFooter = '</tem:CreateShipment></soapenv:Body></soapenv:Envelope>';

    $soapstr = '';
    if (isset($params['Input'])) {
        // Input
        $input = $params['Input'];

        $soapstr .= '<tem:Input>';
        $soapstr .= '<ctt:AuthenticationID>' . $input['AuthenticationID'] . '</ctt:AuthenticationID>';

        if (isset($input['DeliveryNote'])) {
            // DeliveryNote
            $deliveryNote = $input['DeliveryNote'];

            $soapstr .= '<ctt:DeliveryNote>';
            $soapstr .= '<ctt1:ClientId>' . $deliveryNote['ClientId'] . '</ctt1:ClientId>';
            $soapstr .= '<ctt1:ContractId>' . $deliveryNote['ContractId'] . '</ctt1:ContractId>';
            $soapstr .= '<ctt1:DistributionChannelId>' . $deliveryNote['DistributionChannelId'] . '</ctt1:DistributionChannelId>';
            $soapstr .= (isset($deliveryNote['ExtData'])) ? '<ctt1:ExtData>' . $deliveryNote['ExtData'] . '</ctt1:ExtData>' : '';

            if (isset($deliveryNote['ShipmentCTT'])) {
                // ShipmentCTT
                $shipmentCtt = $deliveryNote['ShipmentCTT'];

                $soapstr .= '<ctt1:ShipmentCTT>';

                if (count($shipmentCtt) > 0) {
                    foreach ($shipmentCtt as $shipmentCttItem) {
                        // ShipmentCTT
                        $soapstr .= '<ctt1:ShipmentCTT>';
                        $soapstr .= '<ctt1:HasSenderInformation>' . $shipmentCttItem['HasSenderInformation'] . '</ctt1:HasSenderInformation>';

                        if (isset($shipmentCttItem['ReceiverData'])) {
                            // ReceiverData
                            $receiverData = $shipmentCttItem['ReceiverData'];

                            $soapstr .= '<ctt1:ReceiverData>';
                            $soapstr .= '<ctt1:Address>' . $receiverData['Address'] . '</ctt1:Address>';
                            $soapstr .= '<ctt1:City>' . $receiverData['City'] . '</ctt1:City>';
                            $soapstr .= (isset($receiverData['ContactName'])) ? '<ctt1:ContactName>' . $receiverData['ContactName'] . '</ctt1:ContactName>' : '';
                            $soapstr .= '<ctt1:Country>' . $receiverData['Country'] . '</ctt1:Country>';
                            $soapstr .= (isset($receiverData['Door'])) ? '<ctt1:Door>' . $receiverData['Door'] . '</ctt1:Door>' : '';
                            $soapstr .= (isset($receiverData['Email'])) ? '<ctt1:Email>' . $receiverData['Email'] . '</ctt1:Email>' : '';
                            $soapstr .= (isset($receiverData['Floor'])) ? '<ctt1:Floor>' . $receiverData['Floor'] . '</ctt1:Floor>' : '';
                            $soapstr .= (isset($receiverData['MobilePhone'])) ? '<ctt1:MobilePhone>' . $receiverData['MobilePhone'] . '</ctt1:MobilePhone>' : '';
                            $soapstr .= '<ctt1:Name>' . $receiverData['Name'] . '</ctt1:Name>';
                            $soapstr .= (isset($receiverData['NonPTZipCode'])) ? '<ctt1:NonPTZipCode>' . $receiverData['NonPTZipCode'] . '</ctt1:NonPTZipCode>' : '';
                            $soapstr .= (isset($receiverData['NonPTZipCodeLocation'])) ? '<ctt1:NonPTZipCodeLocation>' . $receiverData['NonPTZipCodeLocation'] . '</ctt1:NonPTZipCodeLocation>' : '';
                            $soapstr .= (isset($receiverData['PTZipCode3'])) ? '<ctt1:PTZipCode3>' . $receiverData['PTZipCode3'] . '</ctt1:PTZipCode3>' : '';
                            $soapstr .= (isset($receiverData['PTZipCode4'])) ? '<ctt1:PTZipCode4>' . $receiverData['PTZipCode4'] . '</ctt1:PTZipCode4>' : '';
                            $soapstr .= (isset($receiverData['PTZipCodeLocation'])) ? '<ctt1:PTZipCodeLocation>' . $receiverData['PTZipCodeLocation'] . '</ctt1:PTZipCodeLocation>' : '';
                            $soapstr .= (isset($receiverData['Phone'])) ? '<ctt1:Phone>' . $receiverData['Phone'] . '</ctt1:Phone>' : '';
                            $soapstr .= '<ctt1:Type>' . $receiverData['Type'] . '</ctt1:Type>';
                            $soapstr .= '</ctt1:ReceiverData>';
                        }

                        if (isset($shipmentCttItem['SenderData'])) {
                            // SenderData
                            $senderData = $shipmentCttItem['SenderData'];

                            $soapstr .= '<ctt1:SenderData>';
                            $soapstr .= '<ctt1:Address>' . $senderData['Address'] . '</ctt1:Address>';
                            $soapstr .= '<ctt1:City>' . $senderData['City'] . '</ctt1:City>';
                            $soapstr .= (isset($senderData['ContactName'])) ? '<ctt1:ContactName>' . $senderData['ContactName'] . '</ctt1:ContactName>' : '';
                            $soapstr .= '<ctt1:Country>' . $senderData['Country'] . '</ctt1:Country>';
                            $soapstr .= (isset($senderData['Door'])) ? '<ctt1:Door>' . $senderData['Door'] . '</ctt1:Door>' : '';
                            $soapstr .= (isset($senderData['Email'])) ? '<ctt1:Email>' . $senderData['Email'] . '</ctt1:Email>' : '';
                            $soapstr .= (isset($senderData['Floor'])) ? '<ctt1:Floor>' . $senderData['Floor'] . '</ctt1:Floor>' : '';
                            $soapstr .= (isset($senderData['MobilePhone'])) ? '<ctt1:MobilePhone>' . $senderData['MobilePhone'] . '</ctt1:MobilePhone>' : '';
                            $soapstr .= '<ctt1:Name>' . $senderData['Name'] . '</ctt1:Name>';
                            $soapstr .= (isset($senderData['NonPTZipCode'])) ? '<ctt1:NonPTZipCode>' . $senderData['NonPTZipCode'] . '</ctt1:NonPTZipCode>' : '';
                            $soapstr .= (isset($senderData['NonPTZipCodeLocation'])) ? '<ctt1:NonPTZipCodeLocation>' . $senderData['NonPTZipCodeLocation'] . '</ctt1:NonPTZipCodeLocation>' : '';
                            $soapstr .= (isset($senderData['PTZipCode3'])) ? '<ctt1:PTZipCode3>' . $senderData['PTZipCode3'] . '</ctt1:PTZipCode3>' : '';
                            $soapstr .= (isset($senderData['PTZipCode4'])) ? '<ctt1:PTZipCode4>' . $senderData['PTZipCode4'] . '</ctt1:PTZipCode4>' : '';
                            $soapstr .= (isset($senderData['PTZipCodeLocation'])) ? '<ctt1:PTZipCodeLocation>' . $senderData['PTZipCodeLocation'] . '</ctt1:PTZipCodeLocation>' : '';
                            $soapstr .= (isset($senderData['Phone'])) ? '<ctt1:Phone>' . $senderData['Phone'] . '</ctt1:Phone>' : '';
                            $soapstr .= '<ctt1:Type>' . $senderData['Type'] . '</ctt1:Type>';
                            $soapstr .= '</ctt1:SenderData>';
                        }

                        if (isset($shipmentCttItem['ShipmentData'])) {
                            // ShipmentData
                            $shipmentData = $shipmentCttItem['ShipmentData'];

                            $soapstr .= '<ctt1:ShipmentData>';
                            $soapstr .= '<ctt1:ClientReference>' . $shipmentData['ClientReference'] . '</ctt1:ClientReference>';
                            $soapstr .= '<ctt1:IsDevolution>' . $shipmentData['IsDevolution'] . '</ctt1:IsDevolution>';
                            $soapstr .= '<ctt1:Quantity>' . $shipmentData['Quantity'] . '</ctt1:Quantity>';
                            $soapstr .= '<ctt1:Weight>' . $shipmentData['Weight'] . '</ctt1:Weight>';
                            $soapstr .= '</ctt1:ShipmentData>';
                        }

                        $soapstr .= '</ctt1:ShipmentCTT>';
                    }
                }

                $soapstr .= '</ctt1:ShipmentCTT>';
            }

            $soapstr .= '<ctt1:SubProductId>' . $deliveryNote['SubProductId'] . '</ctt1:SubProductId>';
            $soapstr .= '</ctt:DeliveryNote>';
        }

        $soapstr .= '<ctt:RequestID>' . $input['RequestID'] . '</ctt:RequestID>';
        $soapstr .= '<ctt:UserID>' . $input['UserId'] . '</ctt:UserID>';
        $soapstr .= '</tem:Input>';
    }

    if ($soapstr == '') {
        echo 'Não foram passados parametros';
        exit;
    } else {
        $soapstr = $soapHeader . $soapstr . $soapFooter;

        $client = new nusoap_client($params['url'], false, false, false, false, false, 50, 50);
        $err    = $client->getError();
        if ($err) {
            $result['error'] = $err;

            return $result;
        }
        $client->setUseCurl(0);
        $client->useHTTPPersistentConnection();
        $soapResult = $client->send($soapstr, 'http://tempuri.org/ICTTShipmentProviderWS/CreateShipment');
        if ($client->fault) {
            $result['error'] = print_r($soapResult, true);
        } else {
            $err = $client->getError();
            if ($err) {
                $result['error'] = $err;
            } else {
                if (isset($soapResult['CreateShipmentResult']['Status']) && $soapResult['CreateShipmentResult']['Status'] == 'Success') {
                    $result['data'] = $soapResult['CreateShipmentResult']['ShipmentData'];
                } else {
                    $result['error'] = print_r($soapResult['CreateShipmentResult']['ErrorsList'], true);
                }
            }
        }
    }

    return $result;
}

function addCTTLogoToLabel($label)
{
    $logo = "~DGE:TLCTT.GRF,04180,011,000000000000000000000000000000000000001F00000000000000000000FFE0000000000000000001FFF0000000000000000003F3F800000000000000000380380000000000000000078038000000000000000007003800000000000000000700380000000000000000078038000000000000000003C078000000000000000003FFF0000000000000000001FFF00000000000000000007FC00000000000000000000000000000000000000000000000000000000000000003C00000000000000000030FF00000000000000000038FF80000000000000000070F780000000000000000070E380000000000000000070E380000000000000000071C380000000000000000039C38000000000000000003FC38000000000000000001FC3800000000000000000070300000000000000000000000000000000000000000000000000000000000000003C000000000000000000207F00000000000000000038FF80000000000000000070F780000000000000000070E380000000000000000070E380000000000000000071C380000000000000000071C38000000000000000003FC38000000000000000001FC38000000000000000000F878000000000000000000000000000000000000000000000000000000000000000000000000000000000000FC30000000000000000003FC38000000000000000003FC380000000000000000039C380000000000000000071C380000000000000000071C380000000000000000031C380000000000000000039C78000000000000000003DCF8000000000000000001FFF0000000000000000000FFC00000000000000000007F8000000000000000000000000000000000000000070000000000000000000007000000000000000000000780000000000000000000038000000000000000000003E000000000000000000007FFF8000000000000000003FFF8000000000000000003FFF8000000000000000000000000000000000000000000000000000000000000000C00000000000000000000FFC0000000000000000001FFF0000000000000000003FFF8000000000000000003803800000000000000000780380000000000000000078018000000000000000003801800000000000000000380380000000000000000038038000000000000000003FFFFE00000000000000003FFFFE00000000000000003FFFFE0000000000000000000000000000000000000000000000000000000000000000800000000000000000300380000000000000000038078000000000000000003E1F8000000000000000001FFE0000000000000000000FFC00000000000000000003F000000000000000000007F80000000000000000000FFC0000000000000000003F3F0000000000000003803C0F8000000000000007803807800000000000001F803000800000000000003F800000000000000000003F00000000000000000000F8000FC300000000000001E0003FC380000000000001C0003FC380000000F00003C00039C380000001F80007F00071C380000001F80007F00071C380000003F8000F780031C380000007E0000F1C0039C78000000FE0000F1C003DCF8000003FC0000F0E001FFF0000007FC0000F87000FFC0000007FC0000F870007F8000001FFC0000FC3800000000001FF800F8FC3800000000001FF801FDFC7800000000001FFFCFFFFE3800000000000FF7FFFFFE00030038000007FBFFFFFE00038038000007FE7FFFFC00038038000003FFFFFFF8003FFFF8000003FFFFFFF2003FFFF8001F00FFFFFFFF003FFFF8003F007FFFFFFF803FFFF0003E007FFFFFFF803FFFE0000E001FFFFFFF800380000006000FFFFFFF8000003800060003FFFFFFC0030038000100003FFFFFC00380380001FFE003FFFFC00380380001FFE001FFFFC03FFFF80001C3E001FFFF803FFFF8000180E001FFFF803FFFF8000080E001FFFF803FFFF00000C1C001FFFF00038000000043FF03FFFE00038000000043FFFFFFFE00000010000063FFFFFFFE000300780001F7FFFFFFFC000780780003FFFFFFFFFC000780380007FFFFFFFFF80007803C0007FFFFFFFFFC0007803C0007FFFFFFFFFC000780780003E3FF87FFFE0003FFF8000083FE007FFF8003FFF8000003FC007FFFC001FFF0000001E0007FFFF0007FC0000000F0003FFFF8000E00000000F0003FFFF800000000000078003FFFF80000000000003C003FFFFF0000000000003E003FFFFF8000000000001F001FFFFFE000000000000F803FFFFDF8000000000007C03FFFFCFC000000000001FC3FFFFC1F000000000000787BFFFE0F8000000000003E7FFFFE07800000000000043C00FF07C00000000000003F003F87C00000000000003F003FC7C00000000000001F800FF8000000000000001FE003FE000000000000000FF000FF0000000000000007F8001F8000000000000003FC00078000000000000003FC00038000000000000000FE0003C0000000000000007E0003E0000000000000007F0003E0000000000000003F0000E0000000000000001F000000000000000000001F000000000000000000001D000000000000000000001C000000000000000000001C00000000000000000000180000000000000000000018000000000000000000001800000000000000000000100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000700007FFFFF80000000000D80007FFFFF80000000000E80077FFFFF80000000000F801F7FFFFF80000000000F001F7E7FFF800000000000001F7E7FFF800000000000031F7E7FFF800000000003CF1F7E7FFF800000000007CF1F7E7FFF800000000007CF1F7E7FFF800000000007CF7F7E7F1F800000000007CFFF7E7E0F800000000007DFFF7E7C0F800000000007FFFF7E780F800000000007FFFF7E200F800000000007FFFF7E000F800000000007FFFF7E000F800000000007FFFC7E018F800000000007FFE07E03CF800000000007FF007F07CF800000000007F8007F0FCF800000000007C0007FFFCF800000000006000F7FFFCF800000000000007F7FFFCF80000000000003FF7FFFCF8000000000001FFF7FFFCF800000000000FFFF7E7FCF800000000003FFFF7E7FFF800000000007FFFF7E7FFF800000000007FFFF7E7FFF800000000007FFFE7E7FFF800000000007FFFC7E7FFF800000000007FFF87E7F1F800000000007FFF07E7E0F800000000007DFE37E7C0F8000000000063F9F7E780F8000000000007FFF7E300F800000000000FFFF7E000F800000000001FFFF7E000F800000000003FFFF7E018F800000000007FFFF7E03CF800000000007FFFF7E07CF800000000007FFFE7F0FCF800000000007FFF07FFFCF800000000007FFC07FFFCF800000000007FE007FFFCF800000000007F0007FFFCF80000000000780077FFFCF800000000000001F7FFFCF80000000000000FF7FFFFF80000000000007FF7E7FFF8000000000003FFF7E7FFF800000000001FFFF7E77FF800000000007FFFF7E71FF800000000007FFFF7E71FF800000000007FFFE7E71CF800000000007FFF07E71CF800000000007FF807E71CF800000000007FE077E71CF800000000007F01F7E71CF800000000007801F7E71CF800000000000001F7E71CF800000000000001F7E71CF800000000000001F7E71CF800000000000001F7E70CF800000000000001F7E700F800000000000003F7E700F80000000000001FF7F700F8000000000000FFF7FF00F8000000000007FFF7FF80F800000000003FFFF7FFE0F800000000007FFFF7FFF8F800000000007FFFF7FFFEF800000000007FFFC7FBFFF800000000007FFE07F1FFF800000000007FF007E0FFF800000000007F8017E0FFF800000000007C0037E07FF80000000000700077E061F800000000000000F7E000F8000000000003F1F7E000F8000000000007FFF7E200F800000000000FFFF7E700F800000000001FFFF7E700F800000000001FFFF7E70CF800000000003FFFE7E71FF800000000003FFFC7E71FF800000000007FFF87E71FF800000000007F7F07E71FF800000000007E3E77E703F800000000007C3FF7E700F80000000000783FF7E700F8000000000078FFF7FF00F800000000007FFFF7FF00F800000000007FFFF7FFC0F800000000007FFFF7FFF0F800000000007FFFF7FFFCF800000000007FFFC7FFFFF800000000007FFE07F1FFF800000000007FF007F0FFF800000000007FC007E07FF800000000007E0007E07FF80000000000700387E03FF80000000000001FC7E03FF80000000000007FE7E03FF8000000000003FFE7E23FF800000000001FFFE7E73FF800000000007FFFE7E71FF800000000007FFFF7E71FF800000000007FFFF7E71FF800000000007FFFF7E71FF800000000007FF0F7E71FF800000000007F80F7E71FF800000000007C01F7E707F800000000006001F7E701F80000000000000FE7E700F80000000000003FE7FF00F8000000000001FFE7FF00F800000000000FFFE7FF80F800000000007FFFC7FFF0F800000000007FFFC7FFFCF800000000007FFF87FFFFF800000000007FFF07FFFFF800000000007FFC07EFFFF800000000007FE007E7FFF800000000007F0007E7FFF80000000000781C07E3FFF80000000000207F07E1FFF8000000000001FF87E1FEF8000000000003FFC7F0F8F8000000000007FFC7F870F8000000000007FFE7F840F800000000000FFFE7FC00F800000000001FFFE7FC00F800000000001FFFF7F800F800000000003FFFF7F003F800000000003FC3F7E007F800000000003F01F7E007F800000000007E01F7E003F800000000007C03E7E0C3F800000000007803E7E3E1F800000000007807E7E7F0F80000000000781FE7FFF0F800000000007FFFC7FFF8F800000000007FFFC7FFFCF800000000007FFF87FFFCF800000000007FFF87FFFEF800000000003FFF07FFFEF800000000003FFF07FFFFF800000000001FFE07FFFFF800000000001FF807E7FFF8000000000007F007E7FFF80000000000018007E73FF80000000000000007E71FF800000000000C0007E71FF800000000007C0007E71CF800000000007C0037E71CF800000000007C01F7E71CF800000000007C0FF7E71CF800000000007C7FF7E71CF800000000007DFFF7E71CF800000000007FFFF7E71CF800000000007FFFF7E71CF800000000007FFFF7E71CF800000000007FFFE7E70CF800000000007FFF87E700F800000000007FFC07E700F800000000007FE007F700F800000000007F0007FF00F800000000007C0007FF00F800000000007C0007FF80F800000000007C0007FFF8F800000000007C0007FFFEF800000000007C0007FFFFF800000000007C0007FFFFF800000000007C0007FFFFF80000000000780007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF80000000000000007FFFFF800000";

    $vLabel = explode('^XA', $label);

    $label = '^XA' . $logo . $vLabel[1];

    return $label;
}