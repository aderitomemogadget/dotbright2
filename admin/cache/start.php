<?php
	if(function_exists('exec'))
	{
		$path = dirname(__FILE__).'/../../core/';
		require_once $path.'../conf/conf.php';


		error_reporting(E_ALL);
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(0);
		
		$path = dirname( __FILE__ ) . '/limpa_cache.php ';
		$myFile_shell = dirname( __FILE__ ) . '/logs/log.txt ';

		$exe_file = "php ".$path;
		exec($exe_file,$output);

	}
	else echo "exec is not enabled";
?>