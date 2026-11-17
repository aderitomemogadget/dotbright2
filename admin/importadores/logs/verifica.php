<?php
	// $path = dirname( __FILE__ ).'/../../../core/';
	// include $path . 'core_files.php';;
	
	if(isset($_GET['file']) && $_GET['file'] == 'jpsacouto') $file = 'atualizar_jpsacouto.txt';
	elseif(isset($_GET['file']) && $_GET['file'] == 'dotbright') $file = 'atualizar_dotbright.txt';
	elseif(isset($_GET['file']) && $_GET['file'] == 'pesquisa') $file = 'atualizar_pesquisa.txt';
	else die();

	$myFile = dirname( __FILE__ )."/../../../files/importar/".$file;
	
	define('YOUR_EOL', "\n");
	if(file_exists($myFile))
	{ 
		echo '1[split]';
		// echo '<div style="background:#FFF;font-size:11px;padding-bottom:10px">';
		
		$fp = fopen($myFile,'r');
		
		$pos = -2; $line = ''; $c = '';
		do {
			$line = $c . $line;
			fseek($fp, $pos--, SEEK_END);
			$c = fgetc($fp);
		} while ($c != YOUR_EOL);

	
		echo '<span style="font-size:11px;color:#18c26c"><b>'. $line.'</b></span><br><br>';
	
		fclose($fp);
		
		// echo '<br><br> <center><a href="#" onclick="remove2();return false;" style="color:#000">PARAR IMPORTAÇÃO</a></center></div>';
	}
	else
	{
		
		
		echo '0[split]';
		
		// echo $GLOBALS['site_shell_url']."files/importado/".str_replace('.txt', '', $file)."*.txt";
		$myarray = glob(dirname( __FILE__ )."/../../../files/importado/".str_replace('.txt', '', $file)."*.txt", GLOB_NOSORT);
// print_r($myarray);
		array_multisort(array_map('filemtime', $myarray), SORT_NUMERIC, SORT_DESC, $myarray);
		$a=0;
		foreach ($myarray as $filename)
		{
			if($a>0) exit;
			
			$data_ficheiro = date ("Y-m-d H:i:s", filemtime($filename));
			$nome_ficheiro = basename($filename);
			// $link_ficheiro = $site_http_url.'files/importado/'.$nome_ficheiro.'?dp='.date('YmdHis');
			$link_ficheiro = $filename; 
			?>
			<div style="width:145px;display:inline-block;padding:5px 0"><?php echo $data_ficheiro;?></div><div style="width:150px;display:inline-block;padding:5px 0"><a href="importadores/logs/see_log.php?tipo=1&file=<?php echo $link_ficheiro;?>" class="fancybox_url preto" style="text-decoration:underline">Ver LOG</a></div><br>
			<div style="clear:both"></div>
			<?php
			$a++;
		} 
	}