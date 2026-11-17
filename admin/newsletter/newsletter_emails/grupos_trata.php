<?
	include "settings.inc.php";
	$retorno="list.php";
	if($_POST['slashes'])
	{
		if($task_form == "insere")
		{
			$result= InsertNewRecord();
			if($result)	show_box_OK(1);
			else		show_box_ERROR(1);
		}
		elseif ($task_form =="altera")
		{
			$result= ChangeRecord();
			if($result)	show_box_OK(2);
			else		show_box_ERROR(2);
		}
		elseif($task_form =="apaga")
		{
			$result = DeleteRecord();
			if($result)	show_box_OK(3);
			else		show_box_ERROR(3);
		}
	}else show_box_ERROR(4,$retorno);
?>