<?php

	$path = dirname( __FILE__ ).'/../../../core/';
	include $path . 'core_files.php';
	include dirname( __FILE__ ).'/funcoes.inc.php';

	$id_og = $_SERVER['argv'][1];
	if($id_og > 0) coloca_margens($id_og);
?>