<?php

/*NOTA: Este metodo não necessita de aceder ao site da ifthen para gerar as referencias MB*/

function format_number($number)
{
	$verifySepDecimal = number_format(99, 2);
	$valorTmp = $number;
	$sepDecimal = substr($verifySepDecimal, 2, 1);
	$hasSepDecimal = True;
	$i = (strlen($valorTmp) - 1);

	for ($i; $i != 0; $i-= 1) {
		if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == ",") {
			$hasSepDecimal = True;
			$valorTmp = trim(substr($valorTmp, 0, $i)) . "@" . trim(substr($valorTmp, 1 + $i));

			break;
		}
	}
	if ($hasSepDecimal != True) {
		$valorTmp = number_format($valorTmp, 2);
		$i = (strlen($valorTmp) - 1);

		for ($i; $i != 1; $i--) {
			if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == ",") {
				$hasSepDecimal = True;
				$valorTmp = trim(substr($valorTmp, 0, $i)) . "@" . trim(substr($valorTmp, 1 + $i));

				break;
			}
		}
	}

	for ($i = 1; $i != (strlen($valorTmp) - 1); $i++) {
		if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == "," || substr($valorTmp, $i, 1) == " ") {
			$valorTmp = trim(substr($valorTmp, 0, $i)) . trim(substr($valorTmp, 1 + $i));

			break;
		}
	}
	if (strlen(strstr($valorTmp, '@')) > 0) {
		$valorTmp = trim(substr($valorTmp, 0, strpos($valorTmp, '@'))) . trim($sepDecimal) . trim(substr($valorTmp, strpos($valorTmp, '@') + 1));
	}

	return $valorTmp;
}
function GenerateMbRef($ent_id, $subent_id, $order_id, $order_value)
{
	$chk_val = 0;
	$order_id = "0000" . $order_id;
	$order_value = sprintf("%01.2f", $order_value);
	$order_value = format_number($order_value);
	//Apenas são considerados os 4 caracteres mais à direita do order_id
	$order_id = substr($order_id, (strlen($order_id) - 4) , strlen($order_id));
	if ($order_value < 1)
	{
		return "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 1 Euro";

	}
	if ($order_value >= 1000000)
	{
		return "<b>AVISO:</b> Pagamento fraccionado por exceder o valor limite para pagamentos no sistema Multibanco<br>";
	}
	while ($order_value >= 1000000)
	{
		GenerateMbRef($order_id++, 999999.99);
		$order_value-= 999999.99;
	}
	//cálculo dos check digits
	$chk_str = sprintf('%05u%03u%04u%08u', $ent_id, $subent_id, $order_id, round($order_value * 100));
	$chk_array = array(
		3,
		30,
		9,
		90,
		27,
		76,
		81,
		34,
		49,
		5,
		50,
		15,
		53,
		45,
		62,
		38,
		89,
		17,
		73,
		51
	);

	for ($i = 0; $i < 20; $i++)
	{
		$chk_int = substr($chk_str, 19 - $i, 1);
		$chk_val+= ($chk_int % 10) * $chk_array[$i];
	}
	$chk_val%= 97;
	$chk_digits = sprintf('%02u', 98 - $chk_val);
	$teste = $subent_id . " " . substr($chk_str, 8, 3) . " " . substr($chk_str, 11, 1) . $chk_digits;

	return $teste;
}
$path =  dirname( __FILE__ ) . '/../../../core/';
require_once $path . 'core_files.php';


//$refgen = GenerateMbRef($entidade, $subentidade, $id_enc, number_format($valor , 2, '.', ''));


$rs_metodo = new DataObj;
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$sql = "SELECT * FROM metodos_pagamento WHERE Tipo=4";
$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
$Sandbox = 0;
$conta_ifthen = '';
if (!$rs_metodo->EOF())
{
	$sandbox = $rs_metodo->db_data["Sandbox"];
	$chave = $rs_metodo->db_data["chavebo"];
	$entidade = $rs_metodo->db_data["entidade"];
	$subentidade = $rs_metodo->db_data["subentidade"];
}
$rs_metodo->db_close();

if ($_GET['id_enc'] > 0)
{
	$id_enc = $_GET['id_enc'];
	$rs = new DataObj;
	$sql_all = "SELECT TotalDocComIva FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
	if (!$rs->EOF()) {
		$valor = $rs->db_data['TotalDocComIva'];
	}
	$rs->db_close();
	$refgen = GenerateMbRef($entidade, $subentidade, $id_enc, number_format($valor , 2, '.', ''));
	//$refgen = GenerateMbRef($entidade, $subentidade, $id_enc, $valor);

	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$sql = "UPDATE encomendas SET Ent_atm='".$entidade."',Ref_atm='".str_replace(' ', '', $refgen)."' WHERE ID='" . $id_enc . "'";
	$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);




	if($refgen!='')
	{
		$msg_tmp.= '<table width="350" border=0 cellspacing=0 cellpadding=0 style="border:1px solid grey;margin:0 auto;border-collapse:inherit;border-spacing:10px;">';
		$msg_tmp.= '<tr><td  align="center"><img src="' .SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_MB_IFTHEN]['url']. 'images/multibanco.png" border="0" /></td>
			<td valign="middle" align="center">Pagamento por Multibanco ou Homebanking</td></tr>';
		$msg_tmp.= '<tr><td>';
		$msg_tmp.= "Entidade:</td><td align='right' style='font-weight:bold'>".$entidade;
		$msg_tmp.= '</td></tr>';
		$msg_tmp.= '<tr><td>';
		$msg_tmp.= "Referência:</td><td align='right' style='font-weight:bold'>".$refgen;
		$msg_tmp.= '</td></tr>';
		$msg_tmp.= '<tr><td>';
		//$msg_tmp.= "Valor:</td><td align='right' style='font-weight:bold;'>&euro;&nbsp;" . number_format(round($valor, 2) , 2, ',', ' ');
		$msg_tmp.= "Valor:</td><td align='right' style='font-weight:bold;'>&euro;&nbsp;" . number_format($valor , 2, ',', ' ');
		$msg_tmp.= '</td></tr>';
		$msg_tmp.= '<tr><td colspan="2" align="center" style="font-size:13px">O talão emitido pelo caixa automático faz prova de pagamento. Conserve-o.</td></tr>';
		$msg_tmp.= '</table>';

		$sql = 'UPDATE encomendas SET enc_pagamento_dados="'.addslashes($msg_tmp).'" WHERE ID = '.$id_enc;
		Runquery($db_link, $GLOBALS['DB_name'], $sql);

	}else{
		$msg_tmp.='Não foi possivel gerar referência.';
	}
	echo $msg_tmp;
}
?>

