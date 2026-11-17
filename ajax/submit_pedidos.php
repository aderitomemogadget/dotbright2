<?php
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	global $path;
	$path="../core/";
	require_once $path.'core_files.php';
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(!$m_lang) $m_lang = $admin_lang;

	$Img_peq1["name"] = $_FILES['img_peq1']['name'];
	$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
	$img_path="../../files/candidatos/";

	$tem_foto1 		= $_POST['tem_foto1'];
	$Apagar_img1 	= $_POST['Apagar_img1'];


	$nome_completo = addslashes($_POST['m_nome']);
	$morada = addslashes($_POST['m_morada']);
	$cp = addslashes($_POST['m_codpos']);
	$NIF = addslashes($_POST['m_contribuinte']);
	$telefone = addslashes($_POST['m_telefone']);
	
	$prod_id = addslashes($_POST['prod_id']);

	if(isset($_POST['Visivel'])) $Visivel = $_POST['Visivel'];
	else $Visivel = 0;

	$sql = "INSERT INTO pedidos(prod_id,nome_completo,morada,cp,telefone,NIF) 
			VALUES ('$prod_id','$nome_completo','$morada','$cp','$telefone','$NIF')";
	$res = Runquery($db_link,$DB_name,$sql);
?>