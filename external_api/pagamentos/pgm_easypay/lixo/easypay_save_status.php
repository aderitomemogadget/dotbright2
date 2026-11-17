<?php

$path = dirname(__FILE__) . '/../../core/';
include $path . 'core_files.php';
require_once('./api/Easypay.php');

$rs_metodo = new DataObj();$rs = new DataObj();
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$sql = "SELECT Sandbox, Username, Account_id, Merchant_site_id, Username_sandbox, Password_sandbox FROM metodos_pagamento WHERE Tipo=8";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF()) 
{
	$user = $rs_metodo->db_data["Username"];
	$entity = $rs_metodo->db_data["Account_id"];
	$cin = $rs_metodo->db_data["Merchant_site_id"];
	$key = $rs_metodo->db_data["Username_sandbox"];
	$code = $rs_metodo->db_data["Password_sandbox"];
	
	
	$sandbox = $rs_metodo->db_data['Sandbox'];
}
else exit;
$rs_metodo->db_close();

/*$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('../../files/request.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);*/


	/*$req_dump = print_r( $_REQUEST, true );
	$fp = file_put_contents( '../../files/request.log', $req_dump );
*/


/*
 [ep_cin] => 6273
    [ep_user] => FIPA021116
    [ep_doc] => FIPA0211162302986820161130180800*/

/*https://www.easypay.pt/_s/api_easypay_03AG.php?ep_cin=6273&ep_user=FIPA021116&ep_doc=FIPA0211162302986820161130180800&ep_key=70b46a6c578f830c30132bda892975c2

*/
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$sql="INSERT INTO easypay_notifications (ep_doc, ep_cin, ep_user, ep_type) VALUES ('".$_GET['ep_doc']."', '".$_GET['ep_cin']."', '".$_GET['ep_user']."', 'credorax')";
Runquery($db_link, $GLOBALS['DB_name'], $sql);


echo '<!--?xml version="1.0" encoding="ISO-8859-1"?-->
<getautomb_key>
  <ep_status>ok0</ep_status>
  <ep_message>generated document</ep_message>
  <ep_cin>'.$_GET['ep_cin'].'</ep_cin>
  <ep_user>'.$_GET['ep_user'].'</ep_user>
  <ep_doc>'.$_GET['ep_doc'].'</ep_doc>
  <ep_key>'.$key.'</ep_key>
</getautomb_key>';

$xmlString=file_get_contents("https://www.easypay.pt/_s/api_easypay_03AG.php?ep_cin=".$_GET['ep_cin']."&ep_user=".$_GET['ep_user']."&ep_doc=".$_GET['ep_doc']."&ep_key=".$key."&s_code=".$code);

//$xml = new SimpleXMLElement($xmlString);

$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);


/*var_dump($array);
echo $array['ep_status'];*/

$sql="UPDATE `easypay_notifications` SET
      `ep_status` = '".$array['ep_status']."',
      `ep_entity` = '".$array['ep_entity']."',
      `ep_reference` = '".$array['ep_reference']."',
      `ep_value` = '".$array['ep_status']."',
      `ep_date` = '".$array['ep_date']."',
      `ep_payment_type` = '".$array['ep_payment_type']."',
      `ep_value_fixed` = '".$array['ep_value_fixed']."',
      `ep_value_var` = '".$array['ep_value_var']."',
      `ep_value_tax` = '".$array['ep_value_tax']."',
      `ep_value_transf` = '".$array['ep_value_transf']."',
      `ep_date_transf` = '".$array['ep_date_transf']."',
      `t_key` = '".$array['t_key']."'
  WHERE `ep_doc` = '".$array['ep_doc']."'";

  Runquery($db_link, $GLOBALS['DB_name'], $sql);


$sql = "SELECT ID FROM encomendas WHERE Ref_atm='".$array['ep_reference']."'";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF()) 
{
	$id_enc = $rs_metodo->db_data["ID"];
}
else exit;
$rs_metodo->db_close();


			$sql = "UPDATE encomendas SET Encomenda_Paga=1,Encomenda_Status='".$array['ep_status']."',Estado='3' WHERE ID='".$id_enc."'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		  enviarEmailCliente($id_enc);
?>