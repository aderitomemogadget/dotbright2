<?php
	$path=dirname( __FILE__ ).'/../core/';
	include $path.'core_files.php';


	global $comparador_produtos;
	if(!isset($comparador_produtos))
	{

		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	    $dados=\cliente_logado();
		$client_id=$dados['cli_id'];
		$client_id_temp=$_SESSION['CLIENTE_ID_TEMP'];


		$comparador_produtos=array();

		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	    $rs=new DataObj;
	    $result=false;
	    if($client_id > 0 && $client_id_temp != '') $sql='SELECT PROD_ID FROM clientes_comparador WHERE (CLIENT_ID="'.$client_id.'" OR CLIENTE_ID_TEMP="'.$client_id_temp.'")';
		else $sql='SELECT PROD_ID FROM clientes_comparador WHERE CLIENTE_ID_TEMP="'.$client_id_temp.'"';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	    if(!$rs->EOF())
	    {
	    	echo "1";
	    	die();
	    }
	    $rs->db_close();
	}

?>0