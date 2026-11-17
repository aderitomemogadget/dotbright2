<?php

define("URL_PATH","https://www.on4tech.pt/external_api/pagamentos/pgm_newnote/pgm_mbway/");
//define("TOKEN","PCD1G@56");
define("TOKEN","eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOjIsImlzcyI6Ik1CX1dBWV9HQVRFV0FZIiwic3ViIjoyLCJpYXQiOiIyMDE1LTEwLTE4VDExOjI2OjIwLjMxNVoiLCJyb2wiOjF9.hZaJTT4bbrAo77WfXlmuWOGiiyYiWrl42sPom5DJUjc");
define("WS_HTTP_URL","https://mbway.pt/Merchant/requestFinancialOperationWS");
//define("TOKEN","UonAzhubqpA7");
//define("WS_HTTP_URL","https://qly.mbway.pt/Merchant/requestFinancialOperationWS");
define("SHELL_PATH","/home/www/on4tech.pt/public_html/external_api/pagamentos/");
define("SOAP_NAMESPACE","http://schemas.xmlsoap.org/ws/2012/12/secext/");
define("SHOW_ERRORS",2);
date_default_timezone_set("UTC");

if(SHOW_ERRORS == 2)
{
	error_reporting(E_ERROR || E_WARNING);
	ini_set('display_errors', 1);
}
else
{
	error_reporting(E_ALL);
	ini_set('display_errors', 0);
}



error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


ini_set("soap.wsdl_cache_enabled", "0");
/*require_once(SHELL_PATH.'includes/definitions.inc.php');
require_once(SHELL_PATH.'includes/obj_php.inc.php');
require_once(SHELL_PATH.'includes/class.phpmailer.php');
require_once(SHELL_PATH.'includes/class.smtp.php');
require_once(SHELL_PATH.'includes/mail.inc.php');*/

require_once(SHELL_PATH.'pgm_newnote/nusoap/nusoap.php');
