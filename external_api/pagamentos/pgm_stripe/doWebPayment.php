<?php
/*$path = dirname( __FILE__ ).'/../../../core/';
require_once $path.'core_files.php';
require_once dirname( __FILE__ ).'/stripe/init.php';

if(isset($_GET['stripeToken']) && $_GET['stripeToken'] !='' && isset($_GET['stripeToken']) && intval($_GET['id_enc']) > 0)
{
	$id_enc = intval($_GET['id_enc']); $valor = 0;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj;
	$sql_all = "SELECT TotalDocComIva FROM encomendas WHERE encomendas.ID='" . $id_enc . "' AND Encomenda_Paga = 0";
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
	if (!$rs->EOF())
	{
		$valor = $rs->db_data['TotalDocComIva'];
	}
	$rs->db_close();

	if($valor > 0)
	{
		\Stripe\Stripe::setApiKey('sk_test_wgkNydrTUG8LPAYWUualZOfm');
		$charge = \Stripe\Charge::create(['amount' => number_format($valor , 2, '.', ''), 'currency' => 'eur', "card" => $_GET['stripeToken']]);
		$myFile =  dirname( __FILE__ ).'/../../../files/stripe_'.intval($_GET['id_enc']).'.txt';
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, print_r($charge, true));
		fclose($fh);
	}
}*/
?>