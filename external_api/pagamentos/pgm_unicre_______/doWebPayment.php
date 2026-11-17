<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	require_once $path.'core_files.php';

	global $site_title;

    echo '<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>'.SITE_TITLE.'</title>
		<meta NAME="robots" CONTENT="all" />
		<meta name="revisit-after" content="1 day">
		<meta name="dcterms.audience" content="Global" />
		<meta name="googlebot" content="noodp">
		<meta name="rating" content="general">
		<meta name="viewport" content="width=1000, user-scalable=1" />
		<style>
		html{height:100%}
		body{background:#ccc;margin:0;padding:0;min-height:100%;font-size:20px;font-family:Georgia;color:#222;}
		.main{align:center;vertical-align:middle;width:1024px;text-align:center;height:200px;margin:0 auto}
		</style>
	</head>
	<body>
		<div class="main">
			<img src="'.(defined('LOGOTIPO_PRINCIPAL') && LOGOTIPO_PRINCIPAL != '' ? LOGOTIPO_PRINCIPAL : 'layout/'.PROJETO.'/logos/logo_top.png').'" /><br/><br/>';
		






	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$Valor=$_POST['amount'];
	$enc_id = $_POST['ref'];

	// Encomenda
	$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'"';
	$rs = new DataObj;
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if($rs->EOF())
	{
		die();
	}
	else
	{
		$Address=$rs->db_data['enc_morada'];
		$City=$rs->db_data['enc_cidade'];
		$Post_Code=$rs->db_data['enc_Codigo_Postal'];
		$E_Mail=$rs->db_data['enc_Email'];
	}
	$rs->db_close();



	$sql = 'INSERT INTO pagamentos_unicre(Tipo,ID_ENC,Estado,CDate,Valor,Resposta1) VALUES (1,"'.$enc_id.'",0,"'.date("Y-m-d H:i:s").'","'.$Valor.'","")';
	$res = Runquery($db_link,DBNAME,$sql);
	$pagamento_id=db_get_last_insert_id($db_link);
	


	$sql="INSERT INTO sibs_visa_logs (ID_Pagamento, Data, Json, Estado, Erros) VALUES('".$pagamento_id."','".date("Y-m-d H:m:i")."', 'Tel = $m_telemovel | Valor = $valor_pagar', '0','')";
	$res = Runquery($db_link,DBNAME, $sql);
	$ID_Log=db_get_last_insert_id($db_link);


	$Valor=number_format($Valor,2,'.','');
	$sandbox=1;

	
	$Country='PT';


	if($sandbox==1)
	{
		//Credenciais de Teste:
	    $authentication_entityId = '8ac7a4c97b519e7c017b53a2a54a0eb8';
	    $Bearer = 'OGFjN2E0Yzk3NGQ0Yjg4YjAxNzRkOGI4MWMzNTA2Zjh8OFI4Y3F6emNXbg==';
	    $Entidade = '23867';

		if($Sibs_Visa_checkoutId !='')
		{
			$url = "https://test.oppwa.com/v1/checkouts";
			$data = "entityId=$authentication_entityId".
	                "&amount=$Valor".
	                "&currency=EUR".
	                "&billing.city=$City".
					"&billing.country=$Country".
					"&billing.street1=".urlencode($Address).
					"&billing.postcode=$Post_Code".
					"&testMode=EXTERNAL".
					"&customParameters[SIBS_ENV]=QLY".
					"&customer.email=$E_Mail".
	                "&paymentType=DB".
					"&registrations[0].id=$Sibs_Visa_checkoutId";
	    }
		else
		{
			//Trocar email pelo do cliente e a morada
			$url = "https://test.oppwa.com/v1/checkouts";
			$data = "entityId=$authentication_entityId".
	                "&amount=$Valor".
	                "&currency=EUR".
	                "&billing.city=$City".
					"&billing.country=$Country".
					"&billing.street1=".urlencode($Address).
					"&billing.postcode=$Post_Code".
					"&testMode=EXTERNAL".
					"&customParameters[SIBS_ENV]=QLY".
					"&customer.email=$E_Mail".
					"&createRegistration=false".
					"&merchantTransactionId=$id_enc" .
	                "&paymentType=DB";
	    }
	}
	else
	{	
		//Credenciais de producao:
	    $authentication_entityId = '8ac7a4c97b519e7c017b53a2a54a0eb8';
	    $Bearer = 'OGFjN2E0Yzk3NGQ0Yjg4YjAxNzRkOGI4MWMzNTA2Zjh8OFI4Y3F6emNXbg==';
	    $Entidade = '25802';


		if($Sibs_Visa_checkoutId !='')
		{
			$url = "https://oppwa.com/v1/checkouts";
			$data = "entityId=$authentication_entityId".
	                "&amount=$Valor".
	                "&currency=EUR".
	                "&billing.city=$City".
					"&billing.country=$Country".
					"&billing.street1=".urlencode($Address).
					"&billing.postcode=$Post_Code".
					"&customer.email=$E_Mail".
					"&merchantTransactionId=$id_enc" .
	                "&paymentType=DB".
					"&registrations[0].id=$Sibs_Visa_checkoutId";
	    }
		else
		{
			//Trocar email pelo do cliente e a morada
			$url = "https://oppwa.com/v1/checkouts";
			$data = "entityId=$authentication_entityId".
	                "&amount=$Valor".
	                "&currency=EUR".
	                "&billing.city=$City".
					"&billing.country=$Country".
					"&billing.street1=".urlencode($Address).
					"&billing.postcode=$Post_Code".
					"&customer.email=$E_Mail".
					"&merchantTransactionId=$id_enc" .
					"&createRegistration=false".
	                "&paymentType=DB";
	    }
	}
	//---------------------------------

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer '.$Bearer));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
	if(curl_errno($ch)) {
		//return curl_error($ch);
	}
	curl_close($ch);
	$resposta=json_decode($responseData,true);

	$sql = 'UPDATE pagamentos_unicre SET Resposta1="'.addslashes(serialize($resposta)).'",checkoutId="'.$resposta['id'].'" WHERE ID = '.$pagamento_id;
	$res = Runquery($db_link,DBNAME,$sql);

	echo '
		<style>
		html, body {
		  font-family:"Titillium Web";
		  background: #232323;
		  background-color: #232323;
		}
		.login-form {
		  max-width: 350px;
		  width: 80%;
		  margin: 0 auto;
		  position: relative;
		  z-index: 2;
		  background: #ccc;
		  background-color: rgb(255, 255, 255);
		}

		input {
		  width: 100%;
		  border-radius: 16px;
		  
		  font-family: \'Titillium Web\', sans-serif;
		  padding: 5px 5px;
		  margin: 5px 0;
		  font-size: 12px;
		  background-color: #ff8a00;
		  border:0px;

		  color: #FFF !important;
			float: left;
			text-transform: uppercase;
			height: 36px;
			text-align: center;
			font-size: 20px;
			cursor: pointer;
			line-height: 30px;
			margin: 0 14px 0 auto;
			background-color: #;
			text-indent: 10px;
			padding-right: 0;

		}

		</style>
	
	<script src="https://app.g27.eu/assets/web/assets/jquery/jquery.min.js"></script>
	<div id="overlay-login" style="min-height: 628px; height: 628px; display: block;">
		<div class="header-out" style="position:absolute"><div class="header" id="header2_login"></div></div>';


	echo '
	<style>
		.wpwl-label{font-family:"Titillium Web";font-size:12px;line-height:14px}
		.wpwl-button{font-family:"Titillium Web";border-radius:9px}
		option{font-family:"Titillium Web"}
		.wpwl-form {margin:0;padding: 10px;text-align: left;font-size:12px;line-height:14px}
	</style>';






	echo '
	<div id="home_not_logado" class="login-box">
		<div class="login-form" style="background-color: transparent;">
			<div style="width:100%;background-color:#fff;text-align:center;padding:0px;border-radius:12px;margin-top:0px">';

			//------------------------------------
			echo '
			<script>
			    var wpwlOptions = {
			        style:"plain",
			        onReady: function(e){
			        	$(".wpwl-label-brand").html("Card Brand");
			        	$(".wpwl-form-card").find(".wpwl-button-pay").on("click", function(e){validateHolder(e);});
			        },
					onBeforeSubmitCard: function(e){return validateHolder(e);},
			        locale:"pt",
			        paymentTarget:"_top",	        
		    		registrations: {
		        		requireCvv: true
		    		},
			        iframeStyles:{
				        "card-number-placeholder":{
				            "color":"#ff0000",
				            "font-size":"14px",
				            "font-family":"Titillium Web"
				        },
				            "cvv-placeholder":{
				            "color":"#0000ff",
				                "font-size":"14px",
				                "font-family":"Titillium Web"
				        }
				    }	        
			    }
			    function validateHolder(e)
			    {
				  var holder = $(".wpwl-control-cardHolder").val();
				  if (holder.trim().length < 2)
				  {
				    $(".wpwl-control-cardHolder").addClass("wpwl-has-error").after("<div class=\'wpwl-hint wpwl-hint-cardHolderError\'>Invalid card holder</div>");
				    return false;
				  }
				  return true;
				}

			</script>';

			if($sandbox==1)
			{
				if($Sibs_Visa_checkoutId=='')
				{
					echo '<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
				}
				else
				{
					echo '<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
				}
			}
			else
			{		
				if($Sibs_Visa_checkoutId=='')
				{
					echo '<script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
				}
				else
				{
					echo '<script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
				}
			}
			echo '<form action="https://www.on4tech.pt/external_api/pagamentos/pgm_unicre/ipn_visa_result.php?r='.$r.'#makeHolderMandatory" class="paymentWidgets" data-brands="VISA MASTER"></form>';


			//------------------------------------


	echo '	</div>

			<div style="width:100%;text-align:center;padding:10px;border-radius:12px;border-bottom-right-radius:0px;margin-top:30px">				
				<input type="button" name="bt-login1" value="CANCELAR" onmouseup="window.open(\'https://www.on4tech.pt/external_api/pagamentos/pgm_unicre/ipn_visa_result.php\',\'_self\');">
			</div>

		</div>		
		</div>
	</div>
	</div>';



	echo '</div>
	</body>
	</html>';

?>