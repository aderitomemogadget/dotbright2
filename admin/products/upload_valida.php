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
	
	$exploded = explode("_",$name);
	
	$rs=new DataObj;$rs2=new DataObj;
	
	$sql = 'select Cabecalho,obj_type_id from products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id where Cabecalho = "'.$exploded[0].'" and Lang="pt"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$id_prod=$rs->db_data["obj_type_id"];
		$rs->db_close();
	
		//ja existem produtos com esta referencia entao vamos mostrar a foto
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$sql = 'select * from products_images where ID_ORG = '.$id_prod;
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF()) echo $site_http_url.'files/products/'.$id_prod.'_1_new.'.$rs2->db_data['img_ext'];
			else echo ' FOTO NAO EXISTE PARA A referencia '.$exploded[0];
		}
	}
	else echo ' A referencia '.$exploded[0].' nao existe ';
	exit();
}