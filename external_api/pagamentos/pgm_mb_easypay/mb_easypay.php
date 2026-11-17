<?php
$path = dirname(__FILE__) . '/../../../core/';
include $path . 'core_files.php';


error_reporting(E_ALL);
ini_set('display_errors', 0);

$rs_metodo = new DataObj();
$rs = new DataObj();
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
//****************** OBTER DADOS DO METODO DE PAGAMENTO:
$sql = "SELECT ID, chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=8";

$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF()) 
{
	$IDb = $rs_metodo->db_data["ID"];
	$user = $rs_metodo->db_data["chavebo"];
	$cin = $rs_metodo->db_data["entidade"];
	$entity = $rs_metodo->db_data["subentidade"];
	$sandbox = 0;//$rs_metodo->db_data['Sandbox'];
}
else exit;
$rs_metodo->db_close();

$o_name = $enc_Nome;
$o_mobile = $enc_Telefone;
$o_email = $enc_Email;
$o_description = " Encomenda Nr ".$id_enc;

$o_obs = '';

$ep_cin = $cin;
$ep_user = $user;
$ep_entity = $entity;

$t_value = $valor;
$ep_language = "PT";
$t_key = $IDD2;

$url_real = "https://www.easypay.pt/_s/api_easypay_01BG.php";
// $url_testes = "http://test.easypay.pt/_s/api_easypay_01BG.php";
//$url_testes = "https://www.easypay.pt/_s/get_ref_detail.php";
$filename = $url_real."?ret_type=".$_POST['ret_type']."&ep_partner=".$_POST['ep_partner']."&ep_cin=".$ep_cin."&ep_user=".$ep_user."&ep_entity=".$ep_entity."&ep_ref_type=auto&t_key=".$t_key."&t_value=".$t_value."&ep_country=".$ep_language."&ep_language=".$ep_language."&o_name=". urlencode($o_name)."&o_mobile=".$o_mobile."&o_email=".$o_email."&o_description=".urlencode($o_description)."&o_obs=".$o_obs."";
// echo SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_MB_EASYPAY]['url']. 'images/multibanco.png';
// echo $filename;
// die();
$result = file_get_contents($filename);    
// $var1 = new SimpleXMLElement($result);
$xml = simplexml_load_string($result);
$json = json_encode($xml);
$var1 = json_decode($json,TRUE);
// print_r($var1);
// you can now print this variable

$entity = $var1['ep_entity'];
$reference = $var1['ep_reference'];
$value = $var1['ep_value'];
$erro = $var1['ep_message'];

$link = str_replace('&amp;', '&', $var1->ep_link);

// echo "<font color=white>".$erro."</font>";
if($entity > 0 && $value > 0)
{
	$tabelaMulti = '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc;margin:0 auto" align="center">';
	$tabelaMulti .= "<tr>";
	$tabelaMulti .=	'<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco</center></td>';
	$tabelaMulti .= "</tr>";
	$tabelaMulti .= "<tr>";
	$tabelaMulti .=	'<td rowspan="4"><div align="center"><img src="' .SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_MB_EASYPAY]['url']. 'images/multibanco.png" border="0" /></div></td>';
	$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>';
	$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.$entity.'</td>';
	$tabelaMulti .= "</tr>";
	$tabelaMulti .= "<tr>";
	$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
	$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'. substr($reference,0,3).' '.substr($reference,3,3).' '.substr($reference,6,9).'</td>';
	$tabelaMulti .= "</tr>";
	$tabelaMulti .= "<tr>";
	$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
	$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.number_format($value, 2,',', ' ').'&nbsp;&euro;</td>';
	$tabelaMulti .= "</tr>";
	$tabelaMulti .= "<tr>";
	$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
	$tabelaMulti .= '<td style="font-size: x-small; text-align:left">&nbsp;</td>';
	$tabelaMulti .= "</tr>";
	$tabelaMulti .= "<tr>";
	$tabelaMulti .=	'<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>';
	$tabelaMulti .= "</tr>";
	$tabelaMulti .= "</table>";

	$sql = 'UPDATE encomendas SET enc_pagamento_dados="'.addslashes($tabelaMulti).'" WHERE ID = '.$id_enc;
	Runquery($db_link, $GLOBALS['DB_name'], $sql);

	echo $tabelaMulti;
}

/*

// $sql = "SELECT * FROM metodos_pagamento_lang WHERE ID_PAI=$IDb";
// $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
// if (!$rs->EOF()) 
// {
	// $Pagamento_nome = $rs->db_data['Cabecalho'];	
// }
// $rs->db_close();

$easypay_params['user'] = $user; //'FIPA021116';
$easypay_params['cin'] = $cin; //'6273';
$easypay_params['entity'] = $entity; //'10611';
$easypay_params['name'] = 'Encomenda Nr: '.$id_enc;
// $easypay_params['description'] = 'description';
// $easypay_params['observation'] = 'observation';
// $easypay_params['mobile'] = '';
$easypay_params['email'] = $enc_Email;
$easypay_params['value'] = $valor;
// $easypay_params['key'] = $key; // '70b46a6c578f830c30132bda892975c2';
$easypay_params['key'] = $IDb; // '70b46a6c578f830c30132bda892975c2';
//Uncomment if you need to validate by code code
$easypay_params['code'] = $code; //'b039add9cc320213cae05aed8589525a';


$easypay = new Easypay($easypay_params);
//set mode to either test or production.
//This can be defined in the config file
if($sandbox == 1)$easypay->set_live(false);
else $easypay->set_live(true);

/*echo $Pagamento_nome;*/

/* if($Pagamento_nome=='VISA')
{
	$xmlString=file_get_contents("https://www.easypay.pt/_s/api_easypay_01BG.php?ep_cin=$cin&ep_user=$user&ep_entity=$entity&ep_ref_type=auto&ep_type=credit-card&ep_currency=EUR&ep_country=PT&ep_language=PT&t_value=$valor&t_key=$key&s_code=$code");

	$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);

	echo "<script>window.open('".$array['ep_link']."','_top');</script>";


	//print_r($array);

	//$e = $easypay->requestPayment($id_enc, $key, $valor, 'credit-card');
	//var_dump($xmlString);

	// $xmlString=file_get_contents("https://www.easypay.pt/_s/api_easypay_03AG.php?ep_cin=".$_GET['ep_cin']."&ep_user=".$_GET['ep_user']."&ep_doc=".$_GET['ep_doc']."&ep_key=".$key."&s_code=".$code);
	// $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
	// $json = json_encode($xml);
	// $array = json_decode($json,TRUE);





	//$sql = "UPDATE encomendas SET Ent_atm='".$reference['ep_entity']."',Ref_atm='".str_replace(' ', '', $reference['ep_reference'])."' WHERE ID='" . $id_enc . "'";
	//$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	?>
	<div runat="server" id="ep_box" style="margin:0 auto; text-align:center; border: 1px solid #ddd; border-radius: 5px; width: 210px; min-height: 70px; padding:10px;" >
		<img src="http://store.easyp.eu/img/easypay_logo_nobrands-01.png" style="height:40px; margin-bottom: 10px;" title="Se quer pagar uma referência multibanco utilize a easypay" alt="Se quer pagar uma referência multibanco utilize a easypay" />
		<div style="margin-top: 10px; width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee" >
			<a href="<?php echo $reference['ep_link'] ?>">
				<img src="http://store.easyp.eu/img/visa_mc_bw-01.png" />
				<div style="margin-top: 20px;padding: 5px; padding-top: 10px; clear: both;" >
					<span style="color: #0088cc; text-decoration: none;">Aguarde, está a ser redireccionado</span>
				</div>
			</a>
		</div>
	</div><?
}
else
{ 
	$reference = $easypay->createReference();
	$sql = "UPDATE encomendas SET Ent_atm='".$reference['ep_entity']."',Ref_atm='".str_replace(' ', '', $reference['ep_reference'])."' WHERE ID='" . $id_enc . "'";
	$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	?>
	<div runat="server" id="ep_box" style="margin:0 auto; text-align:center; border: 1px solid #ddd; border-radius: 5px; width: 210px; min-height: 70px; padding:10px;" >
		<img src="http://store.easyp.eu/img/easypay_logo_nobrands-01.png" style="height:40px; margin-bottom: 10px;" title="Se quer pagar uma referência multibanco utilize a easypay" alt="Se quer pagar uma referência multibanco utilize a easypay" />
		<div style="margin-top: 20px;width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee;">
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
		<?php endif; ?>
	</div><?php
// }*/