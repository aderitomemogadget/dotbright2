<?php
    $path=dirname( __FILE__ ).'/../../core/';
	include $path.'core_files.php';

	error_reporting(E_ERROR); ini_set('display_errors',1);

	$GLOBALS['persist'] = 1;

	gera_json_destaques(1);echo "1";
	//gera_json_home_promocoes(1);echo "1";
	gera_json_maisvendidos(1);echo "1";
	gera_json_home_novidades(1);echo "1";
	gera_json_home_random(1);echo "1";
	gera_json_outlet(1);echo "1";
	
	//gera_json_cat_promocoes(1);echo "1";
	//Para cada produto:
	

	//gera_json_relacionados($prod_id);

	//Para cada familia:
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass,'');
	$rs=new DataObj;
	$sql="SELECT ID FROM products_families where visivel=1";

	$rs->db_open($db_link,DB_NAME,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$IDb=$rs->db_data['ID'];
			gera_json_categorias(1,$IDb,'pt');echo "1";
			$rs->db_next();
		}
	}
	$rs->db_close();

?>