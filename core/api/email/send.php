<?php
	$path = dirname(__FILE__).'/../../../core/';
	//Incluir apenas as librarias necessarias
	require_once $path.'../conf/conf.php';
	require_once $path.'seguranca.php';
	require_once $path.DB_ENGINE;
	require_once $path.'sessions_engine.php';
	require_once $path.'global.php';
	require_once $path.'admin_engine.php';
	require_once $path.'seo.php';
	require_once $path.'gd.php';
	require_once $path.'email.php';

	if(isset($argv) && $argv[1] !='')$tmp_file=$argv[1];
	$string=file_get_contents($tmp_file);
	$post=unserialize($string);
	$Subject=$post['Subject'];
	$Body=$post['Body'];
	$Body=$post['Body'];
	$Email=$post['Email'];

	echo send_phpmailer_v2($Email, $Subject, $Body);
?>