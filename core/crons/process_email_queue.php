<?php
	$path=dirname( __FILE__ ) . '/../../core/';
	require_once $path . "core_files.php";
	include $path.'core_files.php';


	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql="SELECT * from email_queue where Flag='0'";
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$ID=$rs->db_data["ID"];
		$email=$rs->db_data["Email"];
		$Subject=$rs->db_data["Subject"];
		$Message=$rs->db_data["Message"];

		envia_email_queue($ID,$email, $Subject, $Message);
		$rs->db_next();
	}
	$rs->db_close();



	function envia_email_queue($ID=0, $to='', $subject='', $message='', $headers='', $xtype=1)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$db_affected_rows;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		// Claim the message atomically so concurrent workers cannot send it twice.
		$sql="UPDATE email_queue SET Flag=1 WHERE ID=$ID AND Flag=0";
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res || $db_affected_rows != 1)
		{
			return false;
		}


		include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/PHPMailer.php';
		include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/Exception.php';
		include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/SMTP.php';
		include_once $GLOBALS['site_shell_url'].'external_api/PHPMailer-master/src/OAuth.php';

		$mail = new PHPMailer(true);
		try {
			$mail = new PHPMailer();
			$mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)
			$mail->SMTPDebug = 0;                               // Enable verbose debug output
			$mail->isSMTP();                                    // Set mailer to use SMTP
			$mail->Host     = $GLOBALS['Default_SMTP'];
			$mail->SMTPAuth = true;
			$mail->Username = $GLOBALS['Default_SMTP_login'];
			$mail->Password = $GLOBALS['Default_SMTP_pass'];
			$mail->From     = $GLOBALS['Default_SMTP_Email'];
			$mail->FromName = $GLOBALS['FromName'];
			$mail->SMTPSecure = "tls";
			$mail->Port=($GLOBALS['Default_SMTP_porta'] ? $GLOBALS['Default_SMTP_porta'] : 587);

			//Recipients
			$mail->setFrom($GLOBALS['Default_SMTP_Email'],"Dotbright - loja online");
			$mail->addAddress($to);

			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body = $message;
			if($mail->send())
			{
				return true;
			}

			throw new Exception($mail->ErrorInfo ?: 'SMTP send failed');
		}
		catch (Throwable $e)
		{
			// A failed delivery must remain pending instead of looking successful.
			Runquery($db_link,$DB_name,"UPDATE email_queue SET Flag=0 WHERE ID=$ID");
			$log_file=dirname(rtrim($GLOBALS['site_shell_url'],'/')).'/private/email-queue-error.log';
			error_log(date('Y-m-d H:i:s').' queue_id='.$ID.' '.$e->getMessage().PHP_EOL,3,$log_file);
		    return false;
		}
	}
?>
