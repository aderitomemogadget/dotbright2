<?php
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

//ja tenho so dados necessarios


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
	// agora vamos criar o produto ou atualizar
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

//	$exploded = multiexplode(array(",",".","|",":","_","-"),$name);
	$exploded = explode("_",$name);

	$rs=new DataObj;$rs2=new DataObj;

	$sql = 'select Cabecalho,obj_type_id from products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id where Cabecalho = "'.$exploded[0].'" and Lang="pt"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$id_prod=$rs->db_data["obj_type_id"];
		$rs->db_close();

		//ja existem produtos com esta referencia
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$sql = 'select * from products_images where ID_ORG = '.$id_prod;
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				echo ' FOTO ATUALIZADA PARA A referencia '.$exploded[0];

				//ja existe a imagem, vamos substituir
				copia_segura($URL_dest,$site_shell_url."files/products/".$id_prod."_1.jpg");
				unlink($URL_dest);
			}
			else
			{
				echo ' FOTO INSERIDA PARA A referencia '.$exploded[0];

				$img_ext='jpg';
				$sql = 'INSERT INTO products_images (ID_ORG,Ordem,img_ext) VALUES ("'.$id_prod.'",1,"'.$img_ext.'")';
				$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				copia_segura($URL_dest,$site_shell_url."files/products/".$id_prod."_1.jpg");
				unlink($URL_dest);
			}
			unlink($site_shell_url."files/produtosdestaque.php");
			unlink($site_shell_url."files/produtospromocao.php");
			unlink($site_shell_url."files/tmp/prod_".$id_prod."_.txt");
		}
	}
	else
	{
		echo ' A referencia '.$exploded[0].' nao existe ';
	}
	/*else
	{
		$sql="insert into products (ID_ORG,Ref,img_ext) values ($cod,'$name','jpg')";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$new_id=db_get_last_insert_id($db_link);
			copia_segura($URL_dest,$site_shell_url."files/products/".$new_id."_1.jpg");
			unlink($URL_dest);
			InsertProdLinguas($new_id,$name,'','','','','','');
			$sql = 'INSERT INTO products_images (ID_ORG,Ordem,img_ext) VALUES ("'.$new_id.'",1,"jpg")';
			$res=Runquery($db_link,$DB_name,$sql);
		}
	}*/
	//echo 'done';
	exit();
}

/*

$output = sprintf('%s; received %s bytes, CRC32 = %08X, MD5 = %s', $name, number_format(strlen($data)), crc32($data), strtoupper(md5($data)));
$opt = &$_REQUEST['upload_option'];
isset($opt) and $output .= "\nReceived upload_option with value $opt";
if ($callback)
{
  header('Content-Type: text/html; charset=utf-8');
  $output = addcslashes($output, "\\\"\0..\x1F");
  echo '<!DOCTYPE html><html><head></head><body><script type="text/javascript">',
       "try{window.top.$callback(\"$output\")}catch(e){}</script></body></html>";
} else {
  header('Content-Type: text/plain; charset=utf-8');
  echo $output;
}*/