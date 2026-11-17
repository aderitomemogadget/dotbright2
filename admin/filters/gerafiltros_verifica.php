<?
	require_once("settings.inc.php");
	global $site_shell_url;
	

	if(file_exists($site_shell_url.'files/filtros/'.$files.'.temp')) echo '|impon';
	else echo 'off';
?>