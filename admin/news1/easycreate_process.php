<?
	$path="../../core/";
	require_once $path."sessions_engine.php";
	require_once $path."settings.php";
	require_once $path."db_engine.php";
	require_once $path."global.php";
	require_once "funcoes.inc.php";

	if($up || isset($_GET['url']))
	{
		$strURL="../../files/tmp";
		if(!is_dir($strURL)) mkdir($strURL, 0777);
		$upload_folder=$strURL;
		if(isset($_GET['url']))
		{
			$foto=$_GET['url'];
			if(substr_count($foto,'jpg') || substr_count($foto,'gif') || substr_count($foto,'png') || substr_count($foto,'jpg') || substr_count($foto,'gif') || substr_count($foto,'png'))
			{
				list($width,$height,$type,$attr)=getimagesize($foto);
				if($attr)
				{
					$file_source=$foto;
					$foto_name=substr($foto,strrpos($foto,"/"));
					$file_target=$upload_folder."/$foto_name";
					if(file_exists($file_target)) unlink($file_target);
					$cmd="wget '$file_source' -U Mozilla -O ".$file_target;
					exec("$cmd",$result ,$err);
					echo 'done';exit();
				}
			}
			else
			{
				//html
				$file_source=$foto;
				$file_name=substr($foto,strrpos($foto,"/"));
				$file_target=$upload_folder."/$file_name";
				if(file_exists($file_target)) unlink($file_target);
				$cmd="wget '$file_source' -U Mozilla -O ".$file_target;
				exec("$cmd",$result ,$err);
				echo 'done';exit();
			}
			echo 'erro';exit();
		}
		if(count($_FILES)>0){$URL_dest=$upload_folder.'/'.$_FILES['upload']['name'];if( move_uploaded_file( $_FILES['upload']['tmp_name'] ,$URL_dest ) ){$upload_sucesso=1;}}
		else if(isset($_GET['up']))
		{
			if(isset($_GET['base64'])){	$content =base64_decode(file_get_contents('php://input'));}
			else {	$content =file_get_contents('php://input');	}
			$headers =getallheaders();
			$headers =array_change_key_case($headers, CASE_UPPER); //different case was being used for different browsers
			$URL_dest=$upload_folder.'/'.$headers['UP-FILENAME'];
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
			// agora vamos criar o produto
			global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$sql="insert into news1 (ID_ORG,Ref,img_ext) values ($cod,'$fn','jpg')";
		    $res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				$new_id=db_get_last_insert_id($db_link);
				copia_segura($URL_dest,$site_shell_url."files/news1/".$new_id."_1.jpg");
				unlink($URL_dest);
				InsertProdLinguas($new_id,$fn,'','','','','','');
			}
			echo 'done';
			exit();
		}
	}
?>