<?php

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';

    global $Entidade;
	$Entidade="12142";

	$id_enc=intval($_GET['id_enc']);


	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);


    function get_api_key()
	{
		return 'bd3d-07b6-6b87-9a91-335a';
	}

	//Atualizar o estado de todas as encomendas
	$client = @new SoapClient('https://clientes.eupago.pt/eupagov20.wsdl');
	$data_ini=date('Ymd000000');
	$data_fim=date('Ymd235959');
	$data_ini = date('Ymd000000', strtotime($data_ini.' -1 day'));

	$arraydados = array (
			"chave" => get_api_key(),
			"dtHrInicio" => $data_ini,
			"dtHrFim" => $data_fim
		);
		$object = $client->getTransactionsBetweenDates($arraydados);
	$result=json_decode(json_encode($object), true);
	$result=$result['getPaymentsResult']['Payment'];

	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj;

	$i=0;
	$t=count($result);
	while($i<$t)
	{
		$ref=$result[$i]['Referencia'];
		$estado=$result[$i]['Processamento'];
		$entidade=$result[$i]['Entidade'];

		if($entidade=='00000' && $estado=='paga')
		{
			$sql='SELECT * FROM eupago_mbway_logs WHERE Erros LIKE \'%referencia";s:8:"'.$ref.'"%\'';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if (!$rs->EOF())
			{
				$IDb= $rs->db_data['ID'];
				$ID_Encomenda= $rs->db_data['ID_Encomenda'];
				$Estado_atual= $rs->db_data['Estado'];
				if($Estado_atual!=1)
				{
					$sql='UPDATE eupago_mbway_logs set Estado=1 where ID = '.$IDb;
					$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				$rs->db_close();
				$sql='SELECT * FROM encomendas WHERE ID = '.$ID_Encomenda;
				$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
				if (!$rs->EOF())
				{
					$Encomenda_Paga= $rs->db_data['Encomenda_Paga'];
					if($Encomenda_Paga!=1)
					{
						$sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='13' WHERE ID='".$ID_Encomenda."'";
						$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
						enviarEmailCliente($ID_Encomenda,'', 13);
					}
				}
			}
		}
		$i++;
	}

	$Estado=0;

	if($id_enc>0)
	{
		$sql="SELECT * from eupago_mbway_logs where ID_Encomenda=$id_enc";
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if (!$rs->EOF())
		{
			$Estado= $rs->db_data['Estado'];
			if($Estado==1)
			{
				$Estado=13;
			}
			$rs->db_next();
		}
		$rs->db_close();
	}

	echo $Estado;