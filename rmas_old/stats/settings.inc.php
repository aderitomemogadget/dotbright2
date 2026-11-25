<?
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][5])) die("Sem Permiss&otilde;es");
?>