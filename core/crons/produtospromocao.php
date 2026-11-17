<?php
	$path=dirname( __FILE__ ) . '/../core/';
	include $path.'core_files.php';
	global $site_shell_url,$site_http_url_internal;
	$produtos_promocao='files/produtospromocao.php';
	$promos=GERA_PRODUTOS_PROMOCAO();
	$file_name=$site_shell_url.$produtos_promocao;
	unlink($file_name);
	$fh=fopen($file_name,'w') or die("can't open file");
	fwrite($fh,$promos);
	fclose($fh);
?>