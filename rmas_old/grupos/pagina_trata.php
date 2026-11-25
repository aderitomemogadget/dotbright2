<?
	include 'settings.inc.php';
	if($ID_ORG>0)$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG";
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		if($slashes=="'")
		{
			$Descricao=addslashes($Descricao);
		}
		if($task_form=="insere")
		{
			$result=InsertNewRecord($Descricao);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$result=ChangeRecord($ID,$Descricao);
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$result=DeleteRecord($ID);
			if($result)show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>