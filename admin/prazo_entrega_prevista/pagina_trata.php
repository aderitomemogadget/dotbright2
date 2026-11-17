<?
	include 'settings.inc.php';
	$retorno="pagina.php";
	$result=ChangeRecord();
	if($result)show_box_OK(2,$retorno);
	else show_box_ERROR(2,$retorno);
?>