<?
	include 'settings.inc.php';
	$retorno="../media/list.php";
	if(isset($_POST['slashes']))
	{
		if($slashes=="'")
		{
			$descricao=addslashes($descricao);
			$texto=addslashes($texto);
		}
		if($task_form=="insere")
		{
			$retorno="../media/list.php";
			$result=InsertNewRecord($nome,$help,$menu_tipo,$comportamento,$altura,$largura,$select_image,$tipo_fundo,$bg_color,$legendas,$aleatoria);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno="pagina.php?ID=$ID";
			$result=ChangeRecord($ID,$nome,$help,$menu_tipo,$comportamento,$altura,$largura,$select_image,$tipo_fundo,$bg_color,$legendas,$aleatoria);
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$retorno="../media/list.php";
			$result=DeleteRecord($ID);
			if($result)show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>