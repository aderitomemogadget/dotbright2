<?php
	include "settings.inc.php";
	if($ID > 0 && $task_form != "duplicar"&& $task_form != "apaga") $retorno="pagina.php?ID=$ID";
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$Img_peq2["name"]=$_FILES['img_peq2']['name'];
		$Img_peq2["temp"]=$_FILES['img_peq2']['tmp_name'];
		$img_path="../../files/brands/";
		if($task_form== "insere")
		{
			$result = InsertNewRecord();
			if($result) show_box_OK(1, $retorno);
			else show_box_ERROR(1, $retorno);
		}
		elseif($task_form == "altera")
		{
			$result=ChangeRecord();
			if($result) show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form == "apaga")
		{
			$result=DeleteRecord();
			if($result) show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
		elseif($task_form == "duplicar")
		{
			$result = InsertNewRecord();
			if($result) show_box_OK(1, $retorno);
			else show_box_ERROR(1, $retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>