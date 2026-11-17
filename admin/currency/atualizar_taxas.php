<?
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	$sql_all = "Select * from currency";
	$db_res = $rs->db_open($db_link,$DB_name,$sql_all);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$currency_id=$rs->db_data["currency_id"];
		$currency_country=$rs->db_data["currency_country"];
		$currency_lingua=$rs->db_data["currency_lingua"];
		$currency_estado=$rs->db_data["currency_estado"];

		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;

		$currency_taxa = getCurrency($currency_country);

		if($currency_taxa)
		{
			$sql =
			"
				UPDATE
					currency
				SET
					currency_value='".$currency_taxa['taxa']."',
					currency_symbol='".$currency_taxa['symbol']."',
					currency_ok=1
				WHERE
					currency_id=$currency_id
			";
			Runquery($db_link,$DB_name,$sql);
		}

		$rs->db_next();
		$i++;
	}
	$rs->db_close();
?>