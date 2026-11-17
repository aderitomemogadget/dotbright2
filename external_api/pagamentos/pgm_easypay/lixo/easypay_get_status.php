<?php
$path = dirname(__FILE__) . '/../../core/';
include $path . 'core_files.php';
require_once('./api/Easypay.php');


error_reporting(E_ALL);
ini_set('display_errors', 0);

$rs_metodo = new DataObj();$rs = new DataObj();
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$sql = "SELECT Sandbox, Username, Account_id, Merchant_site_id, Username_sandbox, Password_sandbox FROM metodos_pagamento WHERE Tipo=8";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF()) 
{
	$user = $rs_metodo->db_data["Username"];
	echo $entity = $rs_metodo->db_data["Account_id"];
	$cin = $rs_metodo->db_data["Merchant_site_id"];
	$key = $rs_metodo->db_data["Username_sandbox"];
	$code = $rs_metodo->db_data["Password_sandbox"];	
	
	$sandbox = $rs_metodo->db_data['Sandbox'];
}
else exit;
$rs_metodo->db_close();


/*$id_enc = intval($_GET['id_enc']);
if($id_enc > 0){} else exit;
$sql_all = "SELECT TotalDocComIva, enc_Email FROM encomendas WHERE encomendas.ID='$id_enc'";
$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
if (!$rs->EOF()) 
{
	$valor = $rs->db_data['TotalDocComIva'];
	$enc_Email = $rs->db_data['enc_Email'];
}
*/


$easypay_params['user'] = $user; //'FIPA021116';
$easypay_params['cin'] = $cin; //'6273';
$easypay_params['entity'] = $entity; //'10611';
$easypay_params['name'] = 'Encomenda Nr: '.$id_enc;
$easypay_params['email'] = $enc_Email;
$easypay_params['value'] = $valor;
$easypay_params['key'] = $key; // '70b46a6c578f830c30132bda892975c2';
//Uncomment if you need to validate by code code
$easypay_params['code'] = $code; //'b039add9cc320213cae05aed8589525a';

/**
 * Create a new Easypay Object
 * @var Easypay
 */
$easypay = new Easypay($easypay_params);

//set mode to either test or production.
//This can be defined in the config file
if($sandbox == 1)$easypay->set_live(false);
else $easypay->set_live(true);


$ref='627300084';

$verification = $easypay->getTransactionVerification( $ref );
    
echo '<pre>';
var_dump($verification);
echo '</pre>';?>


<!doctype html>
<html>
    <head>
        <title>Easypay Reference Creation Test</title>
    </head>
    <body>
        <div id="container">
            <span>Estado - <?= $_GET['s'] == 'ok' ? 'Success' : 'Error' ?></span>
        </div>
    </body>
</html>