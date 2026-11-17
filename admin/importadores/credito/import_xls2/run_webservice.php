<?php
$path = dirname( __FILE__ ) . '/../../../../core/';
require_once $path . 'core_files.php';

$path = dirname( __FILE__ ) . '/import.php';
$exe_file = EXEC." ".$path." > /dev/null &";
exec($exe_file);
