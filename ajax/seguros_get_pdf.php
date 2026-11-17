<?php
	set_time_limit(0);
	$path = dirname( __FILE__ ).'/../core/';
	require_once $path.'core_files.php';

	ini_set('display_errors',0);
	ini_set('display_startup_errors',0);


	$get_data = authenticate('https://api-staging.planicare.pt/api/Authentication/Authenticate');
	$token=substr($get_data, strpos($get_data,'"access_token":"')+16,strlen($get_data));
	$token=substr($token,0,strpos($token,'"'));



	$x_id = clean_var_api($_POST['id']);

	$data_string = json_encode($data);
	$msg = callAPI_GET('https://api-staging.planicare.pt/api/Simulation/GetPDF?simulationId='.$x_id, $token);
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
		$url='';
		$pdf_content='';
		$msg=substr($msg, strpos($msg,'binaryContent":"')+strlen('binaryContent":"'),strlen($msg));
		$pdf_content=substr($msg, 0,strpos($msg,'"'));
		$pdf_decoded = base64_decode ($pdf_content);

		$pdf = fopen ($GLOBALS['site_shell_url'].'files/simulation'.$x_id.'.pdf','w');
		fwrite ($pdf,$pdf_decoded);
		fclose ($pdf);

		echo '0|'.$GLOBALS['site_shell_url'].'files/simulation'.$x_id.'.pdf';
	}

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