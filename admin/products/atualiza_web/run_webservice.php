<?php
$path = dirname( __FILE__ ) . '/../../../core/';
require_once $path . 'core_files.php';

$path = dirname( __FILE__ ) . '/geraprods.php';
$exe_file = "php ".$path." > /dev/null &";
exec($exe_file);
