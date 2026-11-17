<?php
$path = dirname( __FILE__ ) . '/../../core/';
require_once $path . 'settings.php';
$path = dirname( __FILE__ ) . '/../../';
if(file_exists($path.'files/importar/atualizar_temp.txt'))
{ 
	$file_atualizar = dirname(__FILE__) . '/../../files/importar/atualizar_temp.txt';
	if(file_exists($file_atualizar))
	{
		$line = fgets(fopen($file_atualizar, 'r'));
		$ext = pathinfo($line, PATHINFO_EXTENSION);
		$file = explode('_', str_replace(SITE_SHELL_URL . 'files/importar/', '', $line));		
		echo '<b>a analisar ficheiro</b>: '.$file[0].'.'.$ext.' <img src="img/loading.gif" border="0" style="vertical-align:middle" />';
	}
	
	echo '[split]';
	$tem_ficheiros = 0;$num = 1;
	if(count(glob(dirname(__FILE__) . '/../../files/importar/*.xlsx')) > 0) echo '<span style="font-weight:bold;color:#000;font-size:12px">Ficheiros para analise:</span><br><br>';
	foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$file = explode('_', str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename));
		echo $num.' - '.$file[0].'.'.$ext.' - <a style="color:#000" href="#" onclick="remove(\''.str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename).'\');return false;">Remover</a><br>';
		$num++;
	}
}
else
{
	$tem_ficheiros = 0;
	foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
	{
		$tem_ficheiros = 1;
	}
	
	if($tem_ficheiros == 1) echo '-1[split]';
	else echo '0[split]';
	
	$tem_ficheiros = 0; $num = 1;
	if(count(glob(dirname(__FILE__) . '/../../files/importar/*.xlsx')) > 0) echo '<span style="font-weight:bold;color:#000;font-size:12px">Ficheiros para analise:</span><br><br>';
	foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$file = explode('_', str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename));
		echo $num.' - '.$file[0].'.'.$ext.' - <a style="color:#000" href="#" onclick="remove(\''.str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename).'\');return false;">Remover</a><br>';
		$num++;
	}
}