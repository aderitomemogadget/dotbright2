<?
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include "funcoes.inc.php";

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][9])) die("Sem Permiss&otilde;es");
	
	
	
	$prod['pt'] = 'O Artigo [produto] encontra-se [estado]';
	$prod['en'] = 'O Artigo [produto] encontra-se [estado]';
	$prod['fr'] = 'O Artigo [produto] encontra-se [estado]';
	$prod['de'] = 'O Artigo [produto] encontra-se [estado]';
	
?>