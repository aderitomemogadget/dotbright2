<?php

ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');
	set_time_limit(0);
	$path = '../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	require_once __DIR__.'/../signature_helpers.php';


$rma = intval($_GET['rma'] ?? 0);
if (!rma_signature_validate_request()) {
	rma_signature_error('A sessão expirou ou o pedido não é válido. Abra novamente a RMA e repita a assinatura.', 403);
}
require_once 'signature-to-image.php';
$json = isset($_POST['output']) && is_string($_POST['output']) ? trim($_POST['output']) : '';
if ($rma < 1 || $json === '' || strlen($json) > 2097152) {
	rma_signature_error('A assinatura está vazia ou é inválida.');
}
$img = sigJsonToImage($json);
if (!$img) {
	rma_signature_error('Não foi possível processar o desenho da assinatura.');
}
ob_start(); // start output buffer
imagepng($img); // output image data to buffer
imagedestroy($img);
// create Data URI for a PNG image, using the binary image
// data captured from the output buffer
$dataURI = 'data:image/png;base64,'.base64_encode(ob_get_clean());

// echo HTML img element using that Data URI, somewhere on the page
//echo 'rma:'.$rma;
//echo '<img src="'.$dataURI.'">';


if($rma>0 && $dataURI)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$sql = 'UPDATE rmas SET signed="'.$dataURI.'",signed_date="'.date("Y-m-d").'" WHERE ID='.$rma;
	Runquery($db_link, $DB_name, $sql);
	header('Location: index.php?rma='.$rma);
	exit;
}

?>
