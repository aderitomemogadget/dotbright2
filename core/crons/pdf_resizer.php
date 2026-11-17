<?php
set_time_limit(0);
/* SCRIPT PARA DIMINIUIR O TAMANHO DOS PDFS */

error_reporting(E_ALL);
ini_set('display_errors',1);

$path2=dirname( __FILE__ ) . '/../../';
$path='/var/www/lojas/hello.pt';

if(!stream_resolve_include_path($path.'/files/pdfs_convertidos.txt')) file_put_contents($path2.'/files/pdfs_convertidos.txt','');

$res1=convertPDF($path.'/files/products/EE');
$res2=convertPDF($path.'/files/products/DT');
$res3=convertPDF($path.'/files/products/UE');

function convertPDF($directory){

	$path2=dirname( __FILE__ ) . '/../../';
	$path='/var/www/lojas/hello.pt';
	$filesDone=file_get_contents($path.'/files/pdfs_convertidos.txt');
	$filesDoneArray=explode(';',$filesDone);

	$dirArray=scandir($directory);

	foreach ($dirArray as $value) {
		$input=$directory.'/'.$value;
		$output=$input.'.temp';
		if($value != '.' && $value != '..' && is_file($directory.'/'.$value) && !in_array($input, $filesDoneArray) && strpos(strtolower($value), '.pdf') !== false){
			$input=$directory.'/'.$value;
			$output=$input.'.temp';
			echo 'converting file '.$input.'...<br>';
			exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -sOutputFile='.$output.' '.$input, $result);

			echo 'coverted!';
			unlink($input);
			rename($output, $input);
			$filesDoneArray[]=$input;
			echo ' resized: '.$input.'<br>';
			flush();
    		ob_flush();

		}
	}

	if(file_put_contents($path.'/files/pdfs_convertidos.txt', implode(';',$filesDoneArray))) return true;
	return false;
}?>