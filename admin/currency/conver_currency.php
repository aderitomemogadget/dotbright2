<? 
	include "settings.inc.php";
	
	// $currency_taxa = getCurrency($countries_id);	
	// $currency_value = $currency_taxa['taxa'];
	// $currency_symbol = $currency_taxa['symbol'];
	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	
	$rs = new DataObj;
	
	$sql_all="Select currency_value from currency where currency_id='$countries_id'";
	// echo $sql_all;
	$rs->db_open($db_link,$DB_name,$sql);
	$currency_value=$rs->db_data["currency_value"];
	$rs->db_close();
	
	echo $currency_value;
?>