<?php
	$path = dirname( __FILE__ ) . '/../../core/';
	require_once $path . 'core_files.php';

	ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');

	error_reporting(E_ERROR);
	ini_set('display_errors',-1);

	global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql = "SELECT ID FROM products WHERE Aproved=1 and apagado=0";

	$rs->db_open( $db_link,$DB_name,$sql);
	$total_produtos = $rs->db_rows;
	$linha = 1;
	$ll = '';
	$ll2 = '';
	while(!$rs->EOF())
	{

		$ll = intval(($linha * 100 ) / $total_produtos);
		if($ll != $ll2)
		{
			$ll2 = $ll;
			echo $ll."% \n";
		}
		$id_produto = $rs->db_data["ID"];
		$array = get_produtc_info($id_produto,'',0,0,'',1);


		$linha++;
		$rs->db_next();
	}
	$rs->db_close();

?>