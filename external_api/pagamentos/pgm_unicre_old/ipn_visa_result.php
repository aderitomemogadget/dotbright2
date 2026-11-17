<?php
//**************************************************
//Recebe os dados sempre que há um pagamento por VISA bem sucedido
//**************************************************

$fp = fopen('php://input', 'r');
$http_body = stream_get_contents($fp);
$raw_post = serialize($_POST);
$get = serialize($_GET);
fclose($fp);
$iv_from_http_header = $_SERVER['HTTP_X_INITIALIZATION_VECTOR'];
$auth_tag_from_http_header = $_SERVER['HTTP_X_AUTHENTICATION_TAG'];
file_put_contents('logs/sibs_visa0_'.date('Y_m_d_h_i_s').'.txt',$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body.'&POST='.$raw_post.'&GET='.$get);


?><!DOCTYPE html><html><body><script type="text/javascript">window.open("https://www.on4tech.pt/p5-registo-utilizador-pt?show_compras=1&last=1","_self");</script></body></html>