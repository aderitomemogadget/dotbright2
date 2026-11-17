<?
	include "settings.inc.php";

	$retorno="list.php?ID_ORG=".$ID_ORG."&DumpCach=".uniqid("");

	if($task_form == "insere")
	{
		$result= InsertNewRecord($ID_ORG,$nome,$apelido,$empresa,$email,$contacto,$morada,$codpos,$localidade,$tel,$fax, str2date("$ano-$mes-$dia"),$obs,$tipo);
		if($result)	show_box_OK(1,$retorno);
		else		show_box_ERROR(1,$retorno);
	}
	elseif ($task_form =="altera")
	{
		$result= ChangeRecord($ID,$nome,$apelido,$empresa,$email,$contacto,$morada,$codpos,$localidade,$tel,$fax, str2date("$ano-$mes-$dia"),$obs,$tipo);
		if($result)	show_box_OK(2,$retorno);
		else		show_box_ERROR(2,$retorno);
	}
	elseif($task_form =="apaga")
	{
		$result = DeleteRecord($ID);
		if($result)	show_box_OK(3,$retorno);
		else		show_box_ERROR(3,$retorno);
	}
?>