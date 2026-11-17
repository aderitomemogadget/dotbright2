<?php
	$page_titulo="Produtos: Familias";
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include "funcoes.inc.php";


	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][8])) die("Sem Permiss&otilde;es");
?>