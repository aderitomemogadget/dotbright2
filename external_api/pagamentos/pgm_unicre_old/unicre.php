<?php
	include "include.php";

    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

    if(isset($_GET['cancel']) && $_GET['cancel']==1)
	{
    	$token = $_GET['token'];

    	$sql = 'UPDATE pagamentos SET Pago=0, Cancelado=1, data2="'.date('Y-m-d H:i:s').'" WHERE unicretoken="'.$token.'"';
		$res=Runquery($db_link,$DB_name,$sql);
       
        $referer = '';
       
        echo drawRedirectPage('<span style="color:#FF0000">'.getLinguas('key_shop_msg1_c').'</span>', $referer);
    }
	else if(isset($_GET['notification']) && $_GET['notification']==1)
	{		
		// echo '<script>window.open("'.$site_http_url.'","_self");</script>';
    }    
?>