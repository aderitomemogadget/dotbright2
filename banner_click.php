<?php
	$url = $_GET['url'];

	$_GET ='';
	$_POST ='';
	$_REQUEST ='';

	unset($_GET);
	unset($_POST);
	unset($_REQUEST);


	$includes_path="conf/";
	include $includes_path."conf.php";
	if(!substr_count($url,'http://') && !substr_count($url,'https://')) $url = $website_url_email.$url;
	if(!$url) $url = $site_http_url;

	if(substr_count($url,'https://'))
	{
		$url=str_replace("https://","",$url);
		echo '<script type="text/javascript">open("https://'.$url.'","_top");</script>';
	}
	else
	{
		$url=str_replace("http://","",$url);
		echo '<script type="text/javascript">open("http://'.$url.'","_top");</script>';
	}
?>