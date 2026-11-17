<?php
$path = dirname( __FILE__ ) . '/../../../';
if(file_exists($path.'files/importar/atualizar_dotbright.txt'))
{ 
	$fh = fopen($path.'files/importar/atualizar_dotbright.txt','r');
	while ($line = fgets($fh)) 
	{
		if($line!='') $last =  $line;
	}
	fclose($fh);
	
	echo $last;
}
elseif(file_exists($path.'files/importar/dotbright_feed_export.txt'))
{	
	echo '-1';
}
else
{	
	echo '0';
}
?>