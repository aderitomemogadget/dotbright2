<?php
	require_once('settings.inc.php');
	$ID=$_GET['ID'];
	$html_gerado=getNewsleterPreview($ID,'print',701);
	if($html_gerado!='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql="update nl_newsletter SET Texto='".addslashes($html_gerado)."' WHERE ID=".$ID;
		$res=Runquery($db_link,$DB_name,$sql);
		echo '<script>window.open("'.$website_url_email.'admin/newsletter/newsletter_template.php?task2=altera&ID='.$ID.'&Template=1","_self")</script>';
	}