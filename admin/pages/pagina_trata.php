<?php
	include 'settings.inc.php';

	if($m_familia > 0)$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG&tipo=$tipo";
	else $retorno="list.php?tipo=$tipo";
	if($_POST['slashes'])
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];

		$Img_peq2["name"]=$_FILES['img_peq2']['name'];
		$Img_peq2["temp"]=$_FILES['img_peq2']['tmp_name'];

		$Img_peq3["name"]=$_FILES['img_peq3']['name'];
		$Img_peq3["temp"]=$_FILES['img_peq3']['tmp_name'];

		$Img_peq4["name"]=$_FILES['img_peq4']['name'];
		$Img_peq4["temp"]=$_FILES['img_peq4']['tmp_name'];

		$Img_peqdoc["name"]=$_FILES['img_peqdoc']['name'];
		$Img_peqdoc["temp"]=$_FILES['img_peqdoc']['tmp_name'];
		$img_path="../../files/pages/";
		if($slashes=="'")
		{
			$descricao=addslashes($descricao);
			$descricao2=addslashes($descricao2);
			$texto=addslashes($texto);
			$texto2=addslashes($texto2);
			$texto3=addslashes($texto3);
			$texto10=addslashes($texto10);
			$texto11=addslashes($texto11);
			$texto12=addslashes($texto12);
			$texto13=addslashes($texto13);
			$texto14=addslashes($texto14);
			$texto15=addslashes($texto15);
			$texto16=addslashes($texto16);
			$texto17=addslashes($texto17);
			$texto18=addslashes($texto18);
			$texto19=addslashes($texto19);
			$texto20=addslashes($texto20);
		}

		if($task_form=="insere")
		{
			$result=InsertNewRecord($m_familia,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peqdoc,$Img_estilo,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$script,$interna,$menus);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG&tipo=$tipo";
			$result=ChangeRecord($ID,$m_familia,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$draft,$draft_id,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peqdoc,$Img_estilo,$Apagar_img1,$Apagar_img2,$Apagar_img3,$Apagar_img4,$Apagar_imgdoc,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$script,$interna,$menus);
			if($result)show_box_OK(2,$retorno);
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$result=DeleteRecord($ID,$draft_id,$img_path);
			if($result)show_box_OK(3,$retorno);
			else show_box_ERROR(3,$retorno);
		}
		elseif($task_form=="duplicar")
		{

			$result=DuplicateRecord($m_familia,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peqdoc,$Img_estilo,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$tem_foto2_ID,$tem_foto2_ext,$script,$interna,$menus);

			// VOU RETORNAR À PAGINA DUPLICADA
			$retorno = "pagina.php?ID=".$result['ID'];

			if($result['res']) show_box_OK(1, $retorno);
			else show_box_ERROR(1, $retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>