<?php
function pinta_linha($str)
{
	$str=str_replace('Produto Sem familia ou marca', '<b style="color:#FF0000">Produto Sem familia ou marca</b>', $str);
	$str=str_replace('importado e ativo no website', '<b style="color:#0075ff">Importado e ativo no website</b>', $str);
	return $str;
}

if(isset($_GET['file']) && file_exists($_GET['file']) && substr_count($_GET['file'], '.txt'))
{
	echo '<style>body{font-family:arial, verdana, sans-serif; font-size:12px}</style>';

	$filename = $_GET['file'];
	$fp = fopen($filename, "r");

	$content = fread($fp, filesize($filename));
	$lines = explode("\n", $content);
	fclose($fp);
	for($a=0; $a<count($lines); $a++)
	{
		echo pinta_linha($lines[$a]).'<br>';
	}
}
else
{
	echo 'nop';
}
?>