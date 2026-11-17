<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time','0');
ini_set('max_input_time','0');
ini_set('memory_limit','-1');

$path=dirname( __FILE__ ) . '/../../core/';
require_once $path . 'core_files.php';

$projeto='hello.pt';

cleanPDFfromFolder('/var/www/lojas/'.$projeto.'/files/products/EE/','ee');
cleanPDFfromFolder('/var/www/lojas/'.$projeto.'/files/products/DT/','dt');
cleanPDFfromFolder('/var/www/lojas/'.$projeto.'/files/products/UE/','ue');


function cleanPDFfromFolder($path, $type='ee'){

	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

	echo '<br>Converting PDF from: '.$path.'...<br>';
	$scanned_directory=array_diff(scandir(substr($path, 0, -1)), array('..', '.'));
	foreach ($scanned_directory as $file) {
		if(is_file($path.$file) && mime_content_type($path.$file) == 'application/pdf' ) { /* PRIMEIRO VERIFICA SE É PDF */
			$tempArray=explode('_',$file);
			$id=$tempArray[0];

			$ret=createJPGfromPDF($path.$file, $path.$file.'.temp');
			if($ret){
				unlink($path.$file);
				rename($path.$file.'.temp', $path.str_replace('PDF', 'jpg', str_replace('pdf', 'jpg', $file)));
				echo $id.' | ';
				$res=Runquery($db_link, $DB_name, 'UPDATE products SET '.$type.'_ext="jpg" WHERE ID="'.$id.'"');
			}
		}
		flush();
    	ob_flush();
	}
}

/**
 * Converts a PDF to a Jpeg
 * @param  string $input  path of the file to be converted
 * @param  string $output converted file path
 * @return bool           if it was converted with success;
 */
function createJPGfromPDF($input, $output){
    $quality=80;
    $res='150x150';
    exec("'gs' '-dNOPAUSE' '-sDEVICE=jpeg' '-dUseCIEColor' '-dTextAlphaBits=4' '-dGraphicsAlphaBits=4' '-o$output' '-r$res' '-dJPEGQ=$quality' '$input'",$output);
    if(count($output)){
    	return true;
    }
    return false;
}
?>