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
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$size = @getimagesize($URL_dest);
		switch ($size[2])
		{
			case 1: $ext = "gif"; break;
			case 2: $ext = "jpg";  break;
			case 3: $ext = "png"; break;
			default: die(); break;
		}
		$id_prod=$cod;
		$sql = 'select ID from products_images where ID_ORG = '.$id_prod;
		$rs2=new DataObj;
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			echo "***";
			//ja existe a imagem, vamos substituir
			copia_segura($URL_dest,$site_shell_url."files/products/".$id_prod."_1.".$ext);
			unlink($URL_dest);
		}
		else
		{
			$sql = 'INSERT INTO products_images (ID_ORG,Ordem,img_ext) VALUES ("'.$id_prod.'",1,"'.$ext.'")';
			$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
			copia_segura($URL_dest,$site_shell_url."files/products/".$id_prod."_1.".$ext);
			unlink($URL_dest);
		}
		echo 'done';						
		exit();
	}
	else echo "Falha";
?>