<div style="display:block;padding:5px;vertical-align:top;background:#FFF;min-height:118px">
<h2>Ficheiro a Importar..</h2>
<?php

	$myarray = glob($GLOBALS['site_shell_url']."files/importar/analisar_csv*.txt", GLOB_NOSORT);

	array_multisort(array_map('filemtime', $myarray), SORT_NUMERIC, SORT_DESC, $myarray);
	$a=1;
	foreach ($myarray as $filename)
	{
		if($a>15) exit;
		
		$data_ficheiro = date ("Y-m-d H:i:s", filemtime($filename));
		$nome_ficheiro = basename($filename);
		// $link_ficheiro = $site_http_url.'files/importar/'.$nome_ficheiro.'?dp='.date('YmdHis');	
		$link_ficheiro = $filename; 
		?>
		<div style="width:145px;display:inline-block;padding:5px 0"><?php echo $data_ficheiro;?></div><div style="width:150px;display:inline-block;padding:5px 0"><img src="img/loading.gif" border="0" style="vertical-align:middle"><a href="see_log.php?tipo=0&file=<?php echo $link_ficheiro;?>" class="fancybox_url preto" style="text-decoration:underline">Ver LOG</a></div><br>
		<div style="clear:both"></div>
		<?php
		$a++;
	}
?>
</div>

<div style="display:block;padding:5px;vertical-align:top;background:#FFF;min-height:118px">
<h2>Ficheiros Importados</h2>
<?php

	$myarray = glob($GLOBALS['site_shell_url']."files/importado/analisar_csv*.txt", GLOB_NOSORT);

	array_multisort(array_map('filemtime', $myarray), SORT_NUMERIC, SORT_DESC, $myarray);
	$a=1;
	foreach ($myarray as $filename)
	{
		if($a>15) exit;
		
		$data_ficheiro = date ("Y-m-d H:i:s", filemtime($filename));
		$nome_ficheiro = basename($filename);
		// $link_ficheiro = $site_http_url.'files/importado/'.$nome_ficheiro.'?dp='.date('YmdHis');
		$link_ficheiro = $filename; 
		?>
		<div style="width:145px;display:inline-block;padding:5px 0"><?php echo $data_ficheiro;?></div><div style="width:150px;display:inline-block;padding:5px 0"><a href="see_log.php?tipo=1&file=<?php echo $link_ficheiro;?>" class="fancybox_url preto" style="text-decoration:underline">Ver LOG</a></div><br>
		<div style="clear:both"></div>
		<?php
		$a++;
	}
?>
</div>

