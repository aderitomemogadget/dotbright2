<?php
	$path = '/var/www/dev/farmaciafs/core/';
	$path2 = "/var/www/dev/farmaciafs/rmas/filters/cache/";
	$path3 = '/var/www/dev/farmaciafs/files/';
	include $path.'core_files.php';
	include $path2.'funcoes.inc.php';

	
	$id_org = $_SERVER['argv'][1];
	$marca = $_SERVER['argv'][2];
	if($id_org > 0) gera_familias($id_org);
	if($marca > 0) gera_marcas($marca);
	
	
	gerar_sitemap();
?>