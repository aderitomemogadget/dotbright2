<?php
	$path =  dirname( __FILE__ ) . '/../../../core/';

	include $path.'core_files.php';

	ini_set('display_errors', 1);
	ini_set('display_startup_errors',1);
	error_reporting(1);

	// define('COOKIE_FILE', '../img_upload/cookie.txt');


	function flush_buffers()
	{
	    ob_end_flush();
	    ob_flush();
	    flush();
	    ob_start();
	}
	function clear_text($str)
	{
	    $str=trim($str,"\t\n\r\0\x0B");

	    /* http://pt.php.net/manual/en/function.chr.php#85050 */
	    $str = str_replace(chr(130),',',$str);    // baseline single quote
	    $str = str_replace(chr(131),'NLG',$str);  // florin
	    $str = str_replace(chr(132),'"',$str);    // baseline double quote
	    $str = str_replace(chr(133),'...',$str);  // ellipsis
	    $str = str_replace(chr(134),'**',$str);   // dagger (a second footnote)
	    $str = str_replace(chr(135),'***',$str);  // double dagger (a third footnote)
	    $str = str_replace(chr(136),'^',$str);    // circumflex accent
	    $str = str_replace(chr(137),'o/oo',$str); // permile
	    $str = str_replace(chr(138),'Sh',$str);   // S Hacek
	    $str = str_replace(chr(139),'<',$str);    // left single guillemet
	    $str = str_replace(chr(140),'OE',$str);   // OE ligature
	    $str = str_replace(chr(145),"'",$str);    // left single quote
	    $str = str_replace(chr(146),"'",$str);    // right single quote
	    $str = str_replace(chr(147),'"',$str);    // left double quote
	    $str = str_replace(chr(148),'"',$str);    // right double quote
	    $str = str_replace(chr(149),'-',$str);    // bullet
	    $str = str_replace(chr(150),'-',$str);    // endash
	    $str = str_replace(chr(151),'--',$str);   // emdash
	    $str = str_replace(chr(152),'~',$str);    // tilde accent
	    $str = str_replace(chr(153),'(TM)',$str); // trademark ligature
	    $str = str_replace(chr(154),'sh',$str);   // s Hacek
	    $str = str_replace(chr(155),'>',$str);    // right single guillemet
	    $str = str_replace(chr(156),'oe',$str);   // oe ligature
	    $str = str_replace(chr(159),'Y',$str);    // Y Dieresis

		$valores=array("(",")","³","°","²","+","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","?");
	    $str=str_replace($valores,'',$str);

		$valores=array(" ","'","/","&",":",";","`","´","~","^","¨","_","+",",",".","+","|","\\","<",">","–","—","–","­","–","—","€","±","×","¿");
		for($c=123; $c<191; $c++)  $valores[]=chr($c);
	    $str=str_replace($valores,'-',$str);

		$str=str_replace('-----','-',$str);
		$str=str_replace('----','-',$str);
		$str=str_replace('---','-',$str);
		$str=str_replace('--','-',$str);

		$valores=array("á","à","ã","â","ä"); 	$str=str_replace($valores,'a',$str);
		$valores=array("é","è","ê","ë"); 		$str=str_replace($valores,'e',$str);
		$valores=array("í","ì","î","ï"); 		$str=str_replace($valores,'i',$str);
		$valores=array("ó","ò","õ","ô","ö"); 	$str=str_replace($valores,'o',$str);
		$valores=array("ú","ù","û","ü"); 		$str=str_replace($valores,'u',$str);
		$valores=array("ñ"); 		$str=str_replace($valores,'n',$str);
		$valores=array("ç"); 		$str=str_replace($valores,'c',$str);

		$valores=array("Á","À","Ã","Â","Ä"); 	$str=str_replace($valores,'A',$str);
		$valores=array("É","È","Ê","Ë"); 		$str=str_replace($valores,'E',$str);
		$valores=array("Í","Ì","Î","Ï"); 		$str=str_replace($valores,'I',$str);
		$valores=array("Ó","Ò","Õ","Ô","Ö"); 	$str=str_replace($valores,'O',$str);
		$valores=array("Ú","Ù","Û","Ü"); 		$str=str_replace($valores,'U',$str);
		$valores=array("Ñ"); 		$str=str_replace($valores,'N',$str);
		$valores=array("Ç"); 		$str=str_replace($valores,'C',$str);

		if(strlen($str) > 100) $str = substr($str,0,100);
		$str = preg_replace('/[^0-9A-Za-z\s]+/', ' ', $str);

		return $str;
	}



$n_encomenda = intval($_POST['n_encomenda']);

// $n_encomenda="202001294A173";
// $n_encomenda="202100969A1294";

$valor_devolver=round($_POST['valor'], 2);

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
    $rs2=new DataObj;

    /*$sql="SELECT * from oney_logs WHERE ID_Encomenda='$n_encomenda'";
	$rs->Open($hConn, $erp_DBName, $sql);
	if(!$rs->EOF())
	{
		$ID_Log=$rs->aFields["ID"];
	}
	$rs->close();*/

	//Vamos gravar o log das acoes para usar mais tarde
	// $sql="INSERT INTO oney_logs (ID_Encomenda, Data, Json, Estado, Erros) VALUES('".$n_encomenda."','".date("Y-m-d H:m:i")."', '', 'A PREPARAR O ENVIO PARA ONEY O CANCELAMENTO','')";
	// $res=Runquery($db_link, $DBName, $sql);
	// $ID_Log=db_get_last_insert_id($db_link);
	$sql="SELECT * FROM oney_logs WHERE ID_Encomenda='$n_encomenda'  ORDER BY ID DESC LIMIT 1";
	$rs2->db_open($db_link, $DB_name, $sql);
	$return = '';
	if(!$rs2->EOF())
	{
		$ID_oney = $rs2->db_data['ID'];
		
	}
	$rs2->db_close();


	// Encomenda
	$sql="SELECT * FROM encomendas WHERE ID='$n_encomenda'";
	$rs2->db_open($db_link, $DB_name, $sql);
	$return = '';
	if($rs2->EOF())
	{
		echo false;
		die("Erro");
	}
	$TotalDocComIva = round($rs2->db_data["TotalDocComIva"], 2);



	$valor_devolver= $TotalDocComIva;

	$n_encomenda = $n_encomenda.'A'.$ID_oney;


	$purchase = array(
        'cancellation_reason_code'    =>  0,/* 1 = fraude, 0 = cancellation */
        /*'refund_down_payment'    =>  0,*/
        'cancellation_amount' => $valor_devolver
    );

    $postValues = array(
        "language_code"=>$LANGUAGE_CODE, /* Customer language code , Used to display Oney page with customer language */
        'merchant_request_id' => $ID_oney, /* Must be unique in each request */
        'purchase'    =>  $purchase
    );

	$json_string=json_encode($postValues);

	

	$headers[] = 'X-Oney-Authorization:'.$API_KEY_PAYMENT;
	$headers[] = 'X-Oney-Partner-Country-Code:PT';
	$headers[] = 'X-Oney-Secret:None';
    $headers[] = 'Content-Type:application/json';

	$url = $PRODUCTION_GATEWAY_URL.$Oney_cancel_url;
	$url = str_replace('{psp_guid}', $PSP_GUID, $url);
	$url = str_replace('{merchant_guid}', $MERCHANT_GUID, $url);
	//$url = str_replace('{reference}', $n_encomenda, $url);
	$url = str_replace('{reference}', 'CMDE|'.$n_encomenda, $url);

	// echo $url;
	// echo '<textarea style="width:100%;height:100px">'.$json_string.'</textarea>';

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




    var_dump($data);

    $sql="UPDATE oney_logs set Json2='".addslashes($data)."' WHERE ID = '$ID_oney' ";
	$res=Runquery($db_link, $DBName, $sql);

	if($res)
	{
		echo 1;		
	}
	else
	{
		echo 0;
	}



}
else
{
	echo 0;
}