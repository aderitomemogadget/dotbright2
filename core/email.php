<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function send_message_admin($subject,$message,$admin_idb=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql="SELECT * from logins where ID='$admin_idb'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())$email=$rs->db_data["email"];
	$rs->db_close();
	if($admin_id)$admin_idb=$admin_id;
	if($admin_idb)
	{
		$sql="SELECT * from logins where ID='$admin_idb'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())$email=$rs->db_data["email"];
		$rs->db_close();
	}
	$message=clean_var($message);
	$sql="INSERT INTO mensagens (ID_From, ID_Cliente, Mensagem,folder,Assunto,Data) values ('0','$admin_id','$message','','$subject','".date("Y-m-d H:i:s")."')";
	$res=Runquery($db_link,$DB_name,$sql);
	// if($email)send_email($email,$subject,$subject."<br><br>".$message);
}

function send_email($xmail='', $xassunto='', $xmsg='', $xheaders='', $xtype=1, $attach=array())
{
	$err=true;
	if($xmail)
	{
		$array_mails=explode(',',$xmail);
		if(!count($array_mails))
		{
			//if(!send_phpmailer_v2($xmail, $xassunto, $xmsg, $xheaders, $xtype, $attach)) $err=false;
			if(!email_background($xmail, $xassunto, $xmsg, $xheaders, $xtype, $attach)) $err=false;
		}
		else
		{
			for($i=0;$i<count($array_mails);$i++)
			{
				if(!send_phpmailer_v2($array_mails[$i], $xassunto, $xmsg, $xheaders, $xtype, $attach)) $err=false;
				//if(!email_background($array_mails[$i], $xassunto, $xmsg, $xheaders, $xtype, $attach)) $err=false;
			}
		}
		return $err;
	}
}

function send_email_rma($xmail='',$xassunto='',$xmsg='',$xheaders='',$xtype=1)
{
	$err=true;
	if($xmail)
	{
		$array_mails=explode(',',$xmail);
		if(!count($array_mails))
		{
			//if(!email_standard($xmail,$xassunto,$xmsg,$xheaders,$xtype)) $err=false;
			if(!send_phpmailer_v2_rma($xmail,$xassunto,$xmsg,$xheaders,$xtype)) $err=false;
		}
		else
		{
			for($i=0;$i<count($array_mails);$i++)
			{
				//if(!email_standard($array_mails[$i],$xassunto,$xmsg,$xheaders,$xtype)) $err=false;
				if(!send_phpmailer_v2_rma($array_mails[$i],$xassunto,$xmsg,$xheaders,$xtype)) $err=false;
			}
		}
		return $err;
	}
}

function email_background($xmail, $xassunto, $message, $headers="", $xtype=1, $SMTP_Email='', $attach='')
{
	$post = array('Email'=> $xmail,'Subject'=> $xassunto,'Body'=> $message);
	$string=serialize($post);
	$file='email_'.uniqid("");

	global $site_shell_url;

	//echo PHP.' '.$site_shell_url.'core/api/email/send.php '.$site_shell_url.'files/tmp/'.$file.' >/dev/null 2>&1 &';
	//die();
	file_put_contents($site_shell_url.'files/tmp/'.$file, $string);
	exec(PHP.' '.$site_shell_url.'core/api/email/send.php '.$site_shell_url.'files/tmp/'.$file.' >/dev/null 2>&1 &');
	return true;
}
function send_phpmailer_v2_rma($to, $subject, $message, $headers='', $xtype=1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$message=str_replace('"','\"',$message);
	$sql="INSERT INTO email_queue (Email, Subject, Message) values ('$to','$subject','$message')";
	$res=Runquery($db_link,$DB_name,$sql);


	$path = dirname( __FILE__ ) . '/../core/';
	exec('/usr/bin/php '.$path.'crons/process_email_queue.php > /dev/null &');


	return true;



	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/PHPMailer.php';
	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/Exception.php';
	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/SMTP.php';
	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/OAuth.php';

	$mail = new PHPMailer(true);
	/*try {*/
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)
		$mail->SMTPDebug = 0;                               // Enable verbose debug output
		$mail->isSMTP();                                    // Set mailer to use SMTP
		$mail->Host     = $GLOBALS['Default_SMTP'];
		$mail->SMTPAuth = true;
		$mail->Username = $GLOBALS['Default_SMTP_login'];
		$mail->Password = $GLOBALS['Default_SMTP_pass'];
		$mail->From     = $GLOBALS['Default_SMTP_Email_rma'];
		$mail->FromName = $GLOBALS['FromName'];
		$mail->SMTPSecure = "tls";
		$mail->Port=($GLOBALS['Default_SMTP_porta'] ? $GLOBALS['Default_SMTP_porta'] : 587);

		//Recipients
		$mail->setFrom($GLOBALS['Default_SMTP_Email_rma'],"Dotbright - loja online");
		$mail->addAddress($to);

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->send();
		return true;
	/*}
	catch (Exception $e)
	{
	    return false;
	}*/
}
function email_standard($xmail="",$xassunto="",$xmsg="",$xheaders="",$xtype=1)
{
	global $default_folder,$email_sender;
	if($xmail)
	{
		if(strtoupper(substr(PHP_OS,0,3)=='WIN')){$eol="\r\n";} elseif(strtoupper(substr(PHP_OS,0,3)=='MAC')){$eol="\r";}else{$eol="\n";}
		$now=uniqid("");
		$headers="";
		$headers.='From: <'.$email_sender.'>'.$eol;
		//$headers.='Content-Type: text/html; charset=UTF-8'.$eol;
		$headers.='Reply-To: '.$email_sender.$eol;
		$headers.='Return-Path: '.$email_sender.$eol;
		$headers.="Message-Id: <".date("YmdHis").".".$now."@".$_SERVER['HTTP_HOST'].">".$eol;
		$headers.="X-Mailer: PHP v".phpversion().$eol;
		$mime_boundary=md5(time());
		$headers.='MIME-Version: 1.0'.$eol; // é necessario ter uma linha de intervalo
		$headers.="Content-Type: multipart/alternative; charset=utf-8; boundary=\"".$mime_boundary."\"".$eol;
		$message =$eol."--$mime_boundary".$eol."Content-type: text/plain; charset=\"UTF-8\"".$eol."Content-transfer-encoding: 8bit".$eol.$eol.(strip_tags($xmsg)).$eol;
		$message.=$eol."--$mime_boundary".$eol."Content-type: text/html;  charset=\"UTF-8\"".$eol."Content-transfer-encoding: 8bit".$eol.$eol.$xmsg.$eol."--$mime_boundary--";

		return @mail($xmail,$xassunto,$message,$headers);
	}
}
function send_phpmailer_v2($to, $subject, $message, $headers, $xtype, $attach=array())
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$message=str_replace('"','\"',$message);
	$sql="INSERT INTO email_queue (Email, Subject, Message) values ('$to','$subject','$message')";
	$res=Runquery($db_link,$DB_name,$sql);


	$path = dirname( __FILE__ ) . '/../core/';
	exec('/usr/bin/php '.$path.'crons/process_email_queue.php > /dev/null &');


	return true;



	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/PHPMailer.php';
	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/Exception.php';
	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/SMTP.php';
	include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/OAuth.php';

	$mail = new PHPMailer(true);
	/*try {*/
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)
		$mail->SMTPDebug = 0;                               // Enable verbose debug output
		$mail->isSMTP();                                    // Set mailer to use SMTP
		$mail->Host     = $GLOBALS['Default_SMTP'];
		$mail->SMTPAuth = true;
		$mail->Username = $GLOBALS['Default_SMTP_login'];
		$mail->Password = $GLOBALS['Default_SMTP_pass'];
		$mail->From     = $GLOBALS['Default_SMTP_Email_rma'];
		$mail->FromName = $GLOBALS['FromName'];
		$mail->SMTPSecure = "tls";
		$mail->Port=($GLOBALS['Default_SMTP_porta'] ? $GLOBALS['Default_SMTP_porta'] : 587);

		//Recipients
		$mail->setFrom($GLOBALS['Default_SMTP_Email_rma'],"Dotbright - loja online");
		$mail->addAddress($to);

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->send();
		return true;
	/*}
	catch (Exception $e)
	{
	    return false;
	}*/
}
function send_phpmailer_v2___($to, $subject, $message, $headers, $xtype, $attach=array())
{
	if($GLOBALS['dev']==1)$to='aderito@memogadget.com';

	//require_once($GLOBALS['site_shell_url'].'external_api/PHPMailer_5.2.2/class.phpmailer.php');

	require_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/PHPMailer.php';
	require_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/Exception.php';
	require_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/SMTP.php';
	require_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/OAuth.php';



	$mail=new PHPMailer();
	if($xtype) $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	
//	$mail->SMTPDebug = 2;
	
	$mail->WordWrap = 50;// Definição de quebra de linha
	$mail->SMTP_PORT = (defined('SMTP_PORTA') && SMTP_PORTA != '' ? SMTP_PORTA : 465);
	$mail->IsSMTP();// send via SMTP
	$mail->SMTPAuth = true;// 'true' para autenticação
	$mail->Mailer = "smtp";//Usando protocolo SMTP

	$mail->SMTPSecure = (defined('SMTPSecure') && SMTPSecure != '' ? SMTPSecure : ''); // Endereço do servidor SMTP
	$mail->Host = (defined('SMTP_HOST') && SMTP_HOST != '' ? SMTP_HOST : $GLOBALS['Default_SMTP']); // Endereço do servidor SMTP
	$mail->Username = (defined('SMTP_LOGIN') && SMTP_LOGIN != '' ? SMTP_LOGIN : $GLOBALS['Default_SMTP_login']); // Usuário do servidor SMTP
	$mail->Password = (defined('SMTP_PASS') && SMTP_PASS != '' ? SMTP_PASS : $GLOBALS['Default_SMTP_pass']); // Senha do servidor SMTP

	$mail->From = (defined('SMTP_EMAIL') && SMTP_EMAIL != '' ? SMTP_EMAIL : $GLOBALS['Default_SMTP_Email']); // Seu e-mail
	$mail->FromName = (defined('FROM_NAME') && FROM_NAME != '' ? FROM_NAME : $GLOBALS['FromName']); // Seu nome

	if(!empty($attach)) $mail->AddAttachment($attach[0], $attach[1]);

	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->CharSet='utf-8'; // Charset da mensagem (opcional)
	$mail->AddAddress($to);
	$mail->MsgHTML($message);
	$mail->Subject =$subject; // Assunto da mensagem
	$mail->AltBody=$message;

	$enviado=$mail->Send();
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();
	
	
//	die();

	if ($enviado)return true;
	else
	{
		echo '<b>Erro!</b><br />' . $mail->ErrorInfo;
		return false;
	}
}

function remove_caracteres($str)
{
	$valores=array("(",")","°","˛","+","˝","%","«","»","?");	$str=str_replace($valores,'',$str);
	$valores=array(" ","'","/","&",":",";","`","´","~","^","¨","_","+",",",".","+","|","\\",".");	$str=str_replace($valores,' ',$str);
	$str=str_replace('--','',$str);
	$str=str_replace('!!!','!',$str);
	$str=str_replace('!!','!',$str);
	$valores=array("á","ŕ","ă","â","ä"); 	$str=str_replace($valores,'a',$str);
	$valores=array("é","č","ę","ë"); 		$str=str_replace($valores,'e',$str);
	$valores=array("í","ě","î","ď"); 		$str=str_replace($valores,'i',$str);
	$valores=array("ó","ň","ő","ô","ö"); 	$str=str_replace($valores,'o',$str);
	$valores=array("ú","ů","ű","ü"); 		$str=str_replace($valores,'u',$str);
	$valores=array("ń"); 		$str=str_replace($valores,'n',$str);
	$valores=array("ç"); 		$str=str_replace($valores,'c',$str);
	$valores=array("Á","Ŕ","Ă","Â","Ä"); 	$str=str_replace($valores,'A',$str);
	$valores=array("É","Č","Ę","Ë"); 		$str=str_replace($valores,'E',$str);
	$valores=array("Í","Ě","Î","Ď"); 		$str=str_replace($valores,'I',$str);
	$valores=array("Ó","Ň","Ő","Ô","Ö"); 	$str=str_replace($valores,'O',$str);
	$valores=array("Ú","Ů","Ű","Ü"); 		$str=str_replace($valores,'U',$str);
	$valores=array("Ń"); 		$str=str_replace($valores,'N',$str);
	$valores=array("Ç"); 		$str=str_replace($valores,'C',$str);
	return $str;
}
/**
Email validation function. Thanks to http://www.linuxjournal.com/article/9585
*/
function validEmail($email)
{
   $isValid=true;
   $atIndex=strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
	  $isValid=false;
   }
   else
   {
	  $domain=substr($email, $atIndex+1);
	  $local=substr($email, 0, $atIndex);
	  $localLen=strlen($local);
	  $domainLen=strlen($domain);
	  if ($localLen < 1 || $localLen > 64)
	  {
		 // local part length exceeded
		 $isValid=false;
	  }
	  else if ($domainLen < 1 || $domainLen > 255)
	  {
		 // domain part length exceeded
		 $isValid=false;
	  }
	  else if ($local[0] == '.' || $local[$localLen-1] == '.')
	  {
		 // local part starts or ends with '.'
		 $isValid=false;
	  }
	  else if (preg_match('/\\.\\./', $local))
	  {
		 // local part has two consecutive dots
		 $isValid=false;
	  }
	  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	  {
		 // character not valid in domain part
		 $isValid=false;
	  }
	  else if (preg_match('/\\.\\./', $domain))
	  {
		 // domain part has two consecutive dots
		 $isValid=false;
	  }
	  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\",'',$local)))
	  {
		 // character not valid in local part unless
		 // local part is quoted
		 if (!preg_match('/^"(\\\\"|[^"])+"$/',
			 str_replace("\\\\",'',$local)))
		 {
			$isValid=false;
		 }
	  }
	  if ($isValid && function_exists('checkdnsrr'))
	  {
		if (!(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
		 // domain not found in DNS
		 $isValid=false;
	   }
	  }
   }
   return $isValid;
}


function sendIcalEmail($from_name,$from_address,$destname,$email,$meeting_date,$meeting_name,$meeting_duration)
{
	$subject = "Meeting Booking"; //Doubles as email subject and meeting subject in calendar
	$meeting_description = "Here is a brief description of my meeting\n\n";
	$meeting_location = "My Office"; //Where will your meeting take place

	//Convert MYSQL datetime and construct iCal start, end and issue dates
	$meetingstamp = strtotime($meeting_date . " UTC");
	$dtstart= gmdate("Ymd\THis\Z",$meetingstamp);
	$dtend= gmdate("Ymd\THis\Z",$meetingstamp+$meeting_duration);
	$todaystamp = gmdate("Ymd\THis\Z");

	//Create unique identifier
	$cal_uid = date('Ymd').'T'.date('His')."-".rand()."@mydomain.com";

	//Create Mime Boundry
	$mime_boundary = "----Meeting Booking----".md5(time());

	//Create Email Headers
	$headers = "From: ".$from_name." <".$from_address.">\n";
	$headers .= "Reply-To: ".$from_name." <".$from_address.">\n";

	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	$headers .= "Content-class: urn:content-classes:calendarmessage\n";

	//Create Email Body (HTML)
	$message .= "--$mime_boundary\n";
	$message .= "Content-Type: text/html; charset=UTF-8\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";

	$message .= "<html>\n";
	$message .= "<body>\n";
	$message .= '<p>Dear '.$destname.',</p>';
	$message .= '<p>Here is my HTML Email / Used for Meeting Description</p>';
	$message .= "</body>\n";
	$message .= "</html>\n";
	$message .= "--$mime_boundary\n";

	//Create ICAL Content (Google rfc 2445 for details and examples of usage)
	$ical =    'BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VEVENT
ORGANIZER:MAILTO:'.$from_address.'
DTSTART:'.$dtstart.'
DTEND:'.$dtend.'
LOCATION:'.$meeting_location.'
TRANSP:OPAQUE
SEQUENCE:0
UID:'.$cal_uid.'
DTSTAMP:'.$todaystamp.'
DESCRIPTION:'.$meeting_description.'
SUMMARY:'.$subject.'
PRIORITY:5
CLASS:PUBLIC
END:VEVENT
END:VCALENDAR';

	$message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST\n';
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
	$message .= $ical;

	//SEND MAIL
	$mail_sent = @mail( $email, $subject, $message, $headers );

	if($mail_sent)     {
		return true;
	} else {
		return false;
	}
}

function sendgrid_email($xmail, $xassunto, $message, $headers, $xtype=1, $SMTP_Email='', $attach=array())
{
	$url = 'https://api.sendgrid.com/';
	$user = 'yyyy';
	$pass = 'xxxx';
	$json_string = array('to' => array($xmail), 'category' => 'noreply');
	$xassunto = utf8_encode($xassunto);
	$message =  utf8_encode($message);

	if(!empty($attach))
	{
		$params = array(
			'api_user'  => $user,
			'api_key'   => $pass,
			'x-smtpapi' => json_encode($json_string),
			'to'        => $xmail,
			'subject'   => $xassunto,
			'html'      => $message,
			'fromname'  => 'AAAA',
			'from'      => 'nl@AAAA.pt',
			'files['.$attach[1].']' => '@'.$attach[0]
		  );

	}
	else
	{
		$params = array(
			'api_user'  => $user,
			'api_key'   => $pass,
			'x-smtpapi' => json_encode($json_string),
			'to'        => $xmail,
			'subject'   => $xassunto,
			'html'      => $message,
			'fromname'  => 'AAAA',
			'from'      => 'nl@AAAA.pt'
		  );
	}
	$request =  $url.'api/mail.send.json';

	// Generate curl request
	$session = curl_init($request);
	// Tell curl to use HTTP POST
	curl_setopt ($session, CURLOPT_POST, true);
	// Tell curl that this is the body of the POST
	curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
	// Tell curl not to return headers, but do return the response
	curl_setopt($session, CURLOPT_HEADER, false);
	// Tell PHP not to use SSLv3 (instead opting for TLS)
	curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

	// obtain response
	$response = curl_exec($session);
	curl_close($session);

	// print everything out
	print_r($response);

	if(substr_count($response, 'success')) return true;
	else return false;
}