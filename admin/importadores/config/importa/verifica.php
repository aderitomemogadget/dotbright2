<?php

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);


	$myFile = dirname( __FILE__ )."/../../../../files/importar/analisar_csv".$Supplier_ID.".txt";

	if(file_exists($myFile))
	{
		echo '1[split]';
		echo '<div style="background:#FFF;font-size:11px;padding:10px">';
		$line = '';
		$i=0;
		echo '<b>Progresso de Importação:<br><br><span style="font-size:10px">';
		echo $line = exec('tail -n 1 '.$myFile);
		echo '</span></b>';
		echo '<br><br> <center><a href="#" onclick="remove2();return false;" style="color:#000">PARAR IMPORTAÇÃO</a></center></div>';
	}
	else
	{
		if(file_exists(dirname(__FILE__) .'/../../../files/importar/importa_manual.csv')){ echo '0[split]<div style="background:#FFF;font-size:11px;padding:10px;color:#0c28f3">Foi encontrado um ficheiro para importação - Clique em "Forçar Importação" para começar a importar!</div>';}
		else echo '0[split]<div style="background:#FFF;font-size:11px;padding:10px">Não existe ficheiro para importação</div>';
	}
?>