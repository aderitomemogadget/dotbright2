<?php

	$file = '/home/www/4mstore.pt/public_html/files/mbway_'.date('Ymdh').uniqid('').'.txt';
	file_put_contents($file, file_get_contents('php://input'));


	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
	$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
	if($ip != '94.46.13.92'){
		echo $ip;
		die();
	}
	else{
		echo 'Ip válido';
	}

	$id_enc = intval($_GET['descricao']);
	$descricao = $_GET['descricao'];
	$statusCode = $_GET['statuscode'];
	$Data = $_GET['data'];
	$Valor = $_GET['valor'];

	if(!$id_enc)die("false");

	/*POST que vem da lusopay:
	descricao -> externalReference
	statusCode -> Pagamento (000), Rejeitado (020), Cancelado pela SIBS (048)
	Data
	Valor*/

	$sql="SELECT * from lusopay_mbway_logs where ID_Encomenda=$id_enc";
	$rs = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

	if(!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['ID_Encomenda'];

		if(substr_count($statusCode,'020')>0)
		{
			$Estado=2;
			$sql="UPDATE lusopay_mbway_logs set Estado=2 where ID=$IDb";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
		if(substr_count($statusCode,'048')>0)
		{
			$Estado=3;
			$sql="UPDATE lusopay_mbway_logs set Estado=3 where ID=$IDb";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}

		if(substr_count($statusCode,'000')>0)
		{
			$Estado=13;
			$sql="UPDATE lusopay_mbway_logs set Estado=13 where ID=$IDb";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			enviarEmailCliente($ID_Encomenda,'', 13);
			//Agora deve enviar encomenda para o cliente de paga
		}
		$rs->db_close();
		echo 'true';
		die();
	}
	echo 'false';
	die();
?>
