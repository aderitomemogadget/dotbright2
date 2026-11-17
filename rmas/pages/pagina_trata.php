<?
	include 'settings.inc.php';
	if($m_familia>0)$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG&tipo=$tipo";
	else $retorno="list.php?tipo=$tipo";
	if($_POST['slashes'])
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$Img_peq2["name"]=$_FILES['img_peq2']['name'];
		$Img_peq2["temp"]=$_FILES['img_peq2']['tmp_name'];
		$img_path="../../files/pages/";
		if($slashes=="'")
		{
			$descricao=addslashes($descricao);
			$texto=addslashes($texto);
			$texto2=addslashes($texto2);
			$texto3=addslashes($texto3);
		}
		if($task_form=="insere")
		{
			$result=InsertNewRecord($m_familia,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno="pagina.php?prev=0&ID=$ID_ORG&ID_ORG=$ID_ORG&tipo=$tipo";
			$result=ChangeRecord($ID,$m_familia,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$draft,$draft_id,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Apagar_img1,$Apagar_img2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2);
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
			$result=DuplicateRecord($m_familia,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$tem_foto2_ID,$tem_foto2_ext);
			if($result)show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>