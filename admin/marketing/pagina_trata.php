<?
	include 'settings.inc.php';
	if($m_familia > 0)$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG&tipo=$tipo";
	else $retorno="list.php?tipo=$tipo";
	if($_POST['slashes'])
	{
		$img_path="../../files/pages/";
		/*if($slashes=="'")
		{
		}*/
		if($task_form=="insere")
		{

			$result=InsertNewRecord($emails_encomenda,$receive_notifications,$Default_notifications_email,$receive_notifications_orders,$receive_notifications_news,$receive_notifications_registo);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG&tipo=$tipo";
			$result=ChangeRecord($ID,$emails_encomenda,$receive_notifications,$Default_notifications_email,$receive_notifications_orders,$receive_notifications_news,$receive_notifications_registo);
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>