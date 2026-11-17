<?
	ob_start();
	$callback = &$_REQUEST['fd-callback'];
	if (!empty($_FILES['fd-file']) and is_uploaded_file($_FILES['fd-file']['tmp_name']))
	{
	  // Regular multipart/form-data upload.
	  $name = $_FILES['fd-file']['name'];
	  $data = file_get_contents($_FILES['fd-file']['tmp_name']);
	}
	else
	{
	  // Raw POST data.
	  $name = urldecode(@$_SERVER['HTTP_X_FILE_NAME']);
	  $data = file_get_contents("php://input");
	}
	
	$path="../../core/";
	require_once $path."sessions_engine.php";
	require_once $path."settings.php";
	require_once $path."db_engine.php";
	require_once $path."global.php";
	require_once "funcoes.inc.php";
	$upload_sucesso=0;
	$strURL="../../files/tmp";
	$URL_dest=$strURL.'/'.$name;
	if(file_put_contents($URL_dest,$data)){$upload_sucesso=1;}
	if($upload_sucesso)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$size = @getimagesize($URL_dest);
		switch ($size[2])
		{
			case 1: $ext = "gif"; break;
			case 2: $ext = "jpg";  break;
			case 3: $ext = "png"; break;
			default: die(); break;
		}
		if($name!="")
		{ 
			$pos = strrpos($name,"."); 
			$img_ext = substr($name, $pos+1, strlen($name)+1-$pos);
			if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
			{		
				unlink($img_path.$nome_ficheiro_old);
			}		
			$token = date('YmdHis').bin2hex(uniqid());	
			$nome_ficheiro = $token.".".$img_ext;
		}
		
		$sql="INSERT INTO modulos_imgs (bloco_img_ID_ORG,bloco_img_ext,bloco_img_ordem) VALUES ('".$_GET['ID']."','$nome_ficheiro','0')";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$new_id=db_get_last_insert_id($db_link);
			$sql="INSERT INTO modulos_imgs_lang (bloco_img_ID_PAI,bloco_img_titulo,lang) VALUES ('".$new_id."','','$admin_lang')";
			$res=Runquery($db_link,$DB_name,$sql);

			copia_segura($URL_dest, $site_shell_url."files/documentos/".$nome_ficheiro);
			unlink($URL_dest);
		}
		echo 'done';						
		exit();
	}
?>