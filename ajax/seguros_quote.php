<?php
	set_time_limit(0);
	$path = dirname( __FILE__ ).'/../core/';
//	require_once $path.'core_files.php';

	ini_set('display_errors',0);
	ini_set('display_startup_errors',0);


	$get_data = authenticate('https://api-staging.planicare.pt/api/Authentication/Authenticate');
	$token=substr($get_data, strpos($get_data,'"access_token":"')+16,strlen($get_data));
	$token=substr($token,0,strpos($token,'"'));



	$x_op0 = clean_var_api($_POST['m_op0']);if($x_op0==1)$m_op0=true;else $m_op0=false;
	$x_op1 = clean_var_api($_POST['m_op1']);
	$x_op2 = clean_var_api($_POST['m_op2']);
	$x_op3 = clean_var_api($_POST['m_op3']);
	$x_op4 = clean_var_api($_POST['m_op4']);
	$x_op5 = clean_var_api($_POST['m_op5']);
	if($x_op5==1)$m_op5=0;
	if($x_op5==2)$m_op5=1;

	$x_op6 = clean_var_api($_POST['m_op6']);

	if($x_op6==1)$m_op6=1;
	if($x_op6==2)$m_op6=2;
	if($x_op6==3)$m_op6=3;
	if($x_op6==4)$m_op6=4;



	$x_op7 = clean_var_api($_POST['m_op7']);
	$x_op8 = clean_var_api($_POST['m_op8']);
	$x_op9 = clean_var_api($_POST['m_op9']);
	$x_op9b = clean_var_api($_POST['m_op9b']);
	$x_op10 = clean_var_api($_POST['m_op10']);
	$x_op11 = clean_var_api($_POST['m_op11']);
	$x_op12 = clean_var_api($_POST['m_op12']);
	$x_op13 = clean_var_api($_POST['m_op13']);
	$x_op14 = clean_var_api($_POST['m_op14']);//id do seguro
	$x_op15 = clean_var_api($_POST['m_op15']);//tipo de pagamento

	/*{"recurrence":1,"recurrenceName":"Mensal","premium":15.0},
	{"recurrence":4,"recurrenceName":"Trimestral","premium":44.11},
	{"recurrence":0,"recurrenceName":"Anual","premium":172.99},
	{"recurrence":3,"recurrenceName":"Semestral","premium":86.49}],*/

	if($x_op15==1)$m_op15=1;//mens
	if($x_op15==2)$m_op15=4;//trim
	if($x_op15==3)$m_op15=3;//semestre
	if($x_op15==4)$m_op15=0;//ano
	$x_op16 = clean_var_api($_POST['m_op16']);//simulationId
	$x_op17 = clean_var_api($_POST['m_op17']);//IBAN
	$x_op18 = clean_var_api($_POST['m_op18']);//numero de pessoas
	$x_op21 = clean_var_api($_POST['m_op21']);//profissao


	if($x_op17!='')
	{
		$payment=array(
	    "recurrence"	 => intval($m_op15),
	    "paymentMethod"	 => 2,
	    "iban"	 => $x_op17
		);
	}
	else
	{
		$payment=array(
	    "recurrence"	 => intval($m_op15),
	    "paymentMethod"	 => 1
		);
	}

	if($x_op18<=1)
	{
		if($m_op0==true)
		{
			$data = array(
			"simulationId" => intval($x_op16),
			"planId" => intval($x_op14),
			"startDate" => $x_op1.'T00:00:00',
			"holder" => array(
				"nif" => $x_op3,
				"fullName" => $x_op2,
				"birthDate"	 => $x_op4,
			    "sexEnum"	 => intval($m_op5),
			    "addressLine"	 => $x_op10,
			    "city"	 => $x_op13,
			    "telephone"	 => $x_op8,
			    "cellPhone"	 => $x_op8,
			    "eMail"	 => $x_op7,
			    "policyHolder"	 => true,
			    "occupation"	 => $x_op21,
			    "maritalStatus"	 => intval($m_op6),
			    "postalCode"	 => $x_op9,
			    "postalCodeExt"	 => $x_op9b
			),
			"holderIsBeneficiary" => $m_op0,
			"payment" => $payment
			);
		}
		else
		{
			$x=1;
			//O tomador não vai pertencer
			$x_op1_ = clean_var_api($_POST['m_op1_'.$x]);
			$x_op2_ = clean_var_api($_POST['m_op2_'.$x]);
			$x_op3_ = clean_var_api($_POST['m_op3_'.$x]);
			$x_op4_ = clean_var_api($_POST['m_op4_'.$x]);
			$x_op5_ = clean_var_api($_POST['m_op5_'.$x]);
			$x_op6_ = clean_var_api($_POST['m_op6_'.$x]);
			$x_op7_ = clean_var_api($_POST['m_op7_'.$x]);
			$x_op8_ = clean_var_api($_POST['m_op8_'.$x]);
			$x_op9_ = clean_var_api($_POST['m_op9_'.$x]);//codigo postal
			$x_op9b_ = clean_var_api($_POST['m_op9b_'.$x]);//codigo postal
			$x_op10_ = clean_var_api($_POST['m_op10_'.$x]);
			$x_op11_ = clean_var_api($_POST['m_op11_'.$x]);
			$x_op12_ = clean_var_api($_POST['m_op12_'.$x]);
			$x_op13_ = clean_var_api($_POST['m_op13_'.$x]);
			$x_op14_ = clean_var_api($_POST['m_op14_'.$x]);//profissao
			$x_op20_ = clean_var_api($_POST['m_op20_'.$x]);

			if($x_op5_==1)$m_op5_=0;
			if($x_op5_==2)$m_op5_=1;

			$firstBeneficiary=array(
		    "nif" => $x_op3_,
			"fullName" => $x_op2_,
			"birthDate"	 => $x_op4_,
		    "sexEnum"	 => intval($m_op5_),
		    "addressLine"	 => $x_op10_,
		    "city"	 => $x_op13_,
		    "telephone"	 => $x_op8_,
		    "cellPhone"	 => $x_op8_,
		    "eMail"	 => $x_op7_,
		    "policyHolder"	 => true,
		    "occupation"	 => $x_op14_,
		    "maritalStatus"	 => intval($x_op6_),
		    "postalCode"	 => $x_op9_,
		    "postalCodeExt"	 => $x_op9b_
			);
			$data = array(
			"simulationId" => intval($x_op16),
			"planId" => intval($x_op14),
			"startDate" => $x_op1.'T00:00:00',
			"holder" => array(
				"nif" => $x_op3,
				"fullName" => $x_op2,
				"birthDate"	 => $x_op4,
			    "sexEnum"	 => intval($m_op5),
			    "addressLine"	 => $x_op10,
			    "city"	 => $x_op13,
			    "telephone"	 => $x_op8,
			    "cellPhone"	 => $x_op8,
			    "eMail"	 => $x_op7,
			    "policyHolder"	 => true,
			    "occupation"	 => $x_op21,
			    "maritalStatus"	 => intval($m_op6),
			    "postalCode"	 => $x_op9,
			    "postalCodeExt"	 => $x_op9b
			),
			"holderIsBeneficiary" => $m_op0,
			"firstBeneficiary" => $firstBeneficiary,
			"payment" => $payment
			);

		}
	}
	else
	{
		$x=1;
		if($m_op0==true)
		{
			//O tomador vai pertencer
			$firstBeneficiary=array(
		    "nif" => $x_op3,
			"fullName" => $x_op2,
			"birthDate"	 => $x_op4,
		    "sexEnum"	 => intval($m_op5),
		    "addressLine"	 => $x_op10,
		    "city"	 => $x_op13,
		    "telephone"	 => $x_op8,
		    "cellPhone"	 => $x_op8,
		    "eMail"	 => $x_op7,
		    "policyHolder"	 => true,
		    "occupation"	 => $x_op21,
		    "maritalStatus"	 => intval($m_op6),
		    "postalCode"	 => $x_op9,
		    "postalCodeExt"	 => $x_op9b
			);
			$x_op18=$x_op18-1;//vamos retirar o ultimo
		}
		else
		{
			//O tomador nao vai pertencer
			$x_op1_ = clean_var_api($_POST['m_op1_'.$x]);
			$x_op2_ = clean_var_api($_POST['m_op2_'.$x]);
			$x_op3_ = clean_var_api($_POST['m_op3_'.$x]);
			$x_op4_ = clean_var_api($_POST['m_op4_'.$x]);
			$x_op5_ = clean_var_api($_POST['m_op5_'.$x]);
			$x_op6_ = clean_var_api($_POST['m_op6_'.$x]);
			$x_op7_ = clean_var_api($_POST['m_op7_'.$x]);
			$x_op8_ = clean_var_api($_POST['m_op8_'.$x]);
			$x_op9_ = clean_var_api($_POST['m_op9_'.$x]);//codigo postal
			$x_op9b_ = clean_var_api($_POST['m_op9b_'.$x]);//codigo postal
			$x_op10_ = clean_var_api($_POST['m_op10_'.$x]);
			$x_op11_ = clean_var_api($_POST['m_op11_'.$x]);
			$x_op12_ = clean_var_api($_POST['m_op12_'.$x]);
			$x_op13_ = clean_var_api($_POST['m_op13_'.$x]);
			$x_op14_ = clean_var_api($_POST['m_op14_'.$x]);
			$x_op20_ = clean_var_api($_POST['m_op20_'.$x]);

			if($x_op5_==1)$m_op5_=0;
			if($x_op5_==2)$m_op5_=1;

			$firstBeneficiary=array(
		    "nif" => $x_op3_,
			"fullName" => $x_op2_,
			"birthDate"	 => $x_op4_,
		    "sexEnum"	 => intval($m_op5_),
		    "addressLine"	 => $x_op10_,
		    "city"	 => $x_op13_,
		    "telephone"	 => $x_op8_,
		    "cellPhone"	 => $x_op8_,
		    "eMail"	 => $x_op7_,
		    "policyHolder"	 => true,
		    "occupation"	 => $x_op14_,
		    "maritalStatus"	 => intval($x_op6_),
		    "postalCode"	 => $x_op9_,
		    "postalCodeExt"	 => $x_op9b_
			);

			$x++;
		}

		$otherBeneficiaries=array();
		$z=0;
		while($x<=$x_op18)
		{
			$x_op1_ = clean_var_api($_POST['m_op1_'.$x]);
			$x_op2_ = clean_var_api($_POST['m_op2_'.$x]);
			$x_op3_ = clean_var_api($_POST['m_op3_'.$x]);
			$x_op4_ = clean_var_api($_POST['m_op4_'.$x]);
			$x_op5_ = clean_var_api($_POST['m_op5_'.$x]);
			$x_op6_ = clean_var_api($_POST['m_op6_'.$x]);
			$x_op7_ = clean_var_api($_POST['m_op7_'.$x]);
			$x_op8_ = clean_var_api($_POST['m_op8_'.$x]);
			$x_op9_ = clean_var_api($_POST['m_op9_'.$x]);
			$x_op9b_ = clean_var_api($_POST['m_op9b_'.$x]);
			$x_op10_ = clean_var_api($_POST['m_op10_'.$x]);
			$x_op11_ = clean_var_api($_POST['m_op11_'.$x]);
			$x_op12_ = clean_var_api($_POST['m_op12_'.$x]);
			$x_op13_ = clean_var_api($_POST['m_op13_'.$x]);
			$x_op14_ = clean_var_api($_POST['m_op14_'.$x]);
			$x_op20_ = clean_var_api($_POST['m_op20_'.$x]);

			if($x_op5_==1)$m_op5_=0;
			if($x_op5_==2)$m_op5_=1;

			$otherBeneficiaries[$z]=array(
				"client" => array(
				    "nif" => $x_op3_,
					"fullName" => $x_op2_,
					"birthDate"	 => $x_op4_,
				    "sexEnum"	 => intval($m_op5_),
				    "addressLine"	 => $x_op10_,
				    "city"	 => $x_op13_,
				    "telephone"	 => $x_op8_,
				    "cellPhone"	 => $x_op8_,
				    "eMail"	 => $x_op7_,
				    "policyHolder"	 => true,
				    "occupation"	 => $x_op14_,
				    "maritalStatus"	 => intval($x_op6_),
				    "postalCode"	 => $x_op9_,
				    "postalCodeExt"	 => $x_op9b_
					),
				"relationShip" => intval($x_op20_)
			);

			$x++;
			$z++;
		}



		$data = array(
			"simulationId" => intval($x_op16),
			"planId" => intval($x_op14),
			"startDate" => $x_op1.'T00:00:00',
			"holder" => array(
				"nif" => $x_op3,
				"fullName" => $x_op2,
				"birthDate"	 => $x_op4,
			    "sexEnum"	 => intval($m_op5),
			    "addressLine"	 => $x_op10,
			    "city"	 => $x_op13,
			    "telephone"	 => $x_op8,
			    "cellPhone"	 => $x_op8,
			    "eMail"	 => $x_op7,
			    "policyHolder"	 => true,
			    "occupation"	 => $x_op21,
			    "maritalStatus"	 => intval($m_op6),
			    "postalCode"	 => $x_op9,
			    "postalCodeExt"	 => $x_op9b
			),
			"holderIsBeneficiary" => $m_op0,
			"firstBeneficiary" => $firstBeneficiary,
			"otherBeneficiaries" => $otherBeneficiaries,
			"payment" => $payment
			);
	}


	$data_string = json_encode($data);

	$msg = callAPI_POST('https://api-staging.planicare.pt/api/Proposal/Issue', $token, $data_string);


	$erro='';

	if(substr_count($msg,'validationErrors')>0)
	{
		$msg=substr($msg, strpos($msg,'"validationErrors"')+strlen('"validationErrors"'),strlen($msg));
		$msg=substr($msg, strpos($msg,'"message":"')+strlen('"message":"'),strlen($msg));
		$msg=substr($msg, 0,strpos($msg,'"'));
		$erro=(preg_replace( "/\r|\n/", "", $msg));
	}

	if($erro!='')echo '1|'.$erro;
	else
	{
		/*var_dump($msg);
		die();*/
		$url='';
		$msg=substr($msg, strpos($msg,'"nextURL":"')+strlen('"nextURL":"'),strlen($msg));
		$msg=substr($msg, 0,strpos($msg,'"'));
		$url=addslashes(preg_replace( "/\r|\n/", "", $msg));
		echo '0|'.$url;

		/*print_r($data_string);
		echo "--------------------";
		var_dump($msg);*/
	}

	/*print_r($data_string);
		echo "--------------------";
		var_dump($msg);*/

	function authenticate($url)
	{
		// Initialise cURL
	  	$ch = curl_init($url);
	  	$actualUrl = $url;

	  	$data = array(
		"Username" => "api@planicare.pt",
		"Password" => "1234"
		);
		$data_string = json_encode($data);


	  	curl_setopt($ch, CURLOPT_URL, $actualUrl);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type:application/json',
	        	'Content-Length: ' . strlen($data_string)
		    )
		);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		return $server_output;
	}

	function callAPI_GET($url, $token)
	{
	   // Initialise cURL
	  	$ch = curl_init($url);
	  	$actualUrl = $url;
	  	curl_setopt($ch, CURLOPT_URL, $actualUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET');
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type:application/json',
		        'Authorization: Bearer '.$token
		    )
		);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		return $server_output;
	}

	function callAPI_POST($url, $token, $data_string)
	{
	   // Initialise cURL
	  	$ch = curl_init($url);
	  	$actualUrl = $url;
	  	curl_setopt($ch, CURLOPT_URL, $actualUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type:application/json',
		        'Authorization: Bearer '.$token,
	        	'Content-Length: ' . strlen($data_string)
		    )
		);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		return $server_output;
	}

	function clean_var_api($variable)
	{
	    $variable = stripslashes(strip_tags(trim(rtrim($variable))));
	    $variable = addslashes($variable);
	    return $variable;
	}
?>