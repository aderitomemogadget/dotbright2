<?php

	$myFile_shell = dirname( __FILE__ )."/logs/log.txt";
	if(file_exists($myFile_shell))
	{
		echo $line = exec('tail -n 1 '.$myFile);
	}

	$myFile = dirname( __FILE__ )."/logs/log.txt";
	if(file_exists($myFile))
	{
		echo '<div style="background:#FFF;font-size:11px;padding:10px">';
		$line = '';
		$i=0;
		echo '<b>Progresso de Atualização:<br><br><span style="font-size:10px">';
		echo $line = exec('tail -n 1 '.$myFile);
		echo '</span></b>';

	}
	else echo 'AGUARDA';
?>