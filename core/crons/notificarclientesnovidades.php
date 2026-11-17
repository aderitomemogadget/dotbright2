<?php
	header ('Content-type: text/html; charset=UTF-8');
	$path=dirname( __FILE__ ) . '/../core/';
	include $path.'core_files.php';
	notificarProdutoNovidades();
?>