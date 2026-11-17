<?
	include 'settings.inc.php';
	$retorno="../media_type/pagina.php?ID=$tipo";
	if(isset($_POST['slashes']))
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$img_path="../../files/media/";
		if($slashes=="'")
		{
			$Descritivo=addslashes($Descritivo);
		}
		$script=str_replace("< ","<",$script);
		if($task_form=="insere")
		{
			$result=InsertNewRecord($name,$url,$largura,$altura,$tipo,$pagina,$anunciante,str2date("$ano-$mes-$dia"),str2date("$ano2-$mes2-$dia2"),$n_impressoes,$n_cliques,$peso,$visible,$img_path,$Img_peq1,$script,$permanente,$Descritivo,$tema,$Familia);
			if($result)	show_box_OK(1);
			else		show_box_ERROR(1);
		}
		elseif($task_form=="altera")
		{
			$retorno="../media_type/pagina.php?ID=$tipo";
			$result=ChangeRecord($ID,$name,$url,$largura,$altura,$tipo,$pagina,$anunciante,str2date("$ano-$mes-$dia"),str2date("$ano2-$mes2-$dia2"),$n_impressoes,$n_cliques,$peso,$visible,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$script,$permanente,$Descritivo,$tema);
			if($result)	show_box_OK(2,$retorno);
			else		show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$result=DeleteRecord($ID,$img_path);
			if($result)	show_box_OK(3);
			else		show_box_ERROR(3);
		}
	}
	else show_box_ERROR(4,$retorno);
?>