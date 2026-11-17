<?php
	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	$myFile_shell = dirname( __FILE__ )."/../../../../files/importar/run_cmd_".$Supplier_ID.".txt";
	if(file_exists($myFile_shell))unlink($myFile_shell);

	include dirname( __FILE__ ).'/../../../../conf/conf.php';

	if(function_exists('exec'))
	{
		error_reporting(E_ALL);
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(0);

		if($Supplier_ID==2)
		{
			//Macorlux
			$path = dirname( __FILE__ ) . '/../webservice_macorlux.php ';
			$exe_file = "php ".$path." > ".$myFile_shell;
			exec($exe_file,$output);
		}
		elseif($Supplier_ID==4)
		{
			//Macorlux
			$path = dirname( __FILE__ ) . '/../webservice_fd.php ';
			$exe_file = "php ".$path." > ".$myFile_shell;
			exec($exe_file,$output);
		}
		else
		{
			$path = dirname( __FILE__ ) . '/../import_file_trata.php '.$Supplier_ID;
			$exe_file = "php ".$path." > ".$myFile_shell;
			exec($exe_file,$output);
		}

	}
	else echo "exec is not enabled";
?>