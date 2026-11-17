<?
	include 'settings.inc.php';
	if($ID>0 && $task_form=="altera")$retorno="pagina.php?ID=$ID";
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		if($slashes=="'")
		{
			if($task_form=="altera")
			{
				$result=ChangeRecord();
				if($result)show_box_OK(2,$retorno);
				else show_box_ERROR(2,$retorno);
			}
			elseif($task_form=="apaga")
			{
				$result=DeleteRecord();
				if($result)show_box_OK(3,$retorno);
				else show_box_ERROR(3,$retorno);
			}
		}
	}
	else show_box_ERROR(4,$retorno);
?>