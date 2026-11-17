<?php
session_start();
if($n_encomenda)
{
    global $DB_host,$DB_user,$erp_db_password,$DB_pass,$lang,$ss_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$erp_db_password);
    $dados=new DataObj;
    // Encomenda
	$sql="SELECT * FROM encomendas WHERE ID2='$n_encomenda'";
	$dados->Open($db_link, $DB_pass, $sql);
	$enc_nome="Clickfiel Online Nº $n_encomenda";

	$TotalDocComIva = round($dados->aFields["TotalDocComIva"], 2);

	$valor = $TotalDocComIva;
   	$sql="SELECT * FROM logins WHERE ID='$ss_id'";
	$dados->open($db_link, $DB_pass, $sql);
	$firstname=$dados->aFields["Nome"];
	$email=$dados->aFields["username"];
}

//Configuration Settings
$paypal[business]="mail@Clickfiel.pt";
$paypal[site_url]="http://www.Clickfiel.pt/";
$paypal[image_url]="templates/logo2.jpg";//imagem para o logotipo da empresa
$paypal[success_url]="pgm_paypal/ipn/ipn.php";
$paypal[cancel_url]="pgm_paypal/ipn/ipn.php";
$paypal[notify_url]="pgm_paypal/ipn/ipn.php";
$paypal[return_method]="1"; //1=GET 2=POST
$paypal[currency_code]="EUR"; //[USD,GBP,JPY,CAD,EUR]
$paypal[lc]="US";//US

$paypal[url]="http://www.paypal.com/cgi-bin/webscr";
$paypal[post_method]="fso"; //fso=fsockopen(); curl=curl command line libCurl=php compiled with libCurl support
$paypal[curl_location]="/usr/local/bin/curl";

$paypal[bn]="Clickfiel";
$paypal[cmd]="_xclick";

//Payment Page Settings
$paypal[display_comment]="0"; //0=yes 1=no
$paypal[comment_header]="Comments";
$paypal[continue_button_text]="Continue >>";
$paypal[background_color]=""; //""=white 1=black
$paypal[display_shipping_address]=""; //""=yes 1=no
$paypal[display_comment]="1"; //""=yes 1=no


//Product Settings
$paypal[item_name]=$enc_nome; //"$_POST[item_name]";
$paypal[item_number]=$n_encomenda; //"$_POST[item_number]";
$paypal[amount]=$valor; //"$_POST[amount]";
$paypal[quantity]=1; //$qtd; //"$_POST[quantity]";
$paypal[edit_quantity]=""; //1=yes ""=no

//Shipping and Taxes
$paypal[shipping_amount]=$portes;
$paypal[shipping_amount_per_item]="";
$paypal[handling_amount]=$taxa; //"";
$paypal[custom_field]="";

//Customer Settings
$paypal[firstname]=$firstname; //"$_POST[firstname]";
$paypal[email]=$email;

?>