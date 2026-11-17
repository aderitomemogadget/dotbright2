<?php
header('Content-Type: text/html; charset=utf-8');
require_once $path =  dirname( __FILE__ )."/../settings.inc.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$demo_mode = false;
$upload_dir = $site_shell_url.'files/tmp/';
$allowed_ext = array('jpg','jpeg','png','gif');


if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit_status('Error! Wrong HTTP method!');
}

function submete_ficheiro_up($file_name, $nome_ficheiro_old)
{
	$img_path = '../../../files/frames/';
	// echo $img_path.'<br>';
	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	// print_r($_FILES[$file_name]).'<br>';

	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;

	// echo $file_name.' - ('.$Img_peq["name"].')<br>';
	$temp_name = '';
	if($Img_peq["name"] != '')
	{
		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], $pos+1, strlen($Img_peq['name'])+1-$pos);

		$temp_name = str_replace('.'.$img_ext, '', $Img_peq["name"]);

		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
		{
			unlink($img_path.$nome_ficheiro_old);
		}
		$token = date('YmdHis').bin2hex(openssl_random_pseudo_bytes(5));
		$nome_ficheiro = $token.".".$img_ext;

		copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro);
	}

	return array('nome_ficheiro' => $nome_ficheiro, 'temp_name' => $temp_name);
}

if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 )
{

	$pic = $_FILES['pic'];

	if(!in_array(get_extension($pic['name']),$allowed_ext)){
		exit_status('Only '.implode(',',$allowed_ext).' files are allowed!');
	}

	if($demo_mode){

		// File uploads are ignored. We only log them.

		$line = implode('		', array( date('r'), $_SERVER['REMOTE_ADDR'], $pic['size'], $pic['name']));
		file_put_contents('log.txt', $line.PHP_EOL, FILE_APPEND);

		exit_status('Uploads are ignored in demo mode.');
	}


	// Move the uploaded file from the temporary
	// directory to the uploads folder:


	$ficheiro_arr = submete_ficheiro_up('pic', '');
	$nome_ficheiro = $ficheiro_arr['nome_ficheiro'];
	$nome_ficheiro_temp = $ficheiro_arr['temp_name'];



	if($nome_ficheiro != '')
	{

		$cod = $_GET['ID_ORG'];
		$fn = basename($pic['name']);

		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);


		$tr2 = ""; if($nome_ficheiro) $tr2 = "bloco_ext,";
		$tr3 = ""; if($nome_ficheiro) $tr3 = "'".$nome_ficheiro."',";

		$sql = "
			INSERT INTO
				frames_save
				(
					bloco_id,
					bloco_lang,
					bloco_titulo,
					bloco_url,
					bloco_produto,
					".$tr2."
					bloco_ativo,
					flag
				)
				VALUES
				(
					'".$cod."',
					'".$GLOBALS['admin_lang']."',
					'".($nome_ficheiro_temp)."',
					'',
					'',
					".$tr3."
					1,
					1
				)
			";
			$res = Runquery($db_link, $DB_name, $sql);


		exit_status('File was uploaded successfuly!');
	}
	else
	{
		exit_status('File empty!');
	}
}

exit_status('Something went wrong with your upload!');


// Helper functions

function exit_status($str){
	echo json_encode(array('status'=>$str));
	exit;
}

function get_extension($file_name){
	$ext = explode('.', $file_name);
	$ext = array_pop($ext);
	return strtolower($ext);
}
function convertToUTF8($str) {
    $enc = mb_detect_encoding($str);

    if ($enc && $enc != 'UTF-8') {
        return iconv($enc, 'UTF-8', $str);
    } else {
        return $str;
    }
}
function submete_ficheiro2($file_name, $nome_ficheiro_old)
{
	$img_path = dirname( __FILE__ ).'/../../../files/'.FOLDER.'/';
	$token = date('YmdHis').bin2hex(openssl_random_pseudo_bytes(5));

	$img_ext = pathinfo($file_name, PATHINFO_EXTENSION);
	$nome_ficheiro = $token.".".$img_ext;

	copia_segura($nome_ficheiro_old, $img_path.$nome_ficheiro);

	return $nome_ficheiro;
}
?>