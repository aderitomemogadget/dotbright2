<?php
$path = dirname( __FILE__ ) . '/../../../../core/';
require_once $path . 'core_files.php';
	
$path = dirname( __FILE__ ) . '/import_verify.php';
$exe_file = EXEC." ".$path." > /dev/null &";
echo $exe_file;
exec($exe_file);
