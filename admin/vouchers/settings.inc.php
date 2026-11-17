<?
	$path="../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";
	include "funcoes.inc.php";
	include "funcoes_avancada.inc.php";


	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][28])) die("Sem Permiss&otilde;es");
?>