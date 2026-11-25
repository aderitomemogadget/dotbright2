<?
//die();


	include "settings.inc.php";
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);


	send_email_rma('aderito@memogadget.com', 'Estado do seu pedido de assistência técnica ', 'message');

?>