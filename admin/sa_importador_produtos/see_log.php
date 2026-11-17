<?php
$path = dirname( __FILE__ ) . '/../../core/';
require_once $path . 'settings.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);

function pinta_linha($str)
{
	$str = str_replace('Produto Sem familia ou marca', '<b style="color:#FF0000">Produto Sem familia ou marca</b>', $str);
	$str = str_replace('importado e ativo no website', '<b style="color:#0075ff">Importado e ativo no website</b>', $str);
	
	return $str;
}

if(isset($_GET['file']) && file_exists($_GET['file']) && substr_count($_GET['file'], '.txt'))
{ 
	// if(intval($_GET['tipo']) == 1) $file = file_get_contents($site_http_url.'files/importar/'.basename($_GET['file']));
	// else $file = file_get_contents($site_http_url.'files/importado/'.basename($_GET['file']));
	// echo $site_http_url.'files/importado/'.basename($_GET['file']);
	
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
	
	// print_r($lines);
	
	// echo nl2br($file);
}
else
{
	echo 'nop';
}