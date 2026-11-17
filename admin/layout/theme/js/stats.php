<?php
/*	$path="../../../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';

	if($f_data1=="" && $f_data2=="")
	{
		$dias=10;
		$f_data1=date("Y-m-d", strtotime("-$dias day"));
		$f_data2=date("Y-m-d");
	}
	if(!$mes) $mes=date("m");
	if(!$ano) $ano=date("Y");

	$v='';
	$vt='';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	while($dias > 0)
	{
		$n_acessos=0;

		$f_data1=date("Y-m-d", strtotime("-$dias day"));
		$sql="select count(*) as Contador from stats where Data = '$f_data1' Group By ip ";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())$n_acessos=$rs->db_data["Contador"];
		$rs->db_close();
		$v[$dias]=number_format($n_acessos,0,'','.');

		$n_acessos=0;

		$sql="select count(*) as Contador from stats where Data = '$f_data1' ";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())$n_acessos=$rs->db_data["Contador"];
		$rs->db_close();
		$vt[$dias]=number_format($n_acessos,0,'','.');

		$dias--;
	}


	echo json_encode(array("v_10" => $v[10],"v_9" => $v[9],"v_8" => $v[8],"v_7" => $v[7],"v_6" => $v[6],"v_5" => $v[5],"v_4" => $v[4],"v_3" => $v[3],"v_2" => $v[2],"v_1" => $v[1],"vt_10" => $vt[10],"vt_9" => $vt[9],"vt_8" => $vt[8],"vt_7" => $vt[7],"vt_6" => $vt[6],"vt_5" => $vt[5],"vt_4" => $vt[4],"vt_3" => $vt[3],"vt_2" => $vt[2],"vt_1" => $vt[1]));
*/
?>