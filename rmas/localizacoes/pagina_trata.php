<?
	include 'settings.inc.php';
	if($zone_id > 0 && $task_form != "apaga")$retorno="pagina.php?zone_id=$zone_id";
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		if($task_form=="insere")
		{
			$result=InsertNewRecord();
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$result=ChangeRecord();
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$result=DeleteRecord($ID,$img_path);
			if($result) show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>