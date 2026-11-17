<?php
require_once('settings.inc.php');
$path = dirname( __FILE__ ) . '/../../../';
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	

	if($tem_ficheiros == 1 && !file_exists($path.'files/importar/atualizar_web.txt'))
	{
		echo '-1[split]';
		// echo $linhas;
	}
	elseif(file_exists($path.'files/importar/atualizar_web.txt'))
	{ 
		echo '0[split]';
		echo '<center><b>a atualizar base de dados</b> <br><br> </center><div style="background:#FFF;font-size:11px;padding:10px">';
		
		$fh = fopen($path.'files/importar/atualizar_web.txt', 'r');
		while ($line = fgets($fh)) 
		{
			echo rtrim($line, ' | ');
		}
		fclose($fh);
		
		echo '...<img src="img/loading.gif" border="0" style="vertical-align:middle" /><br><br> <center><a href="#" onclick="remove2();return false;" style="color:#000">CANCELAR ATUALIZAÇÂO</a></center></div>';
	}
	else
	{
		echo '-1[split]';
		echo 'Nenhum processo se encontra a decorrer[split]';
	}

