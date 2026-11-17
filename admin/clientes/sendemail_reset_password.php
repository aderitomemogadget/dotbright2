<?php
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs=new Dataobj;
	$rs2=new Dataobj;

	/*ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);*/


	Runquery($db_link, $GLOBALS['DB_name'], "UPDATE clientes SET pass_gerada='1' WHERE Username='aderito@memogadget.com'");
	//die();

	//Vamos ler todos os clientes que tem de fazer reset a password
	$sql = "SELECT * FROM clientes WHERE pass_gerada=1";
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    while (!$rs->EOF())
	{
		$Cliente_ID = $rs->db_data['ID'];
echo		$m_email = $rs->db_data['Username'];
		$username = $rs->db_data['Username'];
        $nm = $rs->db_data['Nome'];
        $apl = $rs->db_data['Apelido'];
        $token = md5($username);
        Runquery($db_link, $GLOBALS['DB_name'], "UPDATE clientes SET token_pass='$token' WHERE Username='$username'");

		//$m_email='aderitofernandes@gmail.com';

			$body = array
            (
                '[username]' => $m_email,
                '[nome]' => $nm,
                '[apelido]' => $apl,
                '[token_pass]' => "Clique neste link para <a href=".SITE_HTTP_URL .seo('pages.php?page_id='.$GLOBALS['recuperar_page_id']).'?token='.$token."> Alterar a sua password</a>"
            );
			$message = template_email(13, $body);
			send_email($m_email, $message['subject'], $message['body']);


		
		$rs->db_next();
    }
    $rs->db_close();

	echo "1";
?>