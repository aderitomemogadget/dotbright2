<?php
$path = dirname(__FILE__) . '/../../core/';
include $path . 'core_files.php';
require_once('./api/Easypay.php');


error_reporting(E_ALL);
ini_set('display_errors', 0);

$rs_metodo = new DataObj();$rs = new DataObj();
$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);


//******************************Obter dados da encomenda****/
$id_enc = intval($_GET['id_enc']);
if($id_enc > 0){} else exit;
$sql = "SELECT Metodo_pagamento,TotalDocComIva, enc_Email FROM encomendas WHERE encomendas.ID='$id_enc'";

$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs->EOF())
{
	$valor = $rs->db_data['TotalDocComIva'];
	$enc_Email = $rs->db_data['enc_Email'];
	$ID_Metodo_pagamento = $rs->db_data['Metodo_pagamento'];
}
$rs->db_close();

//****************** OBTER DADOS DO METODO DE PAGAMENTO:
if($ID_Metodo_pagamento)$sql = "SELECT ID,Sandbox, Username, Account_id, Merchant_site_id, Username_sandbox, Password_sandbox FROM metodos_pagamento WHERE ID=$ID_Metodo_pagamento";
else $sql = "SELECT ID,Sandbox, Username, Account_id, Merchant_site_id, Username_sandbox, Password_sandbox FROM metodos_pagamento WHERE Tipo=8";



$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs_metodo->EOF())
{
	$IDb = $rs_metodo->db_data["ID"];
	$user = $rs_metodo->db_data["Username"];
	$entity = $rs_metodo->db_data["Account_id"];
	$cin = $rs_metodo->db_data["Merchant_site_id"];
	$key = $rs_metodo->db_data["Username_sandbox"];
	$code = $rs_metodo->db_data["Password_sandbox"];

	$sandbox = $rs_metodo->db_data['Sandbox'];
}
else exit;
$rs_metodo->db_close();



$sql = "SELECT * FROM metodos_pagamento_lang WHERE ID_PAI=$IDb";
$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
if (!$rs->EOF())
{
	$Pagamento_nome = $rs->db_data['Cabecalho'];
}
$rs->db_close();

// conta = 101121


//conta para referencias MB:
$user='RAVAGN090120';
$cin='101121';
$entity='21098';
$key='df90691f-fdb0-4ced-a0c7-d96d8ed275be';
$code='1612ba9c52c9dabccbf3fd8ba6ab2ea0';//acesso por codigo em vez de ip

/**
//ID:    Account ID
	//Chave: API Key
	$easypay = array('ID' => 'bbc52326-2b29-4d72-adba-34d620b22ddc','Chave' => '9f46d863-620b-4c89-b663-1642ef0577d2');
/**/

/**/
/********************* Sandbox data *********************/
// $key = 'mariajc@ravagnanidental-portugal.com';
// $code = 'UPq6tR';
/**/

//conta para visa:
$user = 'MARIAJ290120';//'RAVAGN090120';
$cin='998763323';
$entity='11683';
$key='c9e6c5a0-1b6d-4fb0-8081-d259c61f5313';
$code='c224c30729131179b3a5c2343b6f4fcc';//acesso por codigo em vez de ip



//dados em sandbox:

 

$easypay_params['user'] = $user; //'FIPA021116';
$easypay_params['cin'] = $cin; //'6273';
$easypay_params['entity'] = $entity; //'10611';
$easypay_params['transaction_key'] = 'Encomenda Nr: '.$id_enc;
$easypay_params['email'] = $enc_Email;
$easypay_params['value'] = $valor;
$easypay_params['key'] = $key; // '70b46a6c578f830c30132bda892975c2';
//Uncomment if you need to validate by code
$easypay_params['code'] = $code; //'b039add9cc320213cae05aed8589525a';


/*================*/ 

$easypay_params['production_server'] = 'https://api.prod.easypay.pt/2.0/'; //TODOss
$easypay_params['test_server'] = 'https://api.test.easypay.pt/2.0/';//TODOss
$easypay_params['request_reference'] = 'single';//TODOss
$easypay_params['accountid'] = 'bbc52326-2b29-4d72-adba-34d620b22ddc';
$easypay_params['apikey'] = '9f46d863-620b-4c89-b663-1642ef0577d2';
$easypay_params['mobile'] = $cin;
//Uncomment if you need to validate by code code
$easypay_params['code'] = '9f46d863-620b-4c89-b663-1642ef0577d2';
$easypay_params['ref_type'] = 'auto';
$easypay_params['observation'] = 'observation';

/**
 * Create a new Easypay Object
 * @var Easypay 
 */
$easypay = new Easypay($easypay_params);
//set mode to either test or production.
//This can be defined in the config file
if($sandbox == 1)$easypay->set_live(false);
else $easypay->set_live(true);


/*echo $Pagamento_nome;*/

$Pagamento_nome='VISA';

if($Pagamento_nome=='VISA')
{
	$data_cliente = cliente_logado();
	/*
	[cli_id] 
    [Nome]
    [Apelido]
    [Grupo] 
    [Nif] 
    [Email]
    [Pais] 
	*/
	$rs = new DataObj();
	$sql = 'SELECT TotalDocComIva FROM encomendas WHERE ID='.$_GET['id_enc'];
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if (!$rs->EOF()) 
	{
		$tvalue = number_format($rs->db_data["TotalDocComIva"],2,".","");
	}
	$rs->db_close();
	$o_name = $data_cliente['Nome'].' '.$data_cliente['Apelido'];
	$o_email = $data_cliente['Email'];
	$url="https://api.prod.easypay.pt/2.0/single";
	

	$headers = ["AccountId: bbc52326-2b29-4d72-adba-34d620b22ddc","ApiKey: 9f46d863-620b-4c89-b663-1642ef0577d2"];
	$body=Array(
		"type" => "sale",
		"currency" => "EUR",
		"capture" => Array("descriptive" => "encomenda $id_enc"),
		"value" => floatval($tvalue),
		"method" => "cc");

	try {
			$curlOpts = [
			    CURLOPT_URL => $url,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_POST => 1,
			    CURLOPT_TIMEOUT => 60,
			    CURLOPT_POSTFIELDS => json_encode($body),
			    CURLOPT_HTTPHEADER => $headers
			];

				$curl = curl_init();
				curl_setopt_array($curl, $curlOpts);
				$response_body = curl_exec($curl);
				curl_close($curl);
				$response = json_decode($response_body, true);

		} catch( Exception $e ) {
			$response = false;
		} 

	if($response['status']=='ok'){
		$sql = 'UPDATE encomendas SET easypay_id="'.$response['id'].'" WHERE ID='.$_GET['id_enc'];
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	}

		echo '<pre>';
		 print_r($response);
		 echo '</pre>';
		 $_GET['id_enc'];
		 //die();
		 /**/
	//https://docs.easypay.pt/api/01BG
	/************************************************/

	/************************************************/
	/**
	$xmlString=file_get_contents("https://www.easypay.pt/_s/api_easypay_01BG.php?ep_cin=$cin&ep_user=$user&ep_entity=$entity&ep_ref_type=auto&ep_type=credit-card&ep_currency=EUR&ep_country=PT&ep_language=PT&t_value=$valor&t_key=$key&s_code=$code");

	var_dump($xmlString);


	$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	echo "<script>window.open('".$array['ep_link']."','_top');</script>";
	/**/
	?>
	<div runat="server" id="ep_box" style="margin:0 auto; text-align:center; border: 1px solid #ddd; border-radius: 5px; width: 210px; min-height: 70px; padding:10px;" >
		<img src="http://store.easyp.eu/img/easypay_logo_nobrands-01.png" style="height:40px; margin-bottom: 10px;" title="Se quer pagar uma referência multibanco utilize a easypay" alt="Se quer pagar uma referência multibanco utilize a easypay" />
		<div style="margin-top: 10px; width: 190px; float: left; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color:#eee" >
				<img src="http://store.easyp.eu/img/visa_mc_bw-01.png" />
				<div style="margin-top: 20px;padding: 5px; padding-top: 10px; clear: both;" >
					<span style="color: #0088cc; text-decoration: none;">Aguarde, está a ser redireccionado</span>
				</div>
			</a>
		</div>
	</div><?

	header("location: ".$response['method']['url']);
}
else
{
	//Criar referencia MB
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
		<? endif; ?>
	</div><?
}