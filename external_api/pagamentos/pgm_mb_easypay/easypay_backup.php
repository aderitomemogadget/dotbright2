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
	$entity = $rs_metodo->db_data["Account_id"];
	$cin = $rs_metodo->db_data["Merchant_site_id"];
	$key = $rs_metodo->db_data["Username_sandbox"];
	$code = $rs_metodo->db_data["Password_sandbox"];
	
	
	$sandbox = $rs_metodo->db_data['Sandbox'];
}
else exit;
$rs_metodo->db_close();


$id_enc = intval($_GET['id_enc']);
if($id_enc > 0){} else exit;
$sql_all = "SELECT TotalDocComIva, enc_Email FROM encomendas WHERE encomendas.ID='$id_enc'";
$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
if (!$rs->EOF()) 
{
	$valor = $rs->db_data['TotalDocComIva'];
	$enc_Email = $rs->db_data['enc_Email'];
}



$easypay_params['user'] = $user; //'FIPA021116';
$easypay_params['cin'] = $cin; //'6273';
$easypay_params['entity'] = $entity; //'10611';

$easypay_params['name'] = 'Encomenda Nr: '.$id_enc;
// $easypay_params['description'] = 'description';
// $easypay_params['observation'] = 'observation';
// $easypay_params['mobile'] = '';
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
$reference = $easypay->createReference();

//var_dump($reference);

/*

array(9) { ["ep_status"]=> string(3) "ok0" ["ep_message"]=> string(122) "ep_country and ep_entity and ep_user and ep_cin ok and validation by code;code ok - new reference generated - 627300043 - " ["ep_cin"]=> string(4) "6273" ["ep_user"]=> string(10) "FIPA021116" ["ep_entity"]=> string(5) "10611" ["ep_reference"]=> string(9) "627300043" ["ep_value"]=> string(5) "26.07" ["t_key"]=> string(32) "70b46a6c578f830c30132bda892975c2" ["ep_link"]=> string(110) "https://www.easypay.pt/_s/c11.php?e=10611&r=627300043&v=26.07&c=PT&l=PT&t_key=70b46a6c578f830c30132bda892975c2" } 
*/

$sql = "UPDATE encomendas SET Ent_atm='".$reference['ep_entity']."',Ref_atm='".str_replace(' ', '', $reference['ep_reference'])."' WHERE ID='" . $id_enc . "'";
$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
			
//var_dump($reference);

?>
<div runat="server" id="ep_box" style="margin:0 auto; text-align:center; border: 1px solid #ddd; border-radius: 5px; width: 210px; min-height: 70px; padding:10px;" >
	<img src="http://store.easyp.eu/img/easypay_logo_nobrands-01.png" style="height:40px; margin-bottom: 10px;" title="Se quer pagar uma referência multibanco utilize a easypay" alt="Se quer pagar uma referência multibanco utilize a easypay" />
	<div style="width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee;">
		<img src="http://store.easyp.eu/img/MB_bw-01.png" />
		
		<div style="padding: 5px; padding-top: 10px; clear: both;">
			<span style="font-weight: bold;float: left;">Entidade:</span>
			<span style="color: #0088cc; float: right"><?php echo $reference['ep_entity'] ?> (Easypay)</span>
		</div>

		<div style="padding: 5px;clear: both;">
			<span style="font-weight: bold;float: left;">Referência:</span>
			<span style="color: #0088cc; float: right"><?php echo wordwrap( $reference['ep_reference'], 3, ' ', true ) ?></span>
		</div>
		
		<div style="padding: 5px; clear: both;">
			<span style="font-weight: bold;float: left;">Valor:</span>
			<span style="color: #0088cc; float: right"><?php echo $reference['ep_value'] ?> &euro;</span>
		</div>
	</div>
	<div style="margin-top: 10px; width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee;display:none" >
		<a href="<?php echo $reference['ep_link'] ?>">
			<img src="http://store.easyp.eu/img/visa_mc_bw-01.png" />
			<div style="padding: 5px; padding-top: 10px; clear: both;" >
				<span style="color: #0088cc; text-decoration: none;">Pay Now!</span>
			</div>
		</a>
	</div>
	<? if($reference['ep_boleto_link']): ?>
	<div style="margin-top: 10px; width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee;display:none">
		<a href="<?php echo $reference['ep_boleto_link'] ?>">
			<img src="http://store.easyp.eu/img/bb_bw-01.png" />
			<div style="padding: 5px; padding-top: 10px; clear: both;" >
				<span style="color: #0088cc; text-decoration: none;">Pay Now!</span>
			</div>
		</a>
	</div>
	<? endif; ?>
</div>  
  