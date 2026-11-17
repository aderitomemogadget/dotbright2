<?php
	$path =  dirname( __FILE__ ) . '/../../../core/';

	include $path.'core_files.php';

	ini_set('display_errors', 1);
	ini_set('display_startup_errors',1);
	error_reporting(1);

	define('COOKIE_FILE', '../img_upload/cookie.txt');

	function flush_buffers()
	{
	    ob_end_flush();
	    ob_flush();
	    flush();
	    ob_start();
	}

	//$n_encomenda = intval($_GET['n_encomenda']);
	//$n_encomenda="202001293A172";

	global $DB_host,$DB_user,$DB_pass,$DB_name;

    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

    $rs=new DataObj;
    $dados=new DataObj;


	$n_encomenda = intval($_POST['n_encomenda']);
	/*$n_encomenda=202001156;
	$n_encomenda="c202001028";
	$n_encomenda="202001373A210";
	$n_encomenda="202002043A573";*/

	//	$n_encomenda="202001917";


	if($n_encomenda)
	{



		$sql="SELECT ID FROM encomendas WHERE ID =$n_encomenda";

		$dados->db_open($db_link, $DB_name, $sql);
		if(!$dados->EOF())
		{
		    while(!$dados->EOF())
		    {
		    	$valid = '';
		    	$ID = $dados->db_data["ID"];
		    	// $ID2 = $dados->db_data["ID2"];
		    	// $email = $dados->db_data["email"];
		    	// $Des_Social = $dados->db_data["Des_Social"];
		    	// $ymail = 'vendas@clickfiel.pt';
		    	// $zmail = 'miguelalmeida@clickfiel.pt';

				$valid=get_oney_ready_to_delivery($ID);
				if($valid==1)
	    		{
	    			echo 1;
	    			//OK
	    			$sqlp = "UPDATE encomendas set email_enviado=2 where ID=".$ID;
	    		}
	    		else
	    		{
	    			echo 0;
					// por a encomenda como não paga
		    		$sqlp = "UPDATE encomendas set Estado=3 where ID=".$ID;
	    		}
	    		Runquery($db_link, $DBName, $sql);
	    		// echo $sqlp.'<br><br>';
		    	$dados->db_next();
		    }
		}
		$dados->db_close();
	}
	else
	{
		echo 0;
	}

	function get_oney_ready_to_delivery($id_encomenda)
	{

		global $DB_host,$DB_user,$DB_pass,$DB_name;

	    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	    $dados=new DataObj;
		$sql = 'select * from oney_logs where ID_Encomenda="'.$id_encomenda.'" order by ID desc LIMIT 1';
		$dados->db_open($db_link, $DB_name, $sql);
		if(!$dados->EOF())
		{
			$n_encomenda = $id_encomenda.'A'.$dados->db_data["ID"];
		}
		$dados->db_close();


		if($n_encomenda)
		{

			$LANGUAGE_CODE = "PT";
		    //Pre prod
		    /*$MERCHANT_GUID = "b36d5c06-bf0a-49d3-880f-4445fbb2c9bd";
		    $PSP_GUID = "f13a7218-96c4-4a51-91bb-3d19d567136a";*/

		    // producao
			$MERCHANT_GUID = "9817f122-f441-487e-b575-d9ebd0b8e79c";
		    $PSP_GUID = "be313d13-b7a6-4e2c-b703-0ddc8a005122";
		    $API_KEY_PAYMENT = "a67818c911164fdd9794ceabdf2c594b";
		   	$API_KEY_MARKETING = "03bcea2501f3425fabf7d362bc3abefc";

			$SANDBOX_GATEWAY_URL = "https://api-staging.oney.io/staging";
		    $PRODUCTION_GATEWAY_URL = "https://api.oney.io";

		    $FacilyPayURL = "/payments/v1/purchase/facilypay_url";
		    $Oneycardpayment = "/payments/v1/purchase/card_payment_url";

			$Oney_confirm_url		 ="/payments/v1/purchase/psp_guid/{psp_guid}/merchant_guid/{merchant_guid}/reference/{reference}/action/confirm";
			$Oney_cancel_url 		 ="/payments/v1/purchase/psp_guid/{psp_guid}/merchant_guid/{merchant_guid}/reference/{reference}/action/cancel";
			$Oney_get_payment_status ="/payments/v1/purchase/psp_guid/{psp_guid}/merchant_guid/{merchant_guid}/reference/{reference}?language_code={language_code}";



			//Vamos gravar o log das acoes para usar mais tarde
			global $DB_host,$DB_user,$DB_pass,$DB_name;

    		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		    $rs=new DataObj;
		    $sql="SELECT * from oney_logs WHERE ID_Encomenda='$n_encomenda'";
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF())
			{
				$ID_Log=$rs->db_data["ID"];
			}
			$rs->db_close();


			$sql="SELECT * FROM encomendas WHERE ID='$n_encomenda'";
			$rs->Open($hConn, $erp_DBName, $sql);
			if(!$rs->EOF())
			{
				$TotalDocComIva = round($rs->db_data["TotalDocComIva"], 2);
			}
			$rs->db_close();




			$payment = array(
		        "payment_amount"=>$TotalDocComIva
		    );

		    $postValues = array(
		        'merchant_request_id' => $ID_Log, /* Must be unique in each request */
		        'payment'    =>  $payment
		    );

			$json_string=json_encode($postValues);


			$headers[] = 'X-Oney-Authorization:'.$API_KEY_PAYMENT;
			$headers[] = 'X-Oney-Partner-Country-Code:PT';
			$headers[] = 'X-Oney-Secret:None';
		    $headers[] = 'Content-Type:application/json';

			$url = $PRODUCTION_GATEWAY_URL.$Oney_confirm_url;
			$url = str_replace('{psp_guid}', $PSP_GUID, $url);
			$url = str_replace('{merchant_guid}', $MERCHANT_GUID, $url);
			//$url = str_replace('{reference}', $n_encomenda, $url);
			$url = str_replace('{reference}', 'CMDE|'.$n_encomenda, $url);
			$url = str_replace('{language_code}', $LANGUAGE_CODE, $url);

			echo $url;

		    $curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);

			curl_setopt($curl, CURLOPT_CUSTOMREQUEST , "POST");
		    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		    curl_setopt($curl, CURLOPT_POST, true);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE);
		    curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILE);

		    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		    curl_setopt($curl, CURLOPT_HEADER, 1);
		    curl_setopt($curl, CURLINFO_HEADER_OUT, true);

		    curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
		    //Default timeout is 0 (zero) which means it never times out during transfer.
		    //curl_setopt($curl, CURLOPT_TIMEOUT_MS, 400);
		    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); //300 seconds é o default mesmo que se coloque 0
			curl_setopt($curl, CURLOPT_TIMEOUT, 20000); //timeout in 20 seconds

			curl_setopt($curl, CURLOPT_LOW_SPEED_TIME, 60000);
		  	curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 30000);

			set_time_limit(0);// to infinity

		    $tentativas=0;
		    $data = curl_exec($curl);


		    $sql="UPDATE oney_logs set Json2='".addslashes($data)."' WHERE ID = '$ID_Log' ";
			$rs=Runquery($db_link, $DBName, $sql);

			if(substr_count($data,'FUNDED'))return 1;



		}
		return 0;
	}

?>