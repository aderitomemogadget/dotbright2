<?php
header('Content-Type: text/html; charset=utf-8'); 
require_once $path =  dirname( __FILE__ )."/../settings.inc.php";

error_reporting(E_ALL);
ini_set('display_errors', 0);
 
$demo_mode = false;
$upload_dir = $site_shell_url.'files/tmp/';
$allowed_ext = array('jpg','jpeg','png','gif');


if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit_status('Error! Wrong HTTP method!');
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
	
	if(move_uploaded_file($pic['tmp_name'], $upload_dir.$pic['name']))
	{
		
		$cod = $_GET['ID_ORG'];
		
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		
		$fn = basename($pic['name']);
		
		
		$ext = pathinfo($fn, PATHINFO_EXTENSION);
		$fn_titulo = str_replace('.'.$ext, '', $fn);
		$fn_titulo2 = utf8_encode(str_replace(array('.'.$ext, ",",".","|",":","_","-"), ' ', $fn));
				
		$img = submete_ficheiro2($fn, $upload_dir.$pic['name']);
		// echo $img ;
		$sql="insert into ".TBMODULO." (bloco_visivel,bloco_ext) values (1,'".$img."')";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$new_id = db_get_last_insert_id($db_link);
			
			$sql = 'INSERT INTO '.TBMODULO_LANG.' (bloco_id, bloco_titulo, bloco_lang) VALUES ("'.$new_id.'", "'.$fn_titulo2.'", "en")';
			$res = Runquery($db_link,$DB_name,$sql);
		}
		
		/* $sql="select ID from products where Ref = '".$fn."'";
		// echo $sql;
		$rs=new DataObj;$rs2=new DataObj;
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs->EOF())
		{		
			
			$ext = pathinfo($fn, PATHINFO_EXTENSION);
			$fn_titulo = str_replace('.'.$ext, '', $fn);
			$fn_titulo2 = str_replace(array('.'.$ext, ",",".","|",":","_","-"), ' ', $fn);
			$sql="insert into products (ID_ORG,Ref,img_ext,ID_Marca) values ($cod,'$fn_titulo','jpg', 1)";
			// echo $sql;
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				$new_id=db_get_last_insert_id($db_link);
				copy($upload_dir.$pic['name'],$site_shell_url."files/products/".$new_id."_1.jpg");
				InsertProdLinguas($new_id,$fn_titulo2,'','','','','','','','','','');
				
				$sql = 'INSERT INTO products_images (ID_ORG,Ordem,img_ext) VALUES ("'.$new_id.'",1,"jpg")';
				$res=Runquery($db_link,$DB_name,$sql);
			}
		}
		$rs->db_close(); */
		exit_status('File was uploaded successfuly!');
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
	
	copy($nome_ficheiro_old, $img_path.$nome_ficheiro);
	
	return $nome_ficheiro;
}
?>