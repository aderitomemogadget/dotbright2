<?php
$path = dirname(__FILE__) . '/../../../core/';
include $path . 'core_files.php';


error_reporting(E_ALL);
ini_set('display_errors', 0);

$rs_metodo = new DataObj();$rs = new DataObj();
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

//******************************Obter dados da encomenda****/
$id_enc = intval($_GET['id_enc']);

if($id_enc > 0){} else exit;

$sql = "SELECT Metodo_pagamento,TotalDocComIva, enc_Email, enc_Nome, enc_Telefone FROM encomendas WHERE encomendas.ID='$id_enc'";
$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs->EOF())
{
	$valor = $rs->db_data['TotalDocComIva'];
	$enc_Email = $rs->db_data['enc_Email'];
	$enc_Nome = $rs->db_data['enc_Nome'];
	$enc_Telefone = $rs->db_data['enc_Telefone'];
}
$rs->db_close();

$sandbox = 0;


$sql = "SELECT ID, chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=4";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF())
{
	$chavebo = $rs_metodo->db_data["chavebo"];
	$entidade = $rs_metodo->db_data["entidade"];
	$subentidade = $rs_metodo->db_data["subentidade"];
	$sandbox = $rs_metodo->db_data['Sandbox'];
}
else exit;
$rs_metodo->db_close();


$ref='627300084';

$url_real = "https://www.ifthenpay.pt/IfmbWS/WsIfmb.asmx/GetPayments";
//$url_testes = "https://www.easypay.pt/_s/get_ref_detail.php";
$filename = $url_real.'?chavebackoffice='.$chavebo.'&entidade='.$entidade.'&subentidade='.$subentidade.'&referencia=&valor=&sandbox=0';
$verification = file_get_contents($filename);


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