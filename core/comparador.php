<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

function addRemoveComparador($idprod,$tipo)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $dados=\cliente_logado();
	$cli_id=$dados['cli_id'];
	$cli_id_temp=$_SESSION['CLIENTE_ID_TEMP'];
	if($tipo == 'add')
	{
		if(!isProductInComparador($idprod,$cli_id,$cli_id_temp))
		{
			$sql='INSERT INTO clientes_comparador (PROD_ID, CLIENT_ID, CLIENTE_ID_TEMP) VALUES ("'.$idprod.'", "'.$cli_id.'", "'.$cli_id_temp.'")';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

			if(!$res) echo "-1";
			else echo getLinguas('key_comparador_rem');
		}
		else
		{
			echo getLinguas('key_comparador_rem');
		}
	}
	else if($tipo == 'remove')
	{
		if($client_id > 0 && $client_id_temp != '') $sql='DELETE FROM `clientes_comparador` WHERE PROD_ID="'.$idprod.'" AND (CLIENT_ID="'.$cli_id.'" OR CLIENTE_ID_TEMP="'.$cli_id_temp.'")';
		else $sql='DELETE FROM `clientes_comparador` WHERE PROD_ID="'.$idprod.'" AND CLIENTE_ID_TEMP="'.$cli_id_temp.'"';
		$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
		if(!$res) echo "-1";
		else echo getLinguas('key_comparador_add');
	}
}

function isProductInComparador($prodid,$client_id,$client_id_temp)
{
	//Para tornar esta funcao mais rapida, a primeira vez que for invocada vai carregar para a memoria todos os produtos da wishlist
	global $comparador_produtos;
	if(!isset($comparador_produtos))
	{
		$comparador_produtos=array();

		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	    $rs=new DataObj;
	    $result=false;
	    if($client_id > 0 && $client_id_temp != '') $sql='SELECT PROD_ID FROM clientes_comparador WHERE (CLIENT_ID="'.$client_id.'" OR CLIENTE_ID_TEMP="'.$client_id_temp.'")';
		else $sql='SELECT PROD_ID FROM clientes_comparador WHERE CLIENTE_ID_TEMP="'.$client_id_temp.'"';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	    while(!$rs->EOF())
	    {
	    	$comparador_produtos[$rs->db_data['PROD_ID']]=1;
	    	$rs->db_next();
	    }
	    $rs->db_close();
	}
	$result=false;
	if($comparador_produtos[$prodid]==1)$result=true;

    return $result;
}

function getComparadorCliente($client_id=0,$client_id_temp='')
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=array();
	$pos=0;
	if($client_id > 0 && $client_id_temp!='') $sql='SELECT LOW_PRIORITY COUNT(cw.PROD_ID) AS count FROM clientes_comparador cw WHERE (cw.CLIENT_ID="'.$client_id.'" OR cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") ORDER BY cw.ID DESC';
	else $sql='SELECT LOW_PRIORITY COUNT(cw.PROD_ID) AS count FROM clientes_comparador cw WHERE (cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") ORDER BY cw.ID DESC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$result=$rs->db_data['count'];
	}
	$rs->db_close();
	return $result;
}