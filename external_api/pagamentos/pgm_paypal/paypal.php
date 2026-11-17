<?php
$path = dirname( __FILE__ ).'/../../../core/';
require_once $path.'core_files.php';	

// if there is not action variable, set the default action of 'process'
if (empty($_GET['action'])) $_GET['action'] = 'process';  
switch ($_GET['action']) 
{ 
	case 'success':
		// Order was successful...
		$enc_id = $_POST['item_number'];
		// $enc_id = $_SESSION['paypal_encid'];
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		
		
		$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='Pending',Estado='13' WHERE ID='".$enc_id."' AND Estado=''";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

    	$referer = $GLOBALS['site_http_url'];
		echo drawRedirectPage(getLinguas('key_shop_msg1'), $referer, 3000, 1);
		
		$rs = new DataObj;
		$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'" AND Assistencia=1';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF()) 
		{
			unset($_SESSION['ss_shop_reparacoes']);
			unset($_SESSION['ss_n_enc_reparacoes']);
			$shop_reparacoes = array();
			$n_enc_reparacoes = 0;
		}
		else
		{
			unset($_SESSION['ss_shop']);
			unset($_SESSION['ss_n_enc']);
			$shop = array(); 
			$n_enc = 0;
			
			if($GLOBALS['module_voucher']==1)
			{
				unset($_SESSION['ss_voucher']);
				unset($_SESSION['ss_n_voucher']);
				$voucher = array(); 
				$n_voucher = 0;
			}
		}
		$rs->db_close();
		
		$myFile =  dirname( __FILE__ ).'/../../../files/success_'.$enc_id.'.txt';
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, print_r($_POST, true)."\n".$sql);
		fclose($fh);		

	break;
    case 'cancel':
    	$referer='';
    	
    	$enc_id = $_SESSION['paypal_encid'];
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql = "UPDATE encomendas SET Encomenda_Paga=0, Encomenda_Status='Cancel', Estado='15' WHERE ID='".$enc_id."'";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

    	if($dados = cliente_logado())
		{
    		$referer = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1&id_enc='.$enc_id;
    	}
		else
		{
			$referer = $GLOBALS['site_http_url'];
		}
	
	
		$rs = new DataObj;
		$sql = 'SELECT * FROM encomendas WHERE ID="'.$enc_id.'" AND Assistencia=1';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF()) 
		{
			unset($_SESSION['ss_shop_reparacoes']);
			unset($_SESSION['ss_n_enc_reparacoes']);
			$shop_reparacoes = array();
			$n_enc_reparacoes = 0;
		}
		else
		{
			unset($_SESSION['ss_shop']);
			unset($_SESSION['ss_n_enc']);
			$shop = array(); 
			$n_enc = 0;
			if($GLOBALS['module_voucher']==1)
			{
				unset($_SESSION['ss_voucher']);
				unset($_SESSION['ss_n_voucher']);
				$voucher = array(); 
				$n_voucher = 0;
			}
		}
		$rs->db_close();	

		echo drawRedirectPage(getLinguas('key_shop_msg1_c'), $referer, 3000, 1);    	
		
	break;
	case 'ipn':
	   	
		$id_enc = $_POST['txn_id'];
		$id_enc2 = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$txn_type = $_POST['txn_type'];

		if(isset($payment_status) && $payment_status=='Completed')
		{
			$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
			$sql = "UPDATE encomendas SET Encomenda_Paga=1,Encomenda_Status='".$payment_status."',Estado='13' WHERE ID='".$id_enc2."'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		    enviarEmailCliente($id_enc2);
		}
		elseif(isset($payment_status))
		{
			$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
			$sql = "UPDATE encomendas SET Encomenda_Paga=0,Encomenda_Status='".$payment_status."',Estado='14' WHERE ID='" . $id_enc2 . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		
		$myFile =  dirname( __FILE__ ).'/../../../files/ipn_'.$id_enc2.'.txt';
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, print_r($_POST, true)."\n".$sql);
		fclose($fh);
		
	break;
}