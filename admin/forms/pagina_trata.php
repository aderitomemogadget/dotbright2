<?
include "settings.inc.php";
if($_POST['slashes'])
{
if($task_form=="insere")
{
	$result=InsertNewRecord($name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo,$obrigatorio,$popup);
	if($result) show_box_OK(1);
	else show_box_ERROR(1);
}
elseif($task_form=="altera")
{
	$result=ChangeRecord($ID,$name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo,$obrigatorio,$popup);
	if($result) show_box_OK(2);
	else show_box_ERROR(2);
}
elseif($task_form=="apaga")
{
	$result=DeleteRecord($ID);
	if($result) show_box_OK(3);
	else show_box_ERROR(3);
}
}else show_box_ERROR(4,$retorno); 
?>