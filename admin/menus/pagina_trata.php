<?php
	include "settings.inc.php";

	if(isset($_POST['slashes']))
	{
		$Img_peq1["name"] = $_FILES['img_peq1']['name'];
		$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];

		$Img_peq2["name"] = $_FILES['img_peq2']['name'];
		$Img_peq2["temp"] = $_FILES['img_peq2']['tmp_name'];

		$Img_peq3["name"] = $_FILES['img_peq3']['name'];
		$Img_peq3["temp"] = $_FILES['img_peq3']['tmp_name'];

		$img_path = "../../files/menus/";
		$c=count($_POST); $keys=array_keys($_POST);
		$url=""; $i=0; $template_id='';
		while($i < $c) { if(substr_count($keys[$i],"template_id")) $template_id .= str_replace('template_id', '', $keys[$i]).';'; $i++; }
		if($slashes=="'")
		{
			$url_nome=addslashes($url_nome);
			$url_nome2=addslashes($url_nome2);
		}
		if($task_form=="insere")
		{

			$result=InsertNewRecord($ID_ORG,$ordem,$visivel,$url_nome,$url_nome2,$url_link,$tipo,$m_lang,$largura,$altura,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$template_id,$novajanela,$catalogo_id,$TipoMenu,$Indexar,$Logado,$menu_icon_fa,$texto_icon_fa);

			if($result['ID'] > 0) $retorno = 'pagina.php?ID='.$result['ID']; elseif($ID_ORG) $retorno = 'pagina.php?ID='.$ID_ORG; else $retorno="list.php";

			if($result['res'])show_box_OK(1, $retorno);
			else show_box_ERROR(1, $retorno);
		}
		elseif($task_form=="altera")
		{
			$result = ChangeRecord($ID,$m_familia2,$ordem,$visivel,$url_nome,$url_nome2,$url_link,$tipo,$m_lang,$largura,$altura,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$Img_peq2,$Apagar_img2,$tem_foto2,$Img_peq3,$Apagar_img3,$tem_foto3,$template_id,$novajanela,$catalogo_id,$TipoMenu,$Indexar,$Logado,$menu_icon_fa,$texto_icon_fa,$apagar_fa_icon);

			if($ID > 0) $retorno = 'pagina.php?ID='.$ID; elseif($m_familia2) $retorno = 'pagina.php?ID='.$m_familia2; else $retorno="list.php";

			if($result) show_box_OK(2, $retorno);
			else show_box_ERROR(2, $retorno);
		}
		elseif($task_form=="apaga")
		{

			if($m_familia2 > 0) $retorno = 'pagina.php?ID='.$m_familia2; else $retorno="list.php";
			$result=DeleteRecord($ID,$img_path);
			if($result)show_box_OK(3, $retorno);
			else show_box_ERROR(3, $retorno);
		}
		elseif($task_form=="duplicar")
		{
			if(!$ID_ORG)$ID_ORG=0;
			$result=InsertNewRecord($m_familia2,$ordem,$visivel,$url_nome,$url_nome2,$url_link,$tipo,$m_lang,$largura,$altura,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$template_id,$novajanela,$catalogo_id,$TipoMenu,$Indexar,$Logado,$menu_icon_fa,$texto_icon_fa);
			if($result['ID'] > 0) $retorno = 'pagina.php?ID='.$result['ID']; elseif($m_familia2) $retorno = 'pagina.php?ID='.$m_familia2; else $retorno="list.php";

			if($result['ID'] > 0) show_box_OK(1, $retorno);
			else show_box_ERROR(1, $retorno);
		}

		$files = glob('../../files/tmp/show_menu_*');
		foreach ($files as $file)
		{
		 	unlink($file);
		}
		$files = glob('../../files/tmp/show_multiple_menus_*');
		foreach ($files as $file)
		{
		 	unlink($file);
		}

	}
	else show_box_ERROR(4,$retorno);
?>