<?php
$path = dirname( __FILE__ ) . '/insere_prods.php';
$exe_file = "php ".$path." > /dev/null &";
exec($exe_file);