<?
	include "settings.inc.php";
	$retorno="list.php";
	if(isset($_POST['slashes']))
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$img_path="../../files/templates/";
		if($slashes=="'")
		{
			$cabecalho=addslashes($cabecalho);
			$texto1=addslashes($texto1);
			$texto2=addslashes($texto2);
			$texto3=addslashes($texto3);
		}
		if($task_form=="insere")
		{
			$result=InsertNewRecord($ordem,str2date("$ano-$mes-$dia"),str2date("$ano2-$mes2-$dia2"),$cabecalho,$texto1,$texto2,$texto3,$texto4,$img_path,$Img_peq1,$visivel,$visivel2,$destaque,$margem_top,$bg_color,$efeito,$url,$site_width,$estilo,$estilo2,$m_lang,$link_to);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno="pagina.php?ID=$ID";
			$result=ChangeRecord($ID,$ordem,str2date("$ano-$mes-$dia"),str2date("$ano2-$mes2-$dia2"),$cabecalho,$texto1,$texto2,$texto3,$texto4,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$tem_foto2,$visivel,$visivel2,$destaque,$margem_top,$bg_color,$efeito,$url,$site_width,$estilo,$estilo2,$m_lang,$link_to);
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$result=DeleteRecord($ID,$img_path);
			if($result)show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
		elseif($task_form=="duplicar")
		{
			if(!$ID_ORG)$ID_ORG=0;
			$result=InsertNewRecord($ordem,str2date("$ano-$mes-$dia"),str2date("$ano2-$mes2-$dia2"),$cabecalho,$texto1,$texto2,$texto3,$texto4,$img_path,$Img_peq1,$visivel,$visivel2,$destaque,$margem_top,$bg_color,$efeito,$url,$site_width,$estilo,$estilo2,$m_lang,$link_to);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>