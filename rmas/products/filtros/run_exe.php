<?php
	$path = dirname( __FILE__ ) . '/../../../rmas/products/filtros/run.php';
	$exe_file = "php ".$path." > /dev/null &";
	exec($exe_file);
	$path = dirname( __FILE__ ) . '/../../../rmas/products/filtros/geraprods.php';
	$exe_file = "php ".$path." > /dev/null &";
	exec($exe_file);
	// $path = dirname( __FILE__ ) . '/../../../core/crons/webservicefreeplace_gera_imgs.php';
	// $exe_file = "php ".$path." > /dev/null &";
	// exec($exe_file);
?>