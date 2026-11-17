<?php
	$path = dirname( __FILE__ ) . '/../../../admin/sa_importador_produtos/import_file_trata.php';
	$exe_file = "/usr/local/bin/php ".$path." > /dev/null &";
	// $exe_file = "php ".$path." > /dev/null &";
	exec($exe_file);
?>