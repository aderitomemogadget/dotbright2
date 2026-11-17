<?php 

include "settings.inc.php";

$familia = $_GET['familia'];
global $DB_host,$DB_user,$DB_pass,$DB_name, $admin_lang; 


$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$sql2="SELECT pfl.* FROM  products_families pf 
		inner join products_families_lang pfl on (pf.ID = pfl.ID_PAI) 
		WHERE  pf.ID_ORG=".$familia." and pfl.Lang='".$admin_lang."'";
		$obj2=new DataObj;
		$obj2->db_open($hConn,$DB_name,$sql2);
		echo '<option value="">SEM SUBFAMILIA</option>';
		while(!$obj2->EOF())
    	{
    		echo '<option value="'.$obj2->db_data['ID_PAI'].'"';
			echo '>'.$obj2->db_data['Cabecalho'].'</option>';
			$obj2->db_next();
		}
		$obj2->db_close();

?>
