<?php
if(isset($paypal['business']))
{
    $includes_path="../../includes/";
	include $includes_path."includes.inc.php";
    global $Server, $UserName, $db_password, $DBName, $Nome, $lang;
	$db_link=db_opendb_conection($Server, $UserName, $db_password);
	$sql="UPDATE encomendas SET paga=1 WHERE ID=".$_POST['item_number'];
	$res=Runquery($db_link, $DBName, $sql);
}
else
{
	die('This page is not directly accessible');
}
?>