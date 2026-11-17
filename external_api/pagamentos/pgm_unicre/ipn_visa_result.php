<?php
//**************************************************
//Recebe os dados sempre que há um pagamento por VISA bem sucedido
//**************************************************


die();//mudar para os dados finais do cliente




ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(0);

$fp = fopen('php://input', 'r');
$http_body = stream_get_contents($fp);
$raw_post = serialize($_POST);
$get = serialize($_GET);
fclose($fp);
file_put_contents('logs/sibs_visa0_'.date('Y_m_d_h_i_s').'.txt','&BODY='.$http_body.'&POST='.$raw_post.'&GET='.$get);

$transactionID=$_GET["id"];


$sandbox=0;
if($sandbox==0)
{
    //Producao
    $request_url = 'https://api.sibspayments.com/sibs/spg/v1/payments/'.$transactionID.'/status';
    $authentication_entityId = '43e7b5d9-b84c-4bf9-b070-0dba4bb062e6';
    $Entidade = '27763';
    $Terminal_ID = '1463063';       
    $Bearer ='0274242b9ceb174ebfbd17df8398bc72da.eyJlIjoiMTk4MzE5OTczMDEzOSIsInJvbGVzIjoiU1BHX01BTkFHRVIiLCJ0b2tlbkFwcERhdGEiOiJ7XCJtY1wiOlwiNTA2OTY2XCIsXCJ0Y1wiOlwiMTMyNTYzNlwifSIsImkiOiIxNjY3NTgwNTMwMTM5IiwiaXMiOiJodHRwczovL3ByZC5zaXRlMS5zc28uc3lzLnNpYnMucHQvYXV0aC9yZWFsbXMvUFJELk1FUkNILlBPUlQiLCJ0eXAiOiJCZWFyZXIiLCJpZCI6IjY2VHVITWFKU1hlZjdmZDgwYjA4Yzk0YjA4YmQ4ZjJhNTBjMjRiNTY0MCJ9.7a6e052b56070705c6661c1e59f70f330a6b345b0a66ff06eee50072ffa29c892279b95537b12f79c0e0ab9345098d0bd377db6f4efb3d12bf45dad2e08dce28';
}
else
{
    //testes:
    $request_url = 'https://spg.qly.site1.sibs.pt/api/v1/payments/'.$transactionID.'/status';

    $authentication_entityId = '';
    $Entidade = '';
    $Terminal_ID = '';
    $Bearer ='';
}

$path =  dirname( __FILE__ ) . '/../../../core/';
include $path.'core_files.php';
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

$response = callAPI_GET($request_url,$Bearer,$authentication_entityId);
$res = json_decode($response,true);

file_put_contents('logs/sibs_visa01_'.date('Y_m_d_h_i_s').'.txt',serialize($res));

// Encomenda
$sql = 'SELECT * FROM pagamentos_unicre WHERE checkoutId="'.$transactionID.'"';
$rs = new DataObj;
$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
if(!$rs->EOF())
{
	$IDb=$rs->db_data['ID'];

    $ID_ENC=$rs->db_data['ID_ENC'];

    if($res['returnStatus']['statusMsg']=='Success' && $res['paymentStatus']=='Success')
    {
        $sql = 'UPDATE pagamentos_unicre set Estado = 13, UDate="'.date("Y-m-d H:m:i").'", Code="'.$res['statusMsg'].'" WHERE ID = '.$IDb;
        Runquery($db_link,$GLOBALS['DB_name'], $sql);
        $sql = 'UPDATE sibs_visa_logs set Estado = 13, Webhook = "'.addslashes(serialize($res)).'" WHERE ID_Pagamento = "'.$IDb.'"';
        Runquery($db_link,$GLOBALS['DB_name'], $sql);
        $sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='22' WHERE ID='$ID_ENC'";
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
    }
    else
    {
        $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'", Code="'.$res['statusMsg'].'" WHERE ID = '.$IDb;
        Runquery($db_link,$GLOBALS['DB_name'], $sql);
        $sql = 'UPDATE sibs_visa_logs set Estado = 4, Webhook = "'.addslashes(serialize($res)).'" WHERE ID_Pagamento = "'.$IDb.'"';
        Runquery($db_link,$GLOBALS['DB_name'], $sql);

        $sql = "UPDATE encomendas SET Estado='14' WHERE ID='$ID_ENC'";
        $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
    }
}
$rs->db_close();

function callAPI_GET($url,$Bearer,$authentication_entityId)
{
   // Initialise cURL
  	$ch = curl_init($url);
  	$actualUrl = $url;
  	curl_setopt($ch, CURLOPT_URL, $actualUrl);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET');
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER,
	    array(
	        'Content-Type:application/json',
	        'X-IBM-Client-Id: '.$authentication_entityId,
	        'Authorization: Bearer '.$Bearer
	    )
	);
	$server_output = curl_exec($ch);
	curl_close ($ch);
	return $server_output;
}

?><!DOCTYPE html><html><body><script type="text/javascript">window.open("https://www.biominds.pt/p5-registo-utilizador-pt?show_compras=1&last=1","_self");</script></body></html>