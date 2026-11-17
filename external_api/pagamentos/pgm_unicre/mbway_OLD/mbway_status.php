<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	require_once $path.'core_files.php';

	$id_enc = intval($_GET['id_enc']);
	if(!$id_enc)die("0");

	$Estado=-1;


	$sql="SELECT * from sibs_mbway_logs where ID_Encomenda=$id_enc order By ID Desc";
	$rs = new DataObj;
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{
		$IDb= $rs->db_data['ID'];
		$ID_Encomenda= $rs->db_data['ID_Encomenda'];
		$Data= $rs->db_data['Data'];
		$Json= $rs->db_data['Json'];
		$Estado= $rs->db_data['Estado'];
		$responseData=$Erros= $rs->db_data['Erros'];
		$dados = json_decode($Erros, true);
		$id_retorno = $dados["id"];
		if($Estado==0 && $id_retorno != '')
		{
			//Vamos obter o estado do pagamento
			if(substr_count($responseData,'user authorization failed')>0)
			{
				$Estado=2;
				$sql="UPDATE sibs_mbway_logs set Estado=2 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'Cancelled by user')>0)
			{
				$Estado=3;
				$sql="UPDATE sibs_mbway_logs set Estado=3 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'transaction declined')>0)
			{
				$Estado=4;
				$sql="UPDATE sibs_mbway_logs set Estado=4 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas set Estado=14 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}
			if(substr_count($responseData,'Request successfully')>0)
			{
				$Estado=13;
				$sql="UPDATE sibs_mbway_logs set Estado=13 where ID=$IDb";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				$sql="UPDATE encomendas SET Encomenda_Paga=1, Estado=13 where ID=$ID_Encomenda";
				Runquery($db_link, $GLOBALS['DB_name'], $sql);
				//enviarEmailCliente($ID_Encomenda,'', 13);
				//o email é enviado pelo webhook
			}
		}
		//E se ja passaram mais de 4 minutos?
		$from_time = strtotime($Data);
		$to_time = strtotime(date("Y-m-d H:m:i"));

		if(round(abs($to_time - $from_time) / 60,2)*100>4)
		{
			$Estado=5;
			$sql="UPDATE sibs_mbway_logs set Estado=5 where ID=$IDb";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
			$sql="UPDATE encomendas set Estado=15 where ID=$ID_Encomenda";
			Runquery($db_link, $GLOBALS['DB_name'], $sql);
		}
	}
	$rs->db_close();


	echo $Estado;


?>