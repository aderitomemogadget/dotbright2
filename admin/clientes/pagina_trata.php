<?
	include "settings.inc.php";
	if(isset($_POST['ID']) && $_POST['ID'] > 0) $retorno='pagina.php?ID='.$_POST['ID'].'&tab='.$_POST['tab'];
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		$img_path='../../files/clientes/';
		if($slashes=="'")
		{
			$descricao=addslashes($descricao);
			$texto=addslashes($texto);
		}
		try
		{
			if($task_form=="insere")
			{
				$result=InsertNewRecord();
				if($result) show_box_OK(1,$retorno);
				else show_box_ERROR(1,$retorno);
			}
			elseif($task_form=="altera")
			{
				$result=ChangeRecord();
				if($result) show_box_OK(2,$retorno);
				else show_box_ERROR(2,$retorno);
			}
			elseif($task_form=="apaga")
			{
				$retorno="list.php";
				$result=DeleteRecord();
				if($result)show_box_OK(3,$retorno);
				else show_box_ERROR(3,$retorno);
			}
		}
		catch (Throwable $e)
		{
			error_log('[clientes/pagina_trata] '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine()."\n".$e->getTraceAsString());
			show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>
