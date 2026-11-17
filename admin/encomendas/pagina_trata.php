<?
	include 'settings.inc.php';
	if($ID_ORG>0)$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG";
	else $retorno="list.php";
	if(isset($_POST['slashes']))
	{
		if($slashes=="'")
		{
			$Nome=addslashes($Nome);
		}
		$Img_peq1["name"]=$img_peq1_name;
		$Img_peq1["temp"]=$img_peq1;
		$img_path="../../files/packs/";
		if($task_form=="insere")
		{
			$result=InsertNewRecord($Nome,$Preco,$NLances,$NPontos,$Estado,$img_path,$Img_peq1,$norecize);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$result=ChangeRecord($ID,$Nome,$Preco,$NLances,$NPontos,$Estado,$img_path,$Img_peq1,$Apagar_img1,$norecize);
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