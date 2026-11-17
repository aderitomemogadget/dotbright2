<?php
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$m_url=$url_return;
	foreach($_POST as $varName => $value)
  	{
		$dv=$value;
		$dv=$value;

		$str_id=substr($varName, strpos($varName,"_")+1);
		if($str_id > 0)
		{
			$sql="UPDATE products_families SET Ordem=$dv WHERE ID=$str_id";
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res)  echo db_error($db_link);
		}
   	};
	if($res)	show_box_OK(2,$m_url);
	else		show_box_ERROR(2,$m_url);
	db_closedb_conection($db_link);
?>