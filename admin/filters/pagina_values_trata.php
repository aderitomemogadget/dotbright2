<?
	include 'settings.inc.php';
	if(isset($_POST['category_id']) && $_POST['category_id'] > 0 && $task_form != "apaga" && $task_form != "duplicar") $retorno="pagina.php?category_id=".$_POST['category_id'];
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		if($task_form=="insere")
		{
			$result=InsertNewRecordValues();
			if($result) show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$result=ChangeRecordValues();
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$result=DeleteRecordValues($ID,$img_path);
			if($result) show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
		elseif($task_form=="duplicar")
		{
			$result=InsertNewRecordValues();
			if($result) show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>