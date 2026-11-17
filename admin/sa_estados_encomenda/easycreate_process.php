<?
	require_once "settings.inc.php";
	
	error_reporting(E_ALL);
	ini_set('display_errors', 0);
	
	set_time_limit(0);
	ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');
	
	function multiExplode($delimiters,$string) 
	{
		return explode($delimiters[0],strtr($string,array_combine(array_slice($delimiters,1),array_fill(0,count($delimiters)-1,array_shift($delimiters)))));
	}

	if($up || isset($_GET['url']))
	{
		$strURL = $site_shell_url."files/tmp";
		if(!is_dir($strURL)) mkdir($strURL, 0777);			
		$upload_folder=$strURL;		
		
		if(count($_FILES)>0){echo 'files';$URL_dest=$upload_folder.'/'.$_FILES['upload']['name'];if( move_uploaded_file( $_FILES['upload']['tmp_name'] ,$URL_dest ) ){$upload_sucesso=1;}} 
		else if(isset($_GET['up']))
		{
			// echo 'up';
			if(isset($_GET['base64'])){	$content =base64_decode(file_get_contents('php://input'));} 
			else {	$content =file_get_contents('php://input');	}
			
			
			
			$headers =getallheaders();		
			$headers =array_change_key_case($headers, CASE_UPPER); //different case was being used for different browsers
			$URL_dest=$upload_folder.'/'.$headers['UP-FILENAME'];
			
			// echo $URL_dest;
			if(file_put_contents($URL_dest,$content)){$upload_sucesso=1;}
		}
		if($upload_sucesso)
		{
			$original_size=filesize($URL_dest);
			if($original_size < $size){echo "erro1";exit();}
			if($original_size > $size)
			{
				// o tamanho é maior diferente, entao tem de ser convertido
				$fx=file_get_contents($URL_dest);
				$fx=explode(",",$fx);
				if(!file_put_contents($URL_dest,base64_decode($fx[1]))){echo "erro2";exit();}
			}
			// agora vamos criar o produto ou atualizar
			global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$fn = basename($URL_dest);
			$exploded = multiexplode(array(",",".","|",":","_","-"),$fn);

				
			$ext = pathinfo($fn, PATHINFO_EXTENSION);
			$fn_titulo = str_replace('.'.$ext, '', $fn);
			$fn_titulo2 = str_replace(array('.'.$ext, ",",".","|",":","_","-"), ' ', $fn);
			
			
			$img = submete_ficheiro($fn, '');
			
			$sql="insert into ".TBMODULO." (bloco_visivel,bloco_ext) values (1,'".$img."')";
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				$new_id = db_get_last_insert_id($db_link);
				
				$sql = 'INSERT INTO '.TBMODULO_LANG.' (bloco_id, bloco_titulo, bloco_lang) VALUES ("'.$new_id.'", "'.$fn_titulo2.'", "en")';
				$res = Runquery($db_link,$DB_name,$sql);
			}
		
			echo 'done';						
			exit();
		}
	}
?>