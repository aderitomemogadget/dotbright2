<?php
include "settings.inc.php";
global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$linha = '';
$rs = new DataObj;
$sql = '
		SELECT
			Email
		FROM
			nl_emails_registados';

$rs->db_open($db_link,$DB_name,$sql);
$i=0;
if(!$rs->EOF())
{
	$linha = '';
	while(!$rs->EOF())
	{
		$Email = $rs->db_data["Email"];

		$linha .= $Email."\n";

		$rs->db_next();

	}
}
$rs->db_close();

if($linha)
{

$filename = "produtos_100".date("YmdHis").".csv";
header('Content-type: application/pdf');
header("Content-Disposition: attachment; filename=$filename");
header('Expires: 0');
header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
header('Pragma: public');
echo $linha;
}
?>