<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	include $path . 'core_files.php';;


	$myFile = dirname( __FILE__ )."/../../../files/importar/analisar_csv.txt";
	
	define('YOUR_EOL', "\n");
	if(file_exists($myFile))
	{ 
		echo '1[split]';
		echo '<div style="background:#FFF;font-size:11px;padding:10px">';
		
		$fp = fopen($myFile,'r');
		
		$pos = -2; $line = ''; $c = '';
		do {
			$line = $c . $line;
			fseek($fp, $pos--, SEEK_END);
			$c = fgetc($fp);
		} while ($c != YOUR_EOL);

	
		echo '<b>Progresso de Importação:<br><br><span style="font-size:14px">'. $line.'</span></b>';
	
		fclose($fp);
		
		echo '<br><br> <center><a href="#" onclick="remove2();return false;" style="color:#000">PARAR IMPORTAÇÃO</a></center></div>';
	}
	else
	{
		echo '0[split]';
		
		// $arr_loks = array();
		// foreach (glob(dirname( __FILE__ )."/../../../files/export_prods_*.csv") as $filename)
		// {
			// $arr_loks[] = $filename;
		// }
		// if(count($arr_loks) > 0) echo '<br><a class="preto" href="'.$site_http_url.'files/'.basename($arr_loks[count($arr_loks)-1]).'">Download Ficheiro Produtos</div><br><br>'; 
		// echo 'Nada a importar[split]';
	}