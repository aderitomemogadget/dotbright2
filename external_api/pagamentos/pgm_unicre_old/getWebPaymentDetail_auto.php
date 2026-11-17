<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN"><?php
// INITIALIZE
include "include.php";
$array = array();

global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$sql='SELECT pagamento_id as ID, unicretoken FROM pagamentos WHERE Pago=0 AND Transaction_Code=""';
// echo $sql;
$rs=new DataObj;
$rs->db_open($db_link,$DB_name,$sql);
if(!$rs->EOF())
{
	while(!$rs->EOF())
	{
		$payline = new paylineSDK(MERCHANT_ID, ACCESS_KEY, PROXY_HOST, PROXY_PORT, PROXY_LOGIN, PROXY_PASSWORD, PRODUCTION);

		$array['token'] = $rs->db_data['unicretoken'];	
		$array['version'] = '';
		// echo $rs->db_data['unicretoken'];

		// RESPONSE FORMAT
		$response = $payline->getWebPaymentDetails($array);
		if(isset($response))
		{

			$page_id = 38;
			$referer='';

			$code = $response["result"]["code"];
			$token = $array['token'];
			

			$msg = '';
			

			$id_enc = $_POST['id_enc'];

		
			$ID = $rs->db_data['ID'];
			
			if($code=="00000")
			{
				$msg = "O seu pagamento foi efetuado com sucesso.";
				$sql = 'UPDATE pagamentos SET Transaction_Code="'.$code.'", data2="'.date('Y-m-d H:i:s').'", Pago=1, Encomenda_Status="Success", Enc_txt="'.$msg.'" WHERE unicretoken="'.$token.'"';
				$res = Runquery($db_link, $DB_name, $sql);           
			 
				$referer = $GLOBALS['site_http_url'];//.seo('pages.php?page_id=28');
			}
			elseif(substr($code, 0, 2)=="01")
			{
				$msg = "O seu pagamento foi recusado.";
				$sql = 'UPDATE pagamentos SET Transaction_Code="'.$code.'", data2="'.date('Y-m-d H:i:s').'",Pago=0,Encomenda_Status="Denied", Enc_txt="'.$msg.'" WHERE unicretoken="'.$token.'"';
				$res = Runquery($db_link,$DB_name,$sql);    	   
			}
			elseif(substr($code, 0, 2)=="04")
			{
				$msg = "O seu pagamento foi recusado por suspeita de fraude.";
				$sql = 'UPDATE pagamentos SET Transaction_Code="'.$code.'", data2="'.date('Y-m-d H:i:s').'",Pago=0,Encomenda_Status="Failed_Fraud", Enc_txt="'.$msg.'" WHERE unicretoken="'.$token.'"';
				$res = Runquery($db_link,$DB_name,$sql);
			   
			}
			elseif(substr($code, 0, 2)=="10")
			{
				$msg = "O seu pagamento falhou. Volte a tentar mais tarde.";
				$sql = 'UPDATE pagamentos SET Transaction_Code="'.$code.'", data2="'.date('Y-m-d H:i:s').'",Pago=0,Encomenda_Status="Failed", Enc_txt="'.$msg.'" WHERE unicretoken="'.$token.'"';
				$res = Runquery($db_link,$DB_name,$sql);
			}
			elseif(substr($code, 0, 2)=="02")
			{
				$msg = "O seu pagamento falhou devido a problemas técnicos. Volte a tentar mais tarde.";
				$sql = 'UPDATE pagamentos SET Transaction_Code="'.$code.'", data2="'.date('Y-m-d H:i:s').'",Pago=0,Encomenda_Status="Failed_Technical", Enc_txt="'.$msg.'" WHERE unicretoken="'.$token.'"';
				$res = Runquery($db_link,$DB_name,$sql);    	    
			}
		}
		
		$rs->db_next();
	}
}
$rs->db_close(1);