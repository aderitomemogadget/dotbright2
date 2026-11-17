<?
	include 'settings.inc.php';
	$retorno="list.php";
	if(isset($_POST['slashes']))
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$Img_peq2["name"]=$_FILES['img_peq2']['name'];
		$Img_peq2["temp"]=$_FILES['img_peq2']['tmp_name'];
		$Img_peq3["name"]=$_FILES['img_peq3']['name'];
		$Img_peq3["temp"]=$_FILES['img_peq3']['tmp_name'];
		$img_path="../../files/news1_families/";
		if($_POST['slashes']=="'")
		{
			$cabecalho=addslashes($cabecalho);
			$texto=addslashes($texto);
		}
		if($task_form=='insere')
		{
			$retorno="list.php";
			$result=InsertNewFam($m_familia,$ordem,$cabecalho,$URL,$texto,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$areas,$tipo_noticia);
			if($result)	show_box_OK(1,$retorno);
			else		show_box_ERROR(1,$retorno);
		}
		elseif($task_form=='altera')
		{
			$retorno="news1_families.php?ID=$ID&ID_ORG=$ID_ORG";
			$result=ChangeFam($ID,$m_familia,$ordem,$cabecalho,$URL,$texto,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$Img_peq2,$Apagar_img2,$tem_foto2,$Img_peq3,$Apagar_img3,$tem_foto3,$margemcli,$margemrev,$cor,$visivel,$destaque,$tipo_check,$tipo,$cat1,$cat2,$cat3,$download,$areas,$tipo_noticia);
			if($result)	show_box_OK(2,$retorno);
			else		show_box_ERROR(2,$retorno);
		}
		elseif($task_form=='apaga')
		{
			$retorno="list.php";
			$result=DeleteFam($ID,$img_path);
			if($result)	show_box_OK(3,$retorno);
			else		show_box_ERROR(3,$retorno);
		}
		if($task_form=="duplicar")
		{
			$retorno="list.php";
			$result=InsertNewFam($m_familia,$ordem,$cabecalho,$URL,$texto,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$areas,$tipo_noticia);
			if($result)	show_box_OK(1,$retorno);
			else		show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>